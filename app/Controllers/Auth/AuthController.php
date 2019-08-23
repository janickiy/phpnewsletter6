<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;

class AuthController extends Controller
{
    public function p403($request,$response)
    {
        return $this->view->render($response,'403.twig');
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
	public function getSignOut($request,$response)
	{
		$this->auth->logout();
		return $response->withRedirect($this->router->pathFor('auth.signin'));
	}

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
	public function getSignIn($request,$response)
	{
		return $this->view->render($response,'auth/signin.twig');
	}

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
	public function postSignIn($request, $response)
	{
		$auth = $this->auth->attempt(
			$request->getParam('login'),
			$request->getParam('password')
		);

		if (!$auth) {
			$this->flash->addMessage('error','Could not sign you in with those details');

			return $response->withRedirect($this->router->pathFor('admin.main'));
		}

		return $response->withRedirect($this->router->pathFor('admin.main'));
	}
}