<?php

namespace App\Services;

use App\Enums\CefrTypeEnum;
use App\Jobs\WritingCheck;
use App\Models\FullExercise;
use App\Models\FullResult;
use App\Models\PremiumToUsers;
use App\Models\SpeakingExercise;
use App\Models\WritingExercise;
use App\Models\WritingPrompt;
use App\Traits\ListeningGetDataTrait;
use App\Traits\ReadingGetDataTrait;
use App\Traits\SpeakingGetDataTrait;
use App\Traits\TestStoreHelperTrait;
use App\Traits\WritingGetDataTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExamService
{

    public function getPractiseGetData($exercise_id,$type)
    {
        if ($type == CefrTypeEnum::SPEAKING->value) {
            $data = [];

            $speakingExercise = SpeakingExercise::with([
                'part_one_first.questions',
                'part_one_second.questions',
                'part_one_second.images',
                'part_two.question',
                'part_two.images',
                'part_three'
            ])->find($exercise_id);

            if ($speakingExercise->part_category==4) { return  [];}

            if ($speakingExercise?->part_one_first && $speakingExercise->part_one_first->questions->count()) {
                foreach ($speakingExercise->part_one_first->questions as $k => $q) {
                    $data[] = [
                        'key'  => 'part_one_first_' . $k,
                        'mode' => 'text',
                        'html' => null,
                        'text' => ($q->question ?? '') . "\n\n" . ($q->modal_answer ?? ''),
                    ];
                }
            }

            if ($speakingExercise?->part_one_second && $speakingExercise->part_one_second->questions->count()) {
                foreach ($speakingExercise->part_one_second->questions as $k => $q) {

                    $imgsHtml = '<div class="mt-4 flex gap-4">';
                    foreach (($speakingExercise->part_one_second->images ?? []) as $img) {

                        $src = method_exists($img, 'getUrlAttribute')
                            ? $img->url
                            : asset('storage/speaking_images/' . $img->image);

                        $imgsHtml .= '<div class="w-1/2">';
                        $imgsHtml .= '<img class="w-full h-[160px] object-cover rounded-xl border border-slate-200" src="'.$src.'" alt="image">';
                        $imgsHtml .= '</div>';
                    }
                    $imgsHtml .= '</div>';

                    $safeQuestion = e($q->question ?? '');
                    $safeAnswer   = nl2br(e($q->modal_answer ?? ''));

                    $html = '<div class="space-y-3">'
                        . '<div class="font-semibold text-slate-900">'.$safeQuestion.'</div>'
                        . $imgsHtml
                        . '<div class="text-slate-700 leading-relaxed">'.$safeAnswer.'</div>'
                        . '</div>';

                    $data[] = [
                        'key'  => 'part_one_second_' . $k,
                        'mode' => 'html',
                        'html' => $html,
                        'text' => null,
                    ];
                }
            }

            if ($speakingExercise?->part_two && $speakingExercise->part_two->question) {

                $partTwo = $speakingExercise->part_two;
                $q = $partTwo->question; // hasOne

                // Question -> UL/LI (• bilan)
                $lines = preg_split("/\r\n|\n|\r/", (string)($q->question ?? ''));
                $items = [];
                foreach ($lines as $line) {
                    $t = trim($line);
                    if ($t === '') continue;
                    $t = trim($t);
                    if ($t !== '') $items[] = $t;
                }

                $questionHtml = '';
                if (count($items) > 0) {
                    $questionHtml = '<ul class="list-disc pl-6 space-y-2 text-slate-800 font-medium">'
                        . implode('', array_map(function ($it) {
                            $clean = preg_replace('/^[•\-\*\x{2022}]+\s*/u', '', $it);
                            return '<li>' . e($clean) . '</li>';
                        }, $items))
                        . '</ul>';
                } else {
                    $questionHtml = '<div class="text-slate-800 font-medium">' . nl2br(e($q->question ?? '')) . '</div>';
                }

                // Images
                $imgs = $partTwo->images ?? [];
                $imgsHtml = '';
                if ($imgs && count($imgs) > 0) {
                    $imgsHtml = '<div class="mt-4 flex gap-4">';
                    foreach ($imgs as $img) {
                        $src = method_exists($img, 'getUrlAttribute')
                            ? $img->url
                            : asset('storage/speaking_images/' . $img->image);

                        $wrapClass = (count($imgs) === 1) ? 'w-full' : 'w-1/2';
                        $imgsHtml .= '<div class="'.$wrapClass.'">';
                        $imgsHtml .= '<img class="min-w-50 h-[180px] object-cover rounded-xl border border-slate-200" src="'.$src.'" alt="image">';
                        $imgsHtml .= '</div>';
                    }
                    $imgsHtml .= '</div>';
                }

                // Modal answer (TO‘G‘RI JOYI: part_two->modal_answer)
                $safeAnswer = nl2br(e($partTwo->modal_answer ?? ''));

                $html = '<div class="space-y-4">'
                    . $questionHtml
                    . $imgsHtml
                    . '<div class="text-slate-700 leading-relaxed">'.$safeAnswer.'</div>'
                    . '</div>';

                $data[] = [
                    'key'  => 'part_two_0',
                    'mode' => 'html',
                    'html' => $html,
                    'text' => null,
                ];
            }

            if ($speakingExercise?->part_three) {

                $p3 = $speakingExercise->part_three;

                $theme = e($p3->theme ?? '');
                $raw   = (string)($p3->question_text ?? '');

                $lines = preg_split("/\r\n|\n|\r/", $raw);

                $forItems = [];
                $againstItems = [];
                $mode = null; // 'for' | 'against'

                foreach ($lines as $line) {
                    $t = trim($line);
                    if ($t === '') continue;

                    $upper = mb_strtoupper($t);

                    if ($upper === 'FOR') { $mode = 'for'; continue; }
                    if ($upper === 'AGAINST') { $mode = 'against'; continue; }

                    $t = preg_replace('/^[•\-\*\x{2022}]+\s*/u', '', $t);
                    $t = trim($t);
                    if ($t === '') continue;

                    if ($mode === 'for') $forItems[] = $t;
                    if ($mode === 'against') $againstItems[] = $t;
                }

                $forHtml = count($forItems)
                    ? '<ul class="list-disc pl-6 space-y-2 text-base text-slate-700">'
                    . implode('', array_map(fn($it) => '<li>' . e($it) . '</li>', $forItems))
                    . '</ul>'
                    : '';

                $againstHtml = count($againstItems)
                    ? '<ul class="list-disc pl-6 space-y-2 text-base text-slate-700">'
                    . implode('', array_map(fn($it) => '<li>' . e($it) . '</li>', $againstItems))
                    . '</ul>'
                    : '';

                $answerHtml = '';
                if (!empty($p3->modal_answer)) {
                    $answerHtml =
                        '<div class="mt-6 bg-slate-50 border border-slate-200 rounded-2xl p-5">'
                        . '<div class="text-sm font-bold text-slate-900 mb-2">Model Answer</div>'
                        . '<div class="text-slate-700 leading-relaxed text-sm">'
                        . nl2br(e($p3->modal_answer))
                        . '</div>'
                        . '</div>';
                }

                $html =
                    '<div class="w-full">'
                    .   '<div class="text-center text-lg font-semibold text-slate-900">'
                    .     $theme
                    .   '</div>'
                    .   '<div class="mt-5 border-t border-slate-200 pt-5">'
                    .     '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">'
                    .       '<div class="md:pr-6 md:border-r md:border-slate-200">'
                    .         '<div class="text-lg font-bold text-slate-900 mb-3">For</div>'
                    .         $forHtml
                    .       '</div>'
                    .       '<div class="md:pl-6">'
                    .         '<div class="text-lg font-bold text-slate-900 mb-3">Against</div>'
                    .         $againstHtml
                    .       '</div>'
                    .     '</div>'
                    .   '</div>'
                    .   $answerHtml
                    . '</div>';

                $data[] = [
                    'key'  => 'part_three_0',
                    'mode' => 'html',
                    'html' => $html,
                    'text' => null,
                ];
            }

            return $data;
        }
        elseif ($type==CefrTypeEnum::WRITING->value){
            $data=[];

            $writingExercise=WritingExercise::with(['task_one','task_two'])->find($exercise_id);

            if ($writingExercise->task_one && $writingExercise->task_one->example_text){

                $data[] =[
                    'key'  => 'task_one',
                    'mode' => 'text',
                    'html' => $writingExercise->task_one->theme,
                    'text' => $writingExercise->task_one->example_text,
                ];
            }

            if ($writingExercise->task_two && $writingExercise->task_two->example_text){
                $data[] =[
                    'key'  => 'task_two',
                    'mode' => 'text',
                    'html' => $writingExercise->task_two->theme.'\n',
                    'text' => $writingExercise->task_two->example_text,
                ];
            }

            return $data;
        }else{
             return [];
        }
    }

    public function getErrorMessageAndRedirectUrl($status,$type,$part=null)
    {
        if ($status){

            $this_status=false;
            $errorMessage='';

            if (auth()->check()){
                $user = auth()->user();
                $today = now();

                $existingPlan = PremiumToUsers::where('user_id', $user->id)
                    ->where(function ($query) use ($today) {
                        $query->where('start_time', '<=', $today)
                            ->where('end_time', '>=', $today);
                    })
                    ->orderByDesc('id')
                    ->first();
                if (!$existingPlan) {
                    $this_status=true;
                    $errorMessage='Please Upgrade your plan!';
                }
            }else{
              $this_status=true;
              $errorMessage='Please Register or Login!';
            }

            return ['status'=>$this_status,'route'=>redirect()->route('user.practice-tests',
                ['type'=>$type,'part'=>$part])->with('error',$errorMessage) ];

        }else{

            return ['status'=>false,'route'=>redirect()->route('user.practice-tests',
                ['type'=>$type,'part'=>$part]) ];

        }
    }
}
