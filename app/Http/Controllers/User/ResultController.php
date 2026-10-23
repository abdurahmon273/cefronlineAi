<?php

namespace App\Http\Controllers\User;

use App\Enums\CefrTypeEnum;
use App\Enums\IeltsTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\FullResult;
use App\Services\FullMockService;
use App\Traits\ListeningGetDataTrait;
use App\Traits\ReadingGetDataTrait;
use App\Traits\SpeakingGetDataTrait;
use App\Traits\WritingGetDataTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultController extends Controller
{
    use ListeningGetDataTrait, WritingGetDataTrait, ReadingGetDataTrait,SpeakingGetDataTrait;


    public function __construct(
        protected FullMockService $fullMockService,
    ){}
    public function index()
    {
        return view('user.results.results');
    }
    public function resultsShow($id,$type)
    {

            switch ($type){
            case CefrTypeEnum::LISTENING->value:
                $data=$this->GetListeningResultData($id);

                return view('user.results.show-listening', compact('data'));
            case CefrTypeEnum::READING->value:
                $data=$this->GetReadingResultData($id);

                return view('user.results.show-reading', compact('data'));
            case CefrTypeEnum::WRITING->value:
                $data=$this->GetWritingResultData($id);

                return view('user.results.show-writing', compact('data'));
            case CefrTypeEnum::SPEAKING->value:
                $data=$this->GetSpeakingResultData($id);

                return view('user.results.show-speaking', compact('data'));
            default:
                abort(404);
        }
    }
    public function fullResultsShow($id,$type=0)
    {
        $data=$this->fullMockService->getFullMockResultData($id,$type);


        return view('user.results.show-full-mock',$data);
    }



    public function ReadingResultDetail($id)
    {
        $data=$this->GetReadingResultData($id);

        return view('user.reading.reading_result',$data);
    }
    public function ListeningResultDetail($id)
    {
        $data=$this->GetListeningResultData($id);

        return view('user.listening.listening_result',$data);
    }

    public function DownloadResultPdf($id,$type)
    {
        if ($type==CefrTypeEnum::WRITING->value){
            $data = $this->GetWritingResultData($id);
            $pdfName= $data['writingResult']['exercise']['name'];
            // return view('user.pdf.results.writing-pdf', $data);
            $pdf = Pdf::loadView('user.pdf.results.writing-pdf', $data);

            return $pdf->download('writing_result_' . $pdfName . '.pdf');
        }elseif ($type==CefrTypeEnum::SPEAKING->value){
            $data = $this->GetSpeakingResultData($id);
            $pdfName= $data['speakingResult']->exercise->title;
            // return view('user.pdf.results.writing-pdf', $data);
            $pdf = Pdf::loadView('user.pdf.results.speaking-pdf', $data);

            return $pdf->download('speaking_result_' . $pdfName . '.pdf');
        }

    }

    public function ApiDownloadResultPdf($id,$type,$token)
    {
        if ($token=='wgp8kbrymkuaZEwNcAaMfQbTHFQmzKB6W57OecGW'){

        if ($type==CefrTypeEnum::WRITING->value){
            $data = $this->GetWritingResultData($id);
            $pdfName= $data['writingResult']['exercise']['name'];
            // return view('user.pdf.results.writing-pdf', $data);
            $pdf = Pdf::loadView('user.pdf.results.writing-pdf', $data);

            return $pdf->download('writing_result_' . $pdfName . '.pdf');
        }elseif ($type==CefrTypeEnum::SPEAKING->value){
            $data = $this->GetSpeakingResultData($id);
            $pdfName= $data['speakingResult']->exercise->title;
            // return view('user.pdf.results.writing-pdf', $data);
            $pdf = Pdf::loadView('user.pdf.results.speaking-pdf', $data);

            return $pdf->download('speaking_result_' . $pdfName . '.pdf');
        }

        }else{
            return abort(404);
        }

    }

}
