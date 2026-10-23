<?php

namespace App\Http\Controllers\Api\V2\Listening;

use App\Enums\CefrTypeEnum;
use App\Http\Controllers\Api\V2ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V2;
use App\Http\Resources\V2\Listening\ListeningExerciseResource;
use App\Http\Resources\V2\Listening\ListeningExerciseWithPartResource;
use App\Http\Resources\V2\Listening\ListeningResultResource;
use App\Http\Resources\Writing\WritingResultResource;
use App\Models\ListeningExercise;
use App\Models\ListeningResult;
use App\Models\PremiumToUsers;
use App\Models\WritingResult;
use App\Services\PremiumApiService;
use App\Traits\ListeningGetDataTrait;
use App\Traits\ReadingGetDataTrait;
use App\Traits\TestStoreHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListeningExerciseApiController extends V2ApiController
{
    use TestStoreHelperTrait,ListeningGetDataTrait,ReadingGetDataTrait;

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/v2/listening_exercise",
     *     operationId="getV2ListeningExercise",
     *     tags={"Listening API"},
     *     summary="Get listening exercise detail",
     *     description="Returns a single listening exercise. Response is part-based: one part for normal exercises, six parts for full listening.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="Listening exercise id",
     *         @OA\Schema(type="integer", example=419)
     *     ),
     *     @OA\Parameter(
     *         name="device_id",
     *         in="query",
     *         required=false,
     *         description="Device id",
     *         @OA\Schema(type="string", example="device-123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=38),
     *                 @OA\Property(property="name", type="string", example="Test 2.1"),
     *                 @OA\Property(property="part_category", type="string", example="full"),
     *                 @OA\Property(property="part_category_number", type="string", example="7"),
     *                 @OA\Property(property="status", type="string", example="0"),
     *                 @OA\Property(property="difficulty", type="string", example="2"),
     *                 @OA\Property(property="time_limit", type="integer", example=0),
     *                 @OA\Property(property="result_array", type="array", nullable=true, @OA\Items(type="integer", example=23)),
     *                 @OA\Property(
     *                     property="part_one",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=25),
     *                     @OA\Property(property="audio", type="string", nullable=true, example="http://127.0.0.1:8000/storage/audios/part_one_audio.mp3"),
     *                     @OA\Property(property="advice", type="string", example="You will hear some sentences."),
     *                     @OA\Property(property="tests", type="array", @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=65),
     *                         @OA\Property(property="answer_1", type="string", example="For the transportation department."),
     *                         @OA\Property(property="answer_2", type="string", example="No, I ordered online."),
     *                         @OA\Property(property="answer_3", type="string", example="Traffic’s really heavy today."),
     *                         @OA\Property(property="correct_answer", type="string", example=3)
     *                     ))
     *                 ),
     *                 @OA\Property(
     *                     property="part_two",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=15),
     *                     @OA\Property(property="audio", type="string", nullable=true, example="http://127.0.0.1:8000/storage/audios/part_two_audio.mp3"),
     *                     @OA\Property(property="advice", type="string", example="You will hear someone giving a talk."),
     *                     @OA\Property(property="title", type="string", nullable=true, example=null),
     *                     @OA\Property(property="text", type="string", example="Video Game Review ..."),
     *                     @OA\Property(property="tests", type="array", @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=41),
     *                         @OA\Property(property="count_number", type="string", example="9"),
     *                         @OA\Property(property="correct_answer", type="string", example="rainforest"),
     *                         @OA\Property(property="extra_answer", type="string", nullable=true, example=null)
     *                     ))
     *                 ),
     *                 @OA\Property(
     *                     property="part_three",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=15),
     *                     @OA\Property(property="audio", type="string", nullable=true, example="http://127.0.0.1:8000/storage/audios/part_three_audio.mp3"),
     *                     @OA\Property(property="advice", type="string", example="You will hear people speaking in different situations."),
     *                     @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                     @OA\Property(property="speakers", type="array", @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=32),
     *                         @OA\Property(property="title", type="string", example="15. Speaker 1"),
     *                         @OA\Property(property="correct_answer_id", type="integer", example=42)
     *                     )),
     *                     @OA\Property(property="answers", type="array", @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=42),
     *                         @OA\Property(property="letter_key", type="string", example="0"),
     *                         @OA\Property(property="answer", type="string", example="Exams are necessary.")
     *                     ))
     *                 ),
     *                 @OA\Property(
     *                     property="part_four",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=16),
     *                     @OA\Property(property="audio", type="string", nullable=true, example="http://127.0.0.1:8000/storage/audios/part_four_audio.mp3"),
     *                     @OA\Property(property="image", type="string", nullable=true, example="http://127.0.0.1:8000/storage/maps/image.png"),
     *                     @OA\Property(property="advice", type="string", example="You will hear someone giving a talk."),
     *                     @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                     @OA\Property(property="questions", type="array", @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=36),
     *                         @OA\Property(property="title", type="string", example="19. Harbor View Bookstore"),
     *                         @OA\Property(property="correct_answer_id", type="integer", example=48)
     *                     )),
     *                     @OA\Property(property="answers", type="array", @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=48),
     *                         @OA\Property(property="letter_key", type="string", example="0"),
     *                         @OA\Property(property="answer", type="string", example=" ")
     *                     ))
     *                 ),
     *                 @OA\Property(
     *                     property="part_five",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=26),
     *                     @OA\Property(property="audio", type="string", nullable=true, example="http://127.0.0.1:8000/storage/audios/part_five_audio.mp3"),
     *                     @OA\Property(property="advice", type="string", example="You will hear three extracts."),
     *                     @OA\Property(property="tests", type="array", @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=73),
     *                         @OA\Property(property="question", type="string", example="What does the man complain about?"),
     *                         @OA\Property(property="answer_1", type="string", example="the cost of accommodation"),
     *                         @OA\Property(property="answer_2", type="string", example="the lack of snow"),
     *                         @OA\Property(property="answer_3", type="string", example="the lack of snow"),
     *                         @OA\Property(property="correct_answer", type="string", example="2")
     *                     ))
     *                 ),
     *                 @OA\Property(
     *                     property="part_six",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=16),
     *                     @OA\Property(property="audio", type="string", nullable=true, example="http://127.0.0.1:8000/storage/audios/part_six_audio.mp3"),
     *                     @OA\Property(property="advice", type="string", example="You will hear a part of a lecture."),
     *                     @OA\Property(property="title", type="string", nullable=true, example=null),
     *                     @OA\Property(property="text", type="string", example="Roman Tablets ..."),
     *                     @OA\Property(property="tests", type="array", @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=47),
     *                         @OA\Property(property="count_number", type="string", example="30"),
     *                         @OA\Property(property="correct_answer", type="string", example="envelope"),
     *                         @OA\Property(property="extra_answer", type="string", nullable=true, example=null)
     *                     ))
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(type="object",
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Undeviceided")
     *                 ),
     *                 @OA\Schema(type="object",
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
     *         description="Listening exercise not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Listening exercise not found")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        if ($request->device_id && \auth()->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }

        $listening_exercise=ListeningExercise::find($request->id);

        if($listening_exercise->status=='1'){
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

        if ($listening_exercise) {
            return new ListeningExerciseWithPartResource($listening_exercise);
        }else {
            return response()->json(["message" => "Listening exercise not found"], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/v2/listening/next/exercise/{id}",
     *     operationId="getNextListeningExercise",
     *     tags={"Listening API"},
     *     summary="Get next listening exercise",
     *     description="Returns the next listening exercise in the same exercise and part category.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Current Listening Exercise ID",
     *         @OA\Schema(type="integer", example=38)
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
     *                 @OA\Property(property="id", type="integer", example=39),
     *                 @OA\Property(property="name", type="string", example="Test 2.2"),
     *                 @OA\Property(property="part_category", type="string", example="full"),
     *                 @OA\Property(property="part_category_number", type="string", example="7"),
     *                 @OA\Property(property="status", type="string", example="0"),
     *                 @OA\Property(property="difficulty", type="string", example="2"),
     *                 @OA\Property(property="time_limit", type="integer", example=0),
     *                 @OA\Property(property="result_array", type="array", nullable=true, @OA\Items(type="integer")),
     *
     *                 @OA\Property(
     *                     property="part_one",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=25),
     *                     @OA\Property(property="audio", type="string", nullable=true),
     *                     @OA\Property(property="advice", type="string"),
     *                     @OA\Property(
     *                         property="tests",
     *                         type="array",
     *                         @OA\Items(type="object")
     *                     )
     *                 ),
     *
     *                 @OA\Property(
     *                     property="part_two",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="audio", type="string", nullable=true),
     *                     @OA\Property(property="advice", type="string"),
     *                     @OA\Property(property="title", type="string", nullable=true),
     *                     @OA\Property(property="text", type="string"),
     *                     @OA\Property(
     *                         property="tests",
     *                         type="array",
     *                         @OA\Items(type="object")
     *                     )
     *                 ),
     *
     *                 @OA\Property(
     *                     property="part_three",
     *                     nullable=true,
     *                     type="object"
     *                 ),
     *
     *                 @OA\Property(
     *                     property="part_four",
     *                     nullable=true,
     *                     type="object"
     *                 ),
     *
     *                 @OA\Property(
     *                     property="part_five",
     *                     nullable=true,
     *                     type="object"
     *                 ),
     *
     *                 @OA\Property(
     *                     property="part_six",
     *                     nullable=true,
     *                     type="object"
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
     *                 @OA\Property(property="success", type="boolean", example=false)
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
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function next_exercise($id)
    {
        $listeningExercise=ListeningExercise::with('exercise')->find($id);

        if (!$listeningExercise->order_id){
            return $this->error(['seccess'=>false],'exercise not found');
        }
        $next_listening_Exercise= $listeningExercise->order_id ? ListeningExercise::where('exercise_id',$listeningExercise->exercise_id)->where('part_category',$listeningExercise->part_category)->where('order_id','>',$listeningExercise->order_id)->first() : null;

        if ($next_listening_Exercise){

            return $this->success(new ListeningExerciseWithPartResource($next_listening_Exercise) ,'exercise next');

        }else{

            return $this->error(['seccess'=>false],'exercise not found');
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/v2/listening_save",
     *     operationId="postV2ListeningSave",
     *     tags={"Listening API"},
     *     summary="Save listening result",
     *     description="Send result data for available listening parts.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"listening_exercise_id","all_answers","correct_answers","wrong_answers","parts_data"},
     *             @OA\Property(property="listening_exercise_id", type="integer", example=38),
     *             @OA\Property(property="all_answers", type="integer", example=35),
     *             @OA\Property(property="correct_answers", type="integer", example=29),
     *             @OA\Property(property="wrong_answers", type="integer", example=5),
     *             @OA\Property(
     *                 property="parts_data",
     *                 type="object",
     *                 description="Each part can be null or omitted. If sent, every item must contain all required keys.",
     *                 @OA\Property(
     *                     property="part_one",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         required={"id","is_correct","answer","correct_answer_key","correct_answer"},
     *                         @OA\Property(property="id", type="integer", example=65),
     *                         @OA\Property(property="is_correct", type="integer", example=0),
     *                         @OA\Property(property="answer", type="string", example="answer_3"),
     *                         @OA\Property(property="correct_answer_key", type="string", example="answer_2"),
     *                         @OA\Property(property="correct_answer", type="string", example="It's a generous offer")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_two",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         required={"id","is_correct","answer","correct_answer"},
     *                         @OA\Property(property="id", type="integer", example=41),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer", type="string", example="five"),
     *                         @OA\Property(property="correct_answer", type="string", example="six"),
     *                         @OA\Property(property="extra_answer", type="string", example="6",nullable=true)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_three",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         required={"id","is_correct","answer_id","correct_answer","correct_answer_id"},
     *                         @OA\Property(property="id", type="integer", example=32),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer_id", type="integer", example=42),
     *                         @OA\Property(property="correct_answer", type="string", example="Exams are necessary."),
     *                         @OA\Property(property="correct_answer_id", type="integer", example=42)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_four",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         required={"id","is_correct","answer_id","correct_answer","correct_answer_id"},
     *                         @OA\Property(property="id", type="integer", example=36),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer_id", type="integer", example=48),
     *                         @OA\Property(property="correct_answer", type="string", example="E"),
     *                         @OA\Property(property="correct_answer_id", type="integer", example=48)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_five",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         required={"id","is_correct","answer","correct_answer_key","correct_answer"},
     *                         @OA\Property(property="id", type="integer", example=73),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer", type="string", example="answer_2"),
     *                         @OA\Property(property="correct_answer_key", type="string", example="answer_2"),
     *                         @OA\Property(property="correct_answer", type="string", example="the lack of snow")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_six",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         required={"id","is_correct","answer","correct_answer"},
     *                         @OA\Property(property="id", type="integer", example=47),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer", type="string", example="envelope"),
     *                         @OA\Property(property="correct_answer", type="string", example="envelope")
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
     *             @OA\Property(property="id", type="integer", example=118),
     *             @OA\Property(property="next_exercise_id", type="integer", example=118,nullable=true),
     *             @OA\Property(property="message", type="string", example="saqlandi"),
     *             @OA\Property(
     *                 property="part_data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="part_one",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=65),
     *                         @OA\Property(property="is_correct", type="integer", example=0),
     *                         @OA\Property(property="answer", type="string", example="answer_3"),
     *                         @OA\Property(property="correct_answer_key", type="string", example="answer_2"),
     *                         @OA\Property(property="correct_answer", type="string", example="It is a generous offer")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_two",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=41),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer", type="string", example="five"),
     *                         @OA\Property(property="correct_answer", type="string", example="six")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_three",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=32),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer_id", type="integer", example=42),
     *                         @OA\Property(property="correct_answer", type="string", example="Exams are necessary."),
     *                         @OA\Property(property="correct_answer_id", type="integer", example=42)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_four",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=36),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer_id", type="integer", example=48),
     *                         @OA\Property(property="correct_answer", type="string", example="E"),
     *                         @OA\Property(property="correct_answer_id", type="integer", example=48)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_five",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=73),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer", type="string", example="answer_2"),
     *                         @OA\Property(property="correct_answer_key", type="string", example="answer_2"),
     *                         @OA\Property(property="correct_answer", type="string", example="the lack of snow")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_six",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=47),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer", type="string", example="envelope"),
     *                         @OA\Property(property="correct_answer", type="string", example="envelope")
     *                     )
     *                 )
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
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="listening_exercise_id", type="array", @OA\Items(type="string", example="Exercise ID is required.")),
     *                 @OA\Property(property="all_answers", type="array", @OA\Items(type="string", example="all_answers is required.")),
     *                 @OA\Property(property="correct_answers", type="array", @OA\Items(type="string", example="correct_answers is required.")),
     *                 @OA\Property(property="wrong_answers", type="array", @OA\Items(type="string", example="wrong_answers is required.")),
     *                 @OA\Property(property="parts_data", type="array", @OA\Items(type="string", example="The parts data field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function save(Request $request)
    {

        $this->validate($request, [
            'listening_exercise_id' => 'required|exists:listening_exercises,id',
            'all_answers' => 'required',
            'correct_answers' => 'required',
            'wrong_answers' => 'required',
            'parts_data' => 'required|array',
        ], [
            'listening_exercise_id.required' => 'Exercise ID is required.',
            'listening_exercise_id.exists' => 'The exercise does not exist.',
            'all_answers.required' => 'all_answers is required.',
            'correct_answers.required' => 'correct_answers is required.',
        ]);
        $listening_exercise=ListeningExercise::find($request->listening_exercise_id);


        $result=ListeningResult::create([
            'listening_exercise_id'=>$request->listening_exercise_id,
            'category_type'=>$listening_exercise->part_category ?? null,
            'all_answers'=>$request->all_answers,
            'correct_answers'=>$request->correct_answers,
            'wrong_answers'=>$request->wrong_answers,
            'user_id'=>Auth::id(),
            'percent'=>($request->correct_answers/($request->all_answers > 0 ? $request->all_answers : 1))*100,
        ]);

        $part_data = $this->createListeningResultParts($result,$request->parts_data);


        $next_listening_Exercise= $listening_exercise->order_id ? ListeningExercise::where('exercise_id',$listening_exercise->exercise_id)->where('part_category',$listening_exercise->part_category)->where('order_id','>',$listening_exercise->order_id)->first() : null;

        if($next_listening_Exercise && $next_listening_Exercise->status){
            $existingPlan = PremiumToUsers::where('user_id', auth('sanctum')->user()->id)
                ->where(function ($query) {
                    $query->where('start_time', '<=', now())
                        ->where('end_time', '>=', now());
                })
                ->orderByDesc('id')
                ->first();

            if (!$existingPlan) {
                $next_listening_Exercise=null;
            }
        }

        return response()->json([
            'id'=>$result->id,
            'next_exercise_id'=>$next_listening_Exercise? $next_listening_Exercise->id: null,
            'message'=>'saqlandi',
            'part_data'=>$part_data,
        ],202);
    }

    /**
     * @OA\Get(
     *     path="/v2/listening/results",
     *     operationId="getV2ListeningResults",
     *     tags={"Listening API"},
     *     summary="Get listening results",
     *     description="Returns the authenticated user's listening results (paginated, 10 per page).",
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
     *                     @OA\Property(property="id", type="integer", example=12),
     *                     @OA\Property(property="all_answers", type="integer", example=35),
     *                     @OA\Property(property="correct_answers", type="integer", example=29),
     *                     @OA\Property(property="wrong_answers", type="integer", example=6),
     *                     @OA\Property(property="percentage", type="number", format="float", example=82.86),
     *                     @OA\Property(property="category", type="string", nullable=true, example="part 6"),
     *                     @OA\Property(property="listening_exercise_id", type="integer", example=38),
     *                     @OA\Property(property="createdDate", type="string", example="26 iyun, 09:10")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/listening/results?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/listening/results?page=6"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/listening/results?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="last_page", type="integer", example=6),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/listening/results?page=2"),
     *                         @OA\Property(property="label", type="string", example="2"),
     *                         @OA\Property(property="active", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/listening/results"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=10),
     *                 @OA\Property(property="total", type="integer", example=59)
     *                 )
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
    public function results(Request $request)
    {
        $listening_results = ListeningResult::where('user_id', Auth::id())
            ->orderByDesc('id')
            ->whereDoesntHave('full_results')
            ->whereHas('exercise')
            ->paginate(10);

        return ListeningResultResource::collection($listening_results);
    }
    /**
     * @OA\Get(
     *     path="/v2/listening/result/show/{id}",
     *     operationId="getV2ListeningResultShow",
     *     tags={"Listening API"},
     *     summary="Get listening result detail",
     *     description="Returns full listening result detail with all available parts.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=118)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=118),
     *                 @OA\Property(property="listening_exercise_id", type="integer", example=164),
     *                 @OA\Property(property="createdText", type="string", example="4 март, 08:16"),
     *                 @OA\Property(property="name", type="string", example="Full Mock 1"),
     *                 @OA\Property(property="status", type="string", example="0"),
     *                 @OA\Property(property="type", type="integer", example=0),
     *                 @OA\Property(property="part_category", type="string", nullable=true, example="full"),
     *                 @OA\Property(property="part_category_number", type="string", example="7"),
     *                 @OA\Property(property="difficulty", type="string", example="0"),
     *                 @OA\Property(property="asset_url", type="string", example="/"),
     *                 @OA\Property(property="time_limit", type="integer", example=35),
     *                 @OA\Property(property="audio_control", type="integer", example=0),
     *                 @OA\Property(property="all_answers", type="integer", example=35),
     *                 @OA\Property(property="correct_answers", type="integer", example=2),
     *                 @OA\Property(property="wrong_answers", type="integer", example=0),
     *                 @OA\Property(property="feedback", type="string", example="Your score is very low. You need to improve your basics."),
     *                 @OA\Property(property="suggestion", type="string", example="Focus on simple English listening and reading exercises daily"),
     *                 @OA\Property(
     *                     property="percentageData",
     *                     type="object",
     *                     @OA\Property(property="percent", type="integer", example=6),
     *                     @OA\Property(property="array", type="integer", example=289),
     *                     @OA\Property(property="offset", type="integer", example=272),
     *                     @OA\Property(property="mock_ball", type="integer", nullable=true, example=26),
     *                     @OA\Property(property="degree", type="string", nullable=true, example="Below B1"),
     *                     @OA\Property(property="is_mock", type="boolean", example=true)
     *                 ),
     *                 @OA\Property(
     *                     property="parts_data",
     *                     type="object",
     *                     @OA\Property(property="part_one", type="string", example="Part One"),
     *                     @OA\Property(property="part_two", type="string", example="Part Two"),
     *                     @OA\Property(property="part_three", type="string", example="Part Three"),
     *                     @OA\Property(property="part_four", type="string", example="Part Four"),
     *                     @OA\Property(property="part_five", type="string", example="Part Five"),
     *                     @OA\Property(property="part_six", type="string", example="Part Six")
     *                 ),
     *                 @OA\Property(property="active", type="string", nullable=true, example="part_one"),
     *                 @OA\Property(
     *                     property="part_one",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=72),
     *                     @OA\Property(property="advice", type="string", example="You will hear some sentences. You will hear each sentence twice. Choose the correct reply to each sentence (A, B, or C)."),
     *                     @OA\Property(property="audio", type="string", nullable=true, example="/storage/audios/part_one_audio_1772507684_69a65224a46ca.mp3"),
     *                     @OA\Property(
     *                         property="tests",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=428),
     *                             @OA\Property(property="answer_1", type="string", example="That's too bad."),
     *                             @OA\Property(property="answer_2", type="string", example="No, I'm not."),
     *                             @OA\Property(property="answer_3", type="string", example="I think it is."),
     *                             @OA\Property(property="correct_answer_key", type="string", example="answer_3"),
     *                             @OA\Property(property="status", type="string", example="not_answered"),
     *                             @OA\Property(property="user_answer_key", type="string", nullable=true, example=null),
     *                             @OA\Property(property="correct_answer", type="string", nullable=true, example=null),
     *                             @OA\Property(property="is_correct", type="integer", nullable=true, example=null)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="question_data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="user_answer", type="string", example="Not answered"),
     *                             @OA\Property(property="correct_answer", type="string", example="I think it is."),
     *                             @OA\Property(property="is_correct", type="boolean", example=false)
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_two",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=69),
     *                     @OA\Property(property="audio", type="string", nullable=true, example="/storage/audios/part_two_audio_1772507684_69a65224a5c98.mp3"),
     *                     @OA\Property(property="advice", type="string", example="You will hear someone giving a talk."),
     *                     @OA\Property(property="title", type="string", nullable=true, example=null),
     *                     @OA\Property(property="text", type="string", example="School trip to farm\r\nMeeting place: 8 a.m. next to the (9)[9]"),
     *                     @OA\Property(
     *                         property="answers",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=384),
     *                             @OA\Property(property="count_number", type="string", example="9"),
     *                             @OA\Property(property="extra_answer", type="string", nullable=true, example=null),
     *                             @OA\Property(property="correct_answer", type="string", example="gate"),
     *                             @OA\Property(property="status", type="string", example="not_answered"),
     *                             @OA\Property(property="user_answer", type="string", nullable=true, example=null),
     *                             @OA\Property(property="is_correct", type="integer", nullable=true, example=null)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="question_data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="user_answer", type="string", example="Not answered"),
     *                             @OA\Property(property="correct_answer", type="string", example="gate"),
     *                             @OA\Property(property="is_correct", type="boolean", example=false)
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_three",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=87),
     *                     @OA\Property(property="advice", type="string", example="You will hear people speaking in different situations. Match each speaker (15-18) to the place where the speaker is (A-F). There are TWO EXTRA places which you do not need to use. Mark your answers on the answer sheet."),
     *                     @OA\Property(property="audio", type="string", nullable=true, example="/storage/audios/part_three_audio_1772507684_69a65224a6c47.mp3"),
     *                     @OA\Property(property="map", type="string", nullable=true, example=""),
     *                     @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                     @OA\Property(
     *                         property="questions",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=374),
     *                             @OA\Property(property="title", type="string", example="15. Speaker 1"),
     *                             @OA\Property(property="correct_answer_id", type="integer", example=585)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="answers",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=585),
     *                             @OA\Property(property="letter_key", type="string", example="0"),
     *                             @OA\Property(property="answer", type="string", example="A) Why people go on excursion."),
     *                             @OA\Property(property="is_selected", type="boolean", example=false)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="result_rows",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="question_id", type="integer", nullable=true, example=374),
     *                             @OA\Property(property="answer_id", type="integer", nullable=true, example=585),
     *                             @OA\Property(property="correct_answer_id", type="integer", nullable=true, example=585),
     *                             @OA\Property(property="correct_answer", type="string", nullable=true, example="A) Why people go on excursion."),
     *                             @OA\Property(property="is_correct", type="integer", example=0),
     *                             @OA\Property(property="status", type="string", example="answered")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="question_data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="user_answer", type="string", example="Not answered"),
     *                             @OA\Property(property="correct_answer", type="string", example="A) Why people go on excursion."),
     *                             @OA\Property(property="is_correct", type="boolean", example=false)
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_four",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=88),
     *                     @OA\Property(property="advice", type="string", example="You will hear someone giving a talk. Label the places (19-23) on the map (A-F). There areFOUR extra options which you do not need to use. Mark your answers on the answer sheet"),
     *                     @OA\Property(property="audio", type="string", nullable=true, example="/storage/audios/part_four_audio_1772514480_69a66cb03e859.mp3"),
     *                     @OA\Property(property="map", type="string", nullable=true, example="public/maps/image_1772507760_69a652709962b.jpg"),
     *                     @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                     @OA\Property(
     *                         property="questions",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=378),
     *                             @OA\Property(property="title", type="string", example="19. The Reynolds House"),
     *                             @OA\Property(property="correct_answer_id", type="integer", example=598)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="answers",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=591),
     *                             @OA\Property(property="letter_key", type="string", example="0"),
     *                             @OA\Property(property="answer", type="string", example="A"),
     *                             @OA\Property(property="is_selected", type="boolean", example=false)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="result_rows",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="question_id", type="integer", nullable=true, example=378),
     *                             @OA\Property(property="answer_id", type="integer", nullable=true, example=598),
     *                             @OA\Property(property="correct_answer_id", type="integer", nullable=true, example=598),
     *                             @OA\Property(property="correct_answer", type="string", nullable=true, example="H"),
     *                             @OA\Property(property="is_correct", type="integer", example=0),
     *                             @OA\Property(property="status", type="string", example="answered")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="question_data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="user_answer", type="string", example="Not answered"),
     *                             @OA\Property(property="correct_answer", type="string", example="H"),
     *                             @OA\Property(property="is_correct", type="boolean", example=false)
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_five",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=73),
     *                     @OA\Property(property="advice", type="string", example="You will hear three extracts."),
     *                     @OA\Property(property="audio", type="string", nullable=true, example="/storage/audios/part_five_audio_1772507684_69a65224a7cf9.mp3"),
     *                     @OA\Property(
     *                         property="tests",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=436),
     *                             @OA\Property(property="answer_1", type="string", example="her children enjoy listening to her read aloud."),
     *                             @OA\Property(property="answer_2", type="string", example="she shares a reading habit with other parents."),
     *                             @OA\Property(property="answer_3", type="string", example="parents should read aloud to children."),
     *                             @OA\Property(property="correct_answer_key", type="string", example="answer_1"),
     *                             @OA\Property(property="question", type="string", example="Extract One\r\n24. The second speaker says that she believes that"),
     *                             @OA\Property(property="status", type="string", example="not_answered"),
     *                             @OA\Property(property="user_answer_key", type="string", nullable=true, example=null),
     *                             @OA\Property(property="correct_answer", type="string", nullable=true, example=null),
     *                             @OA\Property(property="is_correct", type="integer", nullable=true, example=null)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="question_data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="user_answer", type="string", example="Not answered"),
     *                             @OA\Property(property="correct_answer", type="string", example="her children enjoy listening to her read aloud."),
     *                             @OA\Property(property="is_correct", type="boolean", example=false)
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_six",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=70),
     *                     @OA\Property(property="audio", type="string", nullable=true, example="/storage/audios/part_six_audio_1772507684_69a65224a88a6.mp3"),
     *                     @OA\Property(property="advice", type="string", example="You will hear a part of a lecture."),
     *                     @OA\Property(property="title", type="string", nullable=true, example=null),
     *                     @OA\Property(property="text", type="string", example="The ancient city of Cuzco\r\nThe words open-air (30)[30] are sometimes used to describe the city because of its historic buildings."),
     *                     @OA\Property(
     *                         property="answers",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=390),
     *                             @OA\Property(property="count_number", type="string", example="30"),
     *                             @OA\Property(property="extra_answer", type="string", nullable=true, example=null),
     *                             @OA\Property(property="correct_answer", type="string", example="museum"),
     *                             @OA\Property(property="status", type="string", example="not_answered"),
     *                             @OA\Property(property="user_answer", type="string", nullable=true, example=null),
     *                             @OA\Property(property="is_correct", type="integer", nullable=true, example=null)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="question_data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="user_answer", type="string", example="Not answered"),
     *                             @OA\Property(property="correct_answer", type="string", example="museum"),
     *                             @OA\Property(property="is_correct", type="boolean", example=false)
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="result qaytarildi")
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
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\ListeningResult] 999")
     *         )
     *     )
     * )
     */
    public function result_show(string $id)
    {
        $data=$this->GetListeningResultData($id);

        return $this->success($data,'result qaytarildi');
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
