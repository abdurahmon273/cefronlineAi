<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\ListeningExercise;
use Illuminate\Http\Request;

class ExcerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.uploads.index');
    }

    public function listening_index()
    {

        $excercises = Exercise::with('listening_exercises')->where('category_type','0')->orderBy('order_id')->get();

        return view('admin.listenings.index', compact('excercises'));
    }

    public function reading_index()
    {

        $excercises = Exercise::with('reading_exercises')->where('category_type','1')->orderBy('order_id')->get();

        return view('admin.reading.readings.index', compact('excercises',));
    }
    public function writing_index()
    {

        return view('admin.writing.writings.index');
    }
    public function speaking_index()
    {

        return view('admin.speaking.speakings.index');
    }
    public function full_index()
    {

        return view('admin.full_mock.exercises.index');
    }
    public function writing_task1()
    {
        return view('admin.writing.task_one_writings.index');
    }
    public function writing_task2()
    {
        return view('admin.writing.task_two_writings.index');
    }
    public function writing_full()
    {
        return view('admin.writing.full_writings.index');
    }
    public function speaking_part1()
    {
        return view('admin.speaking.part_one_speakings.index');
    }
    public function speaking_part2()
    {
        return view('admin.speaking.part_two_speakings.index');
    }
    public function speaking_part3()
    {
        return view('admin.speaking.part_three_speakings.index');
    }
    public function speaking_full()
    {
        return view('admin.speaking.part_full_speakings.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function listening_create()
    {
        return view('admin.listenings.create');
    }
    public function reading_create()
    {
        return view('admin.reading.readings.create');
    }
    public function writing_task1_create()
    {
        return view('admin.writing.task_one_writings.create');
    }
    public function writing_task2_create()
    {
        return view('admin.writing.task_two_writings.create');
    }
    public function writing_full_create()
    {
        return view('admin.writing.full_writings.create');
    }
    public function speaking_part1_create()
    {
        return view('admin.speaking.part_one_speakings.create');
    }
    public function speaking_part2_create()
    {
        return view('admin.speaking.part_two_speakings.create');
    }
    public function speaking_part3_create()
    {
        return view('admin.speaking.part_three_speakings.create');
    }
    public function speaking_full_create()
    {
        return view('admin.speaking.part_full_speakings.create');
    }

    public function create()
    {
        return view('admin.listenings.create');
    }
    public function full_create()
    {


        return view('admin.full_mock.exercises.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function listening_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
        ]);

        $request_array=$request->all();
        $request_array['category_type']='0';
        Exercise::create($request_array);

        return redirect()->route('admin.listening.exercises.index');

    }
    public function reading_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
        ]);

        $request_array=$request->all();
        $request_array['category_type']='1';
        Exercise::create($request_array);

        return redirect()->route('admin.reading.exercises.index');

    }
    public function writing_task1_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',

        ]);

        $request_array=$request->all();
        $request_array['category_type']='2';
        $request_array['part_type']='1';
        Exercise::create($request_array);

        return redirect()->route('admin.writing.task1.exercise.index');

    }
    public function writing_task2_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',

        ]);
        $request_array=$request->all();
        $request_array['category_type']='2';
        $request_array['part_type']='2';
        Exercise::create($request_array);

        return redirect()->route('admin.writing.task2.exercise.index');

    }
    public function writing_full_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',

        ]);
        $request_array=$request->all();
        $request_array['category_type']='2';
        $request_array['part_type']='3';
        Exercise::create($request_array);

        return redirect()->route('admin.writing.full.exercises.index');

    }
    public function speaking_part1_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
        ]);

        $request_array=$request->all();
        $request_array['category_type']='3';
        $request_array['part_type']='1';
        Exercise::create($request_array);

        return redirect()->route('admin.speaking.part1.exercise.index');

    }
    public function speaking_part2_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',

        ]);
        $request_array=$request->all();
        $request_array['category_type']='3';
        $request_array['part_type']='2';
        Exercise::create($request_array);

        return redirect()->route('admin.speaking.part2.exercise.index');

    }
    public function speaking_part3_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',

        ]);

        $request_array=$request->all();
        $request_array['category_type']='3';
        $request_array['part_type']='3';
        Exercise::create($request_array);

        return redirect()->route('admin.speaking.part3.exercise.index');
    }
    public function speaking_full_store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:3',

        ]);

        $request_array=['name'=>$request->title];
        $request_array['category_type']='3';
        $request_array['part_type']='4';
        Exercise::create($request_array);

        return redirect()->route('admin.speaking.full.exercise.index');
    }
    public function full_store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:3',
        ]);

        $request_array=['name'=>$request->title];
        $request_array['category_type']='4';
        $request_array['part_type']='4';
        $exercise= Exercise::create($request_array);

        return redirect()->route('admin.full.exercises.index');

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
    public function show(Exercise $excercise)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function listening_edit($id)
    {
        $excercise = Exercise::findOrFail($id);
        return view('admin.listenings.edit', compact('excercise'));
    }
    public function reading_edit($id)
    {

        $excercise = Exercise::findOrFail($id);
        return view('admin.reading.readings.edit', compact('excercise'));
    }
    public function writing_task1_edit($id)
    {

        $excercise = Exercise::findOrFail($id);
        return view('admin.writing.task_one_writings.edit', compact('excercise'));
    }
    public function writing_task2_edit($id)
    {

        $excercise = Exercise::findOrFail($id);
        return view('admin.writing.task_two_writings.edit', compact('excercise'));
    }
    public function writing_full_edit($id)
    {

        $excercise = Exercise::findOrFail($id);
        return view('admin.writing.full_writings.edit', compact('excercise'));
    }
    public function speaking_part1_edit($id)
    {

        $excercise = Exercise::findOrFail($id);
        return view('admin.speaking.part_one_speakings.edit', compact('excercise'));
    }
    public function speaking_part2_edit($id)
    {

        $excercise = Exercise::findOrFail($id);
        return view('admin.speaking.part_two_speakings.edit', compact('excercise'));
    }
    public function speaking_part3_edit($id)
    {

        $excercise = Exercise::findOrFail($id);
        return view('admin.speaking.part_three_speakings.edit', compact('excercise'));
    }
    public function speaking_full_edit($id)
    {

        $excercise = Exercise::findOrFail($id);
        return view('admin.speaking.part_full_speakings.edit', compact('excercise'));
    }


    public function full_edit($id)
    {
        $excercise = Exercise::findOrFail($id);
        return view('admin.full_mock.exercises.edit', compact('excercise'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function listening_update(Request $request, Exercise $excercise)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
        ]);

        $excercise->update($request->all());

        return redirect()->route('admin.listening.exercises.index')->with('success', 'Exercise updated successfully');
    }
    public function reading_update(Request $request, Exercise $excercise)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
        ]);

        $excercise->update($request->all());

        return redirect()->route('admin.reading.exercises.index')->with('success', 'Exercise updated successfully');
    }
    public function writing_task1_update(Request $request, Exercise $excercise)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
        ]);

        $excercise->update($request->all());

        return redirect()->route('admin.writing.task1.exercise.index')->with('success', 'Exercise updated successfully');
    }
    public function writing_task2_update(Request $request, Exercise $excercise)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
        ]);

        $excercise->update($request->all());

        return redirect()->route('admin.writing.task2.exercise.index')->with('success', 'Exercise updated successfully');
    }
    public function writing_full_update(Request $request, Exercise $excercise)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
        ]);

        $excercise->update($request->all());

        return redirect()->route('admin.writing.full.exercises.index')->with('success', 'Exercise updated successfully');
    }
    public function speaking_part1_update(Request $request, Exercise $excercise)
    {
        $this->validate($request, [
            'title' => 'required|min:3',
        ]);

        $excercise->update(['name'=>$request->title]);

        return redirect()->route('admin.speaking.part1.exercise.index')->with('success', 'Exercise updated successfully');
    }
    public function speaking_part2_update(Request $request, Exercise $excercise)
    {
        $this->validate($request, [
            'title' => 'required|min:3',
        ]);

        $excercise->update(['name'=>$request->title]);

        return redirect()->route('admin.speaking.part2.exercise.index')->with('success', 'Exercise updated successfully');
    }
    public function speaking_part3_update(Request $request, Exercise $excercise)
    {
        $this->validate($request, [
            'title' => 'required|min:3',
        ]);

        $excercise->update(['name'=>$request->title]);

        return redirect()->route('admin.speaking.part3.exercise.index')->with('success', 'Exercise updated successfully');
    }
    public function speaking_full_update(Request $request, Exercise $excercise)
    {
        $this->validate($request, [
            'title' => 'required|min:3',
        ]);

        $excercise->update(['name'=>$request->title]);

        return redirect()->route('admin.speaking.full.exercise.index')->with('success', 'Exercise updated successfully');
    }

    public function full_update(Request $request, Exercise $excercise)
    {
        $this->validate($request, [
            'title' => 'required|min:3',
        ]);

        $excercise->update(['name'=>$request->title]);

        return redirect()->route('admin.full.exercises.index')->with('success', 'Exercise updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $exercise=Exercise::with('listening_exercises.listening_part_one_fife_question')->findOrFail($id);
        foreach ($exercise->listening_exercises as $listening_exercise) {
            $listening_exercise->listening_part_one_fife_question()->delete();
            $listening_exercise->delete();
        }
        $exercise->delete();
        return back();
    }
    public function reading_destroy($id)
    {
        $exercise=Exercise::findOrFail($id);
        $exercise->delete();
        return back();
    }
    public function writing_destroy($id)
    {
        $exercise=Exercise::findOrFail($id);
        $exercise->delete();
        return back();
    }
}
