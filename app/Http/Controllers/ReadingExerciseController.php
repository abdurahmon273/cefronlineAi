<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\FullExercise;
use App\Models\ListeningExercise;
use App\Models\ReadingExercise;
use App\Models\ReadingPartFour;
use App\Models\ReadingPartOneFiveQuestion;
use App\Models\ReadingPartTwoThree;
use Illuminate\Http\Request;

class ReadingExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
//        abort_if(Gate::denies('lesson_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $readings = ReadingExercise::where('exercise_id', $id)->orderBy('order_id')->get();
        $exercise = Exercise::find($id)->exercise_id;
        return view('admin.reading.reading_exercises.index', compact('readings', 'id', 'exercise'));
    }

    public function reading_full_index($id)
    {
        $reading_exercise=ReadingExercise::with('part_one')->orderBy('order_id')->find($id);
        return view('admin.full_mock.reading.index', compact('reading_exercise'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        return view('admin.reading.reading_exercises.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'part_category' => 'required',
        ]);

        if ($request->part_category == '6') {
            $this->validate($request, [
                'name' => 'required|min:3',
                'part_one_advice' => 'required',
                'part_two_advice' => 'required',
                'part_three_advice' => 'required',
                'part_four_advice' => 'required',
                'part_five_advice' => 'required',
                'difficulty' => 'required',
//                'time_limit' => 'required|numeric|min:1',
            ]);


            $reading=ReadingExercise::create([
                'name'=>$request->name,
                'part_category'=>$request->part_category,
                'status'=>$request->status,
                'exercise_id'=>$request->exercise_id,
                'difficulty' => $request->difficulty,
                'time_limit' => $request->time_limit ?? 0,
            ]);

            ReadingPartOneFiveQuestion::create([
                'advice' => $request->part_one_advice,
                'type' => '0',
                'reading_exercise_id' => $reading->id,
            ]);

            ReadingPartTwoThree::create([
                'advice' => $request->part_two_advice,
                'type' => '0',
                'reading_exercise_id' => $reading->id,
            ]);

            ReadingPartTwoThree::create([
                'advice' => $request->part_three_advice,
                'type' => '1',
                'reading_exercise_id' => $reading->id,
            ]);

            ReadingPartFour::create([
                'advice' => $request->part_four_advice,
                'reading_exercise_id' => $reading->id,
            ]);

            ReadingPartOneFiveQuestion::create([
                'advice' => $request->part_five_advice,
                'type' => '1',
                'reading_exercise_id' => $reading->id,
            ]);
        } else {
            $this->validate($request, [
                'name' => 'required|min:3',
                'status' => 'required',
                'exercise_id' => 'required',
                'advice' => 'required',
                'difficulty' => 'required',
//                'time_limit' => 'required|numeric|min:1',
            ]);
            $array=$request->all();
            $array['time_limit']=$request->time_limit ?? 0;

            $reading = ReadingExercise::create($request->all());

            if ($request->part_category == '1') {
                ReadingPartOneFiveQuestion::create([
                    'advice' => $request->advice,
                    'type' => '0',
                    'reading_exercise_id' => $reading->id,
                ]);
            } elseif ($request->part_category == '2') {
                ReadingPartTwoThree::create([
                    'advice' => $request->advice,
                    'type' => '0',
                    'reading_exercise_id' => $reading->id,
                ]);
            } elseif ($request->part_category == '3') {
                ReadingPartTwoThree::create([
                    'advice' => $request->advice,
                    'type' => '1',
                    'reading_exercise_id' => $reading->id,
                ]);
            } elseif ($request->part_category == '4') {
                ReadingPartFour::create([
                    'advice' => $request->advice,
                    'reading_exercise_id' => $reading->id,
                ]);
            } elseif ($request->part_category == '5') {
                ReadingPartOneFiveQuestion::create([
                    'advice' => $request->advice,
                    'type' => '1',
                    'reading_exercise_id' => $reading->id,
                ]);
            }
        }




        return redirect()->route('admin.readings.exercise.index', ['id' => $reading->exercise_id]);
    }

    public function store1(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:3',
            'part_one_advice' => 'required',
            'part_two_advice' => 'required',
            'part_three_advice' => 'required',
            'part_four_advice' => 'required',
            'part_five_advice' => 'required',
            'difficulty' => 'required',
//            'time_limit' => 'required|numeric|min:1',
        ]);

        $full_exercise = FullExercise::find($request->exercise_id);
        $reading = $full_exercise->reading_exercise;
        $reading->update([
            'difficulty' => $request->difficulty,
            'time_limit' => $request->time_limit ?? 0 ,
        ]);
        ReadingPartOneFiveQuestion::create([
            'advice' => $request->part_one_advice,
            'type' => '0',
            'reading_exercise_id' => $reading->id,
        ]);

        ReadingPartTwoThree::create([
            'advice' => $request->part_two_advice,
            'type' => '0',
            'reading_exercise_id' => $reading->id,
        ]);

        ReadingPartTwoThree::create([
            'advice' => $request->part_three_advice,
            'type' => '1',
            'reading_exercise_id' => $reading->id,
        ]);

        ReadingPartFour::create([
            'advice' => $request->part_four_advice,
            'reading_exercise_id' => $reading->id,
        ]);

        ReadingPartOneFiveQuestion::create([
            'advice' => $request->part_five_advice,
            'type' => '1',
            'reading_exercise_id' => $reading->id,
        ]);


        return redirect()->route('admin.full_exercise.more.index', ['id' => $full_exercise->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ReadingExercise $readingExercise)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $reading_exercise = ReadingExercise::with('part_one')->find($id);

        return view('admin.reading.reading_exercises.edit', compact('reading_exercise'));
    }


    public function change($id)
    {
        $reading_exercise = ReadingExercise::with('part_one', 'part_two', 'part_three', 'part_four', 'part_five')->find($id);
        $FullExercise = FullExercise::with('reading_exercise.part_one', 'reading_exercise.part_two', 'reading_exercise.part_three', 'reading_exercise.part_four', 'reading_exercise.part_five')->find($id);

        if ($FullExercise->reading_exercise->part_one) {
            return view('admin.full_mock.reading.edit', compact('FullExercise', 'id'));

        } else {
            return view('admin.full_mock.reading.create', compact('FullExercise', 'id'));
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if ($request->part_category == '6') {
            $this->validate($request, [
                'name' => 'required|min:3',
                'part_one_advice' => 'required',
                'part_two_advice' => 'required',
                'part_three_advice' => 'required',
                'part_four_advice' => 'required',
                'part_five_advice' => 'required',
                'difficulty' => 'required',
            ]);
        }else{
            $this->validate($request, [
                'name' => 'required|min:3',
                'status' => 'required',
                'advice' => 'required',
                'difficulty' => 'required',
            ]);
        }

        $reading_exercise = ReadingExercise::with('part_one','part_two','part_three','part_four','part_five')->find($id);
        $old_part_category=$reading_exercise->part_category;
        $reading_exercise->update([
            'name'=>$request->name,
            'part_category'=>$request->part_category,
            'status'=>$request->status,
            'difficulty'=>$request->difficulty,
            'time_limit'=>$request->time_limit ?? 0,
        ]);

        if ($request->part_category == $old_part_category) {
            if ($request->part_category == '6') {
                // not change full
                $reading_exercise->part_one->update([
                    'advice' => $request->part_one_advice,
                ]);

                $reading_exercise->part_two->update([
                    'advice' => $request->part_two_advice,
                ]);
                $reading_exercise->part_three->update([
                    'advice' => $request->part_three_advice,
                ]);
                $reading_exercise->part_four->update([
                    'advice' => $request->part_four_advice,
                ]);
                $reading_exercise->part_five->update([
                    'advice' => $request->part_five_advice,
                ]);

            }else{
                // not change part
                if ($reading_exercise->part_category == '1') {
                    $reading_exercise->part_one->update([
                        'advice' => $request->advice,
                    ]);
                } elseif ($reading_exercise->part_category == '2') {
                    $reading_exercise->part_two->update([
                        'advice' => $request->advice,
                    ]);
                } elseif ($reading_exercise->part_category == '3') {
                    $reading_exercise->part_three->update([
                        'advice' => $request->advice,
                    ]);
                } elseif ($reading_exercise->part_category == '4') {
                    $reading_exercise->part_four->update([
                        'advice' => $request->advice,
                    ]);
                } elseif ($reading_exercise->part_category == '5') {
                    $reading_exercise->part_five->update([
                        'advice' => $request->advice,
                    ]);
                }
            }
        }else{

            if ($request->part_category == '6') {
                //  change full
                if ($reading_exercise->part_one) {
                    $reading_exercise->part_one->forceDelete();
                } elseif ($reading_exercise->part_two) {
                    $reading_exercise->part_two->forceDelete();
                } elseif ($reading_exercise->part_three) {
                    $reading_exercise->part_three->forceDelete();
                } elseif ($reading_exercise->part_four) {
                    $reading_exercise->part_four->forceDelete();
                } elseif ($reading_exercise->part_five) {
                    $reading_exercise->part_five->forceDelete();
                }

                ReadingPartOneFiveQuestion::create([
                    'advice' => $request->part_one_advice,
                    'type' => '0',
                    'reading_exercise_id' => $reading_exercise->id,
                ]);

                ReadingPartTwoThree::create([
                    'advice' => $request->part_two_advice,
                    'type' => '0',
                    'reading_exercise_id' => $reading_exercise->id,
                ]);

                ReadingPartTwoThree::create([
                    'advice' => $request->part_three_advice,
                    'type' => '1',
                    'reading_exercise_id' => $reading_exercise->id,
                ]);

                ReadingPartFour::create([
                    'advice' => $request->part_four_advice,
                    'reading_exercise_id' => $reading_exercise->id,
                ]);

                ReadingPartOneFiveQuestion::create([
                    'advice' => $request->part_five_advice,
                    'type' => '1',
                    'reading_exercise_id' => $reading_exercise->id,
                ]);
            }else{
                //  change part

                if ($reading_exercise->part_one) {
                    $reading_exercise->part_one->forceDelete();
                }
                if ($reading_exercise->part_two) {
                    $reading_exercise->part_two->forceDelete();
                }
                if ($reading_exercise->part_three) {
                    $reading_exercise->part_three->forceDelete();
                }
                if ($reading_exercise->part_four) {
                    $reading_exercise->part_four->forceDelete();
                }
                if ($reading_exercise->part_five) {
                    $reading_exercise->part_five->forceDelete();
                }


                if ($request->part_category == '1') {
                    ReadingPartOneFiveQuestion::create([
                        'advice' => $request->advice,
                        'type' => '0',
                        'reading_exercise_id' => $reading_exercise->id,
                    ]);
                } elseif ($request->part_category == '2') {
                    ReadingPartTwoThree::create([
                        'advice' => $request->advice,
                        'type' => '0',
                        'reading_exercise_id' => $reading_exercise->id,
                    ]);
                } elseif ($request->part_category == '3') {
                    ReadingPartTwoThree::create([
                        'advice' => $request->advice,
                        'type' => '1',
                        'reading_exercise_id' => $reading_exercise->id,
                    ]);
                } elseif ($request->part_category == '4') {
                    ReadingPartFour::create([
                        'advice' => $request->advice,
                        'reading_exercise_id' => $reading_exercise->id,
                    ]);
                } elseif ($request->part_category == '5') {
                    ReadingPartOneFiveQuestion::create([
                        'advice' => $request->advice,
                        'type' => '1',
                        'reading_exercise_id' => $reading_exercise->id,
                    ]);
                }
            }
        }

        return redirect()->route('admin.readings.exercise.index', ['id' => $reading_exercise->exercise_id]);
    }

    public function update1(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:3',
            'part_one_advice' => 'required',
            'part_two_advice' => 'required',
            'part_three_advice' => 'required',
            'part_four_advice' => 'required',
            'part_five_advice' => 'required',
            'difficulty' => 'required',
//            'time_limit' => 'required|numeric|min:1',
        ]);
        $full_exercise = FullExercise::find($request->exercise_id);
        $reading_exercise = $full_exercise->reading_exercise;

        $reading_exercise->update([
            'name' => $request->title,
            'difficulty' => $request->difficulty,
            'time_limit' => $request->time_limit ?? 0 ,
        ]);

        $reading_exercise->part_one->update([
            'advice' => $request->part_one_advice,
        ]);

        $reading_exercise->part_two->update([
            'advice' => $request->part_two_advice,
        ]);
        $reading_exercise->part_three->update([
            'advice' => $request->part_three_advice,
        ]);
        $reading_exercise->part_four->update([
            'advice' => $request->part_four_advice,
        ]);
        $reading_exercise->part_five->update([
            'advice' => $request->part_five_advice,
        ]);
