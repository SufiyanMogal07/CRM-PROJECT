<?php
require '../../vendor/autoload.php';
require "../../config/config.php";
require '../../utils/authenticate.php';
require '../../utils/helper.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
$BASE_URL = $_ENV['BASE_URL'];

header("Access-Control-Allow-Origin: $BASE_URL");
header("Access-Control-Allow-Methods: POST,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    sendResponse(405, ["success" => false, "message" => "It's not a valid request!!"]);  // Method Not Allowed
}

$data = json_decode(file_get_contents("php://input"), true);
$headers = getallheaders();
$secretKey = $_ENV['JWT_SECRET_KEY'];

$decodedToken = authenticateUser($secretKey,$headers);
$role = $decodedToken->data->role;

if($role!=="admin") {
    sendResponse("401",["success" => false, "message" => "Admin Only!!"]);
    exit();
}

if ($role === "admin") {
    $name = $data["name"];
    $email = $data["email"];
    $phone = $data["phone"];
    $password = $data["password"];

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        sendResponse(500, ["success" => false, "message" => "Sent Data is Empty!!"]);
    }
    $hashedPass = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["success" => false, "message" => "Email Already Exist!!"]);
        exit();
    }
    $stmt = $conn->prepare("INSERT INTO admin (name,email,phone,password) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $hashedPass);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['success' => true, 'message' => 'Admin added Successfully!!']);
        return;
    } else {
        throw new Exception('Error adding Admin: ' . $stmt->error);
        return;
    }
}
