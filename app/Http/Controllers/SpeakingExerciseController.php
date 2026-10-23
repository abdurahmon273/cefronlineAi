<?php

namespace App\Http\Controllers;

use App\Jobs\SpeakingTextToAudioJob;
use App\Models\Exercise;
use App\Models\FullExercise;
use App\Models\ReadingExercise;
use App\Models\SpeakingExercise;
use App\Models\SpeakingPartOneFirst;
use App\Models\SpeakingPartOneSecond;
use App\Models\SpeakingPartThree;
use App\Models\SpeakingPartTwo;
use App\Models\WritingExercise;
use App\Models\WritingTaskOne;
use App\Models\WritingTaskTwo;
use Illuminate\Http\Request;

class SpeakingExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $writings = WritingExercise::where('exercise_id',$id)->get();
        $exercise=Exercise::find($id);
        return view('admin.writing.writing_exercises.index', compact('writings','id','exercise'));
    }
    public function index_part_one($id)
    {
        return view('admin.speaking.speaking_part_one_exercises.index', compact('id'));
    }
    public function index_part_two($id)
    {
        return view('admin.speaking.speaking_part_two_exercises.index', compact('id'));
    }
    public function index_part_three($id)
    {
        return view('admin.speaking.speaking_part_three_exercises.index', compact('id'));
    }
    public function index_full($id)
    {
        return view('admin.speaking.speaking_full_exercises.index', compact('id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {

//        return view('admin.writing.writing_exercises.create', compact('id'));
    }
    public function part_one_create($id)
    {

        return view('admin.speaking.speaking_part_one_exercises.create', compact('id'));
    }
    public function part_two_create($id)
    {

        return view('admin.speaking.speaking_part_two_exercises.create', compact('id'));
    }
    public function part_three_create($id)
    {

        return view('admin.speaking.speaking_part_three_exercises.create', compact('id'));
    }
    public function full_create($id)
    {

        return view('admin.speaking.speaking_full_exercises.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'name'=>'required|min:3',
            'part_category'=>'required',
            'status'=>'required',
            'theme'=>'required',
            'requirement'=>'required',
            'advice'=>'required',
            'word_count'=>'required',
            'time_limit'=>'required|numeric|min:1',
        ]);

        if ($request->part_category=='1') {
            $this->validate($request,[
                'bullet_point_1'=>'required',
                'bullet_point_2'=>'required',
                'bullet_point_3'=>'required',
            ]);
        }

        $writing= WritingExercise::create([
            'name'=>$request->name,
            'part_category'=>$request->part_category,
            'status'=>$request->status,
            'example_text'=>$request->example_text,
            'exercise_id'=>$request->exercise_id,
        ]);

        if ($request->part_category=='1') {
            WritingTaskOne::create([
                'theme'=>$request->theme,
                'requirement'=>$request->requirement,
                'advice'=>$request->advice,
                'time_limit'=>$request->time_limit,
                'word_count'=>$request->word_count,
                'bullet_point_1'=>$request->bullet_point_1,
                'bullet_point_2'=>$request->bullet_point_2,
                'bullet_point_3'=>$request->bullet_point_3,
                'writing_exercise_id'=>$writing->id,
            ]);
        }elseif ($request->part_category=='2') {
            WritingTaskTwo::create([
                'theme'=>$request->theme,
                'requirement'=>$request->requirement,
                'advice'=>$request->advice,
                'time_limit'=>$request->time_limit,
                'word_count'=>$request->word_count,
                'writing_exercise_id'=>$writing->id,
            ]);
        }
        return redirect()->route('admin.writings.exercise.index',['id'=>$writing->exercise_id] );

    }
    public function part_one_store(Request $request)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'title'=>'required|min:3',
            'status'=>'required',
        ]);



        $speaking= SpeakingExercise::create([
            'title'=>$request->title,
            'part_category'=>'1',
            'status'=>$request->status,
            'example_answer'=>$request->example_answer,
            'exercise_id'=>$request->exercise_id,
        ]);


        SpeakingPartOneFirst::create([
            'title'=>'Part 1.1',
            'speaking_exercise_id'=>$speaking->id,
        ]);

        SpeakingPartOneSecond::create([
            'title'=>'Part 1.2',
            'speaking_exercise_id'=>$speaking->id,
        ]);



        return redirect()->route('admin.speakings_exercise.part_one.index',['id'=>$speaking->exercise_id] );

    }
    public function part_two_store(Request $request)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'title'=>'required|min:3',
            'status'=>'required',
            'seconds'=>'required',
        ]);



        $speaking= SpeakingExercise::create([
            'title'=>$request->title,
            'part_category'=>'2',
            'status'=>$request->status,
            'exercise_id'=>$request->exercise_id,
        ]);


        SpeakingPartTwo::create([
            'title'=>'Part 2',
            'time'=>$request->seconds,
            'speaking_exercise_id'=>$speaking->id,
            'modal_answer'=>$request->modal_answer,
        ]);

        return redirect()->route('admin.speakings_exercise.part_two.index',['id'=>$speaking->exercise_id] );

    }
    public function part_three_store(Request $request)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'title'=>'required|min:3',
            'status'=>'required',
            'seconds'=>'required',
            'theme'=>'required',
        ]);



        $speaking= SpeakingExercise::create([
            'title'=>$request->title,
            'part_category'=>'3',
            'status'=>$request->status,
            'exercise_id'=>$request->exercise_id,
        ]);


        $question=SpeakingPartThree::create([
            'title'=>'Part 3',
            'time'=>$request->seconds,
            'theme'=>$request->theme,
            'speaking_exercise_id'=>$speaking->id,
            'modal_answer'=>$request->modal_answer,
        ]);


