<?php
declare(strict_types=1);
require '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$secretKey = $_ENV['JWT_SECRET_KEY'];

use Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");


$data = json_decode(file_get_contents("php://input"), true);
include('../config/config.php');

if (isset($data['email'], $data['password'])) {
    $email = $data['email'];
    $password = $data['password'];
    function authenticateUser($conn, $email, $password, $table, $role, $secretKey)
    {
        $stmt = $conn->prepare("SELECT * from $table WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            if ($password===$row['password'] || password_verify($password,$row['password'])) {
                $issuedAt = time();
                $expirationTime = ($role === "admin") ? $issuedAt + (3600*10) : $issuedAt + (3600*3);
                $payload = [
                    'iat' => $issuedAt,
                    'exp' => $expirationTime,
                    'data' => [
                        'id' =>  $row['id'],
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'role' => $role
                    ]
                ];
                $jwt = JWT::encode($payload, $secretKey, 'HS256');

                echo json_encode(["success" => true, "message" => "Login Successfull", "token" => $jwt]);
                return true;
            } else {
                echo json_encode(["success" => false, "message" => "Invalid Password"]);
                return true;
            }
        } return false;
    }
    if(!authenticateUser($conn,$email,$password,"admin","admin",$secretKey)) {
        if(!authenticateUser($conn,$email,$password,"employee","employee",$secretKey)) {
        echo json_encode(["success"=> false,"message"=> "$email Not Found!!!"]);
        }
    }

} else {
    echo json_encode(["success" => false, "message" => "Email and Password Required"]);
}

$conn->close();
exit();
