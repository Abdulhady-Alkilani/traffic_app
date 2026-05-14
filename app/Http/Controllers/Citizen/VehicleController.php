<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\CitizenData;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    public function store(Request $request)
    {
        $citizenData = Auth::user()->citizenData;

        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number',
            'vehicle_type' => 'required|string',
            'make' => 'required|string',
            'model_year' => 'required|string',
            'color' => 'required|string',
        ]);

        Vehicle::create([
            'citizen_id' => $citizenData->id,
            ...$validated,
        ]);

        return back()->with('success', __('messages.vehicle_added'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $citizenData = Auth::user()->citizenData;

        if ($vehicle->citizen_id !== $citizenData->id) {
            abort(403);
        }

        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'vehicle_type' => 'required|string',
            'make' => 'required|string',
            'model_year' => 'required|string',
            'color' => 'required|string',
        ]);

        $vehicle->update($validated);

        return back()->with('success', __('messages.vehicle_updated'));
    }

    public function destroy(Vehicle $vehicle)
    {
        $citizenData = Auth::user()->citizenData;

        if ($vehicle->citizen_id !== $citizenData->id) {
            abort(403);
        }

        $vehicle->delete();

        return back()->with('success', __('messages.vehicle_deleted'));
    }
}
