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
