<?php 

namespace App\Controllers;


// use App\Models\Category; //this is important to load the model by this

use App\Models\Admin;
use App\Models\FixedUsers;


use App\Controllers\Controller;

use Respect\Validation\Validator as v;

 use App\Helper\AdminDetails;

//include database connection for SQL
// use App\Database\DbConnect;


class AccountController extends Controller
{

	public function accountAction($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}


			
		$user = Admin::find($_SESSION['user']);
		$this->container->view->addAttribute('Title', "Admin Profile");
		$this->container->view->addAttribute('user', $user);

		return $this->view->render($response, "modules/user/user.phtml");

	}

	public function addUserAction($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Main Admin"){

			$this->container->view->addAttribute('Title', "Add Administrative User");
			return $this->view->render($response, "modules/user/addAdministrativeUser.phtml");
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));

	}

	public function myloginhistoryAction($request, $response)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$user = Admin::find($_SESSION['user']);

		$this->container->view->addAttribute('Title', "My Login History");
		$this->container->view->addAttribute('user', $user);

		return $this->view->render($response, "modules/user/myLoginHistory.phtml");
	}

	public function loginhistoryAction($request, $response, $args)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$id = $args['id'];

		$user = Admin::find($id);

		if ($user) {
			$this->container->view->addAttribute('Title', "Login History - ".$user->name);
			$this->container->view->addAttribute('user', $user);

			return $this->view->render($response, "modules/user/userLoginHistory.phtml");
		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invaid Request!";

		return $response->withRedirect($this->router->pathFor('user.profile'));


		
	}

	public function selfHistoryAction($request, $response)
	{
		
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}


		$db_conx = $this->DbConnect->connect();



		// storing  request (ie, get/post) global array to a variable  
		$requestData= $_REQUEST;

		// echo $requestData['start']."<br>";
		// echo $requestData['length']; die();


		$columns = array( 
		// datatable column index  => database column name
			0 =>'id', 
			1 => 'login_time',
			3 => 'logout_time',
			2 => 'logtime'
		);




		// getting total number records without any search
		$user=$_SESSION['user'];

		$sql = "SELECT id, user, login_time, logout_time, TIMEDIFF(logout_time, login_time) AS logtime ";
		$sql.=" FROM admin_login WHERE user='$user' ";
		$query=mysqli_query($db_conx, $sql) or die("ERROR");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.




		$sql = "SELECT id, user, login_time, logout_time, TIMEDIFF(logout_time, login_time) AS logtime  ";
		$sql.=" FROM admin_login WHERE user=$user ";


		$query=mysqli_query($db_conx, $sql) or die("/public/products/getproducts: get products");
		$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.
			
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query=mysqli_query($db_conx, $sql) or die("/public/products/getproducts: get products");

			


		$data = array();

		$count = 0;
		while( $row=mysqli_fetch_array($query) ) {  // preparing an array

			$nestedData=array(); 
			$nestedData[] =	++$count;
			$nestedData[] = $row["login_time"];
			$nestedData[] = $row["logout_time"];
			
			if ($row['logout_time'] > 0) {
				
				$nestedData[] = $row["logtime"];
			
			} else $nestedData[] = 'Not Available';
		    
		    
			$data[] = $nestedData;
		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data   // total data array
					);

		echo json_encode($json_data);  // send data as json format


	}

	public function userHistoryAction($request, $response, $args)
	{
		
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}


		$id = $args['id'];

		$user = Admin::find($id);

		if ($user) {


			$db_conx = $this->DbConnect->connect();



			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;

			// echo $requestData['start']."<br>";
			// echo $requestData['length']; die();


			$columns = array( 
			// datatable column index  => database column name
				0 =>'id', 
				1 => 'login_time',
				3 => 'logout_time',
				2 => 'logtime'
			);




			// getting total number records without any search
			$user=$id;

			$sql = "SELECT id, user, login_time, logout_time, TIMEDIFF(logout_time, login_time) AS logtime ";
			$sql.=" FROM admin_login WHERE user='$user' ";
			$query=mysqli_query($db_conx, $sql) or die("ERROR");
			$totalData = mysqli_num_rows($query);
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.




			$sql = "SELECT id, user, login_time, logout_time, TIMEDIFF(logout_time, login_time) AS logtime  ";
			$sql.=" FROM admin_login WHERE user=$user ";


			$query=mysqli_query($db_conx, $sql) or die("/public/products/getproducts: get products");
			$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.
				
			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

			$query=mysqli_query($db_conx, $sql) or die("/public/products/getproducts: get products");

				


			$data = array();

			$count = 0;
			while( $row=mysqli_fetch_array($query) ) {  // preparing an array

				$nestedData=array(); 
				$nestedData[] =	++$count;
				$nestedData[] = $row["login_time"];
				$nestedData[] = $row["logout_time"];
				
				if ($row['logout_time'] > 0) {
					
					$nestedData[] = $row["logtime"];
				
				} else $nestedData[] = 'Not Available';
			    
			    
				$data[] = $nestedData;
			}



			$json_data = array(
						"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
						"recordsTotal"    => intval( $totalData ),  // total number of records
						"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
						"data"            => $data   // total data array
						);

			echo json_encode($json_data);  // send data as json format

		}


	}


	public function editAccountAction($request, $response)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}



		$validation = $this->validator->validate($request, [

			'username'	=> v::notEmpty(),
			'mobile'	=> v::phone(),
			'nid'		=> v::numeric(),
			'address'	=> v::stringType(),

		]);

		if ($request->getParam('email')!='') {
				
			$validation = $this->validator->validate($request, [
				'email'		=> v::email(),
			]);

		}

		if ($validation->failed()) {

			$_SESSION['show_modal'] = 'editProfile';

			return $response->withRedirect($this->router->pathFor('user.profile'));
			
		}

		$user = Admin::find($_SESSION['user']);

		$user->username = $request->getParam('username');
		$user->mobile = $request->getParam('mobile');
		$user->email = $request->getParam('email');
		$user->nid = $request->getParam('nid');
		$user->address = $request->getParam('address');

		$user->save();

		$_SESSION['success'] = 'success';
		$_SESSION['msg'] = "Successfully updated account information.";

		return $response->withRedirect($this->router->pathFor('user.profile'));
	}


	public function checkusernameAction($request, $response)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$db_conx = $this->DbConnect->connect();

		$username = $request->getParam('username');

		$sql = "SELECT * FROM `admin_user` WHERE `username`='$username';";
		$result = mysqli_query($db_conx,$sql );
		$total_cat = mysqli_num_rows($result);
		if($total_cat>0){

		return 'taken';

		} else return 'nottaken';


	}


	public function addSecurityQusAction($request, $response)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}



		$validation = $this->validator->validate($request, [

			'question'	=> v::notEmpty()->stringType(),
			'answer'	=> v::notEmpty()->stringType(),
			'password'		=> v::notEmpty(),

		]);

		if ($validation->failed()) {


			$_SESSION['show_modal'] = 'addSecurityQus';

			return $response->withRedirect($this->router->pathFor('user.profile'));
			
		}

		$user = Admin::find($_SESSION['user']);

		if (!password_verify($request->getParam('password'), $user->password)) {
			
			$_SESSION['error'] = 'error';

			$_SESSION['msg'] = "Wrong password! Please try again.";

			return $response->withRedirect($this->router->pathFor('user.profile'));

		}

		AdminSecurity::create([

				'user'      	=> $_SESSION['user'],
				'question'         => $request->getParam('question'),
				'answer'          => $request->getParam('answer'),
				'active'          => '1'

			]);

		$_SESSION['success'] = 'success';
		$_SESSION['msg'] = "Successfully created new security question.";

		return $response->withRedirect($this->router->pathFor('user.profile'));

	}


	public function getquestionAction($request, $response)
	{
		$id = $request->getParam('id');
		$question = AdminSecurity::find($id);

	    $data["question"]	= $question->question;
	    $data["answer"]		= $question->answer;

		return json_encode($data);
	}

	public function editSecurityQusAction($request, $response)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}



		$validation = $this->validator->validate($request, [

			'id'		=> v::notEmpty()->numeric(),
			'question'	=> v::notEmpty()->stringType(),
			'answer'	=> v::notEmpty()->stringType(),
			'password'	=> v::notEmpty(),

		]);

		if ($validation->failed()) {


			$_SESSION['error'] = 'error';

			$_SESSION['msg'] = "Validation Error! Please try again.";

			return $response->withRedirect($this->router->pathFor('user.profile'));
			
		}

		$question = AdminSecurity::find($request->getParam('id'));

		$question->question = $request->getParam('question');

		$question->answer = $request->getParam('answer');

		$question->save();

		$_SESSION['success'] = 'success';
		$_SESSION['msg'] = "Successfully updated the security question.";

		return $response->withRedirect($this->router->pathFor('user.profile'));

	}

	public function changePasswordAction($request, $response)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}



		$validation = $this->validator->validate($request, [

			'changepassword1'	=> v::notEmpty()->stringType(),
			'changepassword2'	=> v::notEmpty()->stringType(),
			'password'	=> v::notEmpty(),

		]);

		if ($validation->failed()) {


			$_SESSION['error'] = 'error';

			$_SESSION['msg'] = "Validation Error! Please try again.";

			return $response->withRedirect($this->router->pathFor('user.profile'));
			
		}

		$user = Admin::find($_SESSION['user']);

		$password1 = $request->getParam('changepassword1');
		$password2 = $request->getParam('changepassword2');

		if (password_verify($request->getParam('password'), $user->password)) {
			
			if ($password1==$password2) {

				$user->password = password_hash($password1, PASSWORD_DEFAULT);

				$user->save();

				$_SESSION['success'] = 'success';
				$_SESSION['msg'] = "Successfully updated your password.";

				return $response->withRedirect($this->router->pathFor('user.profile'));
			
			} else {
				$_SESSION['error'] = 'error';

				$_SESSION['msg'] = "Passwords Do Not Match! Try again.";

				return $response->withRedirect($this->router->pathFor('user.profile'));
			}
		
		} else {
			$_SESSION['error'] = 'error';

			$_SESSION['msg'] = "Invalid Current Password! Try again.";

			return $response->withRedirect($this->router->pathFor('user.profile'));
		}


		

		

		$_SESSION['error'] = 'error';

		$_SESSION['msg'] = "Validations Error! Please try again.";

		return $response->withRedirect($this->router->pathFor('user.profile'));

	}

	public function deleteSecurityAction($request, $response, $args)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$questionid = $args['id'];


		$question = AdminSecurity::find($questionid);

		$question->delete();

		$_SESSION['success'] = 'success';
		$_SESSION['msg'] = "Successfully deleted the security question.";

		return $response->withRedirect($this->router->pathFor('user.profile'));
	}

	public function addRoleAction($request, $response)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		//access control
      	//create role -> resource=6
      	if (AccessControl::getPermission(6)) {

			$this->container->view->addAttribute('Title', "Add New Role");

			return $this->view->render($response, "modules/user/AdminAddRole.phtml");
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));

	}

	public function addRolePostAction($request, $response)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		//access control
      	//create role -> resource=6
      	if (AccessControl::getPermission(6)) {

			$name = $request->getParam('name');
			$description = $request->getParam('description');
			$resources = $request->getParam('resources');

			$role = AdminRole::create([

					'name'      	=> $request->getParam('name'),
					'description'   => $request->getParam('description'),
					'created_by'    => $_SESSION['user'],
					'active'        => '1'

				]);


			foreach ($resources as $resource) {

				if ($resource) {
					
					AdminRolePermission::create([

						'role_id'      	=> $role->id,
						'resource'   	=> $resource,
						'permission'    => 'allow',
						'active'        => '1'

					]);
				}
				
				

			}


			return 'done';

		}

		
	}

	public function getResourcesAction($request, $response)
	{
		
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}


		$data = array();

		$resources = AdminResource::select('id','module', 'label', 'level', 'has_child', 'parent')
            ->groupBy('module')
            ->orderBy('module_pos', 'asc')
	        ->orderBy('id', 'asc')
            ->get();

        // echo $resources;
        foreach ($resources as $resource) {
        	// echo $resource->module;
        	// echo "<br>";

        	$resourceParent = AdminResource::where('module', $resource->module)->where('parent', 0)->first(); //getting parent

        	if ($resourceParent->has_child==1) {
        		
        		$parentData['text'] = $resourceParent->label;
        		$parentData['tag'] = $resourceParent->id;

        		$resourceChild = AdminResource::where('module', $resource->module)->where('parent', $resourceParent->id)->get(); //getting childs

        		$childs = array();

        		foreach ($resourceChild as $child) {

        			if ($child->has_child==1) {
        				
        				$childData['text'] = $child->label;
	        			$childData['tag'] = $child->id;

	        			$resourceGrandCHilds = AdminResource::where('module', $resource->module)->where('parent', $child->id)->get(); //getting grand childs

	        			$grandChilds = array();
	        			
	        			foreach ($resourceGrandCHilds as $grandChild) {
	        				
	        				$grandChildData['text'] = $grandChild->label;
	        				$grandChildData['tag'] = $grandChild->id;

	        				array_push($grandChilds, $grandChildData);
	        			}

	        			$childData['nodes'] = $grandChilds;

        			} else {

        				unset($childData);
        				$childData['text'] = $child->label;
	        			$childData['tag'] = $child->id;


        			}

        			array_push($childs, $childData);
        			
        		}

        		$parentData['nodes'] = $childs;

        	} else {

        		unset($parentData); 
        		$parentData['text'] = $resourceParent->label;
        		$parentData['tag'] = $resourceParent->id;

        	}

        	array_push($data, $parentData);


        }

        $allResources = AdminResource::select('id')->get();

        $allResrc = array();

        foreach ($allResources as $resrc) {
        	array_push($allResrc, $resrc->id);
        }

        $output['treeview'] = $data;
        $output['all'] = $allResrc;

        return json_encode($output);

		
	}

	public function getRoleDetailsAction($request, $response, $args)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$id = $args['id'];

		$role = AdminRole::find($id);

		if ($role) {

			$this->container->view->addAttribute('role', $role);

			$this->container->view->addAttribute('Title', "Role Details");

			return $this->view->render($response, "modules/user/roleDetails.phtml");
		}
	}

	//get role resources for details view

	public function getRoleResourcesAction($request, $response, $args)
	{
		
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$id = $args['id'];

		$role = AdminRole::find($id);

		if ($role) {
			
			$selectedResource = array();

			$roleResources = AdminRolePermission::where('role_id', $role->id)->get();

			foreach ($roleResources as $value) {
				
				array_push($selectedResource, $value->resource);

			}


			$data = array();

			$resources = AdminResource::select('id','module', 'label', 'level', 'has_child', 'parent')
	            ->groupBy('module')
	            ->orderBy('module_pos', 'asc')
	            ->orderBy('id', 'asc')
	            ->get();

	        // echo $resources;
	        foreach ($resources as $resource) {
	        	// echo $resource->module;
	        	// echo "<br>";

	        	$resourceParent = AdminResource::where('module', $resource->module)->where('parent', 0)->first(); //getting parent

	        	if ($resourceParent->has_child==1) {

        			$parentData['text'] = $resourceParent->label;
	        		$parentData['tag'] = $resourceParent->id;

	        		$resourceChild = AdminResource::where('module', $resource->module)->where('parent', $resourceParent->id)->get(); //getting childs

	        		$childs = array();

	        		foreach ($resourceChild as $child) {

	        			if ($child->has_child==1) {
	        				
	        				$childData['text'] = $child->label;
		        			$childData['tag'] = $child->id;

		        			$resourceGrandCHilds = AdminResource::where('module', $resource->module)->where('parent', $child->id)->get(); //getting grand childs

		        			$grandChilds = array();
		        			
		        			foreach ($resourceGrandCHilds as $grandChild) {
		        				
		        				$grandChildData['text'] = $grandChild->label;
		        				$grandChildData['tag'] = $grandChild->id;

		        				if (in_array($grandChild->id, $selectedResource)) {
	        			
				        		$state['checked'] = 'true';
				        		// $state['expanded'] = 'true';
				        		$state['selected'] = 'true';

				        		$grandChildData['state'] = $state;
				        		
				        		}

				        		array_push($grandChilds, $grandChildData);
		        			}

		        			$childData['nodes'] = $grandChilds;

	        			} else {

	        				unset($childData);
	        				$childData['text'] = $child->label;
		        			$childData['tag'] = $child->id;


	        			}

	        			if (in_array($child->id, $selectedResource)) {

	        				$state['checked'] = 'true';
			        		// $state['expanded'] = 'true';
			        		$state['selected'] = 'true';

			        		$childData['state'] = $state;
	        			
		        		
		        		}

		        		array_push($childs, $childData);
	        			
	        		}

	        		if (in_array($resourceParent->id, $selectedResource)) {
	        		
		        		$state['checked'] = 'true';
		        		$state['expanded'] = 'true';
		        		// $state['selected'] = 'true';

		        		$parentData['state'] = $state;
	        			
	        		}

	        		$parentData['nodes'] = $childs;

	        	} else {

	        		unset($parentData); 

	        		if (in_array($resourceParent->id, $selectedResource)) {
	        		
		        		$state['checked'] = 'true';
		        		$state['expanded'] = 'true';
		        		// $state['selected'] = 'true';

		        		$parentData['state'] = $state;
	        			
	        		}

	        		$parentData['text'] = $resourceParent->label;
	        		$parentData['tag'] = $resourceParent->id;

	        	}

	        	// if (in_array($resourceParent->id, $selectedResource)) {
	        		
	        	// 	$state['checked'] = 'true';
	        	// 	$state['expanded'] = 'true';
	        	// 	$state['selected'] = 'true';

	        	// 	$parentData['state'] = $state;
        			
        		// }

        		array_push($data, $parentData);
	        	


	        }

	        $allResources = AdminResource::select('id')->get();

	        $allResrc = array();

	        foreach ($allResources as $resrc) {
	        	array_push($allResrc, $resrc->id);
	        }

	        $output['treeview'] = $data;
	        $output['all'] = $allResrc;
	        $output['inresource'] = $selectedResource;

	        return json_encode($output);


		}
	
	}

	public function editUserAction($request, $response, $args)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

        //access control
        //edit users -> resource=66
        if (AccessControl::getPermission(66)) {

			$id = $args['id'];

			$user = Admin::find($id);

			if ($user) {

				$this->container->view->addAttribute('user', $user);

				$this->container->view->addAttribute('Title', "Edit User Account");

				return $this->view->render($response, "modules/user/editUser.phtml");
			}

		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}


	public function editUserPostAction($request, $response, $args)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$id = $args['id'];

		$user = Admin::find($id);

		if ($user) {

			$validation = $this->validator->validate($request, [

				'role'				=>v::notEmpty()->numeric(),
				'username'			=> v::notEmpty()->stringType(),
				'name'				=> v::notEmpty()->stringType(),
				'description'		=> v::notEmpty()->stringType(),
				'mobile'			=> v::notEmpty()->phone(),
				'nid'				=> v::numeric(),
				'address'			=> v::stringType(),
				'password'			=> v::stringType(),
				'retypepassword'	=> v::stringType(),

			]);

			if ($request->getParam('email')!='') {
				
				$validation = $this->validator->validate($request, [
					'email'		=> v::email(),
				]);

			}

			if ($validation->failed()) {


				$_SESSION['error'] = 'error';
				$_SESSION['msg'] = "Invaid Form Submission!";

				return $response->withRedirect('/user/edituser/'.$id);
				
			}

			if ($request->getParam('password')!=$request->getParam('retypepassword')) {
				
				$_SESSION['error'] = 'error';

				$_SESSION['msg'] = "Password do not match! Please try again.";

				return $response->withRedirect('/user/edituser/'.$id);
			}

			$user->username = $request->getParam('username');
			$user->name = $request->getParam('name');
			$user->description = $request->getParam('description');
			$user->mobile = $request->getParam('mobile');
			$user->email = $request->getParam('email');
			$user->nid = $request->getParam('nid');
			$user->address = $request->getParam('address');
			$user->salary = $request->getParam('salary');

			if ($request->getParam('password')) {
				
				$user->password = password_hash($request->getParam('password'), PASSWORD_DEFAULT);
			}
			
			
			$user->role = $request->getParam('role');
			$user->is_active = $request->getParam('status');

			$user->save();


			$_SESSION['success'] = 'success';
			$_SESSION['msg'] = "Successfully updated user ". $user->name .".";

			return $response->withRedirect($this->router->pathFor('user.profile'));

		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invaid Request!";

		return $response->withRedirect($this->router->pathFor('user.profile'));		

	}


	//edit role get
	public function editRoleResourcesAction($request, $response, $args)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		//access control
	    //edit roles -> resource=67
	    if (AccessControl::getPermission(67)) {

			$id = $args['id'];

			$role = AdminRole::find($id);

			if ($role) {

				$this->container->view->addAttribute('role', $role);

				$this->container->view->addAttribute('Title', "Edit Role");

				return $this->view->render($response, "modules/user/editRole.phtml");
			}

			$_SESSION['error'] = 'error';
			$_SESSION['msg'] = "Invaid Request!";

			return $response->withRedirect($this->router->pathFor('user.profile'));		
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));



	}

	public function getEditRoleResourcesAction($request, $response, $args)
	{
		
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$id = $args['id'];

		$role = AdminRole::find($id);

		if ($role) {
			
			$selectedResource = array();

			$roleResources = AdminRolePermission::where('role_id', $role->id)->get();

			foreach ($roleResources as $value) {
				
				array_push($selectedResource, $value->resource);

			}


			$data = array();

			$resources = AdminResource::select('id','module', 'label', 'level', 'has_child', 'parent')
	            ->groupBy('module')
	            ->orderBy('module_pos', 'asc')
	            ->orderBy('id', 'asc')
	            ->get();

	        // echo $resources;
	        foreach ($resources as $resource) {
	        	// echo $resource->module;
	        	// echo "<br>";

	        	$resourceParent = AdminResource::where('module', $resource->module)->where('parent', 0)->first(); //getting parent

	        	if ($resourceParent->has_child==1) {

        			$parentData['text'] = $resourceParent->label;
	        		$parentData['tag'] = $resourceParent->id;

	        		$resourceChild = AdminResource::where('module', $resource->module)->where('parent', $resourceParent->id)->get(); //getting childs

	        		$childs = array();

	        		foreach ($resourceChild as $child) {

	        			if ($child->has_child==1) {
	        				
	        				$childData['text'] = $child->label;
		        			$childData['tag'] = $child->id;

		        			$resourceGrandCHilds = AdminResource::where('module', $resource->module)->where('parent', $child->id)->get(); //getting grand childs

		        			$grandChilds = array();
		        			
		        			foreach ($resourceGrandCHilds as $grandChild) {
		        				
		        				$grandChildData['text'] = $grandChild->label;
		        				$grandChildData['tag'] = $grandChild->id;

		        				if (in_array($grandChild->id, $selectedResource)) {
	        			
				        		$state['checked'] = 'true';
				        		// $state['expanded'] = 'true';
				        		// $state['selected'] = 'true';

				        		$grandChildData['state'] = $state;
				        		
				        		}

				        		array_push($grandChilds, $grandChildData);
				        		unset($grandChildData);
		        			}

		        			$childData['nodes'] = $grandChilds;

	        			} else {

	        				unset($childData);
	        				$childData['text'] = $child->label;
		        			$childData['tag'] = $child->id;


	        			}

	        			if (in_array($child->id, $selectedResource)) {

	        				$state['checked'] = 'true';
			        		// $state['expanded'] = 'true';
			        		// $state['selected'] = 'true';

			        		$childData['state'] = $state;
	        			
		        		
		        		}

		        		array_push($childs, $childData);
		        		unset($childData);
	        			
	        		}

	        		if (in_array($resourceParent->id, $selectedResource)) {
	        		
		        		$state['checked'] = 'true';
		        		$state['expanded'] = 'true';
		        		// $state['selected'] = 'true';

		        		$parentData['state'] = $state;
	        			
	        		}

	        		$parentData['nodes'] = $childs;

	        	} else {

	        		unset($parentData); 

	        		if (in_array($resourceParent->id, $selectedResource)) {
	        		
		        		$state['checked'] = 'true';
		        		$state['expanded'] = 'true';
		        		// $state['selected'] = 'true';

		        		$parentData['state'] = $state;
	        			
	        		}

	        		$parentData['text'] = $resourceParent->label;
	        		$parentData['tag'] = $resourceParent->id;

	        	}

	        	

        		array_push($data, $parentData);
        		unset($parentData);
	        	

	        }

	        $allResources = AdminResource::select('id')->get();

	        $allResrc = array();

	        foreach ($allResources as $resrc) {
	        	array_push($allResrc, $resrc->id);
	        }

	        $output['treeview'] = $data;
	        $output['all'] = $allResrc;
	        $output['inresource'] = $selectedResource;

	        return json_encode($output);


		}
	
	}


	public function editRolePostAction($request, $response, $args)
	{
		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$id = $args['id'];

		$role = AdminRole::find($id);

		if ($role) {

			//update role

			$role->name = $request->getParam('name');
			$role->description = $request->getParam('description');

			$role->save();
			
			$resources = $request->getParam('resources');

			//
			
			//dele old rows
			$oldroleResources = AdminRolePermission::where('role_id', $role->id)->delete();



			foreach ($resources as $resource) {

				if ($resource) {
					
					AdminRolePermission::create([

						'role_id'      	=> $role->id,
						'resource'   	=> $resource,
						'permission'    => 'allow',
						'active'        => '1'

					]);
				}
				
				

			}


			return 'done';

		}

		
	}

	//view any user
	public function viewUserAction($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$id = $args['id'];

		$user = Admin::find($id);

		if ($user) {
			
			$this->container->view->addAttribute('Title', "User Profile - ".$user->name);
			$this->container->view->addAttribute('user', $user);

			return $this->view->render($response, "modules/user/userProfileView.phtml");

		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invaid Request!";

		return $response->withRedirect($this->router->pathFor('user.profile'));

	}

	public function getEmpInfobyIdAction($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}


		//access control
      	//give loans -> resource=91
	    if (AccessControl::getPermission(91)) {

			$id = $request->getParam('employee');

			$user = Admin::find($id);

			if ($user) {

				$data["id"] = $user->id;
				$data["name"] = $user->name;
				$data["description"] = $user->description;
				$data["role"] = AdminRole::find($user->role)->name;
				$data["salary"] = $user->salary;
				$data["loan"] = $user->loan;
				
				return json_encode($data);

			} else return "Invaid";

		}

	}

	public function addUserPostAction($request, $response)
	{

		$validation = $this->validator->validate($request, [

			'designation' 	=> v::stringType()->notEmpty(),
			'username'		=> v::noWhitespace()->notEmpty(),
			'name' 			=> v::stringType()->notEmpty(),
			'description' 	=> v::stringType(),
			'mobile'		=> v::stringType(),
			'address'		=> v::stringType(),
			'email'			=> v::email(),

		]);

		if ($validation->failed()) {

			return $response->withRedirect($this->router->pathFor('user.add'));
		}

			
		$x= date('YmdHis');
		//get faculty of the teacher

		Admin::create([
			'name'      	=> $request->getParam('name'),
			'email'        	=> $request->getParam('email'),
			'password_hash'	=> md5($x), 
			'type'   => 	'Administrative',
			'description'    => $request->getParam('description'),
			'designation'    => $request->getParam('designation'),
			'mobile'    => $request->getParam('mobile'),
			'address'    => $request->getParam('address'),

		]);

		//email here
		$_SESSION['success'] = 'success';
		$_SESSION['msg'] = "Successfully created user account. Link to complete profile has been sent to user's email address.";
		return $response->withRedirect($this->router->pathFor('users.all'));
		
	}


	public function fixedUsersAction($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Main Admin"){

			$this->container->view->addAttribute('Title', "Fixed Administrative User");
			return $this->view->render($response, "modules/user/fixedUsers.phtml");
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));

	}

	public function setInchargeAction($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Main Admin"){

			$this->container->view->addAttribute('Title', "Dean Office In Charge Setup");
			$this->container->view->addAttribute('id', $args['id']);
			return $this->view->render($response, "modules/user/inchargeSetup.phtml");
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));

	}


	public function setInchargePostAction($request, $response, $args)
	{
		if($_SESSION['type']=="Main Admin"){

			$inchegeDept = $args['id'];

			$validation = $this->validator->validate($request, [

				'name' 			=> v::stringType()->notEmpty(),
				'description' 	=> v::stringType(),
				'mobile'		=> v::stringType(),
				'address'		=> v::stringType(),
				'email'			=> v::email(),

			]);

			if ($validation->failed()) {

				return $response->withRedirect('/setincharge/'.$inchegeDept);
			}

				
			$x= date('YmdHis');
			//get faculty of the teacher

			$newIncharge = Admin::create([
				'name'      	=> $request->getParam('name'),
				'email'        	=> $request->getParam('email'),
				'password_hash'	=> md5($x), 
				'type'   => 	'Dean Office',
				'description'    => $request->getParam('description'),
				'designation'    => 'Dean Office In Charge',
				'faculty'		=> $inchegeDept - 2,
				'mobile'    => $request->getParam('mobile'),
				'address'    => $request->getParam('address'),
				
			]);

			$inchargeInfo = FixedUsers::find($inchegeDept);
			if ($inchargeInfo->user) {
				$oldIncharge = Admin::find($inchargeInfo->user);
				$oldIncharge->active = 0;
				$oldIncharge->save();
			}
			

			$inchargeInfo->user = $newIncharge->id;
			$inchargeInfo->save();

			//email here
			$_SESSION['success'] = 'success';
			$_SESSION['msg'] = "Successfully created user account. Link to complete profile has been sent to user's email address.";
			return $response->withRedirect($this->router->pathFor('user.fixed'));

		}
		
	}

	public function setFixedAction($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Main Admin"){

			$inchegeDept = $args['id'];
			$inchargeInfo = FixedUsers::find($inchegeDept);

			$this->container->view->addAttribute('Title', $inchargeInfo->designation." Setup");
			$this->container->view->addAttribute('id', $args['id']);
			$this->container->view->addAttribute('designation', $inchargeInfo->designation);
			return $this->view->render($response, "modules/user/vcrgSetup.phtml");
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));

	}


	public function setFixedPostAction($request, $response, $args)
	{
		if($_SESSION['type']=="Main Admin"){

			$inchegeDept = $args['id'];
			if ($inchegeDept==1) {
				$designation = 'VC';
			} else if($inchegeDept==2){
				$designation = "Registrar";
			}

			$validation = $this->validator->validate($request, [

				'name' 			=> v::stringType()->notEmpty(),
				'description' 	=> v::stringType(),
				'mobile'		=> v::stringType(),
				'address'		=> v::stringType(),
				'email'			=> v::email(),

			]);

			if ($validation->failed()) {

				return $response->withRedirect('/setincharge/'.$inchegeDept);
			}

				
			$x= date('YmdHis');
			//get faculty of the teacher

			$newIncharge = Admin::create([
				'name'      	=> $request->getParam('name'),
				'email'        	=> $request->getParam('email'),
				'password_hash'	=> md5($x), 
				'type'   => 	'Administrative',
				'description'    => $request->getParam('description'),
				'designation'    => $designation,
				'mobile'    => $request->getParam('mobile'),
				'address'    => $request->getParam('address'),

			]);

			$inchargeInfo = FixedUsers::find($inchegeDept);
			if ($inchargeInfo->user) {
				$oldIncharge = Admin::find($inchargeInfo->user);
				$oldIncharge->active = 0;
				$oldIncharge->save();
			}
			

			$inchargeInfo->user = $newIncharge->id;
			$inchargeInfo->save();

			//email here
			// return "successfully created account";
			$_SESSION['success'] = 'success';
			$_SESSION['msg'] = "Successfully created user account. Link to complete profile has been sent to user's email address.";
			return $response->withRedirect($this->router->pathFor('user.fixed'));

		}
		
	}

	public function allUsers($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Main Admin"){

			$users = Admin::where('active', 1)->get();

			$this->container->view->addAttribute('Title', 'All Users');

			$this->container->view->addAttribute('users', $users);
			return $this->view->render($response, "modules/user/allUsers.phtml");
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));

	}

	
}




