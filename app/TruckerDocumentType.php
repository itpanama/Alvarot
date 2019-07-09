<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TruckerDocumentType extends Model
{
    protected $table = 'trucker_document_type';

    public static function getTruckerDocumentActive() {
        return self::where('active', 1)->get();
    }
}
