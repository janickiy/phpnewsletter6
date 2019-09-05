<?php

namespace App\Controllers\Dashboard;

use App\Controllers\Controller;
use App\Models\ReadySent;

class LogController extends Controller
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function index($request,$response)
   {
       $title =  "Журнал рассылки";

       return $this->view->render($response,'dashboard/log/index.twig',compact('title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function clear($request,$response)
   {
       ReadySent::truncate();

       $this->flash->addMessage('success', 'Журнал очищен');

       return $response->withRedirect($this->router->pathFor('admin.log.index'));
   }

   public function download($request,$response)
   {

   }

    /**
     * @param $request
     * @param $response
     * @param $parametr
     * @return mixed
     */
   public function info($request,$response,$parametr)
   {
       $title = "Журнал рассылки";
       $id = $parametr['id'];

       return $this->view->render($response,'dashboard/log/info.twig',compact('title','id'));
   }
	
}