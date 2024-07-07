<?php

function fetchDataFromDatabase($conn,$sql, $cacheFile) {
    $result = mysqli_query($conn, $sql);
    // Check for errors in database query
    if (!$result) {
        die('MySQL query failed with error: ' . mysqli_error($conn));
    }
    // Fetch all rows as an array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // Serialize and save new data to cache
    file_put_contents($cacheFile, serialize($data));
    // Optionally, use the new data as needed, e.g., return or echo
    return $data;
}

function file_upload($name,$path, $filetype = null) {
    $signature = 'speedloan';
    $maxFileSize = 10 * 1024 * 1024; // 10MB in bytes
    // Validate file size
    if ($_FILES[$name]["size"] > $maxFileSize) {
        return [
            'success' => false,
            'message' => "Image upload failed! File size exceeds the maximum limit of 10MB."
        ];
    }
    // Validate file type (allow only images)
    $allowedImageExtensions = $filetype ?? ['jpg', 'jpeg', 'png', 'gif'];
    $extension = pathinfo($_FILES[$name]["name"], PATHINFO_EXTENSION);

    if (!in_array(strtolower($extension), $allowedImageExtensions)) {
        return [
            'success' => false,
            'message' => "Image upload failed! Only images with extensions: " . implode(', ', $allowedImageExtensions) . " are allowed."
        ];
    }

    $filename = $signature . "_" . uniqid().time();
    $basename = $filename . "." . $extension;
    $source = $_FILES[$name]["tmp_name"];
    $destination = $path . $basename;
    $upload = move_uploaded_file($source, $destination);
    $protocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
    $baseURL = $protocol . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) .'/' . $path;

    if ($upload) {
        return [
            'success' => true,
            'reference' => $baseURL . $basename,
            'message' => "Image uploaded successfully!"
        ];
    } else {
        return [
            'success' => false,
            'message' => "Image upload failed! Please try again later."
        ];
    }
}
function getMailFile($filename, $data) {
    $filePath = __DIR__ . '/../views/mails/' . $filename;
    $fileContent = file_get_contents($filePath);
    foreach ($data as $param => $value) {
        $search = "%$param%";
        $fileContent = str_replace($search, $value, $fileContent);
    }
    return $fileContent;
}
function send_message($phone,$type=1,$message,$sender){
    // Your variables
    $key=TEXTNG_MESSAGE_API_KEY;
    $route="2";
    if ($type == 1){
        $response = httpPost("https://api.textng.xyz/otp-sms/",array("key"=>"$key","phone"=>"$phone","message"=>"$message","route"=>"$route","sender"=>"$sender","siscb" => 1));
    }
    
    //Getting the Reference Number 
    // extract text in front of "Reference:"
    $reference_text = "";
    if (preg_match("/Reference:(.*?)\s*\|\|/", $response, $matches)) {
        $reference_text = trim($matches[1]);
    }
    return $response;
}
function httpPost($url, $data){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function monthDifference($startDate, $endDate) {
    // Convert string dates to DateTime objects
    $startDateTime = new DateTime($startDate);
    $endDateTime = new DateTime($endDate);
    // Calculate the difference in months
    $interval = $startDateTime->diff($endDateTime);
    $months = $interval->y * 12 + $interval->m;
    return $months;
}
function sendMail($email,$message,$subject){
    include_once  __DIR__ . '/../middlewares/mailing.php';
        // To send HTML mail, you need to set the Content-Type header
                try {
            //Server settings for Gmail
            //Recipient
            $mail->setFrom(MAILING_EMAIL_ADDRESS, MAILING_FROM); // Your Gmail email address and name
            $mail->addAddress($email); // Recipient email address and name
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            // Send email
            $mail->send();
            return true;
        } catch (Exception $e) {
             echo $e->getMessage();
            return false;
        }
    }
function createKey() { 
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ023456789"; 
    srand((double)microtime()*1000000); 
    $i = 0; 
    $pass = '' ; 
    while ($i <= 10) { 
        $num = rand() % 33; 
        $tmp = substr($chars, $num, 1); 
        $pass = $pass . $tmp; 
        $i++; 
    } 
     return $pass; 
}
function cleanme($data) {
    global $connect;
    $input = $data;
    // This removes all the HTML tags from a string. This will sanitize the input string, and block any HTML tag from entering into the database.
    // filter_var($geeks, FILTER_SANITIZE_STRING);
    $input = filter_var($input, FILTER_SANITIZE_STRING);
    $input = trim($input, " \t\n\r");
    // htmlspecialchars() convert the special characters to HTML entities while htmlentities() converts all characters.
    // Convert the predefined characters "<" (less than) and ">" (greater than) to HTML entities:
    $input = htmlspecialchars($input, ENT_QUOTES,'UTF-8');
    // prevent javascript codes, Convert some characters to HTML entities:
    $input = htmlentities($input, ENT_QUOTES, 'UTF-8');
    $input = stripslashes(strip_tags($input));
    $input = mysqli_real_escape_string($connect, $input);
    return $input;
}
function showpost($text) {
    $text = str_replace("\\r\\n", "", $text);
    $text = trim(preg_replace('/\t+/', '', $text));
    
    $text = htmlspecialchars_decode($text, ENT_QUOTES);
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    $text = htmlspecialchars_decode($text, ENT_QUOTES);
    $text = nl2br($text);
    return $text;
}

function getIp(){  
    //whether ip is from the share internet  
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
        $ip = $_SERVER['HTTP_CLIENT_IP'];  
    }  
    //whether ip is from the proxy  
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
    }  
    //whether ip is from the remote address  
    else{  
        $ip = $_SERVER['REMOTE_ADDR'];  
    }  
    return $ip;  
}
function getBrowser() { 
  $u_agent = $_SERVER['HTTP_USER_AGENT'];
  $bname = 'Unknown';
  $platform = 'Unknown';
  $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    $ub="";
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }elseif(preg_match('/Firefox/i',$u_agent)){
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }elseif(preg_match('/OPR/i',$u_agent)){
        $bname = 'Opera';
        $ub = "Opera";
    }elseif(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
        $bname = 'Apple Safari';
        $ub = "Safari";
    }elseif(preg_match('/Netscape/i',$u_agent)){
        $bname = 'Netscape';
        $ub = "Netscape";
    }elseif(preg_match('/Edge/i',$u_agent)){
        $bname = 'Edge';
        $ub = "Edge";
    }elseif(preg_match('/Trident/i',$u_agent)){
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }else {
            $version= $matches['version'][1];
        }
    }else {
        $version= $matches['version'][0];
    }

    // check if we have a number
    if ($version==null || $version=="") {$version="?";}

    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
} 
function getDatetimethatPasssed($endday){
    $todayis=date("Y-m-d");
    $earlier = new DateTime("$endday");
    $later = new DateTime("$todayis");
    $abs_diff = $later->diff($earlier)->format("%a"); //3
    return $abs_diff;
}


