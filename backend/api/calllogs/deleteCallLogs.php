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

$admin_id = ($role==="admin" ? $decodedToken->data->id : getAdminId($decodedToken->data->id,"employee","created_by"));


if($role!=="admin" && $role!=="employee") {
    sendResponse("403",["success" => false, "message" => "Forbidden!! Admin Only!!"]);
    exit();
}


$stmt = $conn->prepare("DELETE FROM calllog WHERE id = ? AND admin_id = ?");
$stmt->bind_param("ii",$id,$admin_id);

if($stmt->execute() ){
    if ($stmt->affected_rows > 0) {
        sendResponse(200, ["success" => true, "message" => "Call Log Deleted Successfully!!"]);
    } else {
        sendResponse(404, ["success" => false, "message" => "Call Log Not Found or Already Deleted"]);
    }
} else {
    sendResponse(500, ["success" => false, "message" => "Failed to delete record"]);
}
