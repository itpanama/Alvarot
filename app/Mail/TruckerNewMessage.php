<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TruckerNewMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $trucker;
    public $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($trucker, $mailData)
    {
        $this->trucker = $trucker;
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Nuevo Mensaje de MSC Panama";

        $view = $this->view('emails.trucker-new-message')->subject($subject);

        if (count($this->trucker['attachments'])) {
            foreach ($this->trucker['attachments'] as $attachment) {
                $view->attach($attachment['path'], [
                    'as' => $attachment['name'],
                    'mime' => $attachment['mime']
                ]);
            }
        }

        return $view;
    }
}