//        if ($question->theme){
//            SpeakingTextToAudioJob::dispatch('three_theme', $question->id);
//        }

        return redirect()->route('admin.speakings_exercise.part_three.index',['id'=>$speaking->exercise_id] );

    }
    public function full_store1(Request $request)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'title'=>'required|min:3',
            'part_two_seconds'=>'required',
            'part_three_seconds'=>'required',
            'theme'=>'required',
        ]);

        $full_exercise=FullExercise::find($request->exercise_id);
        $speaking= $full_exercise->speaking_exercise;

        $speaking->update([
            'title'=>$request->title,
            'part_category'=>'4',
        ]);


        SpeakingPartOneFirst::create([
            'title'=>'Part 1.1',
            'speaking_exercise_id'=>$speaking->id,
        ]);

        SpeakingPartOneSecond::create([
            'title'=>'Part 1.2',
            'speaking_exercise_id'=>$speaking->id,
        ]);

        SpeakingPartTwo::create([
            'title'=>'Part 2',
            'time'=>$request->part_two_seconds,
            'modal_answer'=>$request->part_two_modal_answer,
            'speaking_exercise_id'=>$speaking->id,
        ]);

        SpeakingPartThree::create([
            'title'=>'Part 3',
            'time'=>$request->part_three_seconds,
            'modal_answer'=>$request->part_three_modal_answer,
            'theme'=>$request->theme,
            'speaking_exercise_id'=>$speaking->id,
        ]);

        return redirect()->route('admin.full_exercise.more.index',['id'=>$speaking->full_exercise->id] );

    }
    public function full_store(Request $request)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'title'=>'required|min:3',
            'status'=>'required',
            'part_two_seconds'=>'required',
            'part_three_seconds'=>'required',
            'theme'=>'required',
        ]);



        $speaking= SpeakingExercise::create([
            'title'=>$request->title,
            'part_category'=>'4',
            'status'=>$request->status,
            'exercise_id'=>$request->exercise_id,
            'example_answer'=>$request->example_answer,
        ]);


        SpeakingPartOneFirst::create([
            'title'=>'Part 1.1',
            'speaking_exercise_id'=>$speaking->id,
        ]);

        SpeakingPartOneSecond::create([
            'title'=>'Part 1.2',
            'speaking_exercise_id'=>$speaking->id,
        ]);

        SpeakingPartTwo::create([
            'title'=>'Part 2',
            'time'=>$request->part_two_seconds,
            'modal_answer'=>$request->part_two_modal_answer,
            'speaking_exercise_id'=>$speaking->id,
        ]);

        $question=SpeakingPartThree::create([
            'title'=>'Part 3',
            'time'=>$request->part_three_seconds,
            'theme'=>$request->theme,
            'modal_answer'=>$request->part_three_modal_answer,
            'speaking_exercise_id'=>$speaking->id,
        ]);

