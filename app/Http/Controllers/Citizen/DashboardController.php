<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\TrafficViolation;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $citizenData = $user->citizenData;

        if (!$citizenData) {
            return redirect('/');
        }

        $vehicles = Vehicle::where('citizen_id', $citizenData->id)
            ->latest()
            ->paginate(10, ['*'], 'vehicles_page');

        $reports = Report::where('citizen_id', $citizenData->id)
            ->with('vehicle')
            ->latest()
            ->paginate(10, ['*'], 'reports_page');

        $violations = TrafficViolation::where('citizen_id', $citizenData->id)
            ->with('vehicle')
            ->latest()
            ->paginate(10, ['*'], 'violations_page');

        return view('citizen.dashboard', compact('citizenData', 'vehicles', 'reports', 'violations'));
    }
}
