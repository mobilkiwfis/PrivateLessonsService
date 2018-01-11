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

$user->pull_data($db);


// User not logged in
if ($user->is_logged === false) 
{
    $response->set_status("NO_OK");
    $response->data_add(new ResponseElement("E201", "user"));
    
    die(json_encode($response));
} 
else 
{
    if ($user->is_banned === true) 
    {
        $response->set_status("NO_OK");
        $response->data_add(new ResponseElement("E201", "banned"));
        
        die(json_encode($response));
    } 

    if ($user->is_activated === false) 
    {
        $response->set_status("NO_OK");
        $response->data_add(new ResponseElement("E201", "not_activated"));
        
        die(json_encode($response));
    } 
}


// Handle input
$offer_id = (isset($_POST["offer_id"])) ? $_POST["offer_id"] : null;
$good_offer_id = true;



// Validate offer_id
if ($offer_id !== null) {
    $offer_id = trim($offer_id);
    $offer_id = intval($offer_id); // To int

    if ($offer_id < 0)
    {
        $good_offer_id = false;
        $response->data_add(new ResponseElement("E310", "offer_id"));
    }
} 
else 
{
    $good_offer_id = false;
    $response->data_add(new ResponseElement("E301", "offer_id"));
}


if (!$good_offer_id)
{
    $response->set_status("NO_OK");
    die(json_encode($response));
}



// search in database for user
$query = "SELECT * FROM $db_table_offers WHERE offer_id=:offer_id AND owner_id=:owner_id LIMIT 1";
$statement = $db->prepare($query);
$statement->bindParam(":offer_id", $offer_id, PDO::PARAM_INT);
$statement->bindParam(":owner_id", $user->user_id, PDO::PARAM_INT);
$statement->execute();

$offer_data = null;

if ($statement->rowCount() > 0) 
{
    $offer_data = $statement->fetchAll(PDO::FETCH_OBJ);
} 
else 
{
    $response->set_status("NO_OK");
    $response->data_add(new ResponseElement("E321", "offer_id"));
    die(json_encode($response));
}

$status = new stdClass();
$status->url = $offer_url;
$status->offer_callback_url = "$offer_callback_url?offer_id=$offer_id";
$status->business = $offer_business;
$status->offer_id = $offer_id;
$status->duration = $two_weeks;
$status->price = $offer_upgrade_price;
$status->currency = $offer_upgrade_currency;
$response->data_set($status);


$response->set_status("OK");
die(json_encode($response));

?>