//        SpeakingTextToAudioJob::dispatch('three_theme', $question->id);

        return redirect()->route('admin.speakings_exercise.full.index',['id'=>$speaking->exercise_id] );

    }

    /**
     * Display the specified resource.
     */
    public function show(WritingExercise $writingExercise)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $writing_exercise=WritingExercise::with('task_one','task_two')->find($id);
        $task=[];
        if ($writing_exercise->part_category=='1') {
            $task=$writing_exercise->task_one;
        }elseif ($writing_exercise->part_category=='2'){
            $task=$writing_exercise->task_two;
        }
        return view('admin.writing.writing_exercises.edit', compact('task','writing_exercise','id'));
    }
    public function part_one_edit($id)
    {
        $speaking_exercise=SpeakingExercise::with('part_one_first','part_one_second')->find($id);
        $task=[];

        return view('admin.speaking.speaking_part_one_exercises.edit', compact('task','speaking_exercise','id'));
    }
    public function part_two_edit($id)
    {
        $speaking_exercise=SpeakingExercise::with('part_two')->find($id);
        $task=[];

        return view('admin.speaking.speaking_part_two_exercises.edit', compact('task','speaking_exercise','id'));
    }
    public function part_three_edit($id)
    {
        $speaking_exercise=SpeakingExercise::with('part_three')->find($id);
        $task=[];

        return view('admin.speaking.speaking_part_three_exercises.edit', compact('task','speaking_exercise','id'));
    }
    public function full_edit($id)
    {
        $speaking_exercise=SpeakingExercise::with('part_one_first','part_one_second','part_two','part_three')->find($id);
        $task=[];

        return view('admin.speaking.speaking_full_exercises.edit', compact('task','speaking_exercise','id'));
    }
    public function change($id)
    {
        $FullExercise=FullExercise::with('speaking_exercise.part_one_first','speaking_exercise.part_one_second','speaking_exercise.part_two','speaking_exercise.part_three')->find($id);

        if ($FullExercise->speaking_exercise->part_two) {

            return view('admin.full_mock.speaking.edit',compact('FullExercise','id'));
        }else{

            return view('admin.full_mock.speaking.create',compact('FullExercise','id'));
        }



        return view('admin.speaking.speaking_full_exercises.edit', compact('task','speaking_exercise','id'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'name'=>'required|min:3',
            'status'=>'required',
            'theme'=>'required',
            'requirement'=>'required',
            'advice'=>'required',
            'word_count'=>'required',
            'time_limit'=>'required|numeric|min:1',
        ]);
        $writing_exercise=WritingExercise::with('task_one','task_two')->find($id);

        if ($writing_exercise->part_category=='1') {
            $this->validate($request,[
                'bullet_point_1'=>'required',
                'bullet_point_2'=>'required',
                'bullet_point_3'=>'required',
            ]);
        }

        $writing_exercise->update([
            'name'=>$request->name,
            'status'=>$request->status,
            'example_text'=>$request->example_text,
        ]);
        if ($writing_exercise->part_category=='1') {
            $writing_exercise->task_one->update([
                'theme'=>$request->theme,
                'requirement'=>$request->requirement,
                'advice'=>$request->advice,
                'time_limit'=>$request->time_limit,
                'word_count'=>$request->word_count,
                'bullet_point_1'=>$request->bullet_point_1,
                'bullet_point_2'=>$request->bullet_point_2,
                'bullet_point_3'=>$request->bullet_point_3,
            ]);
        }elseif ($writing_exercise->part_category=='2') {
            $writing_exercise->task_two->update([
                'theme'=>$request->theme,
                'requirement'=>$request->requirement,
                'advice'=>$request->advice,
                'time_limit'=>$request->time_limit,
                'word_count'=>$request->word_count,
            ]);
        }
        return redirect()->route('admin.writings.exercise.index',['id'=>$writing_exercise->exercise_id] );

    }
    public function part_one_update(Request $request, $id)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'title'=>'required|min:3',
            'status'=>'required',
        ]);



        $speaking= SpeakingExercise::find($id);

        $speaking->update([
            'title'=>$request->title,
            'status'=>$request->status,
            'example_answer'=>$request->example_answer,

        ]);


        return redirect()->route('admin.speakings_exercise.part_one.index',['id'=>$speaking->exercise_id] );

    }


    public function part_two_update(Request $request, $id)
    {
            $this->validate($request,[
                'exercise_id'=>'required',
                'title'=>'required|min:3',
                'status'=>'required',
                'seconds'=>'required',
            ]);

        $speaking= SpeakingExercise::with('part_two')->find($id);

        $speaking->update([
            'title'=>$request->title,
            'status'=>$request->status,
        ]);
        $speaking->part_two->update([
            'time'=>$request->seconds,
            'modal_answer'=>$request->modal_answer,
        ]);


        return redirect()->route('admin.speakings_exercise.part_two.index',['id'=>$speaking->exercise_id] );

    }

    public function part_three_update(Request $request, $id)
    {
            $this->validate($request,[
                'exercise_id'=>'required',
                'title'=>'required|min:3',
                'status'=>'required',
                'seconds'=>'required',
                'theme'=>'required',
            ]);

        $speaking= SpeakingExercise::with('part_three')->find($id);

        $speaking->update([
            'title'=>$request->title,
            'status'=>$request->status,
        ]);
        $speaking->part_three->update([
            'time'=>$request->seconds,
            'theme'=>$request->theme,
            'modal_answer'=>$request->modal_answer,
        ]);


//        if ($speaking->part_three->wasChanged('theme')) {
//            SpeakingTextToAudioJob::dispatch('three_theme', $speaking->part_three->id);
//        }

        return redirect()->route('admin.speakings_exercise.part_three.index',['id'=>$speaking->exercise_id] );

    }
    public function full_update(Request $request, $id)
    {
//        dd(1);
        $this->validate($request,[
            'exercise_id'=>'required',
            'title'=>'required|min:3',
            'status'=>'required',
            'part_two_seconds'=>'required',
            'part_three_seconds'=>'required',
            'theme'=>'required',
        ]);

        $speaking= SpeakingExercise::with('part_three')->find($id);

        $speaking->update([
            'title'=>$request->title,
            'status'=>$request->status,
            'example_answer'=>$request->example_answer,
        ]);


        $speaking->part_two->update([
            'time'=>$request->part_two_seconds,
            'modal_answer'=>$request->part_two_modal_answer,
        ]);

        $speaking->part_three->update([
            'time'=>$request->part_three_seconds,
            'theme'=>$request->theme,
            'modal_answer'=>$request->part_three_modal_answer,
        ]);

//        if ($speaking->part_three->wasChanged('theme')) {
//            SpeakingTextToAudioJob::dispatch('three_theme', $speaking->part_three->id);
//        }

        return redirect()->route('admin.speakings_exercise.full.index',['id'=>$speaking->exercise_id] );

    }
    public function full_update1(Request $request)
    {
//        dd(1);
        $this->validate($request,[
            'exercise_id'=>'required',
            'title'=>'required|min:3',
            'part_two_seconds'=>'required',
            'part_three_seconds'=>'required',
            'theme'=>'required',
        ]);
        $full_exercise=FullExercise::find($request->exercise_id);
        $speaking= $full_exercise->speaking_exercise;

        $speaking->update([
            'title'=>$request->title,
            'part_category'=>'4',
        ]);


        $speaking->part_two->update([
            'time'=>$request->part_two_seconds,
            'modal_answer'=>$request->part_two_modal_answer,
        ]);

        $speaking->part_three->update([
            'time'=>$request->part_three_seconds,
            'theme'=>$request->theme,
            'modal_answer'=>$request->part_three_modal_answer,
        ]);

        return redirect()->route('admin.full_exercise.more.index',['id'=>$full_exercise->id] );
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $speaking_exercise=SpeakingExercise::with('part_one_first','part_one_second','part_two','part_three')->findOrFail($id);
        if ($speaking_exercise->part_one_first) {
            $speaking_exercise->part_one_first->delete();
        }elseif ($speaking_exercise->part_one_second){
            $speaking_exercise->part_one_second->delete();
        }elseif ($speaking_exercise->part_two){
            $speaking_exercise->part_two->delete();
        }elseif ($speaking_exercise->part_three){
            $speaking_exercise->part_three->delete();
        }
        $speaking_exercise->delete();

        return redirect()->back();
    }
}
