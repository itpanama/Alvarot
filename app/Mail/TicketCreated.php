<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TicketCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "An order has been created, " . $this->ticket['type_service_description'] . " MSC PANAMA";

        $view = $this->view('emails.ticket-created')->subject($subject);

        if (count($this->ticket['attachments'])) {
            foreach ($this->ticket['attachments'] as $attachment) {
                $view->attach($attachment['path'], [
                    'as' => $attachment['name'],
                    'mime' => $attachment['mime']
                ]);
            }
        }

        return $view;
    }
}
