<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Cache-Control: no-cache");

include_once "../../config/response.php";
include_once "../../config/database.php";
include_once "../../utilities/functions.php";
include_once "../../middlewares/auth.php";

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
if (!$decodeToken['success']) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid token sent in. Check that token is valid',
        'statusCode' => 401
    ]);
    exit;
}

$userId = $decodeToken['data']['token'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    error_log("Handling POST request");

    // Get the JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    
    $name = isset($data['name']) ? cleanme($data['name']) : '';
    $description = isset($data['description']) ? cleanme($data['description']) : '';

    // Validate input
    if (empty($name)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'Bad Request',
            'message' => 'Client error: Name is required',
            'statusCode' => 400
        ]);
        exit;
    }

    // Generate unique orgId
    $orgId = generatePubKey(5) . time();

    // Insert new organisation into the database
    $insert_org_data = $connect->prepare("INSERT INTO Organisations (orgId, name, description) VALUES (?, ?, ?)");
    $insert_org_data->bind_param("sss", $orgId, $name, $description);
    if ($insert_org_data->execute()) {
        
        // Link user to the new organisation
        $link_user_org = $connect->prepare("INSERT INTO User_Organisation (userId, orgId) VALUES (?, ?)");
        $link_user_org->bind_param("ss", $userId, $orgId);
        $link_user_org->execute();
        
        
        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Organisation created successfully',
            'data' => [
                'orgId' => $orgId,
                'name' => $name,
                'description' => $description
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Error occurred while creating the organisation',
            'statusCode' => 500
        ]);
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    if ($_GET['orgId'] == '.php'){
        $_GET['orgId'] = '';
    }
    if(!empty($_GET['orgId'])){
        $org_id = $_GET['orgId'];
        $query = $connect->prepare("SELECT o.orgId, o.name, o.description
                                FROM Organisations o
                                JOIN User_Organisation uo ON o.orgId = uo.orgId
                                WHERE uo.userId = ? AND o.orgId = ?");
                                
        $query->bind_param("ss", $userId,$org_id);
        $query->execute();
        $result = $query->get_result();
        $organisations = $result->fetch_assoc();
        
    }else{
        $query = $connect->prepare("SELECT o.orgId, o.name, o.description
                                FROM Organisations o
                                JOIN User_Organisation uo ON o.orgId = uo.orgId
                                WHERE uo.userId = ? ");                        
        $query->bind_param("s", $userId);
        $query->execute();
        $result = $query->get_result();
    
        $organisations = [];
        while ($row = $result->fetch_assoc()) {
            $organisations[] = $row;
        }
    }
    

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Organisations retrieved successfully',
        'data' => [
            'organisations' => $organisations
        ]
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed',
        'statusCode' => 405
    ]);
}
?>
