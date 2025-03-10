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

        $sql = "
        SELECT t.id AS taskID,
        c.campaign_name as campaignName,
        e.name as employeeName,
        u.name as userName,
        t.status as status,
        t.action as action,
        t.campaign_id as campaignId,
        t.employee_id as employeeId,
        t.user_id as userId
        FROM task as t
        JOIN campaign as c ON t.campaign_id = c.id
        JOIN employee as e ON t.employee_id = e.id
        JOIN users as u ON t.user_id = u.id ";

        
        if ($role === "employee") {
            $employee_id = $decodedToken->data->id;
            $admin_id = getAdminId($employee_id,"employee","created_by");
            $id = $decodedToken->data->id;
            $sql .=" WHERE t.employee_id = ? AND t.created_by = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii",$id,$admin_id);
        } elseif ($role === "admin") {
            $admin_id = $decodedToken->data->id ?? "";
            $sql .=" WHERE t.created_by = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i",$admin_id);
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

            echo json_encode(["success" => false, "data" => [], "message" => "Empty Table"]);
        }
    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Token decoding error', 'error' => $e->getMessage()]);
    }
}
