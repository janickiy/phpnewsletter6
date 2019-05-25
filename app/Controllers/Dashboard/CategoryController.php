<?php

namespace App\Controllers\Dashboard;

use App\Models\Category;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class CategoryController extends Controller
{
   public function index($request,$response)
   {
       return $this->view->render($response,'dashboard/category/index.twig');
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