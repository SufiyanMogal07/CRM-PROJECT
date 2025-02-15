<?php
require '../../vendor/autoload.php';
require '../../config/config.php';
require '../../utils/authenticate.php';
require '../../utils/helper.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
header("Access-Control-Allow-Origin: *");
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
$headers = apache_request_headers();

if(!$data) {
    sendResponse(405,["success"=> false, "message"=>"Data is not Sent!!"]);
}


$decodedToken = authenticateUser($secretKey,$headers);
$role = $decodedToken->data->role;
error_log("Decoded role: " . $role);

if($role!=="admin" && $role!="employee") {
    sendResponse("401",["success" => false, "message" => "Admin Only!!"]);
    exit();
}


function findElement($id,$table_name) {
    global $conn;
    $sql = "SELECT * FROM $table_name WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $currentRecord = $stmt->get_result();

    if($currentRecord->num_rows > 0) {
        $currentRecord = $currentRecord->fetch_assoc();
        return $currentRecord;
    }
    return null;
}

$id = $data['id'] ?? null;
$time = $data['time'] ?? null;
$date = $data['date'] ?? null;
$outcome = $data['outcome'] ?? null;
$remarks = $data['remarks'] ?? null;
$conversion = $data['conversion'] ?? null;


if (!isset($id) || !isset($time) || !isset($date) || !isset($outcome) || !isset($conversion) ||
    $id === "" || $time === "" || $date === "" || $outcome === "" || $conversion === "") {
    sendResponse(403, ['success'=>false, 'message'=>"Send Data Fields Are Empty!!"]);
}

$sql = "SELECT * FROM calllog WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();

$result = $stmt->get_result();

if(!$result->num_rows > 0) {
    sendResponse(405,['success'=>false,'message'=>"Record Not Found!!"]);
}

$currentRecord = $result->fetch_assoc();
$fields = [];
$params = [];

setFields($fields,$params,"call_time",$data['time'],$currentRecord['call_time']);
setFields($fields,$params,"call_date",$data['date'],$currentRecord['call_date']);
setFields($fields,$params,"outcome",$data['outcome'],$currentRecord['outcome']);
setFields($fields,$params,"remarks",$data['remarks'],$currentRecord['remarks']);
setFields($fields,$params,"conversion",$data['conversion'],$currentRecord['conversion']);

if(empty($fields)) {
    sendResponse(200,['success'=>false,"message" => "No Changes Detected"]);
}


$fields_str = implode(",",$fields);
$params[] = $id;

$sql = "UPDATE calllog SET $fields_str WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("s",count($params) - 1)."i",...$params);

if($stmt->execute()) {
    sendResponse(200,["success"=>true,"message"=> "Record Updated Successfully"]);
} else {
    sendResponse(500,["success" => false, "message" => "Failed to Update record"]);
}