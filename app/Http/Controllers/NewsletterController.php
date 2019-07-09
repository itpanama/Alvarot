<?php

namespace App\Http\Controllers;

use File;
use DB;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Newsletter;

class NewsletterController extends Controller
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

    public function indexActive(Request $request)
    {
        try {
            $query = $this->_buildQuery($request);

            $query->whereRaw('CURRENT_DATE BETWEEN newsletter.start_date AND newsletter.end_date');

            $query->orderBy('newsletter.id', 'desc');

            $data = $query->get();

            return response()->json($data, 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getNewsletter(Request $request, $id)
    {
        return $this->_getNewsletter($id);
    }

    private function _getNewsletter($id)
    {
        try {
            $newsletter = Newsletter::find($id);
            if (!$newsletter) {
                throw new \Exception('Newsletter not found');
            }

            return response()->json($newsletter, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteNewsletter(Request $request, $id)
    {
        try {

            $newsletter = Newsletter::find($id);

            if (!$newsletter) {
                throw new \Exception('Newsletter not found');
            }

            $newsletter->delete();

            return response()->json(true, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function validator(array $data)
    {
        $rules = [
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ];

        return Validator::make($data, $rules);
    }

    public function new(Request $request)
    {
        $validator = $this->validator($request->only([
            'title',
            'start_date',
            'end_date'
        ]));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $newsletter = new Newsletter();

            $attachment = $request->input('attachment');

            $newsletter->title = $request->title;
            $newsletter->start_date = $request->start_date;
            $newsletter->end_date = $request->end_date;
            $newsletter->attachment_name = $attachment['attachment_name'];
            $newsletter->attachment_size = $attachment['attachment_size'];
            $newsletter->save();

            $base_attachments_path_tmp = base_path() . "/attachments/users/" . $user->id;
            $base_attachment_path = base_path() . "/attachments/comunicados/" . $newsletter->id;
            if (!File::exists($base_attachment_path)) {
                File::makeDirectory($base_attachment_path, 0755, true);
            }

            $old_path = $base_attachments_path_tmp . "/" . $attachment['attachment_name'];
            $new_path = $base_attachment_path . "/" . $attachment['attachment_name'];

            File::move($old_path, $new_path);

            $newsletter->save();

            DB::commit();

            return response()->json($newsletter, 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function editNewsletter(Request $request, $id)
    {
        $validator = $this->validator($request->only([
            'title',
            'start_date',
            'end_date'
        ]));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        try {
            DB::beginTransaction();
            $newsletter = Newsletter::find($id);
            $newsletter->title = $request->title;
            $newsletter->start_date = $request->start_date;
            $newsletter->end_date = $request->end_date;
            $newsletter->save();
            DB::commit();

            return response()->json($newsletter, 200);

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

    public function downloadAttachment(Request $request, $id)
    {
        try {
            $newsletter = Newsletter::find($id);
            if (!$newsletter) {
                throw new \Exception('Newsletter not found.');
            }

            $attachment_path = base_path() . "/attachments/comunicados/" . $newsletter->id . "/" . $newsletter->attachment_name;

            if (!File::exists($attachment_path)) {
                return response()->json(['recurso no encontrado.'], 404);
            }

            return response()->download($attachment_path);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function inlineAttachment(Request $request, $id)
    {
        try {
            $newsletter = Newsletter::find($id);
            if (!$newsletter) {
                throw new \Exception('Newsletter not found.');
            }

            $attachment_path = base_path() . "/attachments/comunicados/" . $newsletter->id . "/" . $newsletter->attachment_name;

            if (!File::exists($attachment_path)) {
                return response()->json(['recurso no encontrado.'], 404);
            }

            return response()->file($attachment_path);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function _buildQuery(Request $request)
    {
        ini_set('memory_limit', -1);

        $id = $request->input('id');
        $title = $request->input('title');
        $created_at_start = $request->input('created_at_start');
        $created_at_end = $request->input('created_at_end');

        $query = Newsletter::buildQuery();

        if ($id) {
            $query->where('newsletter.id', '=', $id);
        }

        if ($title) {
            $query->whereRaw("UPPER(newsletter.title) like UPPER(\"%$title%\")");
        }

        if ($created_at_start && $created_at_end) {
            $query->whereRaw('date_format(newsletter.created_at, "%Y-%m-%d") >= ? and date_format(newsletter.created_at, "%Y-%m-%d") <= ?', [$created_at_start, $created_at_end]);
        }

        return $query;
    }
}
