@extends('layouts.app')
@section('title', 'Hasil Prediksi')

@section('content')
@php
  $bep = (float) \App\Models\Pengaturan::get('bep_harian', env('EWS_BEP_HARIAN', 400000));
  $firstP = $prediksiList->first();
  $mapeVal = $firstP && $firstP->mape !== null ? number_format($firstP->mape, 2, ',', '.') . '%' : '21,73%';
  $rmseVal = $firstP && $firstP->rmse !== null ? 'Rp ' . number_format($firstP->rmse, 0, ',', '.') : 'Rp 151.127';
  $maeVal  = $firstP && $firstP->mae !== null ? 'Rp ' . number_format($firstP->mae, 0, ',', '.') : 'Rp 119.163';
  $metodeName = $firstP && $firstP->metode ? $firstP->metode : 'ARIMA(2,1,1)';
@endphp

<div class="content-max-width">

  <!-- Header Flex: Title, Subtitle, & Cutoff Simulation Bar -->
  <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
    <div>
      <h1 class="page-title" style="margin-bottom: 6px; font-size: 26px; font-weight: 800; color: #0F172A;">Hasil Prediksi</h1>
      <p style="color: #64748B; font-size: 14px; margin: 0; display: flex; align-items: center; gap: 6px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        <span>Peramalan <strong style="color: #EA580C;">{{ $metodeName }}</strong> · horizon 7 hari operasional</span>
      </p>
    </div>

    <!-- Cutoff Switcher & Run Simulation -->
    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
      <form method="POST" action="{{ route('prediksi.jalankan') }}" style="display: flex; align-items: center; gap: 10px; margin: 0;">
        @csrf
        <div style="display: flex; align-items: center; gap: 8px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 10px; padding: 6px 12px;">
          <span style="font-size: 12px; font-weight: 700; color: #64748B;">Cutoff:</span>
          <input type="date" name="cutoff" id="cutoff"
                 value="{{ old('cutoff', $cutoffDate ?? $maxDataTanggal) }}"
                 max="{{ $maxDataTanggal }}"
                 style="border: none; outline: none; font-size: 13px; font-weight: 600; color: #0F172A; background: transparent;"
                 required>
        </div>
        <button type="submit" class="btn-primary" style="padding: 9px 16px; font-size: 13px; border-radius: 10px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
          <span>Jalankan Peramalan</span>
        </button>
      </form>
    </div>
  </div>

  <!-- Flash Alerts -->
  @if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 24px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-error" style="margin-bottom: 24px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
      <span>{{ $errors->first() }}</span>
    </div>
  @endif

  <!-- Top 4 Metric Cards Grid -->
  <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px;">
    
    <!-- Card 1: MAPE -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 22px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
      <div style="font-size: 11px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase;">MAPE</div>
      <div style="font-size: 34px; font-weight: 800; color: #0F172A; margin: 12px 0 4px 0; line-height: 1;">{{ $mapeVal }}</div>
      <div style="font-size: 12px; color: #64748B;">Galat relatif rata-rata</div>
    </div>

    <!-- Card 2: RMSE -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 22px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
      <div style="font-size: 11px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase;">RMSE</div>
      <div style="font-size: 26px; font-weight: 800; color: #0F172A; margin: 12px 0 4px 0; line-height: 1.2;">{{ $rmseVal }}</div>
      <div style="font-size: 12px; color: #64748B;">Penalti galat besar</div>
    </div>

    <!-- Card 3: MAE -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 22px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
      <div style="font-size: 11px; font-weight: 700; color: #64748B; letter-spacing: 0.5px; text-transform: uppercase;">MAE</div>
      <div style="font-size: 26px; font-weight: 800; color: #0F172A; margin: 12px 0 4px 0; line-height: 1.2;">{{ $maeVal }}</div>
      <div style="font-size: 12px; color: #64748B;">Galat absolut rata-rata</div>
    </div>

    <!-- Card 4: MODEL TERPILIH (Orange Terracotta Card) -->
    <div style="background: linear-gradient(135deg, #EA580C 0%, #F97316 100%); border-radius: 14px; padding: 22px; color: #FFFFFF; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25);">
      <div style="font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.85); letter-spacing: 0.5px; text-transform: uppercase;">MODEL TERPILIH</div>
      <div style="font-size: 24px; font-weight: 800; color: #FFFFFF; margin: 12px 0 4px 0; line-height: 1.2;">{{ $metodeName }}</div>
      <div style="font-size: 12px; color: rgba(255,255,255,0.9);">Unggul RMSE vs Regresi Linear</div>
    </div>

  </div>

  <!-- Middle Card: Kurva Aktual vs Prediksi Chart -->
  <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; margin-bottom: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); position: relative;">
    
    <!-- Chart Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
      <div>
        <h2 style="font-size: 17px; font-weight: 700; color: #0F172A; margin: 0 0 4px 0;">Kurva Aktual vs Prediksi</h2>
        <p style="font-size: 13px; color: #64748B; margin: 0;">Visualisasi pergerakan omzet harian</p>
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
    <div style="width: 100%; height: 320px; position: relative;">
      <canvas id="arimaChart"></canvas>
    </div>

    <!-- Floating Action Button at Bottom Right of Chart Card -->
    <a href="{{ route('prediksi.index', ['cutoff' => $cutoffDate]) }}" title="Refresh Data"
       style="position: absolute; bottom: 20px; right: 20px; width: 42px; height: 42px; border-radius: 50%; background: #EA580C; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(234, 88, 12, 0.3); text-decoration: none;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="23 4 23 10 17 10"></polyline>
        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
      </svg>
    </a>
  </div>

  <!-- Bottom Grid: Omzet Aktual 7 Hari Terakhir & Prediksi 7 Hari Ke Depan -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
    
    <!-- Card 1: Omzet Aktual 7 Hari Terakhir (s.d. Cutoff) -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <div>
            <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin: 0 0 2px 0;">Omzet Aktual 7 Hari Terakhir</h3>
            <span style="font-size: 12px; color: #64748B;">Data historis s.d. Cutoff {{ \Carbon\Carbon::parse($cutoffDate)->format('d M Y') }}</span>
          </div>
          <div style="color: #2563EB; background: #EFF6FF; padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 700;">
            Aktual
          </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
          <thead>
            <tr style="border-bottom: 1px solid #E2E8F0; text-align: left;">
              <th style="padding: 8px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">TANGGAL</th>
              <th style="padding: 8px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: right;">OMZET (RP)</th>
              <th style="padding: 8px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: center;">vs BEP</th>
            </tr>
          </thead>
          <tbody>
            @forelse($omzet7HariTerakhir as $o)
              @php
                $tglO = $o->tanggal instanceof \Carbon\Carbon ? $o->tanggal->format('Y-m-d') : substr((string)$o->tanggal, 0, 10);
                $valO = (float) $o->jumlah_omzet;
              @endphp
              <tr style="border-bottom: 1px solid #F1F5F9;">
                <td style="padding: 9px 0; font-weight: 600; color: #334155;">
                  {{ \Carbon\Carbon::parse($tglO)->format('d M Y') }}
                </td>
                <td style="padding: 9px 0; text-align: right; font-weight: 700; color: #2563EB;">
                  {{ number_format($valO, 0, ',', '.') }}
                </td>
                <td style="padding: 9px 0; text-align: center;">
                  @if($valO >= $bep)
                    <span style="font-size: 11px; font-weight: 700; color: #16A34A; background: #DCFCE7; padding: 2px 8px; border-radius: 6px;">✔ Aman</span>
                  @else
                    <span style="font-size: 11px; font-weight: 700; color: #DC2626; background: #FEE2E2; padding: 2px 8px; border-radius: 6px;">✘ Bawah</span>
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

      @if($omzet7HariTerakhir->isNotEmpty())
        <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #E2E8F0; display: flex; justify-content: space-between; font-size: 12.5px; color: #64748B;">
          <span>Rata-Rata: <strong style="color: #0F172A;">Rp {{ number_format($omzet7HariTerakhir->avg('jumlah_omzet'), 0, ',', '.') }}</strong></span>
          <span>Total: <strong style="color: #0F172A;">Rp {{ number_format($omzet7HariTerakhir->sum('jumlah_omzet'), 0, ',', '.') }}</strong></span>
        </div>
      @endif
    </div>

    <!-- Card 2: Prediksi 7 Hari Ke Depan -->
    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <div>
            <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin: 0 0 2px 0;">Prediksi 7 Hari Ke Depan</h3>
            <span style="font-size: 12px; color: #64748B;">Hasil peramalan {{ $metodeName }}</span>
          </div>
          <div style="color: #EA580C; background: #FFF4ED; padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 700;">
            Prediksi
          </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
          <thead>
            <tr style="border-bottom: 1px solid #E2E8F0; text-align: left;">
              <th style="padding: 8px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">TANGGAL</th>
              <th style="padding: 8px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: right;">AKTUAL</th>
              <th style="padding: 8px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: right;">PREDIKSI</th>
            </tr>
          </thead>
          <tbody>
            @forelse($prediksiList->take(7) as $p)
              @php
                $tglStr = $p->tanggal_prediksi instanceof \Carbon\Carbon ? $p->tanggal_prediksi->format('Y-m-d') : substr((string)$p->tanggal_prediksi, 0, 10);
                $aktVal = $aktualSetelahCutoff->get($tglStr);
              @endphp
              <tr style="border-bottom: 1px solid #F1F5F9;">
                <td style="padding: 9px 0; font-weight: 600; color: #334155;">
                  {{ \Carbon\Carbon::parse($tglStr)->format('d M Y') }}
                </td>
                <td style="padding: 9px 0; text-align: right; color: #64748B; font-weight: 600;">
                  {{ $aktVal ? number_format($aktVal->jumlah_omzet, 0, ',', '.') : '-' }}
                </td>
                <td style="padding: 9px 0; text-align: right; font-weight: 700; color: #EA580C;">
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

      @if($prediksiList->isNotEmpty())
        <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #E2E8F0; display: flex; justify-content: space-between; font-size: 12.5px; color: #64748B;">
          <span>Rata-Rata Prediksi: <strong style="color: #EA580C;">Rp {{ number_format($prediksiList->take(7)->avg('nilai_prediksi'), 0, ',', '.') }}</strong></span>
          <span>Total: <strong style="color: #EA580C;">Rp {{ number_format($prediksiList->take(7)->sum('nilai_prediksi'), 0, ',', '.') }}</strong></span>
        </div>
      @endif
    </div>

  </div>

  <!-- Card 3: Perbandingan Model -->
  <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 24px; margin-bottom: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
      <h3 style="font-size: 16px; font-weight: 700; color: #0F172A; margin: 0;">Perbandingan Evaluasi Model</h3>
      <div style="color: #64748B;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line></svg>
      </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
      <thead>
        <tr style="border-bottom: 1px solid #E2E8F0; text-align: left;">
          <th style="padding: 10px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase;">MODEL</th>
          <th style="padding: 10px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: right;">MAPE</th>
          <th style="padding: 10px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: right;">RMSE</th>
          <th style="padding: 10px 0; font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: right;">MAE</th>
        </tr>
      </thead>
      <tbody>
        <tr style="border-bottom: 1px solid #F1F5F9;">
          <td style="padding: 14px 0; font-weight: 700; color: #0F172A; display: flex; align-items: center; gap: 8px;">
            <span style="color: #EA580C; font-size: 16px;">⦿</span>
            <span>ARIMA(2,1,1)</span>
          </td>
          <td style="padding: 14px 0; text-align: right; font-weight: 600; color: #334155;">{{ $mapeVal }}</td>
          <td style="padding: 14px 0; text-align: right; font-weight: 600; color: #334155;">{{ str_replace('Rp ', '', $rmseVal) }}</td>
          <td style="padding: 14px 0; text-align: right; font-weight: 600; color: #334155;">{{ str_replace('Rp ', '', $maeVal) }}</td>
        </tr>
        <tr>
          <td style="padding: 14px 0; font-weight: 500; color: #64748B; display: flex; align-items: center; gap: 8px;">
            <span style="color: #CBD5E1; font-size: 16px;">○</span>
            <span>Regresi Linear</span>
          </td>
          <td style="padding: 14px 0; text-align: right; font-weight: 500; color: #64748B;">
            {{ isset($linearMetrics['mape']) && $linearMetrics['mape'] !== null ? number_format($linearMetrics['mape'], 2, ',', '.') . '%' : '-' }}
          </td>
          <td style="padding: 14px 0; text-align: right; font-weight: 500; color: #64748B;">
            {{ isset($linearMetrics['rmse']) && $linearMetrics['rmse'] !== null ? number_format($linearMetrics['rmse'], 0, ',', '.') : '-' }}
          </td>
          <td style="padding: 14px 0; text-align: right; font-weight: 500; color: #64748B;">
            {{ isset($linearMetrics['mae']) && $linearMetrics['mae'] !== null ? number_format($linearMetrics['mae'], 0, ',', '.') : '-' }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById('arimaChart').getContext('2d');

  @php
    $timeline = [];

    if ($aktualHistorical->isNotEmpty()) {
        foreach ($aktualHistorical as $h) {
            $dateStr = $h->tanggal instanceof \Carbon\Carbon ? $h->tanggal->format('Y-m-d') : substr((string)$h->tanggal, 0, 10);
            $label   = \Carbon\Carbon::parse($dateStr)->format('d/m');
            $timeline[$dateStr] = [
                'label'    => $label,
                'aktual'   => (float) $h->jumlah_omzet,
                'prediksi' => null,
            ];
        }
    }

    if ($prediksiList->isNotEmpty()) {
        // Hubungkan titik awal kurva prediksi ke titik cutoff (jika ada data aktualnya)
        $cutoffStr = $cutoffDate instanceof \Carbon\Carbon ? $cutoffDate->format('Y-m-d') : substr((string)$cutoffDate, 0, 10);
        if (isset($timeline[$cutoffStr]) && $timeline[$cutoffStr]['aktual'] !== null) {
            $timeline[$cutoffStr]['prediksi'] = $timeline[$cutoffStr]['aktual'];
        }

        foreach ($prediksiList as $p) {
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
