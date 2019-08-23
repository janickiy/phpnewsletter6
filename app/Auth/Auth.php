<?php

namespace App\Auth;

use App\Models\{Users,Sessions};
use App\Helper\StringHelpers;

class Auth
{
    /**
     * @return mixed
     */
	public function user()
	{
		return Users::find(isset($_SESSION['user']) ? $_SESSION['user'] : 0);
	}

    /**
     * @return bool
     */
	public function check()
	{

        Sessions::where('expiry','<', date("Y-m-d H:i:s"))->delete();

	    if (isset($_COOKIE['token'])) {
	        if (isset($_SESSION['user'])) {
	            return true;
            } else {
                $sessions = Sessions::where('token',$_COOKIE['token'])->first();

                $_SESSION['user'] = $sessions->userId;

                if ($sessions) {
                    return true;
                } else {
                    $this->setCookie('', time() - 3600);
                    unset($_SESSION['user']);
                }
            }
        }

	    return false;
	}

    /**
     * @param $login
     * @param $password
     * @return bool
     */
	public function attempt($login,$password)
	{
        $expiration_date = time() + (3600 * 24 * 365);

		$user = Users::where('login', $login)->first();

		if (!$user) {
			return false;
		}

		if (password_verify($password, $user->password)) {
            $token = StringHelpers::token();
            $this->setCookie($token, $expiration_date);

            $data = [
                'userId' => $user->id,
                'token' => $token,
                'expiry' => date("Y-m-d H:i:s", $expiration_date),
            ];

            Sessions::create($data);

            $_SESSION['user'] = $user->id;

			return true;
		}

		return false;
	}

    /**
     *
     */
	public function logout()
	{
        if (isset($_COOKIE['token'])) {
            Sessions::where('token',$_COOKIE['token'])->delete();
        }

        $this->setCookie('', time() - 3600);

		unset($_SESSION['user']);
	}

    /**
     * @param $value
     * @param $time
     */
	private function setCookie($value = '', $time)
    {
        $domain = $_SERVER['SERVER_NAME'];
        if ((substr($domain, 0, 4)) == "www.") $domain = str_replace('www.','',$domain);

        setcookie ("token", $value, $time, '/', "." . $domain, 0);
    }
}