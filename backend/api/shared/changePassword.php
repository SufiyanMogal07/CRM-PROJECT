<?php
require '../../vendor/autoload.php';
require '../../config/config.php';
require '../../utils/authenticate.php';
require '../../utils/helper.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
$secretKey = $_ENV['JWT_SECRET_KEY'];
$headers = getallheaders();
$BASE_URL = $_ENV['BASE_URL'];
header("Access-Control-Allow-Origin: $BASE_URL");
header("Access-Control-Allow-Methods: PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "PATCH") {
    $data = json_decode(file_get_contents("php://input"), true);
    $decodedToken = authenticateUser($secretKey, $headers);
    $role = $decodedToken->data->role;


    if ($role !== "admin" && $role != "employee") {
        sendResponse(401, ["success" => false, "message" => "Admin Only!!"]);
        exit();
    }

    if ($data === null || !$data) {
        sendResponse(401, ['success' => false, 'message' => 'Invalid JSON or Empty Body!!!']);
    }


    $id = $decodedToken->data->id;
    $tableName = "";
    $password = $data['old_password'] ?? "";
    $newPassword = $data['password'] ?? "";

    $admin_id = ($role === "admin" ? $decodedToken->data->id: getAdminId($id,"employee","created_by"));

    if (empty($password) || empty($newPassword) || empty($admin_id)) {
        sendResponse(500, array("success" => false, "message" => "Empty or invalid data received"));
    }


    $tableName = $role;
    $sql = "SELECT * FROM $tableName WHERE id = ? ";
    $stmt;
    if($role==="employee") {
        $sql .= " AND created_by = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id,$admin_id);
    } else if($role === "admin") {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $currentRecord = $result->fetch_assoc();
    } else {
        sendResponse(404, array("success" => false, "message" => ucFirst($role) . " Not Found !!"));
    }


    if (!password_verify($password, $currentRecord['password'])) {
        sendResponse(403, array("success" => false, "message" => ucFirst($role) . " old password not matched!!"));
    }
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("UPDATE $tableName SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $id);

    $status;
    $msg = "";
    $flag = false;

    if ($stmt->execute()) {
        $status = 200;
        $msg = "Password Change Successfully";
        $flag = true;
    } else {
        $status = 500;
        $msg = "Something Went Wrong While Changing Password";
    }
    sendResponse($status, array("success" => $flag, "message" => $msg));
} else {
    sendResponse(405,["success" => false, "message" => "It's not a valid request!!"]);  // Method Not Allowed
}
