<?php

namespace App\Http\Controllers\User;

use App\Enums\CefrTypeEnum;
use App\Http\Controllers\Controller;
use App\Jobs\SpeakingChangeAudioFormat;
use App\Jobs\WritingCheck;
use App\Models\Exercise;
use App\Models\FullResult;
use App\Models\ListeningExercise;
use App\Models\ListeningResult;
use App\Models\ReadingExercise;
use App\Models\ReadingResult;
use App\Models\SpeakingExercise;
use App\Models\SpeakingPartOneFirstAnswer;
use App\Models\SpeakingPartOneFirstResult;
use App\Models\SpeakingPartOneSecondAnswer;
use App\Models\SpeakingPartOneSecondResult;
use App\Models\SpeakingPartThreeResult;
use App\Models\SpeakingPartTwoResult;
use App\Models\SpeakingResult;
use App\Models\WritingExercise;
use App\Models\WritingPartOneAnswer;
use App\Models\WritingPartTwoAnswer;
use App\Models\WritingResult;
use App\Services\ExamService;
use App\Services\FullMockService;
use App\Services\IeltsService;
use App\Services\ResultService;
use App\Services\SpeakingStoreService;
use App\Services\WritingAiService;
use App\Services\WritingStoreService;
use App\Traits\BillingTrait;
use App\Traits\ListeningGetDataTrait;
use App\Traits\PractiseHelperTrait;
use App\Traits\PrepareTextTrait;
use App\Traits\ReadingGetDataTrait;
use App\Traits\SpeakingGetDataTrait;
use App\Traits\TestDataTrait;
use App\Traits\TestStoreHelperTrait;
use App\Traits\WritingGetDataTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
class TestController extends Controller
{
    use ReadingGetDataTrait,TestStoreHelperTrait,PractiseHelperTrait,ListeningGetDataTrait,WritingGetDataTrait,SpeakingGetDataTrait,BillingTrait;


    public function __construct(
        protected FullMockService $fullMockService,
        protected ExamService $examService,
    ){}
    public function practices()
    {
        $data=$this->getPractisesData();

        return view('user.practices.index',$data);
    }

    public function practiceTests($type=0, Request $request)
    {

        if(($type == CefrTypeEnum::WRITING->value || $type == CefrTypeEnum::SPEAKING->value) && !$request->has('part')) {
            $parts = CefrTypeEnum::SPEAKING_CATEGORIES[$type];
            return view('user.practices.part', compact('parts', 'type'));
        }

        $relation = CefrTypeEnum::CEFR_EXERCISES[$type];

        $exercises = Exercise::with([
            $relation => function ($q) {
                $q->orderBy('order_id');
            }
        ])
            ->where('category_type', $type)
            ->when(($request->has('part') && ($type==CefrTypeEnum::WRITING->value || $type==CefrTypeEnum::SPEAKING->value) ), function($q) use ($request) {
                $q->where('part_type', (int) $request->part);
            })
            ->orderBy('order_id')
            ->paginate(5);


        return view('user.practices.tests', compact('exercises', 'type'));
    }

