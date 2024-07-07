<?php
date_default_timezone_set('Africa/Lagos');

$HOSTNAME = "localhost";
$USERNAME = "sunshi18_johnpaul";
$PASSWORD = "w&o1W]_X+w}0";
$DATABASE = "sunshi18_hng";

$connect = $conn =  new mysqli($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if ($conn -> connect_error) {
	die( "Connection to Database Failed");
	# code...
}
