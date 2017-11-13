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
        $response->data_add(new ResponseElement("E201", "not_active"));
        
        die(json_encode($response));
    } 
}


// Handle input
$firstname = (isset($_POST["firstname"])) ? $_POST["firstname"] : null;
$change_firstname = false;
$good_firstname = true;
$surname = (isset($_POST["surname"])) ? $_POST["surname"] : null;
$change_surname = false;
$good_surname = true;
$password = (isset($_POST["password"])) ? $_POST["password"] : null;
$change_password = false;
$good_password = true;
$photo = (isset($_POST["photo"])) ? $_POST["photo"] : null;
$change_photo = false;
$good_photo = true;
$phone_number = (isset($_POST["phone_number"])) ? $_POST["phone_number"] : null;
$change_phone_number = false;
$good_phone_number = true;



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

    if ($good_firstname) $change_firstname = true;
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

    if ($good_surname) $change_surname = true;
} 


// Password validation
if ($password !== null)
{
    if (strlen($password) < 6)
    {
        $response->data_add(new ResponseElement("E311", "password"));
        $good_password = false;
    }


    if ($good_password) {
        $password = hash($db_password_encoding, $password);
        $change_password = true;
    }
} 



// Phone number validation
if ($phone_number !== null)
{
    $fpn = array(); // filtred phone number

    if (preg_match("/[0-9]/", $phone_number, $fpn))
    {
        if (count($filtred_phone) === 9)
        {
            $phone_number =
                $fpn[0] . $fpn[1] . $fpn[2] . "-" .
                $fpn[3] . $fpn[4] . $fpn[5] . "-" .
                $fpn[6] . $fpn[7] . $fpn[8];
        } 
        else 
        {
            $response->data_add(new ResponseElement("E310", "phone_number"));
            $good_phone_number = false;
        }
    } 
    else 
    {
        $response->data_add(new ResponseElement("E302", "phone_number"));
        $good_phone_number = false;
    }

    if ($good_phone_number) $change_phone_number = true;
} 




if (!$good_firstname &&
    !$good_surname &&
    !$good_password &&
    !$good_phone_number &&
    !$good_photo) 
{
    $response->set_status("NO_OK");
    die(json_encode($response));
}


if (!$change_firstname ||
    !$change_surname ||
    !$change_password ||
    !$change_phone_number ||
    !$change_photo) 
{
    $response->data_add(new ResponseElement("E301", "no_fields_to_change"));
    $response->set_status("NO_OK");
    die(json_encode($response));
}



$query = "UPDATE $db_table_users SET";

if ($change_firstname) $query .= " firstname=:firstname";
if ($change_surname) $query .= " surname=:surname";
if ($change_password) $query .= " password=:password";
if ($change_phone_number) $query .= " phone_number=:phone_number";
if ($change_photo) $query .= " photo=:photo";

$query .= "WHERE user_id=:db_id";


$statement = $db->prepare($query);
$statement->bindParam(":firstname", $firstname, PDO::PARAM_STR);
$statement->bindParam(":surname", $surname, PDO::PARAM_STR);
$statement->bindParam(":password", $password, PDO::PARAM_STR);
$statement->bindParam(":phone_number", $phone_number, PDO::PARAM_STR);
$statement->bindParam(":db_id", $user->db_id, PDO::PARAM_INT);
$statement->execute();

$response->set_status("OK");
die(json_encode($response));

?>