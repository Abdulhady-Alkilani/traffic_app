<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plate_number' => ['required', 'string', 'unique:vehicles,plate_number'],
            'vehicle_type' => ['required', 'string'],
            'make' => ['required', 'string'],
            'model_year' => ['required', 'string'],
            'color' => ['required', 'string'],
        ];
    }
}
