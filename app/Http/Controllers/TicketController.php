<?php

namespace App\Http\Controllers;

use App\Exports\TicketsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\TicketMessageAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Ticket;
use App\User;
use App\Customer;
use App\TicketAttachment;
use App\Trucker;
use File;
use DB;
use Auth;
use App\Jobs\ProcessSendEmail;
use App\Jobs\ProcessSendEmailTicketAssigned;
use App\Jobs\ProcessSendEmailUpdateTicket;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        try {
            $limitPages = $request->input('limitPages', config('app.pagination'));

            $query = $this->_buildQuery($request);

            $data = $query->paginate($limitPages);

            return response()->json($data, 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function indexTruckerToTickets(Request $request)
    {
        try {
            $user = Auth::user();

            $limitPages = $request->input('limitPages', config('app.pagination'));

            $query = $this->_buildQuery($request);

            $trucker = Trucker::where('user_id', '=', $user->id)->first();

            if ($trucker) {
                $query->where('tickets.trucker_id', '=', $trucker->id);
            } else {
                $query->where('tickets.trucker_id', '=', -1);
            }

            $data = $query->paginate($limitPages);

            return response()->json($data, 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function downloadAttachment(Request $request, $ticket_id, $attachment_id)
    {
        try {
            $ticket = Ticket::find($ticket_id);
            if (!$ticket) {
                throw new \Exception('Ticket not found.');
            }

            $ticket_attachment = TicketAttachment::find($attachment_id);
            if (!$ticket_attachment) {
                throw new \Exception('Attachment not found.');
            }

            $attachment_path = base_path() . "/attachments/tickets/" . $ticket->id . "/" . $ticket_attachment->attachment_name;

            if (!File::exists($attachment_path)) {
                return response()->json(['recurso no encontrado.'], 404);
            }

            return response()->download($attachment_path);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function downloadMessageAttachment(Request $request, $ticket_id, $attachment_id)
    {
        try {
            $ticket = Ticket::find($ticket_id);
            if (!$ticket) {
                throw new \Exception('Ticket not found.');
            }

            $ticket_attachment = TicketMessageAttachment::find($attachment_id);
            if (!$ticket_attachment) {
                throw new \Exception('Attachment not found.');
            }

            $attachment_path = base_path() . "/attachments/tickets/" . $ticket->id . "/messages/" . $ticket_attachment->attachment_name;

            if (!File::exists($attachment_path)) {
                return response()->json(['recurso no encontrado.'], 404);
            }

            return response()->download($attachment_path);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $ticket = Ticket::getTicketFull($id);
            if (!$ticket) {
                throw new \Exception('Bad request');
            }

            $user = Auth::user();

            if (
                config('app.role.admin') ||
                config('app.role.employee') ||
                config('app.role.customer') && $ticket->user_id == $user->id && $user->role_id
            ) {
                return response()->json(compact('ticket'), 200);
            } else {
                return response()->json(['error' => 'Not authorized'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteTicket(Request $request, $id)
    {
        try {
            $ticket = Ticket::find($id);
            if (!$ticket) {
                throw new \Exception('Bad request');
            }

            $ticket->delete();

            return response()->json(true, 200);

        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        }
    }

    public function changeAssignationToUser(Request $request)
    {
        try {

            $rules = [
                'ticket_id' => 'required'
            ];

            if ($request->user_id_assigned) {
                $user = User::find($request->user_id_assigned);
                if (!$user) {
                    throw new \Exception('Used not found');
                }
            }

            $data = $request->only([
                'ticket_id'
            ]);


            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages()], 400);
            }

            $ticket = Ticket::find($request->ticket_id);
            if (!$ticket) {
                throw new \Exception('Bad request');
            }

            $assigned = false;
            if ($request->user_id_assigned) {
                $ticket->user_id_assigned = $request->user_id_assigned;
                $ticket->user_assigned_date = date('Y-m-d H:i:s');
                $assigned = true;
            } else {
                $ticket->user_id_assigned = null;
                $ticket->user_assigned_date = null;
            }

            $ticket->save();

            if ($assigned) {
                $this->sendMailTicketAssigned($ticket->id);
            }

            return response()->json(true, 200);

        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        }
    }

    public function changeToCompleted(Request $request)
    {
        try {

            $rules = [
                'ticket_id' => 'required'
            ];

            if ($request->user_id_assigned) {
                $user = User::find($request->user_id_assigned);
                if (!$user) {
                    throw new \Exception('Used not found');
                }
            }

            $data = $request->only([
                'ticket_id'
            ]);

            $user = Auth::user();

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages()], 400);
            }

            $ticket = Ticket::find($request->ticket_id);
            if (!$ticket) {
                throw new \Exception('Bad request');
            }

            if ($request->completed) {
                $ticket->completed = 1;
                $ticket->completed_date = date('Y-m-d H:i:s');
            } else {
                $ticket->completed = 0;
                $ticket->completed_date = null;
            }

            if (!$ticket->user_id_assigned && $request->completed) {
                $ticket->user_id_assigned = $user->id;
                $ticket->user_assigned_date = date('Y-m-d H:i:s');
            }

            $ticket->save();

            if ($request->completed) {
                $this->sendMailTicketUpdated($ticket->id, "The Ticket ID " . $ticket->id . " has been completed.");
            }

            return response()->json(true, 200);

        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        }
    }

    public function new(Request $request)
    {
        $validator = $this->validator($request->only([
            'bl_number',
            'type_port_id',
            'type_service',
            'payment_type_id',
            'comments',
            'trucker_id'
        ]));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $ticket = new Ticket();

            $customer = Customer::getCustomer($user->id);

            $ticket->bl_number = $request->bl_number;
            $ticket->customer_id = $customer->id;
            $ticket->type_service_id = $request->type_service;
            if ($request->payment_type_id) {
                $ticket->payment_type_id = $request->payment_type_id;
            }

            if ($request->type_document_office_id) {
                $ticket->type_document_office_id = $request->type_document_office_id;
            }
            $ticket->comments = $request->comments;
            $ticket->type_port_id = $request->type_port_id;
            $ticket->trucker_id = isset($request->trucker_id) ? $request->trucker_id : null;
            $ticket->save();

            $attachments = $request->input('attachments');
            if (count($attachments)) {
                $base_attachments_path_tmp = base_path() . "/attachments/users/" . $user->id;
                $base_attachment_path = base_path() . "/attachments/tickets/" . $ticket->id;
                if (!File::exists($base_attachment_path)) {
                    File::makeDirectory($base_attachment_path, 0755, true);
                }

                foreach ($attachments as $attachment) {
                    $attachment_model = new TicketAttachment;
                    $attachment_model->ticket_id = $ticket->id;
                    $attachment_model->attachment_name = $attachment['attachment_name'];
                    $attachment_model->attachment_size = $attachment['attachment_size'];

                    $old_path = $base_attachments_path_tmp . "/" . $attachment['attachment_name'];
                    $new_path = $base_attachment_path . "/" . $attachment['attachment_name'];

                    File::move($old_path, $new_path);

                    $attachment_model->save();
                }
            }

            DB::commit();

            $this->sendMailTicketCreated($ticket->id);

            return response()->json($ticket, 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function uploadAttachment(Request $request)
    {
        $file = $request->file('file');

        $user = Auth::user();

        $attachments_path = base_path() . "/attachments/users/" . $user->id;

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

    public function exportExcel(Request $request)
    {
        $query = $this->_buildQuery($request);
        $data = [];
        $data['tickets'] = $query->get();
        $filename = 'ticker_report_' . date('dmYhis') . '.xlsx';

        return Excel::download(new TicketsExport($query), $filename);
    }

    public function showTrucker(Request $request, $id, $trucker_id)
    {
        return $this->_getTrucker($id, $trucker_id);
    }

    private function _getTrucker($id, $trucker_id)
    {
        try {
            $ticket = Ticket::find($id);
            if (!$ticket) {
                throw new \Exception('Bad request');
            }

            if ($ticket->trucker_id != $trucker_id) {
                throw new \Exception('Bad request');
            }

            $trucker = Trucker::getTruckerFull(['truckers.id' => $trucker_id]);
            if (!$trucker) {
                throw new \Exception('Bad request');
            }

            unset($trucker->attacuments);

            $user = Auth::user();

            if (
                config('app.role.admin') ||
                config('app.role.employee') ||
                config('app.role.customer') && $ticket->user_id == $user->id && $user->role_id
            ) {
                return response()->json(compact('trucker'), 200);
            } else {
                return response()->json(['error' => 'Not authorized'], 400);
            }

            return response()->json(compact('trucker'), 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function _buildQuery(Request $request)
    {
        ini_set('memory_limit', -1);

        $ticket_id = $request->input('ticket_id');
        $bl_number = $request->input('bl_number');
        $customer_name = $request->input('customer_name');
        $payment_type_id = $request->input('payment_type_id');
        $type_service_id = $request->input('type_service_id');
        $user_id_assigned = $request->input('user_id_assigned');
        $completed = $request->input('completed');
        $created_at_start = $request->input('created_at_start');
        $created_at_end = $request->input('created_at_end');
        $tickets_unassigned = $request->input('tickets_unassigned');
        $comments = $request->input('comments');
        $type_port_id = $request->input('type_port_id');
        $type_document_office_id = $request->input('type_document_office_id');

        $user = Auth::user();

        $query = Ticket::buildQuery();

        if ($user->role_id == config('app.role.customer')) {
            $query->where('customers.user_id', '=', $user->id);
        }

        if ($customer_name) {
            $query->whereRaw("UPPER(u.name) like UPPER(\"%$customer_name%\")");
        }

        if ($payment_type_id) {
            $query->where('tickets.payment_type_id', '=', $payment_type_id);
        }

        if ($type_service_id) {
            $query->where('tickets.type_service_id', '=', $type_service_id);
        }

        if ($type_port_id) {
            $query->where('tickets.type_port_id', '=', $type_port_id);
        }

        if ($type_document_office_id) {
            $query->where('tickets.type_document_office_id', '=', $type_document_office_id);
        }

        if ($ticket_id) {
            $query->where('tickets.id', '=', $ticket_id);
        }

        if ($bl_number) {
            $query->whereRaw("UPPER(tickets.bl_number) like UPPER(\"%$bl_number%\")");
        }

        if ($comments) {
            $query->whereRaw("UPPER(tickets.comments) like UPPER(\"%$comments%\")");
        }

        if ($completed == '1' || $completed == '0') {
            $query->where('tickets.completed', '=', $completed);
        }

        if ($tickets_unassigned == 1) {
            $query->whereNull('tickets.user_id_assigned');
        } else if ($user_id_assigned) {
            $query->where('tickets.user_id_assigned', '=', $user_id_assigned);
        }

        if ($created_at_start && $created_at_end) {
            $query->whereRaw('date_format(tickets.created_at, "%Y-%m-%d") >= ? and date_format(tickets.created_at, "%Y-%m-%d") <= ?', [$created_at_start, $created_at_end]);
        }

        return $query;
    }

    private function validator(array $data)
    {
        $rules = [
            'bl_number' => 'required',
            'type_port_id' => 'required|integer',
            'type_service' => 'required|integer',
            'payment_type_id' => 'required|integer',
            'comments' => 'required|string'
        ];

        if (
            $data['type_service'] === config('app.type_service.release') ||
            $data['type_service'] === config('app.type_service.empty_release')
        ) {
//            $rules['trucker_id'] = 'integer';
        }

        return Validator::make($data, $rules);
    }

    private function sendMailTicketCreated($id)
    {
        $ticket = Ticket::getTicket($id);

        if (!$ticket) {
            return 'invalid ticket';
        }

        $ticket = $ticket->toArray();

        $mailData = new \stdClass();

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

        $job = (new ProcessSendEmail($ticket, $mailData))->onQueue('emails');
        $this->dispatch($job);
    }

    private function sendMailTicketAssigned($id)
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

        $job = (new ProcessSendEmailTicketAssigned($ticket, $mailData))->onQueue('emails');
        $this->dispatch($job);
    }

    private function sendMailTicketUpdated($id, $comments = "")
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

        if ($ticket['customer_email_2']) {
            $mailData->cc = (object)[
                'email' => $ticket['customer_email_2']
            ];
        }

        $ticket['comments'] = $comments;

        $ticket['attachments'] = [];

        $job = (new ProcessSendEmailUpdateTicket($ticket, $mailData))->onQueue('emails');
        $this->dispatch($job);
    }
}
