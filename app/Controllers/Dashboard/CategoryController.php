<?php

namespace App\Controllers\Dashboard;

use App\Models\Category;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class CategoryController extends Controller
{
   public function index($request,$response)
   {
       return $this->view->render($response,'dashboard/category/index.twig');
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function create($request,$response)
   {
       $title = "Добавление категории";

       return $this->view->render($response,'dashboard/category/create_edit.twig', compact('title'));
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
       ]);

       if (!$validation->isValid()) {

           $_SESSION['errors'] = $validation->getErrors();

           return $response->withRedirect($this->router->pathFor('admin.template.create',array('errors' => ['name' => 12])));
       }

       Category::create($request->getParsedBody());

       $this->flash->addMessage('info','Данные успешно добавлены');

       return $response->withRedirect($this->router->pathFor('admin.category.list'));
   }

   public function edit()
   {

   }

   public function update()
   {

   }

   public function destroy()
   {

   }

	
}