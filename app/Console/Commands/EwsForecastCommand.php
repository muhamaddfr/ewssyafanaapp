<?php

namespace App\Console\Commands;

use App\Models\Notifikasi;
use App\Models\Omzet;
use App\Models\Prediksi;
use App\Models\StatusEws;
use App\Services\EwsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class EwsForecastCommand extends Command
{
    protected $signature = 'ews:forecast {--cutoff= : Tanggal cutoff YYYY-MM-DD (mode simulasi/backtest)}';
    protected $description = 'Menjalankan peramalan ARIMA 7 hari dan memperbarui status EWS omzet';

    public function handle(EwsService $ews): int
    {
        $cutoffInput = $this->option('cutoff');

        if ($cutoffInput) {
            $cutoff = $cutoffInput;
            $this->info("Mode simulasi: cutoff = {$cutoff}");
        } else {
            $maxTgl = Omzet::max('tanggal');
            $cutoff = $maxTgl ?: date('Y-m-d');
            $this->info("Mode produksi: cutoff otomatis = {$cutoff}");
        }

        $pythonExec = env('PYTHON_PATH');
        if (! $pythonExec || ! file_exists($pythonExec)) {
            $laragonPy = 'C:\\laragon\\bin\\python\\python-3.10\\python.exe';
            if (file_exists($laragonPy)) {
                $pythonExec = $laragonPy;
            } else {
                $pythonExec = 'python';
            }
        }

        $pythonScript = base_path('python/arima_forecast.py');
        $envPath      = base_path('.env');
        $cmd          = "\"{$pythonExec}\" \"{$pythonScript}\" --cutoff {$cutoff} --env \"{$envPath}\"";

        $output = [];
        $returnVar = 0;
        exec($cmd . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Gagal menjalankan script Python arima_forecast.py:');
            $this->error(implode("\n", $output));
            return 1;
        }

        $lastLine = '';
        foreach (array_reverse($output) as $line) {
            $trimmed = trim($line);
            if (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}')) {
                $lastLine = $trimmed;
                break;
            }
        }

        if (! $lastLine) {
            $this->error('Output Python tidak berisi JSON valid:');
            $this->error(implode("\n", $output));
            return 1;
        }

        $result = json_decode($lastLine, true);
        if (! $result || ! isset($result['forecast'])) {
            $this->error('Format JSON dari script Python tidak sesuai.');
            return 1;
        }

        $this->info('Python output: ' . $lastLine);

        // Hapus peramalan lama dengan cutoff yang sama
        Prediksi::where('cutoff_tanggal', $cutoff)->delete();

        foreach ($result['forecast'] as $row) {
            Prediksi::create([
                'tanggal_prediksi' => $row['tanggal'],
                'nilai_prediksi'   => $row['nilai'],
                'model'           => 'ARIMA(2,1,1)',
                'mape'             => $result['mape'] ?? null,
                'rmse'             => $result['rmse'] ?? null,
                'mae'              => $result['mae'] ?? null,
                'cutoff_tanggal'   => $cutoff,
            ]);
        }

        // Hitung rata aktual 7 hari aktif sebelum/di cutoff
        $rataAktual = Omzet::where('tanggal', '<=', $cutoff)
            ->orderByDesc('tanggal')
            ->limit(7)
            ->pluck('jumlah_omzet')
            ->avg() ?? 0;

        $rataPrediksi = collect($result['forecast'])->avg('nilai');
        $bep = (float) \App\Models\Pengaturan::get('bep_harian', env('EWS_BEP_HARIAN', 400000));

        $klasifikasi = $ews->classify((float) $rataAktual, (float) $rataPrediksi, $bep);

        // Hapus status lama dengan cutoff yang sama
        $oldStatusIds = StatusEws::where('cutoff_tanggal', $cutoff)->pluck('id_status');
        if ($oldStatusIds->isNotEmpty()) {
            Notifikasi::whereIn('id_status', $oldStatusIds)->delete();
        }
        StatusEws::where('cutoff_tanggal', $cutoff)->delete();

        $status = StatusEws::create([
            'tanggal'        => $cutoff,
            'rasio'          => $klasifikasi['rasio'],
            'rata_aktual'    => $rataAktual,
            'rata_prediksi'  => $rataPrediksi,
            'kategori'       => $klasifikasi['status'],
            'cutoff_tanggal' => $cutoff,
        ]);

        $this->info("✅ Status EWS: {$klasifikasi['status']} (rasio {$klasifikasi['rasio']}%, rata F = Rp " . number_format($rataPrediksi, 0, ',', '.') . ')');

        if (isset($result['mape'])) {
            $this->info("   MAPE={$result['mape']}% | RMSE=Rp " . number_format($result['rmse'] ?? 0, 0, ',', '.') . " | MAE=Rp " . number_format($result['mae'] ?? 0, 0, ',', '.'));
        }

        // Kirim notifikasi jika Waspada/Bahaya
        if (in_array($klasifikasi['status'], ['Waspada', 'Bahaya'])) {
            $this->kirimNotifikasi($status, $klasifikasi, $result['forecast']);
        }

        return 0;
    }

    private function kirimNotifikasi(StatusEws $status, array $klasifikasi, array $forecast): void
    {
        $cutoffDate = Carbon::parse($status->cutoff_tanggal ?? $status->tanggal)->locale('id');
        $cutoffFormatted = $cutoffDate->translatedFormat('j F Y');

        $periodeStr = '';
        if (! empty($forecast)) {
            $firstDate = Carbon::parse($forecast[0]['tanggal'])->locale('id');
            $lastDate  = Carbon::parse($forecast[count($forecast) - 1]['tanggal'])->locale('id');

            if ($firstDate->month === $lastDate->month && $firstDate->year === $lastDate->year) {
                $periodeStr = $firstDate->translatedFormat('j') . '–' . $lastDate->translatedFormat('j F Y');
            } else {
                $periodeStr = $firstDate->translatedFormat('j F') . '–' . $lastDate->translatedFormat('j F Y');
            }
        }

        $bep = (float) \App\Models\Pengaturan::get('bep_harian', env('EWS_BEP_HARIAN', 400000));

        $headerEmoji     = $klasifikasi['status'] === 'Bahaya' ? '🚨' : '⚠️';
        $statusFormatted = strtoupper($klasifikasi['status']);
        $rasioFormatted  = number_format($klasifikasi['rasio'], 2, '.', '');

        $rataAktualFormatted   = 'Rp ' . number_format($status->rata_aktual, 0, ',', '.');
        $rataPrediksiFormatted  = 'Rp ' . number_format($status->rata_prediksi, 0, ',', '.');
        $bepFormatted           = 'Rp ' . number_format($bep, 0, ',', '.');

        if ($klasifikasi['status'] === 'Bahaya') {
            $saran = "Proyeksi omzet terdeteksi berada di bawah batas aman/BEP. Disarankan segera melakukan evaluasi operasional, penyesuaian stok bahan baku, serta promosi penjualan harian.";
        } else {
            $saran = "Tren omzet terdeteksi mulai melambat. Disarankan untuk memantau penjualan harian lebih cermat dan mempersiapkan langkah antisipasi promo.";
        }

        $isi = "📢 *EWS OMZET SYAFANA FRIED CHICKEN*\n\n" .
               "{$headerEmoji} *Status Penjualan: {$statusFormatted}*\n" .
               "📅 *Periode Data:* s.d. {$cutoffFormatted}\n" .
               "🔮 *Proyeksi (7 Hari):* {$periodeStr}\n\n" .
               "📈 *RINGKASAN METRIK:*\n" .
               "• Rata-rata Omzet Aktual (A): {$rataAktualFormatted}\n" .
               "• Rata-rata Prediksi (F): {$rataPrediksiFormatted}\n" .
               "• Target BEP Harian: {$bepFormatted}\n" .
               "• Rasio Tren (F/A): *{$rasioFormatted}%*\n\n" .
               "💡 *ANALISIS & SARAN:*\n" .
               "{$saran}\n\n" .
               "_Pesan otomatis dikirim oleh EWS System_";

        $mode     = env('EWS_MODE', 'real');
        $nomor    = \App\Models\Pengaturan::get('nomor_wa', env('FONNTE_TARGET_WA'));
        $terkirim = false;

        if ($mode === 'real' && $nomor) {
            $response = Http::asForm()->withHeaders([
                'Authorization' => env('FONNTE_TOKEN'),
            ])->post('https://api.fonnte.com/send', [
                'target'  => $nomor,
                'message' => $isi,
            ]);
            $responseBody = $response->body();
            $responseJson = $response->json();

            // Fonnte kadang kembalikan HTTP 200 tapi status=false di body
            $fonnteStatus = $responseJson['status'] ?? true;
            $terkirim = $response->successful() && $fonnteStatus !== false;

            if ($terkirim) {
                $this->info('📱 Notifikasi WhatsApp terkirim. Response: ' . $responseBody);
            } else {
                $this->error('❌ Gagal kirim WhatsApp.');
                $this->error('   HTTP Status : ' . $response->status());
                $this->error('   Response    : ' . $responseBody);
            }
        } else {
            // Demo / no token → simulasi saja
            $terkirim = true;
            $this->info('📱 [Demo] Notifikasi disimulasikan (tidak dikirim ke API Fonnte).');
        }

        Notifikasi::create([
            'id_status'    => $status->id_status,
            'pesan'        => $isi,
            'nomor_tujuan' => $nomor,
            'status_kirim' => $terkirim ? 'Terkirim' : 'Gagal',
            'waktu_kirim'  => now(),
        ]);
    }
}
