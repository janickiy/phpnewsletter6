<?php

namespace App\Controllers\Dashboard;

use App\Models\Settings;
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

        return $this->view->render($response, 'dashboard/settings/index.twig', compact('title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function create($request, $response)
    {
        $title = 'Добавление параметра';

        return $this->view->render($response, 'dashboard/settings/create_edit.twig', compact('title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function store($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'name' => ['rules' => v::stringType()->notEmpty()->length(1, 255), 'messages' => ['length' => 'Ключ должно быть от {{minValue}} до {{maxValue}} символов', 'notEmpty' => 'Это поле обязательно для заполнения']],
            'value' => ['rules' => v::stringType()->notEmpty()->length(1, 255), 'messages' => ['length' => 'Значение должно быть от {{minValue}} до {{maxValue}} символов', 'notEmpty' => 'Это поле обязательно для заполнения']],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();
            $_SESSION['post'] = $request->getParsedBody();

            return $response->withRedirect($this->router->pathFor('admin.category.create'));
        }

        Settings::create($request->getParsedBody());

        if (isset($_SESSION['post'])) unset($_SESSION['post']);
        $this->flash->addMessage('success', 'Данные успешно добавлены');

        return $response->withRedirect($this->router->pathFor('admin.settings.index'));
    }

    /**
     * @param $request
     * @param $response
     * @param $id
     * @return mixed
     */
    public function edit($request, $response, $id)
    {
        $title = "Редактирование параметра";
        $settings = Settings::where('id', $id)->first();

        if (!$settings) return $this->view->render($response, 'errors/404.twig');

        return $this->view->render($response, 'dashboard/settings/create_edit.twig', compact('settings', 'title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function update($request, $response)
    {
        if (!is_numeric($request->getParam('id'))) return $this->view->render($response, 'errors/500.twig');

        $validation = $this->validator->validate($request, [
            'name' => ['rules' => v::stringType()->notEmpty()->length(1, 255), 'messages' => ['length' => 'Ключ должно быть от {{minValue}} до {{maxValue}} символов', 'notEmpty' => 'Это поле обязательно для заполнения']],
            'value' => ['rules' => v::stringType()->notEmpty()->length(1, 255), 'messages' => ['length' => 'Значение должно быть от {{minValue}} до {{maxValue}} символов', 'notEmpty' => 'Это поле обязательно для заполнения']],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.category.create'));
        }

        $data['name'] = $request->getParam('name');
        $data['description'] = $request->getParam('description');
        $data['value'] = $request->getParam('value');

        Settings::where('id', $request->getParam('id'))->update($data);

        $this->flash->addMessage('success', 'Данные успешно обновлены');

        return $response->withRedirect($this->router->pathFor('admin.settings'));
    }

    /**
     * @param $request
     * @param $response
     * @param $id
     */
    public function destroy($request, $response, $id)
    {
        Settings::where('id', $id)->delete();
    }
}