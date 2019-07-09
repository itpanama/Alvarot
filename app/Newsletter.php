<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = 'newsletter';

    public static function buildQuery()
    {
        return self::select([
            'newsletter.id',
            'newsletter.title',
            'newsletter.start_date',
            'newsletter.end_date',
            'newsletter.created_at as newsletter_created',
            DB::raw('(DATE_FORMAT(newsletter.created_at, \'%Y-%m-%d %l:%i:%s %p\')) as newsletter_created_format'),
        ])
            ->orderBy('newsletter.id', 'asc');
    }

    public static function getNewsletter($id)
    {
        $query = self::buildQuery();
        $query->where('transporters.id', '=', $id);
        return $query->first();
    }
}
