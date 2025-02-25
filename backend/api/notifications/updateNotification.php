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
    $conn->close();
    exit();
}
if ($_SERVER['REQUEST_METHOD'] != "PATCH") {
    $conn->close();
    sendResponse(405,["success" => false, "message" => "It's not a valid request!!"]);  // Method Not Allowed
}
$secretKey = $_ENV['JWT_SECRET_KEY'];
$headers = getallheaders();
$decodedToken = authenticateUser($secretKey,$headers);

$role = $decodedToken->data->role;
$data = json_decode(file_get_contents("php://input"), true);

if($role!=="admin" && $role!="employee") {
    $conn->close();
    sendResponse("401",["success" => false, "message" => "Admin Only!!"]);
    exit();
}

$id = $data['id'];

if(!isset($id)) {
    $conn->close();
    sendResponse(405,array('success'=>false,"message"=>"Notification Id is Missing!!"));

}

function findNotification($id) {
    global $conn;
    $sql = "SELECT * FROM notifications WHERE id = ?";
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

$sender_type = ($role==="admin") ? "employee": "admin";
$record = findNotification($id);

if(!isset($record)) {
    $conn->close();
    sendResponse(405,array('success'=>false,"message"=>"Notification Not Found!!"));    
}

if($record['receiver_id'] != $decodedToken->data->id) {
    sendResponse(403,array('success'=>false,"message"=>"Forbidden!!!",'data'=>$record));   
    $conn->close();
}

$sql = "UPDATE notifications SET is_seen = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();

if($stmt->affected_rows > 0) {
    $conn->close();
    sendResponse(200,array('success'=>true,"message"=>"Notfication Updated!!"));   
}