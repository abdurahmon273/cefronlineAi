<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\CefrTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Listening\ListeningResultResource;
use App\Http\Resources\Listenings\ExerciseResource;
use App\Http\Resources\Reading\ReadingExerciseWithPartResource;
use App\Http\Resources\Writing\WritingExerciseWithPartResource;
use App\Http\Resources\Writing\WritingResultResource;
use App\Models\Exercise;
use App\Models\ListeningResult;
use App\Models\PremiumToUsers;
use App\Models\ReadingExercise;
use App\Models\SpeakingResult;
use App\Models\WritingExercise;
use App\Models\WritingPartOneAnswer;
use App\Models\WritingPartTwoAnswer;
use App\Models\WritingResult;
use App\Services\PremiumApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WritingExerciseApiController extends Controller
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

        $reading_exercise=WritingExercise::find($request->id);

        if ($reading_exercise) {
            return new WritingExerciseWithPartResource($reading_exercise);
        }else {
            return response()->json(["message" => "Writing exercise not found"], 404);
        }
    }
    public function save(Request $request)
    {
        $request->merge([
            'category_type' => (string)$request->input('category_type'),
        ]);

        $this->validate($request, [
            'category_type' => 'required|in:1,2',
            'exercise_id' => 'required|exists:writing_exercises,id',
            'text' => 'required',
        ], [
            'category_type.required' => 'Category type is required.',
            'category_type.in' => 'Category type must be 1 or 2.',
            'exercise_id.required' => 'Exercise ID is required.',
            'exercise_id.exists' => 'The exercise does not exist.',
            'text.required' => 'Text is required.',
        ]);



        $text_1=null;
        $text_2=null;
        if ($request->category_type=='1') {
            $text_1=$request->text;

        }elseif ($request->category_type=='2') {
            $text_2=$request->text;
        }

        $writing_result=WritingResult::create([
            'category_type'=>$request->category_type,
            'writing_exercise_id'=>$request->exercise_id,
            'text_1'=>$text_1,
            'text_2'=>$text_2,
            'user_id'=>Auth::id(),
        ]);

        if ($request->category_type== '1' || $request->category_type== '3') {
            $writingPartOneAnswer=WritingPartOneAnswer::create([
                'text_1'=>$text_1,
                'text_2'=>'',
                'writing_result_id'=>$writing_result->id,
                'max_score'=>CefrTypeEnum::WRITING_MAX_SCORES[1],
            ]);
        }

        if ($request->category_type== '2' || $request->category_type== '3') {
            $writingPartTwoAnswer=WritingPartTwoAnswer::create([
                'text'=>$text_2,
                'writing_result_id'=>$writing_result->id,
                'max_score'=> CefrTypeEnum::WRITING_MAX_SCORES[2],
            ]);
        }

      return response()->json(['message'=>'saqlandi'],202);
    }

    public function full_save(Request $request)
    {


        $this->validate($request, [
            'exercise_id' => 'required|exists:writing_exercises,id',
            'text_1' => 'required',
            'text_2' => 'required',
        ], [
            'exercise_id.required' => 'Exercise ID is required.',
            'exercise_id.exists' => 'The exercise does not exist.',
            'text_1.required' => 'Text is required.',
            'text_2.required' => 'Text is required.',
        ]);



        $text_1=$request->text_1;
        $text_2=$request->text_2;


        $writing_result=WritingResult::create([
            'category_type'=>'3',
            'writing_exercise_id'=>$request->exercise_id,
            'text_1'=>$text_1,
            'text_2'=>$text_2,
            'user_id'=>Auth::id(),
        ]);


      return response()->json(['message'=>'saqlandi'],202);
    }
    public function saved_results(Request $request)
    {

        $writing_results=WritingResult::where('user_id',Auth::id())->get();


        return WritingResultResource::collection($writing_results); ;
    }

    public function writing()
    {
        $exercises = Exercise::with('reading_exercises')->where('category_type','2')->orderBy('order_id')->get();

        return ExerciseResource::collection($exercises);
    }
    public function writing_task1(Request $request)
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

        $exercises = Exercise::with([
            'writing_exercises' => function ($query) {
                $query->orderBy('order_id');
            },
            'writing_exercises.task_one',
            'writing_exercises.task_two',
        ])
            ->orderBy('order_id')
            ->where('category_type', '2')
            ->where('part_type', '1')
            ->get();

        return ExerciseResource::collection($exercises);
    }

    public function writing_task2(Request $request)
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

        $exercises = Exercise::with([
            'writing_exercises' => function ($query) {
                $query->orderBy('order_id');
            },
            'writing_exercises.task_one',
            'writing_exercises.task_two',
        ])
            ->orderBy('order_id')
            ->where('category_type','2')
            ->where('part_type','2')
            ->get();

        return ExerciseResource::collection($exercises);
    }

    public function writing_full(Request $request)
    {
        if ($request->device_id) {
            if (auth()->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }

        $exercises = Exercise::with([
            'writing_exercises' => function ($query) {
                $query->orderBy('order_id');
            },
            'writing_exercises.task_one',
            'writing_exercises.task_two',
        ])
            ->where('category_type','2')
            ->where('part_type','3')
            ->orderBy('order_id')
            ->get();

        return ExerciseResource::collection($exercises);
    }

    /**
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
    public function delete_saved_result()
    {
        $result=WritingResult::find(\request()->id);

        if ($result) {
            $result->delete();

            return response()->json(['message'=>'deleted'],202);

        }else{

            return response()->json(['message'=>'There is no result on this id'],404);

        }


    }
    public function destroy(string $id)
    {
        //
    }
}
