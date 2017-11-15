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

    if (preg_match_all("/[0-9]/", $phone_number, $fpn))
    {
        $fpn = $fpn[0];

        if (count($fpn) === 9)
        {
            $phone_number =
                $fpn[0] . $fpn[1] . $fpn[2] . "-" .
                $fpn[3] . $fpn[4] . $fpn[5] . "-" .
                $fpn[6] . $fpn[7] . $fpn[8];
        } 
        else if (count($fpn) === 11)
        {
            $phone_number =
                $fpn[0] . $fpn[1] . " " .
                $fpn[2] . $fpn[3] . $fpn[4] . "-" .
                $fpn[5] . $fpn[6] . $fpn[7] . "-" .
                $fpn[8] . $fpn[9] . $fpn[10];
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


function base64_to_jpeg($base64_string, $output_file, $avatars_path) {
    // open the output file for writing
    $ifp = fopen($avatars_path . $output_file, "wb"); 

    // split the string on commas
    // $data[0] == "data:image/png;base64"
    // $data[1] == <actual base64 string>
    //$data = explode(",", $base64_string);

    // we could add validation here with ensuring count($data) > 1
    //fwrite($ifp, base64_decode($data[1]));
    fwrite($ifp, base64_decode($base64_string));

    // clean up the file resource
    fclose($ifp); 
}


function resize_image($file, $avatars_path, $w, $h, $crop = FALSE) {
    list($width, $height) = getimagesize($avatars_path . $file);
    /*
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    */
    $src = imagecreatefromjpeg($avatars_path . $file);
    //$dst = imagecreatetruecolor($newwidth, $newheight);
    $dst = imagecreatetruecolor($w, $h);
    //imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);

    return $dst;
}


// Phone number validation
if ($photo !== null)
{
    $avatars_path = "../images/avatars/";
    $image_id = microtime(true);
    $uploaded_file_name = "original_$image_id.jpg";
    $avatar_file_name = "avatar_$image_id.jpg";

    try 
    {
        base64_to_jpeg($photo, $uploaded_file_name, $avatars_path);
    } 
    catch (Exception $e) 
    {
        $response->data_add(new ResponseElement("E310", "photo"));
        $response->set_status("NO_OK");
    }

    try 
    {
        $avatar = resize_image($uploaded_file_name, $avatars_path, 256, 256);
        imagejpeg($avatar, $avatars_path . $avatar_file_name);
        unlink($avatars_path . $uploaded_file_name);
    } 
    catch (Exception $e) 
    {
        $response->data_add(new ResponseElement("E310", "photo"));
        $response->set_status("NO_OK");
    }

    // Remove old image if user had one
    if ($user->photo !== "default_avatar.jpg")
        unlink($avatars_path . $user->photo);
    
    // Assign new link
    $photo = $avatar_file_name;

    if ($good_photo) $change_photo = true;
} 




if (!($good_firstname &&
    $good_surname &&
    $good_password &&
    $good_phone_number &&
    $good_photo))
{
    $response->set_status("NO_OK");
    die(json_encode($response));
}


if (!($change_firstname ||
    $change_surname ||
    $change_password ||
    $change_phone_number ||
    $change_photo)) 
{
    $response->data_add(new ResponseElement("E301", "no_fields_to_change"));
    $response->set_status("NO_OK");
    die(json_encode($response));
}



$query = "UPDATE $db_table_users SET ";

$query_set = "";
if ($change_firstname) $query_set .= ", firstname=:firstname";
if ($change_surname) $query_set .= ", surname=:surname";
if ($change_password) $query_set .= ", password=:password";
if ($change_phone_number) $query_set .= ", phone_number=:phone_number";
if ($change_photo) $query_set .= ", photo=:photo";

$query_set = substr($query_set, 2);

$query .= $query_set;
$query .= " WHERE user_id=:user_id";

$statement = $db->prepare($query);
if ($change_firstname) $statement->bindParam(":firstname", $firstname, PDO::PARAM_STR);
if ($change_surname) $statement->bindParam(":surname", $surname, PDO::PARAM_STR);
if ($change_password) $statement->bindParam(":password", $password, PDO::PARAM_STR);
if ($change_phone_number) $statement->bindParam(":phone_number", $phone_number, PDO::PARAM_STR);
if ($change_photo) $statement->bindParam(":photo", $photo, PDO::PARAM_STR);
$statement->bindParam(":user_id", $user->user_id, PDO::PARAM_INT);
$statement->execute();

$response->set_status("OK");
die(json_encode($response));

?>