<?php

namespace App\Controllers\Dashboard;

use App\Models\Users;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use Psr\Http\Message\RequestInterface as Resquest;
use Psr\Http\Message\ResponseInterface as Response;

class UsersController extends Controller
{
    /**
     * @param Resquest $request
     * @param Response $response
     * @return mixed
     */
   public function index(Resquest $request, Response $response)
   {
       $title = "Пользователи";

       return $this->view->render($response,'dashboard/users/index.twig', compact('title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function create($request,$response)
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
           'login' => v::notEmpty()->alpha(),
           'name' => v::notEmpty()->stringType(),
           'password' => v::noWhitespace()->notEmpty()->length(6,null)->matchesPassword($request->getParam('password_again')),
           'password_again' => v::noWhitespace()->notEmpty()->length(6,null),
       ]);

       if (!$validation->isValid()) {
           $_SESSION['errors'] = $validation->getErrors();

           return $response->withRedirect($this->router->pathFor('admin.users.create'));
       }

       if (Users::where('login', 'like', $request->getParam('login'))->first()) {
           $_SESSION['errors'] = ['email' => ['unique' => 'Пользоваитель с таким логином уже есть в базе данных']];;

           return $response->withRedirect($this->router->pathFor('admin.users.create'));
       }

       Users::create($request->getParsedBody());

       $this->flash->addMessage('success','Данные успешно добавлены');

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

       $user = Users::where('id',$id)->first();

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
       if ($this->auth->user()->id == $request->getParam('id')) return $this->view->render($response, 'errors/500.twig');
       if (!is_numeric($request->getParam('id'))) return $this->view->render($response, 'errors/500.twig');

       $validation = $this->validator->validate($request, [
           'login' => v::notEmpty()->alpha(),
           'name' => v::notEmpty()->stringType(),
           'role' => v::notEmpty()->stringType(),
           'password' => v::noWhitespace()->notEmpty()->length(6,null)->matchesPassword($request->getParam('password_again')),
           'password_again' => v::noWhitespace()->notEmpty()->length(6,null),
       ]);

       if (!$validation->isValid()) {
           $_SESSION['errors'] = $validation->getErrors();

           return $response->withRedirect($this->router->pathFor('admin.users.create'));
       }

       $data = [
           'name' => $request->getParam('name'),
           'description' => $request->getParam('name'),
           'login' => $request->getParam('name'),
           'role' => $request->getParam('role'),
       ];

       Users::where('id',$request->getParam('id'))->update($data);

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