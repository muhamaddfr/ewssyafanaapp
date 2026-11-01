<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Omzet extends Model
{
    protected $table = 'omzet';
    protected $primaryKey = 'id_omzet';

    protected $fillable = [
        'tanggal',
        'jumlah_omzet',
        'keterangan',
        'id_user',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'jumlah_omzet' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Accessors & Mutators agar $omzet->jumlah dan $omzet->user_id tetap bisa diakses secara transparan
    public function getJumlahAttribute()
    {
        return $this->attributes['jumlah_omzet'] ?? null;
    }

    public function setJumlahAttribute($value)
    {
        $this->attributes['jumlah_omzet'] = $value;
    }

    public function getUserIdAttribute()
    {
        return $this->attributes['id_user'] ?? null;
    }

    public function setUserIdAttribute($value)
    {
        $this->attributes['id_user'] = $value;
    }
}
