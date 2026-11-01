<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $bepHarian = (float) Pengaturan::get('bep_harian', env('EWS_BEP_HARIAN', 400000));
        $nomorWa   = (string) Pengaturan::get('nomor_wa', env('FONNTE_TARGET_WA', ''));
        return view('pengaturan.index', compact('bepHarian', 'nomorWa'));
    }

    public function update(Request $request)
    {
        if ($request->has('bep_harian')) {
            $request->merge([
                'bep_harian' => preg_replace('/[^0-9]/', '', (string) $request->input('bep_harian')),
            ]);
        }

        $data = $request->validate([
            'bep_harian' => 'required|numeric|min:0',
            'nomor_wa'   => 'required|string|max:20',
        ]);

        Pengaturan::set('bep_harian', $data['bep_harian'], 'Target Break Even Point (BEP) Omzet Harian (Rp)');
        Pengaturan::set('nomor_wa', $data['nomor_wa'], 'Nomor WhatsApp Pemilik Usaha Target Notifikasi EWS');

        return redirect()->route('pengaturan.index')
            ->with('success', 'Pengaturan Target BEP Harian dan Nomor WhatsApp berhasil diperbarui.');
    }
}
