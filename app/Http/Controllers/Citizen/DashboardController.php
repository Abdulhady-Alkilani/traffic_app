<?php

declare(strict_types=1);

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\TrafficViolation;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $citizenData = Auth::user()->citizenData;

        if (!$citizenData) {
            abort(redirect('/'));
        }

        $vehiclesCount = Vehicle::where('citizen_id', $citizenData->id)->count();
        $reportsCount = Report::where('citizen_id', $citizenData->id)->count();
        $violationsCount = TrafficViolation::where('citizen_id', $citizenData->id)->count();

        // Violations by status (for doughnut)
        $violationsByStatus = TrafficViolation::where('citizen_id', $citizenData->id)
            ->toBase()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();

        // Reports by status (for bar chart)
        $reportsByStatus = Report::where('citizen_id', $citizenData->id)
            ->toBase()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();

        // Violations by type (for polar area)
        $violationsByType = TrafficViolation::where('citizen_id', $citizenData->id)
            ->toBase()
            ->selectRaw('violation_type, count(*) as count')
            ->groupBy('violation_type')
            ->pluck('count', 'violation_type')->toArray();

        // Total fines
        $totalFines = TrafficViolation::where('citizen_id', $citizenData->id)->sum('fine_amount');
        $unpaidFines = TrafficViolation::where('citizen_id', $citizenData->id)
            ->where('status', 'unpaid')->sum('fine_amount');

        // Monthly violations (last 6 months for line chart)
        $monthlyViolations = TrafficViolation::where('citizen_id', $citizenData->id)
            ->where('issued_at', '>=', now()->subMonths(5)->startOfMonth())
            ->toBase()
            ->selectRaw("DATE_FORMAT(issued_at, '%Y-%m') as month, count(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')->toArray();

        // Fill empty months
        $monthLabels = [];
        $monthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthKey = now()->subMonths($i)->format('Y-m');
            $monthLabels[] = now()->subMonths($i)->translatedFormat('M Y');
            $monthData[] = $monthlyViolations[$monthKey] ?? 0;
        }

        // Vehicles by type (for horizontal bar)
        $vehiclesByType = Vehicle::where('citizen_id', $citizenData->id)
            ->toBase()
            ->selectRaw('vehicle_type, count(*) as count')
            ->groupBy('vehicle_type')
            ->pluck('count', 'vehicle_type')->toArray();

        return view('citizen.dashboard', compact(
            'citizenData',
            'vehiclesCount',
            'reportsCount',
            'violationsCount',
            'violationsByStatus',
            'reportsByStatus',
            'violationsByType',
            'totalFines',
            'unpaidFines',
            'monthLabels',
            'monthData',
            'vehiclesByType'
        ));
    }
}

