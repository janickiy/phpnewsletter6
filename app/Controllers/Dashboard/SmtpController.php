<?php

namespace App\Controllers\Dashboard;

use App\Controllers\Controller;
use App\Models\Smtp;
use Respect\Validation\Validator as v;

class SmtpController extends Controller
{
    public function index($request, $response)
    {
        $title = "Список SMTP";

        return $this->view->render($response, 'dashboard/smtp/index.twig', compact('title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function create($request, $response)
    {
        $title = "Добавление SMTP";

        return $this->view->render($response, 'dashboard/smtp/create_edit.twig', compact('title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function store($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'host' => ['rules' => v::stringType()->length(1, 255)->notEmpty(),
                'messages' => [
                    'notEmpty' => 'Это поле обязательно для заполнения!',
                    'length' => 'Название должно быть от {{minValue}} до {{maxValue}} символов',
                ]
            ],
            'password' => ['rules' => v::optional(v::noWhitespace()->length(6)),
                'messages' => [
                    'noWhitespace' => 'Это поле не должно сожержать пробелы!',
                    'length' => 'Пароль должен содержать не меньше {{minValue}} символов!',
                ]],
            'username' => ['rules' => v::notEmpty(),
                'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']
            ],
            'port' => ['rules' => v::notEmpty()->numeric(),
                'messages' => ['notEmpty' => 'Это поле обязательно для заполнения', 'numeric' => 'Значение должно быть числом']
            ],
            'timeout' => ['rules' => v::notEmpty()->numeric(),
                'messages' => ['notEmpty' => 'Это поле обязательно для заполнения', 'numeric' => 'Значение должно быть числом']
            ],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.smtp.create'));
        }

        Smtp::create($request->getParsedBody());

        $this->flash->addMessage('success', 'Данные успешно добавлены');

        return $response->withRedirect($this->router->pathFor('admin.smtp.index'));
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
        $smtp = Smtp::where('id', $id)->first();

        if (!$smtp) return $this->view->render($response, 'errors/404.twig');

        return $this->view->render($response, 'dashboard/smtp/create_edit.twig', compact('smtp', 'title'));
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
            'host' => ['rules' => v::stringType()->length(1, 255)->notEmpty(),
                'messages' => [
                    'notEmpty' => 'Это поле обязательно для заполнения!',
                    'length' => 'Название должно быть от {{minValue}} до {{maxValue}} символов',
                ]
            ],
            'password' => ['rules' => v::optional(v::noWhitespace()->length(6)),
                'messages' => [
                    'noWhitespace' => 'Это поле не должно сожержать пробелы!',
                    'length' => 'Пароль должен содержать не меньше {{minValue}} символов!',
                ]],
            'username' => ['rules' => v::notEmpty(),
                'messages' => ['notEmpty' => 'Это поле обязательно для заполнения'],
            ],
            'port' => ['rules' => v::notEmpty()->numeric(),
                'messages' => ['notEmpty' => 'Это поле обязательно для заполнения', 'numeric' => 'Значение должно быть числом']
            ],
            'timeout' => ['rules' => v::notEmpty()->numeric(),
                'messages' => ['notEmpty' => 'Это поле обязательно для заполнения', 'numeric' => 'Значение должно быть числом']
            ],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.smtp.edit', ['id' => $request->getParam('id')]));
        }

        $data['host'] = $request->getParam('host');
        $data['username'] = $request->getParam('username');
        $data['password'] = $request->getParam('password');
        $data['port'] = $request->getParam('port');
        $data['authentication'] = $request->getParam('authentication');
        $data['secure'] = $request->getParam('secure');
        $data['timeout'] = $request->getParam('timeout');

        Smtp::where('id', $request->getParam('id'))->update($data);

        $this->flash->addMessage('success', 'Данные успешно обновлены');

        return $response->withRedirect($this->router->pathFor('admin.smtp.index'));
    }

    /**
     * @param $request
     * @param $response
     * @param $id
     */
    public function destroy($request, $response, $id)
    {
        Smtp::where('id', $id)->delete();
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function status($request, $response)
    {
        $temp = [];

        foreach ($request->getParam('activate') as $id) {
            if (is_numeric($id)) {
                $temp[] = $id;
            }
        }

        switch ($request->getParam('action')) {
            case  0 :
            case  1 :

                Smtp::whereIN('id', $temp)->update(['active' => $request->getParam('action')]);

                break;

            case 2 :

                Smtp::whereIN('id', $temp)->delete();

                break;
        }

        $this->flash->addMessage('success', 'Действия были выполнены');

        return $response->withRedirect($this->router->pathFor('admin.smtp.index'));
    }
}