@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php
  $bep = (float) \App\Models\Pengaturan::get('bep_harian', env('EWS_BEP_HARIAN', 400000));
  
  $hasStatus  = $statusTerbaru !== null;
  $stKategori = $hasStatus ? ($statusTerbaru->kategori ?? ($statusTerbaru->status ?? 'Waspada')) : 'Belum Ada Data';
  $stRasio    = $hasStatus ? $statusTerbaru->rasio : null;
  $stAktual   = $hasStatus ? $statusTerbaru->rata_aktual : 0;
  $stPrediksi = $hasStatus ? $statusTerbaru->rata_prediksi : 0;

  // Background banner berdasarkan status EWS
  $bannerBg = match($stKategori) {
    'Aman'    => 'linear-gradient(135deg, #16A34A 0%, #15803D 100%)',
    'Waspada' => 'linear-gradient(135deg, #EA580C 0%, #F97316 100%)',
    'Bahaya'  => 'linear-gradient(135deg, #DC2626 0%, #991B1B 100%)',
    default   => 'linear-gradient(135deg, #64748B 0%, #475569 100%)', // Netral Abu-abu
  };

  $dotBg = match($stKategori) {
    'Aman'    => '#BBF7D0',
    'Waspada' => '#FEF3C7',
    'Bahaya'  => '#FECACA',
    default   => '#CBD5E1',
  };
@endphp

