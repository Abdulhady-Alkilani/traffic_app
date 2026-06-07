<?php

declare(strict_types=1);

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class VehicleController extends Controller
{
    public function __construct(
        private readonly VehicleService $vehicleService,
    ) {}

    public function index(\Illuminate\Http\Request $request): \Illuminate\View\View
    {
        $citizenData = Auth::user()->citizenData;

        if (!$citizenData) {
            abort(redirect('/'));
        }

        $query = Vehicle::where('citizen_id', $citizenData->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('make', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('vehicle_type', $request->type);
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['created_at', 'model_year', 'make'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        $vehicles = $query->paginate(10)->withQueryString();

        return view('citizen.vehicles.index', compact('vehicles'));
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $this->vehicleService->create(
            Auth::user()->citizenData,
            $request->validated()
        );

        return back()->with('success', __('messages.vehicle_added'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        Gate::authorize('update', $vehicle);

        $this->vehicleService->update($vehicle, $request->validated());

        return back()->with('success', __('messages.vehicle_updated'));
    }

    public function show(Vehicle $vehicle)
    {
        Gate::authorize('view', $vehicle);
        
        $vehicle->load(['violations' => function ($query) {
            $query->latest()->take(10);
        }, 'reports' => function ($query) {
            $query->latest()->take(5);
        }]);
        
        return view('citizen.vehicles.show', compact('vehicle'));
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        Gate::authorize('delete', $vehicle);

        $this->vehicleService->delete($vehicle);

        return back()->with('success', __('messages.vehicle_deleted'));
    }
}
