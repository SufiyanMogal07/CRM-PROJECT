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

$data = json_decode(file_get_contents("php://input"),true);
$header = apache_request_headers();

if(isset($header["Authorization"])) {
    $token = $header["Authorization"];
    $token = str_replace("Bearer ",'',$token);

    try {
        $decodedToken = JWT::decode($token,new Key($secretKey,'HS256'));
    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Token decoding error', 'error' => $e->getMessage()]);
    }
    $role = $decodedToken->data->role;
    if($role==="admin") {
      $name = $data["name"];
      $email = $data["email"];
      $phone = $data["phone"];
      $password = $data["password"];
      $hashedPass = password_hash($password,PASSWORD_BCRYPT);

      $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
      $stmt->bind_param("s",$email);
      $stmt->execute();
      $result = $stmt->get_result();

      if($result->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["success"=> false,"message"=>"Email Already Exist!!"]);
        exit();
      }
      $stmt = $conn->prepare("INSERT INTO admin (name,email,phone,password) VALUES(?,?,?,?)");
      $stmt->bind_param("ssss",$name,$email,$phone,$hashedPass);

      if($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['success'=> true, 'message'=>'Admin added Successfully!!']);
        return;
    }
    else {
        throw new Exception('Error adding Admin: ' . $stmt->error);
        return;
    }
    }
    else {
        http_response_code(403);
        json_encode(['success'=>false,"message"=>"Unauthorized Access!!"]);
        exit();
    }
}