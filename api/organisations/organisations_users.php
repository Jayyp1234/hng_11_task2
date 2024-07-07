<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Cache-Control: no-cache");

include_once "../../config/response.php";
include_once "../../config/database.php";
include_once "../../utilities/functions.php";
include_once "../../middlewares/auth.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $detailsID = 1;
    $getCompanyDetails = $connect->prepare("SELECT * FROM apidatatable WHERE id=?");
    $getCompanyDetails->bind_param('i', $detailsID);
    $getCompanyDetails->execute();
    $result = $getCompanyDetails->get_result();
    $companyDetails = $result->fetch_assoc();
    $companyprivateKey = $companyDetails['privatekey'];
    $minutetoend = $companyDetails['tokenexpiremin'];
    $serverName = $companyDetails['servername'];

    $decodeToken = ValidateAPITokenSentIN($serverName, $companyprivateKey);
    if ($decodeToken['success'] == true) {
        $authUserId = $decodeToken['data']['token'];

        // Get the JSON input
        $data = json_decode(file_get_contents("php://input"), true);
        $userId = isset($data['userId']) ? cleanme($data['userId']) : '';
        $orgId = isset($_GET['orgId']) ? cleanme($_GET['orgId']) : '';

        // Validate input
        if (empty($userId)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'Bad Request',
                'message' => 'Client error: User ID is required',
                'statusCode' => 400
            ]);
            exit;
        }

        // // Check if the authenticated user has access to the organization
        // $checkOrgAccess = $connect->prepare("SELECT * FROM User_Organisation WHERE userId = ? AND orgId = ?");
        // $checkOrgAccess->bind_param("ss", $authUserId, $orgId);
        // $checkOrgAccess->execute();
        // $orgAccessResult = $checkOrgAccess->get_result();

        // if ($orgAccessResult->num_rows == 0) {
        //     http_response_code(403);
        //     echo json_encode([
        //         'status' => 'Forbidden',
        //         'message' => 'You do not have permission to add users to this organization, you are not part',
        //         'statusCode' => 403
        //     ]);
        //     exit;
        // }

        // Insert new user-organization relationship into the database
        $insertUserOrg = $connect->prepare("INSERT INTO User_Organisation (userId, orgId) VALUES (?, ?)");
        $insertUserOrg->bind_param("ss", $userId, $orgId);
        if ($insertUserOrg->execute()) {
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'User added to organization successfully',
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error occurred while adding the user to the organization',
                'statusCode' => 500
            ]);
        }
    } else {
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