//            dd($reading_exercise);
        return redirect()->route('admin.full_exercise.more.index', ['id' => $full_exercise->id]);
    }
    public function update2(Request $request ,$id)
    {
        $this->validate($request, [
            'title' => 'required|min:3',
            'part_one_advice' => 'required',
            'part_two_advice' => 'required',
            'part_three_advice' => 'required',
            'part_four_advice' => 'required',
            'part_five_advice' => 'required',
            'difficulty' => 'required',
//            'time_limit' => 'required|numeric|min:1',
        ]);
        $reading_exercise = ReadingExercise::find($id);

        $reading_exercise->update([
            'name' => $request->title,
            'difficulty' => $request->difficulty,
            'time_limit' => $request->time_limit ?? 0,
        ]);

        $reading_exercise->part_one->update([
            'advice' => $request->part_one_advice,
        ]);

        $reading_exercise->part_two->update([
            'advice' => $request->part_two_advice,
        ]);
        $reading_exercise->part_three->update([
            'advice' => $request->part_three_advice,
        ]);
        $reading_exercise->part_four->update([
            'advice' => $request->part_four_advice,
        ]);
        $reading_exercise->part_five->update([
            'advice' => $request->part_five_advice,
        ]);
//            dd($reading_exercise);
        return redirect()->route('admin.readings.exercise.index', ['id' => $reading_exercise->exercise_id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
//        dd(1);
        $readingExercise = ReadingExercise::findOrFail($id);
        $readingExercise->delete();
        return redirect()->back();
    }
}
