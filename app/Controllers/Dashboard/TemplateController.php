<?php

namespace App\Controllers\Dashboard;

use App\Models\Templates;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class TemplateController extends Controller
{
   public function index($request,$response)
   {
       return $this->view->render($response,'dashboard/template/index.twig');
   }

   public function create()
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