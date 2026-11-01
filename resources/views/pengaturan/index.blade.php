@extends('layouts.app')
@section('title', 'Pengaturan EWS')

@section('content')
<div class="content-max-width">

  <!-- Breadcrumb -->
  <div class="breadcrumb">
    <span>EWS Syafana Fried Chicken</span>
    <span class="breadcrumb-separator">›</span>
    <span class="active-crumb">Pengaturan EWS</span>
  </div>

  <!-- Page Title -->
  <h1 class="page-title">Pengaturan EWS</h1>

  <!-- Flash Alerts -->
  @if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 24px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
        <polyline points="22 4 12 14.01 9 11.01"></polyline>
      </svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-error" style="margin-bottom: 24px;">
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

    <!-- Left Column: Form Card -->
    <div>
      <div class="form-card">
        
        <!-- Card Header -->
        <div class="card-header-flex">
          <div class="card-header-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9A3400" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="3"></circle>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
          </div>
          <h2 class="card-header-title">Pengaturan Target BEP Harian</h2>
        </div>

        <!-- Setting Form -->
        <form method="POST" action="{{ route('pengaturan.update') }}">
          @csrf

          <!-- Target BEP Harian (Rp) -->
          <div class="form-group">
            <label for="bep_harian_display" class="form-label">Target BEP Harian (Rp)</label>
            <div class="input-prefix-box">
              <span class="input-prefix">Rp</span>
              <input type="text" id="bep_harian_display" class="form-control"
                     value="{{ old('bep_harian', $bepHarian) ? number_format((float)old('bep_harian', $bepHarian), 0, ',', '.') : '' }}" required
                     placeholder="Contoh: 400.000" autocomplete="off" inputmode="numeric">
              <input type="hidden" name="bep_harian" id="bep_harian" value="{{ old('bep_harian', (int)$bepHarian) }}">
            </div>
            <p class="form-hint" style="margin-top: 6px;">
              Batas titik impas (*Break Even Point*) minimal omzet harian operasional toko Syafana Fried Chicken.
            </p>
          </div>

          <!-- Nomor WhatsApp Tujuan Notifikasi EWS -->
          <div class="form-group">
            <label for="nomor_wa" class="form-label">Nomor WhatsApp Pemilik Usaha (Target Notifikasi EWS)</label>
            <div class="input-prefix-box">
              <span class="input-prefix">📱</span>
              <input type="text" name="nomor_wa" id="nomor_wa" class="form-control"
                     value="{{ old('nomor_wa', $nomorWa) }}" required
                     placeholder="Contoh: 081234567890 atau 6281234567890">
            </div>
            <p class="form-hint" style="margin-top: 6px;">
              Nomor WhatsApp Pemilik Usaha yang menerima pesan peringatan EWS otomatis saat status *Waspada* atau *Bahaya*.
            </p>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <button type="submit" class="btn-primary">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
              </svg>
              <span>Simpan Pengaturan</span>
            </button>
          </div>

        </form>

      </div>
    </div>

    <!-- Right Column: Info Panel -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
      
      <!-- Card 1: BEP Info -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
          <div style="width: 32px; height: 32px; border-radius: 8px; background: #FFF4ED; display: flex; align-items: center; justify-content: center; color: #9A3400;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
          </div>
          <span style="font-size: 13px; font-weight: 700; color: #0F172A;">Dampak Perubahan BEP & Nomor WA</span>
        </div>
        <div style="font-size: 12.5px; color: #475569; line-height: 1.5;">
          Perubahan target BEP harian dan Nomor WhatsApp akan langsung digunakan oleh sistem EWS untuk evaluasi status dan tujuan pesan notifikasi otomatis.
        </div>
      </div>

      <!-- Card 2: Environment Variables -->
      <div style="background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 14px; padding: 18px; font-size: 12.5px; color: #475569; line-height: 1.5;">
        <div style="font-weight: 700; color: #0F172A; margin-bottom: 8px;">🔒 Keamanan Konfigurasi:</div>
        <ul style="margin: 0; padding-left: 16px; display: flex; flex-direction: column; gap: 6px;">
          <li>Token API Fonnte WhatsApp tersimpan dengan aman pada file lingkungan <code>.env</code>.</li>
          <li>Target BEP Harian dan Nomor WhatsApp Pemilik Usaha dapat diperbarui secara fleksibel dari halaman ini.</li>
        </ul>
      </div>

    </div>

  </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const displayInput = document.getElementById('bep_harian_display');
  const hiddenInput  = document.getElementById('bep_harian');

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
