<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'kode',
        'nama',
        'email',
        'password',
        'peran',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isPemilik(): bool
    {
        return in_array(strtolower((string) $this->peran), ['pemilik', 'pemilik usaha']);
    }

    public function isKeuangan(): bool
    {
        return in_array(strtolower((string) $this->peran), ['keuangan', 'bagian keuangan']);
    }

    // Accessor / Mutator agar $user->id tetap mengembalikan id_user
    public function getIdAttribute()
    {
        return $this->attributes['id_user'] ?? null;
    }
}