<div class="content-max-width">

  <!-- Page Header -->
  <div style="margin-bottom: 24px;">
    <h1 class="page-title" style="margin-bottom: 4px; font-size: 26px; font-weight: 800; color: #EA580C;">Dashboard</h1>
    <p style="color: #64748B; font-size: 14px; margin: 0;">
      Ringkasan kondisi omzet • {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
    </p>
  </div>

  <!-- Top Banner & 3 Stacked Cards (2-Column Grid) -->
  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 28px; align-items: stretch;">
    
    <!-- Left Banner: Status Peringatan Dini Dinamis -->
    <div style="background: {{ $bannerBg }}; border-radius: 16px; padding: 28px; color: #FFFFFF; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 14px rgba(0,0,0,0.15); position: relative; overflow: hidden;">
      
      <div>
        <div style="font-size: 11.5px; font-weight: 700; color: rgba(255,255,255,0.85); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 12px;">
          STATUS PERINGATAN DINI
        </div>

        <div style="font-size: 32px; font-weight: 800; color: #FFFFFF; display: flex; align-items: center; gap: 10px; margin-bottom: 12px; line-height: 1;">
          <span style="display: inline-block; width: 14px; height: 14px; border-radius: 50%; background: {{ $dotBg }};"></span>
          <span>{{ $hasStatus ? $stKategori : 'Belum Ada Data Prediksi' }}</span>
        </div>

        <p style="font-size: 13.5px; color: rgba(255,255,255,0.92); line-height: 1.5; margin: 0; max-width: 580px;">
          @if(!$hasStatus)
            Belum ada hasil peramalan EWS di database. Silakan jalankan peramalan terlebih dahulu pada menu <a href="{{ route('prediksi.index') }}" style="color: #FEF3C7; font-weight: 700; text-decoration: underline;">Hasil Prediksi</a>.
          @elseif($stKategori === 'Aman')
            Rasio tren R = {{ number_format($stRasio, 2) }}% berada di atas target. Kondisi penjualan stabil dan memenuhi target operasional toko.
          @elseif($stKategori === 'Bahaya')
            Rasio tren R = {{ number_format($stRasio, 2) }}% berada di bawah rentang kritis (<70%). Peringatan darurat telah dikirimkan untuk evaluasi mendesak.
          @else
            Rasio tren R = {{ number_format($stRasio, 2) }}% berada di rentang 70%—90%. Notifikasi WhatsApp otomatis telah dikirim ke Pemilik Usaha untuk peninjauan strategi operasional.
          @endif
        </p>
      </div>

      <!-- Bottom 3 Mini Metric Stat Boxes -->
      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-top: 24px;">
        
        <div style="background: rgba(0,0,0,0.18); border-radius: 12px; padding: 14px 16px;">
          <div style="font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.75); margin-bottom: 4px;">Ratio F/A</div>
          <div style="font-size: 22px; font-weight: 800; color: #FFFFFF;">
            {{ $stRasio !== null ? number_format($stRasio, 2) . '%' : '—' }}
          </div>
        </div>

        <div style="background: rgba(0,0,0,0.18); border-radius: 12px; padding: 14px 16px;">
          <div style="font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.75); margin-bottom: 4px;">Actual Revenue</div>
          <div style="font-size: 19px; font-weight: 800; color: #FFFFFF;">
            {{ $stAktual > 0 ? 'Rp ' . number_format($stAktual, 0, ',', '.') : '—' }}
          </div>
        </div>

        <div style="background: rgba(0,0,0,0.18); border-radius: 12px; padding: 14px 16px;">
          <div style="font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.75); margin-bottom: 4px;">Predicted</div>
          <div style="font-size: 19px; font-weight: 800; color: #FFFFFF;">
            {{ $stPrediksi > 0 ? 'Rp ' . number_format($stPrediksi, 0, ',', '.') : '—' }}
          </div>
        </div>

      </div>

    </div>

    <!-- Right Column: 3 Stacked Stat Cards -->
    <div style="display: flex; flex-direction: column; gap: 14px; justify-content: space-between;">
      
      <!-- Card 1: Hari Data Terbaru -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div>
          <div style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Hari Data Terbaru</div>
          <div style="font-size: 22px; font-weight: 800; color: #0F172A; margin-top: 4px;">
            {{ $hariDataTerbaru ? \Carbon\Carbon::parse($hariDataTerbaru)->format('Y-m-d') : '-' }}
          </div>
        </div>
        <div style="width: 40px; height: 40px; border-radius: 10px; background: #FFF7ED; display: flex; align-items: center; justify-content: center; color: #EA580C;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
        </div>
      </div>

      <!-- Card 2: Rata Omzet / Hari -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div>
          <div style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Rata Omzet / Hari</div>
          <div style="font-size: 22px; font-weight: 800; color: #0F172A; margin-top: 4px;">
            Rp {{ number_format($rataOmzetPerHari, 0, ',', '.') }}
          </div>
        </div>
        <div style="width: 40px; height: 40px; border-radius: 10px; background: #FFF7ED; display: flex; align-items: center; justify-content: center; color: #EA580C;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
            <polyline points="17 6 23 6 23 12"></polyline>
          </svg>
        </div>
      </div>

      <!-- Card 3: Total Omzet -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div>
          <div style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Total Omzet</div>
          <div style="font-size: 22px; font-weight: 800; color: #0F172A; margin-top: 4px;">
            Rp {{ number_format($totalOmzet, 0, ',', '.') }}
          </div>
        </div>
        <div style="width: 40px; height: 40px; border-radius: 10px; background: #FFF7ED; display: flex; align-items: center; justify-content: center; color: #EA580C;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"></path>
            <path d="M3 5v14a2 2 0 0 0 2 2h16v-5"></path>
            <path d="M18 12a2 2 0 0 0 0 4h4v-4z"></path>
          </svg>
        </div>
      </div>

    </div>

  </div>

  <!-- Middle Card: Omzet Aktual vs Prediksi (7 Hari) Chart -->
  <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; margin-bottom: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
    
    <!-- Chart Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
      <div>
        <h2 style="font-size: 17px; font-weight: 700; color: #0F172A; margin: 0 0 4px 0;">Omzet Aktual vs Prediksi (7 Hari)</h2>
        <p style="font-size: 13px; color: #64748B; margin: 0;">Visualisasi perbandingan performa real-time terhadap model peramalan.</p>
      </div>

      <!-- Chart Legend -->
      <div style="display: flex; align-items: center; gap: 20px; font-size: 13px; font-weight: 600; color: #475569;">
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #2563EB;"></span>
          <span>Aktual</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #EA580C;"></span>
          <span>Prediksi</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="display: inline-block; width: 16px; height: 0; border-top: 2px dashed #EF4444;"></span>
          <span>Ambang BEP (Rp {{ number_format($bep, 0, ',', '.') }})</span>
        </div>
      </div>
    </div>

    <!-- Chart Canvas Container -->
    <div style="width: 100%; height: 300px; position: relative;">
      <canvas id="dashboardChart"></canvas>
    </div>
  </div>

  <!-- Bottom 2 Grid Cards -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    
    <!-- Left Card: Omzet 7 Hari Terakhir -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin: 0;">Omzet 7 Hari Terakhir</h3>
        <a href="{{ route('omzet.index') }}" style="font-size: 13px; font-weight: 700; color: #EA580C; text-decoration: none;">Lihat Semua</a>
      </div>

      <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
        <thead>
          <tr style="border-bottom: 1px solid #E2E8F0; text-align: left;">
            <th style="padding: 10px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">TANGGAL</th>
            <th style="padding: 10px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: center;">OMZET</th>
            <th style="padding: 10px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: right;">STATUS</th>
          </tr>
        </thead>
        <tbody>
          @forelse($omzetTerakhir7->take(7) as $o)
            <tr style="border-bottom: 1px solid #F1F5F9;">
              <td style="padding: 12px 0; font-weight: 600; color: #334155;">
                {{ \Carbon\Carbon::parse($o->tanggal)->format('d F Y') }}
              </td>
              <td style="padding: 12px 0; text-align: center; font-weight: 700; color: #0F172A;">
                Rp {{ number_format($o->jumlah_omzet, 0, ',', '.') }}
              </td>
              <td style="padding: 12px 0; text-align: right;">
                @if($o->jumlah_omzet >= $bep)
                  <span style="background: #DCFCE7; color: #16A34A; font-weight: 700; padding: 4px 10px; border-radius: 12px; font-size: 11px;">Di atas BEP</span>
                @else
                  <span style="background: #FEE2E2; color: #DC2626; font-weight: 700; padding: 4px 10px; border-radius: 12px; font-size: 11px;">Bawah BEP</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" style="text-align: center; padding: 20px; color: #94A3B8;">Belum ada data omzet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Right Card: Prediksi 7 Hari Terbaru -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin: 0;">Prediksi 7 Hari Terbaru</h3>
        <a href="{{ route('prediksi.index') }}" style="font-size: 13px; font-weight: 700; color: #EA580C; text-decoration: none;">Detail Model</a>
      </div>

      <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
        <thead>
          <tr style="border-bottom: 1px solid #E2E8F0; text-align: left;">
            <th style="padding: 10px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">TANGGAL</th>
            <th style="padding: 10px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: right;">AKTUAL</th>
            <th style="padding: 10px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: right;">PREDIKSI</th>
          </tr>
        </thead>
        <tbody>
          @forelse($prediksiTerbaru7->take(7) as $p)
            @php
              $tglStr = $p->tanggal_prediksi instanceof \Carbon\Carbon ? $p->tanggal_prediksi->format('Y-m-d') : substr((string)$p->tanggal_prediksi, 0, 10);
              $aktVal = isset($aktualSetelahCutoff) ? $aktualSetelahCutoff->get($tglStr) : null;
            @endphp
            <tr style="border-bottom: 1px solid #F1F5F9;">
              <td style="padding: 12px 0; font-weight: 600; color: #334155;">
                {{ \Carbon\Carbon::parse($tglStr)->format('d M Y') }}
              </td>
              <td style="padding: 12px 0; text-align: right; color: #64748B; font-weight: 600;">
                {{ $aktVal ? number_format($aktVal->jumlah_omzet, 0, ',', '.') : '-' }}
              </td>
              <td style="padding: 12px 0; text-align: right; font-weight: 700; color: #EA580C;">
                {{ number_format($p->nilai_prediksi, 0, ',', '.') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" style="text-align: center; padding: 20px; color: #94A3B8;">Belum ada data peramalan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById('dashboardChart').getContext('2d');

  @php
    $timeline = [];

    if ($aktualHistorical7->isNotEmpty()) {
        foreach ($aktualHistorical7 as $h) {
            $dateStr = $h->tanggal instanceof \Carbon\Carbon ? $h->tanggal->format('Y-m-d') : substr((string)$h->tanggal, 0, 10);
            $label   = \Carbon\Carbon::parse($dateStr)->format('d/m');
            $timeline[$dateStr] = [
                'label'    => $label,
                'aktual'   => (float) $h->jumlah_omzet,
                'prediksi' => null,
            ];
        }
    }

    if ($prediksiTerbaru7->isNotEmpty()) {
        $cutoffStr = $latestCutoff instanceof \Carbon\Carbon ? $latestCutoff->format('Y-m-d') : substr((string)$latestCutoff, 0, 10);
        if (isset($timeline[$cutoffStr]) && $timeline[$cutoffStr]['aktual'] !== null) {
            $timeline[$cutoffStr]['prediksi'] = $timeline[$cutoffStr]['aktual'];
        }

        foreach ($prediksiTerbaru7 as $p) {
            $dateStr = $p->tanggal_prediksi instanceof \Carbon\Carbon ? $p->tanggal_prediksi->format('Y-m-d') : substr((string)$p->tanggal_prediksi, 0, 10);
            $label   = \Carbon\Carbon::parse($dateStr)->format('d/m');

            if (!isset($timeline[$dateStr])) {
                $timeline[$dateStr] = [
                    'label'    => $label,
                    'aktual'   => null,
                    'prediksi' => (float) $p->nilai_prediksi,
                ];
            } else {
                $timeline[$dateStr]['prediksi'] = (float) $p->nilai_prediksi;
            }
        }
    }

    ksort($timeline);

    $labelsArr       = array_column($timeline, 'label');
    $aktualDataArr   = array_column($timeline, 'aktual');
    $prediksiDataArr = array_column($timeline, 'prediksi');
  @endphp

  const labels = {!! json_encode($labelsArr) !!};
  const aktualData = {!! json_encode($aktualDataArr) !!};
  const prediksiData = {!! json_encode($prediksiDataArr) !!};
  const bepVal = {{ $bep }};
  const bepData = Array(labels.length).fill(bepVal);

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Aktual',
          data: aktualData,
          borderColor: '#2563EB',
          backgroundColor: 'rgba(37, 99, 235, 0.05)',
          borderWidth: 3,
          pointBackgroundColor: '#2563EB',
          pointRadius: 4,
          tension: 0.3,
          fill: false,
          spanGaps: true
        },
        {
          label: 'Prediksi',
          data: prediksiData,
          borderColor: '#EA580C',
          borderDash: [6, 4],
          borderWidth: 3,
          pointBackgroundColor: '#EA580C',
          pointRadius: 4,
          tension: 0.2,
          fill: false
        },
        {
          label: 'Ambang BEP',
          data: bepData,
          borderColor: '#EF4444',
          borderDash: [4, 4],
          borderWidth: 2,
          pointRadius: 0,
          pointHoverRadius: 0,
          tension: 0,
          fill: false
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: '#64748B', font: { family: 'Plus Jakarta Sans', size: 12 } }
        },
        y: {
          grid: { color: '#F1F5F9' },
          ticks: {
            color: '#64748B',
            font: { family: 'Plus Jakarta Sans', size: 12 },
            callback: function(value) {
              return value >= 1000 ? (value / 1000) + 'k' : value;
            }
          }
        }
      }
    },
    plugins: [{
      id: 'bepLineLabel',
      afterDraw(chart) {
        const { ctx, chartArea: { right }, scales: { y } } = chart;
        if (!y) return;
        const yPos = y.getPixelForValue(bepVal);
        if (yPos >= chart.chartArea.top && yPos <= chart.chartArea.bottom) {
          ctx.save();
          ctx.fillStyle = '#EF4444';
          ctx.font = '600 11px "Plus Jakarta Sans", sans-serif';
          ctx.textAlign = 'right';
          ctx.fillText('Ambang BEP (' + (bepVal / 1000) + 'k)', right - 8, yPos - 6);
          ctx.restore();
        }
      }
    }]
  });
});
</script>
@endsection
