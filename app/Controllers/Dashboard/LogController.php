<?php

namespace App\Controllers\Dashboard;

use App\Models\Log;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use Psr\Http\Message\RequestInterface as Resquest;
use Psr\Http\Message\ResponseInterface as Response;

class LogController extends Controller
{
   public function index($request,$response)
   {
       return $this->view->render($response,'dashboard/log/index.twig');
   }

   public function clear()
   {

   }

   public function download()
   {

   }

	
}