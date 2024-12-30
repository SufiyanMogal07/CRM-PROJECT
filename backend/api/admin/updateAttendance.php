<?php
require '../../vendor/autoload.php';
require "../../config/config.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = $_ENV['JWT_SECRET_KEY'];
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if(!$data) {
    http_response_code(405);
    echo json_encode(["success"=> false, "message"=>"Data is not Sent!!"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != "PATCH") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "message" => "It's not a valid request!!"]);
    exit();
}

$headers = apache_request_headers();

if(isset($headers['Authorization'])) {
    $token = trim(str_replace("Bearer ","",$headers['Authorization']));

    try {
        $decodedToken = JWT::decode($token, new Key($secretKey, 'HS256'));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    $role = $decodedToken->data->role;
    if ($role === "admin") {
        $id = $data['id'];
        $date = $data['date'];
        $status = $data['status'];
        
        if(empty($id) || empty($date) || empty($status)) {
            http_response_code(405);
            echo json_encode(["success"=> false, "message"=>"Data is Empty!!!"]);
            exit();
        } else {
            $stmt = $conn->prepare("SELECT * FROM attendance WHERE id = ?");
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $verify = $stmt->get_result();

            if(!$verify->num_rows>0) {
                http_response_code(401); // Method Not Allowed
                echo json_encode(["success" => false, "message" => "There's no such Employee"]);
                exit();
            }
            $rowsData = $verify->fetch_assoc();
            $fields = [];
            $params = [];

            if($data['date']!==$rowsData['att_date']){
                $fields[] = 'att_date = ?';
                $params[] = $data['date'];
            }
            if($data['status']!==$rowsData['status']){
                $fields[] = 'status = ?';
                $params[] = $data['status'];
            }

            if (empty($fields)) {
                echo json_encode(["success" => false, "message" => "No Changes Detected"]);
                exit();
            }
            $fields_strings = implode(",", $fields);

            $sql = "UPDATE attendance SET $fields_strings WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $params[] = $id;
            $stmt->bind_param(str_repeat("s",count($params)-1)."i",...$params);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Record Updated Successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update record"]);
            }
        }
    } else {
        http_response_code(401); // Method Not Allowed
        echo json_encode(["success" => false, "message" => "Admin Only!!"]);
    }
}
else {
    http_response_code(405);
    echo json_encode(["success"=>false,"message"=>"Authorization Header is Missing!!"]);
}