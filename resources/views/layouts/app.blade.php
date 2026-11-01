<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'EWS Omzet') — Syafana Fried Chicken</title>
  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Layout Stylesheet -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @yield('styles')
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-top">
      
      <!-- Brand Logo -->
      <div class="sidebar-brand">
        <div class="brand-logo-icon">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 17L9 11L13 15L21 7" stroke="#EA580C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M17 7H21V11" stroke="#EA580C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="brand-info">
          <span class="brand-name">Syafana EWS</span>
          <span class="brand-sub">Management Portal</span>
        </div>
      </div>

      <!-- Navigation Menu Berdasarkan Peran -->
      <nav class="sidebar-nav">
        @auth
          @if(auth()->user()->isPemilik())
            <!-- ── STRUKTUR MENU PEMILIK USAHA ── -->
            
            <!-- 1. Beranda -->
            <div class="sidebar-nav-section">Beranda</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
              <svg viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
              </svg>
              <span>Dashboard</span>
            </a>

            <!-- 2. Data & Transaksi -->
            <div class="sidebar-nav-section" style="margin-top: 14px;">Data & Transaksi</div>
            <a href="{{ route('omzet.index') }}" class="nav-item {{ request()->routeIs('omzet*') ? 'active' : '' }}">
              <svg viewBox="0 0 24 24">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
              </svg>
              <span>Data Omzet</span>
            </a>

            <!-- 3. Analisis dan Peringatan Dini -->
            <div class="sidebar-nav-section" style="margin-top: 14px;">Analisis & Peringatan Dini</div>
            <a href="{{ route('notifikasi.index') }}" class="nav-item {{ request()->routeIs('notifikasi*') ? 'active' : '' }}">
              <svg viewBox="0 0 24 24">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
              </svg>
              <span>Notifikasi</span>
            </a>
            <a href="{{ route('prediksi.index') }}" class="nav-item {{ request()->routeIs('prediksi*') ? 'active' : '' }}">
              <svg viewBox="0 0 24 24">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
              </svg>
              <span>Hasil Prediksi</span>
            </a>
            <a href="{{ route('pengaturan.index') }}" class="nav-item {{ request()->routeIs('pengaturan*') ? 'active' : '' }}">
              <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
              </svg>
              <span>Pengaturan EWS</span>
            </a>

          @else
            <!-- ── STRUKTUR MENU BAGIAN KEUANGAN ── -->

            <!-- 1. Beranda -->
            <div class="sidebar-nav-section">Beranda</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
              <svg viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
              </svg>
              <span>Dashboard</span>
            </a>

            <!-- 2. Manajemen Data Omzet -->
            <div class="sidebar-nav-section" style="margin-top: 14px;">Manajemen Data Omzet</div>
            <a href="{{ route('omzet.index') }}" class="nav-item {{ request()->routeIs('omzet*') ? 'active' : '' }}">
              <svg viewBox="0 0 24 24">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
              </svg>
              <span>Data Omzet</span>
            </a>

            <!-- 3. Analisis dan Peringatan Dini -->
            <div class="sidebar-nav-section" style="margin-top: 14px;">Analisis & Peringatan Dini</div>
            <a href="{{ route('prediksi.index') }}" class="nav-item {{ request()->routeIs('prediksi*') ? 'active' : '' }}">
              <svg viewBox="0 0 24 24">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
              </svg>
              <span>Hasil Prediksi</span>
            </a>
          @endif
        @endauth
      </nav>
    </div>

    <!-- Sidebar Bottom (Keluar) -->
    <div class="sidebar-bottom">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="nav-item nav-item-logout">
          <svg viewBox="0 0 24 24">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span>Keluar</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- Main Container -->
  <div class="main">
    
    <!-- Topbar Header -->
    <header class="topbar">
      <div class="topbar-brand-title">
        Early Warning System Omzet
      </div>

      <div class="user-profile-widget">
        <div class="user-details">
          <span class="user-display-name">{{ auth()->user()->nama ?? 'Pengguna' }}</span>
          <span class="user-display-role">{{ auth()->user()->peran ?? 'Pengguna' }}</span>
        </div>
        <div class="user-avatar-circle">
          <svg viewBox="0 0 24 24">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </div>
      </div>
    </header>

    <!-- Page Body -->
    <main class="page-body">
      @yield('content')
    </main>
  </div>

  @yield('scripts')
</body>
</html>
