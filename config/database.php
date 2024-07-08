<?php
date_default_timezone_set('Africa/Lagos');

$HOSTNAME = "localhost";
$USERNAME = "";
$PASSWORD = "";
$DATABASE = "";

$connect = $conn =  new mysqli($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if ($conn -> connect_error) {
	die( "Connection to Database Failed");
	# code...
}
