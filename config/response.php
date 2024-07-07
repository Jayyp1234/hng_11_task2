<?php
function respondOK($data){
    // response 
    header('HTTP/1.1 200 OK');
    echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
function respondNotCompleted($data){
    // 202 Accepted Indicates that the request has been received but not completed yet.
    header('HTTP/1.1 202 OK');
    return json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
function respondURLChanged($data){
    // The URL of the requested resource has been changed temporarily
    header('HTTP/1.1 302 URL changed');
    return json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
function respondNotFound($data){
    //  Not found
    header('HTTP/1.1 404 Not found');
    return json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
function respondForbiddenAuthorized($data){
    // 403 Forbidden
    // Unauthorized request. The client does not have access rights to the content. Unlike 401, the client’s identity is known to the server.
    header("HTTP/1.1 403 Forbidden");
    return json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
function respondUnAuthorized($data){
    // the client’s identity is known to the server.
    header("HTTP/1.1 401 Unauthorized");
    return json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
function respondInternalError($data){
    //  internal server error
    header("HTTP/1.1 500 Internal Server Error");
    return json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
function respondBadRequest($data){
    // 400 Bad Request
    header("HTTP/1.1 400 Bad request");
    return json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
function respondMethodNotAlowed($data){
    // 405 Method Not Allowed
    header("HTTP/1.1 405 Method Not allowed");
    return json_encode($data,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
// ALL RESPONSE CODE

// ALL RESPONSE ERROR
function returnError7001($errordesc,$linktosolve,$hint){
    // bad request
    $data = ["code"=>7001,"text"=>$errordesc,"link"=>"$linktosolve","hint"=>$hint];
    return $data;
}
function returnError7002($errordesc,$linktosolve,$hint){
    // Unauthorized
    $data = ["code"=>7002,"text"=>$errordesc,"link"=>"$linktosolve","hint"=>$hint];
    return $data;
}
function returnError7003($errordesc,$linktosolve,$hint){
    // Method Not allowed
    $data = ["code"=>7003,"text"=>$errordesc,"link"=>"$linktosolve","hint"=>$hint];
    return $data;
}
// ALL ERROR RESPONSE

// RETURN ERROR
function returnErrorArray($text,$method,$endpoint,$errordata,$maindata=[]){
    $text = empty($text) ? '': $text;
    $data = ["status"=>false,"text" => $text,"data" => $maindata, "time" => date("d-m-y H:i:sA",time()), "method" => $method, "endpoint" => $endpoint,"error"=>$errordata];
    return $data;
}
//  RETURN DATA 
function returnSuccessArray($text,$method,$endpoint,$errordata,$data,$status){
    $data = ["status"=>$status,"text" => $text,"data" => $data, "time" => date("d-m-y H:i:sA",time()), "method" => $method, "endpoint" => $endpoint,"error"=>$errordata];
    return $data;
}



?>