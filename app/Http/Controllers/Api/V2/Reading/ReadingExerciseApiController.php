<?php

namespace App\Http\Controllers\Api\V2\Reading;

use App\Http\Controllers\Api\V2ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Listening\ListeningResultResource;
use App\Http\Resources\Listenings\ExerciseResource;
use App\Http\Resources\Listenings\ListeningExerciseWithPartResource;
use App\Http\Resources\Reading\ReadingResultResource;
use App\Http\Resources\V2\Reading\ReadingExerciseWithPartResource;
use App\Models\ListeningExercise;
use App\Models\ListeningResult;
use App\Models\PremiumToUsers;
use App\Models\ReadingExercise;
use App\Models\ReadingResult;
use App\Services\PremiumApiService;
use App\Traits\ListeningGetDataTrait;
use App\Traits\ReadingGetDataTrait;
use App\Traits\TestStoreHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadingExerciseApiController extends V2ApiController
{
    use TestStoreHelperTrait,ReadingGetDataTrait;

    /**
     * @OA\Get(
     *     path="/v2/reading_exercise",
     *     operationId="getV2ReadingExercise",
     *     tags={"Reading API"},
     *     summary="Get reading exercise detail",
     *     description="Returns a single reading exercise. Full reading returns part_one to part_five. Premium access is required.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="Reading exercise id",
     *         @OA\Schema(type="integer", example=164)
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
     *             @OA\Property(property="id", type="integer", example=164),
     *             @OA\Property(property="name", type="string", example="Full Mock 1"),
     *             @OA\Property(property="part_category", type="string", example="full"),
     *             @OA\Property(property="part_category_number", type="string", example="6"),
     *             @OA\Property(property="status", type="string", example="0"),
     *             @OA\Property(property="difficulty", type="string", example="0"),
     *             @OA\Property(property="time_limit", type="integer", example=35),
     *             @OA\Property(
     *                 property="result_array",
     *                 type="object",
     *                 nullable=true,
     *                 example={"1":20,"2":24,"3":27,"4":29,"5":32}
     *             ),
     *             @OA\Property(
     *                 property="part_one",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=11),
     *                 @OA\Property(property="advice", type="string", example="Read the text and complete the answers."),
     *                 @OA\Property(property="title", type="string", nullable=true, example=null),
     *                 @OA\Property(property="text", type="string", example="Iron Age\r\nThe ... stronger [1] than ... protect the [2] of the"),
     *                 @OA\Property(property="answers", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=201),
     *                     @OA\Property(property="count_number", type="string", example="1"),
     *                     @OA\Property(property="correct_answer", type="string", example="camera"),
     *                     @OA\Property(property="extra_answer", type="string", nullable=true, example=null)
     *                 ))
     *             ),
     *             @OA\Property(
     *                 property="part_two",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=21),
     *                 @OA\Property(property="advice", type="string", example="Match the texts with the answers."),
     *                 @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                 @OA\Property(property="texts", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=301),
     *                     @OA\Property(property="text", type="string", example="Text A ..."),
     *                     @OA\Property(property="correct_answer_id", type="integer", example=401)
     *                 )),
     *                 @OA\Property(property="answers", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=401),
     *                     @OA\Property(property="letter_key", type="string", example="0"),
     *                     @OA\Property(property="answer", type="string", example="A suitable title")
     *                 ))
     *             ),
     *             @OA\Property(
     *                 property="part_three",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=22),
     *                 @OA\Property(property="advice", type="string", example="Match the texts with the answers."),
     *                 @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                 @OA\Property(property="texts", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=302),
     *                     @OA\Property(property="text", type="string", example="Text B ..."),
     *                     @OA\Property(property="correct_answer_id", type="integer", example=402)
     *                 )),
     *                 @OA\Property(property="answers", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=402),
     *                     @OA\Property(property="letter_key", type="string", example="1"),
     *                     @OA\Property(property="answer", type="string", example="Another title")
     *                 ))
     *             ),
     *             @OA\Property(
     *                 property="part_four",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=41),
     *                 @OA\Property(property="advice", type="string", example="Read the text and answer the questions."),
     *                 @OA\Property(property="text", type="string", example="An article about wildlife ..."),
     *                 @OA\Property(property="TFNg_tests", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=501),
     *                     @OA\Property(property="question", type="string", example="The writer agrees with the statement."),
     *                     @OA\Property(property="answer_1", type="string", example="True"),
     *                     @OA\Property(property="answer_2", type="string", example="False"),
     *                     @OA\Property(property="answer_3", type="string", example="Not Given"),
     *                     @OA\Property(property="correct_answer", type="string", example="1")
     *                 )),
     *                 @OA\Property(property="MCHQ_tests", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=601),
     *                     @OA\Property(property="question", type="string", example="What is the main idea of the passage?"),
     *                     @OA\Property(property="answer_1", type="string", example="Choice A"),
     *                     @OA\Property(property="answer_2", type="string", example="Choice B"),
     *                     @OA\Property(property="answer_3", type="string", example="Choice C"),
     *                     @OA\Property(property="answer_4", type="string", example="Choice D"),
     *                     @OA\Property(property="correct_answer", type="string", example="2")
     *                 ))
     *             ),
     *             @OA\Property(
     *                 property="part_five",
     *                 nullable=true,
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=51),
     *                 @OA\Property(property="advice", type="string", example="Read the passage and answer the questions."),
     *                 @OA\Property(property="reading_text", type="string", example="Iron Age\r\nThe ... stronger [1] than ... protect the [2] of the "),
     *                 @OA\Property(property="question_text", type="string", example="Questions 31-35"),
     *                 @OA\Property(property="answers", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=701),
     *                     @OA\Property(property="count_number", type="string", example="31"),
     *                     @OA\Property(property="correct_answer", type="string", example="museum"),
     *                     @OA\Property(property="extra_answer", type="string", nullable=true, example=null)
     *                 )),
     *                 @OA\Property(property="tests", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=801),
     *                     @OA\Property(property="question", type="string", example="Choose the correct option."),
     *                     @OA\Property(property="answer_1", type="string", example="Option A"),
     *                     @OA\Property(property="answer_2", type="string", example="Option B"),
     *                     @OA\Property(property="answer_3", type="string", example="Option C"),
     *                     @OA\Property(property="answer_4", type="string", example="Option D"),
     *                     @OA\Property(property="correct_answer", type="string", example="3")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Undeviceided")
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
     *             @OA\Property(property="message", type="string", example="Reading exercise not found")
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

        $reading_exercise=ReadingExercise::find($request->id);

        if($reading_exercise->status=='1'){
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

        if ($reading_exercise) {
            return new ReadingExerciseWithPartResource($reading_exercise);
        }else {
            return response()->json(["message" => "Reading exercise not found"], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/v2/reading/next/exercise/{id}",
     *     operationId="getNextReadingExercise",
     *     tags={"Reading API"},
     *     summary="Get next reading exercise",
     *     description="Returns the next reading exercise in the same exercise and part category.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Current Reading Exercise ID",
     *         @OA\Schema(
     *             type="integer",
     *             example=38
     *         )
     *     ),
     *
    @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * type="object",
     *
     * @OA\Property(
     * property="data",
     * type="object",
     *
     * @OA\Property(property="id", type="integer", example=164),
     * @OA\Property(property="name", type="string", example="Full Mock 1"),
     * @OA\Property(property="part_category", type="string", example="full"),
     * @OA\Property(property="part_category_number", type="string", example="6"),
     * @OA\Property(property="status", type="string", example="0"),
     * @OA\Property(property="difficulty", type="string", example="0"),
     * @OA\Property(property="time_limit", type="integer", example=35),
     * @OA\Property(
     * property="result_array",
     * type="object",
     * nullable=true,
     * example={"1":20,"2":24,"3":27,"4":29,"5":32}
     * ),
     *
     * @OA\Property(
     * property="part_one",
     * nullable=true,
     * type="object",
     * @OA\Property(property="id", type="integer", example=11),
     * @OA\Property(property="advice", type="string", example="Read the text and complete the answers."),
     * @OA\Property(property="title", type="string", nullable=true, example=null),
     * @OA\Property(property="text", type="string", example="Iron Age..."),
     * @OA\Property(
     * property="answers",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="id", type="integer", example=201),
     * @OA\Property(property="count_number", type="string", example="1"),
     * @OA\Property(property="correct_answer", type="string", example="camera"),
     * @OA\Property(property="extra_answer", type="string", nullable=true, example=null)
     * )
     * )
     * ),
     *
     * @OA\Property(
     * property="part_two",
     * nullable=true,
     * type="object",
     * @OA\Property(property="id", type="integer", example=21),
     * @OA\Property(property="advice", type="string", example="Match the texts with the answers."),
     * @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     * @OA\Property(
     * property="texts",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="id", type="integer", example=301),
     * @OA\Property(property="text", type="string", example="Text A ..."),
     * @OA\Property(property="correct_answer_id", type="integer", example=401)
     * )
     * ),
     * @OA\Property(
     * property="answers",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="id", type="integer", example=401),
     * @OA\Property(property="letter_key", type="string", example="0"),
     * @OA\Property(property="answer", type="string", example="A suitable title")
     * )
     * )
     * ),
     *
     * @OA\Property(
     * property="part_three",
     * nullable=true,
     * type="object",
     * @OA\Property(property="id", type="integer", example=22),
     * @OA\Property(property="advice", type="string", example="Match the texts with the answers."),
     * @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     * @OA\Property(
     * property="texts",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="id", type="integer", example=302),
     * @OA\Property(property="text", type="string", example="Text B ..."),
     * @OA\Property(property="correct_answer_id", type="integer", example=402)
     * )
     * ),
     * @OA\Property(
     * property="answers",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="id", type="integer", example=402),
     * @OA\Property(property="letter_key", type="string", example="1"),
     * @OA\Property(property="answer", type="string", example="Another title")
     * )
     * )
     * ),
     *
     * @OA\Property(
     * property="part_four",
     * nullable=true,
     * type="object",
     * @OA\Property(property="id", type="integer", example=41),
     * @OA\Property(property="advice", type="string", example="Read the text and answer the questions."),
     * @OA\Property(property="text", type="string", example="An article about wildlife ..."),
     * @OA\Property(
     * property="TFNg_tests",
     * type="array",
     * @OA\Items(type="object")
     * ),
     * @OA\Property(
     * property="MCHQ_tests",
     * type="array",
     * @OA\Items(type="object")
     * )
     * ),
     *
     * @OA\Property(
     * property="part_five",
     * nullable=true,
     * type="object",
     * @OA\Property(property="id", type="integer", example=51),
     * @OA\Property(property="advice", type="string", example="Read the passage and answer the questions."),
     * @OA\Property(property="reading_text", type="string", example="Iron Age..."),
     * @OA\Property(property="question_text", type="string", example="Questions 31-35"),
     * @OA\Property(
     * property="answers",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="id", type="integer", example=701),
     * @OA\Property(property="count_number", type="string", example="31"),
     * @OA\Property(property="correct_answer", type="string", example="museum"),
     * @OA\Property(property="extra_answer", type="string", nullable=true, example=null)
     * )
     * ),
     * @OA\Property(
     * property="tests",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="id", type="integer", example=801),
     * @OA\Property(property="question", type="string", example="Choose the correct option."),
     * @OA\Property(property="answer_1", type="string", example="Option A"),
     * @OA\Property(property="answer_2", type="string", example="Option B"),
     * @OA\Property(property="answer_3", type="string", example="Option C"),
     * @OA\Property(property="answer_4", type="string", example="Option D"),
     * @OA\Property(property="correct_answer", type="string", example="3")
     * )
     * )
     * )
     * ),
     *
     * @OA\Property(
     * property="message",
     * type="string",
     * example="exercise next"
     * )
     * )
     * ),
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
        $readingExercise=ReadingExercise::with('exercise')->find($id);

        if (!$readingExercise->order_id){
            return $this->error(['seccess'=>false],'exercise not found');
        }
        $next_reading_Exercise=$readingExercise->order_id ? ReadingExercise::where('exercise_id',$readingExercise->exercise_id)->where('part_category',$readingExercise->part_category)->where('order_id','>',$readingExercise->order_id)->first() : null;

        if ($next_reading_Exercise){

            return $this->success(new ReadingExerciseWithPartResource($next_reading_Exercise) ,'exercise next');

        }else{

            return $this->error(['seccess'=>false],'exercise not found');
        }
    }
    /**
     * @OA\Post(
     *     path="/v2/reading/save",
     *     operationId="postV2ReadingSave",
     *     tags={"Reading API"},
     *     summary="Save reading result",
     *     description="Send result data for available reading parts.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reading_exercise_id","all_answers","correct_answers","wrong_answers","parts_data"},
     *             @OA\Property(property="reading_exercise_id", type="integer", example=164),
     *             @OA\Property(property="all_answers", type="integer", example=35),
     *             @OA\Property(property="correct_answers", type="integer", example=20),
     *             @OA\Property(property="wrong_answers", type="integer", example=15),
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
     *                         required={"id","is_correct","answer","correct_answer"},
     *                         @OA\Property(property="id", type="integer", example=201),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer", type="string", example="camera"),
     *                         @OA\Property(property="correct_answer", type="string", example="camera")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_two",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         required={"id","is_correct","answer","correct_answer","correct_answer_id"},
     *                         @OA\Property(property="id", type="integer", example=301),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer", type="integer", example=401),
     *                         @OA\Property(property="correct_answer", type="string", example="A suitable title"),
     *                         @OA\Property(property="correct_answer_id", type="integer", example=401)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_three",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         required={"id","is_correct","answer","correct_answer","correct_answer_id"},
     *                         @OA\Property(property="id", type="integer", example=302),
     *                         @OA\Property(property="is_correct", type="integer", example=0),
     *                         @OA\Property(property="answer", type="integer", example=402),
     *                         @OA\Property(property="correct_answer", type="string", example="Another title"),
     *                         @OA\Property(property="correct_answer_id", type="integer", example=402)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_four",
     *                     type="array",
     *                     nullable=true,
     *                     @OA\Items(
     *                         type="object",
     *                         required={"id","is_correct","answer","correct_answer_key","correct_answer"},
     *                         @OA\Property(property="id", type="integer", example=501),
     *                         @OA\Property(property="is_correct", type="integer", example=1),
     *                         @OA\Property(property="answer", type="string", example="answer_1"),
     *                         @OA\Property(property="correct_answer_key", type="string", example="answer_1"),
     *                         @OA\Property(property="correct_answer", type="string", example="True")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_five",
     *                     type="array",
     *                     nullable=true,
     *                     description="Two item types are allowed here: input and test.",
     *                     example={
     *                         {"id":701,"type":"input","is_correct":0,"answer":"projector","correct_answer":"projectors"},
     *                         {"id":701,"type":"test","is_correct":0,"answer":"answer_1","correct_answer":"C) from production to distribution and projection","correct_answer_key":"answer_3"}
     *                     },
     *                     @OA\Items(
     *                         type="object",
     *                         required={"id","type","is_correct","answer","correct_answer"},
     *                         @OA\Property(property="id", type="integer", example=701),
     *                         @OA\Property(property="type", type="string", example="input"),
     *                         @OA\Property(property="is_correct", type="integer", example=0),
     *                         @OA\Property(property="answer", type="string", example="projector"),
     *                         @OA\Property(property="correct_answer", type="string", example="projectors"),
     *                         @OA\Property(property="correct_answer_key", type="string", nullable=true, example="answer_3")
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
     *             @OA\Property(property="message", type="string", example="saqlandi"),
     *             @OA\Property(property="readingResultId", type="integer", example=123),
     *             @OA\Property(property="next_exercise_id", type="integer", example=118,nullable=true),
     *             @OA\Property(property="parts_data", type="object", description="Validated parts data that was saved.")
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
     *                 @OA\Property(property="reading_exercise_id", type="array", @OA\Items(type="string", example="Exercise ID is required.")),
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
            'reading_exercise_id' => 'required|exists:reading_exercises,id',
            'all_answers' => 'required',
            'correct_answers' => 'required',
            'wrong_answers' => 'required',
            'parts_data' => 'required|array',
        ], [
            'reading_exercise_id.required' => 'Exercise ID is required.',
            'reading_exercise_id.exists' => 'The exercise does not exist.',
            'all_answers.required' => 'all_answers is required.',
            'correct_answers.required' => 'correct_answers is required.',
        ]);




        $percent= $request->all_answers > 0 ? (($request->correct_answers*(100))/$request->all_answers) : 0;

        $reading_exercise=ReadingExercise::find($request->reading_exercise_id);

        $result=ReadingResult::create([
            'reading_exercise_id'=>$request->reading_exercise_id,
            'category_type'=>$reading_exercise->part_category ?? null,
            'all_answers'=>$request->all_answers,
            'correct_answers'=>$request->correct_answers,
            'wrong_answers'=>$request->wrong_answers,
            'user_id'=>Auth::id(),
            'percent'=>$percent,
        ]);


        $validatedData=$this->createReadingResultParts($result,$request->parts_data);
        $next_reading_Exercise=$reading_exercise->order_id ? ReadingExercise::where('exercise_id',$reading_exercise->exercise_id)->where('part_category',$reading_exercise->part_category)->where('order_id','>',$reading_exercise->order_id)->first() : null;

        if($next_reading_Exercise && $next_reading_Exercise->status){
            $existingPlan = PremiumToUsers::where('user_id', auth('sanctum')->user()->id)
                ->where(function ($query) {
                    $query->where('start_time', '<=', now())
                        ->where('end_time', '>=', now());
                })
                ->orderByDesc('id')
                ->first();

            if (!$existingPlan) {
                $next_reading_Exercise=null;
            }
        }

        return response()->json([
            'message'=>'saqlandi',
            'readingResultId'=>$result->id,
            'next_exercise_id'=>$next_reading_Exercise? $next_reading_Exercise->id: null,
            'parts_data'=>$validatedData
            ],202);
    }

    /**
     * @OA\Get(
     *     path="/v2/reading/results",
     *     operationId="getV2ReadingResults",
     *     tags={"Reading API"},
     *     summary="Get reading results",
     *     description="Returns the authenticated user's reading results (paginated, 10 per page).",
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
     *                     @OA\Property(property="id", type="integer", example=154),
     *                     @OA\Property(property="all_answers", type="integer", example=35),
     *                     @OA\Property(property="wrong_answers", type="integer", example=4),
     *                     @OA\Property(property="correct_answers", type="integer", example=2),
     *                     @OA\Property(property="percentage", type="integer", example=6),
     *                     @OA\Property(property="category", type="string", nullable=true, example="full"),
     *                     @OA\Property(property="reading_exercise_id", type="integer", example=48),
     *                     @OA\Property(property="createdDate", type="string", example="26 iyun, 09:10")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/reading/results?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/reading/results?page=3"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/reading/results?page=2")
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
     *                         @OA\Property(property="url", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/reading/results?page=2"),
     *                         @OA\Property(property="label", type="string", example="2"),
     *                         @OA\Property(property="active", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/reading/results"),
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

        $listening_results=ReadingResult::where('user_id',Auth::id())
            ->orderByDesc('id')
            ->whereDoesntHave('full_results')
            ->whereHas('exercise')
            ->paginate(10);


        return ReadingResultResource::collection($listening_results);
    }


    /**
     * @OA\Get(
     *     path="/v2/reading/result/show/{id}",
     *     operationId="getV2ReadingResultShow",
     *     tags={"Reading API"},
     *     summary="Get reading result detail",
     *     description="Returns full reading result detail with all available parts.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=154)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=154),
     *                 @OA\Property(property="reading_exercise_id", type="integer", example=48),
     *                 @OA\Property(property="name", type="string", example="Reading Mock 1"),
     *                 @OA\Property(property="status", type="string", example="0"),
     *                 @OA\Property(property="active", type="string", nullable=true, example="part_one"),
     *                 @OA\Property(property="createdText", type="string", example="4 март, 08:16"),
     *                 @OA\Property(property="type", type="integer", example=1),
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
     *                 @OA\Property(property="part_category", type="string", nullable=true, example="full"),
     *                 @OA\Property(property="part_category_number", type="string", example="6"),
     *                 @OA\Property(property="difficulty", type="string", example="0"),
     *                 @OA\Property(property="time_limit", type="integer", example=60),
     *                 @OA\Property(property="all_answers", type="integer", example=35),
     *                 @OA\Property(property="correct_answers", type="integer", example=2),
     *                 @OA\Property(property="wrong_answers", type="integer", example=4),
     *                 @OA\Property(property="feedback", type="string", example="Your score is very low. You need to improve your basics."),
     *                 @OA\Property(property="suggestion", type="string", example="Focus on simple English listening and reading exercises daily"),
     *                 @OA\Property(
     *                     property="result",
     *                     type="object",
     *                     @OA\Property(property="reading_result_id", type="integer", example=154),
     *                     @OA\Property(property="all_answers", type="integer", example=35),
     *                     @OA\Property(property="correct_answers", type="integer", example=2),
     *                     @OA\Property(property="percent", type="integer", example=6),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-05-29T08:16:00.000000Z")
     *                 ),
     *                 @OA\Property(
     *                     property="parts_data",
     *                     type="object",
     *                     @OA\Property(property="part_one", type="string", example="Part One"),
     *                     @OA\Property(property="part_two", type="string", example="Part Two"),
     *                     @OA\Property(property="part_three", type="string", example="Part Three"),
     *                     @OA\Property(property="part_four", type="string", example="Part Four"),
     *                     @OA\Property(property="part_five", type="string", example="Part Five")
     *                 ),
     *                 @OA\Property(
     *                     property="part_one",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=14),
     *                     @OA\Property(property="advice", type="string", example="Read the text and fill in the missing information."),
     *                     @OA\Property(property="title", type="string", nullable=true, example=null),
     *                     @OA\Property(property="text", type="string", example="The text with numbered gaps."),
     *                     @OA\Property(
     *                         property="answers",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=701),
     *                             @OA\Property(property="count_number", type="string", example="1"),
     *                             @OA\Property(property="extra_answer", type="string", nullable=true, example=null),
     *                             @OA\Property(property="correct_answer", type="string", example="projectors"),
     *                             @OA\Property(property="status", type="string", example="answered"),
     *                             @OA\Property(property="user_answer", type="string", nullable=true, example="projector"),
     *                             @OA\Property(property="is_correct", type="integer", nullable=true, example=0)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="question_data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="user_answer", type="string", example="projector"),
     *                             @OA\Property(property="correct_answer", type="string", example="projectors"),
     *                             @OA\Property(property="is_correct", type="boolean", example=false)
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_two",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=21),
     *                     @OA\Property(property="advice", type="string", example="Match the texts with the correct answers."),
     *                     @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                     @OA\Property(
     *                         property="texts",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=58),
     *                             @OA\Property(property="text", type="string", example="Paragraph text"),
     *                             @OA\Property(property="correct_answer_id", type="integer", example=79)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="answers",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=79),
     *                             @OA\Property(property="letter_key", type="string", example="A"),
     *                             @OA\Property(property="answer", type="string", example="A possible solution to falling numbers"),
     *                             @OA\Property(property="is_selected", type="boolean", example=true)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="question_data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="user_answer", type="string", example="A possible solution to falling numbers"),
     *                             @OA\Property(property="correct_answer", type="string", example="A possible solution to falling numbers"),
     *                             @OA\Property(property="is_correct", type="boolean", example=true)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="result_rows",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="text_id", type="integer", nullable=true, example=58),
     *                             @OA\Property(property="answer_id", type="integer", nullable=true, example=79),
     *                             @OA\Property(property="correct_answer_id", type="integer", nullable=true, example=79),
     *                             @OA\Property(property="correct_answer", type="string", nullable=true, example="A possible solution to falling numbers"),
     *                             @OA\Property(property="is_correct", type="integer", nullable=true, example=1),
     *                             @OA\Property(property="status", type="string", example="answered")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_three",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=22),
     *                     @OA\Property(property="advice", type="string", example="Match the texts with the correct answers."),
     *                     @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                     @OA\Property(
     *                         property="texts",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=59),
     *                             @OA\Property(property="text", type="string", example="Another paragraph text"),
     *                             @OA\Property(property="correct_answer_id", type="integer", example=83)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="answers",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=83),
     *                             @OA\Property(property="letter_key", type="string", example="I"),
     *                             @OA\Property(property="answer", type="string", example="I) Useful physical features"),
     *                             @OA\Property(property="is_selected", type="boolean", example=false)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="question_data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="user_answer", type="string", example="A possible solution to falling numbers"),
     *                             @OA\Property(property="correct_answer", type="string", example="I) Useful physical features"),
     *                             @OA\Property(property="is_correct", type="boolean", example=false)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="result_rows",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="text_id", type="integer", nullable=true, example=59),
     *                             @OA\Property(property="answer_id", type="integer", nullable=true, example=79),
     *                             @OA\Property(property="correct_answer_id", type="integer", nullable=true, example=83),
     *                             @OA\Property(property="correct_answer", type="string", nullable=true, example="I) Useful physical features"),
     *                             @OA\Property(property="is_correct", type="integer", nullable=true, example=0),
     *                             @OA\Property(property="status", type="string", example="answered")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_four",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=12),
     *                     @OA\Property(property="advice", type="string", example="Read the text and answer the questions."),
     *                     @OA\Property(property="text", type="string", example="Reading passage text"),
     *                     @OA\Property(
     *                         property="TFNg_tests",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=44),
     *                             @OA\Property(property="question", type="string", example="Statement 1"),
     *                             @OA\Property(property="answer_1", type="string", example="True"),
     *                             @OA\Property(property="answer_2", type="string", example="False"),
     *                             @OA\Property(property="answer_3", type="string", example="Not Given"),
     *                             @OA\Property(property="correct_answer_key", type="string", example="answer_3"),
     *                             @OA\Property(property="status", type="string", example="answered"),
     *                             @OA\Property(property="user_answer_key", type="string", nullable=true, example="answer_1"),
     *                             @OA\Property(property="correct_answer", type="string", nullable=true, example="Not Given"),
     *                             @OA\Property(property="is_correct", type="integer", nullable=true, example=0)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="MCHQ_tests",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=52),
     *                             @OA\Property(property="question", type="string", example="Which option is correct?"),
     *                             @OA\Property(property="answer_1", type="string", example="A"),
     *                             @OA\Property(property="answer_2", type="string", example="B"),
     *                             @OA\Property(property="answer_3", type="string", example="C"),
     *                             @OA\Property(property="answer_4", type="string", example="D"),
     *                             @OA\Property(property="correct_answer_key", type="string", example="answer_2"),
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
     *                             @OA\Property(property="user_answer", type="string", example="True"),
     *                             @OA\Property(property="correct_answer", type="string", example="Not Given"),
     *                             @OA\Property(property="is_correct", type="boolean", example=false)
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="part_five",
     *                     nullable=true,
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=15),
     *                     @OA\Property(property="advice", type="string", example="Read the text and answer both input and test questions."),
     *                     @OA\Property(property="reading_text", type="string", example="Long reading text"),
     *                     @OA\Property(property="question_text", type="string", example="Questions 30-35"),
     *                     @OA\Property(
     *                         property="answers",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=701),
     *                             @OA\Property(property="count_number", type="string", example="30"),
     *                             @OA\Property(property="extra_answer", type="string", nullable=true, example=null),
     *                             @OA\Property(property="correct_answer", type="string", example="projectors"),
     *                             @OA\Property(property="status", type="string", example="answered"),
     *                             @OA\Property(property="user_answer", type="string", nullable=true, example="projector"),
     *                             @OA\Property(property="is_correct", type="integer", nullable=true, example=0)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="tests",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=801),
     *                             @OA\Property(property="question", type="string", example="What is the correct answer?"),
     *                             @OA\Property(property="answer_1", type="string", example="A) option"),
     *                             @OA\Property(property="answer_2", type="string", example="B) option"),
     *                             @OA\Property(property="answer_3", type="string", example="C) from production to distribution and projection"),
     *                             @OA\Property(property="answer_4", type="string", nullable=true, example=null),
     *                             @OA\Property(property="correct_answer_key", type="string", example="answer_3"),
     *                             @OA\Property(property="correct_answer", type="string", example="3"),
     *                             @OA\Property(property="status", type="string", example="answered"),
     *                             @OA\Property(property="user_answer_key", type="string", nullable=true, example="answer_1"),
     *                             @OA\Property(property="is_correct", type="integer", nullable=true, example=0)
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="question_data",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="user_answer", type="string", example="projector"),
     *                             @OA\Property(property="correct_answer", type="string", example="projectors"),
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
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\ReadingResult] 999")
     *         )
     *     )
     * )
     */
    public function result_show($id)
    {
        $data=$this->GetReadingResultData($id);

        return $this->success($data,'result qaytarildi');
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