    public function nextExercise($type,$id)
    {
       if ($type==CefrTypeEnum::LISTENING->value){
           $listeningExercise=ListeningExercise::with('exercise')->find($id);

           if (!$listeningExercise->order_id){
               return redirect()->route('user.practice-tests',['type'=>CefrTypeEnum::LISTENING->value,'part'=>$listeningExercise->part_category]);
           }
           $next_listening_Exercise= $listeningExercise->order_id ? ListeningExercise::where('exercise_id',$listeningExercise->exercise_id)->where('part_category',$listeningExercise->part_category)->where('order_id','>',$listeningExercise->order_id)->first() : null;

           if ($next_listening_Exercise){

               return redirect()->route('user.practice-test', ['id' => $next_listening_Exercise->id, 'type' => $type,'difficulty'=>$next_listening_Exercise->difficulty ?? 0 ,'time_limit'=>$next_listening_Exercise->time_limit?? 0 ,'part_category'=>$next_listening_Exercise->part_category??0 , 'title'=>$next_listening_Exercise->name,'status'=>$next_listening_Exercise->status]);
           }else{

               return redirect()->route('user.practice-tests',['type'=>CefrTypeEnum::LISTENING->value,'part'=>$listeningExercise->part_category]);
           }
       }elseif ($type==CefrTypeEnum::READING->value){
           $readingExercise=ReadingExercise::with('exercise')->find($id);

           if (!$readingExercise->order_id){
               return redirect()->route('user.practice-tests',['type'=>$type,'part'=>$readingExercise->part_category]);
           }

           $next_reading_Exercise=$readingExercise->order_id ? ReadingExercise::where('exercise_id',$readingExercise->exercise_id)->where('part_category',$readingExercise->part_category)->where('order_id','>',$readingExercise->order_id)->first() : null;

           if ($next_reading_Exercise){

               return redirect()->route('user.practice-test', ['id' => $next_reading_Exercise->id, 'type' => $type,'difficulty'=>$next_reading_Exercise->difficulty ?? 0 ,'time_limit'=>$next_reading_Exercise->time_limit?? 0 ,'part_category'=>$next_reading_Exercise->part_category??0 , 'title'=>$next_reading_Exercise->name,'status'=>$next_reading_Exercise->status]);
           }else{

               return redirect()->route('user.practice-tests',['type'=>$type,'part'=>$readingExercise->part_category]);
           }
       }elseif ($type==CefrTypeEnum::WRITING->value){
           $writingExercise=WritingExercise::with('exercise')->find($id);
           $next_writing_Exercise=$writingExercise->order_id ? WritingExercise::where('exercise_id',$writingExercise->exercise_id)->where('part_category',$writingExercise->part_category)->where('order_id','>',$writingExercise->order_id)->first() : null;

           if ($next_writing_Exercise){

               return redirect()->route('user.practice-test', ['id' => $next_writing_Exercise->id, 'type' => $type,'difficulty'=>$next_writing_Exercise->difficulty ?? 0 ,'time_limit'=>$next_writing_Exercise->time_limit?? 0 ,'part_category'=>$next_writing_Exercise->part_category??0 , 'title'=>$next_writing_Exercise->title,'status'=>$next_writing_Exercise->status]);
           }else{

               return redirect()->route('user.practice-tests',['type'=>$type,'part'=>$writingExercise->part_category]);
           }
       }elseif ($type==CefrTypeEnum::SPEAKING->value){
           $speakingExercise=SpeakingExercise::with('exercise')->find($id);
           $next_speaking_Exercise=$speakingExercise->order_id ? SpeakingExercise::where('exercise_id',$speakingExercise->exercise_id)->where('part_category',$speakingExercise->part_category)->where('order_id','>',$speakingExercise->order_id)->first() : null;

           if ($next_speaking_Exercise){

               return redirect()->route('user.practice-test', ['id' => $next_speaking_Exercise->id, 'type' => $type,'difficulty'=>$next_speaking_Exercise->difficulty ?? 0 ,'time_limit'=>$next_speaking_Exercise->time_limit?? 0 ,'part_category'=>$next_speaking_Exercise->part_category??0 , 'title'=>$next_speaking_Exercise->title,'status'=>$next_speaking_Exercise->status]);
           }else{

               return redirect()->route('user.practice-tests',['type'=>CefrTypeEnum::SPEAKING->value,'part'=>$speakingExercise->part_category]);
           }
       }
    }

    public function practiceTest($id,$type,$difficulty,$time_limit,$part_category ,$title = null,$status = 0)
    {
        if ($type==CefrTypeEnum::FULL_MOCK->value){

            return redirect()->route('user.mock-practice',$id);

        }else{
            $data=$this->examService->getPractiseGetData($id,$type);

            return view('user.practices.test',compact('id','type','difficulty','time_limit','part_category','title','data','status'));
        }
    }
    public function practiceMock($id)
    {
      $data=$this->fullMockService->firstFullMockData($id);

//      dd($data);
      return view('user.practices.fullTest', $data);
    }


    public function listening($id,Request $request){
        $data=$this->GetListeningData($id,$request->exam_type);

        $access=$this->examService->getErrorMessageAndRedirectUrl($data['status'],CefrTypeEnum::LISTENING->value);

        if ($access['status']){
            return $access['route'];
        }

        return view('user.listening.listening',$data);
    }

    public function reading($id){

        $data=$this->getReadingData($id);

        $access=$this->examService->getErrorMessageAndRedirectUrl($data['status'],CefrTypeEnum::READING->value);

        if ($access['status']){
            return $access['route'];
        }

        return view('user.reading.reading',$data);
    }

