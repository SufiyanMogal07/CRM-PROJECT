<?php
require '../../vendor/autoload.php';
require "../../config/config.php";
require '../../utils/authenticate.php';
require '../../utils/helper.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = $_ENV['JWT_SECRET_KEY'];
$BASE_URL = $_ENV['BASE_URL'];
header("Access-Control-Allow-Origin: $BASE_URL");
header("Access-Control-Allow-Methods: PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != "PATCH") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "message" => "It's not a valid request!!"]);
    exit();
}

$headers = getallheaders();

if (!isset($headers['Authorization'])) {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "message" => "Authorization Header Missing!!"]);
    exit();
}

if(!$data) {
    http_response_code(405);
    echo json_encode(["success"=> false, "message"=>"Sent Data is Empty!!"]);
    exit();
}

if (isset($headers["Authorization"])) {
    $token = trim(str_replace("Bearer ", "", $headers['Authorization']));

    try {
        $decodedToken = JWT::decode($token, new Key($secretKey, 'HS256'));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }

    $role = $decodedToken->data->role;
    if ($role === "admin") {
        $id = trim($data["id"]);
        $name = trim($data["name"]);
        $email = trim($data["email"]);
        $phone = trim($data["phone"]);
        
        $stmt = $conn->prepare("SELECT * FROM employee WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result->num_rows > 0) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Record not found"]);
            exit();
        }

        $currentRecord = $result->fetch_assoc();
        $admin_id = $decodedToken->data->id;
        
        if($currentRecord['created_by'] !== $admin_id) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Forbidden!!"]);
            exit();
        }
        $fields = [];
        $params = [];


        if ($data['name'] !== $currentRecord['name']) {
            $fields[] = 'name = ?';
            $params[] = $data['name'];
        }
        if ($data['email'] !== $currentRecord['email']) {
            $fields[] = 'email = ?';
            $params[] = $data['email'];
        }
        if ($data['phone'] !== $currentRecord['phone']) {
            $fields[] = 'phone = ?';
            $params[] = $data['phone'];
        }

        if (empty($fields)) {
            echo json_encode(["success" => false, "message" => "No Changes Detected"]);
            exit();
        }

        $fields_strings = implode(",", $fields);

        $sql = "UPDATE employee SET $fields_strings WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $params[] = $id;
        $stmt->bind_param(str_repeat("s", count($params) - 1)."i", ...$params);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Record Updated Successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update record"]);
        }
    } else {
        http_response_code(401); // Method Not Allowed
        echo json_encode(["success" => false, "message" => "Admin Only!!"]);
    }
}
