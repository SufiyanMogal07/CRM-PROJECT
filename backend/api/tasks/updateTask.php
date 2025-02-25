<?php
require '../../vendor/autoload.php';
require '../../config/config.php';
require '../../utils/authenticate.php';
require '../../utils/helper.php';
require '../notifications/addNotification.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PATCH,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] != "PATCH") {
    sendResponse(405,["success" => false, "message" => "It's not a valid request!!"]);  // Method Not Allowed
}

$data = json_decode(file_get_contents("php://input"), true);
$secretKey = $_ENV['JWT_SECRET_KEY'];
$headers = getallheaders();


$decodedToken = authenticateUser($secretKey,$headers);
$role = $decodedToken->data->role;

if($role!=="admin" && $role!="employee") {
    sendResponse("401",["success" => false, "message" => "Admin Only!!"]);
    exit();
}

if(!$data) {
    sendResponse(405,["success"=> false, "message"=>"Data is not Sent!!"]);
}

function findTask($id,$table_name,$admin_id) {
    global $conn;
    $sql = "SELECT * FROM $table_name WHERE id = ? AND created_by = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii",$id,$admin_id);
    $stmt->execute();
    $currentRecord = $stmt->get_result();

    if($currentRecord->num_rows > 0) {
        $currentRecord = $currentRecord->fetch_assoc();
        return $currentRecord;
    }
    return null;
}
if($role==="employee") {
    $id = $data['id'];
    $status = $data['status'];
    $employee_id = $decodedToken->data->id;
    $admin_id = getAdminId($id,"task","created_by");

    $task = findTask($id,"task",$admin_id);

    if(isset($id) && isset($status)) {
        $currentRecord = findTask($id,'task',$admin_id);
        if(!$currentRecord) {
            sendResponse(404,["success"=>false,"message"=>"Task Not Found!!!"]);
        }
        $currentStatus = $currentRecord['status'];

        if($status===$currentStatus) {
            sendResponse(200,["success"=>false,"message"=>"No Changes Detected"]);
        }

        $sql = "SELECT * FROM employee WHERE id = ? AND created_by = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii",$employee_id,$admin_id);
        $stmt->execute();
        $currentRecord = $stmt->get_result();

        if(!$currentRecord->num_rows > 0) {
            sendResponse(404,["success"=>false,"message"=>"Employee Not Found!!!"]);
        }

        $currentRecord = $currentRecord->fetch_assoc();
        $employee_name = $currentRecord['name'];


        $sql = "UPDATE task SET status = ? WHERE id = ? AND created_by = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii",$status,$id,$admin_id);
    
        if($stmt->execute()) {

            $data['message'] = ucfirst($employee_name)." Updated the Task status to '".$status."'";
            $notify = addNotifications($employee_id,$admin_id,"employee",ucfirst($employee_name)." Updated the Task status to '".$status."'");

            if(!$notify['success']) {
                sendResponse(405,$notify);
            }

            $targetEvent = "private-admin-" . $admin_id;
            $pusher->trigger("notifications", $targetEvent, $data);

            sendResponse(200,["success"=>true,"message"=>"Task Status Updated Successfully!!"]);
        } else {
            sendResponse(405,["success"=>true,"message"=>"Something Went Wrong!!!"]);
        }
    } else {
        sendResponse(405,["success"=>false,"message"=>"Id and Status is not send by the employee"]);
    }
} elseif ($role === "admin") {
    $id = $data["id"] ?? null;
    $status = $data['status'] ?? null;
    $action = $data['action'] ?? null;
    $admin_id = $decodedToken->data->id ?? "";

    if(isset($id) && isset($status) && isset($action)) {
        $currentRecord = findTask($id,'task',$admin_id);

        if(!$currentRecord > 0) {
            sendResponse(404,["success"=>false,"message"=>"Task Not Found!!"]);
        }

        if($status===$currentRecord['status'] && $action===$currentRecord['action']){
            sendResponse(200,["success"=>false,"message"=>"No Changes Detected!!"]);
        }
        try {
            $sql = "UPDATE task SET";
            $fields = [];
            $params = [];
            $types = '';

            if($status!==$currentRecord['status']) {
                $fields[] = "status = ?";
                $params[] = $status;
                $types .= "s";
            }
            if($action!==$currentRecord['action']) {
                $fields[] = "action = ?";
                $params[] = $action;
                $types .= "s";
            }

            $sql = 'UPDATE task SET '. implode(", ",$fields) . ' WHERE id = ? AND created_by = ?';
            $params[] = $id;
            $params[] = $admin_id;
            $types .= "ii";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                sendResponse(200, ["success" => true, "message" => "Task Updated Successfully"]);
            } else {
                sendResponse(500, ["success" => false, "message" => "Database Error: Unable to update task"]);
            }

        } catch (Exception $e) {
            sendResponse(500, ["success" => false, "message" => "Exception: " . $e->getMessage()]);
        }
    } else {
        sendResponse(400,["success"=>false,"message"=>"Id and Data is Not Provided!!"]);
    }
}

