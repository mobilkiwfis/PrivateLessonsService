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


// No email was set, it means user asks for its own data.
if ($email === null) {
    $user->pull_data($db);

    // User not logged in
    if ($user->is_logged === false) 
    {
        $good_email = false;
        $response->set_status("NO_OK");
        $response->data_add(new ResponseElement("E301", "email"));
    }
    else 
    {
        $good_email = true;
        $email = $user->email;
    }
} 
else
{
    $email = trim($email);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);

    if ($email === false)
    {
        $good_email = false;
        $response->set_status("NO_OK");
        $response->data_add(new ResponseElement("E310", "email"));
    }
} 

if (!$good_email)
{
    die(json_encode($response));
}




// search in database for user
$query = "SELECT * FROM $db_table_users WHERE email=:email";
$statement = $db->prepare($query);
$statement->bindParam(":email", $email, PDO::PARAM_STR);
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
    $response->data_add(new ResponseElement("E321", "email"));
}

die(json_encode($response));

?>