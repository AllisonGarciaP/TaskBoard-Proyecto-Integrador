<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Telescopio extends Model
{
    protected $table = 'telescopios';

    protected $fillable = ['nombre', 'tipo', 'apertura_mm', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    // TODO (Tarea 1): define aquí la relación reservas().
    //   Un telescopio tiene muchas reservas (relación uno a muchos).
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }
}
