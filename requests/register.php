<?php

require_once "../configuration.php";
require_once "../classes/User.php";
require_once "../classes/Response.php";
require_once "../classes/ResponseElement.php";

session_start();

$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

//$_SESSION["user"] = new User();
//session_destroy();

$user = (isset($_SESSION["user"])) ? $_SESSION["user"] : new User();
$response = new Response();

//$_SESSION["user"]->is_logged = true;

//var_dump($user);




// User already logged in
if ($user->is_logged === true) {
    $response->set_status("NO_OK");
    $response->data_set(new ResponseElement("E202", "user"));
    
    die(json_encode($response));
} 


// Handle input
$firstname = (isset($_POST["firstname"])) ? $_POST["firstname"] : null;
$good_firstname = true;
$surname = (isset($_POST["surname"])) ? $_POST["surname"] : null;
$good_surname = true;
$password = (isset($_POST["password"])) ? $_POST["password"] : null;
$good_password = true;
$email = (isset($_POST["email"])) ? $_POST["email"] : null;
$good_email = true;

if ($firstname !== null)
{
    $firstname = trim($firstname);

    if (strlen($firstname) < 1)
    {
        // e311
        echo "e311";
        $good_firstname = false;
    } 
    else if (strlen($firstname) > 100)
    {
        // e312
        echo "e312";
        $good_firstname = false;
    }

    if (preg_match("/[0-9]|[^\w\'\` ]|_/", $firstname))
    {
        // e310
        echo "e310";
        $good_firstname = false;
    }
} 
else 
{
    // e301
    echo "e301";
    $good_firstname = false;
}





?>