<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Settings;
use Redirect;
use Session;
use Validator;

class SettingsController extends Controller
{
    public function index()
    {
        return $this->getSettings();
    }

    public function update(Request $request)
    {
        try {
            $email_counter_list = $request->input('email_counter_list');
            $email_trucker_list = $request->input('email_trucker_list');

            if ($email_counter_list) {
                $emails = explode("\n", $email_counter_list);
                $email_deluxe = '';
                if (count($emails) > 0) {
                    foreach ($emails as $key => $email) {
                        $email = trim($email);
                        if (empty($email)) {
                            continue;
                        }

                        $rules = [
                            'email_counter_list' => 'email'
                        ];

                        $data = [
                            'email_counter_list' => $email
                        ];

                        $validator = Validator::make($data, $rules);

                        if ($validator->fails()) {
                            return response()->json(['errors' => $validator->messages()], 400);
                        }

                        $email_deluxe .= $email . ($key == count($emails) - 1 ? '' : "\n");
                    }
                }
            }

            if ($email_trucker_list) {
                $emails = explode("\n", $email_trucker_list);
                $email_deluxe = '';
                if (count($emails) > 0) {
                    foreach ($emails as $key => $email) {
                        $email = trim($email);
                        if (empty($email)) {
                            continue;
                        }

                        $rules = [
                            'email_trucker_list' => 'email'
                        ];

                        $data = [
                            'email_trucker_list' => $email
                        ];

                        $validator = Validator::make($data, $rules);

                        if ($validator->fails()) {
                            return response()->json(['errors' => $validator->messages()], 400);
                        }

                        $email_deluxe .= $email . ($key == count($emails) - 1 ? '' : "\n");
                    }
                }
            }

            $settings = Settings::find(1);
            $settings->email_counter_list = $email_counter_list == '' ? NULL : $email_counter_list;
            $settings->email_trucker_list = $email_trucker_list == '' ? NULL : $email_trucker_list;

            $settings->save();

            return $this->getSettings();

        } catch (\Exception $e) {
            switch ($e->errorInfo[0]) {
                default:
                    return response()->json(['errors' => $e->errorInfo[2]], 400);
                    break;
            }
        }


        $setting = Settings::find(1);
        return view('admin.settings.form')->with(['setting' => $setting]);
    }

    private function getSettings()
    {
        $setting = Settings::select('email_counter_list', 'email_trucker_list')->find(1);
        return response()->json($setting, 200);
    }
}
