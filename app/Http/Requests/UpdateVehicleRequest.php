<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')?->id;

        return [
            'plate_number' => ['required', 'string', 'unique:vehicles,plate_number,' . $vehicleId],
            'vehicle_type' => ['required', 'string'],
            'make' => ['required', 'string'],
            'model_year' => ['required', 'string'],
            'color' => ['required', 'string'],
        ];
    }
}
