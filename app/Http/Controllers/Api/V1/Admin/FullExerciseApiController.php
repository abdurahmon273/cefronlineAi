<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Full\FullExerciseWithPartsResource;
use App\Http\Resources\Reading\ReadingExerciseWithPartResource;
use App\Models\FullExercise;
use App\Models\PremiumToUsers;
use App\Models\ReadingExercise;
use App\Services\PremiumApiService;
use Illuminate\Http\Request;

class FullExerciseApiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->device_id) {
            if (auth()->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }

        $existingPlan = PremiumToUsers::where('user_id', auth()->user()->id)
            ->where(function ($query) {
                $query->where('start_time', '<=', now())
                    ->where('end_time', '>=', now());
            })
            ->orderByDesc('id')
            ->first();

        if (!$existingPlan) {
            $premiumApi = new PremiumApiService();
            return $premiumApi->unPremium();
        }

        $full_exercise=FullExercise::find($request->id);

        if ($full_exercise) {
            return new FullExerciseWithPartsResource($full_exercise);
        }else {
            return response()->json(["message" => "Reading exercise not found"], 404);
        }
    }
}
