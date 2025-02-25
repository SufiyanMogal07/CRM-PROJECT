<?php
include '../../vendor/autoload.php';
include '../../config/config.php';
include '../../utils/authenticate.php';
include '../../utils/helper.php';
require "../notifications/addNotification.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../..');
$dotenv->load();

$secretKey = $_ENV['JWT_SECRET_KEY'];

header('Access-Control-Allow-Methods: POST,OPTIONS');
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$headers = getallheaders();
$data = json_decode(file_get_contents("php://input"),true);

if($data===null || !$data) {
    sendResponse(405,['success'=>false,'message'=>'Invalid JSON or Empty Body!!!']);
}

if($_SERVER['REQUEST_METHOD']==='OPTIONS') {
    http_response_code(200);
    exit();
}

if($_SERVER['REQUEST_METHOD']!=="POST") {
    sendResponse(400,["success"=>false, "message"=> "Invalid Request"]);
}

$decodedToken = authenticateUser($secretKey,$headers);
$role = $decodedToken->data->role;

if($role!=="admin" && $role!=="employee") {
    sendResponse(403,["success"=>false,'message'=>"Unauthorized Access!!"]);
}

$id = $decodedToken->data->id;
$admin_id = ($role === "admin" ? $id: getAdminId($id,"employee","created_by"));

$sql = "SELECT name FROM admin WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();
$admin_name = $record['name'];

if($role === "employee") {
    $employee_id = $decodedToken->data->id ?? "";
} elseif($role==="admin") {
    $employee_id = $data['employee_id'] ?? "";
}

$user_id = $data['user_id'] ?? null;
$time = $data['time'] ?? null;
$date = $data['date'] ?? null;
$outcome = $data['outcome'] ?? null;
$remarks = $data['remarks'] ?? null;
$conversion = $data['conversion'] ?? null;


if (!isset($employee_id) || !isset($time) || !isset($date) || !isset($outcome) || !isset($conversion) ||  !isset($user_id)) {
    sendResponse(200, ['success'=>false, 'message'=>"Send Data Fields Are Empty!!"]);
}

$sql = "INSERT INTO calllog (employee_id,user_id,call_time,call_date,outcome,remarks,conversion,admin_id) VALUES(?,?,?,?,?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iissssii",$employee_id,$user_id,$time,$date,$outcome,$remarks,$conversion,$admin_id);

if($stmt->execute()) {
    if($role==="admin") {
        $notify = addNotifications($admin_id,$employee_id,$role,ucFirst($admin_name)." Added a New Calllog For You!!");
        $data = array('message' => ucFirst($admin_name)." Added a New Calllog For You!!");
        $targetEvent = "private-employee-".$employee_id;
        $pusher->trigger("notifications",$targetEvent,$data);

    } elseif($role==="employee"){
        $sql = "SELECT name FROM employee WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_assoc();
        $employee_name = $result['name'];

        $notify = addNotifications($employee_id,$admin_id,$role,ucFirst($employee_name)." Added a New Calllog!!");

        $data = array('message' => ucFirst($employee_name)." Added a New Calllog!!");
        $targetEvent = "private-admin-".$admin_id;
        $pusher->trigger("notifications",$targetEvent,$data);
    }
    sendResponse(200,['success'=>true,'message'=>'CallLogs Added Sucessfully!!']);
} else {
    sendResponse(500,['success'=>false,'message'=>'Something Went Wrong While Adding Data']);
}