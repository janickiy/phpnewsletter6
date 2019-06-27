<?php

namespace App\Controllers\Dashboard;

use App\Models\{Charset,Settings};
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

        return $this->view->render($response, 'dashboard/settings/index.twig', compact('title','charset'));
    }


    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function update($request, $response)
    {

        $validation = $this->validator->validate($request, [
           //
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.category.create'));
        }

        foreach ($request->getParsedBody() as $key => $value) {
            $this->setValue($key,$value);
        }


        $this->flash->addMessage('success', 'Данные успешно обновлены');

        return $response->withRedirect($this->router->pathFor('admin.settings.index'));
    }

    /**
     * @param $key
     * @param $value
     */
    private function setValue($key,$value)
    {
        $setting = Settings::where('name', $key)->first();

        if ($setting) {
            $setting->value= $value;
            $setting->save();
        }
    }
}