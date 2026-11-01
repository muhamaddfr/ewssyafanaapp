@extends('layouts.app')
@section('title', 'Manajemen Data Omzet')

@section('content')
@php $bep = (float) \App\Models\Pengaturan::get('bep_harian', env('EWS_BEP_HARIAN', 400000)); @endphp

<div class="content-max-width">

  <!-- Breadcrumb -->
  <div class="breadcrumb">
    <span>EWS Syafana Fried Chicken</span>
    <span class="breadcrumb-separator">›</span>
    <span class="active-crumb">Manajemen Data Omzet</span>
  </div>

  <!-- Page Title -->
  <div class="page-header-flex" style="margin-bottom: 20px;">
    <div>
      <h1 class="page-title" style="margin-bottom: 4px;">Kelola Data Omzet</h1>
      <p style="color: #64748B; font-size: 13.5px; margin: 0;">Input omzet harian toko dan kelola data historis penjualan dalam satu tempat.</p>
    </div>
  </div>

  <!-- Flash Alerts -->
  @if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 20px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
        <polyline points="22 4 12 14.01 9 11.01"></polyline>
      </svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-error" style="margin-bottom: 20px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
      <span>{{ $errors->first() }}</span>
    </div>
  @endif

  <!-- Top Section: Input Form (Keuangan) / Audit Banner (Pemilik) & Information Cards -->
  <div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 24px; align-items: start; margin-bottom: 32px;">

    <!-- Left Column: Form Card (Keuangan) / Info Banner (Pemilik) -->
    <div>
      @if(auth()->user()->isKeuangan())
        <div class="form-card">
          
          <!-- Card Header -->
          <div class="card-header-flex">
            <div class="card-header-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9A3400" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="12" y1="18" x2="12" y2="12"></line>
                <line x1="9" y1="15" x2="15" y2="15"></line>
              </svg>
            </div>
            <h2 class="card-header-title">Input Omzet Harian</h2>
          </div>

          <!-- Input Form -->
          <form method="POST" action="{{ route('omzet.store') }}">
            @csrf

            <!-- Tanggal Omzet -->
            <div class="form-group">
              <label for="tanggal" class="form-label">Tanggal Omzet</label>
              <input type="date" name="tanggal" id="tanggal" class="form-control"
                     value="{{ old('tanggal', date('Y-m-d')) }}" required>
              <p class="form-hint">Hanya masukkan hari yang benar-benar ada transaksi. Hari libur tidak perlu diisi.</p>
            </div>

            <!-- Jumlah Omzet (Rp) -->
            <div class="form-group">
              <label for="jumlah_display" class="form-label">Jumlah Omzet (Rp)</label>
              <div class="input-prefix-box">
                <span class="input-prefix">Rp</span>
                <input type="text" id="jumlah_display" class="form-control"
                       value="{{ old('jumlah') ? number_format((float)old('jumlah'), 0, ',', '.') : '' }}" required
                       placeholder="Contoh: 850.000" autocomplete="off" inputmode="numeric">
                <input type="hidden" name="jumlah" id="jumlah" value="{{ old('jumlah') }}">
              </div>
              <div class="bep-info">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#9A3400" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>BEP harian: Rp {{ number_format($bep, 0, ',', '.') }}</span>
              </div>
            </div>

            <!-- Keterangan (opsional) -->
            <div class="form-group">
              <label for="keterangan" class="form-label">Keterangan (opsional)</label>
              <textarea name="keterangan" id="keterangan" class="form-control"
                        placeholder="Misal: Promo spesial, hujan deras, dll.">{{ old('keterangan') }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
              <button type="submit" class="btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                  <polyline points="17 21 17 13 7 13 7 21"></polyline>
                  <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span>Simpan Data Omzet</span>
              </button>
            </div>

          </form>

        </div>
      @else
        <!-- Banner Mode Read-Only Pemilik Usaha -->
        <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 14px; padding: 24px; color: #1E40AF;">
          <div style="display: flex; align-items: flex-start; gap: 14px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #DBEAFE; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #1D4ED8;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </div>
            <div>
              <h3 style="margin: 0 0 6px 0; font-size: 16px; font-weight: 700; color: #1E3A8A;">Mode Pemantauan (Read-Only)</h3>
              <p style="margin: 0; font-size: 13.5px; color: #1E40AF; line-height: 1.5;">
                Sebagai **Pemilik Usaha**, Anda memiliki hak akses untuk memantau dan mengaudit seluruh data historis penjualan harian Syafana Fried Chicken. Penginputan dan perubahan data omzet harian dikelola oleh Bagian Keuangan.
              </p>
            </div>
          </div>
        </div>
      @endif
    </div>

    <!-- Right Column: Information Cards -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
      
      <!-- Card 1: Target BEP -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
          <div style="width: 32px; height: 32px; border-radius: 8px; background: #FFF4ED; display: flex; align-items: center; justify-content: center; color: #9A3400;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
          </div>
          <span style="font-size: 13px; font-weight: 700; color: #0F172A;">Target BEP Harian</span>
        </div>
        <div style="font-size: 22px; font-weight: 800; color: #9A3400; margin-bottom: 6px;">
          Rp {{ number_format($bep, 0, ',', '.') }}
        </div>
        <div style="font-size: 12px; color: #64748B; line-height: 1.4;">
          Batas minimal omzet per hari agar operasional toko tetap berada pada zona aman.
        </div>
      </div>

      <!-- Card 2: Ringkasan Data -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
          RINGKASAN DATA
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
          <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
            <span style="color: #64748B;">Total Hari Aktif:</span>
            <span style="font-weight: 700; color: #0F172A;">{{ \App\Models\Omzet::count() }} Hari</span>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
            <span style="color: #64748B;">Omzet Terakhir:</span>
            <span style="font-weight: 700; color: #0F172A;">
              @php $lastOmz = \App\Models\Omzet::orderByDesc('tanggal')->first(); @endphp
              {{ $lastOmz ? 'Rp ' . number_format($lastOmz->jumlah_omzet, 0, ',', '.') : '-' }}
            </span>
          </div>
        </div>
      </div>

      @if(auth()->user()->isKeuangan())
        <!-- Card 3: Tips Input (Keuangan) -->
        <div style="background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 14px; padding: 18px; font-size: 12.5px; color: #475569; line-height: 1.5;">
          <div style="font-weight: 700; color: #0F172A; margin-bottom: 8px;">💡 Petunjuk Pengisian:</div>
          <ul style="margin: 0; padding-left: 16px; display: flex; flex-direction: column; gap: 6px;">
            <li>Ketik angka nominal, sistem akan otomatis memformatnya ke Rupiah (misal: 850.000).</li>
            <li>Setiap tanggal hanya bisa diinput sekali. Data yang telah tersimpan dapat diedit pada tabel di bawah.</li>
          </ul>
        </div>
      @endif

    </div>

  </div>

  <!-- Bottom Section: Data Historis Omzet Table -->
  <div class="content-card">
    <div class="card-header-flex" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="card-header-icon">
          <svg viewBox="0 0 24 24">
            <line x1="18" y1="20" x2="18" y2="10"></line>
            <line x1="12" y1="20" x2="12" y2="4"></line>
            <line x1="6" y1="20" x2="6" y2="14"></line>
          </svg>
        </div>
        <h2 class="card-header-title">Data Historis Omzet</h2>
      </div>

      <!-- Filter Pilihan Jumlah Baris per Halaman -->
      <form method="GET" action="{{ route('omzet.index') }}" id="perPageForm" style="margin: 0; display: flex; align-items: center; gap: 8px;">
        <label for="per_page" style="font-size: 13px; font-weight: 600; color: #64748B;">Tampilkan:</label>
        <select name="per_page" id="per_page" onchange="document.getElementById('perPageForm').submit();"
                style="padding: 6px 12px; font-size: 13px; font-weight: 700; color: #0F172A; background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 8px; cursor: pointer; outline: none;">
          <option value="10" {{ (isset($perPage) && $perPage == 10) ? 'selected' : '' }}>10 baris</option>
          <option value="20" {{ (isset($perPage) && $perPage == 20) ? 'selected' : '' }}>20 baris</option>
          <option value="30" {{ (isset($perPage) && $perPage == 30) ? 'selected' : '' }}>30 baris</option>
        </select>
      </form>
    </div>

    <table class="tbl">
      <thead>
        <tr>
          <th style="width: 50px;">No</th>
          <th>Tanggal</th>
          <th style="text-align:right">Omzet (Rp)</th>
          <th style="text-align:right">Target Prediksi (Rp)</th>
          <th style="text-align:right">Selisih</th>
          <th style="text-align:center">Evaluasi Target</th>
          <th style="text-align:center">vs BEP</th>
          <th>Keterangan</th>
          @if(auth()->user()->isKeuangan())
            <th style="text-align:right; width: 150px;">Aksi</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @forelse($riwayat as $index => $r)
        @php
          $tglStr = \Carbon\Carbon::parse($r->tanggal)->format('Y-m-d');
          $predItem = $prediksiMap[$tglStr] ?? null;
          $nilaiPrediksi = $predItem ? (float) $predItem->nilai_prediksi : null;
          $selisih = $nilaiPrediksi !== null ? ((float) $r->jumlah_omzet - $nilaiPrediksi) : null;
          $isTutup = str_contains(strtolower($r->keterangan ?? ''), 'tutup') || (float)$r->jumlah_omzet == 0;
        @endphp
        <tr>
          <td style="color:#94A3B8;font-weight:600">{{ ($riwayat->currentPage() - 1) * $riwayat->perPage() + $loop->iteration }}</td>
          <td style="font-weight:700">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
          <td style="text-align:right;font-weight:800;color:#0F172A">
            {{ number_format($r->jumlah_omzet, 0, ',', '.') }}
          </td>
          <td style="text-align:right;font-weight:600;color:#475569">
            {{ $nilaiPrediksi !== null ? number_format($nilaiPrediksi, 0, ',', '.') : '—' }}
          </td>
          <td style="text-align:right;font-weight:700">
            @if($selisih !== null)
              @if($selisih >= 0)
                <span style="color:#166534">+{{ number_format($selisih, 0, ',', '.') }}</span>
              @else
                <span style="color:#DC2626">{{ number_format($selisih, 0, ',', '.') }}</span>
              @endif
            @else
              <span style="color:#94A3B8">—</span>
            @endif
          </td>
          <td style="text-align:center">
            @if($isTutup)
              <span class="pill" style="background:#F1F5F9; color:#475569; border:1px solid #CBD5E1;">Tutup Toko</span>
            @elseif($nilaiPrediksi !== null)
              @if($r->jumlah_omzet >= $nilaiPrediksi)
                <span class="pill pill-aman" style="background:#DCFCE7; color:#166534; border:1px solid #86EFAC;">✔ Favorable</span>
              @else
                <span class="pill pill-bahaya" style="background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5;">✘ Unfavorable</span>
              @endif
            @else
              <span class="pill" style="background:#F8FAFC; color:#64748B; border:1px solid #E2E8F0;">— N/A</span>
            @endif
          </td>
          <td style="text-align:center">
            @if($r->jumlah_omzet >= $bep)
              <span class="pill pill-aman">✔ Aman</span>
            @else
              <span class="pill pill-bahaya">✘ Bawah BEP</span>
            @endif
          </td>
          <td style="color:#64748B;font-size:13px">{{ $r->keterangan ?? '—' }}</td>
          @if(auth()->user()->isKeuangan())
            <td style="text-align:right;">
              <div style="display:inline-flex;align-items:center;gap:6px;">
                <!-- Tombol Edit -->
                <a href="{{ route('omzet.edit', $r->id_omzet) }}" class="btn-outline" style="padding: 6px 12px; font-size: 12px; border-radius: 8px; text-decoration: none; color: #334155; font-weight: 600;" title="Edit Data Omzet">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                  </svg>
                  <span>Edit</span>
                </a>

                <!-- Tombol Delete (Hapus) -->
                <form method="POST" action="{{ route('omzet.destroy', $r->id_omzet) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data omzet tanggal {{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}?');" style="margin: 0; display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FCA5A5; padding: 6px 12px; font-size: 12px; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;" title="Hapus Data Omzet">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    <span>Hapus</span>
                  </button>
                </form>
              </div>
            </td>
          @endif
        </tr>
        @empty
        <tr>
          <td colspan="{{ auth()->user()->isKeuangan() ? '9' : '8' }}" style="text-align:center;padding:40px;color:#94A3B8">
            Belum ada data omzet.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <div class="pagination-wrapper">
      {{ $riwayat->links() }}
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const displayInput = document.getElementById('jumlah_display');
  const hiddenInput  = document.getElementById('jumlah');

  function formatRupiah(value) {
    let raw = value.replace(/[^0-9]/g, '');
    if (!raw) return '';
    return new Intl.NumberFormat('id-ID').format(raw);
  }

  function getRawNumber(value) {
    return value.replace(/[^0-9]/g, '');
  }

  if (displayInput) {
    if (displayInput.value) {
      const raw = getRawNumber(displayInput.value);
      displayInput.value = formatRupiah(raw);
      if (hiddenInput) hiddenInput.value = raw;
    }

    displayInput.addEventListener('input', function () {
      const raw = getRawNumber(this.value);
      this.value = formatRupiah(raw);
      if (hiddenInput) hiddenInput.value = raw;
    });

    displayInput.addEventListener('blur', function () {
      const raw = getRawNumber(this.value);
      if (hiddenInput) hiddenInput.value = raw;
    });
  }
});
</script>
@endsection
