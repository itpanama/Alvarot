<?php

namespace App\Jobs;

use App\Mail\TicketAssigned;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class ProcessSendEmailTicketAssigned implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // numero de intentos antes de determinar que el job fallo.
    public $tries = 1;

    protected $ticket;

    protected $mailData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ticket, $mailData)
    {
        $this->ticket = $ticket;
        $this->mailData = $mailData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $emailBody = new TicketAssigned($this->ticket, $this->mailData);
        if (isset($this->mailData->cc)) {

            Mail::to($this->mailData->to)->cc($this->mailData->cc)->send($emailBody);

        } else {

            Mail::to($this->mailData->to)->send($emailBody);

        }
    }
}