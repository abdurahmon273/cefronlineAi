<?php

namespace App\Http\Controllers\Api\V2\FullExam;

use App\Enums\CefrTypeEnum;
use App\Http\Controllers\Api\V2ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Full\FullExerciseWithPartsResource;
use App\Http\Resources\V2\Full\FullResultListResource;
use App\Http\Resources\Reading\ReadingExerciseWithPartResource;
use App\Jobs\SpeakingChangeAudioFormat;
use App\Models\FullExercise;
use App\Models\FullResult;
use App\Models\ListeningExercise;
use App\Models\ListeningResult;
use App\Models\PremiumToUsers;
use App\Models\ReadingExercise;
use App\Models\ReadingResult;
use App\Models\SpeakingExercise;
use App\Models\SpeakingPartOneFirstAnswer;
use App\Models\SpeakingPartOneFirstResult;
use App\Models\SpeakingPartOneSecondAnswer;
use App\Models\SpeakingPartOneSecondResult;
use App\Models\SpeakingPartThreeResult;
use App\Models\SpeakingPartTwoResult;
use App\Models\SpeakingResult;
use App\Models\WritingExercise;
use App\Models\WritingPartOneAnswer;
use App\Models\WritingPartTwoAnswer;
use App\Models\WritingResult;
use App\Services\FullMockService;
use App\Services\PremiumApiService;
use App\Services\SpeakingStoreService;
use App\Services\WritingStoreService;
use App\Traits\BillingTrait;
use App\Traits\ListeningGetDataTrait;
use App\Traits\ReadingGetDataTrait;
use App\Traits\TestStoreHelperTrait;
use App\Traits\WritingGetDataTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FullExerciseApiController extends V2ApiController
{
    use TestStoreHelperTrait,ListeningGetDataTrait,ReadingGetDataTrait,WritingGetDataTrait,BillingTrait;

    /**
     * @OA\Get(
     *     path="/v2/full/exercises",
     *     operationId="getV2FullExercises",
     *     tags={"Full Exam API"},
     *     summary="Get full mock exam exercise list",
     *     description="Returns all full mock exam groups with their exercises. No authentication required. (Implemented in App\Http\Controllers\Api\V2\Home\ExerciseApiController::full_index.)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="device_id",
     *         in="query",
     *         required=false,
     *         description="Device ID for device verification (optional, checked only when user is authenticated)",
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
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Full Mock Tests"),
     *                     @OA\Property(property="exercise_count", type="integer", example=3),
     *                     @OA\Property(
     *                         property="full_exercises",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=10),
     *                             @OA\Property(property="title", type="string", example="Full Exam #1"),
     *                             @OA\Property(
     *                                 property="status",
     *                                 type="string",
     *                                 example="1",
     *                                 description="0 = free, 1 = premium"
     *                             ),
     *                             @OA\Property(
     *                                 property="time_limit",
     *                                 type="integer",
     *                                 example=175,
     *                                 description="Total time in minutes (listening=35, reading=70, writing=60, speaking=10)"
     *                             )
     *                         )
     *                     )
     *                 )
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
     *
     * @OA\Get(
     *     path="/v2/full_exercise",
     *     operationId="getV2FullExerciseById",
     *     tags={"Full Exam API"},
     *     summary="Get a single full mock exam with all parts",
     *     description="Returns a full mock exam including listening, reading, writing, and speaking exercises. If the exam is premium (status=1), an active premium plan is required.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID of the full exercise",
     *         @OA\Schema(type="integer", example=17)
     *     ),
     *     @OA\Parameter(
     *         name="device_id",
     *         in="query",
     *         required=false,
     *         description="Device ID for device verification (optional, checked only when user is authenticated)",
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
     *                 @OA\Property(property="id", type="integer", example=17),
     *                 @OA\Property(property="title", type="string", example="Full Mock 1"),
     *                 @OA\Property(property="status", type="string", example="0", description="0 = free, 1 = premium"),
     *                 @OA\Property(property="time_limit", type="integer", example=175, description="Total minutes: listening=35, reading=70, writing=60, speaking=10"),
     *
     *                 @OA\Property(
     *                     property="resultData",
     *                     type="object",
     *                     description="Current result state of the user for this full exercise (same shape returned by Full/save and Full/reset).",
     *                     @OA\Property(property="id", type="integer", example=17, description="Full exercise id"),
     *                     @OA\Property(property="reset_status", type="boolean", example=true, description="true if the user has an unfinished result (status=0) and a Reset button should be shown; false otherwise"),
     *                     @OA\Property(
     *                         property="lastResult",
     *                         type="object",
     *                         nullable=true,
     *                         description="Most recent full result of the user for this exercise, or null if none.",
     *                         @OA\Property(property="id", type="integer", example=66),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2026-07-18T13:30:13.000000Z"),
     *                         @OA\Property(property="status", type="integer", enum={0,1}, example=0, description="0 = unfinished, 1 = finished")
     *                     ),
     *                     @OA\Property(
     *                         property="lastResults",
     *                         type="array",
     *                         nullable=true,
     *                         description="Up to 3 previous full results (excluding lastResult), newest first. null when there are no previous results.",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=65),
     *                             @OA\Property(property="status", type="integer", enum={0,1}, example=1),
     *                             @OA\Property(property="created_at", type="string", format="date-time", example="2026-07-18T13:18:31.000000Z")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="exercise_data",
     *                         type="object",
     *                         description="Per-skill availability for the current attempt. true = skill still available to take (not yet saved in the unfinished result); false = already saved. When there is no unfinished result, all skills default to true.",
     *                         @OA\Property(property="listening", type="boolean", example=true),
     *                         @OA\Property(property="reading", type="boolean", example=true),
     *                         @OA\Property(property="writing", type="boolean", example=true),
     *                         @OA\Property(property="speaking", type="boolean", example=true)
     *                     )
     *                 ),
     *
     *                 @OA\Property(
     *                     property="listening_exercise",
     *                     nullable=true,
     *                     type="object",
     *                     description="null if listening part is missing or has no tests",
     *                     @OA\Property(property="id", type="integer", example=164),
     *                     @OA\Property(property="name", type="string", example="Full Mock 1"),
     *                     @OA\Property(property="part_category", type="string", example="full"),
     *                     @OA\Property(property="part_category_number", type="string", example="7"),
     *                     @OA\Property(property="status", type="string", example="0"),
     *                     @OA\Property(property="difficulty", type="string", example="0"),
     *                     @OA\Property(property="time_limit", type="integer", example=35),
     *                     @OA\Property(
     *                         property="result_array",
     *                         type="array",
     *                         description="Score thresholds for each band level",
     *                         @OA\Items(type="integer", example=23)
     *                     ),
     *                     @OA\Property(
     *                         property="part_one",
     *                         type="object",
     *                         description="Multiple choice (A/B/C) — 3-choice questions",
     *                         @OA\Property(property="id", type="integer", example=72),
     *                         @OA\Property(property="audio", type="string", example="http://127.0.0.1:8000/storage/audios/part_one_audio.mp3"),
     *                         @OA\Property(property="advice", type="string", example="You will hear some sentences. Choose the correct reply (A, B, or C)."),
     *                         @OA\Property(
     *                             property="tests",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=428),
     *                                 @OA\Property(property="answer_1", type="string", example="That's too bad."),
     *                                 @OA\Property(property="answer_2", type="string", example="No, I'm not."),
     *                                 @OA\Property(property="answer_3", type="string", example="I think it is."),
     *                                 @OA\Property(property="correct_answer", type="integer", example=3)
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_two",
     *                         type="object",
     *                         description="Fill in the blank — write one word or number",
     *                         @OA\Property(property="id", type="integer", example=69),
     *                         @OA\Property(property="audio", type="string", example="http://127.0.0.1:8000/storage/audios/part_two_audio.mp3"),
     *                         @OA\Property(property="advice", type="string", example="Fill in the missing information. Write ONE WORD and/or A NUMBER."),
     *                         @OA\Property(property="text", type="string", example="School trip to farm\nMeeting place: 8 a.m. next to the (9)[9]"),
     *                         @OA\Property(
     *                             property="tests",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=384),
     *                                 @OA\Property(property="count_number", type="string", example="9"),
     *                                 @OA\Property(property="correct_answer", type="string", example="gate"),
     *                                 @OA\Property(property="extra_answer", type="string", nullable=true, example=null)
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_three",
     *                         type="object",
     *                         description="Matching — speakers to answers",
     *                         @OA\Property(property="id", type="integer", example=87),
     *                         @OA\Property(property="audio", type="string", example="http://127.0.0.1:8000/storage/audios/part_three_audio.mp3"),
     *                         @OA\Property(property="advice", type="string", example="Match each speaker to the place (A-F). TWO options are extra."),
     *                         @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                         @OA\Property(
     *                             property="speakers",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=374),
     *                                 @OA\Property(property="title", type="string", example="15. Speaker 1"),
     *                                 @OA\Property(property="correct_answer_id", type="integer", example=585)
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="answers",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=585),
     *                                 @OA\Property(property="letter_key", type="string", example="0"),
     *                                 @OA\Property(property="answer", type="string", example="A) Why people go on excursion.")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_four",
     *                         type="object",
     *                         description="Map labelling — match locations on a map",
     *                         @OA\Property(property="id", type="integer", example=88),
     *                         @OA\Property(property="audio", type="string", example="http://127.0.0.1:8000/storage/audios/part_four_audio.mp3"),
     *                         @OA\Property(property="image", type="string", example="http://127.0.0.1:8000/storage/maps/image.jpg"),
     *                         @OA\Property(property="advice", type="string", example="Label the places (19-23) on the map (A-F). FOUR options are extra."),
     *                         @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                         @OA\Property(
     *                             property="questions",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=378),
     *                                 @OA\Property(property="title", type="string", example="19. The Reynolds House"),
     *                                 @OA\Property(property="correct_answer_id", type="integer", example=598)
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="answers",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=591),
     *                                 @OA\Property(property="letter_key", type="string", example="0"),
     *                                 @OA\Property(property="answer", type="string", example=" ")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_five",
     *                         type="object",
     *                         description="Multiple choice (A/B/C) from extracts",
     *                         @OA\Property(property="id", type="integer", example=73),
     *                         @OA\Property(property="audio", type="string", example="http://127.0.0.1:8000/storage/audios/part_five_audio.mp3"),
     *                         @OA\Property(property="advice", type="string", example="Choose the correct answer (A, B or C) for each question (24-29)."),
     *                         @OA\Property(
     *                             property="tests",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=436),
     *                                 @OA\Property(property="question", type="string", example="Extract One\n24. The second speaker says that she believes that"),
     *                                 @OA\Property(property="answer_1", type="string", example="her children enjoy listening to her read aloud."),
     *                                 @OA\Property(property="answer_2", type="string", example="she shares a reading habit with other parents."),
     *                                 @OA\Property(property="answer_3", type="string", example="parents should read aloud to children."),
     *                                 @OA\Property(property="correct_answer", type="string", example="1")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_six",
     *                         type="object",
     *                         description="Fill in the blank from a lecture — write one word",
     *                         @OA\Property(property="id", type="integer", example=70),
     *                         @OA\Property(property="audio", type="string", example="http://127.0.0.1:8000/storage/audios/part_six_audio.mp3"),
     *                         @OA\Property(property="advice", type="string", example="Fill in the missing information. Write no more than ONE WORD for each answer."),
     *                         @OA\Property(property="text", type="string", example="The ancient city of Cuzco\nThe words open-air (30)[30] are sometimes used..."),
     *                         @OA\Property(
     *                             property="tests",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=390),
     *                                 @OA\Property(property="count_number", type="string", example="30"),
     *                                 @OA\Property(property="correct_answer", type="string", example="museum"),
     *                                 @OA\Property(property="extra_answer", type="string", nullable=true, example=null)
     *                             )
     *                         )
     *                     )
     *                 ),
     *
     *                 @OA\Property(
     *                     property="reading_exercise",
     *                     nullable=true,
     *                     type="object",
     *                     description="null if reading part is missing or has no answers",
     *                     @OA\Property(property="id", type="integer", example=91),
     *                     @OA\Property(property="name", type="string", example="Full Mock 1"),
     *                     @OA\Property(property="status", type="string", example="0"),
     *                     @OA\Property(property="part_category", type="string", example="full"),
     *                     @OA\Property(property="part_category_number", type="string", example="6"),
     *                     @OA\Property(property="difficulty", type="string", example="0"),
     *                     @OA\Property(property="time_limit", type="integer", example=60),
     *                     @OA\Property(property="result_array", type="array", @OA\Items(type="integer", example=20)),
     *                     @OA\Property(
     *                         property="part_one",
     *                         type="object",
     *                         description="Fill in the blank — use a word from the text",
     *                         @OA\Property(property="id", type="integer", example=48),
     *                         @OA\Property(property="advice", type="string", example="Fill in each gap with ONE word from the rest of the text."),
     *                         @OA\Property(property="title", type="string", nullable=true, example=null),
     *                         @OA\Property(property="text", type="string", example="Black Friday in the United States\nBlack Friday is the day after Thanksgiving..."),
     *                         @OA\Property(
     *                             property="answers",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=178),
     *                                 @OA\Property(property="count_number", type="string", example="1"),
     *                                 @OA\Property(property="correct_answer", type="string", example="holiday"),
     *                                 @OA\Property(property="extra_answer", type="string", nullable=true, example=null)
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_two",
     *                         type="object",
     *                         description="Matching texts to statements (A-J), TWO extras unused",
     *                         @OA\Property(property="id", type="integer", example=69),
     *                         @OA\Property(property="advice", type="string", example="Match each text to the statement. Each statement used ONCE only."),
     *                         @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                         @OA\Property(
     *                             property="texts",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=364),
     *                                 @OA\Property(property="text", type="string", example="7. HELP – snack bar serving person\nBright, friendly, experience not essential..."),
     *                                 @OA\Property(property="correct_answer_id", type="integer", example=436)
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="answers",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=434),
     *                                 @OA\Property(property="letter_key", type="string", example="0"),
     *                                 @OA\Property(property="answer", type="string", example="A) a person with two small children who wants a few hours of unskilled work in the early mornings")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_three",
     *                         type="object",
     *                         description="Matching paragraph headings to passages",
     *                         @OA\Property(property="id", type="integer", example=70),
     *                         @OA\Property(property="advice", type="string", example="Choose the correct heading for each paragraph. More headings than paragraphs."),
     *                         @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                         @OA\Property(
     *                             property="texts",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=372),
     *                                 @OA\Property(property="text", type="string", example="Plastic is no longer fantastic\nI In 2017, Carlos Ferrando saw a piece of art..."),
     *                                 @OA\Property(property="correct_answer_id", type="integer", example=449)
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="answers",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=444),
     *                                 @OA\Property(property="letter_key", type="string", example="0"),
     *                                 @OA\Property(property="answer", type="string", example="A) A time when opportunities were limited")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_four",
     *                         type="object",
     *                         description="True/False/Not Given + Multiple choice from a long text",
     *                         @OA\Property(property="id", type="integer", example=22),
     *                         @OA\Property(property="advice", type="string", example="Read the following text for questions"),
     *                         @OA\Property(property="text", type="string", example="Cultural behavior in business\nMuch of today's business is conducted across international borders..."),
     *                         @OA\Property(
     *                             property="TFNg_tests",
     *                             type="array",
     *                             description="True / False / Not Given questions",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=124),
     *                                 @OA\Property(property="question", type="string", example="21. When doing business internationally, there is a possibility that we might misinterpret each other even speaking the same language."),
     *                                 @OA\Property(property="answer_1", type="string", example="True"),
     *                                 @OA\Property(property="answer_2", type="string", example="False"),
     *                                 @OA\Property(property="answer_3", type="string", example="Not Given"),
     *                                 @OA\Property(property="correct_answer", type="string", example="1")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="MCHQ_tests",
     *                             type="array",
     *                             description="Multiple choice (4 options) questions",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=129),
     *                                 @OA\Property(property="question", type="string", example="26. There might be a misunderstanding if an American smiles at a Russian because the Russian might think that the American is ..."),
     *                                 @OA\Property(property="answer_1", type="string", example="being fake"),
     *                                 @OA\Property(property="answer_2", type="string", example="challenge their authority"),
     *                                 @OA\Property(property="answer_3", type="string", example="trying to break the ice"),
     *                                 @OA\Property(property="answer_4", type="string", example="disrespectful"),
     *                                 @OA\Property(property="correct_answer", type="string", example="1")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_five",
     *                         type="object",
     *                         description="Fill in blank + Multiple choice from a biography text",
     *                         @OA\Property(property="id", type="integer", example=49),
     *                         @OA\Property(property="advice", type="string", example="Read the following text for questions 30-35"),
     *                         @OA\Property(property="reading_text", type="string", example="A biography of Kilian Jornet\nWhen you picture mountain climbers scaling Mount Everest..."),
     *                         @OA\Property(property="question_text", type="string", example="30. It's normal to find it hard to breathe at [30] meters above sea level."),
     *                         @OA\Property(
     *                             property="answers",
     *                             type="array",
     *                             description="Fill-in-the-blank answers",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=184),
     *                                 @OA\Property(property="count_number", type="string", example="30"),
     *                                 @OA\Property(property="correct_answer", type="string", example="3500"),
     *                                 @OA\Property(property="extra_answer", type="string", nullable=true, example=null)
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="tests",
     *                             type="array",
     *                             description="Multiple choice (4 options) questions",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=18),
     *                                 @OA\Property(property="question", type="string", example="34. The majority of climbers on Everest …"),
     *                                 @OA\Property(property="answer_1", type="string", example="need oxygen to finish their ascent"),
     *                                 @OA\Property(property="answer_2", type="string", example="are accompanied"),
     *                                 @OA\Property(property="answer_3", type="string", example="make slow progress to the top"),
     *                                 @OA\Property(property="answer_4", type="string", example="all of the above"),
     *                                 @OA\Property(property="correct_answer", type="string", example="4")
     *                             )
     *                         )
     *                     )
     *                 ),
     *
     *                 @OA\Property(
     *                     property="writing_exercise",
     *                     nullable=true,
     *                     type="object",
     *                     description="null if writing part has no task_one",
     *                     @OA\Property(property="id", type="integer", example=31),
     *                     @OA\Property(property="name", type="string", example="Full Mock 1"),
     *                     @OA\Property(property="part_category", type="string", example="Full mock"),
     *                     @OA\Property(property="part_category_number", type="string", example="3"),
     *                     @OA\Property(property="status", type="string", example="0"),
     *                     @OA\Property(property="time_limit", type="integer", example=60),
     *                     @OA\Property(property="prepare_text", type="string", example="In Task 1 you write a letter. Task 2 is an essay. Task 2 accounts for 2/3 of your Writing band score."),
     *                     @OA\Property(
     *                         property="task_one",
     *                         type="object",
     *                         description="Letter writing task (at least 150 words)",
     *                         @OA\Property(property="id", type="integer", example=16),
     *                         @OA\Property(property="theme", type="string", example="You have just returned from a holiday. You are unhappy with the hotel quality."),
     *                         @OA\Property(property="requirement", type="string", example="Write a letter to the manager of the travel agency. In your letter,"),
     *                         @OA\Property(property="advice", type="string", example="write at least 150 words"),
     *                         @OA\Property(property="example_text", type="string", example="Dear Sir or Madam,\nI understand that no business likes to hear these kinds of things..."),
     *                         @OA\Property(property="bullet_point_1", type="string", example="• say about the holiday and where you stayed"),
     *                         @OA\Property(property="bullet_point_2", type="string", example="• mention why you are unhappy about the hotel"),
     *                         @OA\Property(property="bullet_point_3", type="string", example="• say what you would like the travel agency to do")
     *                     ),
     *                     @OA\Property(
     *                         property="task_two",
     *                         type="object",
     *                         description="Essay writing task (at least 250 words)",
     *                         @OA\Property(property="id", type="integer", example=12),
     *                         @OA\Property(property="theme", type="string", example="Many products contain high levels of sugar which causes health problems. So, sugary products should be made more expensive. Do you agree or disagree?"),
     *                         @OA\Property(property="requirement", type="string", example="at least 250"),
     *                         @OA\Property(property="advice", type="string", example="at least 250"),
     *                         @OA\Property(property="example_text", type="string", example="Sugar is undoubtedly one of the most harmful food ingredients when consumed more than recommended...")
     *                     )
     *                 ),
     *
     *                 @OA\Property(
     *                     property="speaking_exercise",
     *                     nullable=true,
     *                     type="object",
     *                     description="null if speaking part has no questions",
     *                     @OA\Property(property="id", type="integer", example=118),
     *                     @OA\Property(property="title", type="string", example="Full Mock 1"),
     *                     @OA\Property(property="status", type="string", example="0"),
     *                     @OA\Property(property="time_limit", type="integer", example=6),
     *                     @OA\Property(property="part_two_start_time", type="integer", example=60, description="Seconds allowed for Part 2 preparation"),
     *                     @OA\Property(property="part_three_start_time", type="integer", example=60, description="Seconds allowed for Part 3 preparation"),
     *                     @OA\Property(
     *                         property="part_one_first",
     *                         type="object",
     *                         description="Short personal questions (30 sec each)",
     *                         @OA\Property(property="id", type="integer", example=58),
     *                         @OA\Property(property="title", type="string", example="Part 1.1"),
     *                         @OA\Property(
     *                             property="questions",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=101),
     *                                 @OA\Property(property="modal_answer", type="string", nullable=true, example=null),
     *                                 @OA\Property(property="question", type="string", example="Please tell me about your best friend."),
     *                                 @OA\Property(property="time", type="integer", example=30, description="Seconds to answer"),
     *                                 @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/audio/stream/part_one_first_q101.mp3", description="Streaming URL of the question audio, null if none")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_one_second",
     *                         type="object",
     *                         description="Image-based discussion questions",
     *                         @OA\Property(property="id", type="integer", example=58),
     *                         @OA\Property(property="title", type="string", example="Part 1.2"),
     *                         @OA\Property(
     *                             property="images",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="image", type="string", example="http://127.0.0.1:8000/storage/speaking_images/image.jpg")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="questions",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=201),
     *                                 @OA\Property(property="modal_answer", type="string", nullable=true, example=null),
     *                                 @OA\Property(property="question", type="string", example="What do you see in these pictures?"),
     *                                 @OA\Property(property="time", type="integer", example=45),
     *                                 @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/audio/stream/part_one_second_q201.mp3", description="Streaming URL of the question audio, null if none")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_two",
     *                         type="object",
     *                         description="Long turn — speak for 1-2 minutes on a topic",
     *                         @OA\Property(property="id", type="integer", example=54),
     *                         @OA\Property(property="title", type="string", example="Part 2"),
     *                         @OA\Property(property="modal_answer", type="string", example="I'LL NEVER FORGET THE DAY I RECEIVED SOME UNEXPECTED GOOD NEWS..."),
     *                         @OA\Property(
     *                             property="images",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="image", type="string", example="http://127.0.0.1:8000/storage/speaking_images/image.jpg")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="questions",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=301),
     *                                 @OA\Property(property="question", type="string", example="Tell me about a critical decision you have made.\n• How has this decision influenced you?\n• What factors have the highest impact on decisions people make?")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="part_three",
     *                         type="object",
     *                         description="Discussion — argue for/against a statement",
     *                         @OA\Property(property="id", type="integer", example=54),
     *                         @OA\Property(property="title", type="string", example="Part 3"),
     *                         @OA\Property(property="modal_answer", type="string", example="TODAY, I WANT TO TALK ABOUT WHETHER SMARTPHONES SHOULD BE BANNED IN SCHOOLS..."),
     *                         @OA\Property(property="theme", type="string", example="Citizens should be allowed to carry personal guns."),
     *                         @OA\Property(property="question_text", type="string", example="FOR\n• Guns can help people protect themselves\nAGAINST\n• Guns are weapons used to commit a crime")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated or device mismatch",
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
     *         response=403,
     *         description="Premium plan required — no active subscription",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Premium plan required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Full exercise not found",
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

        $full_exercise=FullExercise::find($request->id);

        if($full_exercise->status=='1'){
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


        if ($full_exercise) {
            return new FullExerciseWithPartsResource($full_exercise);
        }else {
            return response()->json(["message" => "Reading exercise not found"], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/v2/Full/save",
     *     operationId="postV2FullExamSave",
     *     tags={"Full Exam API"},
     *     summary="Save full exam result",
     *     description="Saves a full mock exam result. This endpoint can receive listening, reading, writing, and speaking results together in a single request. Because speaking recordings are uploaded files, use multipart/form-data. Each *_result block is optional conceptually, but if a block is sent it must contain the same detailed payload structure as its standalone save endpoint.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="aplication/json",
     *             @OA\Schema(
     *                 required={"full_exercise_id"},
     *                 @OA\Property(property="full_exercise_id", type="integer", example=17),
     *                 @OA\Property(
     *                     property="listening_result",
     *                     type="object",
     *                     nullable=true,
     *                     description="Listening payload copied from /v2/listening_save. Supports six part formats.",
     *                     required={"listening_exercise_id","all_answers","correct_answers","wrong_answers","parts_data"},
     *                     @OA\Property(property="listening_exercise_id", type="integer", example=38),
     *                     @OA\Property(property="all_answers", type="integer", example=35),
     *                     @OA\Property(property="correct_answers", type="integer", example=29),
     *                     @OA\Property(property="wrong_answers", type="integer", example=6),
     *                     @OA\Property(
     *                         property="parts_data",
     *                         type="object",
     *                         @OA\Property(
     *                             property="part_one",
     *                             type="array",
     *                             nullable=true,
     *                             description="Listening part 1, 3-option multiple choice.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"id","is_correct","answer","correct_answer_key","correct_answer"},
     *                                 @OA\Property(property="id", type="integer", example=65),
     *                                 @OA\Property(property="is_correct", type="integer", example=0),
     *                                 @OA\Property(property="answer", type="string", example="answer_3"),
     *                                 @OA\Property(property="correct_answer_key", type="string", example="answer_2"),
     *                                 @OA\Property(property="correct_answer", type="string", example="It's a generous offer")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="part_two",
     *                             type="array",
     *                             nullable=true,
     *                             description="Listening part 2, fill-in answers.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"id","is_correct","answer","correct_answer"},
     *                                 @OA\Property(property="id", type="integer", example=41),
     *                                 @OA\Property(property="is_correct", type="integer", example=1),
     *                                 @OA\Property(property="answer", type="string", example="five"),
     *                                 @OA\Property(property="correct_answer", type="string", example="six"),
     *                                 @OA\Property(property="extra_answer", type="string", nullable=true, example="6")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="part_three",
     *                             type="array",
     *                             nullable=true,
     *                             description="Listening part 3, matching speakers to answers.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"id","is_correct","answer_id","correct_answer","correct_answer_id"},
     *                                 @OA\Property(property="id", type="integer", example=32),
     *                                 @OA\Property(property="is_correct", type="integer", example=1),
     *                                 @OA\Property(property="answer_id", type="integer", example=42),
     *                                 @OA\Property(property="correct_answer", type="string", example="Exams are necessary."),
     *                                 @OA\Property(property="correct_answer_id", type="integer", example=42)
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="part_four",
     *                             type="array",
     *                             nullable=true,
     *                             description="Listening part 4, map/location matching.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"id","is_correct","answer_id","correct_answer","correct_answer_id"},
     *                                 @OA\Property(property="id", type="integer", example=36),
     *                                 @OA\Property(property="is_correct", type="integer", example=1),
     *                                 @OA\Property(property="answer_id", type="integer", example=48),
     *                                 @OA\Property(property="correct_answer", type="string", example="E"),
     *                                 @OA\Property(property="correct_answer_id", type="integer", example=48)
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="part_five",
     *                             type="array",
     *                             nullable=true,
     *                             description="Listening part 5, 3-option multiple choice.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"id","is_correct","answer","correct_answer_key","correct_answer"},
     *                                 @OA\Property(property="id", type="integer", example=73),
     *                                 @OA\Property(property="is_correct", type="integer", example=1),
     *                                 @OA\Property(property="answer", type="string", example="answer_2"),
     *                                 @OA\Property(property="correct_answer_key", type="string", example="answer_2"),
     *                                 @OA\Property(property="correct_answer", type="string", example="the lack of snow")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="part_six",
     *                             type="array",
     *                             nullable=true,
     *                             description="Listening part 6, fill-in answers.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"id","is_correct","answer","correct_answer"},
     *                                 @OA\Property(property="id", type="integer", example=47),
     *                                 @OA\Property(property="is_correct", type="integer", example=1),
     *                                 @OA\Property(property="answer", type="string", example="envelope"),
     *                                 @OA\Property(property="correct_answer", type="string", example="envelope")
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="reading_result",
     *                     type="object",
     *                     nullable=true,
     *                     description="Reading payload copied from /v2/reading/save. Supports five part formats.",
     *                     required={"reading_exercise_id","all_answers","correct_answers","wrong_answers","parts_data"},
     *                     @OA\Property(property="reading_exercise_id", type="integer", example=164),
     *                     @OA\Property(property="all_answers", type="integer", example=35),
     *                     @OA\Property(property="correct_answers", type="integer", example=20),
     *                     @OA\Property(property="wrong_answers", type="integer", example=15),
     *                     @OA\Property(
     *                         property="parts_data",
     *                         type="object",
     *                         @OA\Property(
     *                             property="part_one",
     *                             type="array",
     *                             nullable=true,
     *                             description="Reading part 1, fill-in answers.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"id","is_correct","answer","correct_answer"},
     *                                 @OA\Property(property="id", type="integer", example=201),
     *                                 @OA\Property(property="is_correct", type="integer", example=1),
     *                                 @OA\Property(property="answer", type="string", example="camera"),
     *                                 @OA\Property(property="correct_answer", type="string", example="camera")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="part_two",
     *                             type="array",
     *                             nullable=true,
     *                             description="Reading part 2, matching texts to answers.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"id","is_correct","answer","correct_answer","correct_answer_id"},
     *                                 @OA\Property(property="id", type="integer", example=301),
     *                                 @OA\Property(property="is_correct", type="integer", example=1),
     *                                 @OA\Property(property="answer", type="integer", example=401),
     *                                 @OA\Property(property="correct_answer", type="string", example="A suitable title"),
     *                                 @OA\Property(property="correct_answer_id", type="integer", example=401)
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="part_three",
     *                             type="array",
     *                             nullable=true,
     *                             description="Reading part 3, matching texts/headings.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"id","is_correct","answer","correct_answer","correct_answer_id"},
     *                                 @OA\Property(property="id", type="integer", example=302),
     *                                 @OA\Property(property="is_correct", type="integer", example=0),
     *                                 @OA\Property(property="answer", type="integer", example=402),
     *                                 @OA\Property(property="correct_answer", type="string", example="Another title"),
     *                                 @OA\Property(property="correct_answer_id", type="integer", example=402)
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="part_four",
     *                             type="array",
     *                             nullable=true,
     *                             description="Reading part 4, True/False/Not Given or similar MC answers.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"id","is_correct","answer","correct_answer_key","correct_answer"},
     *                                 @OA\Property(property="id", type="integer", example=501),
     *                                 @OA\Property(property="is_correct", type="integer", example=1),
     *                                 @OA\Property(property="answer", type="string", example="answer_1"),
     *                                 @OA\Property(property="correct_answer_key", type="string", example="answer_1"),
     *                                 @OA\Property(property="correct_answer", type="string", example="True")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="part_five",
     *                             type="array",
     *                             nullable=true,
     *                             description="Reading part 5 can contain mixed item types: input and test.",
     *                             example={
     *                                 {"id":701,"type":"input","is_correct":0,"answer":"projector","correct_answer":"projectors"},
     *                                 {"id":801,"type":"test","is_correct":0,"answer":"answer_1","correct_answer":"Option C","correct_answer_key":"answer_3"}
     *                             },
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"id","type","is_correct","answer","correct_answer"},
     *                                 @OA\Property(property="id", type="integer", example=701),
     *                                 @OA\Property(property="type", type="string", example="input"),
     *                                 @OA\Property(property="is_correct", type="integer", example=0),
     *                                 @OA\Property(property="answer", type="string", example="projector"),
     *                                 @OA\Property(property="correct_answer", type="string", example="projectors"),
     *                                 @OA\Property(property="correct_answer_key", type="string", nullable=true, example="answer_3")
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="writing_result",
     *                     type="object",
     *                     nullable=true,
     *                     description="Writing payload copied from /v2/writing/full/save.",
     *                     required={"writing_exercise_id","text_1_1","text_1_2","text_2","with_ai"},
     *                     @OA\Property(property="writing_exercise_id", type="integer", example=32),
     *                     @OA\Property(property="text_1_1", type="string", example="Task 1 first answer with detailed content ..."),
     *                     @OA\Property(property="text_1_2", type="string", example="Task 1 second answer with detailed content ..."),
     *                     @OA\Property(property="text_2", type="string", example="Task 2 essay answer with detailed content ..."),
     *                     @OA\Property(property="with_ai", type="integer", enum={0,1}, example=1)
     *                 ),
     *                 @OA\Property(
     *                     property="speaking_result",
     *                     type="object",
     *                     nullable=true,
     *                     description="Speaking payload copied from /v2/speaking/save. Nested file uploads must be sent in multipart/form-data.",
     *                     required={"speaking_exercise_id","with_ai","recordings"},
     *                     @OA\Property(property="speaking_exercise_id", type="integer", example=15),
     *                     @OA\Property(property="with_ai", type="string", enum={"0","1"}, example="1"),
     *                     @OA\Property(
     *                         property="recordings",
     *                         type="object",
     *                         @OA\Property(
     *                             property="part_one_first",
     *                             type="array",
     *                             nullable=true,
     *                             description="Speaking full exam part 1 first section recordings.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"questionId","file"},
     *                                 @OA\Property(property="questionId", type="integer", example=101),
     *                                 @OA\Property(property="file", type="string", format="binary")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="part_one_second",
     *                             type="array",
     *                             nullable=true,
     *                             description="Speaking full exam part 1 second section recordings.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"questionId","file"},
     *                                 @OA\Property(property="questionId", type="integer", example=201),
     *                                 @OA\Property(property="file", type="string", format="binary")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="part_two",
     *                             type="array",
     *                             nullable=true,
     *                             description="Speaking full exam part 2 recordings.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"questionId","file"},
     *                                 @OA\Property(property="questionId", type="integer", example=301),
     *                                 @OA\Property(property="file", type="string", format="binary")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="part_three",
     *                             type="array",
     *                             nullable=true,
     *                             description="Speaking full exam part 3 recordings.",
     *                             @OA\Items(
     *                                 type="object",
     *                                 required={"questionId","file"},
     *                                 @OA\Property(property="questionId", type="integer", example=401),
     *                                 @OA\Property(property="file", type="string", format="binary")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Saved successfully. Returns the current full exercise result state (last result plus its recent history).",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=17, description="Full exercise id"),
     *                 @OA\Property(property="reset_status", type="boolean", example=true, description="true if the last result is unfinished (status=0) and can be reset; false when there is no result or the last result is finished (status=1)"),
     *                 @OA\Property(
     *                     property="lastResult",
     *                     type="object",
     *                     nullable=true,
     *                     description="Most recent full result, or null if the user has no result for this exercise.",
     *                     @OA\Property(property="id", type="integer", example=66),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-07-18T13:30:13.000000Z"),
     *                     @OA\Property(property="status", type="integer", enum={0,1}, example=0, description="0 = unfinished, 1 = finished")
     *                 ),
     *                 @OA\Property(
     *                     property="lastResults",
     *                     type="array",
     *                     nullable=true,
     *                     description="Up to 3 previous full results (excluding lastResult), newest first. null when there are no previous results.",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=65),
     *                         @OA\Property(property="status", type="integer", enum={0,1}, example=1),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2026-07-18T13:18:31.000000Z")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="exercise_data",
     *                     type="object",
     *                     description="Per-skill availability for the current attempt. true = skill still available to take (not yet saved in the unfinished result); false = already saved. When there is no unfinished result (status=1 or no result), all skills default to true.",
     *                     @OA\Property(property="listening", type="boolean", example=true),
     *                     @OA\Property(property="reading", type="boolean", example=true),
     *                     @OA\Property(property="writing", type="boolean", example=true),
     *                     @OA\Property(property="speaking", type="boolean", example=true)
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="full result saqlandi")
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
     *                 @OA\Property(property="full_exercise_id", type="array", @OA\Items(type="string", example="The selected full exercise id is invalid.")),
     *                 @OA\Property(property="listening_result.listening_exercise_id", type="array", @OA\Items(type="string", example="Exercise ID is required.")),
     *                 @OA\Property(property="listening_result.all_answers", type="array", @OA\Items(type="string", example="all_answers is required.")),
     *                 @OA\Property(property="listening_result.correct_answers", type="array", @OA\Items(type="string", example="correct_answers is required.")),
     *                 @OA\Property(property="reading_result.reading_exercise_id", type="array", @OA\Items(type="string", example="The selected reading_result.reading_exercise_id is invalid.")),
     *                 @OA\Property(property="reading_result.parts_data", type="array", @OA\Items(type="string", example="The reading result.parts data field is required.")),
     *                 @OA\Property(property="writing_result.writing_exercise_id", type="array", @OA\Items(type="string", example="The selected writing_result.writing_exercise_id is invalid.")),
     *                 @OA\Property(property="writing_result.text_2", type="array", @OA\Items(type="string", example="The writing result.text 2 field is required.")),
     *                 @OA\Property(property="speaking_result.speaking_exercise_id", type="array", @OA\Items(type="string", example="The selected speaking_result.speaking_exercise_id is invalid.")),
     *                 @OA\Property(property="speaking_result.recordings", type="array", @OA\Items(type="string", example="The speaking result.recordings field is required.")),
     *                 @OA\Property(property="speaking_result.recordings.part_one_first.0.questionId", type="array", @OA\Items(type="string", example="The speaking result.recordings.part one first.0.question id field is required.")),
     *                 @OA\Property(property="speaking_result.recordings.part_one_first.0.file", type="array", @OA\Items(type="string", example="The speaking result.recordings.part one first.0.file field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function save(Request $request,WritingStoreService $writingStoreService,SpeakingStoreService $speakingStoreService,FullMockService $fullMockService)
    {
        try {

        $this->validate($request, [
            'full_exercise_id' => 'required|exists:full_exercises,id',

            'listening_result'                             => 'nullable|array',
            'listening_result.listening_exercise_id'      => 'required_with:listening_result|integer|exists:listening_exercises,id',
            'listening_result.all_answers'                => 'required_with:listening_result|integer|min:1',
            'listening_result.correct_answers'            => 'required_with:listening_result|integer|min:0',
            'listening_result.wrong_answers'              => 'required_with:listening_result|integer|min:0',
            'listening_result.parts_data'                 => 'required_with:listening_result|array',

            'reading_result'                              => 'nullable|array',
            'reading_result.reading_exercise_id'          => 'required_with:reading_result|integer|exists:reading_exercises,id',
            'reading_result.all_answers'                  => 'required_with:reading_result|integer|min:1',
            'reading_result.correct_answers'              => 'required_with:reading_result|integer|min:0',
            'reading_result.wrong_answers'                => 'required_with:reading_result|integer|min:0',
            'reading_result.parts_data'                   => 'required_with:reading_result|array',

            'writing_result'                              => 'nullable|array',
            'writing_result.writing_exercise_id'          => 'required_with:writing_result|integer|exists:writing_exercises,id',
            'writing_result.text_1_1'                     => 'nullable|string',
            'writing_result.text_1_2'                     => 'nullable|string',
            'writing_result.text_2'                       => 'nullable|string',
            'writing_result.with_ai'                      => 'required_with:writing_result|in:0,1',

            'speaking_result'                             => 'nullable|array',
            'speaking_result.speaking_exercise_id'        => 'required_with:speaking_result|integer|exists:speaking_exercises,id',
            'speaking_result.with_ai'                     => 'required_with:speaking_result|in:0,1',
            'speaking_result.recordings'                  => 'required_with:speaking_result|array',
            'speaking_result.recordings.*'                => 'array',
            'speaking_result.recordings.*.*.questionId'   => 'required|integer',
            'speaking_result.recordings.*.*.file'         => 'required|file|mimes:webm,mp3,wav,m4a,ogg|max:20480',
        ], [
            'listening_result.listening_exercise_id.required_with' => 'Listening exercise ID is required.',
            'listening_result.listening_exercise_id.exists'        => 'The listening exercise does not exist.',
            'listening_result.all_answers.required_with'           => 'all_answers is required.',
            'listening_result.correct_answers.required_with'       => 'correct_answers is required.',
            'listening_result.wrong_answers.required_with'         => 'wrong_answers is required.',
            'reading_result.reading_exercise_id.required_with'     => 'Reading exercise ID is required.',
            'reading_result.reading_exercise_id.exists'            => 'The reading exercise does not exist.',
            'writing_result.writing_exercise_id.required_with'     => 'Writing exercise ID is required.',
            'writing_result.writing_exercise_id.exists'            => 'The writing exercise does not exist.',
            'speaking_result.speaking_exercise_id.required_with'   => 'Speaking exercise ID is required.',
            'speaking_result.speaking_exercise_id.exists'          => 'The speaking exercise does not exist.',
        ]);
//        return $request->listening_result;

        $user = auth('sanctum')->user();
//        $fullResult = $this->createFullResult($request->full_exercise_id, ['status' => 0, 'user_id' => $user->id]);

        if ($request->listening_result) {

            $listening_request = $request->listening_result;
            $listening_exercise = ListeningExercise::with(['full_exercise'=>function ($query) {
                return $query->select('id','listening_exercise_id','exercise_id');
            }])->find($listening_request['listening_exercise_id']);

            $listeningResult = ListeningResult::create([
                'listening_exercise_id' => $listening_request['listening_exercise_id'],
                'category_type'         => $listening_exercise->part_category ?? null,
                'all_answers'           => $listening_request['all_answers'],
                'correct_answers'       => $listening_request['correct_answers'],
                'wrong_answers'         => $listening_request['wrong_answers'],
                'user_id'               => Auth::id(),
                'percent'               => ($listening_request['correct_answers'] / $listening_request['all_answers']) * 100,
            ]);

            $this->createListeningResultParts($listeningResult, $listening_request['parts_data']);

            $this->accessMockResult($listening_exercise, $listeningResult, $user, CefrTypeEnum::LISTENING->value);
        }

        if ($request->reading_result) {
            $reading_request = $request->reading_result;
            $reading_exercise = ReadingExercise::with(['full_exercise'=>function ($query) {
                return $query->select('id','reading_exercise_id','exercise_id');
            }])->find($reading_request['reading_exercise_id']);
            $percent = ($reading_request['correct_answers'] * 100) / $reading_request['all_answers'];

            $readingResult = ReadingResult::create([
                'reading_exercise_id' => $reading_request['reading_exercise_id'],
                'category_type'       => $reading_exercise->part_category ?? null,
                'all_answers'         => $reading_request['all_answers'],
                'correct_answers'     => $reading_request['correct_answers'],
                'wrong_answers'       => $reading_request['wrong_answers'],
                'user_id'             => Auth::id(),
                'percent'             => $percent,
            ]);
            $this->createReadingResultParts($readingResult, $reading_request['parts_data']);
            $this->accessMockResult($reading_exercise, $readingResult, $user, CefrTypeEnum::READING->value);
        }

        if ($request->writing_result) {
            $writing_request = $request->writing_result;
            $writingExercise = WritingExercise::find($writing_request['writing_exercise_id']);
            $text_11 = $this->validateWritingText($writing_request['text_1_1'] ?? '', 300, 3000);
            $text_12 = $this->validateWritingText($writing_request['text_1_2'] ?? '', 300, 3000);
            $text_2  = $this->validateWritingText($writing_request['text_2'] ?? '', 350, 3500);
            $withAi  = (int) ($writing_request['with_ai'] ?? 0);
            $text_1  = trim($text_11) . "\n\n\n" . trim($text_12);

            $creditStatus = $this->getUserCreditStatus($withAi, $user->balance);

            $writing_result = WritingResult::create([
                'category_type'       => $writingExercise->part_category,
                'writing_exercise_id' => $writingExercise->id,
                'text_1'              => $text_1,
                'text_2'              => $text_2,
                'user_id'             => $user->id,
                'with_ai'             => $withAi ? 1 : 0,
                'status'              => $withAi ? 1 : 0 ,
                'max_score'           => CefrTypeEnum::WRITING_MAX_SCORES[$writingExercise->part_category],
            ]);

            WritingPartOneAnswer::create([
                'text_1'             => $text_11,
                'text_2'             => $text_12,
                'writing_result_id'  => $writing_result->id,
                'max_score'          => CefrTypeEnum::WRITING_MAX_SCORES[1],
            ]);

            WritingPartTwoAnswer::create([
                'text'              => $text_2,
                'writing_result_id' => $writing_result->id,
                'max_score'         => CefrTypeEnum::WRITING_MAX_SCORES[2],
            ]);

            if ($withAi && $creditStatus) {
                $writingStoreService->dispatchToJob($writing_result, $writingExercise, $user->id);
            }

            $isMock=$this->accessMockResult($writingExercise,$writing_result,$user,CefrTypeEnum::WRITING->value);

        }

        if ($request->speaking_result) {
            $speaking_request = $request->speaking_result;
            $withAi = (int) ($speaking_request['with_ai'] ?? 0);
            $speaking_exercise=SpeakingExercise::select('id','part_category')
                ->where('id',$speaking_request['speaking_exercise_id'])->first();
            $part_category = $speaking_exercise->part_category;

            $recordings = $request->file('speaking_result.recordings');
            $recordingInputs = $request->input('speaking_result.recordings', []);

            $creditStatus = $this->getUserCreditStatus($withAi, $user->balance);

            $speakingResult = SpeakingResult::create([
                'user_id'              => $user->id,
                'category_type'        => $part_category,
                'speaking_exercise_id' => $speaking_request['speaking_exercise_id'],
                'with_ai'              => $withAi,
                'status'               => $withAi ? ($creditStatus? 1 : 4) : 0,
                'max_score'            => CefrTypeEnum::SPEAKING_MAX_SCORES[$part_category],
            ]);

            $dir = 'voiceAudios';

            if ($part_category == 1 || $part_category == 4) {
                $speakingPartOneFirstResult = SpeakingPartOneFirstResult::create([
                    'speaking_result_id' => $speakingResult->id,
                ]);

                if (isset($recordings['part_one_first'])) {
                    foreach ($recordings['part_one_first'] as $index => $data) {
                        $questionId = $recordingInputs['part_one_first'][$index]['questionId'] ?? null;
                        $file = $data['file'];
                        $ext = $file->getClientOriginalExtension() ?: 'webm';
                        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'webm';

                        $filename = sprintf(
                            'ex_%d_res_part_1first_q_%d_%s.%s',
                            $speakingResult->id,
                            (int) $questionId,
                            Str::random(10),
                            $safeExt
                        );

                        Storage::disk('private')->putFileAs($dir, $file, $filename);

                        SpeakingPartOneFirstAnswer::create([
                            'one_first_result_id' => $speakingPartOneFirstResult->id,
                            'question_id'         => $questionId,
                            'audio_path'          => $filename,
                        ]);
                    }
                }

                $speakingPartOneSecondResult = SpeakingPartOneSecondResult::create([
                    'speaking_result_id' => $speakingResult->id,
                ]);

                if (isset($recordings['part_one_second'])) {
                    foreach ($recordings['part_one_second'] as $index => $data) {
                        $questionId = $recordingInputs['part_one_second'][$index]['questionId'] ?? null;
                        $file = $data['file'];
                        $ext = $file->getClientOriginalExtension() ?: 'webm';
                        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'webm';

                        $filename = sprintf(
                            'ex_%d_res_part_1_second_q_%d_%s.%s',
                            $speakingResult->id,
                            (int) $questionId,
                            Str::random(10),
                            $safeExt
                        );

                        Storage::disk('private')->putFileAs($dir, $file, $filename);

                        SpeakingPartOneSecondAnswer::create([
                            'one_second_result_id' => $speakingPartOneSecondResult->id,
                            'question_id'          => $questionId,
                            'audio_path'           => $filename,
                        ]);
                    }
                }
            }

            if ($part_category == 2 || $part_category == 4) {
                if (isset($recordings['part_two'])) {
                    foreach ($recordings['part_two'] as $index => $data) {
                        $questionId = $recordingInputs['part_two'][$index]['questionId'] ?? null;
                        $file = $data['file'];
                        $ext = $file->getClientOriginalExtension() ?: 'webm';
                        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'webm';

                        $filename = sprintf(
                            'ex_%d_res_part_2_q_%d_%s.%s',
                            $speakingResult->id,
                            (int) $questionId,
                            Str::random(10),
                            $safeExt
                        );

                        Storage::disk('private')->putFileAs($dir, $file, $filename);

                        SpeakingPartTwoResult::create([
                            'speaking_result_id' => $speakingResult->id,
                            'question_id'        => $questionId,
                            'audio_path'         => $filename,
                        ]);
                    }
                }
            }

            if ($part_category == 3 || $part_category == 4) {
                if (isset($recordings['part_three'])) {
                    foreach ($recordings['part_three'] as $index => $data) {
                        $questionId = $recordingInputs['part_three'][$index]['questionId'] ?? null;
                        $file = $data['file'];
                        $ext = $file->getClientOriginalExtension() ?: 'webm';
                        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'webm';

                        $filename = sprintf(
                            'ex_%d_res_part_3_q_%d_%s.%s',
                            $speakingResult->id,
                            (int) $questionId,
                            Str::random(10),
                            $safeExt
                        );

                        Storage::disk('private')->putFileAs($dir, $file, $filename);

                        SpeakingPartThreeResult::create([
                            'speaking_result_id' => $speakingResult->id,
                            'part_three_id'      => $questionId,
                            'audio_path'         => $filename,
                        ]);
                    }
                }
            }

            if ($withAi && $creditStatus) {
                $speakingStoreService->dispatchToJob($speakingResult, $speaking_exercise, $user->id);
            }else{
                SpeakingChangeAudioFormat::dispatch($speakingResult->id);
            }

            $isMock=$this->accessMockResult($speaking_exercise,$speakingResult,$user,CefrTypeEnum::SPEAKING->value);

        }

        return $this->success($fullMockService->getFullResultData($request->full_exercise_id), 'full result saqlandi');

        }catch (\Exception $exception){
            Log::channel('telegram')->error(" Api error :\n speaking/save \n\n ".$this->escapeForTelegram($exception->getMessage()));

            return $this->error([$exception->getMessage()],'nimadur xato admin bilan bog`laning');

        }
    }

    /**
     * @OA\Get(
     *     path="/v2/Full/reset/{id}",
     *     operationId="getV2FullReset",
     *     tags={"Full Exam API"},
     *     summary="Reset (finish) the current unfinished full exam attempt",
     *     description="Marks the authenticated user's latest unfinished full result (status=0) for the given full exercise as finished (status=1), allowing a fresh attempt to be started. If there is no unfinished result, nothing is changed. Returns the current full exercise result state after the operation.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Full exercise id",
     *         @OA\Schema(type="integer", example=17)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reset applied (or no unfinished result to reset). Returns the current full exercise result state.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=17, description="Full exercise id"),
     *                 @OA\Property(property="reset_status", type="boolean", example=false, description="true if the last result is unfinished (status=0) and can be reset; false when there is no result or the last result is finished (status=1)"),
     *                 @OA\Property(
     *                     property="lastResult",
     *                     type="object",
     *                     nullable=true,
     *                     description="Most recent full result, or null if the user has no result for this exercise.",
     *                     @OA\Property(property="id", type="integer", example=66),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-07-18T13:30:13.000000Z"),
     *                     @OA\Property(property="status", type="integer", enum={0,1}, example=1, description="0 = unfinished, 1 = finished")
     *                 ),
     *                 @OA\Property(
     *                     property="lastResults",
     *                     type="array",
     *                     nullable=true,
     *                     description="Up to 3 previous full results (excluding lastResult), newest first. null when there are no previous results.",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=65),
     *                         @OA\Property(property="status", type="integer", enum={0,1}, example=1),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2026-07-18T13:18:31.000000Z")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="exercise_data",
     *                     type="object",
     *                     description="Per-skill availability for the current attempt. true = skill still available to take (not yet saved in the unfinished result); false = already saved. After a reset the last result is finished, so all skills default to true.",
     *                     @OA\Property(property="listening", type="boolean", example=true),
     *                     @OA\Property(property="reading", type="boolean", example=true),
     *                     @OA\Property(property="writing", type="boolean", example=true),
     *                     @OA\Property(property="speaking", type="boolean", example=true)
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="full result saqlandi")
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
     *         description="Full exercise not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Not Found")
     *         )
     *     )
     * )
     *
     * @OA\Get(
     *     path="/v2/Full/results",
     *     operationId="getV2FullResults",
     *     tags={"Full Exam API"},
     *     summary="List saved full mock exam results",
     *     description="Returns a paginated list of the authenticated user's saved (status=1) full mock exam results with per-skill CEFR scores and an overall score (average of only the skills that were actually taken).",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Filter by full exercise title (partial match).",
     *         @OA\Schema(type="string", example="Full Mock 1")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *         description="Only results created on/after this date (Y-m-d).",
     *         @OA\Schema(type="string", format="date", example="2026-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *         description="Only results created on/before this date (Y-m-d).",
     *         @OA\Schema(type="string", format="date", example="2026-12-31")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Pagination page number (10 per page).",
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
     *                     @OA\Property(property="id", type="integer", example=55, description="FullResult id"),
     *                     @OA\Property(property="full_exercise_id", type="integer", example=17),
     *                     @OA\Property(property="name", type="string", example="Full Mock 1"),
     *                     @OA\Property(property="date", type="string", example="2026-07-12 08:16"),
     *                     @OA\Property(property="score", type="integer", example=6, description="Overall score = average of only the skills taken"),
     *                     @OA\Property(property="listening_score", type="integer", example=7),
     *                     @OA\Property(property="reading_score", type="integer", example=6),
     *                     @OA\Property(property="writing_score", type="integer", example=5),
     *                     @OA\Property(property="speaking_score", type="integer", example=6),
     *                     @OA\Property(property="createdDate", type="string", example="2026-07-12T08:16:00.000000Z")
     *                 )
     *             ),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
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
        $results = FullResult::query()
            ->select([
                'id',
                'full_exercise_id',
                'date',
                'created_at',
                'listening_result_id',
                'reading_result_id',
                'writing_result_id',
                'speaking_result_id',
            ])
            ->with([
                'exercise:id,title',
                'listening_result:id,correct_answers',
                'reading_result:id,correct_answers',
                'writing_result' => fn ($q) => $q->select('id', 'score')->without('partOneAnswer'),
                'speaking_result:id,score',
            ])
            ->where('user_id', auth()->id())
            ->where('status', 1)
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('exercise', function ($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->start_date, fn ($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->end_date, fn ($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->orderByDesc('date')
            ->paginate(10);

        return FullResultListResource::collection($results);
    }
    /**
     * @OA\Get(
     *     path="/v2/Full/result/{id}",
     *     operationId="getV2FullResultShow",
     *     tags={"Full Exam API"},
     *     summary="Get full mock exam result detail",
     *     description="Returns the saved result of a full mock exam (the FullResult created by /v2/Full/save), broken down per skill. listeningData/readingData/speakingData reuse the exact same data generator as their standalone *_result_show endpoints, so their shape is identical to /v2/listening/result/show/{id}, /v2/reading/result/show/{id} and /v2/speaking/result/{id}. writingData is generated by a slightly different method than /v2/writing/result/{id}: it exposes the raw 'writingResult' Eloquent model (instead of separate writing_result_id/category_type fields) and renders corrected text as HTML spans instead of {correction}[mistake] markers. Whenever a skill was not taken/saved for this full result, its *Data field is returned as an empty array [] instead of an object.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the FullResult row (the 'full_result.id' returned by /v2/Full/save), not the full_exercise_id.",
     *         @OA\Schema(type="integer", example=55)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=55, description="FullResult id"),
     *                 @OA\Property(property="exercise_id", type="integer", example=17, description="full_exercise_id"),
     *
     *                 @OA\Property(
     *                     property="listeningData",
     *                     description="Empty array [] when the listening part was not saved for this full result. Otherwise identical to the 'data' object returned by GET /v2/listening/result/show/{id}.",
     *                     oneOf={
     *                         @OA\Schema(type="array", @OA\Items(type="string"), example={}),
     *                         @OA\Schema(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=118),
     *                             @OA\Property(property="listening_exercise_id", type="integer", example=164),
     *                             @OA\Property(property="createdText", type="string", example="4 март, 08:16"),
     *                             @OA\Property(property="name", type="string", example="Full Mock 1"),
     *                             @OA\Property(property="status", type="string", example="0"),
     *                             @OA\Property(property="type", type="integer", example=0),
     *                             @OA\Property(property="part_category", type="string", nullable=true, example="full"),
     *                             @OA\Property(property="part_category_number", type="string", example="7"),
     *                             @OA\Property(property="difficulty", type="string", example="0"),
     *                             @OA\Property(property="asset_url", type="string", example="/"),
     *                             @OA\Property(property="time_limit", type="integer", example=35),
     *                             @OA\Property(property="audio_control", type="integer", example=0),
     *                             @OA\Property(property="all_answers", type="integer", example=35),
     *                             @OA\Property(property="correct_answers", type="integer", example=2),
     *                             @OA\Property(property="wrong_answers", type="integer", example=0),
     *                             @OA\Property(property="feedback", type="string", example="Your score is very low. You need to improve your basics."),
     *                             @OA\Property(property="suggestion", type="string", example="Focus on simple English listening and reading exercises daily"),
     *                             @OA\Property(
     *                                 property="percentageData",
     *                                 type="object",
     *                                 @OA\Property(property="percent", type="integer", example=6),
     *                                 @OA\Property(property="array", type="integer", example=289),
     *                                 @OA\Property(property="offset", type="integer", example=272),
     *                                 @OA\Property(property="mock_ball", type="integer", nullable=true, example=26),
     *                                 @OA\Property(property="degree", type="string", nullable=true, example="Below B1"),
     *                                 @OA\Property(property="is_mock", type="boolean", example=true)
     *                             ),
     *                             @OA\Property(
     *                                 property="parts_data",
     *                                 type="object",
     *                                 @OA\Property(property="part_one", type="string", example="Part One"),
     *                                 @OA\Property(property="part_two", type="string", example="Part Two"),
     *                                 @OA\Property(property="part_three", type="string", example="Part Three"),
     *                                 @OA\Property(property="part_four", type="string", example="Part Four"),
     *                                 @OA\Property(property="part_five", type="string", example="Part Five"),
     *                                 @OA\Property(property="part_six", type="string", example="Part Six")
     *                             ),
     *                             @OA\Property(property="active", type="string", nullable=true, example="part_one"),
     *                             @OA\Property(
     *                                 property="part_one",
     *                                 nullable=true,
     *                                 type="object",
     *                                 description="Multiple choice (A/B/C), 3-choice questions",
     *                                 @OA\Property(property="id", type="integer", example=72),
     *                                 @OA\Property(property="advice", type="string", example="You will hear some sentences. You will hear each sentence twice. Choose the correct reply to each sentence (A, B, or C)."),
     *                                 @OA\Property(property="audio", type="string", nullable=true, example="/storage/audios/part_one_audio_1772507684_69a65224a46ca.mp3"),
     *                                 @OA\Property(
     *                                     property="tests",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=428),
     *                                         @OA\Property(property="answer_1", type="string", example="That's too bad."),
     *                                         @OA\Property(property="answer_2", type="string", example="No, I'm not."),
     *                                         @OA\Property(property="answer_3", type="string", example="I think it is."),
     *                                         @OA\Property(property="correct_answer_key", type="string", example="answer_3"),
     *                                         @OA\Property(property="status", type="string", example="answered"),
     *                                         @OA\Property(property="user_answer_key", type="string", nullable=true, example="answer_3"),
     *                                         @OA\Property(property="correct_answer", type="string", nullable=true, example="I think it is."),
     *                                         @OA\Property(property="is_correct", type="integer", nullable=true, example=1)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="question_data",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="user_answer", type="string", example="I think it is."),
     *                                         @OA\Property(property="correct_answer", type="string", example="I think it is."),
     *                                         @OA\Property(property="is_correct", type="boolean", example=true)
     *                                     )
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="part_two",
     *                                 nullable=true,
     *                                 type="object",
     *                                 description="Fill in the blank, write one word and/or a number",
     *                                 @OA\Property(property="id", type="integer", example=69),
     *                                 @OA\Property(property="audio", type="string", nullable=true, example="/storage/audios/part_two_audio_1772507684_69a65224a5c98.mp3"),
     *                                 @OA\Property(property="advice", type="string", example="You will hear someone giving a talk."),
     *                                 @OA\Property(property="title", type="string", nullable=true, example=null),
     *                                 @OA\Property(property="text", type="string", example="School trip to farm\r\nMeeting place: 8 a.m. next to the (9)[9]"),
     *                                 @OA\Property(
     *                                     property="answers",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=384),
     *                                         @OA\Property(property="count_number", type="string", example="9"),
     *                                         @OA\Property(property="extra_answer", type="string", nullable=true, example=null),
     *                                         @OA\Property(property="correct_answer", type="string", example="gate"),
     *                                         @OA\Property(property="status", type="string", example="answered"),
     *                                         @OA\Property(property="user_answer", type="string", nullable=true, example="gate"),
     *                                         @OA\Property(property="is_correct", type="integer", nullable=true, example=1)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="question_data",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="user_answer", type="string", example="gate"),
     *                                         @OA\Property(property="correct_answer", type="string", example="gate"),
     *                                         @OA\Property(property="is_correct", type="boolean", example=true)
     *                                     )
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="part_three",
     *                                 nullable=true,
     *                                 type="object",
     *                                 description="Matching, speakers to places (A-F), TWO extra options",
     *                                 @OA\Property(property="id", type="integer", example=87),
     *                                 @OA\Property(property="advice", type="string", example="You will hear people speaking in different situations. Match each speaker (15-18) to the place where the speaker is (A-F). There are TWO EXTRA places which you do not need to use."),
     *                                 @OA\Property(property="audio", type="string", nullable=true, example="/storage/audios/part_three_audio_1772507684_69a65224a6c47.mp3"),
     *                                 @OA\Property(property="map", type="string", nullable=true, example=""),
     *                                 @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                                 @OA\Property(
     *                                     property="questions",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=374),
     *                                         @OA\Property(property="title", type="string", example="15. Speaker 1"),
     *                                         @OA\Property(property="correct_answer_id", type="integer", example=585)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="answers",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=585),
     *                                         @OA\Property(property="letter_key", type="string", example="0"),
     *                                         @OA\Property(property="answer", type="string", example="A) Why people go on excursion."),
     *                                         @OA\Property(property="is_selected", type="boolean", example=true)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="result_rows",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="question_id", type="integer", nullable=true, example=374),
     *                                         @OA\Property(property="answer_id", type="integer", nullable=true, example=585),
     *                                         @OA\Property(property="correct_answer_id", type="integer", nullable=true, example=585),
     *                                         @OA\Property(property="correct_answer", type="string", nullable=true, example="A) Why people go on excursion."),
     *                                         @OA\Property(property="is_correct", type="integer", example=1),
     *                                         @OA\Property(property="status", type="string", example="answered")
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="question_data",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="user_answer", type="string", example="A) Why people go on excursion."),
     *                                         @OA\Property(property="correct_answer", type="string", example="A) Why people go on excursion."),
     *                                         @OA\Property(property="is_correct", type="boolean", example=true)
     *                                     )
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="part_four",
     *                                 nullable=true,
     *                                 type="object",
     *                                 description="Map labelling, FOUR extra options",
     *                                 @OA\Property(property="id", type="integer", example=88),
     *                                 @OA\Property(property="advice", type="string", example="You will hear someone giving a talk. Label the places (19-23) on the map (A-F). There are FOUR extra options which you do not need to use."),
     *                                 @OA\Property(property="audio", type="string", nullable=true, example="/storage/audios/part_four_audio_1772514480_69a66cb03e859.mp3"),
     *                                 @OA\Property(property="map", type="string", nullable=true, example="public/maps/image_1772507760_69a652709962b.jpg"),
     *                                 @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                                 @OA\Property(
     *                                     property="questions",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=378),
     *                                         @OA\Property(property="title", type="string", example="19. The Reynolds House"),
     *                                         @OA\Property(property="correct_answer_id", type="integer", example=598)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="answers",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=591),
     *                                         @OA\Property(property="letter_key", type="string", example="0"),
     *                                         @OA\Property(property="answer", type="string", example="A"),
     *                                         @OA\Property(property="is_selected", type="boolean", example=false)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="result_rows",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="question_id", type="integer", nullable=true, example=378),
     *                                         @OA\Property(property="answer_id", type="integer", nullable=true, example=598),
     *                                         @OA\Property(property="correct_answer_id", type="integer", nullable=true, example=598),
     *                                         @OA\Property(property="correct_answer", type="string", nullable=true, example="H"),
     *                                         @OA\Property(property="is_correct", type="integer", example=0),
     *                                         @OA\Property(property="status", type="string", example="answered")
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="question_data",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="user_answer", type="string", example="Not answered"),
     *                                         @OA\Property(property="correct_answer", type="string", example="H"),
     *                                         @OA\Property(property="is_correct", type="boolean", example=false)
     *                                     )
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="part_five",
     *                                 nullable=true,
     *                                 type="object",
     *                                 description="Multiple choice (A/B/C) from extracts",
     *                                 @OA\Property(property="id", type="integer", example=73),
     *                                 @OA\Property(property="advice", type="string", example="You will hear three extracts."),
     *                                 @OA\Property(property="audio", type="string", nullable=true, example="/storage/audios/part_five_audio_1772507684_69a65224a7cf9.mp3"),
     *                                 @OA\Property(
     *                                     property="tests",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=436),
     *                                         @OA\Property(property="answer_1", type="string", example="her children enjoy listening to her read aloud."),
     *                                         @OA\Property(property="answer_2", type="string", example="she shares a reading habit with other parents."),
     *                                         @OA\Property(property="answer_3", type="string", example="parents should read aloud to children."),
     *                                         @OA\Property(property="correct_answer_key", type="string", example="answer_1"),
     *                                         @OA\Property(property="question", type="string", example="Extract One\r\n24. The second speaker says that she believes that"),
     *                                         @OA\Property(property="status", type="string", example="not_answered"),
     *                                         @OA\Property(property="user_answer_key", type="string", nullable=true, example=null),
     *                                         @OA\Property(property="correct_answer", type="string", nullable=true, example=null),
     *                                         @OA\Property(property="is_correct", type="integer", nullable=true, example=null)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="question_data",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="user_answer", type="string", example="Not answered"),
     *                                         @OA\Property(property="correct_answer", type="string", example="her children enjoy listening to her read aloud."),
     *                                         @OA\Property(property="is_correct", type="boolean", example=false)
     *                                     )
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="part_six",
     *                                 nullable=true,
     *                                 type="object",
     *                                 description="Fill in the blank from a lecture, write no more than one word",
     *                                 @OA\Property(property="id", type="integer", example=70),
     *                                 @OA\Property(property="audio", type="string", nullable=true, example="/storage/audios/part_six_audio_1772507684_69a65224a88a6.mp3"),
     *                                 @OA\Property(property="advice", type="string", example="You will hear a part of a lecture."),
     *                                 @OA\Property(property="title", type="string", nullable=true, example=null),
     *                                 @OA\Property(property="text", type="string", example="The ancient city of Cuzco\r\nThe words open-air (30)[30] are sometimes used to describe the city because of its historic buildings."),
     *                                 @OA\Property(
     *                                     property="answers",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=390),
     *                                         @OA\Property(property="count_number", type="string", example="30"),
     *                                         @OA\Property(property="extra_answer", type="string", nullable=true, example=null),
     *                                         @OA\Property(property="correct_answer", type="string", example="museum"),
     *                                         @OA\Property(property="status", type="string", example="not_answered"),
     *                                         @OA\Property(property="user_answer", type="string", nullable=true, example=null),
     *                                         @OA\Property(property="is_correct", type="integer", nullable=true, example=null)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="question_data",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="user_answer", type="string", example="Not answered"),
     *                                         @OA\Property(property="correct_answer", type="string", example="museum"),
     *                                         @OA\Property(property="is_correct", type="boolean", example=false)
     *                                     )
     *                                 )
     *                             )
     *                         )
     *                     }
     *                 ),
     *
     *                 @OA\Property(
     *                     property="readingData",
     *                     description="Empty array [] when the reading part was not saved for this full result. Otherwise identical to the 'data' object returned by GET /v2/reading/result/show/{id}.",
     *                     oneOf={
     *                         @OA\Schema(type="array", @OA\Items(type="string"), example={}),
     *                         @OA\Schema(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=154),
     *                             @OA\Property(property="reading_exercise_id", type="integer", example=91),
     *                             @OA\Property(property="name", type="string", example="Full Mock 1"),
     *                             @OA\Property(property="status", type="string", example="0"),
     *                             @OA\Property(property="active", type="string", nullable=true, example="part_one"),
     *                             @OA\Property(property="createdText", type="string", example="4 март, 08:16"),
     *                             @OA\Property(property="type", type="integer", example=1),
     *                             @OA\Property(
     *                                 property="percentageData",
     *                                 type="object",
     *                                 @OA\Property(property="percent", type="integer", example=6),
     *                                 @OA\Property(property="array", type="integer", example=289),
     *                                 @OA\Property(property="offset", type="integer", example=272),
     *                                 @OA\Property(property="mock_ball", type="integer", nullable=true, example=26),
     *                                 @OA\Property(property="degree", type="string", nullable=true, example="Below B1"),
     *                                 @OA\Property(property="is_mock", type="boolean", example=true)
     *                             ),
     *                             @OA\Property(property="part_category", type="string", nullable=true, example="full"),
     *                             @OA\Property(property="part_category_number", type="string", example="6"),
     *                             @OA\Property(property="difficulty", type="string", example="0"),
     *                             @OA\Property(property="time_limit", type="integer", example=60),
     *                             @OA\Property(property="all_answers", type="integer", example=35),
     *                             @OA\Property(property="correct_answers", type="integer", example=2),
     *                             @OA\Property(property="wrong_answers", type="integer", example=4),
     *                             @OA\Property(property="feedback", type="string", example="Your score is very low. You need to improve your basics."),
     *                             @OA\Property(property="suggestion", type="string", example="Focus on simple English listening and reading exercises daily"),
     *                             @OA\Property(
     *                                 property="result",
     *                                 type="object",
     *                                 @OA\Property(property="reading_result_id", type="integer", example=154),
     *                                 @OA\Property(property="all_answers", type="integer", example=35),
     *                                 @OA\Property(property="correct_answers", type="integer", example=2),
     *                                 @OA\Property(property="percent", type="integer", example=6),
     *                                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-05-29T08:16:00.000000Z")
     *                             ),
     *                             @OA\Property(
     *                                 property="parts_data",
     *                                 type="object",
     *                                 @OA\Property(property="part_one", type="string", example="Part One"),
     *                                 @OA\Property(property="part_two", type="string", example="Part Two"),
     *                                 @OA\Property(property="part_three", type="string", example="Part Three"),
     *                                 @OA\Property(property="part_four", type="string", example="Part Four"),
     *                                 @OA\Property(property="part_five", type="string", example="Part Five")
     *                             ),
     *                             @OA\Property(
     *                                 property="part_one",
     *                                 nullable=true,
     *                                 type="object",
     *                                 description="Fill in the blank, use a word from the text",
     *                                 @OA\Property(property="id", type="integer", example=48),
     *                                 @OA\Property(property="advice", type="string", example="Read the text and fill in the missing information."),
     *                                 @OA\Property(property="title", type="string", nullable=true, example=null),
     *                                 @OA\Property(property="text", type="string", example="Black Friday in the United States\nBlack Friday is the day after Thanksgiving..."),
     *                                 @OA\Property(
     *                                     property="answers",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=178),
     *                                         @OA\Property(property="count_number", type="string", example="1"),
     *                                         @OA\Property(property="extra_answer", type="string", nullable=true, example=null),
     *                                         @OA\Property(property="correct_answer", type="string", example="holiday"),
     *                                         @OA\Property(property="status", type="string", example="answered"),
     *                                         @OA\Property(property="user_answer", type="string", nullable=true, example="holiday"),
     *                                         @OA\Property(property="is_correct", type="integer", nullable=true, example=1)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="question_data",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="user_answer", type="string", example="holiday"),
     *                                         @OA\Property(property="correct_answer", type="string", example="holiday"),
     *                                         @OA\Property(property="is_correct", type="boolean", example=true)
     *                                     )
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="part_two",
     *                                 nullable=true,
     *                                 type="object",
     *                                 description="Matching texts to statements (A-J), TWO extras unused",
     *                                 @OA\Property(property="id", type="integer", example=69),
     *                                 @OA\Property(property="advice", type="string", example="Match the texts with the correct answers."),
     *                                 @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                                 @OA\Property(
     *                                     property="texts",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=364),
     *                                         @OA\Property(property="text", type="string", example="7. HELP, snack bar serving person\nBright, friendly, experience not essential..."),
     *                                         @OA\Property(property="correct_answer_id", type="integer", example=436)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="answers",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=434),
     *                                         @OA\Property(property="letter_key", type="string", example="A"),
     *                                         @OA\Property(property="answer", type="string", example="A) a person with two small children who wants a few hours of unskilled work in the early mornings"),
     *                                         @OA\Property(property="is_selected", type="boolean", example=true)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="question_data",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="user_answer", type="string", example="A) a person with two small children who wants a few hours of unskilled work in the early mornings"),
     *                                         @OA\Property(property="correct_answer", type="string", example="A) a person with two small children who wants a few hours of unskilled work in the early mornings"),
     *                                         @OA\Property(property="is_correct", type="boolean", example=true)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="result_rows",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="text_id", type="integer", nullable=true, example=364),
     *                                         @OA\Property(property="answer_id", type="integer", nullable=true, example=434),
     *                                         @OA\Property(property="correct_answer_id", type="integer", nullable=true, example=436),
     *                                         @OA\Property(property="correct_answer", type="string", nullable=true, example="A) a person with two small children who wants a few hours of unskilled work in the early mornings"),
     *                                         @OA\Property(property="is_correct", type="integer", nullable=true, example=1),
     *                                         @OA\Property(property="status", type="string", example="answered")
     *                                     )
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="part_three",
     *                                 nullable=true,
     *                                 type="object",
     *                                 description="Matching paragraph headings to passages",
     *                                 @OA\Property(property="id", type="integer", example=70),
     *                                 @OA\Property(property="advice", type="string", example="Choose the correct heading for each paragraph."),
     *                                 @OA\Property(property="letter_array", type="array", @OA\Items(type="string", example="A")),
     *                                 @OA\Property(
     *                                     property="texts",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=372),
     *                                         @OA\Property(property="text", type="string", example="Plastic is no longer fantastic\nI In 2017, Carlos Ferrando saw a piece of art..."),
     *                                         @OA\Property(property="correct_answer_id", type="integer", example=449)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="answers",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=444),
     *                                         @OA\Property(property="letter_key", type="string", example="I"),
     *                                         @OA\Property(property="answer", type="string", example="I) A time when opportunities were limited"),
     *                                         @OA\Property(property="is_selected", type="boolean", example=false)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="question_data",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="user_answer", type="string", example="Not answered"),
     *                                         @OA\Property(property="correct_answer", type="string", example="I) A time when opportunities were limited"),
     *                                         @OA\Property(property="is_correct", type="boolean", example=false)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="result_rows",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="text_id", type="integer", nullable=true, example=372),
     *                                         @OA\Property(property="answer_id", type="integer", nullable=true, example=null),
     *                                         @OA\Property(property="correct_answer_id", type="integer", nullable=true, example=449),
     *                                         @OA\Property(property="correct_answer", type="string", nullable=true, example="I) A time when opportunities were limited"),
     *                                         @OA\Property(property="is_correct", type="integer", nullable=true, example=0),
     *                                         @OA\Property(property="status", type="string", example="not_answered")
     *                                     )
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="part_four",
     *                                 nullable=true,
     *                                 type="object",
     *                                 description="True/False/Not Given + Multiple choice (4 options) from a long text",
     *                                 @OA\Property(property="id", type="integer", example=22),
     *                                 @OA\Property(property="advice", type="string", example="Read the following text for questions."),
     *                                 @OA\Property(property="text", type="string", example="Cultural behavior in business\nMuch of today's business is conducted across international borders..."),
     *                                 @OA\Property(
     *                                     property="TFNg_tests",
     *                                     type="array",
     *                                     description="True / False / Not Given questions",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=124),
     *                                         @OA\Property(property="question", type="string", example="21. When doing business internationally, there is a possibility that we might misinterpret each other even speaking the same language."),
     *                                         @OA\Property(property="answer_1", type="string", example="True"),
     *                                         @OA\Property(property="answer_2", type="string", example="False"),
     *                                         @OA\Property(property="answer_3", type="string", example="Not Given"),
     *                                         @OA\Property(property="correct_answer_key", type="string", example="answer_3"),
     *                                         @OA\Property(property="status", type="string", example="answered"),
     *                                         @OA\Property(property="user_answer_key", type="string", nullable=true, example="answer_1"),
     *                                         @OA\Property(property="correct_answer", type="string", nullable=true, example="Not Given"),
     *                                         @OA\Property(property="is_correct", type="integer", nullable=true, example=0)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="MCHQ_tests",
     *                                     type="array",
     *                                     description="Multiple choice (4 options) questions",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=129),
     *                                         @OA\Property(property="question", type="string", example="26. There might be a misunderstanding if an American smiles at a Russian because the Russian might think that the American is ..."),
     *                                         @OA\Property(property="answer_1", type="string", example="being fake"),
     *                                         @OA\Property(property="answer_2", type="string", example="challenge their authority"),
     *                                         @OA\Property(property="answer_3", type="string", example="trying to break the ice"),
     *                                         @OA\Property(property="answer_4", type="string", example="disrespectful"),
     *                                         @OA\Property(property="correct_answer_key", type="string", example="answer_2"),
     *                                         @OA\Property(property="status", type="string", example="not_answered"),
     *                                         @OA\Property(property="user_answer_key", type="string", nullable=true, example=null),
     *                                         @OA\Property(property="correct_answer", type="string", nullable=true, example=null),
     *                                         @OA\Property(property="is_correct", type="integer", nullable=true, example=null)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="question_data",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="user_answer", type="string", example="True"),
     *                                         @OA\Property(property="correct_answer", type="string", example="Not Given"),
     *                                         @OA\Property(property="is_correct", type="boolean", example=false)
     *                                     )
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="part_five",
     *                                 nullable=true,
     *                                 type="object",
     *                                 description="Fill in blank + Multiple choice (4 options) from a biography text",
     *                                 @OA\Property(property="id", type="integer", example=49),
     *                                 @OA\Property(property="advice", type="string", example="Read the text and answer both input and test questions."),
     *                                 @OA\Property(property="reading_text", type="string", example="A biography of Kilian Jornet\nWhen you picture mountain climbers scaling Mount Everest..."),
     *                                 @OA\Property(property="question_text", type="string", example="30. It's normal to find it hard to breathe at [30] meters above sea level."),
     *                                 @OA\Property(
     *                                     property="answers",
     *                                     type="array",
     *                                     description="Fill-in-the-blank answers",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=184),
     *                                         @OA\Property(property="count_number", type="string", example="30"),
     *                                         @OA\Property(property="extra_answer", type="string", nullable=true, example=null),
     *                                         @OA\Property(property="correct_answer", type="string", example="3500"),
     *                                         @OA\Property(property="status", type="string", example="answered"),
     *                                         @OA\Property(property="user_answer", type="string", nullable=true, example="3500"),
     *                                         @OA\Property(property="is_correct", type="integer", nullable=true, example=1)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="tests",
     *                                     type="array",
     *                                     description="Multiple choice (4 options) questions",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=18),
     *                                         @OA\Property(property="question", type="string", example="34. The majority of climbers on Everest ..."),
     *                                         @OA\Property(property="answer_1", type="string", example="need oxygen to finish their ascent"),
     *                                         @OA\Property(property="answer_2", type="string", example="are accompanied"),
     *                                         @OA\Property(property="answer_3", type="string", example="make slow progress to the top"),
     *                                         @OA\Property(property="answer_4", type="string", example="all of the above"),
     *                                         @OA\Property(property="correct_answer_key", type="string", example="answer_4"),
     *                                         @OA\Property(property="status", type="string", example="not_answered"),
     *                                         @OA\Property(property="user_answer_key", type="string", nullable=true, example=null),
     *                                         @OA\Property(property="correct_answer", type="string", nullable=true, example=null),
     *                                         @OA\Property(property="is_correct", type="integer", nullable=true, example=null)
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="question_data",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="user_answer", type="string", example="3500"),
     *                                         @OA\Property(property="correct_answer", type="string", example="3500"),
     *                                         @OA\Property(property="is_correct", type="boolean", example=true)
     *                                     )
     *                                 )
     *                             )
     *                         )
     *                     }
     *                 ),
     *
     *                 @OA\Property(
     *                     property="writingData",
     *                     description="Empty array [] when the writing part was not saved for this full result. Otherwise an object generated by GetWritingResultData(). NOTE: unlike GET /v2/writing/result/{id} (which uses the *Api variant and returns raw text with {correction}[mistake] markers plus flat writing_result_id/category_type fields), this block nests the full 'writingResult' model and renders corrected text as HTML (<span> tags) inside task_one.text_1/text_2 and task_two.text.",
     *                     oneOf={
     *                         @OA\Schema(type="array", @OA\Items(type="string"), example={}),
     *                         @OA\Schema(
     *                             type="object",
     *                             @OA\Property(
     *                                 property="writingResult",
     *                                 type="object",
     *                                 description="Raw WritingResult model, eager loaded with part_one_answer, part_two_answer and exercise.task_one/task_two relations",
     *                                 @OA\Property(property="id", type="integer", example=55),
     *                                 @OA\Property(property="category_type", type="string", example="3"),
     *                                 @OA\Property(property="text_1", type="string", example="Task 1 first answer with detailed content ..."),
     *                                 @OA\Property(property="text_2", type="string", example="Task 2 essay answer with detailed content ..."),
     *                                 @OA\Property(property="writing_exercise_id", type="integer", example=31),
     *                                 @OA\Property(property="user_id", type="integer", example=7),
     *                                 @OA\Property(property="status", type="integer", example=1),
     *                                 @OA\Property(property="with_ai", type="integer", example=1),
     *                                 @OA\Property(property="score", type="number", format="float", nullable=true, example=null),
     *                                 @OA\Property(property="max_score", type="integer", example=53),
     *                                 @OA\Property(property="ball", type="integer", nullable=true, example=null),
     *                                 @OA\Property(property="degree", type="string", nullable=true, example=null),
     *                                 @OA\Property(property="ai_check_price", type="number", format="float", nullable=true, example=1500),
     *                                 @OA\Property(property="uzs_ai_check_price", type="number", format="float", nullable=true, example=22500),
     *                                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-06-26T09:10:11.000000Z"),
     *                                 @OA\Property(property="updated_at", type="string", format="date-time", example="2026-06-26T09:15:21.000000Z"),
     *                                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null),
     *                                 @OA\Property(
     *                                     property="part_one_answer",
     *                                     type="object",
     *                                     nullable=true,
     *                                     @OA\Property(property="id", type="integer", example=16),
     *                                     @OA\Property(property="text_1", type="string", example="Task 1 first answer raw text ..."),
     *                                     @OA\Property(property="text_2", type="string", example="Task 1 second answer raw text ..."),
     *                                     @OA\Property(property="writing_result_id", type="integer", example=55),
     *                                     @OA\Property(property="score_1", type="number", format="float", nullable=true, example=4),
     *                                     @OA\Property(property="score_2", type="number", format="float", nullable=true, example=4),
     *                                     @OA\Property(property="result_1_json", type="string", nullable=true, description="JSON string holding the AI check result for text_1 (task_response, description, reGeneratedText keys)", example="json string of AI check result"),
     *                                     @OA\Property(property="result_2_json", type="string", nullable=true, description="JSON string holding the AI check result for text_2 (task_response, description, reGeneratedText keys)", example="json string of AI check result"),
     *                                     @OA\Property(property="max_score", type="integer", example=5)
     *                                 ),
     *                                 @OA\Property(
     *                                     property="part_two_answer",
     *                                     type="object",
     *                                     nullable=true,
     *                                     @OA\Property(property="id", type="integer", example=12),
     *                                     @OA\Property(property="text", type="string", example="Task 2 essay raw text ..."),
     *                                     @OA\Property(property="writing_result_id", type="integer", example=55),
     *                                     @OA\Property(property="score", type="number", format="float", nullable=true, example=4),
     *                                     @OA\Property(property="result_json", type="string", nullable=true, description="JSON string holding the AI check result for the task 2 essay (task_response, description, reGeneratedText keys)", example="json string of AI check result"),
     *                                     @OA\Property(property="max_score", type="integer", example=6)
     *                                 ),
     *                                 @OA\Property(
     *                                     property="exercise",
     *                                     type="object",
     *                                     description="The full WritingExercise this result belongs to, with task_one/task_two relations loaded",
     *                                     @OA\Property(property="id", type="integer", example=31),
     *                                     @OA\Property(property="name", type="string", example="Full Mock 1"),
     *                                     @OA\Property(property="part_category", type="string", example="3"),
     *                                     @OA\Property(property="status", type="string", example="0"),
     *                                     @OA\Property(
     *                                         property="task_one",
     *                                         type="object",
     *                                         nullable=true,
     *                                         @OA\Property(property="id", type="integer", example=16),
     *                                         @OA\Property(property="theme", type="string", example="You have just returned from a holiday. You are unhappy with the hotel quality."),
     *                                         @OA\Property(property="requirement", type="string", example="Write a letter to the manager of the travel agency."),
     *                                         @OA\Property(property="advice", type="string", example="write at least 150 words"),
     *                                         @OA\Property(property="time_limit", type="integer", example=20),
     *                                         @OA\Property(property="word_count", type="integer", nullable=true, example=150),
     *                                         @OA\Property(property="example_text", type="string", example="Dear Sir or Madam,\nI understand that no business likes to hear these kinds of things..."),
     *                                         @OA\Property(property="bullet_point_1", type="string", example="say about the holiday and where you stayed"),
     *                                         @OA\Property(property="bullet_point_2", type="string", example="mention why you are unhappy about the hotel"),
     *                                         @OA\Property(property="bullet_point_3", type="string", example="say what you would like the travel agency to do"),
     *                                         @OA\Property(property="writing_exercise_id", type="integer", example=31)
     *                                     ),
     *                                     @OA\Property(
     *                                         property="task_two",
     *                                         type="object",
     *                                         nullable=true,
     *                                         @OA\Property(property="id", type="integer", example=12),
     *                                         @OA\Property(property="theme", type="string", example="Many products contain high levels of sugar which causes health problems. Sugary products should be made more expensive. Do you agree or disagree?"),
     *                                         @OA\Property(property="requirement", type="string", example="at least 250"),
     *                                         @OA\Property(property="advice", type="string", example="at least 250"),
     *                                         @OA\Property(property="writing_exercise_id", type="integer", example=31),
     *                                         @OA\Property(property="time_limit", type="integer", example=40),
     *                                         @OA\Property(property="word_count", type="integer", nullable=true, example=250),
     *                                         @OA\Property(property="example_text", type="string", example="Sugar is undoubtedly one of the most harmful food ingredients when consumed more than recommended...")
     *                                     )
     *                                 )
     *                             ),
     *                             @OA\Property(property="type", type="integer", example=2),
     *                             @OA\Property(property="status", type="integer", example=1, description="1 only when with_ai=1 and AI check is finished"),
     *                             @OA\Property(
     *                                 property="tabs",
     *                                 type="object",
     *                                 @OA\Property(property="task_one", type="string", example="Task One"),
     *                                 @OA\Property(property="task_two", type="string", example="Task Two")
     *                             ),
     *                             @OA\Property(property="active", type="string", example="task_one"),
     *                             @OA\Property(
     *                                 property="task_one",
     *                                 type="object",
     *                                 nullable=true,
     *                                 @OA\Property(
     *                                     property="text_1",
     *                                     type="string",
     *                                     description="HTML formatted (<span class='text-green-600 underline font-medium'> for corrections, <span class='text-red-500 line-through mx-1'> for mistakes, wrapped in <p> per paragraph). Plain text (no spans) when status=0/not yet AI checked.",
     *                                     example="<p class='mb-4 leading-7'>Hi Aziz,<br>I am thinking about the next Travel Club trip. I would like to visit Istanbul because it <span class='text-green-600 underline font-medium'>has</span> many beautiful places.</p>"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="text_2",
     *                                     type="string",
     *                                     description="Same HTML formatting rules as text_1",
     *                                     example="<p class='mb-4 leading-7'>Dear Travel Club Organizer,<br>Thank you for <span class='text-green-600 underline font-medium'>asking for our opinions</span> about the next trip.</p>"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="task_response",
     *                                     type="object",
     *                                     @OA\Property(property="task_achievement", type="string", example="Task 1.1\nThe email answers the prompts.\nTask 1.2\nThe response fully addresses the prompt."),
     *                                     @OA\Property(property="lexical_resource", type="string", example="Task 1.1\nVocabulary is adequate and appropriate.\nTask 1.2\nVocabulary is appropriate and varied enough."),
     *                                     @OA\Property(property="coherence_and_cohesion", type="string", example="Task 1.1\nOrganized logically with simple linking devices.\nTask 1.2\nIdeas are well organized into clear paragraphs."),
     *                                     @OA\Property(property="grammatical_range_and_accuracy", type="string", example="Task 1.1\nUses simple grammatical structures mostly accurately.\nTask 1.2\nThe candidate uses a range of sentence structures.")
     *                                 ),
     *                                 @OA\Property(property="description_1", type="string", example="Covers all task requirements and communicates clearly, with some grammatical errors typical of Upper B1 level."),
     *                                 @OA\Property(property="description_2", type="string", example="A relevant, well-structured email that meets the task requirements with minor grammatical and word-choice errors that do not impede understanding."),
     *                                 @OA\Property(property="question", type="string", example="You are a member of the Travel Club at your language center. You received an email from the club organizer."),
     *                                 @OA\Property(
     *                                     property="resultData",
     *                                     nullable=true,
     *                                     type="object",
     *                                     @OA\Property(property="score_1", type="string", example="4"),
     *                                     @OA\Property(property="score_2", type="string", example="4"),
     *                                     @OA\Property(property="max_score_1", type="integer", example=5),
     *                                     @OA\Property(property="max_score_2", type="integer", example=5),
     *                                     @OA\Property(property="ball", type="integer", example=47),
     *                                     @OA\Property(property="max_ball", type="integer", example=53)
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="task_two",
     *                                 type="object",
     *                                 nullable=true,
     *                                 @OA\Property(
     *                                     property="text",
     *                                     type="string",
     *                                     description="HTML formatted, same rules as task_one.text_1",
     *                                     example="<p class='mb-4 leading-7'>In my opinion, social media have both positive and negative effects on young people. However, I think <span class='text-green-600 underline font-medium'>they</span> can be more useful than harmful.</p>"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="task_response",
     *                                     type="object",
     *                                     @OA\Property(property="task_achievement", type="string", example="The task is logically completed: the issue is analyzed, an opinion is expressed, and examples are given."),
     *                                     @OA\Property(property="grammatical_range_and_accuracy", type="string", example="Grammatical forms are used with their main features, but numerous errors are present."),
     *                                     @OA\Property(property="lexical_resource", type="string", example="Vocabulary is sufficient; some words are used incorrectly."),
     *                                     @OA\Property(property="coherence_and_cohesion", type="string", example="The text is logically organized and paragraph structure is maintained.")
     *                                 ),
     *                                 @OA\Property(property="description", type="string", example="Clear position and relevant arguments with good coherence, but noticeable grammatical and lexical errors prevent a higher score."),
     *                                 @OA\Property(property="question", type="string", example="You are participating in an online discussion for language learners."),
     *                                 @OA\Property(
     *                                     property="resultData",
     *                                     nullable=true,
     *                                     type="object",
     *                                     @OA\Property(property="score", type="string", example="4"),
     *                                     @OA\Property(property="max_score", type="integer", example=6),
     *                                     @OA\Property(property="ball", type="integer", example=12),
     *                                     @OA\Property(property="max_ball", type="integer", example=22)
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="percentageData",
     *                                 type="object",
     *                                 @OA\Property(property="percent", type="integer", example=81),
     *                                 @OA\Property(property="array", type="integer", example=289),
     *                                 @OA\Property(property="offset", type="integer", example=55),
     *                                 @OA\Property(property="degree", type="string", example="B2")
     *                             ),
     *                             @OA\Property(property="createdText", type="string", example="30 май, 11:57")
     *                         )
     *                     }
     *                 ),
     *
     *                 @OA\Property(
     *                     property="speakingData",
     *                     description="Empty array [] when the speaking part was not saved for this full result. Otherwise identical to the 'data' object returned by GET /v2/speaking/result/{id}.",
     *                     oneOf={
     *                         @OA\Schema(type="array", @OA\Items(type="string"), example={}),
     *                         @OA\Schema(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=91),
     *                             @OA\Property(
     *                                 property="speakingResult",
     *                                 type="object",
     *                                 description="Raw SpeakingResult model, eager loaded with part_one_first.answers, part_one_second.answers, part_two, part_three and exercise relations",
     *                                 @OA\Property(property="id", type="integer", example=91),
     *                                 @OA\Property(property="user_id", type="integer", example=7),
     *                                 @OA\Property(property="category_type", type="string", example="4"),
     *                                 @OA\Property(property="speaking_exercise_id", type="integer", example=118),
     *                                 @OA\Property(property="audio_path", type="string", nullable=true, example=null),
     *                                 @OA\Property(property="with_ai", type="integer", example=1),
     *                                 @OA\Property(property="status", type="integer", example=1),
     *                                 @OA\Property(property="score", type="number", format="float", nullable=true, example=18.5),
     *                                 @OA\Property(property="max_score", type="integer", example=25),
     *                                 @OA\Property(property="ai_check_price", type="number", format="float", nullable=true, example=1500),
     *                                 @OA\Property(property="uzs_ai_check_price", type="number", format="float", nullable=true, example=22500),
     *                                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-06-26T09:10:11.000000Z"),
     *                                 @OA\Property(property="updated_at", type="string", format="date-time", example="2026-06-26T09:15:21.000000Z"),
     *                                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
     *                             ),
     *                             @OA\Property(property="createdText", type="string", example="31 май, 14:15"),
     *                             @OA\Property(
     *                                 property="tabs",
     *                                 type="object",
     *                                 @OA\Property(property="part_one_first", type="string", example="Part One"),
     *                                 @OA\Property(property="part_one_second", type="string", example="Part One Follow-up"),
     *                                 @OA\Property(property="part_two", type="string", example="Part Two"),
     *                                 @OA\Property(property="part_three", type="string", example="Part Three")
     *                             ),
     *                             @OA\Property(property="active", type="string", example="part_one_first"),
     *                             @OA\Property(property="status", type="integer", example=1, description="1 only when with_ai=1 and AI check is finished"),
     *                             @OA\Property(
     *                                 property="percentageData",
     *                                 type="object",
     *                                 @OA\Property(property="percent", type="integer", example=72),
     *                                 @OA\Property(property="array", type="integer", example=289),
     *                                 @OA\Property(property="offset", type="integer", example=81),
     *                                 @OA\Property(property="ball", type="integer", example=18),
     *                                 @OA\Property(property="degree", type="string", nullable=true, example="B1")
     *                             ),
     *                             @OA\Property(
     *                                 property="part_one_first",
     *                                 nullable=true,
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=33),
     *                                 @OA\Property(property="title", type="string", example="Part One"),
     *                                 @OA\Property(
     *                                     property="questions",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=1001),
     *                                         @OA\Property(property="question", type="string", example="Tell me about your hometown."),
     *                                         @OA\Property(property="answer_text", type="string", example="<p>My hometown is...</p>"),
     *                                         @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/admin/speaking/result/audio/ex_91_res_part_1first_q_1001_abcd1234.webm")
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="resultData",
     *                                     type="object",
     *                                     @OA\Property(property="score", type="string", example="4.5"),
     *                                     @OA\Property(property="max_score", type="integer", example=5),
     *                                     @OA\Property(property="description", type="string", example="Good pronunciation and clear ideas.")
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="part_one_second",
     *                                 nullable=true,
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=34),
     *                                 @OA\Property(property="title", type="string", example="Part One Follow-up"),
     *                                 @OA\Property(
     *                                     property="questions",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=2001),
     *                                         @OA\Property(property="question", type="string", example="Describe what you can see."),
     *                                         @OA\Property(property="answer_text", type="string", example="<p>I can see...</p>"),
     *                                         @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/admin/speaking/result/audio/ex_91_res_part_1_second_q_2001_qwert123.webm")
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="images",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=11),
     *                                         @OA\Property(property="url", type="string", example="/storage/speaking_images/picture_1.jpg")
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="resultData",
     *                                     type="object",
     *                                     @OA\Property(property="score", type="string", example="4"),
     *                                     @OA\Property(property="max_score", type="integer", example=5),
     *                                     @OA\Property(property="description", type="string", example="Vocabulary is acceptable but limited.")
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="part_two",
     *                                 nullable=true,
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=41),
     *                                 @OA\Property(property="title", type="string", example="Part Two"),
     *                                 @OA\Property(
     *                                     property="question",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer", example=3001),
     *                                     @OA\Property(property="question", type="string", example="Talk about a memorable trip."),
     *                                     @OA\Property(property="answer_text", type="string", example="<p>Last year I visited...</p>"),
     *                                     @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/admin/speaking/result/audio/ex_91_res_part_2_q_3001_fghj5678.webm")
     *                                 ),
     *                                 @OA\Property(
     *                                     property="images",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=21),
     *                                         @OA\Property(property="url", type="string", example="/storage/speaking_images/travel.jpg")
     *                                     )
     *                                 ),
     *                                 @OA\Property(
     *                                     property="resultData",
     *                                     type="object",
     *                                     @OA\Property(property="score", type="string", example="5"),
     *                                     @OA\Property(property="max_score", type="integer", example=7),
     *                                     @OA\Property(property="description", type="string", example="Well organized answer with relevant detail.")
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="part_three",
     *                                 nullable=true,
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=51),
     *                                 @OA\Property(property="title", type="string", example="Part Three"),
     *                                 @OA\Property(property="theme", type="string", nullable=true, example="Education"),
     *                                 @OA\Property(property="question_text", type="string", nullable=true, example="FOR: online learning is flexible\nAGAINST: it reduces interaction"),
     *                                 @OA\Property(
     *                                     property="forAgainstData",
     *                                     type="object",
     *                                     @OA\Property(property="forDetails", type="string", example="<ul><li>online learning is flexible</li></ul>"),
     *                                     @OA\Property(property="againtsDetails", type="string", example="<ul><li>it reduces interaction</li></ul>")
     *                                 ),
     *                                 @OA\Property(property="answer_text", type="string", nullable=true, example="<p>I believe both sides have valid points...</p>"),
     *                                 @OA\Property(property="audio_link", type="string", nullable=true, example="http://127.0.0.1:8000/admin/speaking/result/audio/ex_91_res_part_3_q_4001_zxcv9876.webm"),
     *                                 @OA\Property(
     *                                     property="resultData",
     *                                     type="object",
     *                                     @OA\Property(property="score", type="string", example="5"),
     *                                     @OA\Property(property="max_score", type="integer", example=8),
     *                                     @OA\Property(property="description", type="string", example="Balanced discussion with good coherence.")
     *                                 )
     *                             ),
     *                             @OA\Property(
     *                                 property="feedbackCategories",
     *                                 type="array",
     *                                 @OA\Items(type="string", example="Pronunciation")
     *                             )
     *                         )
     *                     }
     *                 ),
     *
     *                 @OA\Property(
     *                     property="testTypes",
     *                     type="array",
     *                     description="List of skills that were actually saved for this full result, in Listening/Reading/Writing/Speaking order",
     *                     @OA\Items(type="string", enum={"Listening","Reading","Writing","Speaking"}, example="Listening")
     *                 ),
     *                 @OA\Property(property="type", type="integer", example=0, description="Hardcoded to 0 by this endpoint")
     *             ),
     *             @OA\Property(property="message", type="string", example="full mock result")
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
    public function show($id,FullMockService $fullMockService)
    {
        $data=$fullMockService->getFullMockResultData($id,0);


        return $this->success($data,'full mock result');
    }

    public function reset($id,FullMockService $fullMockService)
    {
        $user=auth()->user();

        $resultExists=FullResult::where('full_exercise_id',$id)
            ->where('status',0)
            ->where('user_id',$user->id)
            ->orderByDesc('id')
            ->first();

        if ($resultExists){
            $resultExists->status=1;
            $resultExists->save();
        }

        return $this->success($fullMockService->getFullResultData($id), 'full result saqlandi');

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
