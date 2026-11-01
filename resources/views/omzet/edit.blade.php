@extends('layouts.app')
@section('title', 'Edit Data Omzet')

@section('content')
<div class="content-max-width">

  <!-- Breadcrumb -->
  <div class="breadcrumb">
    <span>EWS Syafana Fried Chicken</span>
    <span class="breadcrumb-separator">›</span>
    <a href="{{ route('omzet.index') }}" style="color: #64748B; text-decoration: none;">Data Omzet</a>
    <span class="breadcrumb-separator">›</span>
    <span class="active-crumb">Edit Data Omzet</span>
  </div>

  <!-- Page Title -->
  <h1 class="page-title">Edit Data Omzet Harian</h1>

  <!-- Flash Alerts -->
  @if($errors->any())
    <div class="alert alert-error">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
      <span>{{ $errors->first() }}</span>
    </div>
  @endif

  <!-- 2-Column Grid Layout -->
  <div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 24px; align-items: start;">

    <!-- Left Column: Edit Form Card -->
    <div>
      <div class="form-card">
        
        <!-- Card Header -->
        <div class="card-header-flex">
          <div class="card-header-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9A3400" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
          </div>
          <h2 class="card-header-title">Form Edit Omzet</h2>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="{{ route('omzet.update', $omzet->id_omzet) }}">
          @csrf
          @method('PUT')

          <!-- Tanggal Omzet -->
          <div class="form-group">
            <label for="tanggal" class="form-label">Tanggal Omzet</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control"
                   value="{{ old('tanggal', $omzet->tanggal ? $omzet->tanggal->format('Y-m-d') : '') }}" required>
          </div>

          <!-- Jumlah Omzet (Rp) -->
          <div class="form-group">
            <label for="jumlah_display" class="form-label">Jumlah Omzet (Rp)</label>
            <div class="input-prefix-box">
              <span class="input-prefix">Rp</span>
              <input type="text" id="jumlah_display" class="form-control"
                     value="{{ old('jumlah', (int)$omzet->jumlah_omzet) ? number_format((float)old('jumlah', (int)$omzet->jumlah_omzet), 0, ',', '.') : '' }}" required
                     placeholder="Contoh: 850.000" autocomplete="off" inputmode="numeric">
              <input type="hidden" name="jumlah" id="jumlah" value="{{ old('jumlah', (int)$omzet->jumlah_omzet) }}">
            </div>
            <div class="bep-info">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#9A3400" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
              </svg>
              <span>BEP harian: Rp {{ number_format(\App\Models\Pengaturan::get('bep_harian', env('EWS_BEP_HARIAN', 400000)), 0, ',', '.') }}</span>
            </div>
          </div>

          <!-- Keterangan (opsional) -->
          <div class="form-group">
            <label for="keterangan" class="form-label">Keterangan (opsional)</label>
            <textarea name="keterangan" id="keterangan" class="form-control"
                      placeholder="Misal: Promo spesial, hujan deras, dll.">{{ old('keterangan', $omzet->keterangan) }}</textarea>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <button type="submit" class="btn-primary">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
              </svg>
              <span>Simpan Perubahan</span>
            </button>

            <a href="{{ route('omzet.index') }}" class="btn-outline">
              <span>Batal</span>
            </a>
          </div>

        </form>

      </div>
    </div>

    <!-- Right Column: Information Panel -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div style="font-size: 12px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
          INFORMASI DATA
        </div>
        <div style="font-size: 13px; color: #334155; line-height: 1.5;">
          Perubahan pada data omzet harian akan mempengaruhi perhitungan peramalan ARIMA berikutnya. Pastikan tanggal dan nominal omzet diisi sesuai data aktual transaksi toko.
        </div>
      </div>
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
