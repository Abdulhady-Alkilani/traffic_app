<?php

namespace App\Http\Controllers\Citizen;

use App\Enums\ViolationStatus;
use App\Http\Controllers\Controller;
use App\Models\TrafficViolation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ViolationController extends Controller
{
    public function mockPay(TrafficViolation $violation): JsonResponse
    {
        $user = Auth::user();
        $citizenData = $user->citizenData;

        if (!$citizenData || $violation->citizen_id !== $citizenData->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($violation->status !== ViolationStatus::Unpaid) {
            return response()->json(['success' => false, 'message' => 'Violation is not unpaid'], 400);
        }

        $violation->update(['status' => ViolationStatus::Paid]);

        return response()->json(['success' => true, 'message' => __('messages.payment_success')]);
    }
}
