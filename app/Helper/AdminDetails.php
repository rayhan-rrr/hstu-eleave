<?php

namespace App\Helper;

use App\Models\Admin;
use App\Models\AdminLogin;
use App\Models\AdminRole;

include '../././templates/php_includes/db_conx2.php';

$GLOBALS = array(
    'db_conx' => $db_conx
);
class AdminDetails
{

	public static function getAdminName($id)
	{
		if (isset($_SESSION['user'])) {

			return Admin::find($id)->name;
		
		} else return 0;
		
	}

	//checkTechnician
	/* this function checks if the currently logged in user is a technician. If he is a technician then the function returns TRUE and FALSE otherwise. */

	public static function checkTechnician()
	{
		if (isset($_SESSION['user'])) {
			$user = Admin::find($_SESSION['user']);
			$TECHNICIAN = 4; //the role id of TECHNICIAN is define in the variable
			if ($user->role==4) {
			 	return TRUE;
			 } 
		} else return FALSE;
	}

	public static function getTeacherInfo($id)
	{
		$teacher = Admin::find($id)->teacher_id;

		$db_conx = $GLOBALS['db_conx'];
		$sql = "SELECT * FROM `teachers` WHERE teacher_id='$teacher'";

		$result = mysqli_query($db_conx,$sql );

		if (mysqli_num_rows($result)>0) {
			
			$row = mysqli_fetch_assoc($result);
			$data = array();
			$data['email'] = $row['email'];
			$data['mobile_no'] = $row['mobile_no'];
			$data['fname'] = $row['fname'];
			$data['lname'] = $row['lname'];
			$data['gender'] = $row['gender'];
			$data['designation_id'] = $row['designation_id'];
			$data['short_bio'] = $row['short_bio'];
			$data['research_interest'] = $row['research_interest'];
			$data['last_degree'] = $row['last_degree'];
			$data['dept_id'] = $row['dept_id'];
			$data['role'] = $row['role'];
			$data['status'] = $row['status'];

			$department = $row['dept_id'];

			$designation = $row['designation_id'];

			$sql = "SELECT * FROM `department` WHERE dept_id='$department'";

			$result = mysqli_query($db_conx,$sql );

			$row = mysqli_fetch_assoc($result);

			$data['department_name'] = $row['dept_name'];
			$data['dept_code'] = $row['short_code'];

			$faculty = $row['faculty_id'];
			
			$sql = "SELECT * FROM `faculty` WHERE faculty_id=$faculty";

			$result = mysqli_query($db_conx,$sql );

			$row = mysqli_fetch_assoc($result);

			$data['faculty_name'] = $row['faculty_name'];


			$sql = "SELECT * FROM `designation` WHERE designation_id=$designation";

			$result = mysqli_query($db_conx,$sql );

			$row = mysqli_fetch_assoc($result);

			$data['designation'] = $row['designation_name'];

			return (object) $data;
		}

		return FALSE;

		
		
	}

	public static function getUserInfo()
	{
		return Admin::find($_SESSION['user']);		
	}

	public static function departmentChairman($dept_id)
	{
		
		$db_conx = $GLOBALS['db_conx'];
		$sql = "SELECT * FROM `chairman` WHERE dept_id='$dept_id'";

		$result = mysqli_query($db_conx,$sql );

		if (mysqli_num_rows($result)>0) {
			$row = mysqli_fetch_assoc($result);

			$teacher_id = $row['teacher_id'];

			return $teacher_id;
		}
		return FALSE;
		
	}

	public static function checkIfChairman($userid)
	{
		$user = Admin::find($userid);

		$department = $user->department;

		$db_conx = $GLOBALS['db_conx'];
		$sql = "SELECT * FROM `chairman` WHERE dept_id='$department'";
		$result = mysqli_query($db_conx,$sql );
		if (mysqli_num_rows($result)>0) {
			$row = mysqli_fetch_assoc($result);
		
			if ($row['teacher_id'] == $user->teacher_id) {
				return TRUE;
			}
			return FALSE;
		}
		
	}

	public static function checkIfDean($userid)
	{
		$user = Admin::find($userid);

		$faculty = $user->faculty;

		$db_conx = $GLOBALS['db_conx'];
		$sql = "SELECT * FROM `deans` WHERE faculty_id='$faculty'";
		$result = mysqli_query($db_conx,$sql );
		if (mysqli_num_rows($result)>0) {
			$row = mysqli_fetch_assoc($result);
		
			if ($row['teacher_id'] == $user->teacher_id) {
				return TRUE;
			}
			return FALSE;
			
		}
		return FALSE;
		
	}

	public static function getDeanOfTeacher($userid)
	{
		//gets admin_user id and returns dean's teacher_id

		$user = Admin::find($userid);

		$department = $user->department;

		$db_conx = $GLOBALS['db_conx'];
		$sql = "SELECT * FROM `department` WHERE dept_id='$department'";
		$result = mysqli_query($db_conx,$sql );
		if (mysqli_num_rows($result)>0) {
			$row = mysqli_fetch_assoc($result);

			$faculty_id = $row['faculty_id'];

			$sql = "SELECT * FROM `deans` WHERE faculty_id='$faculty_id'";
			$result = mysqli_query($db_conx,$sql );
			$row = mysqli_fetch_assoc($result);

			if ($row['teacher_id']) {
				return $row['teacher_id'];
			}
			return FALSE;
		}
		return FALSE;	
	}

	public static function getIfRegistrarAccess()
	{
		//gets admin_user id and returns dean's teacher_id

		$user = Admin::find($_SESSION['user']);
		if ($user->designation=='Registrar' || $user->designation=='Deputy Registrar' || $user->designation=='Assistant Registrar') {
			return TRUE;
		}
		return FALSE;
	}


	
}