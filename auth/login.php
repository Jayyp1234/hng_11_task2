<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Send some CORS headers so the API can be called from anywhere
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Cache-Control: no-cache");

include_once "../config/response.php";
include_once "../config/database.php";
include_once "../utilities/functions.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    $email = isset($data['email']) ? cleanme($data['email']) : '';
    $password = isset($data['password']) ? cleanme($data['password']) : '';

    if (empty($email) || empty($password)) {
        http_response_code(401);
        echo json_encode([
            'status' => 'Bad request',
            'message' => 'Email and password are required',
            'statusCode' => 401
        ]);
        exit;
    }

    $checkdata = $connect->prepare("SELECT * FROM Users WHERE email=? ");
    $checkdata->bind_param("s", $email);
    $checkdata->execute();
    $result = $checkdata->get_result();
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $userId = $user['publicKey'];
            $firstname = $user['firstName'];
            $lastname = $user['lastName'];
            $email = $user['email'];
            $phone = $user['phone'];

            $myloc = 1;
            $sysgetdata = $connect->prepare("SELECT * FROM apidatatable WHERE id=?");
            $sysgetdata->bind_param("s", $myloc);
            $sysgetdata->execute();
            $dsysresult7 = $sysgetdata->get_result();
            $getsys = $dsysresult7->fetch_assoc();

            $companyprivateKey = $getsys['privatekey'];
            $minutetoend = $getsys['tokenexpiremin'];
            $serverName = $getsys['servername'];
            $sysgetdata->close();

            include_once '../middlewares/auth.php';
            $tokendata = getTokenToSendAPI($userId, $companyprivateKey, $minutetoend, $serverName);

            if ($tokendata['success'] == true) {
                $token = $tokendata['data']['token'];
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful',
                    'data' => [
                        'accessToken' => $token,
                        'user' => [
                            'userId' => $userId,
                            'firstName' => $firstname,
                            'lastName' => $lastname,
                            'email' => $email,
                            'phone' => $phone,
                        ]
                    ]
                ]);
                return;
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error occurred, try again!',
                    'statusCode' => 500
                ]);
                return;
            }
        } else {
            http_response_code(401);
            echo json_encode([
                'status' => 'Bad request',
                'message' => 'Authentication failed',
                'statusCode' => 401
            ]);
            exit;
        }
    } else {
        http_response_code(401);
        echo json_encode([
            'status' => 'Bad request',
            'message' => 'Authentication failed',
            'statusCode' => 401
        ]);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed',
        'statusCode' => 405
    ]);
    return;
}
?>
