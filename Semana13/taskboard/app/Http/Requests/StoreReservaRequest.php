<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'telescopio_id' => ['required', 'integer', 'exists:telescopios,id'],
            'fecha' => ['required', 'date', 'after_or_equal:2026-11-01'],
            'horas' => ['required', 'integer', 'between:1,4'],
            'objetivo' => ['required', 'string', 'min:3', 'max:60'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'objetivo.required' => 'Debes indicar el objetivo de la observación.',
            'horas.between' => 'Las horas deben estar entre :min y :max.',
            'fecha.after_or_equal' => 'La fecha debe ser el 1 de noviembre de 2026 o posterior.',
            'telescopio_id.exists' => 'El telescopio elegido no existe.',
        ];
    }
}
