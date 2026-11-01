<?php

namespace App\Services;

/**
 * EwsService — Logika klasifikasi status EWS omzet harian.
 *
 * Aturan (Tabel 3.10 skripsi):
 *   R <  70 ATAU F < BEP        → Bahaya
 *   70 <= R < 90 DAN F >= BEP   → Waspada
 *   R >= 90 DAN F >= BEP        → Aman
 */
class EwsService
{
    /**
     * @param float $rataAktual   Rata-rata omzet aktual 7 hari aktif terakhir (A)
     * @param float $rataPrediksi Rata-rata omzet prediksi 7 hari aktif ke depan (F)
     * @param float $bep          Titik Break Even Point harian
     * @return array{rasio: float, status: string}
     */
    public function classify(float $rataAktual, float $rataPrediksi, float $bep): array
    {
        if ($rataAktual <= 0) {
            return ['rasio' => 0.0, 'status' => 'Tidak terdefinisi'];
        }

        $rasio  = ($rataPrediksi / $rataAktual) * 100;
        $status = $this->classifyFromRatio($rasio, $rataPrediksi, $bep);

        return ['rasio' => round($rasio, 2), 'status' => $status];
    }

    public function classifyFromRatio(float $rasio, float $rataPrediksi, float $bep): string
    {
        if ($rasio < 70 || $rataPrediksi < $bep) {
            return 'Bahaya';
        }

        if ($rasio >= 70 && $rasio < 90 && $rataPrediksi >= $bep) {
            return 'Waspada';
        }

        if ($rasio >= 90 && $rataPrediksi >= $bep) {
            return 'Aman';
        }

        return 'Tidak terdefinisi';
    }
}
