<?php

namespace App\Http\Controllers;

use App\Models\Omzet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OmzetController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->get('per_page'), [10, 20, 30]) ? (int) $request->get('per_page') : 10;
        $riwayat = Omzet::orderByDesc('tanggal')->paginate($perPage)->appends(['per_page' => $perPage]);

        $tanggalList = $riwayat->pluck('tanggal')->map(function ($d) {
            return $d instanceof \Carbon\Carbon ? $d->format('Y-m-d') : substr((string) $d, 0, 10);
        })->toArray();

        $prediksiMap = \App\Models\Prediksi::whereIn('tanggal_prediksi', $tanggalList)
            ->orderByDesc('cutoff_tanggal')
            ->get()
            ->groupBy(function ($p) {
                return $p->tanggal_prediksi instanceof \Carbon\Carbon ? $p->tanggal_prediksi->format('Y-m-d') : substr((string) $p->tanggal_prediksi, 0, 10);
            })
            ->map(fn($group) => $group->first());

        return view('omzet.index', compact('riwayat', 'perPage', 'prediksiMap'));
    }

    public function create(Request $request)
    {
        return $this->index($request);
    }

    public function store(Request $request)
    {
        if ($request->has('jumlah')) {
            $request->merge([
                'jumlah' => preg_replace('/[^0-9]/', '', (string) $request->input('jumlah')),
            ]);
        }

        $data = $request->validate([
            'tanggal'    => 'required|date|unique:omzet,tanggal',
            'jumlah'     => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        Omzet::create([
            'tanggal'      => $data['tanggal'],
            'jumlah_omzet' => $data['jumlah'],
            'keterangan'   => $data['keterangan'] ?? null,
            'id_user'      => Auth::id(),
        ]);

        return redirect()->route('omzet.index')
            ->with('success', 'Omzet berhasil disimpan.');
    }

    public function riwayat(Request $request)
    {
        return $this->index($request);
    }

    public function edit($id)
    {
        $omzet = Omzet::findOrFail($id);
        return view('omzet.edit', compact('omzet'));
    }

    public function update(Request $request, $id)
    {
        $omzet = Omzet::findOrFail($id);

        if ($request->has('jumlah')) {
            $request->merge([
                'jumlah' => preg_replace('/[^0-9]/', '', (string) $request->input('jumlah')),
            ]);
        }

        $data = $request->validate([
            'tanggal'    => 'required|date|unique:omzet,tanggal,' . $omzet->id_omzet . ',id_omzet',
            'jumlah'     => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $omzet->update([
            'tanggal'      => $data['tanggal'],
            'jumlah_omzet' => $data['jumlah'],
            'keterangan'   => $data['keterangan'] ?? null,
        ]);

        return redirect()->route('omzet.index')
            ->with('success', 'Data omzet tanggal ' . \Carbon\Carbon::parse($omzet->tanggal)->format('d M Y') . ' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $omzet = Omzet::findOrFail($id);
        $tglFormatted = \Carbon\Carbon::parse($omzet->tanggal)->format('d M Y');
        $omzet->delete();

        return redirect()->route('omzet.index')
            ->with('success', 'Data omzet tanggal ' . $tglFormatted . ' berhasil dihapus.');
    }
}
