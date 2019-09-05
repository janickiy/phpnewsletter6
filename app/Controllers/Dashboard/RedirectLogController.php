<?php

namespace App\Controllers\Dashboard;

use App\Controllers\Controller;
use App\Models\RedirectLog;

class RedirectLogController extends Controller
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function index($request,$response)
   {
       $title =  "Статистика переходов по ссылкам";

       return $this->view->render($response,'dashboard/redirect_log/index.twig',compact('title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function clear($request,$response)
   {
       RedirectLog::truncate();

       $this->flash->addMessage('success', 'Статистика очищина');

       return $response->withRedirect($this->router->pathFor('admin.redirect_log.index'));
   }

   public function download($request,$response)
   {

   }

    public function info($request,$response,$parametr)
    {
        $title =  "Статистика переходов по ссылкам";
    }
	
}