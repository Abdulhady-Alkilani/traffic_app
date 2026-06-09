<?php

declare(strict_types=1);

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Models\Vehicle;
use App\Services\ReportRoutingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportRoutingService $routingService,
    ) {}

    public function index(\Illuminate\Http\Request $request): \Illuminate\View\View
    {
        $citizenData = Auth::user()->citizenData;

        if (!$citizenData) {
            abort(redirect('/'));
        }

        $query = \App\Models\Report::with('vehicle')->where('citizen_id', $citizenData->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('location_text', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('report_type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['created_at', 'report_type', 'status'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('citizen.reports.index', compact('reports'));
    }

    public function create(): View
    {
        $citizenData = Auth::user()->citizenData;
        $vehicles = Vehicle::where('citizen_id', $citizenData->id)->get();

        return view('citizen.report-wizard', compact('citizenData', 'vehicles'));
    }

    public function store(StoreReportRequest $request): RedirectResponse
    {
        $citizenData = Auth::user()->citizenData;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reports', 'public');
        }

        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('reports/videos', 'public');
        }

        $reportedPlate = $request->validated('unknown_plate') ? 'بدون لوحة' : $request->validated('reported_vehicle_plate');

        $report = $this->routingService->createReport([
            'citizen_id' => $citizenData->id,
            'vehicle_id' => $request->validated('vehicle_id'),
            'reported_vehicle_plate' => $reportedPlate,
            'report_type' => $request->validated('report_type'),
            'description' => $request->validated('description'),
            'location_type' => $request->validated('location_type'),
            'latitude' => $request->validated('latitude'),
            'longitude' => $request->validated('longitude'),
            'location_text' => $request->validated('location_text'),
            'image_url' => $imagePath,
            'video_url' => $videoPath,
        ]);

        return redirect()
            ->route('citizen.dashboard')
            ->with('success', __('messages.report_created'))
            ->with('tracking_number', 'RPT-' . str_pad((string) $report->id, 6, '0', STR_PAD_LEFT));
    }

    public function show(\App\Models\Report $report)
    {
        $citizenId = Auth::user()->citizenData->id;

        // Ensure the citizen owns the report or is the subject of a violation from this report
        $isReporter = $report->citizen_id === $citizenId;
        $isViolator = \App\Models\TrafficViolation::where('report_id', $report->id)
            ->where('citizen_id', $citizenId)
            ->exists();

        if (!$isReporter && !$isViolator) {
            abort(403);
        }

        $report->load('vehicle');

        return view('citizen.reports.show', compact('report'));
    }

    public function searchVehicles(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $citizenData = Auth::user()->citizenData;
        $search = $request->get('q', '');

        $vehicles = Vehicle::where('citizen_id', '!=', $citizenData->id)
            ->where(function ($query) use ($search) {
                $query->where('plate_number', 'like', "%{$search}%")
                      ->orWhere('make', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'plate_number', 'make', 'model_year', 'vehicle_type', 'color']);

        return response()->json($vehicles);
    }
}
