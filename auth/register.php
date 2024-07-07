<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Send some CORS headers so the API can be called from anywhere
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST"); // OPTIONS,GET,POST,PUT,DELETE
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
    $firstname = isset($data['firstName']) ? cleanme($data['firstName']) : '';
    $lastname = isset($data['lastName']) ? cleanme($data['lastName']) : '';
    $phone = isset($data['phone']) ? cleanme($data['phone']) : '';

    $errors = [];

    if (empty($firstname)) {
        $errors[] = ['field' => 'firstname', 'message' => 'First name is required'];
    }
    if (empty($lastname)) {
        $errors[] = ['field' => 'lastname', 'message' => 'Last name is required'];
    }
    if (empty($phone)) {
        $errors[] = ['field' => 'phone', 'message' => 'Phone number is required'];
    }
    if (empty($email)) {
        $errors[] = ['field' => 'email', 'message' => 'Email address is required'];
    } else {
        if (!validateEmail($email)) {
            $errors[] = ['field' => 'email', 'message' => 'Invalid Email address.'];
        }
    }
    if (empty($password)) {
        $errors[] = ['field' => 'password', 'message' => 'Password is required'];
    } else {
        if (!validatePassword($password)) {
            $errors[] = ['field' => 'password', 'message' => 'Password must contain upper case, lower case, special character and number and must not be less than 6 in length'];
        }
    }

    if (count($errors) > 0) {
        http_response_code(422);
        echo json_encode([
            'status' => 'error',
            'message' => 'Some data are not valid.',
            'errors' => $errors,
            'statusCode' => 422,
        ]);
        exit;
    } else {
        $checkdata = $connect->prepare("SELECT * FROM Users WHERE email=? ");
        $checkdata->bind_param("s", $email);
        $checkdata->execute();
        $dresult = $checkdata->get_result();
        if ($dresult->num_rows == 0) {
            $userId = generatePubKey(5) . time();
            $status = 1;
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $insert_data = $connect->prepare("INSERT INTO Users (userId, email, firstName, lastName, password, publicKey, status, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_data->bind_param("sssssssi", $userId, $email, $firstname, $lastname, $hashed, $userId, $status, $phone);
            $insert_data->execute();
            $insert_data->close();

            // Create organisation with the user's first name
            $orgId = generatePubKey(5) . time();
            $orgName = $firstname . "'s Organisation";
            $insert_org_data = $connect->prepare("INSERT INTO Organisations (orgId, name) VALUES (?, ?)");
            $insert_org_data->bind_param("ss", $orgId, $orgName);
            $insert_org_data->execute();
            $insert_org_data->close();
            
            // Link user to the organisation
            $link_user_org = $connect->prepare("INSERT INTO User_Organisation (userId, orgId) VALUES (?, ?)");
            $link_user_org->bind_param("ss", $userId, $orgId);
            $link_user_org->execute();
            $link_user_org->close();

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
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Registration successful',
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
            http_response_code(422);
            echo json_encode([
                'status' => 'error',
                'message' => 'Email already exists in the database',
                'errors' => [['field' => 'email', 'message' => 'Email already exists in the database']],
                'statusCode' => 422
            ]);
            exit;
        }
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
