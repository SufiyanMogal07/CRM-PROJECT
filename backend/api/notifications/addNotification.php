<?php
require "../../config/config.php";

function addNotifications($sender_id,$receiver_id,$sender_type,$message) {

    global $conn;

    if(!isset($sender_id) || !isset($receiver_id) || !isset($sender_type) || !isset($message)) {
        return array("success"=>false,"message"=>"Send Data is Empty!!");
    }

    $sql = "INSERT INTO notifications (sender_id,receiver_id,sender_type,message) VALUE(?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss",$sender_id,$receiver_id,$sender_type,$message);

    if($stmt->execute() === true) {
        return array("success"=>true,"message"=>"Notification Added!!");
    } else {
        return array("success"=>false,"message"=>"Error While Adding Notification!!");
    }
}