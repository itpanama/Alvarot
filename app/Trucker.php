<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Trucker extends Model
{
    protected $table = 'truckers';

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public static function buildQuery()
    {
        return self::select([
            'truckers.id',
            'truckers.company_name_operation',
            'truckers.address_company',
            'truckers.number_policy',
            'truckers.expiration_date',
            'truckers.email',
            'truckers.email_2',
            'truckers.phone',
            'truckers.phone_2',
            'truckers.user_id',
            'u.username',
            'u.active',
            'truckers.contact_name',
            'truckers.trucker_status_id',
            'ts.description as trucker_status_description',
            'truckers.created_at as truckers_created',
            'truckers.trucker_status_date as trucker_status_date',
            DB::raw('(DATE_FORMAT(truckers.created_at, \'%Y-%m-%d %l:%i:%s %p\')) as trucker_created_format'),
            DB::raw('(DATE_FORMAT(truckers.trucker_status_date, \'%Y-%m-%d %l:%i:%s %p\')) as trucker_status_date_format'),
            'truckers.comments'
        ])
            ->join('trucker_status as ts', 'ts.id', '=', 'truckers.trucker_status_id')
            ->leftJoin('users as u', 'u.id', '=', 'truckers.user_id')
            ->orderBy('truckers.id', 'asc');
    }


    public static function getTrucker($criteria)
    {
        $query = self::buildQuery();

        if (is_array($criteria)) {
            foreach($criteria as $field => $value) {
                $query->where($field, '=', $value);
            }
        } else {
            $query->where('truckers.id', '=', $criteria);
        }
        return $query->first();
    }

    public static function getActive()
    {
        $query = self::select('id', 'company_name_operation as typeaheadResult');
        $query->where('trucker_status_id', config('app.trucker_status.aprobado'));
        $query->whereNotNull('user_id');
        return $query;
    }

    public static function getTruckerFull($criteria)
    {
        $trucker = self::getTrucker($criteria);

        if ($trucker) {
            $attachments = TruckerDocuments::select([
                'trucker_documents.id',
                'trucker_documents.attachment_name',
                'trucker_documents.trucker_id',
                'trucker_documents.attachment_size',
                'dt.description as trucker_document_type',
                'trucker_documents.created_at',
            ])
                ->where('trucker_documents.trucker_id', '=', $trucker->id)
                ->join('trucker_document_type as dt', 'dt.id', '=', 'trucker_documents.trucker_document_type_id')
                ->get();

            $trucker->attachments = $attachments;

            $trucker_messages = TruckerMessage::select([
                'truckers_messages.id',
                'truckers_messages.comments',
                'truckers_messages.created_at',
                'u.name as fullname',
                'r.description as rol',
            ])
                ->where('truckers_messages.trucker_id', '=', $trucker->id)
                ->join('users as u', 'u.id', '=', 'truckers_messages.user_id')
                ->join('roles as r', 'r.id', '=', 'u.role_id')
                ->get();

            foreach ($trucker_messages as $message) {
                $attachments = [];
                $trucker_message_attachments = TruckerMessageAttachment::where('trucker_message_id', '=', $message->id)->get();
                foreach ($trucker_message_attachments as $trucker_message_attachment) {
                    $trucker_message_attachment->trucker_id = $trucker->id;
                    $attachments [] = $trucker_message_attachment;
                }

                $message->attachments = $attachments;
            }

            $trucker->messages = $trucker_messages;
        }

        return $trucker;
    }
}