function getDaysPassed($vendorsubendday){
    //155555444545
    $datediff =time()-$vendorsubendday;
    
    //60 is for minute
    //60 by 60 is for hr
    //60 by 60 by 24 is for days
    //any number by 60 by 60 by 24 is for months
    $difference = round($datediff/(24 * 60 *60));//getting days
    return $difference;
}



function getMinBetweentimes($latesttime,$oldtime){
    $minbtwis=0;
    $subtractit=$latesttime-$oldtime;
    $minbtwis= round($subtractit/(60));
    return $minbtwis;
}


function addDaysToTime($day,$time){
   $currentTime = $time;
   //The amount of hours that you want to add.
   $daysToAdd = $day;
   //Convert the hours into seconds.
   $secondsToAdd = $daysToAdd * (24 * 60* 60);
   //Add the seconds onto the current Unix timestamp.
   $newTime = $currentTime + $secondsToAdd;
   return $newTime;
}



function generate_string($input, $strength) {
    $input_length = strlen($input);
    $random_string = '';
    for ($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}

function generateRandomDigits($num) {
    $result = '';

    for ($i = 0; $i < $num; $i++) {
        $result .= mt_rand(0, 9); // Append a random digit (0 to 9) to the string
    }

    return $result;
}


function convertTime($time) {
    //88734873489 
    $data = $time;
    $date = strtotime($data);
    return $date;
}

//Has password function starts here
function Password_encrypt($user_pass) {
    $BlowFish_Format="$2y$10$";
    $salt_len=24;
    $salt=Get_Salt($salt_len);
    $the_format=$BlowFish_Format . $salt;
    
    $hash_pass=crypt($user_pass, $the_format);
    return $hash_pass;
}

function Get_Salt($size) {
    $Random_string= md5(uniqid(mt_rand(), true));
    
    $Base64_String= base64_encode($Random_string);
    
    $change_string=str_replace('+', '.', $Base64_String);
    
    $salt=substr($change_string, 0, $size);
    
    return $salt;
}

function check_pass($pass, $storedPass) {
    $Hash=crypt($pass, $storedPass);
    if ($Hash===$storedPass) {
        return(true);
    } else {
        return(false);
    }
}
function validatePhone($phone) {
    $pattern = '/^(\+?233)\d{9}$/';
    if (preg_match($pattern, $phone)) {
        return true; // The phone number is valid
    } else {
        return false; // The phone number is invalid
    }
}



    function validateEmail($email) {
        if ( filter_var($email, FILTER_VALIDATE_EMAIL) ){
            return true;
        }else{
            return false;
        }
    }

    function validatePassword($password){
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 6) {
            return false;
        }else{
            return true;
        }
    }
    
    function checkIfUsernameisEmailorPhone($username){
       $phone =  (validatePhone($username)) ? 'phone': null;
       $email = (filter_var($username, FILTER_VALIDATE_EMAIL)) ? 'email' : null;

       if ($phone){
        return $phone;
       }

       if ($email){
        return $email;
       }

    }

    function getIPAddress() {  
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }  

    #GET USER COORDINATES
    function getLoc($userIp){
        $url = "http://ipinfo.io/".$userIp."/geo";
        $json     = file_get_contents($url);
        $json     = json_decode($json, true);
        $country  = ($json['country']) ?  $json['country'] : "";
        $region   = ($json['region']) ? $json['region'] : "";
        $city     = ($json['city']) ? $json['city'] : "";
        $location = ($json['loc']) ? $json['loc'] : "";

        return $location;
    }
    function generateUniqueId($len) {
        // Generate a prefix based on the current time in microseconds
        $prefix = uniqid();
    
        // Generate a random number and convert it to base 36 for extra randomness
        $randomNum = base_convert(rand(0, pow(36, $len) - 1), 10, 36);
    
        // Concatenate and return the unique ID
        return $prefix . $randomNum;
    }
    
    function generatePubKey($strength){
        $input = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $output = generate_string($input, $strength);
        return $output;
    }

    function generateUserPubKey($connect){
        $loop = 0;
        while ($loop == 0){
            $userKey = "TEXTNG".generatePubKey(37);
            if ( checkIfPubKeyisInDB($connect, $userKey) ){
                $loop = 0;
            }else {
                $loop = 1;
                break;
            }
        }
        return $userKey;
    }

    function checkIfPubKeyisInDB($connect, $pubkey) {
        // Check if the email or phone number is already in the database
        $query = 'SELECT * FROM users WHERE userpubkey = ?';
        $stmt = $connect->prepare($query);
        $stmt->bind_param("s", $pubkey);
        $stmt->execute();
        $result = $stmt->get_result();
        $num_row = $result->num_rows;
        if ($num_row > 0){
            return true;
        }
        return false;
    }

    function checkIfUserisInDB($connect, $user_id) {
        // Check if the email or phone number is already in the database
        $query = 'SELECT * FROM users WHERE id = ?';
        $stmt = $connect->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $num_row = $result->num_rows;

        if ($num_row > 0){
            return true;
        }
        return false;
    }

    function getUserIdWithPubKey($connect, $userpubkey) {
        // Check if the email or phone number is already in the database
        $query = 'SELECT * FROM users WHERE userpubkey = ?';
        $stmt = $connect->prepare($query);
        $stmt->bind_param("s", $userpubkey);
        $stmt->execute();
        $result = $stmt->get_result();
        $num_row = $result->num_rows;

        if ($num_row > 0){
            $row =  mysqli_fetch_assoc($result);
            $user_id = $row['id'];
            return $user_id;
        }
        return false;
    }

    #Inserting Session into the Database
    function addSessionLog($conn, $email, $sessioncode, $ipaddress, $browser, $date, $location, $method, $endpoint) {
        // set status to 1
        $status = 1;
        // Insert seesion log query
        $query = 'INSERT INTO usersessionlog (email, sessioncode, ipaddress, browser, date , status, location) Values (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssis", $email, $sessioncode, $ipaddress, $browser, $date, $status, $location);

        if( $stmt->execute() ){
            return true;
        }
        $errordesc =  $stmt->error;
        $linktosolve = 'https://';
        $hint = "500 code internal error, check ur database connections";
        $errorData = returnError7003($errordesc, $linktosolve, $hint);
        $data = returnErrorArray($errordesc, $method, $endpoint, $errorData, null);
        respondInternalError($data);
    }

