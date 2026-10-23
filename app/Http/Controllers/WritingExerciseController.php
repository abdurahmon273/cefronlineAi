<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\FullExercise;
use App\Models\ReadingExercise;
use App\Models\ReadingPartOneFiveQuestion;
use App\Models\ReadingPartTwoThree;
use App\Models\WritingExercise;
use App\Models\WritingTaskOne;
use App\Models\WritingTaskTwo;
use Illuminate\Http\Request;

class WritingExerciseController extends Controller
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
    public function index_task_one($id)
    {
        return view('admin.writing.writing_task_one_exercises.index', compact('id'));
    }
    public function index_task_two($id)
    {
        return view('admin.writing.writing_task_two_exercises.index', compact('id'));
    }
    public function index_full($id)
    {
        return view('admin.writing.writing_full_exercises.index', compact('id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {

        return view('admin.writing.writing_exercises.create', compact('id'));
    }
    public function task_one_create($id)
    {

        return view('admin.writing.writing_task_one_exercises.create', compact('id'));
    }
    public function task_two_create($id)
    {

        return view('admin.writing.writing_task_two_exercises.create', compact('id'));
    }
    public function full_create($id)
    {

        return view('admin.writing.writing_full_exercises.create', compact('id'));
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
                'time_limit'=>$request->time_limit??30,
                'word_count'=>$request->word_count??200,
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
                'time_limit'=>$request->time_limit??30,
                'word_count'=>$request->word_count??200,
                'writing_exercise_id'=>$writing->id,
            ]);
        }
        return redirect()->route('admin.writings.exercise.index',['id'=>$writing->exercise_id] );

    }
    public function task_one_store(Request $request)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'name'=>'required|min:3',
            'status'=>'required',
            'theme'=>'required',
            'example_text'=>'required',
        ]);



        $writing= WritingExercise::create([
            'name'=>$request->name,
            'part_category'=>'1',
            'status'=>$request->status,
            'exercise_id'=>$request->exercise_id,
        ]);


            WritingTaskOne::create([
                'theme'=>$request->theme,
                'requirement'=>'',
                'advice'=>'',
                'time_limit'=>$request->time_limit ?? 30,
                'word_count'=>$request->word_count ?? 200 ,
                'bullet_point_1'=>'',
                'bullet_point_2'=>'',
                'bullet_point_3'=>'',
                'example_text'=>$request->example_text,
                'writing_exercise_id'=>$writing->id,
            ]);


        return redirect()->route('admin.writings_exercise.task_one.index',['id'=>$writing->exercise_id] );

    }
    public function task_two_store(Request $request)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'name'=>'required|min:3',
            'status'=>'required',
            'theme'=>'required',
            'example_text'=>'required',
        ]);



        $writing= WritingExercise::create([
            'name'=>$request->name,
            'part_category'=>'2',
            'status'=>$request->status,
            'exercise_id'=>$request->exercise_id,
        ]);


            WritingTaskTwo::create([
                'theme'=>$request->theme,
                'requirement'=>'',
                'advice'=>'',
                'time_limit'=>$request->time_limit ?? 30,
                'word_count'=>$request->word_count ?? 200,
                'example_text'=>$request->example_text,
                'writing_exercise_id'=>$writing->id,
            ]);

        return redirect()->route('admin.writings_exercise.task_two.index',['id'=>$writing->exercise_id] );

    }
    public function full_store(Request $request)
    {

//        dd($request->all());
        $this->validate($request,[
            'exercise_id'=>'required',
            'name'=>'required|min:3',
            'status'=>'required',
            'task_one_theme'=>'required',
            'task_one_example_text'=>'required',

            'task_two_theme'=>'required',
            'task_two_word_count'=>'required',
            'task_two_example_text'=>'required',
        ]);



        $writing= WritingExercise::create([
            'name'=>$request->name,
            'part_category'=>'3',
            'status'=>$request->status,
            'exercise_id'=>$request->exercise_id,
        ]);


        WritingTaskOne::create([
            'theme'=>$request->task_one_theme,
            'requirement'=>'',
            'advice'=>'',
            'time_limit'=>$request->task_one_time_limit ?? 30,
            'word_count'=>$request->task_one_word_count ?? 200,
            'bullet_point_1'=>'',
            'bullet_point_2'=>'',
            'bullet_point_3'=>'',
            'example_text'=>$request->task_one_example_text,
            'writing_exercise_id'=>$writing->id,
        ]);

        WritingTaskTwo::create([
            'theme'=>$request->task_two_theme,
            'requirement'=>'',
            'advice'=>'',
            'time_limit'=>$request->task_two_time_limit ?? 30,
            'word_count'=>$request->task_two_word_count ?? 200,
            'example_text'=>$request->task_two_example_text,
            'writing_exercise_id'=>$writing->id,
        ]);

        return redirect()->route('admin.writings_exercise.full.index',['id'=>$writing->exercise_id] );

    }
    public function full_store1(Request $request)
    {

        $this->validate($request,[
            'exercise_id'=>'required',
            'title'=>'required|min:3',
            'task_one_theme'=>'required',
            'task_one_example_text'=>'required',

            'task_two_theme'=>'required',
            'task_two_example_text'=>'required',
        ]);


        $full_exercise=FullExercise::find($request->exercise_id);
        $writing= $full_exercise->writing_exercise;
        $writing->update([
            'name'=>$request->title,
        ]);


        WritingTaskOne::create([
            'theme'=>$request->task_one_theme,
            'requirement'=>'',
            'advice'=>'',
            'time_limit'=>$request->task_one_time_limit ?? 30,
            'word_count'=>$request->task_one_word_count??200,
            'bullet_point_1'=>'',
            'bullet_point_2'=>'',
            'bullet_point_3'=>'',
            'example_text'=>$request->task_one_example_text,
            'writing_exercise_id'=>$writing->id,
        ]);

        WritingTaskTwo::create([
            'theme'=>$request->task_two_theme,
            'requirement'=>'',
            'advice'=>'',
            'time_limit'=>$request->task_two_time_limit ?? 30,
            'word_count'=>$request->task_two_word_count ?? 200,
            'example_text'=>$request->task_two_example_text,
            'writing_exercise_id'=>$writing->id,
        ]);

        return redirect()->route('admin.full_exercise.more.index',['id'=>$writing->full_exercise->id] );

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
    public function task_one_edit($id)
    {
        $writing_exercise=WritingExercise::with('task_one','task_two')->find($id);
        $task=[];
        if ($writing_exercise->part_category=='1') {
            $task=$writing_exercise->task_one;
        }elseif ($writing_exercise->part_category=='2'){
            $task=$writing_exercise->task_two;
        }
        return view('admin.writing.writing_task_one_exercises.edit', compact('task','writing_exercise','id'));
    }
    public function task_two_edit($id)
    {
        $writing_exercise=WritingExercise::with('task_two')->find($id);
        $task=[];
        if ($writing_exercise->part_category=='1') {
            $task=$writing_exercise->task_one;
        }elseif ($writing_exercise->part_category=='2'){
            $task=$writing_exercise->task_two;
        }
        return view('admin.writing.writing_task_two_exercises.edit', compact('task','writing_exercise','id'));
    }
    public function full_edit($id)
    {
        $writing_exercise=WritingExercise::with('task_two')->find($id);
        $task_one=$writing_exercise->task_one;
        $task_two=$writing_exercise->task_two;

        if ($writing_exercise->part_category=='2'){
            $writing_exercise->update([
                'part_category'=>'3'
            ]);
        }

        return view('admin.writing.writing_full_exercises.edit', compact('task_one','task_two','writing_exercise','id'));
    }
    public function change($id)
    {
        $FullExercise=FullExercise::with('writing_exercise.task_one','writing_exercise.task_two')->find($id);

        if ($FullExercise->writing_exercise->task_one) {
            $task_one=$FullExercise->writing_exercise->task_one;
            $task_two=$FullExercise->writing_exercise->task_two;

            return view('admin.full_mock.writing.edit',compact('FullExercise','task_one','task_two','id'));

        }else{

            return view('admin.full_mock.writing.create',compact('FullExercise','id'));
        }

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
                'time_limit'=>$request->time_limit??30,
                'word_count'=>$request->word_count??200,
                'bullet_point_1'=>$request->bullet_point_1,
                'bullet_point_2'=>$request->bullet_point_2,
                'bullet_point_3'=>$request->bullet_point_3,
            ]);
        }elseif ($writing_exercise->part_category=='2') {
            $writing_exercise->task_two->update([
                'theme'=>$request->theme,
                'requirement'=>$request->requirement,
                'advice'=>$request->advice,
                'time_limit'=>$request->time_limit??30,
                'word_count'=>$request->word_count??200,
            ]);
        }
        return redirect()->route('admin.writings.exercise.index',['id'=>$writing_exercise->exercise_id] );

    }
    public function task_one_update(Request $request, $id)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'name'=>'required|min:3',
            'status'=>'required',
            'theme'=>'required',
        ]);
        $writing_exercise=WritingExercise::with('task_one')->find($id);



        $writing_exercise->update([
            'name'=>$request->name,
            'status'=>$request->status,
            'example_text'=>$request->example_text,
        ]);

        if ($writing_exercise->part_category=='1') {
            $writing_exercise->task_one->update([
                'theme'=>$request->theme,
                'requirement'=>'',
                'advice'=>'',
                'time_limit'=>$request->time_limit ?? 20,
                'word_count'=>$request->word_count ?? 200,
                'example_text'=>$request->example_text,
                'bullet_point_1'=>'',
                'bullet_point_2'=>'',
                'bullet_point_3'=>'',
            ]);
        }

        return redirect()->route('admin.writings_exercise.task_one.index',['id'=>$writing_exercise->exercise_id] );

    }


    public function task_two_update(Request $request, $id)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'name'=>'required|min:3',
            'status'=>'required',
            'theme'=>'required',
        ]);
        $writing_exercise=WritingExercise::with('task_one','task_two')->find($id);



        $writing_exercise->update([
            'name'=>$request->name,
            'status'=>$request->status,
        ]);

        if ($writing_exercise->part_category=='2') {
            $writing_exercise->task_two->update([
                'theme'=>$request->theme,
                'requirement'=>'',
                'advice'=>'',
                'time_limit'=>$request->time_limit?? 30,
                'word_count'=>$request->word_count ?? 200,
                'example_text'=>$request->example_text,
            ]);
        }
        return redirect()->route('admin.writings_exercise.task_two.index',['id'=>$writing_exercise->exercise_id] );

    }
    public function full_update(Request $request, $id)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'name'=>'required|min:3',
            'status'=>'required',
            'task_one_theme'=>'required',

            'task_two_theme'=>'required',
        ]);
        $writing_exercise=WritingExercise::with('task_one','task_two')->find($id);



        $writing_exercise->update([
            'name'=>$request->name,
            'status'=>$request->status,
        ]);
        if ($writing_exercise->task_one) {
            $writing_exercise->task_one->update([
                'theme'=>$request->task_one_theme,
                'requirement'=>'',
                'advice'=>'',
                'time_limit'=>$request->task_one_time_limit ?? 30,
                'word_count'=>$request->task_one_word_count ?? 200,
                'example_text'=>$request->task_one_example_text,
                'bullet_point_1'=>'',
                'bullet_point_2'=>'',
                'bullet_point_3'=>'',
            ]);
        }

        if ($writing_exercise->task_two) {
            $writing_exercise->task_two->update([
                'theme'=>$request->task_two_theme,
                'requirement'=>'',
                'advice'=>'',
                'time_limit'=>$request->task_two_time_limit ?? 30,
                'word_count'=>$request->task_two_word_count ?? 200,
                'example_text'=>$request->task_two_example_text,
            ]);
        }


        return redirect()->route('admin.writings_exercise.full.index',['id'=>$writing_exercise->exercise_id] );

    }
    public function full_update1(Request $request)
    {
        $this->validate($request,[
            'exercise_id'=>'required',
            'title'=>'required|min:3',
            'task_one_theme'=>'required',

            'task_two_theme'=>'required',
        ]);
        $full_exercise=FullExercise::find($request->exercise_id);
        $writing_exercise= $full_exercise->writing_exercise;



        $writing_exercise->update([
            'name'=>$request->title,
        ]);

        if ($writing_exercise->task_one) {
            $writing_exercise->task_one->update([
                'theme'=>$request->task_one_theme,
                'requirement'=>'',
                'advice'=>'',
                'time_limit'=>$request->task_one_time_limit ?? 30,
                'word_count'=>$request->task_one_word_count ?? 200,
                'example_text'=>$request->task_one_example_text,
                'bullet_point_1'=>'',
                'bullet_point_2'=>'',
                'bullet_point_3'=>'',
            ]);
        }

        if ($writing_exercise->task_two) {
            $writing_exercise->task_two->update([
                'theme'=>$request->task_two_theme,
                'requirement'=>'',
                'advice'=>'',
                'time_limit'=>$request->task_two_time_limit ?? 30,
                'word_count'=>$request->task_two_word_count ?? 200,
                'example_text'=>$request->task_two_example_text,
            ]);
        }

        return redirect()->route('admin.full_exercise.more.index',['id'=>$full_exercise->id] );

    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $writingExercise=WritingExercise::with('task_one','task_two')->findOrFail($id);
        if ($writingExercise->task_one) {
            $writingExercise->task_one->delete();
        }elseif ($writingExercise->task_two){
            $writingExercise->task_two->delete();
        }
        $writingExercise->delete();
        return redirect()->back();
    }
}
