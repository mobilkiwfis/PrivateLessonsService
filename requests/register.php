<?php

require_once "../configuration.php";
require_once "../classes/User.php";
require_once "../classes/Response.php";
require_once "../classes/ErrorMessage.php";

session_start();

$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

//$_SESSION["user"] = new User();

$user = (isset($_SESSION["user"])) ? $_SESSION["user"] : new User();
$response = new Response();


//var_dump($user);

if ($user->is_logged === true) {
    $response->set_status("NO_OK");
    $response->data_set(new ErrorMessage("E202", "user"));
}

var_dump($response);


?>