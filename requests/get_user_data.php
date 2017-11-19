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



// Handle input
$email = (isset($_GET["email"])) ? $_GET["email"] : null;
$good_email = true;
$user_id = (isset($_GET["user_id"])) ? $_GET["user_id"] : null;
$good_user_id = true;



// Validate email
if ($email !== null) {
    $email = trim($email);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);

    if ($email === false)
    {
        $good_email = false;
        $response->set_status("NO_OK");
        $response->data_add(new ResponseElement("E310", "email"));
    }
} 
else 
{
    $good_email = false;
}


// Validate id
if ($user_id !== null) {
    $user_id = trim($user_id); // Remove spaces from begining and ending
    $user_id = intval($user_id); // To int

    if ($user_id < 0)
    {
        $good_user_id = false;
        $response->set_status("NO_OK");
        $response->data_add(new ResponseElement("E310", "user_id"));
    }
} 
else 
{
    $good_user_id = false;
}





// No email or id was set, it means user asks for its own data.
if (!$good_email && !$good_user_id) {
    $user->pull_data($db);

    // User not logged in
    if ($user->is_logged === false) 
    {
        $good_user_id = false; // just to make sure
        $response->set_status("NO_OK");
        $response->data_add(new ResponseElement("E301", "user"));
    }
    else 
    {
        $good_user_id = true;
        $user_id = $user->user_id;
    }
}


if (!$good_email && !$good_user_id)
{
    die(json_encode($response));
} // else means at least one is good



// search in database for user
$query = "SELECT * FROM $db_table_users WHERE ";
$statement = null;

if ($good_user_id)
{
    $query .= "user_id=:user_id";
    $statement = $db->prepare($query);
    $statement->bindParam(":user_id", $user_id, PDO::PARAM_STR);
} 
else if ($good_email)
{
    $query .= "email=:email";
    $statement = $db->prepare($query);
    $statement->bindParam(":email", $email, PDO::PARAM_STR);
}

$statement->execute();



if ($statement->rowCount() > 0) 
{
    $result = $statement->fetchAll(PDO::FETCH_OBJ);
    $result = $result[0];

    unset($result->password);
    unset($result->activation_key);

    $response->set_status("OK");
    $response->data_set($result);
} 
else 
{
    $response->set_status("NO_OK");
    $response->data_add(new ResponseElement("E321", "user"));
}

die(json_encode($response));

?>