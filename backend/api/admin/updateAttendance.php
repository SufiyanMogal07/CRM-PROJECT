<?php
require '../../vendor/autoload.php';
require "../../config/config.php";
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
    http_response_code(405); // Method Not Allowed
    sendResponse(405, ["success" => false, "message" => "It's not a valid request!!"]);  // Method Not Allowed
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$secretKey = $_ENV['JWT_SECRET_KEY'];
$headers = apache_request_headers();

if (!$data) {
    sendResponse(405, ["success" => false, "message" => "Data is not Sent!!"]);
    exit();
}

$decodedToken = authenticateUser($secretKey, $headers);
$role = $decodedToken->data->role;

if ($role === "admin") {
    $id = $data['id'];
    $date = $data['date'];
    $status = $data['status'];

    $todayDate = new DateTime();
    $inputDate = new DateTime($date);

    if($inputDate > $todayDate) {
        sendResponse(401,["success"=>false,"message"=> "No Future Date Allowed"]);
    }

    if (empty($id) || empty($date) || empty($status)) {
        sendResponse(500, ["success" => false, "message" => "Data is Empty!!!"]);
        exit();
    } else {
        $stmt = $conn->prepare("SELECT * FROM attendance WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $verify = $stmt->get_result();

        if (!$verify->num_rows > 0) {
            sendResponse(404, ["success" => false, "message" => "Record not found"]);
        }
        $currentRecord = $verify->fetch_assoc();
        $fields = [];
        $params = [];

        setFields($fields,$params,'att_date',$date,$currentRecord['att_date']);
        setFields($fields, $params, "status", $status, $currentRecord['status']);

        if (empty($fields)) {
            sendResponse(200,['success'=>false,"message" => "No Changes Detected"]);
        }
        $fields_strings = implode(",", $fields);
        $sql = "UPDATE attendance SET $fields_strings WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $params[] = $id;
        $stmt->bind_param(str_repeat("s", count($params) - 1) . "i", ...$params);

        if($stmt->execute()) {
            sendResponse(200,["success"=>true,"message"=> "Record Updated Successfully","date"=>$todayDate]);
        } else {
            sendResponse(500,["success" => false, "message" => "Failed to update record"]);
        }
    }
}
