<?php 

namespace App\Controllers\Auth;


use App\Models\Admin;
use App\Models\AdminLogin;

use App\Controllers\Controller;

use Respect\Validation\Validator as v;


class AuthController extends Controller
{

	
	
	public function getLogin($request, $response)
	{	
		if($this->auth->check())
		{
			
			return $response->withRedirect($this->router->pathFor('home'));
		}


		return $this->view->render($response, "modules/auth/login.phtml");
	}

	public function getRegister($request, $response)
	{	
		if($this->auth->check())
		{
			
			return $response->withRedirect($this->router->pathFor('home'));
		}

		return $this->view->render($response, "modules/auth/register.phtml");
	}

	public function getDepartments($request, $response)
	{	

		$db_conx = $this->DbConnect->connect2();

		$faculty = $request->getParam('faculty');

		
		$sql = "SELECT * FROM `department` WHERE faculty_id='$faculty'";

		$result = mysqli_query($db_conx,$sql );

		$total_result = mysqli_num_rows($result);

		$departments = '<option value="">Select Your Department</option>';
		if ($total_result>0) {
			while($row = mysqli_fetch_array($result)){

				$departments .= "<option value='".$row["dept_id"]."'>".$row["dept_name"]."</option>";
			}
		}


		return $departments;
	}

	public function getTeachers($request, $response)
	{	

		$db_conx = $this->DbConnect->connect2();

		$department = $request->getParam('department');

		
		$sql = "SELECT * FROM `teachers` WHERE dept_id='$department'";

		$result = mysqli_query($db_conx,$sql );

		$total_result = mysqli_num_rows($result);

		$teachers = '<option value="">Select Yourself</option>';
		if ($total_result>0) {
			while($row = mysqli_fetch_array($result)){

				$teachers .= "<option value='".$row["teacher_id"]."'>".$row["fname"]." ".$row["lname"]."</option>";
			}
		}


		return $teachers;
	}

	public function getTeacherEmail($request, $response)
	{	

		$db_conx = $this->DbConnect->connect2();

		$teacher = $request->getParam('teacher');

		
		$sql = "SELECT * FROM `teachers` WHERE teacher_id='$teacher'";

		$query=mysqli_query($db_conx, $sql);

		$teacherData = mysqli_fetch_assoc($query);
		// $teacherData['email'] = "mdabumarjan66@gmail.com";
		$data = '';
		if ($teacherData) {
			if ($teacherData['email']=='noemail') {
				$data = "noemail";
			} else{

				$parts = explode("@", $teacherData['email']);

				$username = $parts[0];
				$username = substr($username, 0, 5);
				$data = $username."******@".$parts[1];
			}

			
		}

		return $data;
	}




	public function postLogin($request, $response)
	{

		$validation = $this->validator->validate($request, [

			'username' => v::noWhitespace()->notEmpty(),
			'password' => v::noWhitespace()->notEmpty(),

		]);

		if ($validation->failed()) {
			
			return $response->withRedirect($this->router->pathFor('auth.login'));
			
		}

		$auth = $this->auth->attemp(

			$request->getParam('username'),

			$request->getParam('password')

		);

		if (!$auth) {
			
			return $response->withRedirect($this->router->pathFor('auth.login'));

		}

		//update user data to database
		$db_conx = $this->DbConnect->connect();
		
		$userid = $_SESSION['user'];

		$sql = "UPDATE admin_user set logdate=now() where id=$userid ";
		$query=mysqli_query($db_conx, $sql) or die("ERROR");

		return $response->withRedirect($this->router->pathFor('home'));

	}

	public function getLogout($request, $response)
	{

		unset($_SESSION['user']);
		unset($_SESSION['admin_role']);

		$user = AdminLogin::find($_SESSION['last_login']);

		$thisTime = (new \DateTime())->format('Y-m-d H:i:s');

		$user->logout_time = $thisTime;

		$user->save();


		unset($_SESSION['last_login']);

		

		return $response->withRedirect($this->router->pathFor('auth.login'));

	}


	//--------------checkpass-------------------

	public function checkpassAction($request, $response)
	{
		$user = Admin::find($_SESSION['user']);

		if (password_verify($request->getParam('pass'), $user->password)) {
			return 'valid';
		}

		return 'invalid';
	}


	//register post

