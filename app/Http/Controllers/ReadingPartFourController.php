<?php

namespace App\Http\Controllers;

use App\Models\ListeningPartOneFifeQuestion;
use App\Models\ListeningPartOneFifeTest;
use App\Models\ListeningPartTwoSixQuestion;
use App\Models\ReadingExercise;
use App\Models\ReadingPartFour;
use Illuminate\Http\Request;

class ReadingPartFourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function index1($id)
    {
        $reading_exercise=ReadingExercise::with('part_four.tests')->find($id);
        $tfn_tests=$reading_exercise->part_four->tests->where('type','1');
        $mchq_tests=$reading_exercise->part_four->tests->where('type','2');
        $part_four=$reading_exercise->part_four;
        return view('admin.reading.part_four.index' ,compact('reading_exercise','part_four','mchq_tests','tfn_tests'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(ReadingPartFour $readingPartFour)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $part_four=ReadingPartFour::with('reading_exercise')->find($id);
        return view('admin.reading.part_four.edit_text',compact('part_four'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'text'=>'required'
        ]);
        $reading=ReadingPartFour::find($id);
        $reading->text=$request->text;
        $reading->save();
        return redirect()->route('admin.reading.part4.index',['id'=>$request->reading_id]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReadingPartFour $readingPartFour)
    {
        //
    }
}
