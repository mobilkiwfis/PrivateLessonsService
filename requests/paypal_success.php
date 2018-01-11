<?php

require_once "../configuration.php";
require_once "../classes/User.php";
require_once "../classes/Response.php";
require_once "../classes/ResponseElement.php";

session_start();

$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

$response = new Response();



// Handle input
$offer_id = (isset($_GET["offer_id"])) ? $_GET["offer_id"] : null;
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
$query = "SELECT * FROM $db_table_offers WHERE offer_id=:offer_id LIMIT 1";
$statement = $db->prepare($query);
$statement->bindParam(":offer_id", $offer_id, PDO::PARAM_INT);
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

@$time_expires_db = $offer_data->promoted_expire_timestamp;
if ($time_expires_db) {
    $time_expires_db = strtotime($time_expires_db);
} else {
    $time_expires_db = microtime(true);
}
$time_expires_db += $two_weeks;

$time_expires_db = date($db_date_format, $time_expires_db);



$query = "UPDATE $db_table_offers SET promoted_expire_timestamp=:promoted_expire_timestamp WHERE offer_id=:offer_id";
$statement = $db->prepare($query);
$statement->bindParam(":offer_id", $offer_id, PDO::PARAM_INT);
$statement->bindParam(":promoted_expire_timestamp", $time_expires_db, PDO::PARAM_STR);
$statement->execute();


$response->data_add(new ResponseElement("expires", $time_expires_db));
$response->set_status("OK");
die(json_encode($response));

?>