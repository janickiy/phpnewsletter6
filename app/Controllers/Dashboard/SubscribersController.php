<?php

namespace App\Controllers\Dashboard;

use App\Models\Subscribers;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;


class SubscribersController extends Controller
{
   public function index($request,$response)
   {
       return $this->view->render($response,'dashboard/subscribers/index.twig');
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

   public function import()
   {

   }

   public function export()
   {

   }

	
}