    public function writing($id)
    {
        $data=$this->GetWritingData($id);
        $access=$this->examService->getErrorMessageAndRedirectUrl($data['writingExercise']->status,CefrTypeEnum::WRITING->value,$data['part_category_number']);

        if ($access['status']){
            return $access['route'];
        }


        return view('user.writing.writing',$data);
    }

    public function speaking($id){
        $data=$this->GetSpeakingData($id);

        $access=$this->examService->getErrorMessageAndRedirectUrl($data['speakingExercise']->status,CefrTypeEnum::SPEAKING->value,$data['part_category_number']);

        if ($access['status']){
            return $access['route'];
        }

        return view('user.speaking.speaking',$data);
    }

    public function storeListening($id,Request $request)
    {
        $draftData=$this->getDraftData($request);
        $listeningExercise=ListeningExercise::with(['part_one','part_two','part_three.questions','part_four','part_five','part_six','full_exercise'])->find($id);

        $access=$this->examService->getErrorMessageAndRedirectUrl(0,CefrTypeEnum::LISTENING->value);

        if (!auth()->check()){
            return $access['route'];
        }

        [$validatedData, $totalCorrect, $wrongAnswers, $totalQuestions] = $this->processPartsListening($listeningExercise, $draftData);

        $user=auth()->user();
        $result=ListeningResult::create([
            'listening_exercise_id'=>$listeningExercise->id,
            'category_type'=>$listeningExercise->part_category ?? null,
            'all_answers'=>$totalQuestions,
            'correct_answers'=>$totalCorrect,
            'wrong_answers'=>$wrongAnswers,
            'user_id'=>$user->id,
            'percent'=>($totalCorrect/$totalQuestions)*100,
        ]);

        $this->createListeningResultParts($result,$validatedData);

        $isMock=$this->accessMockResult($listeningExercise,$result,$user,CefrTypeEnum::LISTENING->value);

        if ($isMock['success']){

            return redirect()->route('user.mock-practice',$isMock['id']);

        }else{
            return redirect()->route('user.listening.result.detail',$result->id);
        }
    }

    public function storeReading($id,Request $request)
    {
        $draftData=$this->getDraftData($request);
        $readingExercise=ReadingExercise::with(['part_one','part_two','part_three','part_four','part_five'])->find($id);

        $access=$this->examService->getErrorMessageAndRedirectUrl(0,CefrTypeEnum::READING->value);

        if (!auth()->check()){
            return $access['route'];
        }

        [$validatedData, $totalCorrect, $wrongAnswers, $totalQuestions] = $this->processParts($readingExercise, $draftData);


        $user=auth()->user();
        $result=ReadingResult::create([
            'reading_exercise_id'=>$readingExercise->id,
            'category_type'=>$readingExercise->part_category ?? null,
            'all_answers'=>$totalQuestions,
            'correct_answers'=>$totalCorrect,
            'wrong_answers'=>$wrongAnswers,
            'user_id'=>$user->id,
            'percent'=>($totalCorrect/($totalQuestions??1))*100,
//            'reading_draft'=>json_encode($draftData),
        ]);

        $this->createReadingResultParts($result,$validatedData);

        $isMock=$this->accessMockResult($readingExercise,$result,$user,CefrTypeEnum::READING->value);


        if ($isMock['success']){

            return redirect()->route('user.mock-practice',$isMock['id']);

        }else{
            return redirect()->route('user.reading.result.detail',$result->id);
        }
    }

