<?php

namespace App\Http\Controllers;

use App\Models\Omzet;
use App\Models\Prediksi;
use App\Models\StatusEws;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $latestCutoff     = Prediksi::max('cutoff_tanggal') ?? Omzet::max('tanggal');
        $statusTerbaru    = $latestCutoff ? StatusEws::where('cutoff_tanggal', $latestCutoff)->latest('tanggal')->first() : StatusEws::latest('tanggal')->first();

        $hariDataTerbaru  = $latestCutoff ?? Omzet::max('tanggal');
        $totalOmzet       = Omzet::sum('jumlah_omzet');
        $rataOmzetPerHari = Omzet::avg('jumlah_omzet') ?? 0;

        $prediksiTerbaru7    = collect();
        $aktualHistorical7   = collect();
        $omzetTerakhir7      = collect();
        $aktualSetelahCutoff = collect();

        if ($latestCutoff) {
            $prediksiTerbaru7 = Prediksi::where('cutoff_tanggal', $latestCutoff)
                ->orderBy('tanggal_prediksi')
                ->take(7)
                ->get();

            if ($prediksiTerbaru7->isNotEmpty()) {
                $predDates = $prediksiTerbaru7->map(function ($p) {
                    return $p->tanggal_prediksi instanceof \Carbon\Carbon
                        ? $p->tanggal_prediksi->format('Y-m-d')
                        : substr((string)$p->tanggal_prediksi, 0, 10);
                })->toArray();

                $aktualSetelahCutoff = Omzet::whereIn('tanggal', $predDates)
                    ->get()
                    ->keyBy(fn($o) => $o->tanggal instanceof \Carbon\Carbon ? $o->tanggal->format('Y-m-d') : substr((string)$o->tanggal, 0, 10));

                $omzetDalamHorizon = Omzet::whereIn('tanggal', $predDates)->orderBy('tanggal')->get();
                $aktualHistorical7 = Omzet::where('tanggal', '<=', $latestCutoff)
                    ->orderByDesc('tanggal')
                    ->take(7)
                    ->get()
                    ->reverse()
                    ->concat($omzetDalamHorizon)
                    ->unique('id_omzet')
                    ->values();
            } else {
                $aktualHistorical7 = Omzet::where('tanggal', '<=', $latestCutoff)
                    ->orderByDesc('tanggal')
                    ->take(7)
                    ->get()
                    ->reverse()
                    ->values();
            }

            $omzetTerakhir7 = Omzet::where('tanggal', '<=', $latestCutoff)
                ->orderByDesc('tanggal')
                ->take(7)
                ->get()
                ->reverse()
                ->values();
        } else {
            $aktualHistorical7 = Omzet::orderByDesc('tanggal')->take(7)->get()->reverse()->values();
            $omzetTerakhir7    = Omzet::orderByDesc('tanggal')->take(7)->get();
        }

        return view('dashboard', compact(
            'statusTerbaru',
            'hariDataTerbaru',
            'totalOmzet',
            'rataOmzetPerHari',
            'omzetTerakhir7',
            'prediksiTerbaru7',
            'aktualHistorical7',
            'aktualSetelahCutoff',
            'latestCutoff'
        ));
    }
}
