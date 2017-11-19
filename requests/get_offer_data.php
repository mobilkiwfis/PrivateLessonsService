<?php

require_once "../configuration.php";
require_once "../classes/User.php";
require_once "../classes/Response.php";
require_once "../classes/ResponseElement.php";

session_start();

$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

//$_SESSION["user"] = $user = (isset($_SESSION["user"])) ? $_SESSION["user"] : new User();
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



if ($statement->rowCount() > 0) 
{
    $result = $statement->fetchAll(PDO::FETCH_OBJ);
    $result = $result[0];

    $response->set_status("OK");
    $response->data_set($result);
} 
else 
{
    $response->set_status("NO_OK");
    $response->data_add(new ResponseElement("E321", "offer_id"));
}

die(json_encode($response));

?>