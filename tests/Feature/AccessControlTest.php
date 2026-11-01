<?php

namespace Tests\Feature;

use App\Models\Omzet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Skenario 1: User dengan peran 'keuangan' DITOLAK (HTTP 403) saat mengakses /notifikasi (khusus pemilik).
     */
    public function test_keuangan_user_cannot_access_notifikasi(): void
    {
        $keuangan = User::create([
            'nama'     => 'Staf Keuangan Test 3',
            'email'    => 'keuangan_test_3@syafana.test',
            'password' => bcrypt('password'),
            'peran'    => 'keuangan',
        ]);

        $response = $this->actingAs($keuangan)->get('/notifikasi');
        $response->assertStatus(403);
    }

    /**
     * Skenario 2: User dengan peran 'pemilik' BERHASIL (HTTP 200) mengakses /notifikasi.
     */
    public function test_pemilik_user_can_access_pemilik_routes(): void
    {
        $pemilik = User::create([
            'nama'     => 'Pemilik Syafana Test',
            'email'    => 'pemilik_test_1@syafana.test',
            'password' => bcrypt('password'),
            'peran'    => 'pemilik',
        ]);

        $this->actingAs($pemilik);
        $this->get('/notifikasi')->assertStatus(200);
        $this->get('/notifikasi/unduh')->assertStatus(200);
    }

    /**
     * Skenario 3: User dengan peran 'pemilik' DITOLAK (HTTP 403) saat mengakses route manajemen omzet (/omzet/input).
     */
    public function test_pemilik_user_cannot_access_omzet_routes(): void
    {
        $pemilik = User::create([
            'nama'     => 'Pemilik Syafana Test 2',
            'email'    => 'pemilik_test_2@syafana.test',
            'password' => bcrypt('password'),
            'peran'    => 'pemilik',
        ]);

        $this->actingAs($pemilik);
        $this->get('/omzet')->assertStatus(200);
        $this->get('/omzet/input')->assertStatus(403);
    }

    /**
     * Skenario 4: User Bagian Keuangan BERHASIL (HTTP 200) mengakses menu keuangannya (dashboard, input, riwayat, prediksi).
     */
    public function test_keuangan_user_can_access_keuangan_routes(): void
    {
        $keuangan = User::create([
            'nama'     => 'Staf Keuangan Test 4',
            'email'    => 'keuangan_test_4@syafana.test',
            'password' => bcrypt('password'),
            'peran'    => 'keuangan',
        ]);

        $this->actingAs($keuangan);

        $this->get('/dashboard')->assertStatus(200);
        $this->get('/omzet')->assertStatus(200);
        $this->get('/omzet/input')->assertStatus(200);
        $this->get('/omzet/riwayat')->assertStatus(200);
        $this->get('/prediksi')->assertStatus(200);
    }

    /**
     * Skenario 5: Testing CRUD Omzet Bagian Keuangan.
     */
    public function test_omzet_crud_operations(): void
    {
        $user = User::create([
            'nama'     => 'Tester Omzet',
            'email'    => 'test_omzet@syafana.test',
            'password' => bcrypt('password'),
            'peran'    => 'keuangan',
        ]);

        $this->actingAs($user);

        // Store
        $postResp = $this->post('/omzet', [
            'tanggal'    => '2026-12-31',
            'jumlah'     => 950000,
            'keterangan' => 'Test input omzet',
        ]);
        $postResp->assertRedirect(route('omzet.index'));

        $omzet = Omzet::where('tanggal', '2026-12-31')->first();
        $this->assertNotNull($omzet);
        $this->assertEquals(950000, $omzet->jumlah_omzet);

        // Edit View
        $editResp = $this->get("/omzet/{$omzet->id_omzet}/edit");
        $editResp->assertStatus(200);

        // Update
        $putResp = $this->put("/omzet/{$omzet->id_omzet}", [
            'tanggal'    => '2026-12-31',
            'jumlah'     => 990000,
            'keterangan' => 'Updated omzet',
        ]);
        $putResp->assertRedirect(route('omzet.index'));
        $this->assertEquals(990000, $omzet->fresh()->jumlah_omzet);

        // Destroy
        $delResp = $this->delete("/omzet/{$omzet->id_omzet}");
        $delResp->assertRedirect(route('omzet.index'));
        $this->assertNull(Omzet::where('tanggal', '2026-12-31')->first());
    }

    /**
     * Skenario 6: Guest (belum login) dialihkan (HTTP 302) ke halaman login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
