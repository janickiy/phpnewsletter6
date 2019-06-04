<?php

namespace App\Controllers\Dashboard;

use App\Models\Templates;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use App\Models\Category;

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
       $category = Category::get();

       return $this->view->render($response,'dashboard/template/create_edit.twig', compact('category'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function store($request,$response)
   {
       $validation = $this->validator->validate($request,[
           'name' => v::stringType()->notEmpty()->length(1, 255),
           'body' => v::stringType()->notEmpty(),
           'prior' => v::numeric()->notEmpty(),
           'categoryId' => v::numeric()->notEmpty(),
       ]);

       if ($validation->failed()) {
           return $response->withRedirect($this->router->pathFor('admin.template.create'));
       }

       $template = Templates::create([
           'name' => $request->getParam('name'),
           'body' => $request->getParam('body'),
           'prior' => $request->getParam('prior'),
           'categoryId' => $request->getParam('categoryId'),
       ]);

       $this->flash->addMessage('info','Данные успешно добавлены');

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
       $template = Templates::find($id);

       if (!$template) return $this->view->render($response,'404.twig');

       $category = Category::get();

       return $this->view->render($response,'dashboard/template/create_edit.twig', compact('category','template'));
   }

   public function update()
   {

   }

   public function destroy()
   {

   }

	
}