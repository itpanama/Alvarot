<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Customer;
use App\UserPermission;
use App\Trucker;
use App\Role;

class UserController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    public function comunicado($archivo = null) {
        $pathToFile = base_path() . "/comunicados/${archivo}";
        if (file_exists($pathToFile)) {
            return response()->file($pathToFile);
        } else {
            return response()->json([], 404);
        }
    }

    public function index(Request $request) {
        try{
            $limitPages = $request->input('limitPages', config('app.pagination'));
            $name = strtoupper($request->input('name'));
            $username = strtoupper($request->input('username'));
            $email = strtoupper($request->input('email'));
            $active = strtoupper($request->input('active'));
            $role_id = strtoupper($request->input('role_id'));

            $query = DB::table('users')
                ->select([ 'id', 'name', 'username', 'email', 'active' ]);

            $query->whereIn('role_id', [ config('app.role.admin'), config('app.role.employee') ]);

            if($name){
                $query->whereRaw("UPPER(name) like UPPER(\"%$name%\")");
            }

            if($username){
                $query->whereRaw("UPPER(username) like UPPER(\"%$username%\")");
            }

            if($email){
                $query->whereRaw("UPPER(email) like UPPER(\"%$email%\")");
            }

            if($active == '1' || $active == '0'){
                $query->where('active', '=', $active);
            }

            if($role_id){
                $query->where('role_id', '=', $role_id);
            }

            $query->orderBy('name', 'asc');

            $users = $query->paginate($limitPages);
            return response()->json($users, 200);
        } catch(Exception $e){
            return response()->json([ 'error' => $e->getMessage() ], 400);
        }
    }

    public function getInfo() {
        $user = Auth::user();

        if($user->role_id == config('app.role.customer')){
            $customer = Customer::where('user_id', '=', $user->id)->first();
            $user->customer = [
                'customer_id' => $customer->id,
                'email_optional' => $customer->email_optional
            ];
        }

        if($user->role_id == config('app.role.trucker')){
            $trucker = Trucker::where('user_id', '=', $user->id)->first();
            if ($trucker) {
                $user->trucker = [
                    'trucker_id' => $trucker->id,
                    'email_optional' => $trucker->email_2
                ];
            }
        }

        $permissions = UserPermission::select('permission_id')->where('user_id', '=', $user->id)->get()->toArray();

        $permissions = array_map(function($permission) {
            return $permission['permission_id'];
        }, $permissions);

        $user->permissions = [];

        if (count($permissions)) {

            $user->permissions = $permissions;

        }

        return response()->json($user, 200);
    }

    public function getRoles() {
        try{
            $roles = Role::select([ 'id', 'description' ])
                ->whereIn('id', [ config('app.role.admin'), config('app.role.employee') ])
                ->get();

            if(!$roles){
                throw new \Exception('Roles not found');
            }

            return response()->json($roles, 200);
        } catch(\Exception $e){
            return response()->json([ 'error' => $e->getMessage() ], 400);
        }
    }

    public function getUser(Request $request, $id) {
        return $this->_getUser($id);
    }

    public function deleteUser(Request $request, $id) {
        try{
            $user = User::find($id);
            if(!$user){
                throw new \Exception('Bad request');
            }

            $user->delete();

            return response()->json(true, 200);
        } catch(\Exception $e){
            return response()->json([ 'error' => $e->getMessage() ], 400);
        }
    }

    public function newUser(Request $request) {
        $validator = $this->validator($request->only([
                                                         'name',
                                                         'email',
                                                         'username',
                                                         'password',
                                                         'password_confirmation',
                                                         'role_id',
                                                     ]));

        if($validator->fails()){
            return response()->json([ 'errors' => $validator->messages() ], 400);
        }

        return $this->createOrUpdate($request);
    }

    public function editUser(Request $request, $id = null) {
        if(!$id){
            throw new \Exception('Bad request');
        }

        $validator = $this->validator($request->only([
                                                         'name',
                                                         'email',
                                                         'username',
                                                         'password',
                                                         'password_confirmation',
                                                         'role_id',
                                                     ]), $id);

        if($validator->fails()){
            return response()->json([ 'errors' => $validator->messages() ], 400);
        }

        return $this->createOrUpdate($request);
    }

    private function createOrUpdate(Request $request) {
        try{
            if($request->id){
                $user = User::find($request->id);
                if(!$user){
                    throw new \Exception('Bad request');
                }
            } else {
                $user = new User();
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->role_id = $request->role_id;
            $user->active = $request->active;

            if($request->password){
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return $this->_getUser($user->id);
        } catch(\Exception $e){
            return response()->json([ 'error' => $e->getMessage() ], 400);
        }
    }

    private function _getUser($id) {
        try{
            $user = User::find($id);
            if(!$user){
                throw new \Exception('User not found');
            }

            $user->roles = Role::select([ 'id', 'description' ])
                ->whereIn('id', [ config('app.role.admin'), config('app.role.employee') ])
                ->get();

            return response()->json($user, 200);
        } catch(\Exception $e){
            return response()->json([ 'error' => $e->getMessage() ], 400);
        }
    }

    private function validator(array $data, $id = null) {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|confirmed'
        ];

        if($id){
            //edit case

            $rules['email'] = 'required|email|unique:users,email,' . $id;
            $rules['username'] = 'required|unique:users,username,' . $id;

            if(!isset($data['password'])){
                unset($rules['password']);
            }
        } else {
            //new case
        }

        return Validator::make($data, $rules);
    }
}
