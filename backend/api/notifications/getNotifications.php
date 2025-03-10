<?php
require '../../vendor/autoload.php';
require "../../config/config.php";
require "../../utils/helper.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = $_ENV['JWT_SECRET_KEY'];
$BASE_URL = $_ENV['BASE_URL'];
header("Access-Control-Allow-Origin: $BASE_URL");
header("Access-Control-Allow-Methods: GET,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != "GET") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid Request"]);
    exit();
}
$header = getallheaders();

if (!isset($header['Authorization'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Authorization header missing"]);
    exit();
}
if (isset($header['Authorization'])) {
    $token = $header['Authorization'];
    $token = trim(str_replace('Bearer ', '', $token));
    try {
        $decodedToken = JWT::decode($token, new Key($secretKey, 'HS256'));

        $role = $decodedToken->data->role;

        if ($role !== "admin" && $role !== "employee") {
            http_response_code(405);
            echo json_encode(["success" => false, "message" => "Not Allowed!!"]);
            exit();
        }

        $sql = "SELECT * FROM notifications WHERE receiver_id = ? AND sender_type = ? AND is_seen = 0 ORDER BY created_at DESC";

        global $conn;
        if ($role === "employee") {
            $employee_id = $decodedToken->data->id;
            $stmt = $conn->prepare($sql);
            $senderRole = "admin";
            $stmt->bind_param("is",$employee_id,$senderRole);
        
        } elseif ($role === "admin") {
            $admin_id = $decodedToken->data->id ?? "";
            $stmt = $conn->prepare($sql);
            $senderRole = "employee";
            $stmt->bind_param("is",$admin_id,$senderRole);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode(["success" => true, "data" => $data]);
        } else {

            echo json_encode(["success" => false, "data" => [], "message" => "No Notifications"]);
        }
    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Token decoding error', 'error' => $e->getMessage()]);
    }
}
