<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nip',
        'jabatan',
        'instansi',
        'signature_image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the currently active certificate signer setting.
     */
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->latest()->first() ?? static::latest()->first();
    }
}
