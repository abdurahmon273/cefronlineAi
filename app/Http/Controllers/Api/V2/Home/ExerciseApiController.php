<?php

namespace App\Http\Controllers\Api\V2\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Listening\ExerciseResource;
use App\Models\Exercise;
use App\Models\PremiumToUsers;
use App\Services\PremiumApiService;
use Illuminate\Http\Request;

class ExerciseApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/v2/listening/exercises",
     *     operationId="getV2ListeningExercises",
     *     tags={"Listening API"},
     *     summary="Get listening exercises",
     *     description="Returns paginated listening exercises.",
     *     security={{"bearerAuth":{}}},
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
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=92),
     *                     @OA\Property(property="title", type="string", example="Tushgan savollar"),
     *                     @OA\Property(property="exercise_count", type="integer", example=7),
     *                     @OA\Property(
     *                         property="listening_exercises",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=419),
     *                             @OA\Property(property="name", type="string", example="Map"),
     *                             @OA\Property(property="part_category_number", type="string", example="4"),
     *                             @OA\Property(property="status", type="string", example="1"),
     *                             @OA\Property(property="exercise_id", type="integer", example=92)
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/listening/exercises?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example=null),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/listening/exercises?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/listening/exercises"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", example=10)
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
     *     )
     * )
     */
    public function index(Request $request)
    {
        if ($request->device_id && auth()->user()) {
            if (auth()->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }

        $exercises = Exercise::with(['listening_exercises'=>function ($query) {
            return $query->select('id','exercise_id','name','part_category','status')->orderBy('order_id');
        }])
            ->where('category_type','0')->orderBy('order_id')
            ->simplePaginate(10);

        return ExerciseResource::collection($exercises);
    }

    /**
     * @OA\Get(
     *     path="/v2/reading/exercises",
     *     operationId="getV2ReadingExercises",
     *     tags={"Reading API"},
     *     summary="Get reading exercises",
     *     description="Returns paginated reading exercises grouped by category.",
     *     security={{"bearerAuth":{}}},
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
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=24),
     *                     @OA\Property(property="title", type="string", example="Reading part 1"),
     *                     @OA\Property(property="exercise_count", type="integer", example=12),
     *                     @OA\Property(
     *                         property="reading_exercises",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=101),
     *                             @OA\Property(property="name", type="string", example="Reading Test 1"),
     *                             @OA\Property(property="part_category_number", type="string", example="1"),
     *                             @OA\Property(property="status", type="string", example="0"),
     *                             @OA\Property(property="exercise_id", type="integer", example=24)
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/reading/exercises?page=1"),
     *                 @OA\Property(property="last", type="string", nullable=true, example=null),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", nullable=true, example="http://127.0.0.1:8000/api/v2/reading/exercises?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/api/v2/reading/exercises"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", nullable=true, example=10)
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
     *     )
     * )
     */
    public function reading(Request $request)
    {
        if ($request->device_id && auth()->user()) {
            if (auth()->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401); // Adding 401 status code
            }
        }

        $exercises = Exercise::with(['reading_exercises'=>function ($query) {
                return $query->select('id','exercise_id','name','part_category','status')->orderBy('order_id');
            }])
            ->where('category_type','1')
            ->orderBy('order_id')
            ->simplePaginate(10);

        return ExerciseResource::collection($exercises);
    }

    // Swagger docs for GET /v2/full/exercises live in FullExam\FullExerciseApiController
    // (grouped and ordered under the "Full Exam API" tag).
    public function full_index(Request $request)
    {
        if ($request->device_id && auth('sanctum')->user()) {
            if (auth('sanctum')->user()->device_id != $request->device_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Undeviceided',
                ], 401);
            }
        }


        $exercises = Exercise::with(['full_exercise'=>function ($query) {
            return $query->select(
                'id','exercise_id',
                'title','status',
                'listening_exercise_id',
                'reading_exercise_id',
                'writing_exercise_id',
                'speaking_exercise_id')
                ->orderBy('order_id');
        }])
            ->where('category_type','4')
            ->orderBy('order_id')
            ->get();

        return ExerciseResource::collection($exercises);
    }

}