function generateRandomRef(){
    $input = "1234756789098765421789512357";
    $strength= 17;
    $id = generate_string($input, $strength);
    return $id;
}

function checkifFieldExist($connect, $table, $field, $data){
    // check field
    $query = "SELECT * FROM $table WHERE $field = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("s", $data );
    $stmt->execute();
    $result = $stmt->get_result();
    $num_row = $result->num_rows;
    if ($num_row > 0){
       return true;
    }
    return false;
}
function checkif2FieldsExist($connect, $table, $field,$data,$field2,$data2){
    // check field
    $query = "SELECT * FROM $table WHERE $field = ? AND $field2 = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("ss", $data ,$data2 );
    $stmt->execute();
    $result = $stmt->get_result();
    $num_row = $result->num_rows;
    if ($num_row > 0){
       return true;
    }
    return false;
}
function getUserWithPubKey($connect, $userpubkey) {
        // Check if the email or phone number is already in the database
        $query = 'SELECT * FROM users WHERE userpubkey = ?';
        $stmt = $connect->prepare($query);
        $stmt->bind_param("s", $userpubkey);
        $stmt->execute();
        $result = $stmt->get_result();
        $num_row = $result->num_rows;
        if ($num_row > 0){
            $row =  mysqli_fetch_assoc($result);
            $user_id = $row['id'];
            return $user_id;
        }

        return false;
    }
