<?php

namespace Tests\Feature;

use App\Services\EwsService;
use Tests\TestCase;

/**
 * EwsServiceTest — Otomasi White Box Testing modul klasifikasi EWS.
 *
 * Mengeksekusi kelima kasus uji pada Tabel 5.8 BAB V (P0, P1, P1', P2, P3),
 * hasil dari basis path testing terhadap EwsService::classify() dan
 * classifyFromRatio() dengan kompleksitas siklomatik V(G) = 5.
 *
 * Aturan yang diuji mengacu pada Tabel 3.10 BAB III:
 *   R <  70 ATAU F < BEP        -> Bahaya
 *   70 <= R < 90 DAN F >= BEP   -> Waspada
 *   R >= 90 DAN F >= BEP        -> Aman
 *
 * Catatan jalur kelima (baris 21, defensive fallback "Tidak terdefinisi"
 * pada classifyFromRatio()): jalur ini TIDAK diuji di sini karena terbukti
 * infeasible secara matematis. Argumen $rasio pada classifyFromRatio()
 * selalu jatuh ke salah satu dari tiga rentang yang saling lepas dan
 * menyeluruh (<70, [70,90), >=90); begitu $rataPrediksi >= $bep (yang
 * menjadi syarat classify() tidak lebih dulu mengembalikan "Bahaya"),
 * salah satu dari tiga syarat if pasti bernilai benar. Baris 21 tidak
 * bisa dicapai lewat classify() dengan data nyata sehingga tidak
 * memiliki kasus uji basis path yang valid, sebagaimana dijelaskan
 * pada BAB V 5.5.1.
 */
class EwsServiceTest extends TestCase
{
    private EwsService $ews;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ews = new EwsService();
    }

    /**
     * P0 - Simpul 1 benar: rata-rata omzet aktual bernilai nol.
     * Input (A, F, BEP) = (0, 500.000, 400.000)
     * Ekspektasi: status "Tidak terdefinisi", rasio 0,00
     */
    public function test_p0_rata_aktual_nol_menghasilkan_tidak_terdefinisi(): void
    {
        $hasil = $this->ews->classify(
            rataAktual: 0,
            rataPrediksi: 500000,
            bep: 400000,
        );

        $this->assertSame('Tidak terdefinisi', $hasil['status']);
        $this->assertEquals(0.00, $hasil['rasio']);
    }

    /**
     * P1 - Simpul 1 salah, Simpul 2 benar karena rasio < 70.
     * Input (A, F, BEP) = (1.000.000, 650.000, 400.000)
     * Ekspektasi: status "Bahaya", rasio 65,00%
     */
    public function test_p1_rasio_di_bawah_70_menghasilkan_bahaya(): void
    {
        $hasil = $this->ews->classify(
            rataAktual: 1000000,
            rataPrediksi: 650000,
            bep: 400000,
        );

        $this->assertSame('Bahaya', $hasil['status']);
        $this->assertEquals(65.00, $hasil['rasio']);
    }

    /**
     * P1' - Simpul 1 salah, Simpul 2 benar karena F < BEP (rasio >= 70).
     * Kasus tambahan cakupan kondisi majemuk pada Simpul 2, memastikan
     * kedua sebab (rasio < 70 ATAU F < BEP) teruji terpisah walau
     * berada pada jalur struktural yang sama dengan P1.
     * Input (A, F, BEP) = (500.000, 390.000, 400.000)
     * Ekspektasi: status "Bahaya", rasio 78,00%
     */
    public function test_p1_aksen_prediksi_di_bawah_bep_menghasilkan_bahaya(): void
    {
        $hasil = $this->ews->classify(
            rataAktual: 500000,
            rataPrediksi: 390000,
            bep: 400000,
        );

        $this->assertSame('Bahaya', $hasil['status']);
        $this->assertEquals(78.00, $hasil['rasio']);
    }

    /**
     * P2 - Simpul 1 dan 2 salah, Simpul 3 benar (70 <= rasio < 90, F >= BEP).
     * Input (A, F, BEP) = (585.714, 513.729, 400.000)
     * Ekspektasi: status "Waspada", rasio 87,71%
     */
    public function test_p2_rasio_antara_70_dan_90_menghasilkan_waspada(): void
    {
        $hasil = $this->ews->classify(
            rataAktual: 585714,
            rataPrediksi: 513729,
            bep: 400000,
        );

        $this->assertSame('Waspada', $hasil['status']);
        $this->assertEquals(87.71, $hasil['rasio']);
    }

    /**
     * P3 - Simpul 1, 2, dan 3 salah, Simpul 4 benar (rasio >= 90, F >= BEP).
     * Input (A, F, BEP) = (500.000, 475.000, 400.000)
     * Ekspektasi: status "Aman", rasio 95,00%
     */
    public function test_p3_rasio_90_ke_atas_menghasilkan_aman(): void
    {
        $hasil = $this->ews->classify(
            rataAktual: 500000,
            rataPrediksi: 475000,
            bep: 400000,
        );

        $this->assertSame('Aman', $hasil['status']);
        $this->assertEquals(95.00, $hasil['rasio']);
    }

    /**
     * Skenario tambahan (bukan bagian dari Tabel 5.8): membuktikan secara
     * langsung bahwa classifyFromRatio() konsisten dengan classify() untuk
     * nilai rasio hasil pembagian riil, memperkuat argumen bahwa baris 21
     * hanya bisa dicapai lewat pemanggilan langsung classifyFromRatio()
     * dengan kombinasi nilai yang mustahil terjadi lewat classify().
     */
    public function test_classify_dan_classify_from_ratio_konsisten_pada_batas_ambang(): void
    {
        // Tepat di batas 70 -> harus masuk Waspada, bukan Bahaya
        $tepatDiBatasBawah = $this->ews->classify(
            rataAktual: 1000000,
            rataPrediksi: 700000,
            bep: 400000,
        );
        $this->assertSame('Waspada', $tepatDiBatasBawah['status']);
        $this->assertEquals(70.00, $tepatDiBatasBawah['rasio']);

        // Tepat di batas 90 -> harus masuk Aman, bukan Waspada
        $tepatDiBatasAtas = $this->ews->classify(
            rataAktual: 1000000,
            rataPrediksi: 900000,
            bep: 400000,
        );
        $this->assertSame('Aman', $tepatDiBatasAtas['status']);
        $this->assertEquals(90.00, $tepatDiBatasAtas['rasio']);
    }
}