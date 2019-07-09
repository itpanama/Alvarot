<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;
use App\TicketAttachment;
use App\Mail\TicketCreated;
use App\Jobs\ProcessSendEmail;

class EmailTestController extends Controller
{
    private function handlerEmail($ticketID, $sendEmail = true)
    {
        $ticket = Ticket::getTicket($ticketID);

        if (!$ticket) {
            return 'invalid ticket';
        }

        $ticket = $ticket->toArray();

        $mailData = new \stdClass();

        $user = Auth::user();
        $mailData->from = ['address' => $user->email, 'name' => $user->name];

        $mailData->to = (object)[
            'email' => $ticket['customer_email_1'],
            'name' => $ticket['customer_name']
        ];

        if ($ticket['customer_email_2']) {
            $mailData->cc = (object)[
                'email' => $ticket['customer_email_2']
            ];
        }

        $ticket['attachments'] = [];
        $ticket_attachments = TicketAttachment::where('ticket_id', '=', $ticket['id'])->get();
        $dir = base_path();
        foreach ($ticket_attachments as $attachment) {
            $attachment_path = $dir . '/attachments/tickets/' . $ticket['id'] . '/' . $attachment->attachment_name;
            if (file_exists($attachment_path)) {
                $ticket['attachments'] [] = [
                    'path' => $attachment_path,
                    'name' => $attachment->attachment_name,
                    'mime' => mime_content_type($attachment_path),
                ];
            }
        }

        if ($sendEmail) {
            $job = (new ProcessSendEmail($ticket, $mailData))->onQueue('emails');
            $this->dispatch($job);
            return 'done';
        } else {
            return (new TicketCreated($ticket))->render();
        }
    }

    public function sentEmail(Request $request, $ticketID)
    {
        return $this->handlerEmail($ticketID);
    }

    public function sentHtmlEmail(Request $request, $ticketID)
    {
        return $this->handlerEmail($ticketID, false);
    }
}
