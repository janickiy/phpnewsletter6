<?php

namespace App\Controllers\Dashboard;

use App\Models\{Category};
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use App\Helper\{SettingsHelpers, StringHelpers, SendEmailHelpers};


class CategoryController extends Controller
{
    public function index($request, $response)
    {
        $title = "Категория подписчиков";


        //return $this->view->render($response, 'dashboard/category/index.twig', compact('title'));


        SendEmailHelpers::setBody('rrr');
        SendEmailHelpers::setSubject('dttyu io');
        SendEmailHelpers::setPrior(1);
        SendEmailHelpers::setEmail('janickiy@mail.ru');
        $result_send = SendEmailHelpers::sendEmail();

        var_dump($result_send);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function create($request, $response)
    {
        $title = "Добавление категории";

        return $this->view->render($response, 'dashboard/category/create_edit.twig', compact('title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function store($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'name' => ['rules' => v::stringType()->notEmpty()->length(1, 255), 'messages' => ['length' => 'Название должно быть от {{minValue}} до {{maxValue}} символов', 'notEmpty' => 'Это поле обязательно для заполнения']],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.category.create'));
        }

        if (Category::where('name', 'like', $request->getParam('name'))->first()) {
            $_SESSION['errors'] = ['name' => ['unique' => 'Категория с таким именем уже существует! Укажите другое название']];;

            return $response->withRedirect($this->router->pathFor('admin.category.create'));
        }

        Category::create($request->getParsedBody());

        $this->flash->addMessage('success', 'Данные успешно добавлены');

        return $response->withRedirect($this->router->pathFor('admin.category.index'));
    }

    /**
     * @param $request
     * @param $response
     * @param $id
     * @return mixed
     */
    public function edit($request, $response, $id)
    {
        $title = "Редактирование категории";
        $category = Category::where('id', $id)->first();

        if (!$category) return $this->view->render($response, 'errors/404.twig');

        return $this->view->render($response, 'dashboard/category/create_edit.twig', compact('category', 'title'));
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
            'name' => ['rules' => v::stringType()->length(1, 255)->notEmpty(), 'messages' => ['length' => 'Название должно быть от {{minValue}} до {{maxValue}} символов', 'notEmpty' => 'Это поле обязательно для заполнения']],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.category.edit', ['id' => $request->getParam('id')]));
        }

        if (Category::where('name', 'like', $request->getParam('name'))->where('id', '!=', $request->getParam('id'))->first()) {
            $_SESSION['errors'] = ['name' => ['unique' => 'Категория с таким именем уже существует! Укажите другое название']];;

            return $response->withRedirect($this->router->pathFor('admin.category.create'));
        }

        $data['name'] = $request->getParam('name');

        Category::where('id', $request->getParam('id'))->update($data);

        $this->flash->addMessage('success', 'Данные успешно обновлены');

        return $response->withRedirect($this->router->pathFor('admin.category.index'));
    }

    /**
     * @param $request
     * @param $response
     * @param $id
     */
    public function destroy($request, $response, $id)
    {
        Category::where('id', $id)->delete();
    }
}