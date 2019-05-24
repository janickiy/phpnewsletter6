<?php

namespace App\Controllers\Auth;

use App\Models\Users;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use Nette\Mail\Message;

class TemplateController extends Controller
{
   public function list($request,$response)
   {
       return $this->view->render($response,'dashboard/list.twig');
   }


	
}