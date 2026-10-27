<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservaResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'telescopio_id' => $this->telescopio_id,
            'fecha' => $this->fecha?->toDateString(),
            'horas' => $this->horas,
            'objetivo' => $this->objetivo,
            'estado' => $this->estado,
            'telescopio' => $this->whenLoaded('telescopio', fn () => new TelescopioResource($this->telescopio)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
