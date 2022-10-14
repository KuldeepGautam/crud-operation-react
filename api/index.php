<?php
// show errors
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// 
// response headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// include statements
require_once __DIR__ . '/common/paths.php';
require_once $pathUrlParams;
require_once $pathCustomersController;
require_once $pathCustomers;



// frombody post request data
// checking for the format of json object
$frombody = json_decode(file_get_contents('php://input'));

// attribute routing enabled
$urlParsed = parse_url($_SERVER['REQUEST_URI']);

# extracting the url params
extract($_GET);

// print_r($_GET);

// if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
//     $host = 'https://ip.sisrtd.com';
//     $path = $urlParsed['path'];

// }
// print_r($urlParsed);
switch ($urlParsed['path']) {

        // admin api endpoints

    case '/api/customers':
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                echo json_encode(getCustomers($customerId, $pageNumber, $pageSize));
                // echo "hello world..";
                break;
            case 'POST':
                echo json_encode(createCustomer($frombody));
                break;
            case 'PUT':
                echo json_encode(updateCustomer($customerId, $frombody));
                break;
            case 'DELETE':
                echo json_encode(deleteCustomer($customerId));
                break;
        }
        break;
}
