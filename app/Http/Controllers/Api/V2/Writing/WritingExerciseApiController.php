<?php

namespace App\Http\Controllers\Api\V2\Writing;

use App\Enums\CefrTypeEnum;
use App\Http\Controllers\Api\V2ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Listening\ListeningResultResource;
use App\Http\Resources\Reading\ReadingExerciseWithPartResource;
use App\Http\Resources\V2\Listening\ExerciseResource;
use App\Http\Resources\V2\Writing\WritingExerciseWithPartResource;
use App\Http\Resources\V2\Writing\WritingResultResource;
use App\Models\Exercise;
use App\Models\ListeningResult;
use App\Models\PremiumToUsers;
use App\Models\ReadingExercise;
use App\Models\SpeakingResult;
use App\Models\WritingExercise;
use App\Models\WritingPartOneAnswer;
use App\Models\WritingPartTwoAnswer;
use App\Models\WritingResult;
use App\Services\CloudVisionAiService;
use App\Services\PremiumApiService;
use App\Services\WritingStoreService;
use App\Traits\BillingTrait;
use App\Traits\TestStoreHelperTrait;
use App\Traits\WritingGetDataTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WritingExerciseApiController extends V2ApiController
{
    use TestStoreHelperTrait,BillingTrait,WritingGetDataTrait;

    /**
     * @OA\Get(
     *     path="/v2/writing_exercise",
     *     operationId="getV2WritingExercise",
     *     tags={"Writing API"},
     *     summary="Get writing exercise",
     *     description="Returns a single writing exercise. It can be task 1, task 2, or full writing.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer", example=12)
     *     ),
     *     @OA\Parameter(
     *         name="device_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="device-123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=12),
     *             @OA\Property(property="name", type="string", example="Full Mock 1"),
     *             @OA\Property(property="part_category", type="string", nullable=true, example="Full mock"),
     *             @OA\Property(property="part_category_number", type="string", example="3"),
     *             @OA\Property(property="status", type="string", example="0"),
     *             @OA\Property(property="time_limit", type="integer", example=60),
     *             @OA\Property(property="prepare_text", type="string", example="You have 60 minutes to complete both tasks."),
     *             @OA\Property(
     *                 property="question",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=21),
     *                 @OA\Property(property="theme", type="string", example="Travel"),
     *                 @OA\Property(property="requirement", type="string", example="Write an email to your friend."),
     *                 @OA\Property(property="advice", type="string", example="Use simple and clear language."),
     *                 @OA\Property(property="example_text", type="string", nullable=true, example="Dear John, ..."),
     *                 @OA\Property(property="bullet_point_1", type="string", nullable=true, example="Explain why you are writing"),
     *                 @OA\Property(property="bullet_point_2", type="string", nullable=true, example="Give travel details"),
     *                 @OA\Property(property="bullet_point_3", type="string", nullable=true, example="Ask two questions")
     *             ),
     *             @OA\Property(
     *                 property="task_one",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=21),
     *                 @OA\Property(property="theme", type="string", example="Travel"),
     *                 @OA\Property(property="requirement", type="string", example="Write an email to your friend."),
     *                 @OA\Property(property="advice", type="string", example="Use simple and clear language."),
     *                 @OA\Property(property="example_text", type="string", nullable=true, example="Dear John, ..."),
     *                 @OA\Property(property="bullet_point_1", type="string", nullable=true, example="Explain why you are writing"),
     *                 @OA\Property(property="bullet_point_2", type="string", nullable=true, example="Give travel details"),
     *                 @OA\Property(property="bullet_point_3", type="string", nullable=true, example="Ask two questions")
     *             ),
     *             @OA\Property(
     *                 property="task_two",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=31),
     *                 @OA\Property(property="theme", type="string", example="Technology"),
     *                 @OA\Property(property="requirement", type="string", example="Write an essay about technology in education."),
     *                 @OA\Property(property="advice", type="string", example="Support your ideas with examples."),
     *                 @OA\Property(property="example_text", type="string", nullable=true, example="Technology has changed education...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized or device mismatch",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Undeviceided")
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="Unauthenticated.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=402,
     *         description="Premium required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="UnPremium")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Writing exercise not found")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }

        $writing_exercise=WritingExercise::find($request->id);

        if($writing_exercise->status=='1'){
            if (auth('sanctum')->user()) {
                $existingPlan = PremiumToUsers::where('user_id', auth('sanctum')->user()->id)
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
            }else{
                throw new \Illuminate\Auth\AuthenticationException();
            }
        }


        if ($writing_exercise) {
            return new WritingExerciseWithPartResource($writing_exercise);
        }else {
            return response()->json(["message" => "Writing exercise not found"], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/v2/writing/next/exercise/{id}",
     *     operationId="getNextWritingExercise",
     *     tags={"Writing API"},
     *     summary="Get next writing exercise",
     *     description="Returns the next writing exercise in the same exercise and part category.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Current Writing Exercise ID",
     *         @OA\Schema(type="integer", example=12)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *
     *                 @OA\Property(property="id", type="integer", example=12),
     *                 @OA\Property(property="name", type="string", example="Full Mock 1"),
     *                 @OA\Property(property="part_category", type="string", nullable=true, example="Full mock"),
     *                 @OA\Property(property="part_category_number", type="string", example="3"),
     *                 @OA\Property(property="status", type="string", example="0"),
     *                 @OA\Property(property="time_limit", type="integer", example=60),
     *                 @OA\Property(property="prepare_text", type="string", example="You have 60 minutes to complete both tasks."),
     *
     *                 @OA\Property(
     *                     property="question",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=21),
     *                     @OA\Property(property="theme", type="string", example="Travel"),
     *                     @OA\Property(property="requirement", type="string", example="Write an email to your friend."),
     *                     @OA\Property(property="advice", type="string", example="Use simple and clear language."),
     *                     @OA\Property(property="example_text", type="string", nullable=true, example="Dear John, ..."),
     *                     @OA\Property(property="bullet_point_1", type="string", nullable=true, example="Explain why you are writing"),
     *                     @OA\Property(property="bullet_point_2", type="string", nullable=true, example="Give travel details"),
     *                     @OA\Property(property="bullet_point_3", type="string", nullable=true, example="Ask two questions")
     *                 ),
     *
     *                 @OA\Property(
     *                     property="task_one",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=21),
     *                     @OA\Property(property="theme", type="string", example="Travel"),
     *                     @OA\Property(property="requirement", type="string", example="Write an email to your friend."),
     *                     @OA\Property(property="advice", type="string", example="Use simple and clear language."),
     *                     @OA\Property(property="example_text", type="string", nullable=true, example="Dear John, ..."),
     *                     @OA\Property(property="bullet_point_1", type="string", nullable=true, example="Explain why you are writing"),
     *                     @OA\Property(property="bullet_point_2", type="string", nullable=true, example="Give travel details"),
     *                     @OA\Property(property="bullet_point_3", type="string", nullable=true, example="Ask two questions")
     *                 ),
     *
     *                 @OA\Property(
     *                     property="task_two",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=31),
     *                     @OA\Property(property="theme", type="string", example="Technology"),
     *                     @OA\Property(property="requirement", type="string", example="Write an essay about technology in education."),
     *                     @OA\Property(property="advice", type="string", example="Support your ideas with examples."),
     *                     @OA\Property(property="example_text", type="string", nullable=true, example="Technology has changed education...")
     *                 )
     *             ),
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="exercise next"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Exercise not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="success",
     *                     type="boolean",
     *                     example=false
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="exercise not found"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     )
     * )
     */
    public function next_exercise($id)
    {
        $writingExercise=WritingExercise::with('exercise')->find($id);

        $next_writing_Exercise=$writingExercise->order_id ? WritingExercise::where('exercise_id',$writingExercise->exercise_id)->where('part_category',$writingExercise->part_category)->where('order_id','>',$writingExercise->order_id)->first() : null;

        if ($next_writing_Exercise){

            return $this->success(new WritingExerciseWithPartResource($next_writing_Exercise) ,'exercise next');

        }else{
            return $this->error(['seccess'=>false],'exercise not found');

        }
    }

    /**
     * @OA\Post(
     *     path="/v2/writing/task1/save",
     *     operationId="postV2WritingTask1Save",
     *     tags={"Writing API"},
     *     summary="Save writing task 1 result",
     *     description="Saves task 1 writing result and returns result_id. Use GET /v2/writing/result/{id} to get full checked result data.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"exercise_id","text_1","text_2","with_ai"},
     *             @OA\Property(property="exercise_id", type="integer", example=12),
     *             @OA\Property(property="text_1", type="string", example="First answer text..."),
     *             @OA\Property(property="text_2", type="string", example="Second answer text..."),
     *             @OA\Property(property="with_ai", type="integer", enum={0,1}, example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Saved",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="saqlandi"),
     *                     @OA\Property(property="result_id", type="integer", example=45),
     *                     @OA\Property(property="next_exercise_id", type="integer", example=118,nullable=true),
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=true),
     *                     @OA\Property(property="warning", type="boolean", example=true),
     *                     @OA\Property(property="message", type="string", example="Saqlandi, lekin AI balance yetarli emas.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="exercise_id", type="array", @OA\Items(type="string", example="Exercise ID is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function task1_save(Request $request,WritingStoreService $writingStoreService)
    {
        $this->validate($request, [
            'exercise_id' => 'required|exists:writing_exercises,id',
            'text_1' => 'nullable|string',
            'text_2' => 'nullable|string',
            'with_ai' => 'required|in:1,0',
        ], [
            'exercise_id.required' => 'Exercise ID is required.',
            'exercise_id.exists' => 'The exercise does not exist.',
            'text.required' => 'Text is required.',
        ]);


        $writingExercise=WritingExercise::find($request->exercise_id);
        $text_11 = $this->validateWritingText($request->text_1 ?? '', 300, 3000);
        $text_12 = $this->validateWritingText($request->text_2 ?? '', 300, 3000);
        $withAi=$request->input('with_ai');

        $text_1 = trim($text_11) . "\n\n\n" . trim($text_12);  // 2 qator tashlaydi
        $user=auth()->user();

        $creditStatus=$this->getUserCreditStatus($withAi,$user->balance);


        $writing_result=WritingResult::create([
            'category_type'=>$writingExercise->part_category,
            'writing_exercise_id'=>$writingExercise->id,
            'text_1'=>$text_1,
            'text_2'=>'',
            'user_id'=>$user->id,
            'with_ai'=>$withAi ? 1 : 0 ,
            'status'=>$withAi ? 1 : 0 ,
            'max_score'=>CefrTypeEnum::WRITING_MAX_SCORES[$writingExercise->part_category],
        ]);

        $writingPartOneAnswer=WritingPartOneAnswer::create([
            'text_1'=>$text_11,
            'text_2'=>$text_12,
            'writing_result_id'=>$writing_result->id,
            'max_score'=>CefrTypeEnum::WRITING_MAX_SCORES[1],
        ]);



        if ($withAi && $creditStatus){
            $writingStoreService->dispatchToJob($writing_result,$writingExercise,$user->id);
        }else{
            if ($withAi && (!$creditStatus)){
                return response()->json([
                    'success' => true,
                    'warning' => true,
                    'message' => 'Saqlandi, lekin AI balance yetarli emas.'
                ], 202);
            }
        }


        return response()->json([
            'message'=>'saqlandi',
            'result_id'=>$writing_result->id,
            'next_exercise_id'=> null,
        ],202);
    }

    /**
     * @OA\Post(
     *     path="/v2/writing/task2/save",
     *     operationId="postV2WritingTask2Save",
     *     tags={"Writing API"},
     *     summary="Save writing task 2 result",
     *     description="Saves task 2 writing result.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"exercise_id","text","with_ai"},
     *             @OA\Property(property="exercise_id", type="integer", example=22),
     *             @OA\Property(property="text", type="string", example="Essay answer text..."),
     *             @OA\Property(property="with_ai", type="integer", enum={0,1}, example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Saved",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="saqlandi"),
     *                     @OA\Property(property="result_id", type="integer", example=46),
     *                     @OA\Property(property="next_exercise_id", type="integer", example=118,nullable=true),
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=true),
     *                     @OA\Property(property="warning", type="boolean", example=true),
     *                     @OA\Property(property="message", type="string", example="Saqlandi, lekin AI balance yetarli emas.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="text", type="array", @OA\Items(type="string", example="Text is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function task2_save(Request $request,WritingStoreService $writingStoreService)
    {
        $this->validate($request, [
            'exercise_id' => 'required|exists:writing_exercises,id',
            'text' => 'nullable|string',
            'with_ai' => 'required|in:1,0',
        ], [
            'exercise_id.required' => 'Exercise ID is required.',
            'exercise_id.exists' => 'The exercise does not exist.',
            'text.required' => 'Text is required.',
        ]);


        $writingExercise=WritingExercise::find($request->exercise_id);
        $text_2  = $this->validateWritingText($request->text ?? '', 350, 3500);
        $withAi=$request->input('with_ai');

        $user=auth()->user();
        $creditStatus=$this->getUserCreditStatus($withAi,$user->balance);


        $writing_result=WritingResult::create([
            'category_type'=>$writingExercise->part_category,
            'writing_exercise_id'=>$writingExercise->id,
            'text_1'=>'',
            'text_2'=>$text_2,
            'user_id'=>$user->id,
            'with_ai'=>$withAi ? 1 : 0 ,
            'status'=>$withAi ? 1 : 0 ,
            'max_score'=>CefrTypeEnum::WRITING_MAX_SCORES[$writingExercise->part_category],
        ]);


        $writingPartTwoAnswer=WritingPartTwoAnswer::create([
            'text'=>$text_2,
            'writing_result_id'=>$writing_result->id,
            'max_score'=> CefrTypeEnum::WRITING_MAX_SCORES[2],
        ]);


        if ($withAi && $creditStatus){
            $writingStoreService->dispatchToJob($writing_result,$writingExercise,$user->id);
        }else{
            if ($withAi && (!$creditStatus))
                return response()->json([
                    'success' => true,
                    'warning' => true,
                    'message' => 'Saqlandi, lekin AI balance yetarli emas.'
                ], 202);
        }


        return response()->json([
            'message'=>'saqlandi',
            'result_id'=>$writing_result->id,
            'next_exercise_id'=> null,
        ],202);
    }

    /**
     * @OA\Post(
     *     path="/v2/writing/full/save",
     *     operationId="postV2WritingFullSave",
     *     tags={"Writing API"},
     *     summary="Save full writing result",
     *     description="Saves full writing result.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"exercise_id","text_1_1","text_1_2","text_2","with_ai"},
     *             @OA\Property(property="exercise_id", type="integer", example=32),
     *             @OA\Property(property="text_1_1", type="string", example="Task 1 first answer with \n\n..."),
     *             @OA\Property(property="text_1_2", type="string", example="Task 1 second answer with \n\n ..."),
     *             @OA\Property(property="text_2", type="string", example="Task 2 answer..."),
     *             @OA\Property(property="with_ai", type="integer", enum={0,1}, example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Saved",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="saqlandi"),
     *                     @OA\Property(property="result_id", type="integer", example=47),
     *                     @OA\Property(property="next_exercise_id", type="integer", example=118,nullable=true),
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="success", type="boolean", example=true),
     *                     @OA\Property(property="warning", type="boolean", example=true),
     *                     @OA\Property(property="message", type="string", example="Saqlandi, lekin AI balance yetarli emas.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="text_2", type="array", @OA\Items(type="string", example="Text is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function full_save(Request $request,WritingStoreService $writingStoreService)
    {
        $this->validate($request, [
            'exercise_id' => 'required|exists:writing_exercises,id',
            'text_1_1' => 'nullable|string',
            'text_1_2' => 'nullable|string',
            'text_2' => 'nullable|string',
            'with_ai' => 'required|in:1,0',
        ], [
            'exercise_id.required' => 'Exercise ID is required.',
            'exercise_id.exists' => 'The exercise does not exist.',
            'text.required' => 'Text is required.',
        ]);


        $writingExercise=WritingExercise::find($request->exercise_id);
        $text_11 = $this->validateWritingText($request->text_1_1 ?? '', 300, 3000);
        $text_12 = $this->validateWritingText($request->text_1_2 ?? '', 300, 3000);
        $text_2  = $this->validateWritingText($request->text_2 ?? '', 350, 3500);
        $withAi=$request->input('with_ai');

        $text_1 = trim($text_11) . "\n\n\n" . trim($text_12);  // 2 qator tashlaydi
        $user=auth()->user();

        $creditStatus=$this->getUserCreditStatus($withAi,$user->balance);


        $writing_result=WritingResult::create([
            'category_type'=>$writingExercise->part_category,
            'writing_exercise_id'=>$writingExercise->id,
            'text_1'=>$text_1,
            'text_2'=>$text_2,
            'user_id'=>$user->id,
            'with_ai'=>$withAi ? 1 : 0 ,
            'status'=>$withAi ? 1 : 0 ,
            'max_score'=>CefrTypeEnum::WRITING_MAX_SCORES[$writingExercise->part_category],
        ]);

        $writingPartOneAnswer=WritingPartOneAnswer::create([
            'text_1'=>$text_11,
            'text_2'=>$text_12,
            'writing_result_id'=>$writing_result->id,
            'max_score'=>CefrTypeEnum::WRITING_MAX_SCORES[1],
        ]);

        $writingPartTwoAnswer=WritingPartTwoAnswer::create([
            'text'=>$text_2,
            'writing_result_id'=>$writing_result->id,
            'max_score'=> CefrTypeEnum::WRITING_MAX_SCORES[2],
        ]);


        if ($withAi && $creditStatus){
            $writingStoreService->dispatchToJob($writing_result,$writingExercise,$user->id);
        }else{
            if ($withAi && (!$creditStatus)){
                return response()->json([
                    'success' => true,
                    'warning' => true,
                    'message' => 'Saqlandi, lekin AI balance yetarli emas.'
                ], 202);
            }
        }


        return response()->json([
            'message'=>'saqlandi',
            'result_id'=>$writing_result->id,
            'next_exercise_id'=> null,
        ],202);
    }
    /**
     * @OA\Get(
     *     path="/v2/writing/results",
     *     operationId="getV2WritingResults",
     *     tags={"Writing API"},
     *     summary="Get writing results",
     *     description="Returns paginated writing results (10 per page).",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, description="Page number", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=47),
     *                     @OA\Property(property="category_type", type="string", example="3"),
     *                     @OA\Property(property="category", type="string", nullable=true, example="Full"),
     *                     @OA\Property(property="writing_exercise_id", type="integer", example=32),
     *                     @OA\Property(property="date", type="string", format="date-time", example="2026-05-29T08:16:00.000000Z"),
     *                     @OA\Property(property="percentage", type="integer", example=65),
     *                     @OA\Property(property="ball", type="integer", example=49),
     *                     @OA\Property(property="score", type="number", format="float", nullable=true, example=5.5),
     *                     @OA\Property(property="max_score", type="integer", nullable=true, example=75),
     *                     @OA\Property(property="createdDate", type="string", example="26 iyun, 09:10")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/writing/results?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/writing/results?page=3"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/writing/results?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/writing/results?page=2"),
     *                         @OA\Property(property="label", type="string", example="2"),
     *                         @OA\Property(property="active", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/writing/results"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=10),
     *                 @OA\Property(property="total", type="integer", example=29)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    public function saved_results(Request $request)
    {

        $writing_results=WritingResult::where('user_id',Auth::id())
            ->orderByDesc('id')
            ->paginate(10);


        return WritingResultResource::collection($writing_results); ;
    }
    /**
     * @OA\Get(
     *     path="/v2/writing/result/status/{id}",
     *     operationId="getV2WritingResultStatus",
     *     tags={"Writing API"},
     *     summary="Get writing result checking status",
     *     description="Returns the AI checking status and progress percentage of a writing result.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Writing result ID",
     *         @OA\Schema(type="integer", example=47)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 nullable=true,
     *                 @OA\Property(property="status", type="integer", example=3),
     *                 @OA\Property(property="status_text", type="string", example="Complated"),
     *                 @OA\Property(property="percentage", type="integer", example=100)
     *             ),
     *             @OA\Property(property="message", type="string", nullable=true, example="result not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */

    public function get_result_status($id)
    {
        $WritingResult=WritingResult::where('id',$id)->select('id','status','process_category_type','category_type','with_ai')->first();

        if (!$WritingResult){
            return response()->json([
                'success' => false,
                'data' => null,
                'message'=>'result not found'
            ]);
        }

        if ($WritingResult->with_ai ){
            if ($WritingResult->status==3){
                return response()->json([
                    'success' => true,
                    'data' => [
                        'status'=>3,
                        'status_text'=>'Complated',
                        'percentage'=>100,
                        'completed'=>true,
                    ],
                ]);
            }else{
                if ($WritingResult->status){
                    $category_type= $WritingResult->category_type> 0 ? ($WritingResult->category_type == '3' ? 2 : 1) : 1 ;
                    $percentage=intval($WritingResult->process_category_type/($category_type)*100);

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'status'=>$WritingResult->status,
                            'status_text'=>CefrTypeEnum::WRITING_STATUS_DATA[$WritingResult->status],
                            'percentage'=>$percentage,
                            'completed'=>$WritingResult->status ==4 ? true : false,
                        ]
                    ]);
                }

            }
        }else{
            return response()->json([
                'success' => true,
                'data' => [
                    'status'=>0,
                    'status_text'=>'Complated',
                    'percentage'=>0,
                    'completed'=>true,
                ],
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/v2/writing/result/{id}",
     *     operationId="getV2WritingResultShow",
     *     tags={"Writing API"},
     *     summary="Get writing result detail",
     *     description="Returns writing result detail.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=47)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="writing_result_id", type="integer", example=524),
     *                 @OA\Property(property="category_type", type="string", example="3"),
     *                 @OA\Property(property="type", type="integer", example=2),
     *                 @OA\Property(property="download_link", type="string", nullable=true, example="https://example.com/results/524/download"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(
     *                     property="tabs",
     *                     type="object",
     *                     @OA\Property(property="task_one", type="string", example="Task One"),
     *                     @OA\Property(property="task_two", type="string", nullable=true, example="Task Two")
     *                 ),
     *                 @OA\Property(property="active", type="string", nullable=true, example="task_one"),
     *                 @OA\Property(
     *                     property="task_one",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(
     *                         property="text_1",
     *                         type="string",
     *                         description="Curly braces {...} show suggested corrected text. Square brackets [...] show the original text with mistake.",
     *                         example="Hi Aziz,\n\nI am thinking about the next Travel Club trip. I would like to visit Istanbul because it {has} [have] many beautiful places."
     *                     ),
     *                     @OA\Property(
     *                         property="text_2",
     *                         type="string",
     *                         description="Curly braces {...} show suggested corrected text. Square brackets [...] show the original text with mistake.",
     *                         example="Dear Travel Club Organizer,\n\nThank you for {asking for our opinions} [asking our opinions] about the next trip."
     *                     ),
     *                     @OA\Property(
     *                         property="task_response",
     *                         type="object",
     *                         @OA\Property(property="task_achievement", type="string", example="Task 1.1\nThe email answers the prompts.\nTask 1.2\nThe response fully addresses the prompt."),
     *                         @OA\Property(property="lexical_resource", type="string", example="Task 1.1\nVocabulary is adequate and appropriate.\nTask 1.2\nVocabulary is appropriate and varied enough."),
     *                         @OA\Property(property="coherence_and_cohesion", type="string", example="Task 1.1\nOrganized logically with simple linking devices.\nTask 1.2\nIdeas are well organized into clear paragraphs."),
     *                         @OA\Property(property="grammatical_range_and_accuracy", type="string", example="Task 1.1\nUses simple grammatical structures mostly accurately.\nTask 1.2\nThe candidate uses a range of sentence structures.")
     *                     ),
     *                     @OA\Property(property="description_1", type="string", example="Covers all task requirements and communicates clearly, with some grammatical errors typical of Upper B1 level."),
     *                     @OA\Property(property="description_2", type="string", example="A relevant, well-structured email that meets the task requirements with minor grammatical and word-choice errors that do not impede understanding."),
     *                     @OA\Property(property="question", type="string", example="You are a member of the Travel Club at your language center. You received an email from the club organizer."),
     *                     @OA\Property(
     *                         property="resultData",
      *                         nullable=true,
     *                         type="object",
     *                         @OA\Property(property="score_1", type="string", example="4"),
     *                         @OA\Property(property="score_2", type="string", example="4"),
     *                         @OA\Property(property="max_score_1", type="integer", example=5),
     *                         @OA\Property(property="max_score_2", type="integer", example=5),
     *                         @OA\Property(property="ball", type="integer", example=47),
     *                         @OA\Property(property="max_ball", type="integer", example=53)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="task_two",
      *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(
     *                         property="text",
     *                         type="string",
     *                         description="Curly braces {...} show suggested corrected text. Square brackets [...] show the original text with mistake.",
     *                         example="In my opinion, social media have both positive and negative effects on young people. However, I think {they} [it] can be more useful than harmful."
     *                     ),
     *                     @OA\Property(
     *                         property="task_response",
     *                         type="object",
     *                         @OA\Property(property="task_achievement", type="string", example="Vazifa mantiqan to‘liq bajarilgan: muammo tahlil qilinib, fikr bildirilgan va misollar keltirilgan."),
     *                         @OA\Property(property="grammatical_range_and_accuracy", type="string", example="Grammatik shakllar asosiy jihatlari bilan ishlatilgan, biroq ko‘p sonli xatolar mavjud."),
     *                         @OA\Property(property="lexical_resource", type="string", example="Lug‘at yetarli darajada; baʼzi so‘zlar noto‘g‘ri ishlatilgan."),
     *                         @OA\Property(property="coherence_and_cohesion", type="string", example="Matn mantiqan yaxshi tashkil etilgan va paragraf tuzilishi saqlangan.")
     *                     ),
     *                     @OA\Property(property="description", type="string", example="Clear position and relevant arguments with good coherence, but noticeable grammatical and lexical errors prevent a higher score."),
     *                     @OA\Property(property="question", type="string", example="You are participating in an online discussion for language learners."),
     *                     @OA\Property(
     *                         property="resultData",
     *                         nullable=true,
     *                         type="object",
     *                         @OA\Property(property="score", type="string", example="4"),
     *                         @OA\Property(property="max_score", type="integer", example=6),
     *                         @OA\Property(property="ball", type="integer", example=12),
     *                         @OA\Property(property="max_ball", type="integer", example=22)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="percentageData",
     *                     type="object",
     *                     @OA\Property(property="percent", type="integer", example=81),
     *                     @OA\Property(property="array", type="integer", example=289),
     *                     @OA\Property(property="offset", type="integer", example=55),
     *                     @OA\Property(property="degree", type="string", example="B2")
     *                 ),
     *                 @OA\Property(property="createdText", type="string", example="30 май, 11:57")
     *             ),
     *             @OA\Property(property="message", type="string", example="result qaytarildi.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Unauthenticated."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\WritingResult] 999"))
     *     )
     * )
     */
    public function result_show($id)
    {
        $data=$this->GetWritingResultApiData($id);

        return $this->success($data,'result qaytarildi.');
    }


    public function writing()
    {
        $exercises = Exercise::with('reading_exercises')
            ->where('category_type','2')
            ->orderBy('order_id')
            ->get();

        return ExerciseResource::collection($exercises);
    }
    /**
     * @OA\Get(
     *     path="/v2/writing/task1/exercises",
     *     operationId="getV2WritingTask1Exercises",
     *     tags={"Writing API"},
     *     summary="Get writing task 1 exercises",
     *     description="Returns paginated writing task 1 exercise groups.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="device_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="device-123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=9),
     *                     @OA\Property(property="title", type="string", example="Writing Task 1"),
     *                     @OA\Property(property="part_type", type="string", example="Task One"),
     *                     @OA\Property(property="exercise_count", type="integer", example=10),
     *                     @OA\Property(
     *                         property="writing_exercises",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=12),
     *                             @OA\Property(property="name", type="string", example="Letter 1"),
     *                             @OA\Property(property="part_category_number", type="string", example="1"),
     *                             @OA\Property(property="status", type="string", example="0")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/writing/task1/exercises?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example=null),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/writing/task1/exercises?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/writing/task1/exercises"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=10)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Device mismatch",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Undeviceided")
     *         )
     *     )
     * )
     */
    public function writing_task1(Request $request)
    {
        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }

        $exercises = Exercise::with([
            'writing_exercises' => function ($query) {
                $query->orderBy('order_id')
                    ->select('id','name','exercise_id','status','part_category');
            }
        ])
            ->orderBy('order_id')
            ->where('category_type', '2')
            ->where('part_type', '1')
            ->simplePaginate(10);

        return ExerciseResource::collection($exercises);
    }

    /**
     * @OA\Get(
     *     path="/v2/writing/task2/exercises",
     *     operationId="getV2WritingTask2Exercises",
     *     tags={"Writing API"},
     *     summary="Get writing task 2 exercises",
     *     description="Returns paginated writing task 2 exercise groups.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="device_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="device-123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="title", type="string", example="Writing Task 2"),
     *                     @OA\Property(property="part_type", type="string", example="Task Two"),
     *                     @OA\Property(property="exercise_count", type="integer", example=10),
     *                     @OA\Property(
     *                         property="writing_exercises",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=22),
     *                             @OA\Property(property="name", type="string", example="Essay 1"),
     *                             @OA\Property(property="part_category_number", type="string", example="2"),
     *                             @OA\Property(property="status", type="string", example="0")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/writing/task2/exercises?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example=null),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/writing/task2/exercises?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/writing/task2/exercises"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=10)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Device mismatch",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Undeviceided")
     *         )
     *     )
     * )
     */
    public function writing_task2(Request $request)
    {
        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }

        $exercises = Exercise::with([
            'writing_exercises' => function ($query) {
                $query->orderBy('order_id')
                    ->select('id','name','exercise_id','status','part_category');
            }
        ])
            ->orderBy('order_id')
            ->where('category_type', '2')
            ->where('part_type', '2')
            ->simplePaginate(10);

        return ExerciseResource::collection($exercises);
    }

    /**
     * @OA\Get(
     *     path="/v2/writing/full/exercises",
     *     operationId="getV2WritingFullExercises",
     *     tags={"Writing API"},
     *     summary="Get full writing exercises",
     *     description="Returns paginated full writing exercise groups.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="device_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="device-123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=11),
     *                     @OA\Property(property="title", type="string", example="Writing Full Mock"),
     *                     @OA\Property(property="part_type", type="string", example="Task Two"),
     *                     @OA\Property(property="exercise_count", type="integer", example=10),
     *                     @OA\Property(
     *                         property="writing_exercises",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=32),
     *                             @OA\Property(property="name", type="string", example="Full Mock 1"),
     *                             @OA\Property(property="part_category_number", type="string", example="3"),
     *                             @OA\Property(property="status", type="string", example="0")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/writing/full/exercises?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example=null),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/writing/full/exercises?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/writing/full/exercises"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=10)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Device mismatch",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Undeviceided")
     *         )
     *     )
     * )
     */
    public function writing_full(Request $request)
    {
        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }

        $exercises = Exercise::with([
            'writing_exercises' => function ($query) {
                $query->orderBy('order_id')
                    ->select('id','name','exercise_id','status','part_category');
            }
        ])
            ->orderBy('order_id')
            ->where('category_type', '2')
            ->where('part_type', '3')
            ->simplePaginate(10);

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
    /**
     * @OA\Delete(
     *     path="/v2/writing/saved_result/delete",
     *     operationId="deleteV2WritingSavedResult",
     *     tags={"Writing API"},
     *     summary="Delete writing result",
     *     description="Deletes a writing result by id.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer", example=47)
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Unauthenticated."))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="There is no result on this id")
     *         )
     *     )
     * )
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

    /**
     * @OA\Post(
     *     path="/v2/writing/image/change",
     *     operationId="postV2WritingImageChange",
     *     tags={"Writing API"},
     *     summary="Extract text from writing image",
     *     description="Accepts an uploaded image from the authenticated user, spends 1 credit, and returns the OCR text extracted from the image.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"images"},
     *                 @OA\Property(
     *                     property="images",
     *                     description="Image files to read text from. Send as multipart/form-data using images[]. Each file must be an image and no larger than 5 MB.",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Text extracted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="texts",
     *                 description="OCR result returned by Cloud Vision service.",
     *                 oneOf={
     *                     @OA\Schema(type="string", example="The chart shows a steady increase in sales over the period."),
     *                     @OA\Schema(type="array", @OA\Items(type="string", example="The chart shows a steady increase in sales over the period."))
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Not enough credit",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="credit", type="string", example="expired")
     *             ),
     *             @OA\Property(property="message", type="string", example="credit not anough")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="The given data was invalid."),
     *                     @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         @OA\Property(
     *                             property="images",
     *                             type="array",
     *                             @OA\Items(type="string", example="The images field is required.")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="errors_images_0",
     *                         type="array",
     *                         @OA\Items(type="string", example="The images.0 must be an image.")
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function readImage(Request $request,CloudVisionAiService  $cloudVisionAiService)
    {
        $this->validate($request,[
            'images' => 'required|array|min:1',
            'images.*' => 'required|file|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'images.required' => 'At least one image is required.',
            'images.array' => 'Images must be sent as an array.',
            'images.min' => 'At least one image is required.',
            'images.*.required' => 'Each image is required.',
            'images.*.file' => 'Each item must be a valid file.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Allowed image types: jpg, jpeg, png, webp.',
            'images.*.max' => 'Each image must not be greater than 5 MB.',
        ]);

        $user=auth('sanctum')->user();
        $creditStatus=$this->getUserCreditStatus(1,$user->balance);

        if (!$creditStatus){
           return $this->error(['credit'=>'expired'],'credit not anough');
        }

        $images = $request->file('images', []);

        $text = $cloudVisionAiService->ImagesToText($images);

        return response()->json([
            'success' => true,
            'texts' => $text,
        ]);
    }
}
