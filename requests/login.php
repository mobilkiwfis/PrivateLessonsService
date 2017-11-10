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


// User already logged in
if ($user->is_logged === true) 
{
    $response->set_status("NO_OK");
    $response->data_add(new ResponseElement("E202", "user"));
    
    die(json_encode($response));
} 


// Handle input
$email = (isset($_POST["email"])) ? $_POST["email"] : null;
$good_email = true;
$password = (isset($_POST["password"])) ? $_POST["password"] : null;
$good_password = true;


// Email validation
if ($email !== null)
{
    $email = trim($email); // Remove spaces from begining and ending
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);

    if ($email === false)
    {
        $response->data_add(new ResponseElement("E310", "email"));
        $good_email = false;
    }
} 
else 
{
    $response->data_add(new ResponseElement("E301", "email"));
    $good_email = false;
}



// Password validation
if ($password !== null)
{
    // Nothing here, accept password as it is
} 
else 
{
    $response->data_add(new ResponseElement("E301", "password"));
    $good_password = false;
}










if ($good_email &&
    $good_password) 
{
    // Encode
    $password = hash($db_password_encoding, $password);
    
    // search in database for user
    $query = "SELECT * FROM $db_table_users WHERE email=:email AND password=:password";
    $statement = $db->prepare($query);
    $statement->bindParam(":email", $email, PDO::PARAM_STR);
    $statement->bindParam(":password", $password, PDO::PARAM_STR);
    $statement->execute();



    if ($statement->rowCount() > 0) 
    {
        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        $result = $result[0];

        $can_log_in = true;

        if ($result->is_activated != true)
        {
            $can_log_in = false;
            $response->data_add(new ResponseElement("E201", "not_active"));
        }
        
        if ($result->is_banned == true)
        {
            $can_log_in = false;
            $response->data_add(new ResponseElement("E201", "banned"));
        }

        if ($can_log_in)
        {
            $user->db_id = $result->user_id;
            $user->is_logged = true;

            $_SESSION["user"] = $user;

            $response->set_status("OK");
            die(json_encode($response));
        } 
        else
        {
            $response->set_status("NO_OK");
            die(json_encode($response));
        } 
    }
    else 
    {
        $response->data_add(new ResponseElement("E300", "user"));
        $response->set_status("NO_OK");
        die(json_encode($response));
    }
} 
else
{
    $response->set_status("NO_OK");
    die(json_encode($response));
}


?>