<?php
require("jwt/vendor/autoload.php");
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Generated a unique pub key for all users
// generate Unique prive key for company from admin panel
// set Server name on admin $serverName
function getTokenToSendAPI($userPubkey,$companyprivateKey,$minutetoend,$serverName){
    try {
        $issuedAt   = new DateTimeImmutable();
    $expire     = $issuedAt->modify("+$minutetoend minutes")->getTimestamp();  
    $username   = "$userPubkey";
    $data = [
        'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
        'iss'  => $serverName,                       // Issuer
        'nbf'  => $issuedAt->getTimestamp(),         // Not before
        'exp'  => $expire,                           // Expire
        'token' => $username,                     // User name
    ];
    // Encode the array to a JWT string.
    //  get token below
    $auttokn= JWT::encode($data,$companyprivateKey,'HS256');
    //echo $auttokn;
   // print_r($decoded);

    return ['success' => true ,'data' => ['token' => $auttokn]];
    } catch (Exception $e) {
        return ['success' => false ,'data' => null,'message' => $e->getMessage()];
    }
}

function ValidateAPITokenSentIN($serverName,$companyprivateKey){
        $headerName = 'Authorization';
        $headers = getallheaders();
        $signraturHeader = isset($headers[$headerName]) ? $headers[$headerName] : null;
        if($signraturHeader==null){
            $signraturHeader= isset($_SERVER['HTTP_TOKEN']) ? $_SERVER['HTTP_TOKEN']:"";
            $signraturHeader = 'Bearer '.$signraturHeader;
        }
    try{
        if (!preg_match('/Bearer\s(\S+)/',$signraturHeader, $matches)) {
            return ['success' => false ,'data' => null, 'statusCode' => 403 ,'message' => 'Invalid token sent in.'];
        }
        $jwt = $matches[1];
        if (! $jwt) {
            return ['success' => false ,'data' => null, 'statusCode' => 403 ,'message' => 'Invalid token sent in.'];
        }
        //$secretKey  = $companyprivateKey;
        $token = $decoded = JWT::decode($jwt, new Key($companyprivateKey, 'HS256'));
        //print_r($token);
        $now = new DateTimeImmutable();
        if ($token->iss !== $serverName || $token->nbf > $now->getTimestamp() || $token->exp < $now->getTimestamp() || empty($token->token)) {
            return ['success' => false ,'data' => null, 'statusCode' => 403 ,'message' => 'Invalid token sent in.'];
        }
        return ['success' => true ,'data' => ['token' => $token->token], 'statusCode' => 200 ,];
    }
//catch exception
catch(Exception $e) {
     return ['success' => false ,'data' => null, 'statusCode' => 402 ,'message' => $e->getMessage()];
  }
}