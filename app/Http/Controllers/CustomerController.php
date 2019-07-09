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

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        try{
            $limitPages = $request->input('limitPages', config('app.pagination'));
            $name = strtoupper($request->input('name'));
            $email = strtoupper($request->input('email'));

            $query = DB::table('customers as c')
                ->select(['c.id', 'c.name', 'c.email']);

            if ($name) {
                $query->whereRaw("UPPER(c.name) like UPPER(\"%$name%\")");
            }

            if ($email) {
                $query->whereRaw("UPPER(c.email) like UPPER(\"%$email%\")");
            }

            $query->orderBy('c.name', 'asc');

            $customers = $query->paginate($limitPages);

            return response()->json($customers, 200);

        }catch(Exception $e) {
            return response()->json(['error' => $e->getMessage() ], 400);
        }
    }

    public function getCustomer(Request $request, $id) {
        return $this->_getCustomer($id);
    }

    public function deleteCustomer(Request $request, $id) {
        try{

            $customer = Customer::find($id);

            if (!$customer) {
                throw new \Exception('Customer not found');
            }

            $user = User::find($customer->user_id);

            $user->delete();

            return response()->json(true, 200);
        }catch(\Exception $e) {
            return response()->json(['error' => 'Unable to delete the customer.' ], 400);
        }
    }

    public function newCustomer(Request $request) {
        $validator = $this->validator($request->only([
            'name',
            'email',
            'email_optional',
            'username',
            'password',
            'password_confirmation'
        ]));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        return $this->createOrUpdate($request);
    }

    public function editCustomer(Request $request, $id = null) {
        if (!$id) {
            throw new \Exception('Bad request');
        }

        $validator = $this->validator($request->only([
            'name',
            'email',
            'email_optional',
            'username',
            'password',
            'password_confirmation'
        ]), $id);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        return $this->createOrUpdate($request);
    }

    private function createOrUpdate(Request $request) {
        try{
            DB::beginTransaction();

            if ($request->id) {
                $customer = Customer::find($request->id);
                $user = $customer->user;
                if (!$customer) {
                    throw new \Exception('Customer not found');
                }
            } else {
                $customer = new Customer();
                $user = new User();
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->role_id = config('app.role.customer');
            $user->active = $request->active;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            $customer->name = $user->name;
            $customer->email = $user->email;
            $customer->user_id = $user->id;
            $customer->email_optional = $request->email_optional;
            $customer->save();

            $permission = UserPermission::where('permission_id', '=', config('app.permission.trucker_form'))
                ->where('user_id', '=', $user->id)
                ->first();

            if ($request->trucker_form && !$permission){
                $user_permission = new UserPermission();
                $user_permission->user_id = $user->id;
                $user_permission->permission_id = config('app.permission.trucker_form');
                $user_permission->save();
            } else if (!$request->trucker_form && $permission){
                $permission->delete();
            }

            DB::commit();

            return $this->_getCustomer($customer->id);
        }catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage() ], 400);
        }
    }

    private function _getCustomer($id){
        try{
            $customer = Customer::find($id);

            if (!$customer) {
                throw new \Exception('Customer not found');
            }

            $customer->username = $customer->user->username;
            $customer->active = $customer->user->active;

            $permission = UserPermission::where('permission_id', '=', config('app.permission.trucker_form'))
                ->where('user_id', '=', $customer->user_id)
                ->first();

            unset($customer->user);

            unset($customer->user_id);

            $customer->trucker_form = $permission ? true : false;

            return response()->json($customer, 200);

        }catch(\Exception $e) {

            return response()->json(['error' => $e->getMessage() ], 400);

        }
    }

    private function validator(array $data, $id = null) {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:customers,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|confirmed'
        ];

        if(isset($date['email_optional'])) {
            $rules['email_optional'] = 'email';
        }

        if ($id) {
            //edit case
            $customer = Customer::find($id);

            $rules['email'] = "required|email|unique:customers,email,{$id}||unique:users,email,{$customer->user_id}";
            $rules['username'] = 'required|unique:users,username,' . $customer->user_id;

            if (!isset($data['password'])) {
                unset($rules['password']);
            }
        } else {
            //new case
        }

        return Validator::make($data, $rules);
    }
}