    public function storeWriting($id, Request $request,WritingStoreService $writingStoreService)
    {
        $draftJson = $request->input('writing_draft'); // JSON string
        $withAi    = $request->input('with_ai');       // 0/1

        $draftData = json_decode($draftJson, true);
        $writingExercise=WritingExercise::find($id);
        $access=$this->examService->getErrorMessageAndRedirectUrl(0,CefrTypeEnum::WRITING->value,$writingExercise->part_category);

        if (!auth()->check()){
            return $access['route'];
        }


        try {
            $text_11 = $this->validateWritingText($draftData['parts']['part-1-1'] ?? '', 300, 3000);
            $text_12 = $this->validateWritingText($draftData['parts']['part-1-2'] ?? '', 300, 3000);
            $text_2  = $this->validateWritingText($draftData['parts']['part-2'] ?? '', 350, 3500);

//            $text_11 = $draftData['parts']['part-1-1'] ?? '';
//            $text_12 = $draftData['parts']['part-1-2'] ?? '';
//            $text_2  = $draftData['parts']['part-2'] ?? '';

            $text_1 = trim($text_11) . "\n\n\n" . trim($text_12);  // 2 qator tashlaydi
            $user=auth()->user();

            $creditStatus=$this->getUserCreditStatus($withAi,$user->balance);

            $getAlertData=$this->getAlertData(($withAi ? $creditStatus : 1),'Your Creadit Expired!','Writing Successfully saved.');

            $writing_result=WritingResult::create([
                'category_type'=>$writingExercise->part_category,
                'writing_exercise_id'=>$writingExercise->id,
                'text_1'=>$text_1,
                'text_2'=>$text_2,
                'user_id'=>$user->id,
                'with_ai'=>$withAi ? 1 : 0 ,
                'status'=>$withAi ? 1 : 0 ,
                'max_score'=>CefrTypeEnum::WRITING_MAX_SCORES[$writingExercise->part_category],
            ]);

            if ($writingExercise->part_category==1 || $writingExercise->part_category==3) {
                $writingPartOneAnswer=WritingPartOneAnswer::create([
                    'text_1'=>$text_11,
                    'text_2'=>$text_12,
                    'writing_result_id'=>$writing_result->id,
                    'max_score'=>CefrTypeEnum::WRITING_MAX_SCORES[1],
                ]);
            }

            if ($writingExercise->part_category==2 || $writingExercise->part_category==3) {
                $writingPartTwoAnswer=WritingPartTwoAnswer::create([
                    'text'=>$text_2,
                    'writing_result_id'=>$writing_result->id,
                    'max_score'=> CefrTypeEnum::WRITING_MAX_SCORES[2],
                ]);
            }

            if ($withAi && $creditStatus){
                $writingStoreService->dispatchToJob($writing_result,$writingExercise,$user->id);
            }

            $isMock=$this->accessMockResult($writingExercise,$writing_result,$user,CefrTypeEnum::WRITING->value);

            if ($isMock['success']){

                return redirect()->route('user.mock-practice',$isMock['id']);

            }else{
                return redirect()->route('user.practice-tests', ['type' => CefrTypeEnum::WRITING,'part'=>$writingExercise->part_category])->with($getAlertData['type'],$getAlertData['text']);
            }
        }catch (\Exception $e){

            return redirect()->route('user.practice-tests', ['type' => CefrTypeEnum::WRITING,'part'=>$writingExercise->part_category]);

        }

    }

