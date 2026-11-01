<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusEws extends Model
{
    protected $table = 'status_ews';
    protected $primaryKey = 'id_status';

    protected $fillable = [
        'id_prediksi',
        'rasio',
        'kategori',
        'tanggal',
        'rata_aktual',
        'rata_prediksi',
        'cutoff_tanggal',
    ];

    protected $casts = [
        'tanggal'        => 'date',
        'cutoff_tanggal' => 'date',
        'rasio'          => 'decimal:2',
    ];

    public function prediksi()
    {
        return $this->belongsTo(Prediksi::class, 'id_prediksi', 'id_prediksi');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_status', 'id_status');
    }

    // Accessor & Mutator untuk status (alias kategori)
    public function getStatusAttribute()
    {
        return $this->attributes['kategori'] ?? null;
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['kategori'] = $value;
    }
}
