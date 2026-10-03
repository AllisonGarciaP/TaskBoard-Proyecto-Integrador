<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comercio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_comercio',
        'rubro',
        'fecha_afiliacion',
        'telefono',
        'correo_contacto',
    ];

    protected function casts(): array
    {
        return [
            'fecha_afiliacion' => 'date',
        ];
    }

    public function transacciones(): HasMany
    {
        return $this->hasMany(Transaccion::class);
    }
}