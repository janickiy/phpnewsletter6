<?php

namespace App\Controllers\Dashboard;

use App\Models\{Templates,Attach};
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use Slim\Http\UploadedFile;

class TemplateController extends Controller
{
    public function index($request, $response)
    {
        $title = "Шаблоны";

        return $this->view->render($response, 'dashboard/template/index.twig', compact('title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function create($request, $response)
    {
        $title = "Добавление шаблона";

        return $this->view->render($response, 'dashboard/template/create_edit.twig', compact( 'title'));
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
            'name' => ['rules' => v::stringType()->notEmpty()->length(1, 255), 'messages' => ['length' => 'Название должно быть от {{minValue}} до {{maxValue}} символов', 'notEmpty' => 'Это поле обязательно для заполнения']],
            'body' => ['rules' => v::stringType()->notEmpty(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
            'prior' => ['rules' => v::numeric()->notEmpty(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.template.create'));
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
                    'file_name' => $uploadedFile->getClientFilename(),
                    'templateId' => $id
                ];

                Attach::create($attach);

                //  $response->write('uploaded ' . $filename . '<br/>');
            }
        }

        if (isset($_SESSION['post'])) unset($_SESSION['post']);

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

        $attachment = $template->attach;

        return $this->view->render($response, 'dashboard/template/create_edit.twig', compact('template', 'attachment', 'title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     * @throws \Exception
     */
    public function update($request, $response)
    {
        if (!is_numeric($request->getParam('id'))) return $this->view->render($response, 'errors/500.twig');

        $validation = $this->validator->validate($request, [
            'name' => ['rules' => v::stringType()->notEmpty()->length(1, 255), 'messages' => ['length' => 'Название должно быть от {{minValue}} до {{maxValue}} символов', 'notEmpty' => 'Это поле обязательно для заполнения']],
            'body' => ['rules' => v::stringType()->notEmpty(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
            'prior' => ['rules' => v::numeric()->notEmpty(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.template.edit',['id' => $request->getParam('id')]));
        }

        $directory = $this->upload_directory;
        $uploadedFiles = $request->getUploadedFiles();

        // handle single input with multiple file uploads
        foreach ($uploadedFiles['attachfile'] as $uploadedFile) {

            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile($directory, $uploadedFile);

                $attach = [
                    'name' => $filename,
                    'file_name' => $uploadedFile->getClientFilename(),
                    'templateId' => $request->getParam('id')
                ];

                Attach::create($attach);
            };
        }

        $data['name'] = $request->getParam('name');
        $data['body'] = $request->getParam('body');
        $data['prior'] = $request->getParam('prior');

        Templates::where('id', $request->getParam('id'))->update($data);

        $this->flash->addMessage('success', 'Данные успешно обновлены');

        return $response->withRedirect($this->router->pathFor('admin.main'));
    }

    /**
     * @param $request
     * @param $response
     * @param $id
     */
    public function destroy($request, $response, $id)
    {
        $q = Templates::where('id', $id);

        if ($q->exists()) {
            foreach ($q->first()->attach as $a) {
                if (isset($a->id) && $a->id) Attach::Remove($a->id, $this->upload_directory);
            }

            $q->delete();
        }
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

}