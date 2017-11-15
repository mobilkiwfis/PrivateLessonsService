<?php

require_once "../configuration.php";
require_once "../classes/User.php";
require_once "../classes/Response.php";
require_once "../classes/ResponseElement.php";

session_start();

$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

$user = (isset($_SESSION["user"])) ? $_SESSION["user"] : new User();
$response = new Response();

$status = $user->logout($response);
$response->merge_with($status);

die(json_encode($response));

?>