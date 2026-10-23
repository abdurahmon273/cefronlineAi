<?php

namespace App\Http\Controllers;

use App\Models\PrepareText;
use App\Models\SpeakingPrompt;
use App\Models\WritingPrompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class WritingPromptController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('information_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $writingPrompts = WritingPrompt::all();
        $speakingPrompts = SpeakingPrompt::all();

        $types=WritingPrompt::TYPES;
        $speakingTypes=SpeakingPrompt::TYPES;

        return view('admin.writingPrompt.index', compact('writingPrompts','types','speakingPrompts','speakingTypes'));
    }

    public function edit($id)
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $writingPrompt=WritingPrompt::find($id);
        $types=WritingPrompt::TYPES;

        return view('admin.writingPrompt.edit', compact('writingPrompt','types'));
    }
    public function speaking_edit($id)
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $speakingPrompt=SpeakingPrompt::find($id);
        $types=SpeakingPrompt::TYPES;

        return view('admin.writingPrompt.speaking_edit', compact('speakingPrompt','types'));
    }

    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate($request,[
            'input_prompt_text'=>'required|min:3',
            'output_prompt_text'=>'required|min:3',
        ]);

        $writingPrompt=WritingPrompt::find($id);
        $writingPrompt->update($request->all());

        return redirect()->route('admin.writingPrompt.index');
    }
    public function speaking_update(Request $request, $id)
    {
        abort_if(Gate::denies('information_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate($request,[
            'input_prompt_text'=>'required|min:3',
            'output_prompt_text'=>'required|min:3',
        ]);

        $speakingPrompt=SpeakingPrompt::find($id);
        $speakingPrompt->update($request->all());

        return redirect()->route('admin.writingPrompt.index');
    }
}
