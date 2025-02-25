<?php
// pusher_auth.php
require "vendor/autoload.php";
require "utils/authenticate.php";
require "utils/helper.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

headers('Access-Control-Allow-Methods: POST');
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$secretKey = $_ENV['JWT_SECRET_KEY'];
$APP_KEY = $_ENV['PUSHER_APP_KEY'];
$APP_SECRET = $_ENV['PUSHER_APP_SECRET'];
$APP_ID = $_ENV['PUSHER_APP_ID'];
$CLUSTER = $_ENV['PUSHER_APP_CLUSTER'];

$options = array(
    'cluster' => $CLUSTER,
    'useTLS' => true
  );

$headers = getallheaders();

$pusher = new Pusher\Pusher(
    $APP_KEY,
    $APP_SECRET,
    $APP_ID,
    $options
);

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    sendResponse(405,array("success"=>false, "message"=> "Method Not Allowed!!"));
}
if(!$_POST['channel_name'] || !$_POST['socket_id']) {
    sendResponse(403,array("success"=>false, "message"=> "Forbidden !!!"));
}

$decodedToken = authenticateUser($secretKey,$headers);

$id = $decodedToken->data->id;
$role = $decodedToken->data->role;

if($role!=="admin" && $role!=="employee") {
    sendResponse(403,array("success"=>false, "message"=> "Forbidden !!!"));
}

$response = $pusher->authorizeChannel($_POST['channel_name'], $_POST['socket_id']);

echo $response;
