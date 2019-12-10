<?php 

session_start();


require __DIR__ . '/../vendor/autoload.php';

$baseUrl = 'http://'.$_SERVER['SERVER_NAME'].'/'; //------------ set the base url here ------------

//$xml=simplexml_load_file("sys/config.xml") or die("Error: Cannot create object");
// $xml=simplexml_load_file("note.xml") or die("Error: Cannot create object");
// print_r($xml);

// $doc = new DOMDocument();

// if ($doc->load('sys/config.xml')) {

//     $xpath = new DOMXPath($doc);
//     $query = '/Person/phoneNums[@type="skype"]';
//     $results = $xpath->query($query);

//     foreach ($results as $result){ // query may have more than one result
//         echo $result->nodeValue;
//     }

// }


$xml=simplexml_load_file(__DIR__."/../bootstrap/sys/config.xml") or die("Error: Invalid Setup!");

$config = $xml->default_setup->connection;


$app = new \Slim\App([

	'settings' => [
        // Slim Settings
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => true,

        'db' => [
            'driver' => 'mysql',
            'host' => $config->host,
            'database' => $config->dbname,
            'username' => $config->username,
            'password' => $config->password,
            'charset'   => $config->charset,
            'collation' => $config->collation,
            'prefix'    => '',
    	]
        
    ],
]);

// include_once __DIR__."/../bootstrap/sys/modules.php";

// $app = new \Slim\App([

//     'settings' => [
//         // Slim Settings
//         'displayErrorDetails' => true,
//         'determineRouteBeforeAppMiddleware' => true,

//         'db' => [
//             'driver' => 'mysql',
//             'host' => 'localhost',
//             'database' => 'storeslim',
//             'username' => 'root',
//             'password' => '1234',
//             'charset'   => 'utf8',
//             'collation' => 'utf8_unicode_ci',
//             'prefix'    => '',
//         ]
        
//     ],

    

// ]);

$container = $app->getContainer();

//define upload directory
$container['upload_directory'] = __DIR__ . '/../public/uploads/';

//database
$capsule = new \Illuminate\Database\Capsule\Manager;

$capsule->addConnection($container['settings']['db']);

$capsule->setAsGlobal();

$capsule->bootEloquent();

$container['db'] = function($container) use ($capsule){

	return $capsule;

};




//database end


//for twig
// $container['view'] = function($container){

// 	$view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [

// 		'cache' => false,

// 	]);

// 	$view->addExtension(new \Slim\Views\TwigExtension(

// 		$container->router,

// 		$container->request->getUri()
// 	));

// 	return $view;
// };


$container['auth'] = function($container){
    
    return new \App\Auth\Auth;
};
//-------------- setting up php view ----------------
// Register component on container

$container['view'] = new \Slim\Views\PhpRenderer("../templates/",[
        'baseUrl' => $baseUrl,
        'auth' => [

                'check' => $container->auth->check(),
                'user'  => $container->auth->user()


                    ]
    ]);

//---------------- end php view ---------------------

//---------------- validator setup ------------------

$container['validator'] = function($container){

    return new App\Validation\Validator;

};

//---------------- end validator ---------------------










//-------------setting up controllers--------

//dayabase SQL
$container['DbConnect'] = function($container){
    
    return new \App\Database\DbConnect($container);
};

$container['HomeController'] = function($container){
	
	return new \App\Controllers\HomeController($container);
};

$container['AuthController'] = function($container){
    
    return new \App\Controllers\Auth\AuthController($container);
};

$container['InventoryController'] = function($container){
    
    return new \App\Controllers\InventoryController($container);
};

$container['ProductsController'] = function($container){
    
    return new \App\Controllers\ProductsController($container);
};

$container['AccountController'] = function($container){
    
    return new \App\Controllers\AccountController($container);
};

$container['SuppliersController'] = function($container){
    
    return new \App\Controllers\SuppliersController($container);
};

$container['CustomersController'] = function($container){
    
    return new \App\Controllers\CustomersController($container);
};

$container['SalesController'] = function($container){
    
    return new \App\Controllers\SalesController($container);
};

$container['NotificationsController'] = function($container){
    
    return new \App\Controllers\NotificationsController($container);
};

$container['BalanceController'] = function($container){
    
    return new \App\Controllers\BalanceController($container);
};

$container['PaymentController'] = function($container){
    
    return new \App\Controllers\PaymentController($container);
};

$container['CollectionController'] = function($container){
    
    return new \App\Controllers\CollectionController($container);
};





$container['ReportController'] = function($container){
    
    return new \App\Controllers\ReportController($container);
};

$container['RmaserviceController'] = function($container){
    
    return new \App\Controllers\RmaserviceController($container);
};

$container['SettingsController'] = function($container){
    
    return new \App\Controllers\SettingsController($container);
};



$container['FormController'] = function($container){
    
    return new \App\Controllers\FormController($container);
};

//-------------setting up controllers END--------



//-------------Adding Middleware --------

$app->add(new \App\Middleware\ValidationErrorMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));

$app->add(new \App\Middleware\CsrfViewMiddleware($container));


//-------------Adding Middleware END--------


//------------- CSRF --------

// $container['csrf'] = function($container){
    
//     return new \Slim\Csrf\Guard;
// };
$container['csrf'] = function($container) {
    return new \App\Middleware\MyCsrfMiddleware;
};

$app->add('csrf:processRequest');

// $app->add($container->csrf);
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

//------------- CSRF END --------





require __DIR__ . '/../app/routes.php';


