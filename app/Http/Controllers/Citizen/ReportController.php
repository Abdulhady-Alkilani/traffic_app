<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Models\CitizenData;
use App\Models\Vehicle;
use App\Services\ReportCreationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function create()
    {
        $citizenData = Auth::user()->citizenData;
        $vehicles = Vehicle::where('citizen_id', $citizenData->id)->get();

        return view('citizen.report-wizard', compact('citizenData', 'vehicles'));
    }

    public function store(StoreReportRequest $request)
    {
        $citizenData = Auth::user()->citizenData;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reports', 'public');
        }

        $service = new ReportCreationService();
        $report = $service->createReport([
            'citizen_id' => $citizenData->id,
            'vehicle_id' => $request->vehicle_id,
            'report_type' => $request->report_type,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_text' => $request->location_text,
            'image_url' => $imagePath,
        ]);

        return redirect()->route('citizen.dashboard')
            ->with('success', __('messages.report_created'));
    }
}
