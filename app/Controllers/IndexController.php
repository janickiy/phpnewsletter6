<?php

namespace App\Controllers;

use App\Models\User;

class IndexController extends Controller
{
	public function index($request,$response)
	{
		return $this->view->render($response,'home.twig');
	}

    public function p403($request,$response)
    {
        return $this->view->render($response,'403.twig');
    }
}