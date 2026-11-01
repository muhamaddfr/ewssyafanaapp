@extends('layouts.app')
@section('title', 'Riwayat Notifikasi WhatsApp')

@section('content')
<div class="content-max-width">

  <!-- Page Title & Subtitle -->
  <div style="margin-bottom: 24px;">
    <h1 class="page-title" style="margin-bottom: 6px; font-size: 26px; font-weight: 800; color: #0F172A;">Riwayat Notifikasi WhatsApp</h1>
    <p style="color: #64748B; font-size: 14px; margin: 0;">
      Log pengiriman peringatan dini - hanya status <span style="color: #D97706; font-weight: 700;">Waspada</span> / <span style="color: #DC2626; font-weight: 700;">Bahaya</span>
    </p>
  </div>

  <!-- Top 3 Stat Cards Grid -->
  <div style="display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 20px; margin-bottom: 28px;">
    
    <!-- Card 1: Pratinjau Pesan -->
    <div style="background: #FFFFFF; border-radius: 14px; border: 1px solid #E2E8F0; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
      <div style="font-size: 11.5px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 12px;">
        PRATINJAU PESAN ({{ strtoupper($mode) }})
      </div>

      @if($notifikasiTerakhir)
        @php
          $stKat  = $notifikasiTerakhir->statusEws->kategori ?? ($notifikasiTerakhir->statusEws->status ?? 'Waspada');
          $stDate = $notifikasiTerakhir->waktu_kirim ? \Carbon\Carbon::parse($notifikasiTerakhir->waktu_kirim)->format('d F Y') : date('d F Y');
          $stTime = $notifikasiTerakhir->waktu_kirim ? \Carbon\Carbon::parse($notifikasiTerakhir->waktu_kirim)->format('H:i') : '17:44';
          $rasio  = $notifikasiTerakhir->statusEws->rasio ?? 88.32;
        @endphp
        <div style="background: #E2F8D8; border-radius: 12px; padding: 14px 16px; border: 1px solid #C8E6C9; color: #1E293B; font-size: 13px; line-height: 1.5;">
          <div style="font-weight: 700; margin-bottom: 4px;">
            [EWS Syafana] Status omzet: <span style="color: {{ $stKat === 'Bahaya' ? '#DC2626' : '#D97706' }}">{{ $stKat }}</span>.
          </div>
          <div>Rasio tren (R) = {{ number_format($rasio, 2, '.', '') }}% per {{ $stDate }}.</div>
          <div>Tren omzet melemah. Perlu perhatian.</div>
          <div style="text-align: right; font-size: 11px; color: #475569; margin-top: 8px;">
            {{ $stTime }} <span style="color: #16A34A; font-style: italic; font-weight: 600;">✔✔ Terkirim ({{ $mode }})</span>
          </div>
        </div>
      @else
        <div style="background: #E2F8D8; border-radius: 12px; padding: 14px 16px; border: 1px solid #C8E6C9; color: #1E293B; font-size: 13px; line-height: 1.5;">
          <div style="font-weight: 700; margin-bottom: 4px;">
            [EWS Syafana] Status omzet: <span style="color: #D97706">Waspada</span>.
          </div>
          <div>Rasio tren (R) = 88.32% per {{ date('d F Y') }}.</div>
          <div>Tren omzet melemah. Perlu perhatian.</div>
          <div style="text-align: right; font-size: 11px; color: #475569; margin-top: 8px;">
            17:44 <span style="color: #16A34A; font-style: italic; font-weight: 600;">✔✔ Terkirim ({{ $mode }})</span>
          </div>
        </div>
      @endif
    </div>

    <!-- Card 2: Total Terkirim -->
    <div style="background: #FFFFFF; border-radius: 14px; border: 1px solid #E2E8F0; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div style="font-size: 11.5px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase;">
          TOTAL TERKIRIM
        </div>
        <div style="width: 34px; height: 34px; border-radius: 8px; background: #FFF4ED; display: flex; align-items: center; justify-content: center; color: #9A3400;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="22" y1="2" x2="11" y2="13"></line>
            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
          </svg>
        </div>
      </div>

      <div style="margin: 10px 0 4px 0;">
        <span style="font-size: 42px; font-weight: 800; color: #0F172A; line-height: 1;">{{ $totalHariIni > 0 ? $totalHariIni : 1 }}</span>
        <span style="font-size: 14px; font-weight: 600; color: #64748B; margin-left: 6px;">pesan hari ini</span>
      </div>

      <div style="font-size: 12.5px; font-weight: 600; color: #16A34A; display: flex; align-items: center; gap: 6px;">
        <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #16A34A;"></span>
        <span>Mode {{ $mode }} aktif</span>
      </div>
    </div>

    <!-- Card 3: Notifikasi Terakhir -->
    <div style="background: #FFFFFF; border-radius: 14px; border: 1px solid #E2E8F0; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div style="font-size: 11.5px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase;">
          NOTIFIKASI TERAKHIR
        </div>
        <div style="width: 34px; height: 34px; border-radius: 8px; background: #FEF3C7; display: flex; align-items: center; justify-content: center; color: #D97706;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
      </div>

      <div>
        <div style="font-size: 22px; font-weight: 800; color: #0F172A; margin: 8px 0 2px 0;">
          @if($notifikasiTerakhir && $notifikasiTerakhir->waktu_kirim)
            {{ \Carbon\Carbon::parse($notifikasiTerakhir->waktu_kirim)->format('d M Y') }}
          @else
            30 Jul 2026
          @endif
        </div>
        <div style="font-size: 12.5px; color: #64748B; font-weight: 500;">
          Pukul {{ $notifikasiTerakhir && $notifikasiTerakhir->waktu_kirim ? \Carbon\Carbon::parse($notifikasiTerakhir->waktu_kirim)->format('H:i') : '17:44' }} WIB
        </div>
      </div>

      <div style="margin-top: 10px;">
        @php
          $katBadge = strtoupper($notifikasiTerakhir->statusEws->kategori ?? ($notifikasiTerakhir->statusEws->status ?? 'WASPADA'));
        @endphp
        @if($katBadge === 'BAHAYA')
          <span style="background: #FEE2E2; color: #DC2626; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 6px; letter-spacing: 0.5px;">BAHAYA</span>
        @else
          <span style="background: #FEF3C7; color: #B45309; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 6px; letter-spacing: 0.5px;">WASPADA</span>
        @endif
      </div>
    </div>

  </div>

  <!-- Content Card: Log Pengiriman Notifikasi -->
  <div style="background: #FFFFFF; border-radius: 14px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.03); overflow: hidden;">
    
    <!-- Table Header Bar -->
    <div style="padding: 20px 24px; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
      <h2 style="font-size: 18px; font-weight: 700; color: #0F172A; margin: 0;">Log Pengiriman Notifikasi</h2>

      <div style="display: flex; items-center; gap: 12px;">
        
        <!-- Filter Form Dropdown -->
        <form method="GET" action="{{ route('notifikasi.index') }}" style="margin: 0;">
          <select name="status" onchange="this.form.submit()" style="padding: 8px 14px; font-size: 13px; font-weight: 600; color: #475569; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 8px; cursor: pointer;">
            <option value="">⚙ Filter Status</option>
            <option value="Waspada" {{ request('status') == 'Waspada' ? 'selected' : '' }}>Filter: WASPADA</option>
            <option value="Bahaya" {{ request('status') == 'Bahaya' ? 'selected' : '' }}>Filter: BAHAYA</option>
          </select>
        </form>

        <!-- Unduh Log Button -->
        <a href="{{ route('notifikasi.unduh') }}" style="background: #9A3400; color: #FFFFFF; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(154,52,0,0.2);">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
          <span>Unduh Log</span>
        </a>

      </div>
    </div>

    <!-- Table -->
    <table style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; text-align: left;">
          <th style="padding: 14px 24px; font-size: 11px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase; width: 140px;">WAKTU KIRIM</th>
          <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase; width: 110px;">STATUS</th>
          <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase; width: 130px;">NOMOR TUJUAN</th>
          <th style="padding: 14px 20px; font-size: 11px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase;">ISI PESAN</th>
          <th style="padding: 14px 24px; font-size: 11px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase; text-align: right; width: 130px;">STATUS KIRIM</th>
        </tr>
      </thead>
      <tbody>
        @forelse($notifikasiList as $n)
          @php
            $kat      = strtoupper($n->statusEws->kategori ?? ($n->statusEws->status ?? 'WASPADA'));
            $noRaw    = $n->nomor_tujuan ?? env('FONNTE_TARGET_WA', '6285866131730');
            $noClean  = str_starts_with($noRaw, '62') ? '0' . substr($noRaw, 2) : $noRaw;
            
            // Format stacked phone string e.g. 0858-\nxxxx-\n1730
            if (strlen($noClean) >= 10) {
                $noLine1 = substr($noClean, 0, 4) . '-';
                $noLine2 = 'xxxx-';
                $noLine3 = substr($noClean, -4);
            } else {
                $noLine1 = $noClean;
                $noLine2 = '';
                $noLine3 = '';
            }

            $dateFormatted = $n->waktu_kirim ? \Carbon\Carbon::parse($n->waktu_kirim)->format('d M Y') : date('d M Y');
            $timeFormatted = $n->waktu_kirim ? \Carbon\Carbon::parse($n->waktu_kirim)->format('H:i') : '17:44';
          @endphp
          <tr style="border-bottom: 1px solid #F1F5F9;">
            
            <!-- WAKTU KIRIM -->
            <td style="padding: 16px 24px; vertical-align: top;">
              <div style="font-size: 14px; font-weight: 800; color: #0F172A; line-height: 1.2;">{{ $dateFormatted }}</div>
              <div style="font-size: 12px; color: #64748B; margin-top: 4px;">{{ $timeFormatted }}</div>
            </td>

            <!-- STATUS -->
            <td style="padding: 16px 20px; vertical-align: top;">
              @if($kat === 'BAHAYA')
                <span style="background: #FEE2E2; color: #DC2626; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 6px; letter-spacing: 0.5px; display: inline-block;">BAHAYA</span>
              @else
                <span style="background: #FEF3C7; color: #B45309; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 6px; letter-spacing: 0.5px; display: inline-block;">WASPADA</span>
              @endif
            </td>

            <!-- NOMOR TUJUAN -->
            <td style="padding: 16px 20px; vertical-align: top; font-size: 13.5px; font-weight: 600; color: #334155; line-height: 1.3;">
              <div>{{ $noLine1 }}</div>
              @if($noLine2)<div>{{ $noLine2 }}</div>@endif
              @if($noLine3)<div>{{ $noLine3 }}</div>@endif
            </td>

            <!-- ISI PESAN -->
            <td style="padding: 16px 20px; vertical-align: top; font-size: 13.5px; color: #334155; line-height: 1.5;">
              @if($kat === 'BAHAYA')
                <span style="margin-right: 4px;">🚨</span>
              @else
                <span style="margin-right: 4px;">⚠️</span>
              @endif
              <span>{{ Str::limit(str_replace("\n", ' ', $n->pesan), 85) }}</span>
            </td>

            <!-- STATUS KIRIM -->
            <td style="padding: 16px 24px; vertical-align: top; text-align: right;">
              @if($n->terkirim || $n->status_kirim === 'Terkirim')
                <span style="background: #DCFCE7; color: #16A34A; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 12px; letter-spacing: 0.5px; display: inline-block;">TERKIRIM</span>
              @else
                <span style="background: #FEE2E2; color: #DC2626; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 12px; letter-spacing: 0.5px; display: inline-block;">GAGAL</span>
              @endif
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="5" style="text-align: center; padding: 48px; color: #94A3B8; font-size: 14px;">
              Belum ada riwayat notifikasi yang terkirim. Jalankan peramalan pada menu <a href="{{ route('prediksi.index') }}" style="color: #9A3400; font-weight: 700; text-decoration: underline;">Hasil Prediksi</a>.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <!-- Table Footer Pagination -->
    <div style="padding: 16px 24px; border-top: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: 13px; color: #64748B;">
      <div>
        Menampilkan {{ $notifikasiList->firstItem() ?? 0 }}-{{ $notifikasiList->lastItem() ?? 0 }} dari {{ $notifikasiList->total() }} entri
      </div>
      <div>
        {{ $notifikasiList->links() }}
      </div>
    </div>

  </div>

</div>
@endsection
