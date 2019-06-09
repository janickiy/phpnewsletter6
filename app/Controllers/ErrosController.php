<?php

namespace App\Controllers;

class ErrosController extends Controller
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function error400($request,$response)
    {
        return $this->view->render($response,'errors/403.twig',['title' => 'Bad Request']);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function error401($request,$response)
    {
        return $this->view->render($response,'errors/404.twig', ['title' => 'Unauthorized']);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function error404($request,$response)
    {
        return $this->view->render($response,'errors/404.twig', ['title' => 'Not Found']);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function error403($request,$response)
    {
        return $this->view->render($response,'errors/403.twig', ['title' => 'Forbidden']);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function error500($request,$response)
    {
        return $this->view->render($response,'errors/500.twig', ['title' => 'Internal Server Error']);
    }

}