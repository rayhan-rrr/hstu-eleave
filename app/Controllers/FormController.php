<?php 

namespace App\Controllers;

use App\Models\Admin;
use App\Models\Applications;
use App\Models\ApplicationForwarding;
use App\Models\OfficeOrders;

use App\Helper\AdminDetails;

use Respect\Validation\Validator as v;


class FormController extends Controller
{

	
	
	public function newApplication($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Academic"){


			$this->container->view->addAttribute('Title', "Write New Application");
			return $this->view->render($response, "modules/application/newApplication.phtml");
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}

	public function newApplicationPost($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Academic"){

			if (isset($_FILES['attached']) && $_FILES['attached']['error'] === UPLOAD_ERR_OK) {
		      $errors= array();
		      // get details of the uploaded file
				$fileTmpPath = $_FILES['attached']['tmp_name'];
				$fileName = $_FILES['attached']['name'];
				$fileSize = $_FILES['attached']['size'];
				$fileType = $_FILES['attached']['type'];
				$fileNameCmps = explode(".", $fileName);
				$fileExtension = strtolower(end($fileNameCmps));

				$newFileName = $_SESSION['user']. "_". date('d-m-y-His') . '.' . $fileExtension;
		      
		    	$extensions= array('jpg', 'gif', 'png', 'pdf', 'txt', 'xls', 'doc');
		      
		     	if(in_array($fileExtension,$extensions)=== false){
		     		$_SESSION['error'] = 'error';
					$_SESSION['msg'] = "extension not allowed, please choose a valid type of file.";
					return $response->withRedirect($this->router->pathFor('application.new'));
		    	}

		    	if($fileSize > 5000000){
		        	$_SESSION['error'] = 'error';
					$_SESSION['msg'] = "File size exceeds limit!";
					return $response->withRedirect($this->router->pathFor('application.new'));
		    	}

			    

				// directory in which the uploaded file will be moved
				$DIR = __DIR__ . '/../../public/attachments/'.$_SESSION['user']."/";

				if (!is_dir($DIR)) {
				    mkdir($DIR, 0777, true);
				}

				$uploadFileDir = $DIR;
				$dest_path = $uploadFileDir . $newFileName;
				 
				if(move_uploaded_file($fileTmpPath, $dest_path))
				{

					//File is successfully uploaded
					
					$user = Admin::find($_SESSION['user']);

					$application = Applications::create([
						'user'      	=> $_SESSION['user'],
						'department'   => $user->department,
						'faculty'      => $user->faculty,
						'type'   		=> $request->getParam('type'),
						'subject'      => $request->getParam('subject'),
						'days'			=> $request->getParam('days'),
						'body'    		=> $request->getParam('applicationbody'),
						'd_memorandum' => $request->getParam('memorandum'),
						'status' 		=> 'Pending',
						'attachment'   => $newFileName,
						'active'    	=> 1,
					]);

					$_SESSION['success'] = 'success';
					$_SESSION['msg'] = "Successfully created new application.";
					return $response->withRedirect('/application/'.$application->id);

				} else {
					$_SESSION['error'] = 'error';
					$_SESSION['msg'] = "There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.";
					return $response->withRedirect($this->router->pathFor('application.new'));
				}
		      
		   } else {

		   	//File is successfully uploaded
		   	$user = Admin::find($_SESSION['user']);

				$application = Applications::create([
					'user'      	=> $_SESSION['user'],
					'department'   => $user->department,
					'faculty'      => $user->faculty,
					'type'   		=> $request->getParam('type'),
					'subject'      => $request->getParam('subject'),
					'days'			=> $request->getParam('days'),
					'body'    		=> $request->getParam('applicationbody'),
					'd_memorandum' => $request->getParam('memorandum'),
					'status' 		=> 'Pending',
					'active'    	=> 1,
				]);

				$_SESSION['success'] = 'success';
				$_SESSION['msg'] = "Successfully created new application.";
				return $response->withRedirect('/application/'.$application->id);
		   }
	
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}


