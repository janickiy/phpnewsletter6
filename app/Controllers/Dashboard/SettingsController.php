<?php

namespace App\Controllers\Dashboard;

use App\Models\Settings;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use Psr\Http\Message\RequestInterface as Resquest;
use Psr\Http\Message\ResponseInterface as Response;

class SettingsController extends Controller
{
   public function index($request,$response)
   {
       return $this->view->render($response,'dashboard/settings/index.twig');
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