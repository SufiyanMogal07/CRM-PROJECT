<?php
function sendResponse($statuscode, $message)
{
    global $conn;
    http_response_code($statuscode);
    echo json_encode($message);
    $conn->close();
    exit();
}

function setFields(&$fields, &$params, $name, $newRecord, $currentRecord)
{
    if ($newRecord != $currentRecord) {
        $fields[] = "$name = ?";
        $params[] = $newRecord;
    }
}

function getAdminId($id, $tableName, $columnName)
{
    global $conn;
    $sql = "SELECT $columnName FROM $tableName WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result->num_rows > 0) {
        sendResponse(404, ["success" => false, 'message' => ucFirst($tableName) . " Not Found!!!"]);
    }
    $result = $result->fetch_assoc();
    return $result['created_by'];
}


function checkReference($target, $table_name, $first_column, $second_column, $first_id, $second_id,)
{
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM $table_name WHERE $first_column = ? AND $second_column = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $first_id, $second_id);

    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc();
    $total = $total['total'];

    if ($total > 0) {
        sendResponse(200, array("success" => false, "message" => "Cannot delete $target - associated records exist.", $total));
    }
}
