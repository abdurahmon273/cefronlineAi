<?php

namespace App\Http\Controllers;

use App\Models\FullExercise;
use App\Models\ListeningExercise;
use App\Models\ReadingExercise;
use App\Models\SpeakingExercise;
use App\Models\WritingExercise;
use App\Models\WritingTaskOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FullExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        return view('admin.full_mock.full_exercises.index', compact('id'));
    }
    public function index1($id)
    {
        $full_exercise=FullExercise::find($id);
        return view('admin.full_mock.full_more_exercises.index', compact('full_exercise'));
    }
    public function listening_index($id)
    {
        $listening_exercise=ListeningExercise::with('part_one')->find($id);
        return view('admin.full_mock.listening.index', compact('listening_exercise'));
    }
    public function reading_index($id)
    {
        $reading_exercise=ReadingExercise::with('part_one')->find($id);
        return view('admin.full_mock.reading.index', compact('reading_exercise'));
    }
    public function speaking_index($id)
    {
        $speaking_exercise=SpeakingExercise::with('part_one_first')->find($id);
        return view('admin.full_mock.speaking.index', compact('speaking_exercise'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        return view('admin.full_mock.full_exercises.create', compact('id'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'required|min:3',
            'status'=>'required',
//            'listening_title'=>'required|min:3',
//            'reading_title'=>'required|min:3',
//            'writing_title'=>'required|min:3',
//            'speaking_title'=>'required|min:3',
            'difficulty'=>'required',
        ]);
        $listening_exercise=ListeningExercise::create([
            'name'=>$request->title,
            'difficulty'=>$request->difficulty,
            'part_category'=>'7',
            'status'=>$request->status,
            'time_limit'=>35,
            'exercise_id'=>$request->exercise_id,
        ]);

        $reading_exercise=ReadingExercise::create([
            'name'=>$request->title,
            'part_category'=>'6',
            'status'=>$request->status,
            'difficulty'=>$request->difficulty,
            'time_limit'=>70,
            'exercise_id'=>$request->exercise_id,
        ]);

        $writing_exercise=WritingExercise::create([
            'name'=>$request->title,
            'part_category'=>'3',
            'status'=>$request->status,
            'exercise_id'=>$request->exercise_id,
        ]);

        $speaking_exercise=SpeakingExercise::create([
            'title'=>$request->title,
            'part_category'=>'4',
            'status'=>$request->status,
            'exercise_id'=>$request->exercise_id,
        ]);



        $fullExercise= FullExercise::create([
            'title'=>$request->title,
            'status'=>$request->status,
            'exercise_id'=>$request->exercise_id,
            'listening_exercise_id'=>$listening_exercise->id,
            'reading_exercise_id'=>$reading_exercise->id,
            'writing_exercise_id'=>$writing_exercise->id,
            'speaking_exercise_id'=>$speaking_exercise->id,
        ]);

        return redirect()->route('admin.full_exercise.index',['id'=>$fullExercise->exercise_id] );

    }

    /**
     * Display the specified resource.
     */
    public function show(FullExercise $fullExercise)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $full_exercise=FullExercise::with('listening_exercise')->find($id);

        return view('admin.full_mock.full_exercises.edit', compact('id','full_exercise'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'title'=>'required|min:3',
            'status'=>'required',
            'difficulty'=>'required',
        ]);
        $fullExercise= FullExercise::find($id);

        $fullExercise->update([
            'title'=>$request->title,
            'status'=>$request->status,
        ]);

        $fullExercise->listening_exercise->update([
            'difficulty'=>$request->difficulty,
            'status'=>$request->status,
        ]);

        $fullExercise->reading_exercise->update([
            'status'=>$request->status,
            'difficulty'=>$request->difficulty,

        ]);

        $fullExercise->writing_exercise->update([
            'status'=>$request->status,
        ]);

        $fullExercise->speaking_exercise->update([
            'status'=>$request->status,
            'part_category'=>'4',
        ]);


        return redirect()->route('admin.full_exercise.index',['id'=>$fullExercise->exercise_id] );

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $fullExercise= FullExercise::find($id);


        if ($fullExercise->listening_exercise) {
            $listening=$fullExercise->listening_exercise;
            if ($listening->part_one) {

                if ($listening->part_one->audio) {
                    Storage::delete('public/audios/' . $listening->part_one ->audio);
                }
                $listening->part_one->tests()->delete();

            }elseif ($listening->part_five) {

                if ($listening->part_five->audio) {
                    Storage::delete('public/audios/' . $listening->part_five ->audio);
                }
                $listening->part_five->tests()->delete();

            }elseif ($listening->part_two){

                if ($listening->part_two->audio) {
                    Storage::delete('public/audios/' . $listening->part_two ->audio);
                }
                $listening->part_two->listening_part_two_six_tests()->delete();
            }elseif ($listening->part_six){

                if ($listening->part_six->audio) {
                    Storage::delete('public/audios/' . $listening->part_six ->audio);
                }
                $listening->part_six->listening_part_two_six_tests()->delete();

            }elseif ($listening->part_three){
                    if ($listening->part_three->audio) {
                        Storage::delete('public/audios/' . $listening->part_three ->audio);
                    }
                    $listening->part_three->part_three_four_questions()->delete();
                    $listening->part_three->part_three_four_answers()->delete();

            }elseif ($listening->part_four){
                    if ($listening->part_four->audio) {
                        Storage::delete('public/audios/' . $listening->part_four ->audio);
                    }
                    $listening->part_four->part_three_four_questions()->delete();
                    $listening->part_four->part_three_four_answers()->delete();
            }

            $fullExercise->listening_exercise->delete();

        }
        if ($fullExercise->reading_exercise) {
            $fullExercise->reading_exercise->delete();
        }
        if ($fullExercise->speaking_exercise) {
            $fullExercise->speaking_exercise->delete();
        }
        if ($fullExercise->writing_exercise) {
            $fullExercise->writing_exercise->delete();
        }

        $fullExercise->delete();
        return redirect()->back();
    }
}