    public function storeSpeaking(Request $request, $id,SpeakingStoreService $speakingStoreService)
    {
        $withAi = (int) $request->input('with_ai', 0);
        $speaking_exercise=SpeakingExercise::where('id',$id)
            ->with(['part_one_first','part_one_second','part_two','part_three'])->first();
        $part_category=$speaking_exercise->part_category;
        $access=$this->examService->getErrorMessageAndRedirectUrl(0,CefrTypeEnum::SPEAKING->value,$part_category);

        if (!auth()->check()){
            return $access['route'];
        }

        try {
            $recordings = $request->file('recordings');


            $user=auth()->user();
            $creditStatus=$this->getUserCreditStatus($withAi,$user->balance);

            $getAlertData=$this->getAlertData(($withAi ? $creditStatus : 1),'Your Credit Expired!','Speaking Successfully saved.');

            $speakingResult=SpeakingResult::create([
                'category_type'=>$part_category,
                'speaking_exercise_id'=>$speaking_exercise->id,
                'user_id'=>$user->id,
                'with_ai'=>$withAi,
                'status'=>$withAi ? 1 : 0,
                'max_score'=>CefrTypeEnum::SPEAKING_MAX_SCORES[$part_category],
            ]);
            $dir = 'voiceAudios';

            if ($part_category==1 || $part_category==4) {
                $speakingPartOneFirstResult=SpeakingPartOneFirstResult::create([
                    'speaking_result_id'=>$speakingResult->id,
                ]);

                if (isset($recordings['p1_first'])){
                    $rows=[];
                    foreach ($recordings['p1_first'] as $questionId => $file){
                        $ext = $file->getClientOriginalExtension() ?: 'webm';
                        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'webm';

                        $filename = sprintf(
                            'ex_%d_res_part_1first_q_%d_%s.%s',
                            $speakingResult->id,
                            (int)$questionId,
                            Str::random(10),
                            $safeExt
                        );

                        $file->move(storage_path('app/private/' . $dir), $filename);

                        $rows[]=[
                            'one_first_result_id'=>$speakingPartOneFirstResult->id,
                            'question_id'=>$questionId,
                            'audio_path'=>$filename,
                            'created_at'=>now(),
                            'updated_at'=>now(),
                        ];
                    }
                    if ($rows) SpeakingPartOneFirstAnswer::insert($rows);
                }

                $speakingPartOneSecondResult=SpeakingPartOneSecondResult::create([
                    'speaking_result_id'=>$speakingResult->id,
                ]);

                if (isset($recordings['p1_second'])){
                    $rows=[];
                    foreach ($recordings['p1_second'] as $questionId => $file){
                        $ext = $file->getClientOriginalExtension() ?: 'webm';
                        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'webm';

                        $filename = sprintf(
                            'ex_%d_res_part_1_second_q_%d_%s.%s',
                            $speakingResult->id,
                            (int)$questionId,
                            Str::random(10),
                            $safeExt
                        );

                        $file->move(storage_path('app/private/' . $dir), $filename);

                        $rows[]=[
                            'one_second_result_id'=>$speakingPartOneSecondResult->id,
                            'question_id'=>$questionId,
                            'audio_path'=>$filename,
                            'created_at'=>now(),
                            'updated_at'=>now(),
                        ];
                    }
                    if ($rows) SpeakingPartOneSecondAnswer::insert($rows);
                }

            }

            if ($part_category==2 || $part_category==4) {

                if (isset($recordings['p2'])){
                    foreach ($recordings['p2'] as $questionId => $file){
                        $ext = $file->getClientOriginalExtension() ?: 'webm';
                        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'webm';

                        $filename = sprintf(
                            'ex_%d_res_part_2_q_%d_%s.%s',
                            $speakingResult->id,
                            (int)$questionId,
                            Str::random(10),
                            $safeExt
                        );

                        $file->move(storage_path('app/private/' . $dir), $filename);

                        $speakingPartTwoResult=SpeakingPartTwoResult::create([
                            'speaking_result_id'=>$speakingResult->id,
                            'question_id'=>$questionId,
                            'audio_path'=>$filename,
                        ]);
                    }
                }

            }

            if ($part_category==3 || $part_category==4) {

                if (isset($recordings['p3'])){
                    foreach ($recordings['p3'] as $questionId => $file){
                        $ext = $file->getClientOriginalExtension() ?: 'webm';
                        $safeExt = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'webm';

                        $filename = sprintf(
                            'ex_%d_res_part_3_q_%d_%s.%s',
                            $speakingResult->id,
                            (int)$questionId,
                            Str::random(10),
                            $safeExt
                        );

                        $file->move(storage_path('app/private/' . $dir), $filename);

                        $speakingPartThreeResult=SpeakingPartThreeResult::create([
                            'speaking_result_id'=>$speakingResult->id,
                            'part_three_id'=>$questionId,
                            'audio_path'=>$filename,
                        ]);
                    }
                }
            }

            if ($withAi && $creditStatus){
                $speakingStoreService->dispatchToJob($speakingResult,$speaking_exercise,$user->id);
            }else{
                SpeakingChangeAudioFormat::dispatch($speakingResult->id);

            }

            if ($speaking_exercise->full_exercise){
                $isMock=$this->accessMockResult($speaking_exercise,$speakingResult,$user,CefrTypeEnum::SPEAKING->value);

            }else{
                return redirect()->route('user.practice-tests', ['type' => CefrTypeEnum::SPEAKING,'part'=>$speaking_exercise->part_category])->with($getAlertData['type'],$getAlertData['text']);
            }

            if ($isMock['success']){

                return redirect()->route('user.mock-practice',$isMock['id']);

            }else{
                return redirect()->route('user.practice-tests', ['type' => CefrTypeEnum::SPEAKING,'part'=>$speaking_exercise->part_category])->with($getAlertData['type'],$getAlertData['text']);
            }
        }catch (\Exception $e){

            return redirect()->route('user.practice-tests', ['type' => CefrTypeEnum::SPEAKING,'part'=>$speaking_exercise->part_category]);

        }


    }

    public function fullReset($id)
    {
        $user=auth()->user();

        $resultExists=FullResult::where('full_exercise_id',$id)
            ->where('status',0)
            ->where('user_id',$user->id)
            ->orderByDesc('id')
            ->first();
        if ($resultExists){
            $resultExists->status=1;
            $resultExists->save();
        }

        return redirect()->back();
    }
}
