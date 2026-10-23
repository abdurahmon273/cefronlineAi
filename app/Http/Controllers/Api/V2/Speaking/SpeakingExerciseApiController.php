<?php

namespace App\Http\Controllers\Api\V2\Speaking;

use App\Enums\CefrTypeEnum;
use App\Http\Controllers\Api\V2ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Listenings\ExerciseResource;
use App\Http\Resources\V2\Speaking\SpeakingExerciseWithPartResource;
use App\Http\Resources\V2\Speaking\SpeakingResultResource;
use App\Http\Resources\Writing\WritingExerciseWithPartResource;
use App\Http\Resources\Writing\WritingResultResource;
use App\Jobs\SpeakingChangeAudioFormat;
use App\Models\Exercise;
use App\Models\PremiumToUsers;
use App\Models\SpeakingExercise;
use App\Models\SpeakingPartOneFirstAnswer;
use App\Models\SpeakingPartOneFirstResult;
use App\Models\SpeakingPartOneSecondAnswer;
use App\Models\SpeakingPartOneSecondResult;
use App\Models\SpeakingPartThreeResult;
use App\Models\SpeakingPartTwoResult;
use App\Models\SpeakingResult;
use App\Models\WritingExercise;
use App\Models\WritingResult;
use App\Services\ExamService;
use App\Services\FullMockService;
use App\Services\PremiumApiService;
use App\Services\SpeakingStoreService;
use App\Traits\BillingTrait;
use App\Traits\SpeakingGetDataTrait;
use App\Traits\TestStoreHelperTrait;
use App\Traits\WritingGetDataTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpeakingExerciseApiController extends V2ApiController
{

    use BillingTrait,SpeakingGetDataTrait,WritingGetDataTrait;
    public function __construct(
        protected ExamService $examService,
    ){}

    /**
     * @OA\Get(
     *     path="/v2/speaking_exercise",
     *     operationId="getV2SpeakingExercise",
     *     tags={"Speaking API"},
     *     summary="Get speaking exercise ,Not Auth",
     *     description="Returns a single speaking exercise. It can be part 1, part 2, part 3, or full speaking.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer", example=15)
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
     *             @OA\Property(property="id", type="integer", example=15),
     *             @OA\Property(property="title", type="string", example="Speaking Mock 1"),
     *             @OA\Property(property="part_category", type="string", nullable=true, example="part 1"),
     *             @OA\Property(property="part_category_number", type="string", example="1"),
     *             @OA\Property(property="status", type="string", example="0"),
     *             @OA\Property(property="time_limit", type="integer", example=6),
     *             @OA\Property(property="start_time", type="integer", nullable=true, example=60),
     *             @OA\Property(property="part_two_start_time", type="integer", nullable=true, example=60),
     *             @OA\Property(property="part_three_start_time", type="integer", nullable=true, example=60),
     *             @OA\Property(property="example_answer", type="string", nullable=true, example="Example answer text"),
     *             @OA\Property(
     *                 property="part_one_first",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=21),
     *                 @OA\Property(property="title", type="string", example="Part 1"),
     *                 @OA\Property(
     *                     property="questions",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="modal_answer", type="string", nullable=true, example="Sample answer"),
     *                         @OA\Property(property="question", type="string", example="What do you do on weekends?"),
     *                         @OA\Property(property="time", type="integer", example=30),
     *                         @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/admin/speaking/audio/stream/example.mp3")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="part_one_second",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=22),
     *                 @OA\Property(property="title", type="string", example="Part 1 follow-up"),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="image", type="string", nullable=true, example="http://127.0.0.1:8000/storage/speaking_images/example.jpg")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="questions",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="modal_answer", type="string", nullable=true, example="Sample answer"),
     *                         @OA\Property(property="question", type="string", example="Describe what you can see in the picture."),
     *                         @OA\Property(property="time", type="integer", example=30),
     *                         @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/admin/speaking/audio/stream/example.mp3")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="part_two",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=31),
     *                 @OA\Property(property="modal_answer", type="string", nullable=true, example="Sample answer"),
     *                 @OA\Property(property="title", type="string", example="Part 2"),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="image", type="string", nullable=true, example="http://127.0.0.1:8000/storage/speaking_images/example.jpg")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="questions",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="question", type="string", example="Talk about a memorable journey.")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="part_three",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=41),
     *                 @OA\Property(property="title", type="string", example="Part 3"),
     *                 @OA\Property(property="modal_answer", type="string", nullable=true, example="Sample answer"),
     *                 @OA\Property(property="theme", type="string", nullable=true, example="Technology"),
     *                 @OA\Property(property="question_text", type="string", nullable=true, example="Do you think technology improves communication?")
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
     *             @OA\Property(property="message", type="string", example="Speaking exercise not found")
     *         )
     *     )
     * )
     */
    public function  index(Request $request)
    {
        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401);
            }
        }

        $speaking_exercise=SpeakingExercise::find($request->id);

        if($speaking_exercise->status=='1'){
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

        if ($speaking_exercise) {
            return new SpeakingExerciseWithPartResource($speaking_exercise);
        }else {
            return response()->json(["message" => "Speaking exercise not found"], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/v2/speaking/next/exercise/{id}",
     *     operationId="getNextSpeakingExercise",
     *     tags={"Speaking API"},
     *     summary="Get next speaking exercise",
     *     description="Returns the next speaking exercise in the same exercise and part category.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Current Speaking Exercise ID",
     *         @OA\Schema(type="integer", example=15)
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
     *                 @OA\Property(property="id", type="integer", example=15),
     *                 @OA\Property(property="title", type="string", example="Speaking Mock 1"),
     *                 @OA\Property(property="part_category", type="string", nullable=true, example="part 1"),
     *                 @OA\Property(property="part_category_number", type="string", example="1"),
     *                 @OA\Property(property="status", type="string", example="0"),
     *                 @OA\Property(property="time_limit", type="integer", example=6),
     *                 @OA\Property(property="start_time", type="integer", nullable=true, example=60),
     *                 @OA\Property(property="part_two_start_time", type="integer", nullable=true, example=60),
     *                 @OA\Property(property="part_three_start_time", type="integer", nullable=true, example=60),
     *                 @OA\Property(property="example_answer", type="string", nullable=true, example="Example answer text"),
     *
     *                 @OA\Property(
     *                     property="part_one_first",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=21),
     *                     @OA\Property(property="title", type="string", example="Part 1"),
     *                     @OA\Property(
     *                         property="questions",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="modal_answer", type="string", nullable=true, example="Sample answer"),
     *                             @OA\Property(property="question", type="string", example="What do you do on weekends?"),
     *                             @OA\Property(property="time", type="integer", example=30),
     *                             @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/admin/speaking/audio/stream/example.mp3")
     *                         )
     *                     )
     *                 ),
     *
     *                 @OA\Property(
     *                     property="part_one_second",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=22),
     *                     @OA\Property(property="title", type="string", example="Part 1 follow-up"),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="image", type="string", nullable=true, example="http://127.0.0.1:8000/storage/speaking_images/example.jpg")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="questions",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="modal_answer", type="string", nullable=true, example="Sample answer"),
     *                             @OA\Property(property="question", type="string", example="Describe what you can see in the picture."),
     *                             @OA\Property(property="time", type="integer", example=30),
     *                             @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/admin/speaking/audio/stream/example.mp3")
     *                         )
     *                     )
     *                 ),
     *
     *                 @OA\Property(
     *                     property="part_two",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=31),
     *                     @OA\Property(property="modal_answer", type="string", nullable=true, example="Sample answer"),
     *                     @OA\Property(property="title", type="string", example="Part 2"),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="image", type="string", nullable=true, example="http://127.0.0.1:8000/storage/speaking_images/example.jpg")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="questions",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="question", type="string", example="Talk about a memorable journey.")
     *                         )
     *                     )
     *                 ),
     *
     *                 @OA\Property(
     *                     property="part_three",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=41),
     *                     @OA\Property(property="title", type="string", example="Part 3"),
     *                     @OA\Property(property="modal_answer", type="string", nullable=true, example="Sample answer"),
     *                     @OA\Property(property="theme", type="string", nullable=true, example="Technology"),
     *                     @OA\Property(property="question_text", type="string", nullable=true, example="Do you think technology improves communication?")
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
        $speakingExercise=SpeakingExercise::with('exercise')->find($id);
        $next_speaking_Exercise=$speakingExercise->order_id ? SpeakingExercise::where('exercise_id',$speakingExercise->exercise_id)->where('part_category',$speakingExercise->part_category)->where('order_id','>',$speakingExercise->order_id)->first() : null;

        if ($next_speaking_Exercise){

            return $this->success(new SpeakingExerciseWithPartResource($next_speaking_Exercise) ,'exercise next');
        }else{

            return $this->error(['seccess'=>false],'exercise not found');
        }
    }

    /**
     * @OA\Get(
     *     path="/v2/speaking/part1/exercises",
     *     operationId="getV2SpeakingPart1Exercises",
     *     tags={"Speaking API"},
     *     summary="Get speaking part 1 exercises",
     *     description="Returns speaking part 1 exercise groups.",
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
     *                     @OA\Property(property="title", type="string", example="Tests 1-10"),
     *                     @OA\Property(property="exercise_count", type="integer", example=10),
     *                     @OA\Property(
     *                         property="speaking_exercises",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=26),
     *                             @OA\Property(property="title", type="string", example="Test 1"),
     *                             @OA\Property(property="part_category_number", type="string", example="1"),
     *                             @OA\Property(property="status", type="string", example="0")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/part1/exercises?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/part1/exercises?page=1"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example=null)
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/part1/exercises?page=1"),
     *                         @OA\Property(property="label", type="string", example="1"),
     *                         @OA\Property(property="active", type="boolean", example=true)
     *                     )
     *                 ),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/speaking/part1/exercises"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=3),
     *                 @OA\Property(property="total", type="integer", example=3)
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
    public function speaking_part1(Request $request)
    {
        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401);
            }
        }

        $exercises = Exercise::with(['speaking_exercises'=>function($query){
            $query->orderBy('order_id')->select('exercise_id','id','order_id','part_category','title','status');
        }])
            ->where('category_type','3')
            ->where('part_type','1')
            ->orderBy('order_id')
            ->paginate(10);

        return \App\Http\Resources\V2\Listening\ExerciseResource::collection($exercises);
    }

    /**
     * @OA\Get(
     *     path="/v2/speaking/part2/exercises",
     *     operationId="getV2SpeakingPart2Exercises",
     *     tags={"Speaking API"},
     *     summary="Get speaking part 2 exercises",
     *     description="Returns speaking part 2 exercise groups.",
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
     *                     @OA\Property(property="id", type="integer", example=12),
     *                     @OA\Property(property="title", type="string", example="Tests 1-10"),
     *                     @OA\Property(property="exercise_count", type="integer", example=10),
     *                     @OA\Property(
     *                         property="speaking_exercises",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=61),
     *                             @OA\Property(property="title", type="string", example="Test 1"),
     *                             @OA\Property(property="part_category_number", type="string", example="2"),
     *                             @OA\Property(property="status", type="string", example="0")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/part2/exercises?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/part2/exercises?page=1"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example=null)
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/part2/exercises?page=1"),
     *                         @OA\Property(property="label", type="string", example="1"),
     *                         @OA\Property(property="active", type="boolean", example=true)
     *                     )
     *                 ),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/speaking/part2/exercises"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=3),
     *                 @OA\Property(property="total", type="integer", example=3)
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
    public function speaking_part2(Request $request)
    {
        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401);
            }
        }

        $exercises = Exercise::with(['speaking_exercises'=>function($query){
            $query->orderBy('order_id')->select('exercise_id','id','order_id','part_category','title','status');
        }])
            ->where('category_type','3')
            ->where('part_type','2')
            ->orderBy('order_id')
            ->paginate(10);

        return \App\Http\Resources\V2\Listening\ExerciseResource::collection($exercises);
    }

    /**
     * @OA\Get(
     *     path="/v2/speaking/part3/exercises",
     *     operationId="getV2SpeakingPart3Exercises",
     *     tags={"Speaking API"},
     *     summary="Get speaking part 3 exercises",
     *     description="Returns speaking part 3 exercise groups.",
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
     *                     @OA\Property(property="id", type="integer", example=13),
     *                     @OA\Property(property="title", type="string", example="Tests 1-10"),
     *                     @OA\Property(property="exercise_count", type="integer", example=10),
     *                     @OA\Property(
     *                         property="speaking_exercises",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=91),
     *                             @OA\Property(property="title", type="string", example="Test 1"),
     *                             @OA\Property(property="part_category_number", type="string", example="3"),
     *                             @OA\Property(property="status", type="string", example="0")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/part3/exercises?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/part3/exercises?page=1"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example=null)
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/part3/exercises?page=1"),
     *                         @OA\Property(property="label", type="string", example="1"),
     *                         @OA\Property(property="active", type="boolean", example=true)
     *                     )
     *                 ),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/speaking/part3/exercises"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=3),
     *                 @OA\Property(property="total", type="integer", example=3)
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
    public function speaking_part3(Request $request)
    {
        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401);
            }
        }

        $exercises = Exercise::with(['speaking_exercises'=>function($query){
            $query->orderBy('order_id')->select('exercise_id','id','order_id','part_category','title','status');
        }])
            ->where('category_type','3')
            ->where('part_type','3')
            ->orderBy('order_id')
            ->paginate(10);

        return \App\Http\Resources\V2\Listening\ExerciseResource::collection($exercises);
    }

    /**
     * @OA\Get(
     *     path="/v2/speaking/full/exercises",
     *     operationId="getV2SpeakingFullExercises",
     *     tags={"Speaking API"},
     *     summary="Get full speaking exercises",
     *     description="Returns full speaking exercise groups.",
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
     *                     @OA\Property(property="id", type="integer", example=14),
     *                     @OA\Property(property="title", type="string", example="Tests 1-10"),
     *                     @OA\Property(property="exercise_count", type="integer", example=10),
     *                     @OA\Property(
     *                         property="speaking_exercises",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=121),
     *                             @OA\Property(property="title", type="string", example="Test 1"),
     *                             @OA\Property(property="part_category_number", type="string", example="4"),
     *                             @OA\Property(property="status", type="string", example="0")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/full/exercises?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/full/exercises?page=1"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example=null)
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/full/exercises?page=1"),
     *                         @OA\Property(property="label", type="string", example="1"),
     *                         @OA\Property(property="active", type="boolean", example=true)
     *                     )
     *                 ),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/speaking/full/exercises"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=3),
     *                 @OA\Property(property="total", type="integer", example=3)
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
    public function speaking_full(Request $request)
    {
        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401);
            }
        }

        $exercises = Exercise::with(['speaking_exercises'=>function($query){
            $query->orderBy('order_id')->select('exercise_id','id','order_id','part_category','title','status');
        }])
            ->where('category_type','3')
            ->where('part_type','4')
            ->orderBy('order_id')
            ->paginate(10);

        return \App\Http\Resources\V2\Listening\ExerciseResource::collection($exercises);
    }

    /**
     * @OA\Post(
     *     path="/v2/speaking/save",
     *     operationId="postV2SpeakingSave",
     *     tags={"Speaking API"},
     *     summary="Save speaking result",
     *     description="Saves a speaking result. Use multipart/form-data because recordings are uploaded files.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"category_type","speaking_exercise_id","with_ai","recordings"},
     *                 @OA\Property(
     *                     property="category_type",
     *                     type="string",
     *                     enum={"1","2","3","4"},
     *                     example="1",
     *                     description="1 = part 1, 2 = part 2, 3 = part 3, 4 = full"
     *                 ),
     *                 @OA\Property(property="speaking_exercise_id", type="integer", example=15),
     *                 @OA\Property(property="audio_path", type="string", nullable=true, example="records/speaking/audio_123.m4a"),
     *                 @OA\Property(property="with_ai", type="string", enum={"0","1"}, example="1"),
     *                 @OA\Property(
     *                     property="recordings",
     *                     type="object",
     *                     description="Each item should contain questionId and audio file. Unused parts may be omitted.",
     *                     @OA\Property(
     *                         property="part_one_first",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             required={"questionId","file"},
     *                             @OA\Property(property="questionId", type="integer", example=101),
     *                             @OA\Property(property="file", type="string", format="binary")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_one_second",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             required={"questionId","file"},
     *                             @OA\Property(property="questionId", type="integer", example=201),
     *                             @OA\Property(property="file", type="string", format="binary")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_two",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             required={"questionId","file"},
     *                             @OA\Property(property="questionId", type="integer", example=301),
     *                             @OA\Property(property="file", type="string", format="binary")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_three",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             required={"questionId","file"},
     *                             @OA\Property(property="questionId", type="integer", example=401),
     *                             @OA\Property(property="file", type="string", format="binary")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Saved",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=91),
     *             @OA\Property(property="next_exercise_id", type="integer", example=118,nullable=true),
     *             @OA\Property(property="message", type="string", example="Speaking Successfully saved.")
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
     *                 @OA\Property(property="category_type", type="array", @OA\Items(type="string", example="The category type field is required.")),
     *                 @OA\Property(property="speaking_exercise_id", type="array", @OA\Items(type="string", example="The selected speaking exercise id is invalid.")),
     *                 @OA\Property(property="with_ai", type="array", @OA\Items(type="string", example="The selected with ai is invalid.")),
     *                 @OA\Property(property="recordings", type="array", @OA\Items(type="string", example="The recordings field is required.",description="
     * recordings[part_one_first][0][questionId]
     * recordings[part_one_first][0][file]
     *
     * recordings[part_one_first][1][questionId]
     * recordings[part_one_first][1][file]
     *
     * recordings[part_two][0][questionId]
     * recordings[part_two][0][file]
     *
     *     "))
     *
     *             )
     *         )
     *     )
     * )
     */
    public function save(Request $request,SpeakingStoreService $speakingStoreService)
    {
        try {

        $this->validate($request, [
            'category_type' => 'required|in:1,2,3,4',
            'speaking_exercise_id'   => 'required|exists:speaking_exercises,id',
            'with_ai'   => 'required|in:0,1',
            'recordings' => 'required',
            'recordings.*' => 'array',
            'recordings.*.*.questionId' => 'required|integer',
            'recordings.*.*.file' => 'required|file|mimes:webm,mp3,wav,m4a,ogg|max:20480',
        ]);

        $withAi = (int) $request->with_ai ?? 0;
        $speaking_exercise=SpeakingExercise::select('id','part_category')
            ->where('id',$request->speaking_exercise_id)->first();
        $part_category=$speaking_exercise->part_category;

            $recordings = $request->file('recordings');
            $recordingInputs = $request->input('recordings', []);

            $user=auth()->user();
            $creditStatus=$this->getUserCreditStatus($withAi,$user->balance);

            $getAlertData=$this->getAlertData(($withAi ? $creditStatus : 1),'Your Credit Expired!','Speaking Successfully saved.');

            $speakingResult=SpeakingResult::create([
                'user_id'=>auth()->user()->id,
                'category_type'=>$request->category_type,
                'speaking_exercise_id'=>$request->speaking_exercise_id,
                'with_ai'=>$withAi,
                'status'=>$withAi ? ($creditStatus? 1 : 4) : 0,
                'max_score'=>CefrTypeEnum::SPEAKING_MAX_SCORES[$part_category],
            ]);
            $dir = 'voiceAudios';

            if ($part_category==1 || $part_category==4) {
                $speakingPartOneFirstResult=SpeakingPartOneFirstResult::create([
                    'speaking_result_id'=>$speakingResult->id,
                ]);

                if (isset($recordings['part_one_first'])){
                    $rows=[];
                    foreach ($recordings['part_one_first'] as $key => $data){
                        $questionId=$recordingInputs['part_one_first'][$key]['questionId'] ?? null;
                        $file=$data['file'] ?? $data;
                        $ext = $file->getClientOriginalExtension() ?: 'webm';
                        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'webm';

                        $filename = sprintf(
                            'ex_%d_res_part_1first_q_%d_%s.%s',
                            $speakingResult->id,
                            (int)$questionId,
                            Str::random(10),
                            $safeExt
                        );

                        $file->move(storage_path('app/private/' . $dir), $filename);

                        $rows[]=[
                            'one_first_result_id'=>$speakingPartOneFirstResult->id,
                            'question_id'=>$questionId,
                            'audio_path'=>$filename,
                            'created_at'=>now(),
                            'updated_at'=>now(),
                        ];
                    }
                    if ($rows) SpeakingPartOneFirstAnswer::insert($rows);
                }

                $speakingPartOneSecondResult=SpeakingPartOneSecondResult::create([
                    'speaking_result_id'=>$speakingResult->id,
                ]);

                if (isset($recordings['part_one_second'])){
                    $rows=[];
                    foreach ($recordings['part_one_second'] as $key => $data){
                        $questionId=$recordingInputs['part_one_second'][$key]['questionId'] ?? null;
                        $file=$data['file'] ?? $data;
                        $ext = $file->getClientOriginalExtension() ?: 'webm';
                        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'webm';

                        $filename = sprintf(
                            'ex_%d_res_part_1_second_q_%d_%s.%s',
                            $speakingResult->id,
                            (int)$questionId,
                            Str::random(10),
                            $safeExt
                        );

                        $file->move(storage_path('app/private/' . $dir), $filename);

                        $rows[]=[
                            'one_second_result_id'=>$speakingPartOneSecondResult->id,
                            'question_id'=>$questionId,
                            'audio_path'=>$filename,
                            'created_at'=>now(),
                            'updated_at'=>now(),
                        ];
                    }
                    if ($rows) SpeakingPartOneSecondAnswer::insert($rows);
                }

            }

            if ($part_category==2 || $part_category==4) {

                if (isset($recordings['part_two'])){
                    foreach ($recordings['part_two'] as $key => $data){
                        $questionId=$recordingInputs['part_two'][$key]['questionId'] ?? null;
                        $file=$data['file'] ?? $data;
                        $ext = $file->getClientOriginalExtension() ?: 'webm';
                        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'webm';

                        $filename = sprintf(
                            'ex_%d_res_part_2_q_%d_%s.%s',
                            $speakingResult->id,
                            (int)$questionId,
                            Str::random(10),
                            $safeExt
                        );

                        $file->move(storage_path('app/private/' . $dir), $filename);

                        $speakingPartTwoResult=SpeakingPartTwoResult::create([
                            'speaking_result_id'=>$speakingResult->id,
                            'question_id'=>$questionId,
                            'audio_path'=>$filename,
                        ]);
                    }
                }

            }

            if ($part_category==3 || $part_category==4) {

                if (isset($recordings['part_three'])){
                    foreach ($recordings['part_three'] as $key => $data){
                        $questionId=$recordingInputs['part_three'][$key]['questionId'] ?? null;
                        $file=$data['file'] ?? $data;
                        $ext = $file->getClientOriginalExtension() ?: 'webm';
                        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'webm';

                        $filename = sprintf(
                            'ex_%d_res_part_3_q_%d_%s.%s',
                            $speakingResult->id,
                            (int)$questionId,
                            Str::random(10),
                            $safeExt
                        );

                        $file->move(storage_path('app/private/' . $dir), $filename);

                        $speakingPartThreeResult=SpeakingPartThreeResult::create([
                            'speaking_result_id'=>$speakingResult->id,
                            'part_three_id'=>$questionId,
                            'audio_path'=>$filename,
                        ]);
                    }
                }
            }

            if ($withAi && $creditStatus){
                $speakingStoreService->dispatchToJob($speakingResult,$speaking_exercise,$user->id);
            }else{
                SpeakingChangeAudioFormat::dispatch($speakingResult->id);
            }


        return response()->json([
            'id'=>$speakingResult->id,
            'message'=>$getAlertData['text'],
            'next_exercise_id'=> null,
        ],202);

        }catch (\Exception $exception){
            Log::channel('telegram')->error(" Api error :\n speaking/save \n\n ".$this->escapeForTelegram($exception->getMessage()));

            return $this->error([$exception->getMessage()],'nimadur xato admin bilan bog`laning');

        }
    }

    /**
     * @OA\Get(
     *     path="/v2/speaking/result/status/{id}",
     *     operationId="getV2SpeakingResultStatus",
     *     tags={"Speaking API"},
     *     summary="Get speaking result checking status",
     *     description="Returns the AI checking status and progress percentage of a speaking result.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Speaking result ID",
     *         @OA\Schema(type="integer", example=15)
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
     *                 @OA\Property(property="percentage", type="integer", example=100),
     *                 @OA\Property(property="completed", type="boolean", example=true)
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
        $SpeakingResult=SpeakingResult::where('id',$id)->select('id','status','process_category_type','category_type','with_ai')->first();

        if (!$SpeakingResult){
            return response()->json([
                'success' => false,
                'data' => null,
                'message'=>'result not found'
            ]);
        }

        if ($SpeakingResult->with_ai ){
            if ($SpeakingResult->status==3){
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
                $category_type= $SpeakingResult->category_type> 0 ? ($SpeakingResult->category_type == '4' ? 3 : 1) : 1 ;
                $percentage=intval($SpeakingResult->process_category_type/($category_type)*100);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'status'=>$SpeakingResult->status,
                        'status_text'=>CefrTypeEnum::SPEAKING_STATUS_DATA[$SpeakingResult->status],
                        'percentage'=>$percentage,
                        'completed'=>$SpeakingResult->status ==4 ? true : false,
                    ]
                ]);
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
     *     path="/v2/speaking/results",
     *     operationId="getV2SpeakingResults",
     *     tags={"Speaking API"},
     *     summary="Get speaking saved results",
     *     description="Returns saved speaking results for the authenticated user (paginated, 10 per page).",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
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
     *                     @OA\Property(property="id", type="integer", example=91),
     *                     @OA\Property(property="Speaking_exercise_id", type="integer", example=15),
     *                     @OA\Property(property="category_type", type="string", example="4"),
     *                     @OA\Property(property="category", type="string", nullable=true, example="Full"),
     *                     @OA\Property(
     *                         property="percentage_data",
     *                         type="object",
     *                         @OA\Property(property="percent", type="integer", example=72),
     *                         @OA\Property(property="ball", type="integer", example=18)
     *                     ),
     *                     @OA\Property(property="max_score", type="integer", example=25),
     *                     @OA\Property(property="createdDate", type="string", example="26 iyun, 09:10")
     *                 )
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
     *                         @OA\Property(property="url", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/speaking/results?page=2"),
     *                         @OA\Property(property="label", type="string", example="2"),
     *                         @OA\Property(property="active", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/speaking/results"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=10),
     *                 @OA\Property(property="total", type="integer", example=29)
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
     *     )
     * )
     */
    public function saved_results(Request $request)
    {
        $speaking_results = SpeakingResult::where('user_id', Auth::id())
            ->orderByDesc('id')
            ->paginate(10);


        return SpeakingResultResource::collection($speaking_results);
    }

    /**
     * @OA\Get(
     *     path="/v2/speaking/result/{id}",
     *     operationId="getV2SpeakingResultShow",
     *     tags={"Speaking API"},
     *     summary="Get speaking result detail",
     *     description="Returns full speaking result detail with saved answers, AI result data, and part blocks.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=91)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=91),
     *                 @OA\Property(property="download_link", type="string", nullable=true, example="https://example.com/results/91/download"),
     *                 @OA\Property(property="createdText", type="string", example="31 май, 14:15"),
     *                 @OA\Property(
     *                     property="tabs",
     *                     type="object",
     *                     @OA\Property(property="part_one", type="string", example="Part One"),
     *                     @OA\Property(property="part_two", type="string", example="Task Two"),
     *                     @OA\Property(property="part_three", type="string", example="Part Three")
     *                 ),
     *                 @OA\Property(property="active", type="string", example="part_one"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(
     *                     property="percentageData",
     *                     type="object",
     *                     @OA\Property(property="percent", type="integer", example=72),
     *                     @OA\Property(property="array", type="integer", example=289),
     *                     @OA\Property(property="offset", type="integer", example=81),
     *                     @OA\Property(property="ball", type="integer", example=18),
     *                     @OA\Property(property="degree", type="string", nullable=true, example="B1")
     *                 ),
     *                 @OA\Property(
     *                     property="part_one_first",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=33),
     *                     @OA\Property(property="title", type="string", example="Part One"),
     *                     @OA\Property(
     *                         property="questions",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1001),
     *                             @OA\Property(property="question", type="string", example="Tell me about your hometown."),
     *                             @OA\Property(property="answer_text", type="string", example="<p>My hometown is...</p>"),
     *                             @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/admin/speaking/result/audio/ex_91_res_part_1first_q_1001_abcd1234.webm")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="url", type="string", example="/storage/speaking_images/example.jpg")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="resultData",
     *                         type="object",
     *                         @OA\Property(property="score", type="string", example="4.5"),
     *                         @OA\Property(property="max_score", type="integer", example=5),
     *                         @OA\Property(property="description", type="string", example="Good pronunciation and clear ideas.")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_one_second",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=34),
     *                     @OA\Property(property="title", type="string", example="Part One Follow-up"),
     *                     @OA\Property(
     *                         property="questions",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=2001),
     *                             @OA\Property(property="question", type="string", example="Describe what you can see."),
     *                             @OA\Property(property="answer_text", type="string", example="<p>I can see...</p>"),
     *                             @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/admin/speaking/result/audio/ex_91_res_part_1_second_q_2001_qwert123.webm")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=11),
     *                             @OA\Property(property="url", type="string", example="/storage/speaking_images/picture_1.jpg")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="resultData",
     *                         type="object",
     *                         @OA\Property(property="score", type="string", example="4"),
     *                         @OA\Property(property="max_score", type="integer", example=5),
     *                         @OA\Property(property="description", type="string", example="Vocabulary is acceptable but limited.")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_two",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=41),
     *                     @OA\Property(property="title", type="string", example="Part Two"),
     *                     @OA\Property(
     *                         property="question",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=3001),
     *                         @OA\Property(property="question", type="string", example="Talk about a memorable trip."),
     *                         @OA\Property(property="answer_text", type="string", example="<p>Last year I visited...</p>"),
     *                         @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/admin/speaking/result/audio/ex_91_res_part_2_q_3001_fghj5678.webm")
     *                     ),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=21),
     *                             @OA\Property(property="url", type="string", example="/storage/speaking_images/travel.jpg")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="resultData",
     *                         type="object",
     *                         @OA\Property(property="score", type="string", example="5"),
     *                         @OA\Property(property="max_score", type="integer", example=7),
     *                         @OA\Property(property="description", type="string", example="Well organized answer with relevant detail.")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_three",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=51),
     *                     @OA\Property(property="title", type="string", example="Part Three"),
     *                     @OA\Property(property="theme", type="string", nullable=true, example="Education"),
     *                     @OA\Property(property="question_text", type="string", nullable=true, example="FOR: online learning is flexible\nAGAINST: it reduces interaction"),
     *                     @OA\Property(
     *                         property="forAgainstData",
     *                         type="object",
     *                         @OA\Property(property="forDetails", type="string", example="<ul><li>online learning is flexible</li></ul>"),
     *                         @OA\Property(property="againtsDetails", type="string", example="<ul><li>it reduces interaction</li></ul>")
     *                     ),
     *                     @OA\Property(property="answer_text", type="string", nullable=true, example="<p>I believe both sides have valid points...</p>"),
     *                     @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/admin/speaking/result/audio/ex_91_res_part_3_q_4001_zxcv9876.webm"),
     *                     @OA\Property(
     *                         property="resultData",
     *                         type="object",
     *                         @OA\Property(property="score", type="string", example="5"),
     *                         @OA\Property(property="max_score", type="integer", example=8),
     *                         @OA\Property(property="description", type="string", example="Balanced discussion with good coherence.")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="feedbackCategories",
     *                     type="array",
     *                     @OA\Items(type="string", example="Pronunciation")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Speaking Result Show")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function result_show($id)
    {
        $data=$this->GetSpeakingResultApiData($id);

        return $this->success($data,'Speaking Result Show');
    }


    /**
     * API result_show uchun audio URL'ni speaking/answer/audio endpoint'iga
     * (to'liq https URL) yo'naltiradi. Trait'dagi default metodni override qiladi.
     */
    public function speakingResultAudioUrl($filename)
    {
        if (!$filename) {
            return null;
        }
        return route('admin.speaking.result.audio.stream', $filename);

    }


    /**
     * @OA\Get(
     *     path="/v2/speaking/answer/audio",
     *     operationId="getV2SpeakingAnswerAudio",
     *     tags={"Speaking API"},
     *     summary="Stream speaking answer audio by file name",
     *     description="Returns (streams) the recorded speaking answer audio file. The file name is the `audio_path` value returned in the speaking result show response.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="file",
     *         in="query",
     *         required=true,
     *         description="Audio file name (audio_path).",
     *         @OA\Schema(type="string", example="ex_91_res_part_1first_q_1001_abcd1234.webm")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Audio file stream",
     *         @OA\MediaType(
     *             mediaType="audio/mpeg",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="File not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="File name is required",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Audio file nomi yuborilmadi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     *
     * Stream a speaking answer audio file by its filename.
     *
     * GET /v2/speaking/answer/audio?file=ex_91_res_part_1first_q_1001_abcd1234.webm
     */
    public function get_audio(Request $request)
    {
        $filename = $request->query('file', $request->query('filename'));

        if (!$filename) {
            return $this->error(['file is required'], 'Audio file nomi yuborilmadi');
        }

        // path traversal himoyasi: faqat fayl nomini olamiz
        $filename = basename($filename);

        $path = 'voiceAudios/' . $filename;

        if (!Storage::disk('private')->exists($path)) {
            abort(404);
        }

        $mime = Storage::disk('private')->mimeType($path) ?: 'audio/mpeg';

        return new StreamedResponse(function () use ($path) {
            $stream = Storage::disk('private')->readStream($path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Accept-Ranges' => 'bytes',
        ]);
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
     * @OA\Delete(
     *     path="/v2/speaking/saved_result/delete",
     *     operationId="deleteV2SpeakingSavedResult",
     *     tags={"Speaking API"},
     *     summary="Delete speaking saved result",
     *     description="Deletes a saved speaking result by id.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer", example=91)
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
        $result=SpeakingResult::find(\request()->id);

        if ($result) {
            $result->delete();

            return response()->json(['message'=>'deleted'],202);

        }else{

            return response()->json(['message'=>'There is no result on this id'],404);

        }


    }

    public function escapeForTelegram($text)
    {
        $specials = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];

        foreach ($specials as $char) {
            $text = str_replace($char, ' ' . $char, $text);
        }

        return $text;
    }
}