function getEmailWithPubKey($connect, $userpubkey) {
        // Check if the email or phone number is already in the database
        $query = 'SELECT * FROM users WHERE userpubkey = ?';
        $stmt = $connect->prepare($query);
        $stmt->bind_param("s", $userpubkey);
        $stmt->execute();
        $result = $stmt->get_result();
        $num_row = $result->num_rows;

        if ($num_row > 0){
            $row =  mysqli_fetch_assoc($result);
            $email = $row['email'];
            return $email;
        }
        return false;
    }
function getAnyData($conn, $table,$where,$to,$data = "*") {
        // Check if the email or phone number is already in the database
        $query = 'SELECT '.$data.' FROM '.$table.' WHERE '.$where.' = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s",$to);
        $stmt->execute();
        $result = $stmt->get_result();
        $num_row = $result->num_rows;
        if ($num_row > 0){
            $row =  mysqli_fetch_assoc($result);
            $info = $data !="*" ? $row[$data] : $row;
            return $info;
        }
        return [];
    }
function sendanymail($emailfrom,$subject,$toemail,$messageinhtml){
    $connect = $GLOBALS['connect'];
$email = $toemail;
$userName='';
$sql ="SELECT email FROM users WHERE email = '$email' ";
 $select= mysqli_query($connect,$sql);
  if($select){
  if(mysqli_num_rows($select)==1)
  {         $row=mysqli_fetch_assoc($select);
            $email = $row['email'];
            $title = "Marasoft Pay - Password Reset";
            $title = urlencode($title);
            $message = urlencode($messageinhtml);
            $mail= file_get_contents("https://marasoftbanking.com/emailer/broadcast_mail.php?body=$message&email=$email&title=$title");
            if($mail){
                return true;
            } else {
                return false;
            }
            //create an html stuff that echo this..
      
    
  }else{
      echo "<div class='alert alert-danger' role='alert'>There is no account associated with this email address.</div>";
}
}


}