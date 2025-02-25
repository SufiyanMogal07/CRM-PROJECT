<?php
require '../../vendor/autoload.php';
require "../../config/config.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$secretKey = $_ENV['JWT_SECRET_KEY'];
header("Access-Control-Allow-Origin: http://127.0.0.1:3000");
header("Access-Control-Allow-Methods: PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

if($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}


if($_SERVER['REQUEST_METHOD']!="PATCH") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success"=> false, "message"=>"It's not a valid request!!"]);
    exit();
}

$headers = getallheaders();

if(!isset($headers['Authorization'])) {
    http_response_code(400);
    echo json_encode(["success"=> false, "message"=>"Authorization Header Missing!!"]);
    exit();
}

if(!$data) {
    http_response_code(405);
    echo json_encode(["success"=> false, "message"=>"Data is not Sent!!"]);
    exit();
}

if(isset($headers["Authorization"])) {
    $token = trim(str_replace("Bearer ","",$headers['Authorization']));
    try{
        $decodedToken = JWT::decode($token,new Key($secretKey,'HS256'));

    } catch(Exception $e) {
        http_response_code(401);
        echo json_encode(["success"=>false,"message"=>$e->getMessage()]);
    }

    $role = $decodedToken->data->role;
    if($role==="admin") {
        $id = trim($data["id"]);
        $name = trim($data["name"]);
        $phone = trim($data["phone"]);
        $email = trim($data["email"]);
        $address = trim($data["address"]);
        $city = trim($data["city"]);
        $passportno = trim($data["passportno"]);

        $stmt = $conn->prepare("SELECT name,phone,email,address,city,passportno,created_by FROM users WHERE id = ?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $currentRecord = $stmt->get_result();

        if(!$currentRecord->num_rows > 0) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Record not found"]);
            exit();
        }
        $currentRecord = $currentRecord->fetch_assoc();
        $fields = [];
        $params = [];

        $admin_id = $decodedToken->data->id;
        
        if($currentRecord['created_by'] != $admin_id) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Forbidden!!"]);
            exit();
        }

        function setFields(&$fields,&$params,$name,$newRecord,$currentRecord) {
            if($newRecord!==$currentRecord) {
                $fields[] = "$name = ?";
                $params[] = $newRecord;
            }
        }
        setFields($fields,$params,'name',$name,$currentRecord['name']);
        setFields($fields,$params,'phone',$phone,$currentRecord['phone']);
        setFields($fields,$params,'email',$email,$currentRecord['email']);
        setFields($fields,$params,'address',$address,$currentRecord['address']);
        setFields($fields,$params,'city',$city,$currentRecord['city']);
        setFields($fields,$params,'passportno',$passportno,$currentRecord['passportno']);

        if(empty($fields)) {
            echo json_encode(["success" => false, "message" => "No Changes Detected"]);
            exit();
        }
        
        $fields_strings = implode(",",$fields);
        $params[] = $id;

        $sql = "UPDATE users SET $fields_strings WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat("s",count($params) - 1)."i",...$params);

        if($stmt->execute()) {
            echo json_encode(["success"=>true,"message"=> "Record Updated Successfully"]);
        }
        else {
            echo json_encode(["success" => false, "message" => "Failed to update record"]);
        }
    } else {
        http_response_code(401); // Method Not Allowed
        echo json_encode(["success" => false, "message" => "Admin Only!!"]);
    }
} 
