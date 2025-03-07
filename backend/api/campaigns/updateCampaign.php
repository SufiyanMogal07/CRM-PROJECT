<?php
require '../../vendor/autoload.php';
require '../../config/config.php';
require '../../utils/authenticate.php';
require '../../utils/helper.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
$BASE_URL = $_ENV['BASE_URL'];
header("Access-Control-Allow-Origin: $BASE_URL");
header("Access-Control-Allow-Methods: PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] != "PATCH") {
    sendResponse(405,["success" => false, "message" => "It's not a valid request!!"]);  // Method Not Allowed
}

$data = json_decode(file_get_contents("php://input"), true);
$secretKey = $_ENV['JWT_SECRET_KEY'];
$headers = getallheaders();

if(!$data) {
    sendResponse(405,["success"=> false, "message"=>"Data is not Sent!!"]);
    exit();
}

$decodedToken = authenticateUser($secretKey,$headers);
$role = $decodedToken->data->role;

if($role!=="admin") {
    sendResponse("401",["success" => false, "message" => "Admin Only!!"]);
    exit();
}

$id = $data["id"];
$campaign_name = $data['campaign_name'];
$description = $data['description'];
$admin_id = ($role === "admin" ? $decodedToken->data->id: getAdminId($id,"calllog",$id));

if(empty($id) || empty($campaign_name) || empty($campaign_name) || empty($admin_id)) {
    sendResponse(500,["success"=> false, "message"=>"Sent Data is Empty!!"]);
}
$stmt = $conn->prepare("SELECT * FROM campaign WHERE id = ? AND created_by = ?");
$stmt->bind_param("ii",$id,$admin_id);
$stmt->execute();

$currentRecord = $stmt->get_result();

if(!$currentRecord->num_rows > 0) {
    sendResponse(404,["success" => false, "message" => "Record not found"]);
}

$currentRecord = $currentRecord->fetch_assoc();
$admin_id = $decodedToken->data->id;
        
if($currentRecord['created_by'] !== $admin_id) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Forbidden!!"]);
    exit();
}

$fields = [];
$params = [];

setFields($fields,$params,"campaign_name",$campaign_name,$currentRecord['campaign_name']);

setFields($fields,$params,"description",$description,$currentRecord['description']);

if(empty($fields)) {
    sendResponse(200,['success'=>false,"message" => "No Changes Detected"]);
}

$fields_str = implode(",",$fields);
$params[] = $id;


$sql = "UPDATE campaign SET $fields_str WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("s",count($params) - 1)."i",...$params);

if($stmt->execute()) {
    sendResponse(200,["success"=>true,"message"=> "Record Updated Successfully"]);
} else {
    sendResponse(500,["success" => false, "message" => "Failed to Update Record"]);
}