	public function editApplicationPost($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Academic"){

			$applicationId = $args['id'];

			$changesAttach = 0;

			if (isset($_FILES['attached']) && $_FILES['attached']['error'] === UPLOAD_ERR_OK) {
		      $errors= array();
		      // get details of the uploaded file
				$fileTmpPath = $_FILES['attached']['tmp_name'];
				$fileName = $_FILES['attached']['name'];
				$fileSize = $_FILES['attached']['size'];
				$fileType = $_FILES['attached']['type'];
				$fileNameCmps = explode(".", $fileName);
				$fileExtension = strtolower(end($fileNameCmps));

				$newFileName = $_SESSION['user']. "_". date('d-m-y-His') . '.' . $fileExtension;
		      
		    	$extensions= array('jpg', 'gif', 'png', 'pdf', 'txt', 'xls', 'doc');
		      
		     	if(in_array($fileExtension,$extensions)=== false){
		     		$_SESSION['error'] = 'error';
					$_SESSION['msg'] = "extension not allowed, please choose a valid type of file.";
					return $response->withRedirect('/editapplication/'.$applicationId);
		    	}

		    	if($fileSize > 5000000){
		        	$_SESSION['error'] = 'error';
					$_SESSION['msg'] = "File size exceeds limit!";
					return $response->withRedirect('/editapplication/'.$applicationId);
		    	}

			    

				// directory in which the uploaded file will be moved
				$DIR = __DIR__ . '/../../public/attachments/'.$_SESSION['user']."/";

				if (!is_dir($DIR)) {
				    mkdir($DIR, 0777, true);
				}

				$uploadFileDir = $DIR;
				$dest_path = $uploadFileDir . $newFileName;
				 
				if(move_uploaded_file($fileTmpPath, $dest_path))
				{
					//File is successfully uploaded

					$changesAttach = 1;

				} else {
					$_SESSION['error'] = 'error';
					$_SESSION['msg'] = "There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.";
					return $response->withRedirect('/editapplication/'.$applicationId);
				}
		      
		   }

		   $application  = Applications::find($applicationId);
		   if ($application) {
		   	$application->type = $request->getParam('type');
		   	$application->subject = $request->getParam('subject');
		   	$application->days = $request->getParam('days');
		   	$application->d_memorandum = $request->getParam('memorandum');
		   	$application->body = $request->getParam('applicationbody');
		   	if ($changesAttach==1) {
		   		$application->attachment = $newFileName;
		   	}

		   	$application->save();

		   	$_SESSION['success'] = 'success';
				$_SESSION['msg'] = "Successfully edited application.";
				return $response->withRedirect('/application/'.$application->id);	
		   }
		   	

			

	
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}