	public function registerPost($request, $response)
	{
		$teacher = $request->getParam('teacher');

		if ($teacher) {
			$email = $request->getParam('email');

			$db_conx = $this->DbConnect->connect2();
			
			$sql = "SELECT * FROM `teachers` WHERE teacher_id='$teacher' AND email='$email'";

			$result = mysqli_query($db_conx,$sql );

			$total_result = mysqli_num_rows($result);

			if ($total_result==0) {
				//not found
				return "teacher not found!";
			} else {
				//found
				$row = mysqli_fetch_assoc($result);
				$alreadyUser = Admin::where('email', $email)->first();
				if ($alreadyUser) {
					//already registered
					return "already registered user!";
				}
				$x= rand(100000, 1000000);
				// password_hash($request->getParam('password'),PASSWORD_DEFAULT)

				//get faculty of the teacher
				$department = $row['dept_id'];
				$sql = "SELECT * FROM `department` WHERE `dept_id`='$department' ";
				$result = mysqli_query($db_conx,$sql );
				$dept = mysqli_fetch_assoc($result);
				$faculty = $dept['faculty_id'];

				Admin::create([
					'teacher_id' 	=> $row['teacher_id'],
					'name'      	=> $row['fname']." ".$row['lname'],
					'email'        	=> $row['email'],
					'password_hash'	=> md5($x), 
					'type'   => 	'Academic',
					'department'    => $row['dept_id'],
					'faculty'       => $faculty,

				]);

				//email here
				$_SESSION['success'] = 'success';
				$_SESSION['msg'] = "Successfully created user account. Link to complete your profile has been sent to your email address.";
				return $this->view->render($response, "modules/auth/confirmation.phtml");
			}

		}
	}

	public function completeProfile($request, $response, $args)
	{	
		if($this->auth->check())
		{
			$_SESSION['error'] = 'error';
			$_SESSION['msg'] = "You are logged in.";
			return $response->withRedirect($this->router->pathFor('home'));
		}

		$user = Admin::where('password_hash', $args['id'])->first();

		if ($user->active==1) {
			$_SESSION['error'] = "error";
			$_SESSION['msg'] = "Already completed profile! Please Login.";
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if (!$user) {
			$_SESSION['error'] = "error";
			$_SESSION['msg'] = "Invalid request";
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$this->container->view->addAttribute('userId', $user->id);
		$this->container->view->addAttribute('user', $user);

		return $this->view->render($response, "modules/auth/completeProfile.phtml");
	}

	public function completeProfilePost($request, $response, $args)
	{	
		// if($this->auth->check())
		// {
			
		// 	return $response->withRedirect($this->router->pathFor('home'));
		// }

		$userId = $args['id'];

		$user = Admin::find($userId);
		if ($user) {

			if ($user->active==1) {
				$_SESSION['error'] = "error";
				$_SESSION['msg'] = "Already completed profile! Please Login.";
				return $response->withRedirect($this->router->pathFor('auth.login'));
			}
			
			$validation = $this->validator->validate($request, [

				'username' => v::noWhitespace()->notEmpty(),
				'password' => v::noWhitespace()->notEmpty(),
				'password2' => v::noWhitespace()->notEmpty(),

			]);

			if ($validation->failed()) {

				return $response->withRedirect('/complete-profile/'.$user->password_hash);
			}

			$username = $request->getParam('username');
			$alreadyUsed = Admin::where('username', $username)->first();
			if ($alreadyUsed) {
				$_SESSION['error'] = "error";
				$_SESSION['msg'] = "Username already used by someone! Please try a different username.";
				return $response->withRedirect('/complete-profile/'.$user->password_hash);
			}

			$password = $request->getParam('password');
			$password2 = $request->getParam('password2');
			if ($password != $password2) {
				$_SESSION['error'] = "error";
				$_SESSION['msg'] = "Passwords do not match!";
				return $response->withRedirect('/complete-profile/'.$user->password_hash);
			}

			$user->username = $username;
			$user->password = password_hash($request->getParam('password'),PASSWORD_DEFAULT);
			$user->active = 1;
			$user->updated_at = date('Y-m-d H:i:s');
			$user->save();

			$_SESSION['success'] = "success";
			$_SESSION['msg'] = "Successfully completed profile! Login now.";
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$_SESSION['error'] = "error";
		$_SESSION['msg'] = "Invalid request";
		return $response->withRedirect($this->router->pathFor('auth.login'));
	}

	


}