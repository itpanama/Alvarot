<?php

namespace App\Http\Controllers;

use App\PaymentType;
use App\TypeService;
use App\TypePort;
use App\TypeDocumentOffice;
use App\User;
use App\TruckerStatus;
use Auth;

class GeneralController extends Controller
{
    public function getOptions()
    {
        $data = [
            'payment_types' => PaymentType::get(),
            'type_services' => TypeService::get(),
            'type_ports' => TypePort::get(),
            'type_document_office' => TypeDocumentOffice::get(),
        ];

        $user = Auth::user();

        if ($user->role_id != config('app.role.customer')) {

            $data['employers'] = User::getEmployers();

        }

        return response()->json($data, 200);
    }

    public function getOptionsTruckerStatus()
    {
        $data = [
            'trucker_status' => TruckerStatus::get(),
        ];

        return response()->json($data, 200);
    }
}
