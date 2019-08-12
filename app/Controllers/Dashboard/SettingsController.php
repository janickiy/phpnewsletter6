<?php

namespace App\Controllers\Dashboard;

use App\Models\{Charset, Customheaders, Settings};
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class SettingsController extends Controller
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function index($request, $response)
    {
        $title = 'Настройки';

        $charset = Charset::get();
        $customheaders = Customheaders::get();

        return $this->view->render($response, 'dashboard/settings/index.twig', compact('title', 'charset', 'customheaders'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function update($request, $response)
    {

        $validation = $this->validator->validate($request, [
            'EMAIL' => ['rules' => v::email(), 'messages' => ['email' => 'Email указан не верно']],
            'LIMIT_NUMBER' => ['rules' => v::numeric(), 'messages' => ['numeric' => 'Значение должно быть числом']],
            'SLEEP' => ['rules' => v::numeric(), 'messages' => ['numeric' => 'Значение должно быть числом']],
            'DAYS_FOR_REMOVE_SUBSCRIBER' => ['rules' => v::numeric(), 'messages' => ['numeric' => 'Значение должно быть числом']],
            'INTERVAL_NUMBER' => ['rules' => v::numeric(), 'messages' => ['numeric' => 'Значение должно быть числом']],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.settings.index'));
        }

        $array = $request->getParsedBody();
        $array['SHOW_EMAIL'] = isset($array['SHOW_EMAIL']) && $array['SHOW_EMAIL'] == 'on' ? 1 : 0;
        $array['REQUIRE_SUB_CONFIRMATION'] = isset($array['REQUIRE_SUB_CONFIRMATION']) && $array['REQUIRE_SUB_CONFIRMATION'] == 'on' ? 1 : 0;
        $array['SHOW_UNSUBSCRIBE_LINK'] = isset($array['SHOW_UNSUBSCRIBE_LINK']) && $array['SHOW_UNSUBSCRIBE_LINK'] == 'on' ? 1 : 0;
        $array['REQUEST_REPLY'] = isset($array['REQUEST_REPLY']) && $array['REQUEST_REPLY'] == 'on' ? 1 : 0;
        $array['NEW_SUBSCRIBER_NOTIFY'] = isset($array['NEW_SUBSCRIBER_NOTIFY']) && $array['NEW_SUBSCRIBER_NOTIFY'] == 'on' ? 1 : 0;
        $array['RANDOM_SEND'] = isset($array['RANDOM_SEND']) && $array['RANDOM_SEND'] == 'on' ? 1 : 0;
        $array['RENDOM_REPLACEMENT_SUBJECT'] = isset($array['RENDOM_REPLACEMENT_SUBJECT']) && $array['RENDOM_REPLACEMENT_SUBJECT'] == 'on' ? 1 : 0;
        $array['RANDOM_REPLACEMENT_BODY'] = isset($array['RANDOM_REPLACEMENT_BODY']) && $array['RANDOM_REPLACEMENT_BODY'] == 'on' ? 1 : 0;
        $array['ADD_DKIM'] = isset($array['ADD_DKIM']) && $array['ADD_DKIM'] == 'on' ? 1 : 0;
        $array['LIMIT_SEND'] = isset($array['LIMIT_SEND']) && $array['LIMIT_SEND'] == 'on' ? 1 : 0;
        $array['REMOVE_SUBSCRIBER'] = isset($array['REMOVE_SUBSCRIBER']) && $array['REMOVE_SUBSCRIBER'] == 'on' ? 1 : 0;

        foreach ($array as $key => $value) {
            $this->setValue($key, $value);
        }

        if (count($request->getParam('header_name'))) {

            Customheaders::truncate();

            for ($i = 0; $i < count($request->getParam('header_name')); $i++) {
                $name = $request->getParam('header_name');
                $value = $request->getParam('header_value');
                $name[$i] = trim($name[$i]);
                $value[$i] = trim($value[$i]);

                if (preg_match("/^[\-a-zA-Z]+$/", $name[$i])) {
                    $value[$i] = str_replace(';', '', $value[$i]);
                    $value[$i] = str_replace(':', '', $value[$i]);
                    if ($name[$i] && $value[$i]) {
                        $fields = array(
                            'name' => $name[$i],
                            'value'  => $value[$i]
                        );

                        Customheaders::create($fields);
                    }
                }
            }
        } else {
            Customheaders::truncate();
        }

        $this->flash->addMessage('success', 'Данные успешно обновлены');

        return $response->withRedirect($this->router->pathFor('admin.settings.index'));
    }

    /**
     * @param $key
     * @param $value
     */
    private function setValue($key, $value)
    {
        $setting = Settings::where('name', $key)->first();

        if ($setting) {
            $setting->value = $value;
            $setting->save();
        }
    }
}