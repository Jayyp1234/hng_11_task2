<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Send some CORS headers so the API can be called from anywhere
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Cache-Control: no-cache");

include_once "../../config/response.php";
include_once "../../config/database.php";
include_once "../../utilities/functions.php";
include_once "../../middlewares/auth.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $detailsID =1;
    $getCompanyDetails = $connect->prepare("SELECT * FROM apidatatable WHERE id=?");
    $getCompanyDetails->bind_param('i', $detailsID);
    $getCompanyDetails->execute();
    $result = $getCompanyDetails->get_result();
    $companyDetails = $result->fetch_assoc();
    $companyprivateKey = $companyDetails['privatekey'];
    $minutetoend = $companyDetails['tokenexpiremin'];
    $serverName = $companyDetails['servername'];
    include_once '../../middlewares/auth.php';
    $decodeToken = ValidateAPITokenSentIN($serverName,$companyprivateKey);
    if ($decodeToken['success'] == true) {
        $publickey = $decodeToken['data']['token'];
        $requestedUserId = isset($_GET['id']) ? cleanme($_GET['id']) : '';
        //$userId = $decodeToken['data']['userId'];
        if ($requestedUserId !== $publickey) {
            // Check if the requested user is in the same organization as the authenticated user
            $query = $connect->prepare("SELECT * FROM User_Organisation WHERE userId = ? AND orgId IN (SELECT orgId FROM User_Organisation WHERE userId = ?)");
            $query->bind_param("ss", $requestedUserId, $publickey);
            $query->execute();
            $result = $query->get_result();
    
            if ($result->num_rows == 0) {
                http_response_code(403);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'You do not have permission to access this user\'s record',
                    'statusCode' => 403
                ]);
                exit;
            }
        }
    
        $query = $connect->prepare("SELECT publicKey, firstName, lastName, email, phone FROM Users WHERE publicKey = ?");
        $query->bind_param("s", $requestedUserId);
        $query->execute();
        $result = $query->get_result();
    
        if ($result->num_rows == 0) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'User not found',
                'statusCode' => 404
            ]);
            exit;
        }
    
        $user = $result->fetch_assoc();
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'User record retrieved successfully',
            'data' => $user
        ]);
    }else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid token sent in. Check that token is valid',
            'statusCode' => 401
        ]);
        return;
    }
    
    
    
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed',
        'statusCode' => 405
    ]);
}
?>
