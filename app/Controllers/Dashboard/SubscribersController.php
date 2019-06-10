<?php

namespace App\Controllers\Dashboard;

use App\Models\{Subscribers,Subscriptions};
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class SubscribersController extends Controller
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function index($request,$response)
   {
       $title = "Подписчики";

       return $this->view->render($response,'dashboard/subscribers/index.twig', compact('title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function create($request,$response)
   {
       $title = "Добавление подписчика";

       return $this->view->render($response,'dashboard/subscribers/create_edit.twig', compact('title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function store($request,$response)
   {
       $validation = $this->validator->validate($request,[
           'name' => ['rules' => v::stringType()->notEmpty()->length(1, 255),'messages' => ['length' => 'Название должно быть от {{minValue}} до {{maxValue}} символов','notEmpty' => 'Это поле обязательно для заполнения']],
           'email' => ['rules' => v::email()->notEmpty(),'messages' => ['email' => 'Адрес электроной почты указан не верно','notEmpty' => 'Это поле обязательно для заполнения']],
       ]);

       if (!$validation->isValid()) {
           $_SESSION['errors'] = $validation->getErrors();
           $_SESSION['post'] = $request->getParsedBody();

           return $response->withRedirect($this->router->pathFor('admin.subscribers.create'));
       }

       Subscribers::create($request->getParsedBody());

       if (isset($_SESSION['post'])) unset($_SESSION['post']);
       $this->flash->addMessage('success','Данные успешно добавлены');

       return $response->withRedirect($this->router->pathFor('admin.subscribers.index'));
   }

    /**
     * @param $request
     * @param $response
     * @param $id
     * @return mixed
     */
   public function edit($request, $response, $id)
   {
       $title = "Редактирование подписчика";
       $subscriber = Subscribers::where('id', $id)->first();

       if (!$subscriber) return $this->view->render($response, 'errors/404.twig');

       return $this->view->render($response, 'dashboard/category/create_edit.twig', compact('subscriber', 'title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function update($request, $response)
   {
       $validation = $this->validator->validate($request,[
           'name' => ['rules' => v::stringType()->notEmpty()->length(1, 255),'messages' => ['length' => 'Название должно быть от {{minValue}} до {{maxValue}} символов','notEmpty' => 'Это поле обязательно для заполнения']],
           'email' => ['rules' => v::email()->notEmpty(),'messages' => ['email' => 'Адрес электроной почты указан не верно','notEmpty' => 'Это поле обязательно для заполнения']],
       ]);

       if (!$validation->isValid()) {
           $_SESSION['errors'] = $validation->getErrors();
           $_SESSION['post'] = $request->getParsedBody();

           return $response->withRedirect($this->router->pathFor('admin.subscribers.create'));
       }

       $data['name'] = $request->getParam('name');
       $data['email'] = $request->getParam('email');

       Subscribers::where('id', $request->getParam('id'))->update($data);

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
       Subscribers::where('id', $id)->delete();
       Subscriptions::where('subscriberId', $id)->delete();
   }

   public function import()
   {

   }

   public function export()
   {

   }

	
}