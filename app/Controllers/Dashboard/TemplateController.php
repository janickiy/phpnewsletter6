<?php

namespace App\Controllers\Dashboard;

use App\Models\Templates;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use App\Models\Category;
use Slim\Http\UploadedFile;


class TemplateController extends Controller
{
   public function index($request,$response)
   {
       return $this->view->render($response,'dashboard/template/index.twig');
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function create($request,$response)
   {
       $title = "Добавление шаблона";

       $category = Category::get();

       return $this->view->render($response,'dashboard/template/create_edit.twig', compact('category','title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function store($request,$response)
   {
       $validation = $this->validator->validate($request,[
           'name' => ['rules' => v::stringType()->length(1, 255)->notEmpty(),'messages' => ['length' => 'Название должно быть от {{minValue}} до {{maxValue}} символов','notEmpty' => 'Это поле обязательно для заполнения']],
           'body' => ['rules' => v::stringType()->notEmpty(),'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
           'prior' => ['rules' => v::numeric()->notEmpty(),'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
           'categoryId' => ['rules' => v::numeric()->notEmpty(),'messages' => ['notEmpty' => 'Не указана категория подписчиков', 'numeric' => 'Категория подписчиков указана не верно']],
       ]);

       if (!$validation->isValid()) {

           $_SESSION['errors'] = $validation->getErrors();

           return $response->withRedirect($this->router->pathFor('admin.template.create',array('errors' => ['name' => 12])));
       }

       Templates::create($request->getParsedBody());

       $directory = $this->upload_directory;
       $uploadedFiles = $request->getUploadedFiles();

       // handle single input with multiple file uploads
       foreach ($uploadedFiles['attachfile'] as $uploadedFile) {
           if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
               $filename = $this->moveUploadedFile($directory, $uploadedFile);
               //  $response->write('uploaded ' . $filename . '<br/>');
           }
       }

       $this->flash->addMessage('success','Данные успешно добавлены');

       return $response->withRedirect($this->router->pathFor('admin.main'));
   }

    /**
     * @param $id
     * @param $request
     * @param $response
     * @return mixed
     */
   public function edit($id,$request,$response)
   {
       $title = "Редактирование шаблона";

       $template = Templates::find($id);

       if (!$template) return $this->view->render($response,'404.twig');

       $category = Category::get();

       return $this->view->render($response,'dashboard/template/create_edit.twig', compact('category','template','title'));
   }

   public function update()
   {

   }

   public function destroy()
   {

   }

    public function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

	
}