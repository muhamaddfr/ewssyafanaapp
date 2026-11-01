<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';

    protected $fillable = [
        'id_status',
        'pesan',
        'nomor_tujuan',
        'status_kirim',
        'waktu_kirim',
    ];

    protected $casts = [
        'waktu_kirim' => 'datetime',
    ];

    public function statusEws()
    {
        return $this->belongsTo(StatusEws::class, 'id_status', 'id_status');
    }

    // Accessors & Mutators untuk kompatibilitas
    public function getIsiAttribute()
    {
        return $this->attributes['pesan'] ?? null;
    }

    public function setIsiAttribute($value)
    {
        $this->attributes['pesan'] = $value;
    }

    public function getTerkirimAttribute(): bool
    {
        return ($this->attributes['status_kirim'] ?? '') === 'Terkirim';
    }

    public function setTerkirimAttribute($value)
    {
        $this->attributes['status_kirim'] = $value ? 'Terkirim' : 'Gagal';
    }

    public function getStatusEwsIdAttribute()
    {
        return $this->attributes['id_status'] ?? null;
    }

    public function setStatusEwsIdAttribute($value)
    {
        $this->attributes['id_status'] = $value;
    }
}
