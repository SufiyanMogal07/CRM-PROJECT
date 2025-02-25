<?php
require '../../vendor/autoload.php';
require "../../config/config.php";
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

if(!isset($id)){
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "id parameter empty!!"]);
    exit();
}

$header = getallheaders();

if (!isset($header['Authorization'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Authorization header missing"]);
    exit();
}

if($header['Authorization']) {
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
        $admin_id = $decodedToken->data->id;
        
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND created_by = ?");
        $stmt->bind_param("ii",$id,$admin_id);

        if($stmt->execute() ){
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "User Deleted Successfully!!"]);
        } else {
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "User Not Found"]);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token decoding error', 'error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Unauthorized Request"]);
}