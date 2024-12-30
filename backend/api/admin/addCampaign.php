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

if($_SERVER['REQUEST_METHOD']!="POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success"=> false, "message"=>"It's not a valid request!!"]);
    exit();
}

function addCampaignFNC($conn,$data) {
    $name = $data['name'];
    $desc = $data['CampaignDescription'];
     // Validate input data
     if (empty($name) || empty($desc)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    $stmt = $conn->prepare("INSERT INTO campaign(campaign_name,description) VALUE(?,?)");
    $stmt->bind_param("ss",$name,$desc);
    if($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['success'=> true, 'message'=>'Campaign added Successfully!!']);
        return;
    }
    else {
        throw new Exception('Error adding employee: ' . $stmt->error);
        return;
    }
    }

if(isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    $token = trim(str_replace('Bearer ','',$authHeader));
    try {
        $decodedToken = JWT::decode($token, new Key($secretKey, 'HS256'));
    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Token decoding error', 'error' => $e->getMessage()]);
    }
    $userRole = $decodedToken->data->role;
    if($userRole=="admin") {
        addCampaignFNC($conn,$data);
    }
    else {
        http_response_code(403); // Forbidden
        echo json_encode(['success'=>false,"message"=>"Unauthorized Access!!"]);
        exit();
    }
} else {
    echo json_encode(["success"=>false, "message"=>"Authorization Header Missing!!"]);
}