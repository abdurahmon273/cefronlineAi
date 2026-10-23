<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Listenings\ExerciseResource;
use App\Http\Resources\Speaking\SpeakingExerciseWithPartResource;
use App\Http\Resources\Speaking\SpeakingResultResource;
use App\Http\Resources\Writing\WritingExerciseWithPartResource;
use App\Http\Resources\Writing\WritingResultResource;
use App\Models\Exercise;
use App\Models\PremiumToUsers;
use App\Models\SpeakingExercise;
use App\Models\SpeakingResult;
use App\Models\WritingExercise;
use App\Models\WritingResult;
use App\Services\PremiumApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpeakingExerciseApiController extends Controller
{
    public function  index(Request $request)
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

        $speaking_exercise=SpeakingExercise::find($request->id);

        if ($speaking_exercise) {
            return new SpeakingExerciseWithPartResource($speaking_exercise);
        }else {
            return response()->json(["message" => "Speaking exercise not found"], 404);
        }
    }

    public function speaking()
    {
        $exercises = Exercise::with('speaking_exercises')->where('category_type','3')->get();

        return ExerciseResource::collection($exercises);
    }
    public function speaking_part1(Request $request)
    {
        if ($request->device_id) {
            if (auth()->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }
        $exercises = Exercise::with('speaking_exercises')->where('category_type','3')->where('part_type','1')->orderBy('order_id')->get();

        return ExerciseResource::collection($exercises);
    }
    public function speaking_part2(Request $request)
    {
        if ($request->device_id) {
            if (auth()->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }
        $exercises = Exercise::with('speaking_exercises')->where('category_type','3')->where('part_type','2')->orderBy('order_id')->get();

        return ExerciseResource::collection($exercises);
    }
    public function speaking_part3(Request $request)
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

        $exercises = Exercise::with('speaking_exercises')->where('category_type','3')->where('part_type','3')->orderBy('order_id')->get();

        return ExerciseResource::collection($exercises);
    }

    public function speaking_full(Request $request)
    {
        if ($request->device_id) {
            if (auth()->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }

        $exercises = Exercise::with('speaking_exercises')->where('category_type','3')->where('part_type','4')->orderBy('order_id')->get();
        return ExerciseResource::collection($exercises);
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'category_type' => 'required|in:1,2,3,4',
            'speaking_exercise_id'   => 'required|exists:speaking_exercises,id',
            'audio_path'    => 'required',
        ]);

        SpeakingResult::create([
            'user_id'=>auth()->user()->id,
            'category_type'=>$request->category_type,
            'speaking_exercise_id'=>$request->speaking_exercise_id,
            'audio_path'=>$request->audio_path,
        ]);

        return response()->json(['message'=>'saqlandi'],202);
    }

    public function saved_results(Request $request)
    {

        $speaking_results = SpeakingResult::with('speaking_exercise.part_one_first.questions','speaking_exercise.part_one_second.questions','speaking_exercise.part_one_second.images','speaking_exercise.part_two.questions','speaking_exercise.part_two.images','speaking_exercise.part_three.questions','speaking_exercise.exercise')->where('user_id', Auth::id())->get();


        return SpeakingResultResource::collection($speaking_results);;
    }/**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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

    public function delete_saved_result()
    {
        $result=SpeakingResult::find(\request()->id);

        if ($result) {
            $result->delete();

            return response()->json(['message'=>'deleted'],202);

        }else{

            return response()->json(['message'=>'There is no result on this id'],404);

        }


    }

}
