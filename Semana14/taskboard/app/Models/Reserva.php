<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    protected $table = 'reservas';

    // TODO (Tarea 1): declara aquí $fillable con los campos que se pueden asignar en masa:
    //   user_id, telescopio_id, fecha, horas, objetivo y estado.
    protected $fillable = ['user_id', 'telescopio_id', 'fecha', 'horas', 'objetivo', 'estado'];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    // TODO (Tarea 1): define aquí las relaciones telescopio() y user().
    //   Una reserva pertenece a un telescopio y pertenece a un usuario (belongsTo).
    public function telescopio(): BelongsTo
    {
        return $this->belongsTo(Telescopio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
