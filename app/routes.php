<?php 


//$_SESSION['exclude-csrf'] = true;       use it to exclude from csrf!!

//home
$app->get('/', 'HomeController:indexAction')->setName('home');

$app->get('/denied', 'HomeController:accessDeniedAction')->setName('access.denied');

//------------------- Dashboard ---------------------

//get canlender date wise due data
$app->post('/public/dashboard/getschedulecal', 'CollectionController:getScheduleCalAction')->setName('dashboard.schedules.datewise');




//--------------------------- AUTH --------------------------------

//login
$app->get('/login', 'AuthController:getLogin')->setName('auth.login');

$app->post('/login', 'AuthController:postLogin')->setName('auth.login.post');

//register
$app->get('/register', 'AuthController:getRegister')->setName('auth.register');

$app->post('/public/auth/getdepartments', 'AuthController:getDepartments');

$app->post('/public/auth/getteachers', 'AuthController:getTeachers');

$app->post('/public/auth/getteacheremail', 'AuthController:getTeacherEmail');

//register
$app->post('/register', 'AuthController:registerPost');

//complete profile
$app->get('/complete-profile/{id}', 'AuthController:completeProfile');

//complete profile post
$app->post('/complete-profile/{id}', 'AuthController:completeProfilePost');

//logout
$app->get('/logout', 'AuthController:getLogout')->setName('auth.logout');

$app->post('/public/auth/checkpass', 'AuthController:checkpassAction')->setName('auth.logout.post');



//------------------- User ACCOUNT/Profile -------------
//profile
$app->get('/user', 'AccountController:accountAction')->setName('user.profile');

//add user - administrative
$app->get('/user/adduser', 'AccountController:addUserAction')->setName('user.add');

//add user - Fixed
$app->get('/user/fixeduser', 'AccountController:fixedUsersAction')->setName('user.fixed');

//add user - administrative post
$app->post('/user/adduser', 'AccountController:addUserPostAction');

//check username
$app->post('/public/user/checkusername', 'AccountController:checkusernameAction')->setName('user.check.username');

//set incharge
$app->get('/setincharge/{id}', 'AccountController:setInchargeAction');

//set incharge post
$app->post('/setincharge/{id}', 'AccountController:setInchargePostAction');

//set fixed
$app->get('/setfixed/{id}', 'AccountController:setFixedAction');

//set incharge post
$app->post('/setfixed/{id}', 'AccountController:setFixedPostAction');




//--------------------- leave applications

//new application
$app->get('/newapplication', 'FormController:newApplication')->setName('application.new');

//new application
$app->post('/public/newapplication', 'FormController:newApplicationPost');


//edit application
$app->post('/public/editapplication/{id}', 'FormController:editApplicationPost');

//application view
$app->get('/application/{id}', 'FormController:applicationView');

//application edit
$app->get('/editapplication/{id}', 'FormController:applicationEdit');

//application submit
$app->get('/submitapplication/{id}', 'FormController:applicationSubmit');

//all submitted application
$app->get('/allapplications', 'FormController:allApplications')->setName('application.all');

//all drafts application
$app->get('/alldrafts', 'FormController:allDraftApplications')->setName('application.drafts');

//waiting for recommendation of departmental chairman or dean
$app->get('/applications/waitingforrecom', 'FormController:awatingRecommendation')->setName('application.chairman.waitingforrecom')->setName('application.recommend.pending');

//all applications of recommendation of departmental chairman or dean
$app->get('/applications/recommend/allapplications', 'FormController:allRecommendedApps')->setName('application.recommend.all');

//pending applications to registrar
$app->get('/applications/pendingtoregistrar', 'FormController:pendingToRegistrar')->setName('application.pendingtoregistrar');

//all applications to registrar
$app->get('/applications/allapplications/registrar', 'FormController:allAppsRegistrar')->setName('application.all.registrar');

//recommend by chairman and dean
$app->post('/public/application/recommend/{id}', 'FormController:applicationRecommend');

//recommend by chairman and dean
$app->post('/public/application/mark/{id}', 'FormController:applicationMark');

//get users of a designation
$app->post('/public/users/getusersofdesignation', 'FormController:getUsersOfDesignation');


//pending applications to administrative user
$app->get('/applications/administrative/pending', 'FormController:pendingToAdministrative')->setName('application.pending.administrative');

//all applications to administrative user
$app->get('/applications/administrative/all', 'FormController:allToAdministrative')->setName('application.all.administrative');

//recommend by registrar to vc
$app->post('/public/application/recommendtovc/{id}', 'FormController:applicationRecommendToVc');

//pending applications to registrar
$app->get('/applications/pendingtovc', 'FormController:pendingToVC')->setName('application.pendingtovc');

//all applications to registrar
$app->get('/applications/allapplications/vc', 'FormController:allAppsVC')->setName('application.all.vc');

//final action by vc
$app->post('/public/application/finalactionvc/{id}', 'FormController:applicationApprovalByVc');

//generate office order
$app->get('/application/generateorder/{id}', 'FormController:generateOrder');

//generate office order POST
$app->POST('/public/pplication/generateorder/{id}', 'FormController:generateOrderPost');

//view office order
$app->get('/application/officeorder/{id}', 'FormController:orderView');

//submit order to dd
$app->get('/application/submitorder/{id}', 'FormController:orderSubmitToDD');


//assign memorandum

$app->post('/public/application/assignmemorandum/{id}', 'FormController:assignMemorandum');

//confirm finalization
$app->get('/application/confirmfinalization/{id}', 'FormController:confirmFinalization');

//cancel action
$app->get('/application/cancelaction/{id}','FormController:cancelAction');

//start leave
$app->get('/application/startleave/{id}','FormController:startLeave');



//user/allusers
$app->get('/user/allusers', 'AccountController:allUsers')->setName('users.all');

//confirmed applications
$app->get('/applications/confirmed', 'FormController:allConfirmedApplications')->setName('application.confirmed');

//confirmed applications
$app->get('/allteachers', 'AccountController:allTeachers')->setName('allteachers');

//confirmed applications
$app->get('/teachersonleave', 'AccountController:teachersOnLeave')->setName('teachersonleave');


//all teachr
$app->get('/allteachersbydept', 'AccountController:allTeachersByDept')->setName('allteachersbydept');

//confirmed applications
$app->get('/teachersonleavebydept', 'AccountController:teachersOnLeaveByDept')->setName('teachersonleave');

//all teachr
$app->get('/allteachersbyfc', 'AccountController:allTeachersByFaculty')->setName('allteachersbydept');

//confirmed applications
$app->get('/teachersonleavebyfc', 'AccountController:teachersOnLeaveByFacylty')->setName('teachersonleave');

//confirmed applications
// $app->get('/applications/confirmed', 'FormController:allConfirmedApplications')->setName('application.confirmed');
// 










