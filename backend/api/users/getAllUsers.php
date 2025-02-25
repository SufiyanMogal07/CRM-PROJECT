<?php
require '../../vendor/autoload.php';
require "../../config/config.php";
require "../../utils/helper.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = $_ENV['JWT_SECRET_KEY'];
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== "GET") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid Request"]);
    exit();
}


$header = getallheaders();

if (isset($header['Authorization'])) {
    $token = $header['Authorization'];
    $token = trim(str_replace('Bearer ', '', $token));
    try {
        $decodedToken = JWT::decode($token, new Key($secretKey, 'HS256'));

        $role = $decodedToken->data->role;
        if ($role !== "admin" && $role!="employee") {
            http_response_code(405);
            echo json_encode(["success" => false, "message" => "Not Allowed!!"]);
            exit();
        }
        $id =  $decodedToken->data->id;
        $admin_id = ($role==="admin" ? $id : getAdminId($id,"employee","created_by"));

        $sql = "SELECT * FROM users WHERE created_by = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i",$admin_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = array();

        if($result->num_rows>0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            echo json_encode(["success"=>true,"data"=>$data]);
        }else {
            
            echo json_encode(["success"=>false,"message"=>"Empty Table","data"=> []]);
        }
    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Token decoding error', 'error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Unauthorized Request"]);
}
