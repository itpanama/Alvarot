<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FailedJob;
use App\Job;
use Auth;
use DB;

class MonitorController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role_id == config('app.role.admin') ||
            $user->role_id == config('app.role.employee')) {

            $jobs = Job::select(
                [
                    'id',
                    'queue',
                    'payload',
                    'attempts',
                    DB::raw('(DATE_FORMAT(FROM_UNIXTIME(reserved_at), \'%Y-%m-%d %H:%i:%s\')) as reserved_at'),
                    DB::raw('(DATE_FORMAT(FROM_UNIXTIME(available_at), \'%Y-%m-%d %H:%i:%s\')) as available_at'),
                    DB::raw('(DATE_FORMAT(FROM_UNIXTIME(created_at), \'%Y-%m-%d %H:%i:%s\')) as created_at'),
                ]
            )->get();


            $failed_jobs = FailedJob::select(
                [
                    'id',
                    'connection',
                    'queue',
                    'payload',
                    'exception',
                    'failed_at'
                ]
            )->get();


            foreach ($jobs as &$job) {
                $job->payload = json_decode($job->payload);
                unset($job);
            }

            foreach ($failed_jobs as &$failed_job) {
                $failed_job->payload = json_decode($failed_job->payload);
                $failed_job->exception = json_decode($failed_job->exception);
                unset($failed_job);
            }

            return response()->json(compact('jobs', 'failed_jobs'), 200);
        } else {
            return response()->json(['error' => 'No authorized'], 401);
        }
    }
}
