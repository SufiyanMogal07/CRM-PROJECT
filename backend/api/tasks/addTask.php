<?php
require "../../vendor/autoload.php";
require "../../config/config.php";
require "../notifications/addNotification.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../..");
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = $_ENV['JWT_SECRET_KEY'];
$BASE_URL = $_ENV['BASE_URL'];
header("Access-Control-Allow-Origin: $BASE_URL");
header("Access-Control-Allow-Methods: POST,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "message" => "It's not a valid request!!"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$headers = getallheaders();

if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    $token = trim(str_replace("Bearer ", "", $authHeader));
    try {
        $decodedToken = JWT::decode($token, new Key($secret_key, "HS256"));
    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Token decoding error', 'error' => $e->getMessage()]);
    }
    $role = $decodedToken->data->role;
    if ($role !== "admin") {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, "message" => "Unauthorized Access!!"]);
        exit();
    }

    $employee_id = $data['employee_id'];
    $user_id = $data['user_id'];
    $campaign_id = $data['campaign_id'];
    $action = $data['action'];
    $admin_id = $decodedToken->data->id ?? "";

    if (empty($campaign_id) || empty($employee_id) || empty($user_id) || empty($action) || empty($admin_id)) {
        http_response_code(405);
        echo json_encode(['success' => false, "message" => "Sent Data is Empty!!!"]);
        exit();
    }
    $sql = "SELECT name FROM admin WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $result = $result->fetch_assoc();
    $admin_name = $result['name'];

    $sql = "INSERT INTO task (employee_id,user_id,campaign_id,action,created_by) VALUES(?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiisi', $employee_id, $user_id, $campaign_id, $action, $admin_id);


    if ($stmt->execute()) {
        $data['message'] = ucFirst($admin_name)." Assigned you a New Task!!";
        $notify = addNotifications($admin_id,$employee_id,$role, ucFirst($admin_name)." Assigned you a New Task!!");

        if(!$notify['success']) {
            sendResponse(405,$notify);
        }

        $targetEvent = "private-employee-" . $employee_id;
        $pusher->trigger("notifications", $targetEvent, $data);
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Task Added Sucessfully']);
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Task Not Added!!']);
    }
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authorization header not provided', 'header' => $headers['Authorization']]);
    exit();
}
