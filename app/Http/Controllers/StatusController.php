<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Status;

class StatusController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        try{
            $limitPages = $request->input('limitPages', config('app.pagination'));
            $description = strtoupper($request->input('description'));
            $active = strtoupper($request->input('active'));

            $query = DB::table('status');

            if($description){
                $query->whereRaw("UPPER(description) like UPPER(\"%$description%\")");
            }

            if($active == '1' || $active == '0'){
                $query->where('active', '=', $active);
            }

            $query->orderBy('description', 'asc');

            $users = $query->paginate($limitPages);
            return response()->json($users, 200);
        } catch(Exception $e){
            return response()->json([ 'error' => $e->getMessage() ], 400);
        }
    }

    public function getStatus(Request $request, $id) {
        return $this->_getStatus($id);
    }

    public function deleteStatus(Request $request, $id) {
        try{
            $status = Status::find($id);
            if(!$status){
                throw new \Exception('Bad request');
            }

            $status->delete();

            return response()->json(true, 200);
        } catch(\Exception $e){
            return response()->json([ 'error' => $e->getMessage() ], 400);
        }
    }

    public function newStatus(Request $request) {
        $validator = $this->validator($request->only([
                                                         'description'
                                                     ]));

        if($validator->fails()){
            return response()->json([ 'errors' => $validator->messages() ], 400);
        }

        return $this->createOrUpdate($request);
    }

    public function editStatus(Request $request, $id = null) {
        if(!$id){
            throw new \Exception('Bad request');
        }

        $validator = $this->validator($request->only([
                                                         'description',
                                                     ]), $id);

        if($validator->fails()){
            return response()->json([ 'errors' => $validator->messages() ], 400);
        }

        return $this->createOrUpdate($request);
    }

    private function createOrUpdate(Request $request) {
        try{
            if($request->id){
                $status = Status::find($request->id);
                if(!$status){
                    throw new \Exception('Bad request');
                }
            } else {
                $status = new Status();
            }

            $status->description = $request->description;
            $status->color = $request->color ? $request->color : null;
            $status->start = $request->start ? $request->start : null;
            $status->end = $request->end ? $request->end : null;
            $status->active = $request->active;
            $status->save();

            return $this->_getStatus($status->id);
        } catch(\Exception $e){
            return response()->json([ 'error' => $e->getMessage() ], 400);
        }
    }

    private function _getStatus($id) {
        try{
            $status = Status::find($id);
            if(!$status){
                throw new \Exception('Status not found');
            }

            return response()->json($status, 200);
        } catch(\Exception $e){
            return response()->json([ 'error' => $e->getMessage() ], 400);
        }
    }

    private function validator(array $data, $id = null) {
        $rules = [
            'description' => 'required|string'
        ];

        if($id){
            //edit case
        } else {
            //new case
        }

        return Validator::make($data, $rules);
    }
}
