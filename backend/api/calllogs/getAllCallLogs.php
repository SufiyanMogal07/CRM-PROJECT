<?php
require '../../vendor/autoload.php';
require '../../config/config.php';
require '../../utils/authenticate.php';
require '../../utils/helper.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

$secretKey = $_ENV['JWT_SECRET_KEY'];
$BASE_URL = $_ENV['BASE_URL'];
header("Access-Control-Allow-Origin: $BASE_URL");
header("Access-Control-Allow-Methods: GET,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
$headers = getallheaders();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
    sendResponse(405, ["success" => false, "message" => "Invalid Request"]);
}

$decodedToken = authenticateUser($secretKey, $headers);
$role = $decodedToken->data->role;

if ($role !== "admin" && $role !== "employee") {
    sendResponse(405, ["success" => false, 'message' => "Unauthorized Access!!", "data" => []]);
}

$sql = "SELECT c.id,
        c.employee_id,
        c.user_id,
        u.name as user_name,
        e.name as employee_name,
        c.call_time,
        c.call_date,
        c.outcome,
        c.remarks,
        c.conversion
        FROM calllog as c
        JOIN employee as e ON c.employee_id = e.id
        JOIN users as u ON c.user_id = u.id";

if ($role === "employee") {
    $employee_id = $decodedToken->data->id;
    $admin_id = getAdminId($employee_id, "employee", "created_by");
    $employee_id = $decodedToken->data->id;
    $sql .= " WHERE c.employee_id = ? AND c.admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $employee_id, $admin_id);
} elseif ($role === "admin") {
    $admin_id = $decodedToken->data->id;
    $sql .= " WHERE c.admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result->num_rows > 0) {
    sendResponse(200, ['success' => false, 'message' => "Call Logs Table is Empty!", 'data' => []]);
}

$data = array();

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

sendResponse(200, ['success' => true, 'data' => $data]);
