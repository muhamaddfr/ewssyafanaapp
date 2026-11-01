<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';
    protected $fillable = ['kunci', 'nilai', 'keterangan'];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('kunci', $key)->first();
        return $setting ? $setting->nilai : $default;
    }

    public static function set(string $key, $value, ?string $keterangan = null)
    {
        return static::updateOrCreate(
            ['kunci' => $key],
            ['nilai' => (string) $value, 'keterangan' => $keterangan]
        );
    }
}
