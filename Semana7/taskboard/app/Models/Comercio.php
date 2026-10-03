<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comercio extends Model
{
    use HasFactory;

    /**
     * Semana 6 · Eloquent ORM
     * Campos que se pueden asignar de forma masiva (create()/update()).
     */
    protected $fillable = [
        'nombre_comercio',
        'rubro',
        'telefono',
    ];

    /**
     * Un Comercio tiene muchas Transacciones.
     * Analogía: la cocina (Eloquent) sabe que "un comercio pidió muchos platillos".
     */
    public function transacciones(): HasMany
    {
        return $this->hasMany(Transaccion::class);
    }
}
