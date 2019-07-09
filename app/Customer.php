<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use DB;

class Customer extends Model {
    protected $table = 'customers';

    public function user() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public static function getCustomer($user_id) {
        return parent::where('user_id', '=', $user_id)->first();
    }
}
