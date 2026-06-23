<?php

declare(strict_types=1);

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\TrafficViolation;
use App\Services\ViolationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ViolationController extends Controller
{
    public function __construct(
        private readonly ViolationService $violationService,
        private readonly \App\Services\ActivityLogger $logger,
    ) {}

    public function index(\Illuminate\Http\Request $request): \Illuminate\View\View
    {
        $citizenData = Auth::user()->citizenData;

        if (!$citizenData) {
            abort(redirect('/'));
        }

        $query = TrafficViolation::with('vehicle')->where('citizen_id', $citizenData->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('vehicle', function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('violation_type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortField = $request->get('sort', 'issued_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['issued_at', 'fine_amount', 'status'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        $violations = $query->paginate(10)->withQueryString();

        return view('citizen.violations.index', compact('violations'));
    }
    public function show(TrafficViolation $violation)
    {
        $citizenData = Auth::user()->citizenData;

        if (!$citizenData || $violation->citizen_id !== $citizenData->id) {
            abort(403);
        }

        $violation->load(['vehicle', 'report']);

        return view('citizen.violations.show', compact('violation'));
    }

    public function mockPay(\Illuminate\Http\Request $request, TrafficViolation $violation): JsonResponse
    {
        $citizenData = Auth::user()->citizenData;

        if (!$citizenData || $violation->citizen_id !== $citizenData->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$violation->status->isUnpaid()) {
            return response()->json(['success' => false, 'message' => 'Violation is not unpaid'], 400);
        }

        $request->validate([
            'receipt' => 'required|image|max:5120', // Max 5MB
        ]);

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            $violation->payment_receipt_path = $path;
            $violation->status = \App\Enums\ViolationStatus::PendingVerification;
            $violation->save();

            $this->logger->log(
                'payment',
                'traffic_violations',
                "رفع إيصال دفع المخالفة #{$violation->id} بقيمة {$violation->fine_amount} ل.س — بانتظار التحقق",
            );
        }

        return response()->json(['success' => true, 'message' => __('messages.payment_success')]);
    }
}
