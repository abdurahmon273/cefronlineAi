<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exercise;
use App\Models\FullExercise;
use App\Models\Lesson;
use App\Models\ListeningExercise;
use App\Models\ListeningPartOneFifeQuestion;
use App\Models\ListeningPartThreeFour;
use App\Models\ListeningPartTwoSixQuestion;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use function Laravel\Prompts\error;
use function Sodium\add;

class ListeningExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }
    public function full_index($id)
    {
     $listening_exercise=ListeningExercise::where('exercise_id',$id)->first();

        $listenings = ListeningExercise::where('exercise_id',$id)->get();
        $exercise=Exercise::find($id)->exercise_id;
        return view('admin.listening_exercises.index', compact('listenings','id','exercise'));
    }

    public function index1($id)
    {
//        abort_if(Gate::denies('lesson_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $listenings = ListeningExercise::where('exercise_id',$id)->orderBy('order_id')->get();
        $exercise=Exercise::find($id)->exercise_id;
        return view('admin.listening_exercises.index', compact('listenings','id','exercise'));
    }
    public function listening_full_index($id,$exercise_id)
    {
        $listening_exercise=ListeningExercise::with('part_one')->find($id);
        return view('admin.full_mock.listening.index', compact('listening_exercise'));
//        return view('admin.listening_full.index', compact('listening_exercise','exercise_id'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
     return view('admin.listening_exercises.create');
    }

    public function create1($id)
    {
     return view('admin.listening_exercises.create',compact('id'));
    }
    public function change($id)
    {
        $FullExercise=FullExercise::find($id);
        if ($FullExercise->listening_exercise->part_one) {
            return view('admin.full_mock.listening.edit',compact('FullExercise','id'));

        }else{
            return view('admin.full_mock.listening.create',compact('FullExercise','id'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }
    public function store1(Request $request)
    {
        $this->validate($request, [
            'part_category' => 'required',
        ]);

        if ($request->part_category=='7'){

            $this->validate($request,[
                'name'=>'required|min:3',
                'status'=>'required',
                'exercise_id'=>'required',
                'part_category'=>'required',
                'difficulty'=>'required',

                'part_one_advice'=>'required',
                'part_one_audio' => 'required|max:102400', // 100MB
                'part_two_advice'=>'required',
                'part_two_audio' => 'required|max:102400', // 100MB
                'part_three_advice'=>'required',
                'part_three_audio' => 'required|max:102400', // 100MB
                'part_four_advice'=>'required',
                'part_four_audio' => 'required|max:102400', // 100MB
                'part_five_advice'=>'required',
                'part_five_audio' => 'required|max:102400', // 100MB
                'part_six_advice'=>'required',
                'part_six_audio' => 'required|max:102400', // 100MB
            ]);
            $maxOrderId = ListeningExercise::where('exercise_id', $request->exercise_id)->max('order_id');
            $nextOrderId = $maxOrderId ? $maxOrderId + 1 : 1; // If no records exist, start with 1

            $listening= ListeningExercise::create([
                'name'=>$request->name,
                'part_category'=>$request->part_category,
                'status'=>$request->status,
                'difficulty'=>$request->difficulty,
                'time_limit'=>0,
                'exercise_id'=>$request->exercise_id,
                'order_id' => $nextOrderId, // Assign next order_id
            ]);


            //part 1
            $uniqueFilename='';
            if ($request->hasFile('part_one_audio')) {
                $extension = $request->file('part_one_audio')->getClientOriginalExtension();
                $uniqueFilename = 'part_one_audio_' . time() . '_' . uniqid() . '.' . $extension;
                $request->file('part_one_audio')->storeAs('public/audios', $uniqueFilename);
            }
            $part_one=ListeningPartOneFifeQuestion::create([
                'audio'=>$uniqueFilename,
                'advice'=>$request->part_one_advice,
                'type'=>'0',
                'listening_exercise_id'=>$listening->id,
            ]);

            //part 2
            $uniqueFilename='';
            if ($request->hasFile('part_two_audio')) {
                $extension = $request->file('part_two_audio')->getClientOriginalExtension();
                $uniqueFilename = 'part_two_audio_' . time() . '_' . uniqid() . '.' . $extension;
                $request->file('part_two_audio')->storeAs('public/audios', $uniqueFilename);
            }
            $part_two=ListeningPartTwoSixQuestion::create([
                'audio'=>$uniqueFilename,
                'advice'=>$request->part_two_advice,
                'text'=>'text',
                'type'=>'0',
                'listening_exercise_id'=>$listening->id,
            ]);

            //part 3
            $uniqueFilename='';
            if ($request->hasFile('part_three_audio')) {
                $extension = $request->file('part_three_audio')->getClientOriginalExtension();
                $uniqueFilename = 'part_three_audio_' . time() . '_' . uniqid() . '.' . $extension;
                $request->file('part_three_audio')->storeAs('public/audios', $uniqueFilename);
            }
            $part_three=ListeningPartThreeFour::create([
                'audio'=>$uniqueFilename,
                'advice'=>$request->part_three_advice,
                'text'=>'no question',
                'type'=>'0',
                'listening_exercise_id'=>$listening->id,
            ]);

            //part 4
            $uniqueFilename='';
            if ($request->hasFile('part_four_audio')) {
                $extension = $request->file('part_four_audio')->getClientOriginalExtension();
                $uniqueFilename = 'part_four_audio_' . time() . '_' . uniqid() . '.' . $extension;
                $request->file('part_four_audio')->storeAs('public/audios', $uniqueFilename);
            }
            $part_four=ListeningPartThreeFour::create([
                'audio'=>$uniqueFilename,
                'advice'=>$request->part_four_advice,
                'text'=>'no text',
                'type'=>'1',
                'listening_exercise_id'=>$listening->id,
            ]);

            //part 5
            $uniqueFilename='';
            if ($request->hasFile('part_five_audio')) {
                $extension = $request->file('part_five_audio')->getClientOriginalExtension();
                $uniqueFilename = 'part_five_audio_' . time() . '_' . uniqid() . '.' . $extension;
                $request->file('part_five_audio')->storeAs('public/audios', $uniqueFilename);
            }
            $part_five=ListeningPartOneFifeQuestion::create([
                'audio'=>$uniqueFilename,
                'advice'=>$request->part_five_advice,
                'type'=>'1',
                'listening_exercise_id'=>$listening->id,
            ]);

            //part 6
            $uniqueFilename='';
            if ($request->hasFile('part_six_audio')) {
                $extension = $request->file('part_six_audio')->getClientOriginalExtension();
                $uniqueFilename = 'part_six_audio_' . time() . '_' . uniqid() . '.' . $extension;
                $request->file('part_six_audio')->storeAs('public/audios', $uniqueFilename);
            }
            $part_six=ListeningPartTwoSixQuestion::create([
                'audio'=>$uniqueFilename,
                'advice'=>$request->part_six_advice,
                'text'=>'text',
                'type'=>'1',
                'listening_exercise_id'=>$listening->id,
            ]);

        }
        else{
            $this->validate($request,[
                'name'=>'required|min:3',
                'status'=>'required',
                'exercise_id'=>'required',
                'part_category'=>'required',
                'advice'=>'required',
                'audio' => 'required|max:102400', // 100MB
                'difficulty'=>'required',
            ]);
            $maxOrderId = ListeningExercise::where('exercise_id', $request->exercise_id)->max('order_id');
            $nextOrderId = $maxOrderId ? $maxOrderId + 1 : 1; // If no records exist, start with 1

            $listening= ListeningExercise::create([
                'name'=>$request->name,
                'part_category'=>$request->part_category,
                'status'=>$request->status,
                'difficulty'=>$request->difficulty,
                'time_limit'=>0,
                'exercise_id'=>$request->exercise_id,
                'order_id'=>$nextOrderId,
            ]);

            $uniqueFilename='';

            if ($request->hasFile('audio')) {
                $extension = $request->file('audio')->getClientOriginalExtension();
                $uniqueFilename = 'audio_' . time() . '_' . uniqid() . '.' . $extension;
                $request->file('audio')->storeAs('public/audios', $uniqueFilename);
            }

            if ($request->part_category=='1'){
                $part_one=ListeningPartOneFifeQuestion::create([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->advice,
                    'type'=>'0',
                    'listening_exercise_id'=>$listening->id,
                ]);
            }elseif ($request->part_category=='5'){
                $part_one=ListeningPartOneFifeQuestion::create([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->advice,
                    'type'=>'1',
                    'listening_exercise_id'=>$listening->id,
                ]);
            }elseif ($request->part_category=='2'){
                $part_two=ListeningPartTwoSixQuestion::create([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->advice,
                    'text'=>'text',
                    'type'=>'0',
                    'listening_exercise_id'=>$listening->id,
                ]);
            }elseif ($request->part_category=='6'){
                $part_two=ListeningPartTwoSixQuestion::create([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->advice,
                    'text'=>'text',
                    'type'=>'1',
                    'listening_exercise_id'=>$listening->id,
                ]);
            }elseif ($request->part_category=='3'){
                $part_three=ListeningPartThreeFour::create([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->advice,
                    'text'=>'no question',
                    'type'=>'0',
                    'listening_exercise_id'=>$listening->id,
                ]);
            }elseif ($request->part_category=='4'){
                $part_four=ListeningPartThreeFour::create([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->advice,
                    'type'=>'1',
                    'listening_exercise_id'=>$listening->id,
                ]);
            }

        }

        return redirect()->route('admin.listenings.exercise.index',['id'=>$listening->exercise_id] );
    }
    public function store2(Request $request)
    {

        $this->validate($request,[
            'title'=>'required|min:3',
            'part_one_advice'=>'required',
            'part_one_audio' => 'required|max:102400', // 100MB
            'part_two_advice'=>'required',
            'part_two_audio' => 'required|max:102400', // 100MB
            'part_three_advice'=>'required',
            'part_three_audio' => 'required|max:102400', // 100MB
            'part_four_advice'=>'required',
            'part_four_audio' => 'required|max:102400', // 100MB
            'part_five_advice'=>'required',
            'part_five_audio' => 'required|max:102400', // 100MB
            'part_six_advice'=>'required',
            'part_six_audio' => 'required|max:102400', // 100MB
            'difficulty'=>'required',
            'time_limit'=>'required|numeric|min:1',
        ]);

        $full_exercise=FullExercise::find($request->exercise_id);
        $listening= $full_exercise->listening_exercise;

        $listening->update([
            'difficulty'=>$request->difficulty,
            'time_limit'=>$request->time_limit,
        ]);

        //part 1
        $uniqueFilename='';
        if ($request->hasFile('part_one_audio')) {
            $extension = $request->file('part_one_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_one_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_one_audio')->storeAs('public/audios', $uniqueFilename);
        }
        $part_one=ListeningPartOneFifeQuestion::create([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_one_advice,
            'type'=>'0',
            'listening_exercise_id'=>$listening->id,
        ]);

        //part 2
        $uniqueFilename='';
        if ($request->hasFile('part_two_audio')) {
            $extension = $request->file('part_two_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_two_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_two_audio')->storeAs('public/audios', $uniqueFilename);
        }
        $part_two=ListeningPartTwoSixQuestion::create([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_two_advice,
            'text'=>'text',
            'type'=>'0',
            'listening_exercise_id'=>$listening->id,
        ]);

        //part 3
        $uniqueFilename='';
        if ($request->hasFile('part_three_audio')) {
            $extension = $request->file('part_three_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_three_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_three_audio')->storeAs('public/audios', $uniqueFilename);
        }
        $part_three=ListeningPartThreeFour::create([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_three_advice,
            'text'=>'no question',
            'type'=>'0',
            'listening_exercise_id'=>$listening->id,
        ]);

        //part 4
        $uniqueFilename='';
        if ($request->hasFile('part_four_audio')) {
            $extension = $request->file('part_four_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_four_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_four_audio')->storeAs('public/audios', $uniqueFilename);
        }
        $part_four=ListeningPartThreeFour::create([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_four_advice,
            'text'=>'no text',
            'type'=>'1',
            'listening_exercise_id'=>$listening->id,
        ]);

        //part 5
        $uniqueFilename='';
        if ($request->hasFile('part_five_audio')) {
            $extension = $request->file('part_five_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_five_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_five_audio')->storeAs('public/audios', $uniqueFilename);
        }
        $part_five=ListeningPartOneFifeQuestion::create([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_five_advice,
            'type'=>'1',
            'listening_exercise_id'=>$listening->id,
        ]);

        //part 6
        $uniqueFilename='';
        if ($request->hasFile('part_six_audio')) {
            $extension = $request->file('part_six_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_six_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_six_audio')->storeAs('public/audios', $uniqueFilename);
        }
        $part_six=ListeningPartTwoSixQuestion::create([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_six_advice,
            'text'=>'text',
            'type'=>'1',
            'listening_exercise_id'=>$listening->id,
        ]);


        return redirect()->route('admin.full_exercise.more.index',['id'=>$full_exercise->id] );
    }

    /**
     * Display the specified resource.
     */
    public function show(ListeningExercise $listeningExercise)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $listening=ListeningExercise::with('listening_part_one_fife_question','listening_part_two_six_question')->find($id);

            return view('admin.listening_exercises.edit',compact('listening'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'part_category' => 'required',
        ]);

        $listeningExercise=ListeningExercise::with('listening_part_one_fife_question','listening_part_two_six_question','listening_part_three_four','part_one','part_two','part_three','part_four','part_five','part_six')->find($id);

        if ($request->part_category=='7'){

            if ($listeningExercise->part_category=='7'){
                $this->validate($request,[
                    'name'=>'required|min:3',
                    'status'=>'required',
                    'exercise_id'=>'required',
                    'part_category'=>'required',
                    'difficulty'=>'required',

                    'part_one_advice'=>'required',
                    'part_one_audio' => 'max:102400', // 100MB
                    'part_two_advice'=>'required',
                    'part_two_audio' => 'max:102400', // 100MB
                    'part_three_advice'=>'required',
                    'part_three_audio' => 'max:102400', // 100MB
                    'part_four_advice'=>'required',
                    'part_four_audio' => 'max:102400', // 100MB
                    'part_five_advice'=>'required',
                    'part_five_audio' => 'max:102400', // 100MB
                    'part_six_advice'=>'required',
                    'part_six_audio' => 'max:102400', // 100MB
                ]);
            }else{

                if ($listeningExercise->part_category=='1'){
                    $this->validate($request,[
                        'name'=>'required|min:3',
                        'status'=>'required',
                        'exercise_id'=>'required',
                        'part_category'=>'required',
                        'difficulty'=>'required',

                        'part_one_advice'=>'required',
                        'part_two_advice'=>'required',
                        'part_three_advice'=>'required',
                        'part_four_advice'=>'required',
                        'part_five_advice'=>'required',
                        'part_six_advice'=>'required',

                        'part_two_audio' => 'required|max:102400', // 100MB
                        'part_three_audio' => 'required|max:102400', // 100MB
                        'part_four_audio' => 'required|max:102400', // 100MB
                        'part_five_audio' => 'required|max:102400', // 100MB
                        'part_six_audio' => 'required|max:102400', // 100MB
                    ]);
                }elseif ($listeningExercise->part_category=='2'){
                    $this->validate($request,[
                        'name'=>'required|min:3',
                        'status'=>'required',
                        'exercise_id'=>'required',
                        'part_category'=>'required',
                        'difficulty'=>'required',

                        'part_one_advice'=>'required',
                        'part_two_advice'=>'required',
                        'part_three_advice'=>'required',
                        'part_four_advice'=>'required',
                        'part_five_advice'=>'required',
                        'part_six_advice'=>'required',

                        'part_one_audio' => 'required|max:102400', // 100MB
                        'part_three_audio' => 'required|max:102400', // 100MB
                        'part_four_audio' => 'required|max:102400', // 100MB
                        'part_five_audio' => 'required|max:102400', // 100MB
                        'part_six_audio' => 'required|max:102400', // 100MB
                    ]);
                }elseif ($listeningExercise->part_category=='3'){
                    $this->validate($request,[
                        'name'=>'required|min:3',
                        'status'=>'required',
                        'exercise_id'=>'required',
                        'part_category'=>'required',
                        'difficulty'=>'required',

                        'part_one_advice'=>'required',
                        'part_two_advice'=>'required',
                        'part_three_advice'=>'required',
                        'part_four_advice'=>'required',
                        'part_five_advice'=>'required',
                        'part_six_advice'=>'required',

                        'part_one_audio' => 'required|max:102400', // 100MB
                        'part_two_audio' => 'required|max:102400', // 100MB
                        'part_four_audio' => 'required|max:102400', // 100MB
                        'part_five_audio' => 'required|max:102400', // 100MB
                        'part_six_audio' => 'required|max:102400', // 100MB
                    ]);
                }elseif ($listeningExercise->part_category=='4'){
                    $this->validate($request,[
                        'name'=>'required|min:3',
                        'status'=>'required',
                        'exercise_id'=>'required',
                        'part_category'=>'required',
                        'difficulty'=>'required',

                        'part_one_advice'=>'required',
                        'part_two_advice'=>'required',
                        'part_three_advice'=>'required',
                        'part_four_advice'=>'required',
                        'part_five_advice'=>'required',
                        'part_six_advice'=>'required',

                        'part_one_audio' => 'required|max:102400', // 100MB
                        'part_two_audio' => 'required|max:102400', // 100MB
                        'part_three_audio' => 'required|max:102400', // 100MB
                        'part_five_audio' => 'required|max:102400', // 100MB
                        'part_six_audio' => 'required|max:102400', // 100MB
                    ]);
                }elseif ($listeningExercise->part_category=='5'){
                    $this->validate($request,[
                        'name'=>'required|min:3',
                        'status'=>'required',
                        'exercise_id'=>'required',
                        'part_category'=>'required',
                        'difficulty'=>'required',

                        'part_one_advice'=>'required',
                        'part_two_advice'=>'required',
                        'part_three_advice'=>'required',
                        'part_four_advice'=>'required',
                        'part_five_advice'=>'required',
                        'part_six_advice'=>'required',

                        'part_one_audio' => 'required|max:102400', // 100MB
                        'part_two_audio' => 'required|max:102400', // 100MB
                        'part_three_audio' => 'required|max:102400', // 100MB
                        'part_four_audio' => 'required|max:102400', // 100MB
                        'part_six_audio' => 'required|max:102400', // 100MB
                    ]);
                }elseif ($listeningExercise->part_category=='6'){
                    $this->validate($request,[
                        'name'=>'required|min:3',
                        'status'=>'required',
                        'exercise_id'=>'required',
                        'part_category'=>'required',
                        'difficulty'=>'required',

                        'part_one_advice'=>'required',
                        'part_two_advice'=>'required',
                        'part_three_advice'=>'required',
                        'part_four_advice'=>'required',
                        'part_five_advice'=>'required',
                        'part_six_advice'=>'required',

                        'part_one_audio' => 'required|max:102400', // 100MB
                        'part_two_audio' => 'required|max:102400', // 100MB
                        'part_three_audio' => 'required|max:102400', // 100MB
                        'part_four_audio' => 'required|max:102400', // 100MB
                        'part_five_audio' => 'required|max:102400', // 100MB
                    ]);
                }

            }


        }else {

            $this->validate($request, [
                'name' => 'required|min:3',
                'status' => 'required',
                'exercise_id' => 'required',
                'part_category' => 'required',
                'advice' => 'required',
//                'audio' => 'required|max:102400', // 100MB
                'difficulty' => 'required',
            ]);
        }

        $old_part_category=$listeningExercise->part_category;
        $listeningExercise->update($request->all());

        $uniqueFilename='';
        $part='';

        if ($listeningExercise->listening_part_one_fife_question){
            $part=$listeningExercise->listening_part_one_fife_question;
        }elseif ($listeningExercise->listening_part_two_six_question){
            $part=$listeningExercise->listening_part_two_six_question;
        }elseif ($listeningExercise->listening_part_three_four){
            $part=$listeningExercise->listening_part_three_four;
        }


        if ($old_part_category!=$request->part_category){


            if ($request->part_category=='7'){

                //part 1
                $uniqueFilename='';
                if ($request->hasFile('part_one_audio')) {
                    $extension = $request->file('part_one_audio')->getClientOriginalExtension();
                    $uniqueFilename = 'part_one_audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('part_one_audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$part->audio;
                }
                $part_one=ListeningPartOneFifeQuestion::create([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->part_one_advice,
                    'type'=>'0',
                    'listening_exercise_id'=>$listeningExercise->id,
                ]);

                //part 2
                $uniqueFilename='';
                if ($request->hasFile('part_two_audio')) {
                    $extension = $request->file('part_two_audio')->getClientOriginalExtension();
                    $uniqueFilename = 'part_two_audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('part_two_audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$part->audio;
                }
                $part_two=ListeningPartTwoSixQuestion::create([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->part_two_advice,
                    'text'=>'text',
                    'type'=>'0',
                    'listening_exercise_id'=>$listeningExercise->id,
                ]);

                //part 3
                $uniqueFilename='';
                if ($request->hasFile('part_three_audio')) {
                    $extension = $request->file('part_three_audio')->getClientOriginalExtension();
                    $uniqueFilename = 'part_three_audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('part_three_audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$part->audio;
                }
                $part_three=ListeningPartThreeFour::create([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->part_three_advice,
                    'text'=>'no question',
                    'type'=>'0',
                    'listening_exercise_id'=>$listeningExercise->id,
                ]);

                //part 4
                $uniqueFilename='';
                if ($request->hasFile('part_four_audio')) {
                    $extension = $request->file('part_four_audio')->getClientOriginalExtension();
                    $uniqueFilename = 'part_four_audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('part_four_audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$part->audio;
                }
                $part_four=ListeningPartThreeFour::create([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->part_four_advice,
                    'text'=>'no text',
                    'type'=>'1',
                    'listening_exercise_id'=>$listeningExercise->id,
                ]);

                //part 5
                $uniqueFilename='';
                if ($request->hasFile('part_five_audio')) {
                    $extension = $request->file('part_five_audio')->getClientOriginalExtension();
                    $uniqueFilename = 'part_five_audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('part_five_audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$part->audio;
                }
                $part_five=ListeningPartOneFifeQuestion::create([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->part_five_advice,
                    'type'=>'1',
                    'listening_exercise_id'=>$listeningExercise->id,
                ]);

                //part 6
                $uniqueFilename='';
                if ($request->hasFile('part_six_audio')) {
                    $extension = $request->file('part_six_audio')->getClientOriginalExtension();
                    $uniqueFilename = 'part_six_audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('part_six_audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$part->audio;
                }
                $part_six=ListeningPartTwoSixQuestion::create([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->part_six_advice,
                    'text'=>'text',
                    'type'=>'1',
                    'listening_exercise_id'=>$listeningExercise->id,

                ]);
            }
            else{
                $uniqueFilename='';

                if ($request->hasFile('audio')) {
                    $extension = $request->file('audio')->getClientOriginalExtension();
                    $uniqueFilename = 'audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$part->audio;
                }
//                if ($old_part_category=='7'){
                $text_test='0';
                    if ($listeningExercise->part_one){
                        $listeningExercise->part_one->delete();
                        $text_test.='-1';
                    }
                if ($listeningExercise->part_two){
                    $listeningExercise->part_two->delete();
                    $text_test.='-2';
                }
                if ($listeningExercise->part_three){
                    $listeningExercise->part_three->delete();
                    $text_test.='-3';
                }
                if ($listeningExercise->part_four){
                    $listeningExercise->part_four->delete();
                    $text_test.='-4';
                }
                if ($listeningExercise->part_five){
                    $listeningExercise->part_five->delete();
                    $text_test.='-5';
                }
                if ($listeningExercise->part_six){
                    $listeningExercise->part_six->delete();
                    $text_test.='-6';
                }


                if ($request->part_category=='1'){
                    $part_one=ListeningPartOneFifeQuestion::create([
                        'audio'=>$uniqueFilename,
                        'advice'=>$request->advice,
                        'type'=>'0',
                        'listening_exercise_id'=>$listeningExercise->id,
                    ]);
                }elseif ($request->part_category=='5'){
                    $part_one=ListeningPartOneFifeQuestion::create([
                        'audio'=>$uniqueFilename,
                        'advice'=>$request->advice,
                        'type'=>'1',
                        'listening_exercise_id'=>$listeningExercise->id,
                    ]);
                }elseif ($request->part_category=='2'){
                    $part_two=ListeningPartTwoSixQuestion::create([
                        'audio'=>$uniqueFilename,
                        'advice'=>$request->advice,
                        'text'=>'text',
                        'type'=>'0',
                        'listening_exercise_id'=>$listeningExercise->id,
                    ]);
                }elseif ($request->part_category=='6'){
                    $part_two=ListeningPartTwoSixQuestion::create([
                        'audio'=>$uniqueFilename,
                        'advice'=>$request->advice,
                        'text'=>'text',
                        'type'=>'1',
                        'listening_exercise_id'=>$listeningExercise->id,
                    ]);
                }elseif ($request->part_category=='3'){
                    $part_three=ListeningPartThreeFour::create([
                        'audio'=>$uniqueFilename,
                        'advice'=>$request->advice,
                        'text'=>'no question',
                        'type'=>'0',
                        'listening_exercise_id'=>$listeningExercise->id,
                    ]);
                }elseif ($request->part_category=='4'){
                    $part_four=ListeningPartThreeFour::create([
                        'audio'=>$uniqueFilename,
                        'advice'=>$request->advice,
                        'type'=>'1',
                        'listening_exercise_id'=>$listeningExercise->id,
                    ]);
                }
            }
        }
        else{
            if ($request->part_category=='7'){
                $uniqueFilename='';
                if ($request->hasFile('part_one_audio')) {
                    if ($listeningExercise->part_one->audio) {
                        Storage::delete('public/audios/' . $listeningExercise->part_one->audio);
                    }
                    $extension = $request->file('part_one_audio')->getClientOriginalExtension();
                    $uniqueFilename = 'part_one_audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('part_one_audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$listeningExercise->part_one->audio;
                }

                $listeningExercise->part_one->update([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->part_one_advice,
                ]);

                //part 2
                $uniqueFilename='';
                if ($request->hasFile('part_two_audio')) {
                    if ($listeningExercise->part_two->audio) {
                        Storage::delete('public/audios/' . $listeningExercise->part_two->audio);
                    }
                    $extension = $request->file('part_two_audio')->getClientOriginalExtension();
                    $uniqueFilename = 'part_two_audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('part_two_audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$listeningExercise->part_two->audio;
                }

                $listeningExercise->part_two->update([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->part_two_advice,
                ]);

                //part 3
                $uniqueFilename='';
                if ($request->hasFile('part_three_audio')) {
                    if ($listeningExercise->part_three->audio) {
                        Storage::delete('public/audios/' . $listeningExercise->part_three->audio);
                    }
                    $extension = $request->file('part_three_audio')->getClientOriginalExtension();
                    $uniqueFilename = 'part_three_audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('part_three_audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$listeningExercise->part_three->audio;
                }

                $listeningExercise->part_three->update([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->part_three_advice,
                ]);

                //part 4
                $uniqueFilename='';
                if ($request->hasFile('part_four_audio')) {
                    if ($listeningExercise->part_four->audio) {
                        Storage::delete('public/audios/' . $listeningExercise->part_four->audio);
                    }
                    $extension = $request->file('part_four_audio')->getClientOriginalExtension();
                    $uniqueFilename = 'part_four_audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('part_four_audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$listeningExercise->part_four->audio;
                }

                $listeningExercise->part_four->update([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->part_four_advice,
                ]);

                //part 5
                $uniqueFilename='';
                if ($request->hasFile('part_five_audio')) {
                    if ($listeningExercise->part_five->audio) {
                        Storage::delete('public/audios/' . $listeningExercise->part_five->audio);
                    }
                    $extension = $request->file('part_five_audio')->getClientOriginalExtension();
                    $uniqueFilename = 'part_five_audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('part_five_audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$listeningExercise->part_five->audio;
                }

                $listeningExercise->part_five->update([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->part_five_advice,
                ]);

                //part 6
                $uniqueFilename='';
                if ($request->hasFile('part_six_audio')) {
                    if ($listeningExercise->part_six->audio) {
                        Storage::delete('public/audios/' . $listeningExercise->part_six->audio);
                    }
                    $extension = $request->file('part_six_audio')->getClientOriginalExtension();
                    $uniqueFilename = 'part_six_audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('part_six_audio')->storeAs('public/audios', $uniqueFilename);
                }else{
                    $uniqueFilename=$listeningExercise->part_six->audio;
                }

                $listeningExercise->part_six->update([
                    'audio'=>$uniqueFilename,
                    'advice'=>$request->part_six_advice,
                ]);
            }else{

                if ($part){
                    $part->update(['advice'=>$request->advice]);
                }

                if ($request->hasFile('audio') && $part) {
                    // delete history audio $name= $part->audio
                    if ($part->audio) {
                        Storage::delete('public/audios/' . $part->audio);
                    }

                    $extension = $request->file('audio')->getClientOriginalExtension();
                    $uniqueFilename = 'audio_' . time() . '_' . uniqid() . '.' . $extension;
                    $request->file('audio')->storeAs('public/audios', $uniqueFilename);
                    $part->update(['audio'=>$uniqueFilename]);
                }
            }

        }



        return redirect()->route('admin.listenings.exercise.index',['id'=>$listeningExercise->exercise_id]);
    }

    public function update1(Request $request,$id)
    {
//        dd($request->all());

        $this->validate($request,[
            'title'=>'required|min:3',
            'part_one_advice'=>'required',
            'part_two_advice'=>'required',
            'part_three_advice'=>'required',
            'part_four_advice'=>'required',
            'part_five_advice'=>'required',
            'part_six_advice'=>'required',
            'difficulty'=>'required',
        ]);

        $listening= ListeningExercise::find($id);

        $listening->update([
            'name'=>$request->title,
            'difficulty'=>$request->difficulty,
            'time_limit'=>$request->time_limit,
        ]);

        //part 1
        $uniqueFilename='';
        if ($request->hasFile('part_one_audio')) {
            if ($listening->part_one->audio) {
                Storage::delete('public/audios/' . $listening->part_one->audio);
            }
            $extension = $request->file('part_one_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_one_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_one_audio')->storeAs('public/audios', $uniqueFilename);
        }else{
            $uniqueFilename=$listening->part_one->audio;
        }

        $listening->part_one->update([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_one_advice,
        ]);

        //part 2
        $uniqueFilename='';
        if ($request->hasFile('part_two_audio')) {
            if ($listening->part_two->audio) {
                Storage::delete('public/audios/' . $listening->part_two->audio);
            }
            $extension = $request->file('part_two_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_two_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_two_audio')->storeAs('public/audios', $uniqueFilename);
        }else{
            $uniqueFilename=$listening->part_two->audio;
        }

        $listening->part_two->update([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_two_advice,
        ]);

        //part 3
        $uniqueFilename='';
        if ($request->hasFile('part_three_audio')) {
            if ($listening->part_three->audio) {
                Storage::delete('public/audios/' . $listening->part_three->audio);
            }
            $extension = $request->file('part_three_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_three_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_three_audio')->storeAs('public/audios', $uniqueFilename);
        }else{
            $uniqueFilename=$listening->part_three->audio;
        }

        $listening->part_three->update([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_three_advice,
        ]);

        //part 4
        $uniqueFilename='';
        if ($request->hasFile('part_four_audio')) {
            if ($listening->part_four->audio) {
                Storage::delete('public/audios/' . $listening->part_four->audio);
            }
            $extension = $request->file('part_four_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_four_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_four_audio')->storeAs('public/audios', $uniqueFilename);
        }else{
            $uniqueFilename=$listening->part_four->audio;
        }

        $listening->part_four->update([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_four_advice,
        ]);

        //part 5
        $uniqueFilename='';
        if ($request->hasFile('part_five_audio')) {
            if ($listening->part_five->audio) {
                Storage::delete('public/audios/' . $listening->part_five->audio);
            }
            $extension = $request->file('part_five_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_five_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_five_audio')->storeAs('public/audios', $uniqueFilename);
        }else{
            $uniqueFilename=$listening->part_five->audio;
        }

        $listening->part_five->update([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_five_advice,
        ]);

        //part 6
        $uniqueFilename='';
        if ($request->hasFile('part_six_audio')) {
            if ($listening->part_six->audio) {
                Storage::delete('public/audios/' . $listening->part_six->audio);
            }
            $extension = $request->file('part_six_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_six_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_six_audio')->storeAs('public/audios', $uniqueFilename);
        }else{
            $uniqueFilename=$listening->part_six->audio;
        }

        $listening->part_six->update([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_six_advice,
        ]);



        return redirect()->route('admin.listenings.exercise.index',['id'=>$listening->exercise_id] );
    }
    public function update2(Request $request)
    {

        $this->validate($request,[
            'title'=>'required|min:3',
            'part_one_advice'=>'required',
            'part_two_advice'=>'required',
            'part_three_advice'=>'required',
            'part_four_advice'=>'required',
            'part_five_advice'=>'required',
            'part_six_advice'=>'required',
//            'part_one_audio' => 'required|max:102400', // 100MB
//            'part_two_audio' => 'required|max:102400', // 100MB
//            'part_three_audio' => 'required|max:102400', // 100MB
//            'part_four_audio' => 'required|max:102400', // 100MB
//            'part_five_audio' => 'required|max:102400', // 100MB
//            'part_six_audio' => 'required|max:102400', // 100MB
            'difficulty'=>'required',
        ]);

        $full_exercise=FullExercise::find($request->exercise_id);
        $listening= $full_exercise->listening_exercise;

        $listening->update([
            'name'=>$request->title,
            'difficulty'=>$request->difficulty,
        ]);

        //part 1
        $uniqueFilename='';
        if ($request->hasFile('part_one_audio')) {
            if ($listening->part_one->audio) {
                Storage::delete('public/audios/' . $listening->part_one->audio);
            }
            $extension = $request->file('part_one_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_one_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_one_audio')->storeAs('public/audios', $uniqueFilename);
        }else{
            $uniqueFilename=$listening->part_one->audio;
        }

        $listening->part_one->update([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_one_advice,
        ]);

        //part 2
        $uniqueFilename='';
        if ($request->hasFile('part_two_audio')) {
            if ($listening->part_two->audio) {
                Storage::delete('public/audios/' . $listening->part_two->audio);
            }
            $extension = $request->file('part_two_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_two_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_two_audio')->storeAs('public/audios', $uniqueFilename);
        }else{
            $uniqueFilename=$listening->part_two->audio;
        }

        $listening->part_two->update([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_two_advice,
        ]);

        //part 3
        $uniqueFilename='';
        if ($request->hasFile('part_three_audio')) {
            if ($listening->part_three->audio) {
                Storage::delete('public/audios/' . $listening->part_three->audio);
            }
            $extension = $request->file('part_three_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_three_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_three_audio')->storeAs('public/audios', $uniqueFilename);
        }else{
            $uniqueFilename=$listening->part_three->audio;
        }

        $listening->part_three->update([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_three_advice,
        ]);

        //part 4
        $uniqueFilename='';
        if ($request->hasFile('part_four_audio')) {
            if ($listening->part_four->audio) {
                Storage::delete('public/audios/' . $listening->part_four->audio);
            }
            $extension = $request->file('part_four_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_four_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_four_audio')->storeAs('public/audios', $uniqueFilename);
        }else{
            $uniqueFilename=$listening->part_four->audio;
        }

        $listening->part_four->update([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_four_advice,
        ]);

        //part 5
        $uniqueFilename='';
        if ($request->hasFile('part_five_audio')) {
            if ($listening->part_five->audio) {
                Storage::delete('public/audios/' . $listening->part_five->audio);
            }
            $extension = $request->file('part_five_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_five_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_five_audio')->storeAs('public/audios', $uniqueFilename);
        }else{
            $uniqueFilename=$listening->part_five->audio;
        }

        $listening->part_five->update([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_five_advice,
        ]);

        //part 6
        $uniqueFilename='';
        if ($request->hasFile('part_six_audio')) {
            if ($listening->part_six->audio) {
                Storage::delete('public/audios/' . $listening->part_six->audio);
            }
            $extension = $request->file('part_six_audio')->getClientOriginalExtension();
            $uniqueFilename = 'part_six_audio_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('part_six_audio')->storeAs('public/audios', $uniqueFilename);
        }else{
            $uniqueFilename=$listening->part_six->audio;
        }

        $listening->part_six->update([
            'audio'=>$uniqueFilename,
            'advice'=>$request->part_six_advice,
        ]);



        return redirect()->route('admin.full_exercise.more.index',['id'=>$full_exercise->id] );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $listening=ListeningExercise::with('listening_part_one_fife_question.tests','listening_part_two_six_question.listening_part_two_six_tests')->find($id);

        if ($listening->part_category=='1' || $listening->part_category=='5') {

            if ($listening->listening_part_one_fife_question->audio) {
                Storage::delete('public/audios/' . $listening->listening_part_one_fife_question ->audio);
            }
            $listening->listening_part_one_fife_question->tests()->delete();

        }elseif ($listening->part_category=='2' || $listening->part_category=='6'){
          if ($listening->listening_part_two_six_question){
            if ($listening->listening_part_two_six_question->audio) {
                Storage::delete('public/audios/' . $listening->listening_part_two_six_question ->audio);
            }
            $listening->listening_part_two_six_question->listening_part_two_six_tests()->delete();
          }

        }elseif ($listening->part_category=='3' || $listening->part_category=='4'){
          if ($listening->listening_part_three_four){
            if ($listening->listening_part_three_four->audio) {
                Storage::delete('public/audios/' . $listening->listening_part_three_four ->audio);
            }
            $listening->listening_part_three_four->part_three_four_questions()->delete();
            $listening->listening_part_three_four->part_three_four_answers()->delete();
          }

        }

        $listening->listening_part_one_fife_question()->delete();
        $listening->delete();

       return back();
    }
}
