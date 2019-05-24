<?php

namespace App\Auth;

use App\Models\Users;
/**
*
*/
class Auth
{
	public function user()
	{
		return Users::find(isset($_SESSION['user']) ? $_SESSION['user'] : 0);
	}

	public function check()
	{
		return isset($_SESSION['user']);
	}

	public function attempt($login,$password)
	{
		$user = Users::where('login',$login)->first();

		if (!$user) {
			return false;
		}
		
		if (password_verify($password,$user->password)) {
			$_SESSION['user'] = $user->id;
			return true;
		}

		return false;
	}

	public function logout()
	{
		unset($_SESSION['user']);
	}
}