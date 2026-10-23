<?php

namespace App\Http\Controllers\Api\V1\Admin\Listenings;

use App\Http\Controllers\Controller;
use App\Http\Resources\Listening\ListeningResultResource;
use App\Http\Resources\Listenings\ListeningExerciseResource;
use App\Http\Resources\Listenings\ListeningExerciseWithPartResource;
use App\Http\Resources\Writing\WritingResultResource;
use App\Models\ListeningExercise;
use App\Models\ListeningResult;
use App\Models\PremiumToUsers;
use App\Models\WritingResult;
use App\Services\PremiumApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListeningExerciseApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

        $listening_exercise=ListeningExercise::find($request->id);

        if ($listening_exercise) {
            return new ListeningExerciseWithPartResource($listening_exercise);
        }else {
            return response()->json(["message" => "Listening exercise not found"], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    public function save(Request $request)
    {

        $this->validate($request, [
            'listening_exercise_id' => 'required|exists:listening_exercises,id',
            'all_answers' => 'required',
            'correct_answers' => 'required',
        ], [
            'listening_exercise_id.required' => 'Exercise ID is required.',
            'listening_exercise_id.exists' => 'The exercise does not exist.',
            'all_answers.required' => 'all_answers is required.',
            'correct_answers.required' => 'correct_answers is required.',
        ]);




        $percent=($request->correct_answers*(100))/$request->all_answers;
        $listening_exercise=ListeningExercise::find($request->listening_exercise_id);
        $writing_result=ListeningResult::create([
            'listening_exercise_id'=>$request->listening_exercise_id,
            'category_type'=>$listening_exercise->part_category ?? null,
            'all_answers'=>$request->all_answers,
            'correct_answers'=>$request->correct_answers,
            'user_id'=>Auth::id(),
            'percent'=>$percent,
        ]);


        return response()->json(['message'=>'saqlandi'],202);
    }
    public function saved_results(Request $request)
    {

        $listening_results=ListeningResult::where('user_id',Auth::id())->get();


        return ListeningResultResource::collection($listening_results); ;
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
