<?php

namespace App\Controllers\Dashboard;

use App\Models\{Templates,Attach};
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use App\Models\Category;
use Slim\Http\UploadedFile;
use App\Helper\StringHelpers;

class TemplateController extends Controller
{
    public function index($request, $response)
    {
        return $this->view->render($response, 'dashboard/template/index.twig');
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function create($request, $response)
    {
        $title = "Добавление шаблона";

        $category = Category::get();

        return $this->view->render($response, 'dashboard/template/create_edit.twig', compact('category', 'title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     * @throws \Exception
     */
    public function store($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'name' => ['rules' => v::stringType()->length(1, 255)->notEmpty(), 'messages' => ['length' => 'Название должно быть от {{minValue}} до {{maxValue}} символов', 'notEmpty' => 'Это поле обязательно для заполнения']],
            'body' => ['rules' => v::stringType()->notEmpty(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
            'prior' => ['rules' => v::numeric()->notEmpty(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
            'categoryId' => ['rules' => v::numeric()->notEmpty(), 'messages' => ['notEmpty' => 'Не указана категория подписчиков', 'numeric' => 'Категория подписчиков указана не верно']],
        ]);

        if (!$validation->isValid()) {

            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.template.create', array('errors' => ['name' => 12])));
        }

        $id = Templates::create($request->getParsedBody())->id;

        $directory = $this->upload_directory;
        $uploadedFiles = $request->getUploadedFiles();

        // handle single input with multiple file uploads
        foreach ($uploadedFiles['attachfile'] as $uploadedFile) {
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile($directory, $uploadedFile);

                $attach = [
                    'name' => $filename,
                    'templateId' => $id
                ];

                Attach::create($attach);

                //  $response->write('uploaded ' . $filename . '<br/>');
            }
        }

        $this->flash->addMessage('success', 'Данные успешно добавлены');

        return $response->withRedirect($this->router->pathFor('admin.main'));
    }

    /**
     * @param $id
     * @param $request
     * @param $response
     * @return mixed
     */
    public function edit($request, $response, $id)
    {
        $title = "Редактирование шаблона";
        $template = Templates::where('id', $id)->first();

        if (!$template) return $this->view->render($response, 'errors/404.twig');

        $category = Category::get();

        return $this->view->render($response, 'dashboard/template/create_edit.twig', compact('category', 'template', 'title'));
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
            'body' => ['rules' => v::stringType()->notEmpty(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
            'prior' => ['rules' => v::numeric()->notEmpty(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
            'categoryId' => ['rules' => v::numeric()->notEmpty(), 'messages' => ['notEmpty' => 'Не указана категория подписчиков', 'numeric' => 'Категория подписчиков указана не верно']],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.template.create', array('errors' => ['name' => 12])));
        }

        $data['name'] = $request->getParam('name');
        $data['body'] = $request->getParam('body');
        $data['prior'] = $request->getParam('prior');
        $data['categoryId'] = $request->getParam('categoryId');

        Templates::where('id', $request->getParam('id'))->update($data);

        $this->flash->addMessage('success', 'Данные успешно обновлены');

        return $response->withRedirect($this->router->pathFor('admin.main'));
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
        Templates::where('id', $id)->delete();
    }

    /**
     * @param $directory
     * @param UploadedFile $uploadedFile
     * @return string
     * @throws \Exception
     */
    public function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    public function addAttach()
    {

    }
}