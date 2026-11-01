<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediksi extends Model
{
    protected $table = 'prediksi';
    protected $primaryKey = 'id_prediksi';

    protected $fillable = [
        'id_omzet',
        'tanggal_prediksi',
        'nilai_prediksi',
        'model',
        'mape',
        'rmse',
        'mae',
        'cutoff_tanggal',
    ];

    protected $casts = [
        'tanggal_prediksi' => 'date',
        'cutoff_tanggal'   => 'date',
        'nilai_prediksi'   => 'decimal:2',
        'mape'             => 'decimal:2',
        'rmse'             => 'decimal:2',
        'mae'              => 'decimal:2',
    ];

    public function omzet()
    {
        return $this->belongsTo(Omzet::class, 'id_omzet', 'id_omzet');
    }

    // Accessors & Mutators untuk kompatibilitas
    public function getTanggalAttribute()
    {
        return $this->attributes['tanggal_prediksi'] ?? null;
    }

    public function setTanggalAttribute($value)
    {
        $this->attributes['tanggal_prediksi'] = $value;
    }

    public function getMetodeAttribute()
    {
        return $this->attributes['model'] ?? 'ARIMA';
    }

    public function setMetodeAttribute($value)
    {
        $this->attributes['model'] = $value;
    }
}
