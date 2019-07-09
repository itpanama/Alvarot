<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Ticket extends Model
{
    protected $table = 'tickets';

    public static function buildQuery()
    {
        return self::select([
            'tickets.id',
            'tickets.bl_number',
            'tickets.user_id_assigned',
            'type_ports.description as port',
            'ua.name as user_assigned',
            'customers.user_id',
            'tickets.trucker_id',
            'truckers.company_name_operation as trucker_name',
            'tickets.completed',
            'tickets.completed_date',
            'tickets.completed',
            'customers.email as customer_email_1',
            'customers.email_optional as customer_email_2',
            'u.name as customer_name',
            'type_services.description as type_service_description',
            'type_document_office.description as type_document_office_description',
            'payment_type.description as payment_type_description',
            'tickets.created_at as ticket_created',
            DB::raw('(DATE_FORMAT(tickets.created_at, \'%Y-%m-%d %l:%i:%s %p\')) as ticket_created_format'),
            DB::raw('(DATE_FORMAT(tickets.completed_date, \'%Y-%m-%d %l:%i:%s %p\')) as completed_date_format'),
            'tickets.comments',
            DB::raw('(SELECT description FROM `status` where TIMEDIFF(CURRENT_TIMESTAMP, tickets.created_at) BETWEEN start and end) as color_description'),
            DB::raw(<<<EOF
case when 
    (SELECT status.color FROM `status` where TIMEDIFF(CURRENT_TIMESTAMP, tickets.created_at) BETWEEN start and end limit 1) is null
then 
    (SELECT es.color FROM status as es where es.start is not null and es.end is null limit 1)
else 
    (SELECT color FROM `status` where TIMEDIFF(CURRENT_TIMESTAMP, tickets.created_at) BETWEEN start and end limit 1)
end as color_hex
EOF
            ),
            DB::raw('TIMESTAMPDIFF(DAY, TIMESTAMP(tickets.created_at), NOW()) AS received_time'),
            DB::raw('TIMESTAMPDIFF(DAY, TIMESTAMP(tickets.created_at), NOW()) AS created_at_days'),
            DB::raw('MOD(TIMESTAMPDIFF(HOUR, TIMESTAMP(tickets.created_at), NOW()), 24) AS created_at_hours'),
            DB::raw('MOD(TIMESTAMPDIFF(MINUTE, TIMESTAMP(tickets.created_at), NOW()), 60) AS created_at_minutes'),
            DB::raw('TIMESTAMPDIFF(MINUTE, tickets.created_at, tickets.completed_date) AS minutes_total'),
        ])
            ->join('customers', 'customers.id', '=', 'tickets.customer_id')
            ->join('users as u', 'u.id', '=', 'customers.user_id')
            ->join('type_services', 'tickets.type_service_id', '=', 'type_services.id')
            ->leftJoin('users as ua', 'ua.id', '=', 'tickets.user_id_assigned')
            ->leftJoin('payment_type', 'tickets.payment_type_id', '=', 'payment_type.id')
            ->leftJoin('type_ports', 'tickets.type_port_id', '=', 'type_ports.id')
            ->leftJoin('type_document_office', 'tickets.type_document_office_id', '=', 'type_document_office.id')
            ->leftJoin('truckers', 'tickets.trucker_id', '=', 'truckers.id')
            ->orderBy('tickets.id', 'asc');
    }

    public static function getTicket($id)
    {
        $query = self::buildQuery();
        $query->where('tickets.id', '=', $id);
        $ticket = $query->first();
        return $ticket;
    }

    public static function getTicketFull($id)
    {
        $ticket = self::getTicket($id);

        if ($ticket) {
            $ticket_attachments = TicketAttachment::where('ticket_id', '=', $ticket->id)->get();

            $ticket_messages = TicketMessage::select([
                'tickets_messages.id',
                'tickets_messages.comments',
                'tickets_messages.created_at',
                'u.name as fullname',
                'r.description as rol',
            ])
                ->where('tickets_messages.ticket_id', '=', $ticket->id)
                ->join('users as u', 'u.id', '=', 'tickets_messages.user_id')
                ->join('roles as r', 'r.id', '=', 'u.role_id')
                ->get();

            foreach ($ticket_messages as $message) {
                $attachments = [];
                $ticket_message_attachments = TicketMessageAttachment::where('ticket_message_id', '=', $message->id)->get();
                foreach ($ticket_message_attachments as $ticket_message_attachment) {
                    $ticket_message_attachment->ticket_id = $ticket->id;
                    $attachments [] = $ticket_message_attachment;
                }

                $message->attachments = $attachments;
            }

            $ticket->attachments = $ticket_attachments;
            $ticket->messages = $ticket_messages;
        }

        return $ticket;
    }
}
