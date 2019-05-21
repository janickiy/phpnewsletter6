<?php

namespace App\Middleware;

/**
*
*/
class PermissionMiddleware extends Middleware
{

    protected $permissions;

    public function __construct($container,$permissions)
    {
         $this->permissions = $permissions;
         parent::__construct($container);
    }

	public function __invoke($request,$response,$next)
	{
		if (!$this->container->auth->check()) {
			$this->container->flash->addMessage('error','Please sign in before doing that');
			return $response->withRedirect($this->container->router->pathFor('auth.signin'));
		}

		$permission = false;

        $user = $this->container->auth->user();

        if ($this->hasPermission($user->role)) $permission = true;
        if ($user->role == 'admin') $permission = true;

        if ($permission === false) return $response->withRedirect($this->container->router->pathFor('403'));

        $response = $next($request,$response);
		return $response;

	}

    /**
     * @param $role
     * @param string $permissions
     * @return bool
     */
    protected function hasPermission($role)
    {
        $arr = explode('|', $this->permissions);

        foreach ($arr as $key => $val) {
            $arr[$key] = trim($val);
        }

        if (in_array($role, $arr))
            return true;
        else
            return false;
    }
}