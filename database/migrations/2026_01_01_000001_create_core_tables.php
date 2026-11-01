<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── 2. Struktur Tabel omzet ──────────────────────────────────────────
        Schema::create('omzet', function (Blueprint $table) {
            $table->id('id_omzet');                                            // Primary Key (id_omzet)
            $table->date('tanggal')->unique();                                 // Tanggal pencatatan omzet
            $table->decimal('jumlah_omzet', 15, 2);                            // Nilai omzet harian
            $table->text('keterangan')->nullable();                           // Keterangan opsional
            $table->unsignedBigInteger('id_user');                             // Foreign Key ke users(id_user)
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        // ─── 3. Struktur Tabel prediksi ───────────────────────────────────────
        Schema::create('prediksi', function (Blueprint $table) {
            $table->id('id_prediksi');                                         // Primary Key (id_prediksi)
            $table->unsignedBigInteger('id_omzet')->nullable();                // Foreign Key ke omzet(id_omzet)
            $table->foreign('id_omzet')->references('id_omzet')->on('omzet')->onDelete('set null');
            $table->date('tanggal_prediksi');                                  // Tanggal nilai yang diprediksi
            $table->decimal('nilai_prediksi', 15, 2);                          // Hasil peramalan omzet
            $table->string('model', 50)->default('ARIMA(2,1,1)');             // Nama model peramalan
            $table->decimal('mape', 5, 2)->nullable();                         // Akurasi MAPE (%)
            $table->decimal('rmse', 15, 2)->nullable();                        // Galat RMSE (Rp)
            $table->decimal('mae', 15, 2)->nullable();                         // Galat MAE (Rp)
            $table->date('cutoff_tanggal')->nullable()->index();               // Parameter cutoff peramalan
            $table->timestamps();
        });

        // ─── 4. Struktur Tabel status_ews ─────────────────────────────────────
        Schema::create('status_ews', function (Blueprint $table) {
            $table->id('id_status');                                           // Primary Key (id_status)
            $table->unsignedBigInteger('id_prediksi')->nullable();             // Foreign Key ke prediksi(id_prediksi)
            $table->foreign('id_prediksi')->references('id_prediksi')->on('prediksi')->onDelete('set null');
            $table->decimal('rasio', 5, 2);                                    // Rasio pencapaian (%)
            $table->enum('kategori', ['Aman', 'Waspada', 'Bahaya', 'Tidak terdefinisi']); // Kategori status
            $table->date('tanggal');                                           // Tanggal penilaian status
            $table->decimal('rata_aktual', 15, 2)->nullable();                 // Rata-rata omzet aktual
            $table->decimal('rata_prediksi', 15, 2)->nullable();               // Rata-rata peramalan
            $table->date('cutoff_tanggal')->nullable()->index();               // Parameter cutoff
            $table->timestamps();
        });

        // ─── 5. Struktur Tabel notifikasi ─────────────────────────────────────
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');                                       // Primary Key (id_notifikasi)
            $table->unsignedBigInteger('id_status');                           // Foreign Key ke status_ews(id_status)
            $table->foreign('id_status')->references('id_status')->on('status_ews')->onDelete('cascade');
            $table->text('pesan');                                             // Isi pesan peringatan dini
            $table->string('nomor_tujuan', 20)->nullable();                    // Nomor WhatsApp tujuan
            $table->enum('status_kirim', ['Terkirim', 'Gagal'])->default('Terkirim'); // Status pengiriman
            $table->dateTime('waktu_kirim')->nullable();                       // Waktu pengiriman
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('status_ews');
        Schema::dropIfExists('prediksi');
        Schema::dropIfExists('omzet');
    }
};
