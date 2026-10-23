<?php

namespace App\Http\Controllers;

use App\Models\ListeningPartThreeFour;
use App\Models\SpeakingExercise;
use App\Models\SpeakingPartOneSecondImages;
use App\Models\SpeakingPartOneSecondQuestions;
use App\Models\SpeakingPartTwo;
use App\Models\SpeakingPartTwoImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpeakingPartOneSecondImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $speaking_exercise = SpeakingExercise::with('part_one_second')->find($id);


        return view('admin.speaking.part_one.create_image',compact('speaking_exercise')  );
    }
    public function create2($id)
    {
        $speaking_exercise = SpeakingExercise::with('part_two')->find($id);


        return view('admin.speaking.part_two.create_image',compact('speaking_exercise')  );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg', // Add validation for the image
        ]);

        $uniqueFilename = '';

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $uniqueFilename = 'image_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('image')->storeAs('public/speaking_images', $uniqueFilename);


            $image =  SpeakingPartOneSecondImages::create([
                'image'=>$uniqueFilename,
                'part_one_second_id'=>$request->question_id,
            ]);

        }
        return redirect()->route('admin.speaking.part1.index',['id'=>$request->speaking_exercise_id]);

    }
    public function store2(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg', // Add validation for the image
        ]);

        $uniqueFilename = '';

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $uniqueFilename = 'image_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('image')->storeAs('public/speaking_images', $uniqueFilename);


            $image =  SpeakingPartTwoImage::create([
                'image'=>$uniqueFilename,
                'part_two_id'=>$request->question_id,
            ]);

        }
        return redirect()->route('admin.speaking.part2.index',['id'=>$request->speaking_exercise_id]);

    }

    /**
     * Display the specified resource.
     */
    public function show(SpeakingPartOneSecondImages $speakingPartOneSecondImages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id,$speaking_exercise_id)
    {
        $speaking_exercise = SpeakingExercise::with('part_one_second')->find($speaking_exercise_id);
        $image=SpeakingPartOneSecondImages::find($id);

        return view('admin.speaking.part_one.edit_image',compact('speaking_exercise','image')  );
    }
    public function edit2($id,$speaking_exercise_id)
    {
        $speaking_exercise = SpeakingExercise::with('part_two')->find($speaking_exercise_id);
        $image=SpeakingPartTwoImage::find($id);

        return view('admin.speaking.part_two.edit_image',compact('speaking_exercise','image')  );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg', // Add validation for the image
        ]);

        $image=SpeakingPartOneSecondImages::find($id);

        if (!$image) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        // Check if the old image exists and delete it
        if ($image->image && Storage::exists('public/speaking_images/' . $image->image)) {
            Storage::delete('public/speaking_images/' . $image->image);
        }

        $uniqueFilename = '';

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $uniqueFilename = 'image_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('image')->storeAs('public/speaking_images', $uniqueFilename);

            // Save the new filename in the database
            $image->image = $uniqueFilename;
            $image->save();
        }
        return redirect()->route('admin.speaking.part1.index',['id'=>$request->speaking_exercise_id]);

    }
    public function update2 (Request $request, $id)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg', // Add validation for the image
        ]);

        $image=SpeakingPartTwoImage::find($id);

        if (!$image) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        // Check if the old image exists and delete it
        if ($image->image && Storage::exists('public/speaking_images/' . $image->image)) {
            Storage::delete('public/speaking_images/' . $image->image);
        }

        $uniqueFilename = '';

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $uniqueFilename = 'image_' . time() . '_' . uniqid() . '.' . $extension;
            $request->file('image')->storeAs('public/speaking_images', $uniqueFilename);

            // Save the new filename in the database
            $image->image = $uniqueFilename;
            $image->save();
        }
        return redirect()->route('admin.speaking.part2.index',['id'=>$request->speaking_exercise_id]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $image=SpeakingPartOneSecondImages::find($id);
        if ($image){
            if ($image->image && Storage::exists('public/speaking_images/' . $image->image)) {
                Storage::delete('public/speaking_images/' . $image->image);
            }
            $image->delete();
        }
        return redirect()->back();
    }
    public function destroy1($id)
    {
        $image=SpeakingPartTwoImage::find($id);
        if ($image){
            if ($image->image && Storage::exists('public/speaking_images/' . $image->image)) {
                Storage::delete('public/speaking_images/' . $image->image);
            }
            $image->delete();
        }
        return redirect()->back();
    }
}
