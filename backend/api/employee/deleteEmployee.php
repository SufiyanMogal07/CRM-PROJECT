<?php
require '../../vendor/autoload.php';
require "../../config/config.php";
require "../../utils/helper.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = $_ENV['JWT_SECRET_KEY'];
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE ,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not Allowed"]);
    exit();
}
$id = $_GET['id'];

if (!isset($id)) {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "id parameter empty!!"]);
    exit();
}

$header = apache_request_headers();

if (!isset($header['Authorization'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Authorization header missing"]);
    exit();
}


if ($header['Authorization']) {
    $token = $header['Authorization'];
    $token = trim(str_replace('Bearer ', '', $token));
    try {
        $decodedToken = JWT::decode($token, new Key($secretKey, 'HS256'));

        $role = $decodedToken->data->role;
        if ($role !== "admin") {
            http_response_code(405);
            echo json_encode(["success" => false, "message" => "Not Allowed!!"]);
            exit();
        }

        // Employee Exist or Not Check
        $admin_id = $decodedToken->data->id;
        $stmt = $conn->prepare("DELETE FROM employee WHERE id = ? AND created_by = ?");
        $stmt->bind_param("ii", $id, $admin_id);

        // Attendance
        checkReference("Employee","attendance", "employee_id", "created_by", $id, $admin_id);

        // Tasks
        checkReference("Employee","task", "employee_id", "created_by", $id, $admin_id);

        // callLog
        checkReference("Employee","calllog", "employee_id", "admin_id", $id, $admin_id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "Employee Deleted Successfully!!"]);
        } else {
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "Employee Not Found!!"]);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token decoding error', 'error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Unauthorized Request"]);
}
