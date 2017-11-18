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
$offer_belongs_to_user = true;
$owner_id = null;
$offer_expires = null;
$good_offer_expires_delta = true;


// Offer_id validation
if ($offer_id !== null)
{
    $offer_id = trim($offer_id); // Remove spaces from begining and ending
    $offer_id = intval($offer_id); // To int
    
    $query = "SELECT * FROM $db_table_offers WHERE offer_id=:offer_id";
    $statement = $db->prepare($query);
    $statement->bindParam(":offer_id", $offer_id, PDO::PARAM_INT);
    $statement->execute();

    if ($statement->rowCount() > 0) 
    {
        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        $result = $result[0];

        $owner_id = $result->owner_id;
        $owner_id = intval($owner_id);

        $offer_expires = $result->visibility_expire_timestamp;
        $offer_expires = strtotime($offer_expires);
    } 
    else 
    {
        $response->data_add(new ResponseElement("E321", "offer_id"));
        $good_offer_id = false;
    }
} 
else
{
    $response->data_add(new ResponseElement("E301", "offer_id"));
    $good_offer_id = false;
}


if (!$good_offer_id)
{
    $response->set_status("NO_OK");
    die(json_encode($response));
}

if ($owner_id !== $user->user_id)
{
    $response->data_add(new ResponseElement("E200", "offer_not_belongs_to_user"));
    $offer_belongs_to_user = false;
}


$time_stamp = microtime(true);
//$offer_expires_delta = $offer_expires - $time_stamp - $one_week;

if ($offer_expires - $time_stamp > $one_week)
{
    $response->data_add(new ResponseElement("E200", "offer_cannot_be_bumped_yet"));
    //$response->data_add(new ResponseElement("can_bump_in", $offer_expires_delta));
    $good_offer_expires_delta = false;
}


if (!($offer_belongs_to_user &&
    $good_offer_expires_delta))
{
    $response->set_status("NO_OK");
    die(json_encode($response));
}


$time_expires_db = date($db_date_format, $time_stamp + $two_weeks);

$query = "UPDATE $db_table_offers SET 
    visibility_expire_timestamp=:visibility_expire_timestamp
    WHERE 
    offer_id=:offer_id";
$statement = $db->prepare($query);
$statement->bindParam(":visibility_expire_timestamp", $time_expires_db, PDO::PARAM_STR);
$statement->bindParam(":offer_id", $offer_id, PDO::PARAM_INT);
$statement->execute();

//$response->data_add(new ResponseElement("will_expire_at", $time_expires_db));
$response->set_status("OK");
die(json_encode($response));

?>