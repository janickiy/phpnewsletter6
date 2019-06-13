<?php

namespace App\Controllers\Dashboard;

use App\Models\Users;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use Psr\Http\Message\RequestInterface as Resquest;
use Psr\Http\Message\ResponseInterface as Response;

class UsersController extends Controller
{
   public function index(Resquest $request, Response $response)
   {
       $title = "Пользователи";

       return $this->view->render($response,'dashboard/users/index.twig', compact('title'));
   }

   public function create($request,$response)
   {

   }

   public function store()
   {

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