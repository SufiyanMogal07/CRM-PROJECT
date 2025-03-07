<?php
require '../../vendor/autoload.php';
require "../../config/config.php";
require "../notifications/addNotification.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = $_ENV['JWT_SECRET_KEY'];
$BASE_URL = $_ENV['BASE_URL'];
header("Access-Control-Allow-Origin: $BASE_URL");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-type, Authorization");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"),true);
$headers = getallheaders();

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if($_SERVER['REQUEST_METHOD']!="POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success"=> false, "message"=>"It's not a valid request!!"]);
    exit();
}

function addCampaignFNC($conn,$campaignData,$admin_id,$pusher,$role) {
    $name = $campaignData['name'];
    $desc = $campaignData['CampaignDescription'];
     // Validate input data
     if (empty($name) || empty($desc) || empty($admin_id)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }

    $sql = "SELECT name FROM admin WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    $admin_name = $record['name'];

    $sql = "INSERT INTO campaign (campaign_name,description,created_by) VALUE(?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi",$name,$desc,$admin_id);
    

    if($stmt->execute()) {
        $sql = "SELECT id FROM employee WHERE created_by = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = array('message' => ucFirst($admin_name)." added a new Campaign");

        while($employee = $result->fetch_assoc()) {
            $employeeId = $employee['id'];
            $targetEvent = "private-employee-".$employeeId;
            $pusher->trigger("notifications",$targetEvent,$data);
           
            addNotifications($admin_id,$employeeId,"admin",ucFirst($admin_name)." added a new Campaign");
        }

        http_response_code(201);
        echo json_encode(['success'=> true, 'message'=>'Campaign added Successfully!!']);
        return;
    }
    else {
        throw new Exception('Error adding Campaign: ' . $stmt->error);
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
    $role = $decodedToken->data->role;
    $admin_id = $decodedToken->data->id;

    if($role=="admin") {
        addCampaignFNC($conn,$data,$admin_id,$pusher,$role);
    }
    else {
        http_response_code(403); // Forbidden
        echo json_encode(['success'=>false,"message"=>"Unauthorized Access!!"]);
        exit();
    }
} else {
    echo json_encode(["success"=>false, "message"=>"Authorization Header Missing!!"]);
}