<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use Log;
use App;
use Auth;
use Session;
use Redirect;
use File;
use Validator;
use Hash;

use Illuminate\Http\Request;
use App\TruckerMessage;
use App\TruckerMessageAttachment;
use App\TruckerDocumentType;
use App\Trucker;
use App\Customer;
use App\User;
use App\TruckerDocuments;
use App\Jobs\ProcessSendEmailTruckerNewMessage;

class TruckerController extends Controller
{
    public function __construct()
    {
        $this->maxSizeDocument = 5000; //5MB
        $this->documentMimeType = 'pdf,docx,xlsx,doc,jpg,jpeg,gif,bmp';
        $this->maxSizeDocumentHuman = ($this->maxSizeDocument / 1000) . "MB";
        App::setLocale('es');
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

    public function getActiveTruckers(Request $request)
    {
        try {
            $truckers = Trucker::getActive()->get();
            return response()->json($truckers, 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function solicitudTransportista()
    {
        return redirect('registro-transportista');
    }

    public function crearSolicitudTransportista(Request $request)
    {
        return redirect('registro_transportista');
    }

    public function show(Request $request, $id)
    {
        return $this->_getTrucker($id);
    }

    public function downloadAttachment(Request $request, $trucker_id, $attachment_id)
    {
        try {
            $transpoter = Trucker::find($trucker_id);
            if (!$transpoter) {
                return response()->json(['recurso no encontrado.'], 404);
            }

            $transporter_document_attachment = TruckerDocuments::find($attachment_id);
            if (!$transporter_document_attachment) {
                return response()->json(['recurso no encontrado.'], 404);
            }

            $attachment_path = base_path() . "/attachments/transporters_documents/" . $transpoter->id . "/" . $transporter_document_attachment->attachment_name;

            if (!File::exists($attachment_path)) {
                return response()->json(['recurso no encontrado.'], 404);
            }

            return response()->download($attachment_path);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function changeTruckerStatus(Request $request)
    {
        try {

            $rules = [
                'id' => 'required',
                'trucker_status_id' => 'required'
            ];

            $data = $request->only([
                'id',
                'trucker_status_id'
            ]);

            $user = Auth::user();

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages()], 400);
            }

            $trucker = Trucker::find($request->id);
            if (!$trucker) {
                throw new \Exception('Bad request');
            }

            $trucker->trucker_status_id = $request->trucker_status_id;
            $trucker->trucker_status_user_id = $user->id;
            $trucker->trucker_status_date = date('Y-m-d H:i:s');
            $trucker->save();

            return response()->json(true, 200);

        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        }
    }

    public function editTrucker(Request $request, $id = null)
    {
        if (!$id) {
            throw new \Exception('Bad request');
        }

        $data = $request->all();

        $validator = $this->validator($data, $id);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        return $this->update($request);
    }

    public function getTruckerRequest()
    {
        $user = Auth::user();
        if (!in_array($user->role_id, [config('app.role.customer'), config('app.role.trucker')])) {
            throw new \Exception('Bad request');
        }

        $trucker = Trucker::getTruckerFull(['truckers.user_id' => $user->id]);

        if ($user->role_id == config('app.role.customer') && !$trucker) {
            $trucker = new \stdClass();
        }

        $trucker->trucker_document_type = TruckerDocumentType::getTruckerDocumentActive();

        return response()->json(compact('trucker'), 200);
    }

    public function editTruckerRequest(Request $request)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role_id, [config('app.role.customer'), config('app.role.trucker')])) {
            throw new \Exception('Bad request');
        }

        $trucker = Trucker::where('user_id', $user->id)->first();
        $data = $request->all();

        if ($trucker) {
            $data['trucker_id'] = $trucker->id;
        }

        $validator = $this->validator_trucker($data);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        try {
            DB::beginTransaction();

            $trucker = Trucker::find($request->id);
            if (!$trucker) {
                if ($user->role_id == config('app.role.trucker')) {
                    throw new \Exception('Trucker not found');
                } else {
                    $trucker = new Trucker();
                    $trucker->user_id = $user->id;
                }
            }

            $user->email = $request->email;
            $user->save();

            $trucker->company_name_operation = $request->input('company_name_operation');
            $trucker->address_company = $request->input('address_company');
            $trucker->number_policy = $request->input('number_policy');
            $trucker->expiration_date = $request->input('expiration_date');

            $trucker->email = $request->input('email');
            $trucker->email_2 = $request->input('email_2');
            $trucker->phone = $request->input('phone');
            $trucker->phone_2 = $request->input('phone_2');
            $trucker->contact_name = $request->input('contact_name');

            if (!$trucker->trucker_status_id) {
                $trucker->trucker_status_id = config('app.trucker_status.pendiente');
            } else {
                $trucker->trucker_status_id = $request->input('trucker_status_id');
            }

            $trucker->save();

            DB::commit();

            return $this->_getTrucker($trucker->id);
        } catch (\Exception $e) {
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

        $attachment_name = sprintf('%s_%s_%s', date('dmY_his'), time(), $file->getClientOriginalName());
        $attachment_size = $file->getClientSize();

        if ($attachment_size === 0) {
            return response()->json(['error' => 'The file size cannot be zero.'], 400);
        }

        $request->file('file')->move($attachments_path, $attachment_name);

        $attachment = [
            'attachment_name' => $attachment_name,
            'attachment_size' => $attachment_size,
            'trucker_document_type_id' => (int)$request->input('trucker_document_type_id')
        ];

        return response()->json($attachment, 200);
    }

    public function uploadAttachmentFromMessage(Request $request)
    {
        $file = $request->file('file');

        $trucker_id = $request->input('trucker_id');

        if (!$trucker_id) {
            return response()->json(['error' => 'The trucker id is required.'], 400);
        }

        $attachments_path = base_path() . "/attachments/trucker/" . $trucker_id . "/messages";

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

    public function newMessage(Request $request, $trucker_id = null)
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

            $trucker = Trucker::find($trucker_id);
            if (!$trucker) {
                throw new \Exception('Trucker does not exist.');
            }

            $trucker_message = new TruckerMessage();

            $trucker_message->user_id = $user->id;
            $trucker_message->trucker_id = $trucker->id;
            $trucker_message->comments = $request->comments;
            $trucker_message->save();

            $attachments = $request->input('attachments');
            if (count($attachments)) {
                foreach ($attachments as $attachment) {
                    $attachment_model = new TruckerMessageAttachment();
                    $attachment_model->trucker_message_id = $trucker_message->id;
                    $attachment_model->attachment_name = $attachment['attachment_name'];
                    $attachment_model->attachment_size = $attachment['attachment_size'];
                    $attachment_model->save();
                }
            }

            DB::commit();

            $this->sendMailTruckerNewMessage($trucker_id, $trucker_message->id, $request->comments);

            return $this->_getTrucker($trucker_id);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function sendMailTruckerNewMessage($trucker_id, $trucker_message_id, $comments)
    {
        $trucker = Trucker::getTrucker($trucker_id);

        if (!$trucker) {
            return 'invalid trucker';
        }

        $trucker = $trucker->toArray();

        $mailData = new \stdClass();

        $user = Auth::user();

        $mailData->from = ['address' => $user->email, 'name' => $user->name];

        $mailData->to = (object)[
            'email' => $trucker['email'],
            'name' => $trucker['contact_name']
        ];

        $mailData->cc = [];

        $settings = App\Settings::first();

        if ($settings->email_trucker_list) {

            $emails = explode("\n", $settings->email_trucker_list);

            $mailData->cc = $emails;

        }

        $trucker['comments'] = $comments;

        $trucker['attachments'] = [];

        $trucker_attachments = TruckerMessageAttachment::where('trucker_message_id', '=', $trucker_message_id)->get();
        $dir = base_path();
        foreach ($trucker_attachments as $attachment) {
            $attachment_path = $dir . '/attachments/trucker/' . $trucker['id'] . '/messages/' . $attachment->attachment_name;
            if (file_exists($attachment_path)) {
                $trucker['attachments'] [] = [
                    'path' => $attachment_path,
                    'name' => $attachment->attachment_name,
                    'mime' => mime_content_type($attachment_path),
                ];
            }
        }

        $job = (new ProcessSendEmailTruckerNewMessage($trucker, $mailData))->onQueue('emails');

        $this->dispatch($job);
    }

    public function deleteTrucker(Request $request, $id) {
        try{
            $trucker = Trucker::find($id);

            if (!$trucker) {
                return response()->json(['error' => 'Not authorized.' ], 400);
            }

            if ($trucker->trucker_status_id == config('app.trucker_status.aprobado')) {
                return response()->json(['error' => 'Only truckers with status pending can be deleted.' ], 400);
            }

            if ($trucker->user_id) {
                $customer = Customer::where('user_id', '=', $trucker->user_id)->first();
                if (!$customer) {
                    $trucker_user = User::find($trucker->user_id);
                    if ($trucker_user) {
                        $trucker_user->delete();
                    }
                }
            }

            $trucker->delete();

            return response()->json(true, 200);
        }catch(\Exception $e) {
            return response()->json(['error' => 'Unable to delete the trucker.' ], 400);
        }
    }

    public function downloadMessageAttachment(Request $request, $trucker_id, $attachment_id)
    {
        try {
            $trucker = Trucker::find($trucker_id);
            if (!$trucker) {
                throw new \Exception('Trucker not found.');
            }

            $ticket_attachment = TruckerMessageAttachment::find($attachment_id);
            if (!$ticket_attachment) {
                throw new \Exception('Attachment not found.');
            }

            $attachment_path = base_path() . "/attachments/trucker/" . $trucker->id . "/messages/" . $ticket_attachment->attachment_name;

            if (!File::exists($attachment_path)) {
                return response()->json(['recurso no encontrado.'], 404);
            }

            return response()->download($attachment_path);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function saveAttachments(Request $request, $trucker_id)
    {
        try {
            $user = Auth::user();

            if ($user->role_id != config('app.role.admin')) {
                return response()->json(['Not authorized.'], 404);
            }

            $trucker = Trucker::get(['truckers.user_id' => $user->id]);
            $attachments = $request->input('attachments');
            if (count($attachments)) {
                $base_attachments_path_tmp = base_path() . "/attachments/users/" . $user->id;

                $trucker_documents_path = sprintf('%s/attachments/transporters_documents/%s', base_path(), $trucker->id);

                if (!File::exists($trucker_documents_path)) {
                    File::makeDirectory($trucker_documents_path, 0755, true);
                }

                foreach ($attachments as &$attachment) {
                    $document = new TruckerDocuments();
                    $document->trucker_id = $trucker->id;
                    $document->attachment_name = $attachment['attachment_name'];
                    $document->attachment_size = $attachment['attachment_size'];
                    $document->trucker_document_type_id = $attachment['trucker_document_type_id'];

                    $old_path = $base_attachments_path_tmp . "/" . $attachment['attachment_name'];
                    $new_path = $trucker_documents_path . "/" . $attachment['attachment_name'];

                    if (File::move($old_path, $new_path)) {
                        $document->save();
                        $attachment['id'] = $document->id;
                    }

                    unset($attachment);
                }

                return response()->json(compact('attachments'), 200);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteAttachment(Request $request, $attachment_id)
    {
        try {
            $trucker_document = TruckerDocuments::find($attachment_id);

            if (!$trucker_document) {
                throw new \Exception('Bad request');
            }

            $trucker_documents_path = sprintf('%s/attachments/transporters_documents/%s/%s', base_path(), $trucker_document->trucker_id, $trucker_document->attachment_name);

            if (@unlink($trucker_documents_path)) {
                $trucker_document->delete();
                return response()->json(true, 200);
            } else {
                throw new \Exception("Error trying to delete the document {$trucker_document->attachment_name}");
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $trucker = Trucker::find($request->id);
            if (!$trucker) {
                throw new \Exception('Trucker not found');
            }

            $user = $trucker->user;
            if (!$user) {
                $user = new User();
                $user->name = "trucker id {$request->id}";
            }

            $user->email = $request->email;
            $user->username = $request->username;
            $user->role_id = config('app.role.trucker');
            $user->active = $request->active ? 1 : 0;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            $trucker->user_id = $user->id;

            $trucker->company_name_operation = $request->input('company_name_operation');
            $trucker->address_company = $request->input('address_company');
            $trucker->number_policy = $request->input('number_policy');
            $trucker->expiration_date = $request->input('expiration_date');

            $trucker->email = $request->input('email');
            $trucker->email_2 = $request->input('email_2');
            $trucker->phone = $request->input('phone');
            $trucker->phone_2 = $request->input('phone_2');
            $trucker->contact_name = $request->input('contact_name');
            $trucker->trucker_status_id = $request->input('trucker_status_id');

            $trucker->save();

            DB::commit();

            return $this->_getTrucker($trucker->id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function _buildQuery(Request $request)
    {
        ini_set('memory_limit', -1);

        $id = $request->input('id');
        $company_name_operation = $request->input('company_name_operation');
        $number_policy = $request->input('number_policy');
        $contact_name = $request->input('contact_name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $trucker_status_id = $request->input('trucker_status_id');
        $created_at_start = $request->input('created_at_start');
        $created_at_end = $request->input('created_at_end');

        $expiration_date_at_start = $request->input('expiration_date_at_start');
        $expiration_date_at_end = $request->input('expiration_date_at_end');

        $query = Trucker::buildQuery();

        if ($id) {
            $query->where('truckers.id', '=', $id);
        }

        if ($company_name_operation) {
            $query->whereRaw("UPPER(truckers.company_name_operation) like UPPER(\"%$company_name_operation%\")");
        }

        if ($number_policy) {
            $query->whereRaw("UPPER(truckers.number_policy) like UPPER(\"%$number_policy%\")");
        }

        if ($contact_name) {
            $query->whereRaw("UPPER(truckers.contact_name) like UPPER(\"%$contact_name%\")");
        }

        if ($email) {
            $query->whereRaw("UPPER(truckers.email) like UPPER(\"%$email%\")");
        }

        if ($phone) {
            $query->whereRaw("UPPER(truckers.phone) like UPPER(\"%$phone%\")");
        }

        if ($trucker_status_id) {
            $query->where('truckers.trucker_status_id', '=', $trucker_status_id);
        }

        if ($created_at_start && $created_at_end) {
            $query->whereRaw('date_format(truckers.created_at, "%Y-%m-%d") >= ? and date_format(truckers.created_at, "%Y-%m-%d") <= ?', [$created_at_start, $created_at_end]);
        }

        if ($expiration_date_at_start && $expiration_date_at_end) {
            $query->whereRaw('date_format(truckers.expiration_date, "%Y-%m-%d") >= ? and date_format(truckers.expiration_date, "%Y-%m-%d") <= ?', [$expiration_date_at_start, $expiration_date_at_end]);
        }

        return $query;
    }

    private function _getTrucker($id)
    {
        try {
            $trucker = Trucker::getTruckerFull(['truckers.id' => $id]);
            if (!$trucker) {
                throw new \Exception('Bad request');
            }

            $trucker->trucker_document_type = TruckerDocumentType::getTruckerDocumentActive();

            return response()->json(compact('trucker'), 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function validator(array $data, $id = null)
    {
        $rules = [
            'company_name_operation' => 'required',
            'address_company' => 'required',
            'number_policy' => 'required',
            'number_policy' => "required|unique:truckers,number_policy,{$id}",
            'expiration_date' => 'required|date',
            'email' => "required|email|unique:truckers,email,{$id}|unique:users,email",
            'email_2' => 'email',
            'phone' => 'required',
            'contact_name' => 'required',
        ];

        if (empty($data['email_2'])) {
            unset($rules['email_2']);
        }

        if (!$data['user_id']) {
            $rules['username'] = 'required|unique:users,username';
            $rules['password'] = 'required|confirmed';
        }

        if ($data['user_id']) {
            $rules['email'] = "required|unique:truckers,email,{$id}|unique:users,email,{$data['user_id']}";
            $rules['username'] = "required|unique:users,username,{$data['user_id']}";
            $rules['password'] = 'required|confirmed';

            if (!isset($data['password'])) {
                unset($rules['password']);
            }
        }

        return Validator::make($data, $rules);
    }

    private function validator_trucker(array $data)
    {
        $rules = [
            'company_name_operation' => 'required',
            'address_company' => 'required',
            'number_policy' => 'required|unique:truckers,number_policy',
            'expiration_date' => 'required|date',
            'email' => "required|unique:truckers,email",
            'email_2' => 'email',
            'phone' => 'required',
            'contact_name' => 'required',
        ];

        if (!empty($data['trucker_id'])) {
            $rules['number_policy'] = "required|unique:truckers,number_policy,{$data['trucker_id']}";
            $rules['email'] = "required|unique:truckers,email,{$data['trucker_id']}";
        }

        if (empty($data['email_2'])) {
            unset($rules['email_2']);
        }

        return Validator::make($data, $rules);
    }
}
