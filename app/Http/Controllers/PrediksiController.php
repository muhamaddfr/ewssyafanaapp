<?php

namespace App\Http\Controllers;

use App\Models\Omzet;
use App\Models\Prediksi;
use App\Models\StatusEws;
use App\Services\EwsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PrediksiController extends Controller
{
    public function index(Request $request)
    {
        // Gunakan cutoff dari query param (setelah simulasi) atau fallback ke max di DB
        $latestCutoff = $request->query('cutoff')
            ?? Prediksi::max('cutoff_tanggal')
            ?? Omzet::max('tanggal');

        // Daftar semua cutoff tersedia untuk dropdown switching
        $allCutoffs = Prediksi::select('cutoff_tanggal')
            ->distinct()
            ->orderByDesc('cutoff_tanggal')
            ->pluck('cutoff_tanggal')
            ->map(fn($d) => $d instanceof \Carbon\Carbon ? $d->format('Y-m-d') : (string) $d);

        $prediksiList        = collect();
        $statusEws           = null;
        $cutoffDate          = $latestCutoff;
        $aktualSetelahCutoff = collect();
        $aktualHistorical    = collect();
        $omzet7HariTerakhir  = collect();

        $linearMetrics = ['mape' => null, 'rmse' => null, 'mae' => null];

        // 1. Ambil 7 hari omzet aktual terakhir (sebelum atau tepat pada cutoff)
        if ($cutoffDate) {
            $omzet7HariTerakhir = Omzet::where('tanggal', '<=', $cutoffDate)
                ->orderByDesc('tanggal')
                ->take(7)
                ->get()
                ->reverse()
                ->values();
        }

        // 2. Ambil data peramalan untuk cutoff terpilih
        if ($latestCutoff) {
            $prediksiList = Prediksi::where('cutoff_tanggal', $latestCutoff)
                ->orderBy('tanggal_prediksi')
                ->get();

            $statusEws = StatusEws::where('cutoff_tanggal', $latestCutoff)
                ->latest('tanggal')
                ->first();

            $omzetDalamHorizon = collect();

            // Data omzet aktual untuk tanggal-tanggal yang diprediksi (jika sudah diisi di DB)
            if ($prediksiList->isNotEmpty()) {
                $predDates = $prediksiList->map(function ($p) {
                    return $p->tanggal_prediksi instanceof \Carbon\Carbon
                        ? $p->tanggal_prediksi->format('Y-m-d')
                        : substr((string)$p->tanggal_prediksi, 0, 10);
                })->toArray();

                $omzetDalamHorizon = Omzet::whereIn('tanggal', $predDates)
                    ->orderBy('tanggal')
                    ->get();

                $aktualSetelahCutoff = $omzetDalamHorizon->keyBy(fn($o) => $o->tanggal instanceof \Carbon\Carbon ? $o->tanggal->format('Y-m-d') : substr((string)$o->tanggal, 0, 10));
            }

            // Data omzet historis gabungan untuk Kurva Grafik (7 hari omzet aktual sebelum cutoff + omzet aktual yang ada dalam horizon prediksi)
            $aktualHistorical = $omzet7HariTerakhir->concat($omzetDalamHorizon)->unique('id_omzet')->values();

            // Metriks Walk-Forward Validation riil untuk Regresi Linear
            $linearMetrics = $this->hitungWfvRegresiLinear($latestCutoff);
        } else {
            $aktualHistorical = $omzet7HariTerakhir;
        }

        // Tanggal max data untuk batas atas cutoff
        $maxDataTanggal = Omzet::max('tanggal');

        return view('prediksi.index', compact(
            'prediksiList',
            'statusEws',
            'cutoffDate',
            'aktualSetelahCutoff',
            'aktualHistorical',
            'omzet7HariTerakhir',
            'maxDataTanggal',
            'allCutoffs',
            'linearMetrics'
        ));
    }

    private function hitungWfvRegresiLinear(string $cutoff): array
    {
        $rows = Omzet::where('tanggal', '<=', $cutoff)
            ->orderBy('tanggal', 'asc')
            ->pluck('jumlah_omzet')
            ->map(fn($v) => (float) $v)
            ->toArray();

        $n = count($rows);
        if ($n < 15) {
            return ['mape' => null, 'rmse' => null, 'mae' => null];
        }

        $initialWindow = max(15, (int) ($n * 0.70));
        if ($initialWindow >= $n) {
            $initialWindow = $n - 1;
        }

        $actuals = [];
        $preds   = [];

        for ($i = $initialWindow; $i < $n; $i++) {
            $train   = array_slice($rows, 0, $i);
            $trueVal = $rows[$i];
            $m       = count($train);

            $sumX = 0; $sumY = 0; $sumXY = 0; $sumXX = 0;
            for ($j = 0; $j < $m; $j++) {
                $sumX  += $j;
                $sumY  += $train[$j];
                $sumXY += ($j * $train[$j]);
                $sumXX += ($j * $j);
            }

            $denom = ($m * $sumXX - $sumX * $sumX);
            if ($denom == 0) {
                continue;
            }

            $slope     = ($m * $sumXY - $sumX * $sumY) / $denom;
            $intercept = ($sumY - $slope * $sumX) / $m;

            $fc = $slope * $m + $intercept;

            $actuals[] = $trueVal;
            $preds[]   = $fc;
        }

        $numFolds = count($actuals);
        if ($numFolds === 0) {
            return ['mape' => null, 'rmse' => null, 'mae' => null];
        }

        $mapeSum = 0.0;
        $sse     = 0.0;
        $maeSum  = 0.0;
        $mapeN   = 0;

        for ($k = 0; $k < $numFolds; $k++) {
            $a   = $actuals[$k];
            $p   = $preds[$k];
            $err = $a - $p;

            $sse    += ($err * $err);
            $maeSum += abs($err);
            if ($a != 0) {
                $mapeSum += abs($err / $a);
                $mapeN++;
            }
        }

        $mape = $mapeN > 0 ? round(($mapeSum / $mapeN) * 100, 2) : null;
        $rmse = round(sqrt($sse / $numFolds), 2);
        $mae  = round($maeSum / $numFolds, 2);

        return ['mape' => $mape, 'rmse' => $rmse, 'mae' => $mae];
    }

    public function jalankan(Request $request)
    {
        $request->validate([
            'cutoff' => 'required|date',
        ]);

        $cutoff = $request->input('cutoff');

        // Pastikan cutoff ada datanya
        $adaData = Omzet::where('tanggal', '<=', $cutoff)->count();
        if ($adaData < 15) {
            return back()->withErrors([
                'cutoff' => 'Data sebelum tanggal cutoff terlalu sedikit (min 15 hari aktif).',
            ]);
        }

        // Jalankan forecast dengan cutoff sebagai parameter
        Artisan::call('ews:forecast', ['--cutoff' => $cutoff]);

        // Redirect ke index dengan cutoff di URL agar tampilan langsung update
        return redirect()->route('prediksi.index', ['cutoff' => $cutoff])
            ->with('success', 'Peramalan berhasil dijalankan untuk cutoff ' . $cutoff . '.');
    }
}
