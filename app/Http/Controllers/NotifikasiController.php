<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Notifikasi::with('statusEws');

        // Filter status jika ada parameter
        if ($request->filled('status')) {
            $status = $request->query('status');
            $query->whereHas('statusEws', function ($q) use ($status) {
                $q->where('kategori', $status);
            });
        }

        $notifikasiList = $query->orderByDesc('waktu_kirim')
            ->orderByDesc('id_notifikasi')
            ->paginate(10)
            ->withQueryString();

        $totalTerkirim = Notifikasi::where('status_kirim', 'Terkirim')->count();
        
        // Count hari ini
        $totalHariIni = Notifikasi::where('status_kirim', 'Terkirim')
            ->whereDate('waktu_kirim', \Carbon\Carbon::today())
            ->count();

        $notifikasiTerakhir = Notifikasi::with('statusEws')
            ->orderByDesc('waktu_kirim')
            ->orderByDesc('id_notifikasi')
            ->first();

        $mode = env('EWS_MODE', 'real');

        return view('notifikasi.index', compact(
            'notifikasiList',
            'totalTerkirim',
            'totalHariIni',
            'notifikasiTerakhir',
            'mode'
        ));
    }

    public function unduhLog(): StreamedResponse
    {
        $fileName = 'log_notifikasi_ews_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID Notifikasi', 'Waktu Kirim', 'Status EWS', 'Nomor Tujuan', 'Isi Pesan', 'Status Pengiriman']);

            Notifikasi::with('statusEws')
                ->orderByDesc('waktu_kirim')
                ->chunk(100, function ($logs) use ($handle) {
                    foreach ($logs as $log) {
                        $st = $log->statusEws->kategori ?? ($log->statusEws->status ?? 'Waspada');
                        fputcsv($handle, [
                            $log->id_notifikasi,
                            $log->waktu_kirim ? $log->waktu_kirim->format('Y-m-d H:i:s') : '',
                            $st,
                            $log->nomor_tujuan,
                            $log->pesan,
                            $log->status_kirim ?? ($log->terkirim ? 'Terkirim' : 'Gagal'),
                        ]);
                    }
                });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
