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
$firstname = (isset($_POST["firstname"])) ? $_POST["firstname"] : null;
$good_firstname = true;
$surname = (isset($_POST["surname"])) ? $_POST["surname"] : null;
$good_surname = true;
$password = (isset($_POST["password"])) ? $_POST["password"] : null;
$good_password = true;
$email = (isset($_POST["email"])) ? $_POST["email"] : null;
$good_email = true;


// Firstname validation
if ($firstname !== null)
{
    $firstname = trim($firstname); // Remove spaces from begining and ending
    $firstname = preg_replace("/\s+/", " ", $firstname); // Multiple white characters to space

    if (strlen($firstname) < 1)
    {
        $response->data_add(new ResponseElement("E311", "firstname"));
        $good_firstname = false;
    } 
    else if (strlen($firstname) > 100)
    {
        $response->data_add(new ResponseElement("E312", "firstname"));
        $good_firstname = false;
    }

    if (preg_match("/[0-9]|[^\w\'\` ]|_/", $firstname))
    {
        $response->data_add(new ResponseElement("E310", "firstname"));
        $good_firstname = false;
    }
} 
else 
{
    $response->data_add(new ResponseElement("E301", "firstname"));
    $good_firstname = false;
}




// Surname validation
if ($surname !== null)
{
    $surname = trim($surname); // Remove spaces from begining and ending
    $surname = preg_replace("/\s+/", " ", $surname); // Multiple white characters to space

    if (strlen($surname) < 1)
    {
        $response->data_add(new ResponseElement("E311", "surname"));
        $good_surname = false;
    } 
    else if (strlen($surname) > 100)
    {
        $response->data_add(new ResponseElement("E312", "surname"));
        $good_surname = false;
    }

    if (preg_match("/[0-9]|[^\w\'\` ]|_/", $surname))
    {
        $response->data_add(new ResponseElement("E310", "surname"));
        $good_surname = false;
    }
} 
else 
{
    $response->data_add(new ResponseElement("E301", "surname"));
    $good_surname = false;
}



// Password validation
if ($password !== null)
{
    if (strlen($password) < 6)
    {
        $response->data_add(new ResponseElement("E311", "password"));
        $good_password = false;
    }
} 
else 
{
    $response->data_add(new ResponseElement("E301", "password"));
    $good_password = false;
}




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





if ($good_firstname &&
    $good_surname &&
    $good_password &&
    $good_email) 
{
    // Database validation
    $query = "SELECT * FROM $db_table_users WHERE email=:email";
    $statement = $db->prepare($query);
    $statement->bindParam(":email", $email, PDO::PARAM_STR);
    $statement->execute();

    if ($statement->rowCount() > 0)
    {
        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        foreach($result as $row) 
        {
            if ($row->is_activated == true)
            {
                // email registered, forgot password?
                $response->set_status("NO_OK");
                $response->data_add(new ResponseElement("E320", "email"));
                die(json_encode($response));
            } 
            else
            {
                // email registered, resend activation link?
                $response->set_status("NO_OK");
                $response->data_add(new ResponseElement("E320", "email"));
                die(json_encode($response));
            } 
        }
    } 
    else
    {
        // Encode
        $password = hash($db_password_encoding, $password);
        $time_stamp = null;
        $time_stamp_db = null;
        $activation_key = null;

        $query = "SELECT activation_key FROM $db_table_users WHERE activation_key=:activation_key";
        $statement = $db->prepare($query);

        do {
            $time_stamp = microtime(true);
            $time_stamp_db = date($db_date_format, $time_stamp);
            $activation_key = hash($db_password_encoding, $time_stamp); 
            $statement->bindParam(":activation_key", $activation_key, PDO::PARAM_STR);
            $statement->execute();
        } while ($statement->rowCount() > 0);


        $query = "INSERT INTO $db_table_users (
            `user_id`,
            `firstname`,
            `surname`,
            `password`,
            `email`,
            `photo`,
            `phone_number`,
            `creation_timestamp`,
            `is_activated`,
            `activation_key`,
            `is_banned`
        ) VALUES (
            null,
            :firstname,
            :surname,
            :password,
            :email,
            'default_avatar.jpg',
            null,
            :creation_timestamp,
            '0',
            :activation_key,
            '0'
        )";
        $statement = $db->prepare($query);
        $statement->bindParam(":firstname", $firstname, PDO::PARAM_STR);
        $statement->bindParam(":surname", $surname, PDO::PARAM_STR);
        $statement->bindParam(":password", $password, PDO::PARAM_STR);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->bindParam(":creation_timestamp", $time_stamp_db, PDO::PARAM_STR);
        $statement->bindParam(":activation_key", $activation_key, PDO::PARAM_STR);
        $statement->execute();



        $response->set_status("OK");
        die(json_encode($response));
    } 
} 
else
{
    $response->set_status("NO_OK");
    die(json_encode($response));
}


?>