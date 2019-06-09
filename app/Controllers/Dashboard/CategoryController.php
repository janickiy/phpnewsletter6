<?php

namespace App\Controllers\Dashboard;

use App\Models\Category;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use Psr\Http\Message\RequestInterface as Resquest;
use Psr\Http\Message\ResponseInterface as Response;

class CategoryController extends Controller
{
   public function index($request,$response)
   {
       $title = "Категория подписчиков";

       return $this->view->render($response,'dashboard/category/index.twig', compact('title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function create(Resquest $request, Response $response)
   {
       $title = "Добавление категории";

       return $this->view->render($response,'dashboard/category/create_edit.twig', compact('title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function store(Resquest $request, Response $response)
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

           return $response->withRedirect($this->router->pathFor('admin.template.edit',['id' => $request->getParam('id')]));
       }

       $data['name'] = $request->getParam('name');

       Category::where('id', $request->getParam('id'))->update($data);

       $this->flash->addMessage('success', 'Данные успешно обновлены');

       return $response->withRedirect($this->router->pathFor('admin.category'));
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