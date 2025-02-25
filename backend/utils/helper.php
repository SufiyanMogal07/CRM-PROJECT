<?php
function sendResponse($statuscode, $message)
{
    http_response_code($statuscode);
    echo json_encode($message);
    exit();
}

function setFields(&$fields, &$params, $name, $newRecord, $currentRecord)
{   
    if ($newRecord != $currentRecord) {
        $fields[] = "$name = ?";
        $params[] = $newRecord;
    }
}

function getAdminId($id,$tableName,$columnName) {
    global $conn;
    $sql = "SELECT $columnName FROM $tableName WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();

    if(!$result->num_rows>0) {
        sendResponse(404,["success"=>false,'message'=>ucFirst($tableName)." Not Found!!!"]);
    }
    $result= $result->fetch_assoc();
    return $result['created_by'];
}
