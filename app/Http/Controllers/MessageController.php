<?php

namespace App\Http\Controllers;

use App\TicketMessage;
use App\TicketMessageAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Ticket;
use App\User;
use App\Settings;
use File;
use DB;
use Auth;
use App\Jobs\ProcessSendEmailUpdateTicket;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function deleteMessage(Request $request, $id)
    {
        try {
            $ticketMessage = TicketMessage::find($id);
            if (!$ticketMessage) {
                throw new \Exception('Bad request');
            }

            $ticketMessage->delete();

            return response()->json(true, 200);

        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        }
    }

    public function new(Request $request)
    {
        $validator = $this->validator($request->only([
            'ticket_id',
            'comments'
        ]));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();

            $ticket = Ticket::find($request->ticket_id);
            if (!$ticket) {
                throw new \Exception('Ticket does not exist.');
            }

            $ticket_message = new TicketMessage();

            $ticket_message->user_id = $user->id;
            $ticket_message->ticket_id = $ticket->id;
            $ticket_message->comments = $request->comments;
            $ticket_message->save();

            if ($ticket->completed != $request->completed) {
                if ($request->completed) {
                    $ticket->completed = 1;
                    $ticket->completed_date = date('Y-m-d H:i:s');
                } else {
                    $ticket->completed = 0;
                    $ticket->completed_date = null;
                }
            }

            if (!$ticket->user_id_assigned) {
                $ticket->user_id_assigned = $user->id;
                $ticket->user_assigned_date = date('Y-m-d H:i:s');
            }

            $ticket->save();

            $attachments = $request->input('attachments');
            if (count($attachments)) {
                foreach ($attachments as $attachment) {
                    $attachment_model = new TicketMessageAttachment();
                    $attachment_model->ticket_message_id = $ticket_message->id;
                    $attachment_model->attachment_name = $attachment['attachment_name'];
                    $attachment_model->attachment_size = $attachment['attachment_size'];
                    $attachment_model->save();
                }
            } else {
                $attachments = [];
            }

            DB::commit();

            $this->sendMailTicketUpdated($ticket->id, $request->comments, $attachments);

            unset($ticket);
            $ticket = Ticket::getTicket($request->ticket_id);

            return response()->json(compact('ticket'), 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function newMessageFromTicket(Request $request, $ticket_id = null)
    {
        $data = $request->only([
            'comments'
        ]);

        $rules = [
            'comments' => 'required|string'
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();

            $ticket = Ticket::find($ticket_id);
            if (!$ticket) {
                throw new \Exception('Ticket does not exist.');
            }

            if ($ticket->completed) {
                throw new \Exception('Unable to add a message from a ticket completed.');
            }

            $ticket_message = new TicketMessage();

            $ticket_message->user_id = $user->id;
            $ticket_message->ticket_id = $ticket->id;
            $ticket_message->comments = $request->comments;
            $ticket_message->save();

            $ticket->save();

            $attachments = $request->input('attachments');
            if (count($attachments)) {
                foreach ($attachments as $attachment) {
                    $attachment_model = new TicketMessageAttachment();
                    $attachment_model->ticket_message_id = $ticket_message->id;
                    $attachment_model->attachment_name = $attachment['attachment_name'];
                    $attachment_model->attachment_size = $attachment['attachment_size'];
                    $attachment_model->save();
                }
            } else {
                $attachments = [];
            }

            DB::commit();

            $this->sendMailTicketUpdated($ticket->id, $request->comments, $attachments);

            unset($ticket);
            $ticket = Ticket::getTicketFull($ticket_id);

            return response()->json(compact('ticket'), 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function uploadAttachment(Request $request)
    {
        $file = $request->file('file');

        $ticket_id = $request->input('ticket_id');

        if (!$ticket_id) {
            return response()->json(['error' => 'The ticket id is required.'], 400);
        }

        $attachments_path = base_path() . "/attachments/tickets/" . $ticket_id . "/messages";

        if (!File::exists($attachments_path)) {
            File::makeDirectory($attachments_path, 0755, true);
        }

        $attachment_name = sprintf('%s_%s', date('dmY'), $file->getClientOriginalName());
        $attachment_size = $file->getClientSize();

        if ($attachment_size === 0) {
            return response()->json(['error' => 'The file size cannot be zero.'], 400);
        }

        $request->file('file')->move($attachments_path, $attachment_name);

        return response()->json(['attachment_name' => $attachment_name, 'attachment_size' => $attachment_size], 200);
    }

    private function validator(array $data)
    {
        $rules = [
            'ticket_id' => 'required|integer',
            'comments' => 'required|string'
        ];

        return Validator::make($data, $rules);
    }

    private function sendMailTicketUpdated($id, $comments = "", $attachments_response = [])
    {
        $ticket = Ticket::getTicket($id);

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

        $mailData->cc = [];

        if ($ticket['customer_email_2']) {
            $mailData->cc [] = $ticket['customer_email_2'];
        }

        $settings = Settings::first();

        if ($settings->email_counter_list) {

            $emails = explode("\n", $settings->email_counter_list);

            $mailData->cc = array_merge($mailData->cc, $emails);

        }

        $ticket['comments'] = $comments;

        $ticket['attachments'] = [];
        $dir = base_path() . "/attachments/tickets/" . $id . "/messages";
        foreach ($attachments_response as $attachment) {
            $attachment_path = $dir . '/' . $attachment['attachment_name'];
            if (file_exists($attachment_path)) {
                $ticket['attachments'] [] = [
                    'path' => $attachment_path,
                    'name' => $attachment['attachment_name'],
                    'mime' => mime_content_type($attachment_path),
                ];
            }
        }

        $job = (new ProcessSendEmailUpdateTicket($ticket, $mailData))->onQueue('emails');
        $this->dispatch($job);
    }
}
