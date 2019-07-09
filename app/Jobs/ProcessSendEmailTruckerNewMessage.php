<?php

namespace App\Jobs;

use App\Mail\TruckerNewMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class ProcessSendEmailTruckerNewMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // numero de intentos antes de determinar que el job fallo.
    public $tries = 1;

    protected $trucker;

    protected $mailData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($trucker, $mailData)
    {
        $this->trucker = $trucker;
        $this->mailData = $mailData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $emailBody = new TruckerNewMessage($this->trucker, $this->mailData);
        if (isset($this->mailData->cc)) {

            Mail::to($this->mailData->to)->cc($this->mailData->cc)->send($emailBody);

        } else {

            Mail::to($this->mailData->to)->send($emailBody);

        }
    }
}
