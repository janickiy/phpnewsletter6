<?php

namespace App\Controllers\Dashboard;

use App\Models\Log;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

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