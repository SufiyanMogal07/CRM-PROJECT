<?php
require '../../vendor/autoload.php';
require "../../config/config.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$secretKey = $_ENV['JWT_SECRET_KEY'];
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$headers = getallheaders();
if($_SERVER['REQUEST_METHOD']!="POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success"=> false, "message"=>"It's not a valid request!!"]);
    exit();
}


function addUsersFNC($conn,$data,$admin_id) {
    $name = $data['name'] ?? "";
    $phone = $data['phone'] ?? "";
    $email = $data['email'] ?? "";
    $address = $data['address'] ?? "";
    $city = $data['city'] ?? "";
    $passport = $data['passport'] ?? "";
    

     // Validate input data
     if (empty($name) || empty($phone) || empty($email) || empty($address) || empty($city) || empty($passport) || empty($admin_id)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }
        $verify = $conn->prepare("SELECT * from users WHERE email= ? or phone = ?");
        $verify->bind_param('ss',$email,$phone);
        $verify->execute();
        $result = $verify->get_result();
        if($result->num_rows>0) {
            echo json_encode(['success'=>false,'message'=>'Email or Phone Number is Already Exist']);
            return;
        }
        $stmt = $conn->prepare("INSERT INTO users (name,phone,email,address,city,passportno,created_by) VALUES(?,?,?,?,?,?,?)");    
        $stmt->bind_param("ssssssi",$name,$phone,$email,$address,$city,$passport,$admin_id);

        if($stmt->execute()) {
            http_response_code(201);
            echo json_encode(['success'=> true, 'message'=>'User added Successfully!!']);
            return;
        }
        else {
            throw new Exception('Error adding User: ' . $stmt->error);
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
    $admin_id = $decodedToken->data->id;

    if($userRole=="admin") {
        addUsersFNC($conn,$data,$admin_id);
    }
    else {
        http_response_code(403); // Forbidden
        echo json_encode(['success'=>false,"message"=>"Unauthorized Access!!"]);
        exit();
    }

} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authorization header not provided']);
    exit();
} 