	public function applicationView($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		
		if ($application) {
			$user = Admin::find($_SESSION['user']);

			$this->container->view->addAttribute('Title', "View Application");
			$this->container->view->addAttribute('application', $application);
			$this->container->view->addAttribute('user', $user);
			return $this->view->render($response, "modules/application/applicationView.phtml");
		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invalid request!";
		return $response->withRedirect($this->router->pathFor('home'));
	
	}

	public function applicationEdit($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		
		if ($application && $application->user==$_SESSION['user']) {
			$this->container->view->addAttribute('Title', "Edit Application");
			$this->container->view->addAttribute('application', $application);
			return $this->view->render($response, "modules/application/applicationEdit.phtml");
		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invalid request!";
		return $response->withRedirect($this->router->pathFor('home'));
	
	}

	public function applicationSubmit($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		
		if ($application && $application->user==$_SESSION['user']) {

			$teacher = AdminDetails::getTeacherInfo($_SESSION['user']);
			$chairman = AdminDetails::departmentChairman($teacher->dept_id);

			ApplicationForwarding::create([
					'application'  	=> $application->id,
					'department'   => $application->department,
					'faculty'      => $application->faculty,
					'to_designation'  => 'Chairman',
					'to_user'      	=> $chairman,//user means teache_id, not admin_user id
					'forwarded_by'		=> $_SESSION['user'],
					'by_designation'	=> 'Teacher',
					'docket' 			=> 'NA',
					'active'    		=> 1,
				]);

			$application->submitted = 1;
			$application->status = "Submitted to Chairman";
			$application->save();


			$_SESSION['success'] = 'success';
			$_SESSION['msg'] = 'Successfully submitted application!';
			return $response->withRedirect('/application/'.$applicationId);
		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invalid request!";
		return $response->withRedirect($this->router->pathFor('home'));
	
	}


	public function allApplications($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Academic"){

			$this->container->view->addAttribute('Title', "All Applications");
			return $this->view->render($response, "modules/application/allApplications.phtml");
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}


	public function allDraftApplications($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Academic"){

			$this->container->view->addAttribute('Title', "All Draft Applications");
			return $this->view->render($response, "modules/application/allDraftApplications.phtml");
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}

	public function awatingRecommendation($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Academic"){
			//for chairman
			if (AdminDetails::checkIfChairman($_SESSION['user'])){

				$user = Admin::find($_SESSION['user']);

				$forwardings = ApplicationForwarding::where('to_designation', 'Chairman')->where('department', $user->department)->where('active', 1)->get();

				$this->container->view->addAttribute('user', $user);
				$this->container->view->addAttribute('forwardings', $forwardings);

				$this->container->view->addAttribute('Title', "Waiting For Recommendation");
				return $this->view->render($response, "modules/application/waitingRecomCD.phtml");
			}

			//for dean
			if (AdminDetails::checkIfDean($_SESSION['user'])){

				$user = Admin::find($_SESSION['user']);

				$forwardings = ApplicationForwarding::where('to_designation', 'Dean')->where('faculty', $user->faculty)->where('active', 1)->get();

				$this->container->view->addAttribute('user', $user);
				$this->container->view->addAttribute('forwardings', $forwardings);

				$this->container->view->addAttribute('Title', "Waiting For Recommendation");
				return $this->view->render($response, "modules/application/waitingRecomCD.phtml");
			}

			
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}

	public function allRecommendedApps($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Academic"){
			//for chairman
			if (AdminDetails::checkIfChairman($_SESSION['user'])){

				$user = Admin::find($_SESSION['user']);

				$forwardings = ApplicationForwarding::where('to_designation', 'Chairman')->where('department', $user->department)->get();

				$this->container->view->addAttribute('user', $user);
				$this->container->view->addAttribute('forwardings', $forwardings);

				$this->container->view->addAttribute('Title', "Waiting For Recommendation");
				return $this->view->render($response, "modules/application/allApplicationCD.phtml");
			}

			//for dean
			if (AdminDetails::checkIfDean($_SESSION['user'])){

				$user = Admin::find($_SESSION['user']);

				$forwardings = ApplicationForwarding::where('to_designation', 'Dean')->where('faculty', $user->faculty)->get();

				$this->container->view->addAttribute('user', $user);
				$this->container->view->addAttribute('forwardings', $forwardings);

				$this->container->view->addAttribute('Title', "Waiting For Recommendation");
				return $this->view->render($response, "modules/application/allApplicationCD.phtml");
			}

			
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}

	public function applicationRecommend($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		
		if ($application) {

			$teacher = AdminDetails::getTeacherInfo($_SESSION['user']);
			

			$toDesignation = '';
			if (AdminDetails::checkIfChairman($_SESSION['user'])) {
				$dean = AdminDetails::getDeanOfTeacher($_SESSION['user']);

				$toUser = $dean;
				$toDesignation = "Dean";
				$byDesignation = "Chairman";

				$oldForwarding = ApplicationForwarding::where('application', $applicationId)->where('to_designation', "Chairman")->where('active', 1)->first();
				$oldForwarding->active = 0;
				$oldForwarding->save();


			} else{
				$registrar = Admin::where('designation', 'Registrar')->where('active',1)->first();

				$toUser = $registrar->id;
				$toDesignation = "Registrar";
				$byDesignation = "Dean";

				$oldForwarding = ApplicationForwarding::where('application', $applicationId)->where('to_designation', "Dean")->where('active', 1)->first();
				if ($oldForwarding) {
					$oldForwarding->active = 0;
					$oldForwarding->save();
				}
			}

			ApplicationForwarding::create([
					'application'  	=> $application->id,
					'department'   	=> $application->department,
					'faculty'      	=> $application->faculty,
					'to_designation'  => $toDesignation,
					'to_user'      	=> $toUser,//user means teache_id, not admin_user id
					'forwarded_by'		=> $_SESSION['user'],
					'by_designation'	=> $byDesignation,
					'forward_remark'  => $request->getParam('note'),
					'docket' 			=> 'NA',
					'active'    		=> 1,
				]);

			$application->submitted = 1;
			$application->status = "Recommended and Forwarded to ".$toDesignation ." by ".$byDesignation;
			$application->save();


			$_SESSION['success'] = 'success';
			$_SESSION['msg'] = 'Successfully Recommended & Forwarded!';
			return $response->withRedirect('/application/'.$applicationId);
		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invalid request!";
		return $response->withRedirect($this->router->pathFor('home'));
	}


	public function pendingToRegistrar($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Administrative"){
			$user = Admin::find($_SESSION['user']);

			if ($user->designation=='Registrar'){

				$forwardings = ApplicationForwarding::where('to_designation', 'Registrar')->where('active', 1)->get();
				$this->container->view->addAttribute('user', $user);
				$this->container->view->addAttribute('forwardings', $forwardings);

				$this->container->view->addAttribute('Title', "Waiting For Recommendation");
				return $this->view->render($response, "modules/application/pendingToRegisrar.phtml");
			}
			
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}

	public function allAppsRegistrar($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Administrative"){
			$user = Admin::find($_SESSION['user']);

			if ($user->designation=='Registrar'){

				$this->container->view->addAttribute('user', $user);
				$this->container->view->addAttribute('forwardings', $forwardings);

				$this->container->view->addAttribute('Title', "All Applications");
				return $this->view->render($response, "modules/application/allAppsRegisrar.phtml");
			}
			
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}

	public function getUsersOfDesignation($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$designation = $request->getParam('designation');

		if ($designation) {
			$users = Admin::where('designation', $designation)->where('active', 1)->get();

			$options = '<option value="">Select A User</option>';
			foreach ($users as $user) {
				$options .= "<option value='".$user->id."'>".$user->name."</option>";
			}
			return $options;
		}
	}

	
	public function applicationMark($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		
		if ($application) {
			$toDesignation = $request->getParam('designation');
			$toUser = $request->getParam('touser');
			$note = $request->getParam('note');
			$docket = $request->getParam('docket');
			$byUser = Admin::find($_SESSION['user']);

			$oldForwarding = ApplicationForwarding::where('application', $applicationId)->where('active', 1)->orderBy('id', 'desc')->first();
			if ($oldForwarding) {
				$oldForwarding->active = 0;
				$oldForwarding->save();
			}

			ApplicationForwarding::create([
					'application'  	=> $application->id,
					'department'   	=> $application->department,
					'faculty'      	=> $application->faculty,
					'to_designation'  => $toDesignation,
					'to_user'      	=> $toUser,//user means teache_id, not admin_user id
					'forwarded_by'		=> $_SESSION['user'],
					'by_designation'	=> $byUser->designation,
					'forward_remark'  => $note,
					'docket' 			=> $docket,
					'active'    		=> 1,
				]);
			if ($application->status=="Approved by VC") {
				$application->status = "Marked to ".$toDesignation ." by ".$byUser->designation." For Office Order";
			} else if($application->status=="Marked to Deputy Director by Registrar For Office Order") {
				$application->status = "Processing Office Order";
			} else {
				$application->status = "Reviewed And Marked to ".$toDesignation ." by ".$byUser->designation;
			}
			$application->save();

			$_SESSION['success'] = 'success';
			$_SESSION['msg'] = "Successfully Reviewed And Marked to ".$toDesignation;
			return $response->withRedirect('/application/'.$applicationId);
		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invalid request!";
		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function pendingToAdministrative($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Administrative"){
			$user = Admin::find($_SESSION['user']);

			$forwardings = ApplicationForwarding::where('to_designation', '!=', 'Dean')->where('to_designation', '!=', 'Chairman')->where('to_user', $user->id)->where('active', 1)->get();

			$this->container->view->addAttribute('user', $user);
			$this->container->view->addAttribute('forwardings', $forwardings);

			$this->container->view->addAttribute('Title', "Pending Applications");
			return $this->view->render($response, "modules/application/toAdministrative.phtml");
			
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}

	public function allToAdministrative($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Administrative"){
			$user = Admin::find($_SESSION['user']);

			$forwardings = ApplicationForwarding::where('to_user', $user->id)->where('to_designation', '!=', 'Dean')->where('to_designation', '!=', 'Chairman')->get();

			$this->container->view->addAttribute('user', $user);
			$this->container->view->addAttribute('forwardings', $forwardings);

			$this->container->view->addAttribute('Title', "All Forwardings");
			return $this->view->render($response, "modules/application/allToAdministrative.phtml");
			
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}

	public function applicationRecommendToVc($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		
		if ($application) {

			$byUser = Admin::find($_SESSION['user']);
			

			$toDesignation = 'VC';
			$vc = Admin::where('designation', 'VC')->where('active', 1)->first();
			$toUser = $vc->id;
			$byDesignation = "Registrar";

			$oldForwarding = ApplicationForwarding::where('application', $applicationId)->where('active', 1)->orderBy('id', 'desc')->first();
			if ($oldForwarding) {
				$oldForwarding->active = 0;
				$oldForwarding->save();
			}

			ApplicationForwarding::create([
					'application'  	=> $application->id,
					'department'   	=> $application->department,
					'faculty'      	=> $application->faculty,
					'to_designation'  => $toDesignation,
					'to_user'      	=> $toUser,//user means teache_id, not admin_user id
					'forwarded_by'		=> $byUser->id,
					'by_designation'	=> $byDesignation,
					'forward_remark'  => $request->getParam('note'),
					'docket' 			=> 'NA',
					'active'    		=> 1,
				]);

			$application->submitted = 1;
			$application->status = "Recommended and Forwarded to ".$toDesignation ." by ".$byDesignation;
			$application->save();


			$_SESSION['success'] = 'success';
			$_SESSION['msg'] = 'Successfully Recommended & Forwarded!';
			return $response->withRedirect('/application/'.$applicationId);
		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invalid request!";
		return $response->withRedirect($this->router->pathFor('home'));
	}

	public function applicationApprovalByVc($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		
		if ($application) {

			$byUser = Admin::find($_SESSION['user']);
			

			$toDesignation = 'Registrar';
			$registrar = Admin::where('designation', 'Registrar')->where('active', 1)->first();
			$toUser = $registrar->id;
			$byDesignation = "VC";

			$oldForwarding = ApplicationForwarding::where('application', $applicationId)->where('active', 1)->orderBy('id', 'desc')->first();
			if ($oldForwarding) {
				$oldForwarding->active = 0;
				$oldForwarding->save();
			}

			ApplicationForwarding::create([
					'application'  	=> $application->id,
					'department'   	=> $application->department,
					'faculty'      	=> $application->faculty,
					'to_designation'  => $toDesignation,
					'to_user'      	=> $toUser,//user means teache_id, not admin_user id
					'forwarded_by'		=> $_SESSION['user'],
					'by_designation'	=> $byDesignation,
					'forward_remark'  => $request->getParam('note'),
					'docket' 			=> 'NA',
					'active'    		=> 1,
				]);

			$approval = $request->getParam('approval');
			$application->final_action = $approval;
			$application->status = $approval." by ".$byDesignation;
			$application->save();


			$_SESSION['success'] = 'success';
			$_SESSION['msg'] = 'Successfully '.$approval.'!';
			return $response->withRedirect('/application/'.$applicationId);
		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invalid request!";
		return $response->withRedirect($this->router->pathFor('home'));
	}

	
	public function pendingToVC($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Administrative"){
			$user = Admin::find($_SESSION['user']);

			if ($user->designation=='VC'){

				$forwardings = ApplicationForwarding::where('to_designation', 'VC')->where('active', 1)->get();
				$this->container->view->addAttribute('user', $user);
				$this->container->view->addAttribute('forwardings', $forwardings);

				$this->container->view->addAttribute('Title', "Waiting For Approval");
				return $this->view->render($response, "modules/application/pendingToVC.phtml");
			}
			
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}

	public function allAppsVC($request, $response)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		if($_SESSION['type']=="Administrative"){
			$user = Admin::find($_SESSION['user']);

			if ($user->designation=='VC'){

				$this->container->view->addAttribute('user', $user);
				$this->container->view->addAttribute('forwardings', $forwardings);

				$this->container->view->addAttribute('Title', "All Applications");
				return $this->view->render($response, "modules/application/allAppsRegisrar.phtml");///same
			}
			
		}
		return $response->withRedirect($this->router->pathFor('access.denied'));
	}


	
	public function generateOrder($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		$user = Admin::find($_SESSION['user']);
		
		if ($application && $user->designation=='Computer Operator') {

			$this->container->view->addAttribute('Title', "Generate Office Order");
			$this->container->view->addAttribute('application', $application);
			return $this->view->render($response, "modules/application/generateOrder.phtml");

		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invalid request!";
		return $response->withRedirect($this->router->pathFor('home'));
	
	}

	public function generateOrderPost($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		$user = Admin::find($_SESSION['user']);
		
		if ($application && $user->designation=='Computer Operator') {

			$orderbody = $request->getParam('orderbody');

			$order = OfficeOrders::create([
					'application'  	=> $application->id,
					'office_order'   	=> $orderbody,
					'active'    		=> 1,
				]);

			$application->office_order = $order->id;
			$application->save();

			$_SESSION['success'] = 'success';
			$_SESSION['msg'] = 'Successfully Generated Office Order';

			return $response->withRedirect('/application/officeorder/'.$application->id);

		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invalid request!";
		return $response->withRedirect($this->router->pathFor('home'));
	
	}

	public function orderView($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		$user = Admin::find($_SESSION['user']);
		
		if ($application && $application->office_order) {

			$order = OfficeOrders::find($application->office_order);

			$this->container->view->addAttribute('Title', "Office Order Details");
			$this->container->view->addAttribute('application', $application);
			$this->container->view->addAttribute('user', $user);
			$this->container->view->addAttribute('order', $order);
			return $this->view->render($response, "modules/application/viewOrder.phtml");

		}

		$_SESSION['error'] = 'error';
		$_SESSION['msg'] = "Invalid request!";
		return $response->withRedirect($this->router->pathFor('home'));
	
	}

	
	public function orderSubmitToDD($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		
		if ($application) {

			$order = OfficeOrders::find($application->office_order);
			$order->submitted = 1;
			$order->save();

			$oldForwarding = ApplicationForwarding::where('application', $applicationId)->where('active', 1)->orderBy('id', 'desc')->first();
			

			$toDesignation = $oldForwarding->by_designation;
			$toUser = $oldForwarding->forwarded_by;
			$note = 'Submitted Office Order to '.$toDesignation;
			$docket = '';
			$byUser = Admin::find($_SESSION['user']);
			
			if ($oldForwarding) {
				$oldForwarding->active = 0;
				$oldForwarding->save();
			}

			ApplicationForwarding::create([
					'application'  	=> $application->id,
					'department'   	=> $application->department,
					'faculty'      	=> $application->faculty,
					'to_designation'  => $toDesignation,
					'to_user'      	=> $toUser,//user means teache_id, not admin_user id
					'forwarded_by'		=> $_SESSION['user'],
					'by_designation'	=> $byUser->designation,
					'forward_remark'  => $note,
					'docket' 			=> $docket,
					'active'    		=> 1,
				]);

			$application->status = 'Office Order Generated. Waiting for approval';
			$application->save();

			$_SESSION['success'] = 'success';
			$_SESSION['msg'] = "Successfully Submitted to ".$toDesignation;

			return $response->withRedirect('/application/'.$applicationId);
		}

	}

	public function assignMemorandum($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		
		if ($application) {

			$order = OfficeOrders::find($application->office_order);
			$order->memorandum = $request->getParam('memorandum');
			$order->save();

			
			$registrar = Admin::where('designation', 'Registrar')->where('active',1)->first();

			$toDesignation = 'Registrar';
			$toUser = $registrar->id;
			$note = 'Memorandum assigned. Submitted for finalization';
			$docket = '';
			$byUser = Admin::find($_SESSION['user']);
			
			$oldForwarding = ApplicationForwarding::where('application', $applicationId)->where('active', 1)->orderBy('id', 'desc')->first();
			if ($oldForwarding) {
				$oldForwarding->active = 0;
				$oldForwarding->save();
			}

			ApplicationForwarding::create([
					'application'  	=> $application->id,
					'department'   	=> $application->department,
					'faculty'      	=> $application->faculty,
					'to_designation'  => $toDesignation,
					'to_user'      	=> $toUser,//user means teache_id, not admin_user id
					'forwarded_by'		=> $_SESSION['user'],
					'by_designation'	=> $byUser->designation,
					'forward_remark'  => $note,
					'docket' 			=> $docket,
					'active'    		=> 1,
				]);

			$application->status = 'Memorandum assigned. Submitted for finalization';
			$application->save();

			$_SESSION['success'] = 'success';
			$_SESSION['msg'] = "Successfully Submitted the memorandum!";

			return $response->withRedirect('/application/'.$applicationId);
		}

	}

	public function confirmFinalization($request, $response, $args)
	{

		if(!$this->auth->check())
		{
			return $response->withRedirect($this->router->pathFor('auth.login'));
		}

		$applicationId = $args['id'];

		$application = Applications::find($applicationId);
		
		if ($application) {

			$oldForwarding = ApplicationForwarding::where('application', $applicationId)->where('active', 1)->orderBy('id', 'desc')->first();
			if ($oldForwarding) {
				$oldForwarding->active = 0;
				$oldForwarding->save();
			}


			$application->confirmed = 1;
			$application->status = 'Confirmed';
			$application->save();

			$_SESSION['success'] = 'success';
			$_SESSION['msg'] = "Successfully Confirmed Finalization of application!";

			return $response->withRedirect('/application/'.$applicationId);
		}

	}

	
	
	

}