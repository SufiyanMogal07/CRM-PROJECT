<?php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    function authenticateUser($secretKey,$headers) {
        if(isset($headers['Authorization'])) {

            $token = trim(str_replace("Bearer ","",$headers['Authorization']));
            
            try {
                $decodedToken = JWT::decode($token,new Key($secretKey,'HS256'));
                return $decodedToken;
            } catch(Exception $e) {
                sendResponse(400,['success' => false, 'message' => 'Token decoding error', 'error' => $e->getMessage()]);
            }
            
        } else {
            sendResponse(405,["success" => false, "message" => "Authorization Header Missing!!"]);
            return false;
        }
    }