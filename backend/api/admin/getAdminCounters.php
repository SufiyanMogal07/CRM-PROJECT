<?php
require '../../vendor/autoload.php';
require '../../config/config.php';
require '../../utils/authenticate.php';
require '../../utils/helper.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../..');
$dotenv->load();

$secretKey = $_ENV['JWT_SECRET_KEY'];
$BASE_URL = $_ENV['BASE_URL'];

header("Access-Control-Allow-Origin: $BASE_URL");
header("Access-Control-Allow-Methods: GET,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
$headers = getallheaders();

if($_SERVER['REQUEST_METHOD']==='OPTIONS') {
    http_response_code(200);
    exit();
}

if($_SERVER['REQUEST_METHOD']!=="GET") {
    sendResponse(405,["success"=>false, "message"=> "Invalid Request"]);
}

$decodedToken = authenticateUser($secretKey,$headers);
$role = $decodedToken->data->role;

if($role!=="admin") {
    sendResponse(401,["success"=>false,'message'=>"Unauthorized Access!!"]);
}

$admin_id = $decodedToken->data->id;

// Employees
$sql = "SELECT COUNT(*) AS total_employee FROM employee WHERE created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$admin_id);
$stmt->execute();
$result = $stmt->get_result();
$result = $result->fetch_assoc();
$employee = $result['total_employee'];

// Users
$sql = "SELECT COUNT(*) AS total_users FROM users WHERE created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$admin_id);
$stmt->execute();
$result = $stmt->get_result();
$result = $result->fetch_assoc();
$users = $result['total_users'];


// Campaigns
$sql = "SELECT COUNT(*) AS total_campaigns FROM campaign WHERE created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$admin_id);
$stmt->execute();
$result = $stmt->get_result();
$result = $result->fetch_assoc();
$campaigns = $result['total_campaigns'];

// Pending Tasks
$sql = "SELECT COUNT(*) AS pending_tasks FROM task WHERE status = 'Pending' AND created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$admin_id);
$stmt->execute();
$result = $stmt->get_result();
$result = $result->fetch_assoc();
$tasks = $result['pending_tasks'];

// Total Call logs
$sql = "SELECT COUNT(*) AS total_calllogs FROM calllog WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$admin_id);
$stmt->execute();
$result = $stmt->get_result();
$result = $result->fetch_assoc();
$calllogs = $result['total_calllogs'];

$data = [
    ["employee-counter" => $employee],
    ["user-counter"      => $users],
    ["campaign-counter"  => $campaigns],
    ["task-counter"    => $tasks],
    ["calllog-counter"   => $calllogs],
];

echo json_encode(array("data"=>$data));

