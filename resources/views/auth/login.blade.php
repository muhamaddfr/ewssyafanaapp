<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk — Early Warning System Omzet</title>
  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Stylesheet -->
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

  <div class="main-container">
    <div class="login-card">
      
      <!-- Panel Kiri -->
      <div class="login-left">
        <div class="left-header">
          <div class="brand-logo">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 17L9 11L13 15L21 7" stroke="#D84B00" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M17 7H21V11" stroke="#D84B00" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h1 class="left-title">Early Warning<br>System Omzet</h1>
          <p class="left-desc">Pemantauan & peramalan omzet harian berbasis ARIMA(2,1,1) dengan notifikasi WhatsApp otomatis.</p>
        </div>

        <div class="feature-list">
          <div class="feature-item">
            <div class="feature-icon">
              <svg viewBox="0 0 24 24">
                <path d="M23 6L13.5 15.5L8.5 10.5L1 18" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M17 6H23V12" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="feature-text">
              <span class="feature-title">Peramalan ARIMA(2,1,1)</span>
              <span class="feature-sub">Prediksi akurat performa harian</span>
            </div>
          </div>

          <div class="feature-item">
            <div class="feature-icon">
              <svg viewBox="0 0 24 24">
                <rect x="5" y="2" width="14" height="20" rx="4" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="7" r="1.5" fill="#FFFFFF"/>
                <circle cx="12" cy="12" r="1.5" fill="#FFFFFF"/>
                <circle cx="12" cy="17" r="1.5" fill="#FFFFFF"/>
              </svg>
            </div>
            <div class="feature-text">
              <span class="feature-title">Status EWS</span>
              <span class="feature-sub">Aman / Waspada / Bahaya</span>
            </div>
          </div>

          <div class="feature-item">
            <div class="feature-icon">
              <svg viewBox="0 0 24 24">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="feature-text">
              <span class="feature-title">Notifikasi WhatsApp</span>
              <span class="feature-sub">Real-time alerts via Fonnte</span>
            </div>
          </div>

          <div class="feature-item">
            <div class="feature-icon">
              <svg viewBox="0 0 24 24">
                <path d="M4 21v-7m4 7V10m4 7V3m4 18v-9m4 18V7" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="feature-text">
              <span class="feature-title">Simulasi & Backtesting</span>
              <span class="feature-sub">Validasi model cutoff statistik</span>
            </div>
          </div>
        </div>

        <div class="left-footer">
          Professional Forecasting for Syafana Fried Chicken.
        </div>
      </div>

      <!-- Panel Kanan -->
      <div class="login-right">
        <div class="right-header">
          <h2 class="right-title">Selamat Datang</h2>
          <p class="right-desc">Masuk ke akun Anda untuk melanjutkan ke dashboard pemantauan.</p>
        </div>

        @if ($errors->any())
          <div class="alert-error">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>{{ $errors->first() }}</span>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
          @csrf
          
          <div class="form-group">
            <div class="label-wrapper">
              <label for="email" class="form-label">EMAIL</label>
            </div>
            <div class="input-wrapper">
              <input type="email" name="email" id="email" class="form-control"
                     value="{{ old('email') }}" required autofocus
                     placeholder="nama@syafana.test">
            </div>
          </div>

          <div class="form-group">
            <div class="label-wrapper">
              <label for="password" class="form-label">PASSWORD</label>
              <a href="#" onclick="alert('Silakan hubungi administrator untuk mereset password.'); return false;" class="forgot-link">Lupa Password?</a>
            </div>
            <div class="input-wrapper">
              <input type="password" name="password" id="password" class="form-control"
                     required placeholder="••••••••">
              <button type="button" id="togglePasswordBtn" class="toggle-password" aria-label="Toggle password visibility">
                <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
          </div>

          <div class="checkbox-group">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember" class="checkbox-label">Ingat saya di perangkat ini</label>
          </div>

          <button type="submit" class="btn-submit">
            <span>Masuk</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </button>
        </form>

        <div class="right-footer">
          Syafana Fried Chicken · EWS v1.0 · {{ date('Y') }}
        </div>
      </div>

    </div>
  </div>

  <!-- Page Outer Footer -->
  <footer class="page-footer">
    <div class="footer-left">
      <div class="footer-brand">Early Warning System Omzet</div>
      <div class="footer-copyright">© {{ date('Y') }} Early Warning System Omzet. Professional Forecasting Solutions.</div>
    </div>
    <div class="footer-links">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
      <a href="#">Contact Support</a>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const toggleBtn = document.getElementById('togglePasswordBtn');
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');

      toggleBtn.addEventListener('click', function() {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        
        if (isPassword) {
          eyeIcon.innerHTML = `
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
          `;
        } else {
          eyeIcon.innerHTML = `
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          `;
        }
      });
    });
  </script>

</body>
</html>
