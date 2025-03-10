<?php
require '../../vendor/autoload.php';
require '../../config/config.php';
require '../../utils/authenticate.php';
require '../../utils/helper.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
$BASE_URL = $_ENV['BASE_URL'];
header("Access-Control-Allow-Origin: $BASE_URL");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-type, Authorization");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"),true);
$headers = getallheaders();
$secretKey = $_ENV['JWT_SECRET_KEY'];

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if($_SERVER['REQUEST_METHOD']!="DELETE") {
    sendResponse(405,["success" => false, "message" => "It's not a valid request!!"]);  // Method Not Allowed
}
$id = $_GET['id'];

if(!isset($id)){
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Id parameter is Empty!!"]);
    exit();
}

$decodedToken = authenticateUser($secretKey,$headers);
$role = $decodedToken->data->role;


if($role!=="admin") {
    sendResponse("403",["success" => false, "message" => "Forbidden!! Admin Only!!"]);
    exit();
}

$admin_id = $decodedToken->data->id ?? "";

$stmt = $conn->prepare("DELETE FROM task WHERE id = ? AND created_by = ?");
$stmt->bind_param("ii",$id,$admin_id);

if($stmt->execute() ){
    sendResponse(200,["success" => true, "message" => "Task Deleted Successfully!!"]);
} else {
    sendResponse(404,["success" => false, "message" => "Task Not Found or Already Deleted"]);
}
