<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;



    protected $email;
    protected $message;

    public function __construct($email, $message)
    {
        $this->email = $email;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
//        Mail::raw($this->message, function ($message)  {
//            $message->to($this->email)
//                ->subject('Email tasdiqlash kodi');
//        });

        Mail::html("
    <p>Kodni hech kimga bermang!</p>
    <p>TM school ilovasiga kirish uchun tasdiqlash kodi:</p>
    <h2><b>$this->message</b></h2>
", function ($message)  {
            $message->to($this->email)
                ->subject('Email tasdiqlash kodi');
        });
    }
}
