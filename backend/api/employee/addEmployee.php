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


function addEmployeeFNC($conn,$data,$created_by) {
    $name = $data['name'];
    $email = $data['email'];
    $phone = $data['phone'];
    $password = $data['password'];
     // Validate input data
     if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($created_by)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }
        $hashedPass = password_hash($password,PASSWORD_BCRYPT);
        $verify = $conn->prepare("SELECT * from employee WHERE email= ? or phone = ?");
        $verify->bind_param('ss',$email,$phone);
        $verify->execute();
        $result = $verify->get_result();
        if($result->num_rows>0) {
            echo json_encode(['success'=>false,'message'=>'Email or Phone Number is Already Exist']);
            exit();
        }
        $stmt = $conn->prepare("INSERT INTO employee (name,email,phone,password,created_by) VALUES(?,?,?,?,?)");
        $stmt->bind_param("ssssi",$name,$email,$phone,$hashedPass,$created_by);

        if($stmt->execute()) {
            http_response_code(201);
            echo json_encode(['success'=> true, 'message'=>'Employee added Successfully!!']);
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
        $created_by = $decodedToken->data->id;
        addEmployeeFNC($conn,$data,$created_by);
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