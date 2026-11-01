@extends(auth()->check() ? 'layouts.app' : 'auth.login')

@section('title', '403 — Akses Ditolak')

@section('content')
<div class="content-max-width" style="margin: 40px auto; max-width: 540px;">
  
  <!-- Form Card / Error Card -->
  <div class="form-card" style="text-align: center; padding: 44px 36px;">
    
    <!-- Lock / Shield Icon -->
    <div style="width: 64px; height: 64px; background: #FEF2F2; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
      </svg>
    </div>

    <!-- Title -->
    <h1 style="font-size: 24px; font-weight: 800; color: #0F172A; margin-bottom: 8px;">
      Akses Ditolak (403)
    </h1>

    <!-- Subtitle / Message -->
    <p style="font-size: 14px; color: #64748B; line-height: 1.6; margin-bottom: 28px;">
      {{ $exception->getMessage() ?: 'Akses ditolak: halaman ini khusus pemilik usaha.' }}
    </p>

    <!-- Action Button -->
    <div>
      <a href="{{ route('dashboard') }}" class="btn-primary" style="display: inline-flex; justify-content: center;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Kembali ke Dashboard</span>
      </a>
    </div>

  </div>

</div>
@endsection
