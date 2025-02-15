<?php

include '../../vendor/autoload.php';
include '../../config/config.php';
include '../../utils/authenticate.php';
include '../../utils/helper.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../..');
$dotenv->load();

$secretKey = $_ENV['JWT_SECRET_KEY'];

header('Access-Control-Allow-Methods: POST,OPTIONS');
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$headers = apache_request_headers();
$data = json_decode(file_get_contents("php://input"),true);

if($data===null || !$data) {
    sendResponse(405,['success'=>false,'message'=>'Invalid JSON or Empty Body!!!']);
}

if($_SERVER['REQUEST_METHOD']==='OPTIONS') {
    http_response_code(200);
    exit();
}

if($_SERVER['REQUEST_METHOD']!=="POST") {
    sendResponse(405,["success"=>false, "message"=> "Invalid Request"]);
}

$decodedToken = authenticateUser($secretKey,$headers);
$role = $decodedToken->data->role;

if($role!=="admin" && $role!=="employee") {
    sendResponse(405,["success"=>false,'message'=>"Unauthorized Access!!"]);
}
if($role === "employee") {
    $employee_id = $decodedToken->data->id ?? null;
} elseif($role==="admin") {
    $employee_id = $data['employee_id'] ?? null;
}

$user_id = $data['user_id'] ?? null;
$time = $data['time'] ?? null;
$date = $data['date'] ?? null;
$outcome = $data['outcome'] ?? null;
$remarks = $data['remarks'] ?? null;
$conversion = $data['conversion'] ?? null;

if(!isset($employee_id) || !isset($user_id) || !isset($time) || !isset($date) || !isset($outcome) || !isset($conversion)) {
    sendResponse(403,['success'=>false,'message'=>"Send Data Fields Are Empty!!"]);
}

$sql = "INSERT INTO calllog (employee_id,user_id,call_time,call_date,outcome,remarks,conversion) VALUES(?,?,?,?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iissssi",$employee_id,$user_id,$time,$date,$outcome,$remarks,$conversion);

if($stmt->execute()) {
    sendResponse(200,['success'=>true,'message'=>'CallLogs Added Sucessfully!!']);
} else {
    sendResponse(500,['success'=>false,'message'=>'Something Went Wrong While Adding Data']);
}