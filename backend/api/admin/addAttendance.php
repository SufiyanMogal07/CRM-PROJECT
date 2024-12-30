<?php
require '../../vendor/autoload.php';
require "../../config/config.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = $_ENV['JWT_SECRET_KEY'];
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-type, Authorization");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"),true);
$headers = apache_request_headers();

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}
function addAttendance($conn, $data)
{
    $stmt = $conn->prepare("SELECT * FROM attendance where employee_id = ? AND att_date = ?");
    $stmt->bind_param("is", $data["employee_id"], $data['date']);
    $stmt->execute();
    $verify = $stmt->get_result();

    if ($verify->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            "success" => false,
            "message" => "Attendance already recorded for this employee on the given date."
        ]);
        exit();
    } else {
        $sql = $conn->prepare("INSERT INTO attendance (employee_id,att_date,status) VALUE (?,?,?)");
        $sql->bind_param("iss",$data['employee_id'],$data['date'],$data['status']);
        if($sql->execute()) {
            echo json_encode(["success"=>true,"message"=>"Attendance Added Successfully"]);
        }
        else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["success"=>true,"message"=>"Failed to add Attendance. Please try again later!"]);
        }
    }
}
if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    $token = trim(str_replace('Bearer ', '', $authHeader));
    try {
        $decodedToken = JWT::decode($token, new Key($secretKey, 'HS256'));
    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode([
            'success' => false,
            'message' => 'Token decoding error',
            'error' => $e->getMessage()
        ]);
        exit();
    }
    $employee_id = $data['employee_id'];
    $date = $data['date'];
    $status = $data['status'];

    if (empty($employee_id) || empty($date) || empty($status)) {
        echo json_encode(["success" => false, "message" => "Data is Empty!!!"]);
        exit();
    }
    if(!in_array($status,["present","absent","leave"])) {
        echo json_encode(["success" => false, "message" => "The Status is Invalid!!!"]);
        exit();
    }

    addAttendance($conn, $data);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authorization header not provided']);
    exit();
}
