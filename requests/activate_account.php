<?php

require_once "../configuration.php";
require_once "../classes/User.php";
require_once "../classes/Response.php";
require_once "../classes/ResponseElement.php";

session_start();

$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

$_SESSION["user"] = $user = (isset($_SESSION["user"])) ? $_SESSION["user"] : new User();
$response = new Response();


// User already logged in
if ($user->is_logged === true) 
{
    $response->set_status("NO_OK");
    $response->data_add(new ResponseElement("E202", "user"));
    
    die(json_encode($response));
} 


// Handle input
$key = (isset($_GET["key"])) ? $_GET["key"] : null;
$good_key = true;


// Key validation
if ($key !== null)
{
    $key = trim($key);
} 
else 
{
    $response->data_add(new ResponseElement("E301", "key"));
    $good_key = false;
}


if (!$good_key) 
{
    $response->set_status("NO_OK");
    die(json_encode($response));
}





// search in database for key
$query = "SELECT * FROM $db_table_users WHERE activation_key=:key";
$statement = $db->prepare($query);
$statement->bindParam(":key", $key, PDO::PARAM_STR);
$statement->execute();

$user_id = null;
$can_activate = false;
if ($statement->rowCount() > 0) 
{
    $can_activate = true;
    $result = $statement->fetchAll(PDO::FETCH_OBJ);
    $result = $result[0];

    $user_id = $result->user_id;

    if ($result->is_activated == true)
    {
        $can_activate = false;
        $response->data_add(new ResponseElement("E202", "already_activated"));
    }
}
    
if (!$can_activate) {
    $response->set_status("NO_OK");
    die(json_encode($response));
}



$query = "UPDATE $db_table_users SET is_activated=1 WHERE user_id=:user_id";
$statement = $db->prepare($query);
$statement->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$statement->execute();


$response->set_status("OK");
die(json_encode($response));

?>