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

   public function create($request,$response)
   {

$template['id'] = 3;
       return $this->view->render($response,'dashboard/template/create_edit.twig',compact('template'));
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