<?php

namespace App\Controllers\Dashboard;

use App\Models\Users;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class UsersController extends Controller
{
    /**
     * @param Resquest $request
     * @param Response $response
     * @return mixed
     */
    public function index($request,$response)
    {
        $title = "Пользователи";

        return $this->view->render($response, 'dashboard/users/index.twig', compact('title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function create($request, $response)
    {
        $title = "Добавление пользователя";

        return $this->view->render($response, 'dashboard/users/create_edit.twig', compact('title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function store($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'login' => ['rules' => v::notEmpty()->alnum(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения', 'alnum' => 'Логин должен содержать только латинские буквы и цифры']],
            'name' => ['rules' => v::notEmpty()->stringType(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
            'role' => ['rules' => v::notEmpty()->stringType(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
            'password' => ['rules' => v::notEmpty()->noWhitespace()->length(6)->matchesPassword(password_hash($request->getParam('password_again'),PASSWORD_DEFAULT)),
                'messages' => [
                    'notEmpty' => 'Это поле обязательно для заполнения!',
                    'noWhitespace' => 'Это поле не должно сожержать пробелы!',
                    'length' => 'Пароль должен содержать не меньше {{minValue}} символов!',
                    'matchesPassword' => 'Пароли не совпадают',
                ]],
            'password_again' => ['rules' => v::notEmpty()->noWhitespace()->length(6),
                'messages' => [
                    'notEmpty' => 'Это поле обязательно для заполнения',
                    'noWhitespace' => 'Это поле не должно сожержать пробелы!',
                    'length' => 'Пароль должен содержать не меньше {{minValue}} символов!',
                ]],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.users.create'));
        }

        if (Users::where('login', 'like', $request->getParam('login'))->first()) {
            $_SESSION['errors'] = ['login' => ['unique' => 'Пользоваитель с таким логином уже есть в базе данных']];;

            return $response->withRedirect($this->router->pathFor('admin.users.create'));
        }

        Users::create($request->getParsedBody());

        $this->flash->addMessage('success', 'Данные успешно добавлены');

        return $response->withRedirect($this->router->pathFor('admin.users.index'));
    }

    /**
     * @param $request
     * @param $response
     * @param $id
     * @return mixed
     */
    public function edit($request, $response, $id)
    {
        $title = "Редактирование пользователя";

        $user = Users::select('id', 'name', 'description', 'role', 'login')->find($id)->first();

        if (!$user) return $this->view->render($response, 'errors/404.twig');

        return $this->view->render($response, 'dashboard/users/create_edit.twig', compact('title', 'user'));
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
            'login' => ['rules' => v::notEmpty()->alnum(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения', 'alnum' => 'Логин должен содержать только латинские буквы и цифры']],
            'name' => ['rules' => v::notEmpty()->stringType(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
            'role' => ['rules' => v::notEmpty()->stringType(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
            'password' => ['rules' => v::optional(v::noWhitespace()->length(6)->matchesPassword(password_hash($request->getParam('password_again'),PASSWORD_DEFAULT))),
                'messages' => [
                    'noWhitespace' => 'Это поле не должно сожержать пробелы!',
                    'length' => 'Пароль должен содержать не меньше {{minValue}} символов!',
                    'matchesPassword' => 'Пароли не совпадают',
                ]],
            'password_again' => ['rules' => v::optional(v::noWhitespace()->length(6)),
                'messages' => [
                    'noWhitespace' => 'Это поле не должно сожержать пробелы!',
                    'length' => 'Пароль должен содержать не меньше {{minValue}} символов!',
                    'matchesPassword' => 'Пароли не совпадают',
                ]],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.users.edit', ['id' => $request->getParam('id')]));
        }

        if (Users::where('login', 'like', $request->getParam('login'))->where('id', '!=', $request->getParam('id'))->first()) {
            $_SESSION['errors'] = ['login' => ['unique' => 'Пользоваитель с таким логином уже есть в базе данных']];;

            return $response->withRedirect($this->router->pathFor('admin.users.edit', ['id' => $request->getParam('id')]));
        }

        $data = [
            'name' => $request->getParam('name'),
            'description' => $request->getParam('name'),
            'login' => $request->getParam('name'),
            'role' => $request->getParam('role'),
        ];

        if (!empty($request->getParam('password')) && !empty($request->getParam('password_again'))) {
            $data['password'] = password_hash($request->getParam('password'),PASSWORD_DEFAULT);
        }

        Users::where('id', $request->getParam('id'))->update($data);

        $this->flash->addMessage('success', 'Данные успешно обновлены');

        return $response->withRedirect($this->router->pathFor('admin.users.index'));
    }

    /**
     * @param $request
     * @param $response
     * @param $id
     * @return mixed
     */
    public function destroy($request, $response, $id)
    {
        if ($this->auth->user()->id == $id) return $this->view->render($response, 'errors/500.twig');

        Users::where('id', $id)->delete();
    }

}