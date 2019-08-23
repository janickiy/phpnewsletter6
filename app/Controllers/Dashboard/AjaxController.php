<?php

namespace App\Controllers\Dashboard;

use App\Controllers\Controller;
use App\Models\{Settings};
use App\Helper\{StringHelpers,ActionHelpers};

class AjaxController extends Controller
{
    public function action($request, $response)
    {
        if ($request->getParam('action')) {
            switch ($request->getParam('action')) {
                case 'change_lng':

                    $setting = Settings::where('name', 'LANGUAGE')->first();
                    $setting->value = $request->getParam('lng');

                    return $response->withJson(
                        ['result' => $setting->save()]
                    );

                    break;

                case 'send_test_email':

                    $subject = $request->getParam('name');
                    $body = $request->getParam('body');
                    $prior = $request->getParam('prior');
                    $email = $request->getParam('email');

                    $errors = [];


                    if (empty($subject)) $errors[] = StringHelpers::trans('error.empty_subject');
                    if (empty($body)) $errors[] = StringHelpers::trans('error.empty_content');
                    if (empty($email)) $errors[] = StringHelpers::trans('error.empty_email');
                    if (!empty($email) && StringHelpers::isEmail($email)) $errors[] = StringHelpers::trans('error.empty_email');

                    if (count($errors) == 0) {

                        ActionHelpers::sendEmail($email, $subject, $body, $prior);


                    } else {
                        $result_send = 'errors';
                        $msg = implode(",", $errors);
                    }

                    $content = ['result' => $result_send, 'msg' => $msg];


                    break;

            }
        }
    }
}