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
$description = (isset($_POST["description"])) ? $_POST["description"] : null;
$good_description = true;
$category = (isset($_POST["category"])) ? $_POST["category"] : null;
$good_category = true;
$price = (isset($_POST["price"])) ? $_POST["price"] : null;
$good_price = true;
$localization = (isset($_POST["localization"])) ? $_POST["localization"] : null;
$good_localization = true;
$at_teachers_house = (isset($_POST["at_teachers_house"])) ? $_POST["at_teachers_house"] : null;
$good_at_teachers_house = true;
$at_students_house = (isset($_POST["at_students_house"])) ? $_POST["at_students_house"] : null;
$good_at_students_house = true;
$get_to_student_for_free = (isset($_POST["get_to_student_for_free"])) ? $_POST["get_to_student_for_free"] : null;
$good_get_to_student_for_free = true;

$mo_morning = (isset($_POST["mo_morning"])) ? $_POST["mo_morning"] : null;
$good_mo_morning = true;
$mo_evening = (isset($_POST["mo_evening"])) ? $_POST["mo_evening"] : null;
$good_mo_evening = true;

$tu_morning = (isset($_POST["tu_morning"])) ? $_POST["tu_morning"] : null;
$good_tu_morning = true;
$tu_evening = (isset($_POST["tu_evening"])) ? $_POST["tu_evening"] : null;
$good_tu_evening = true;

$we_morning = (isset($_POST["we_morning"])) ? $_POST["we_morning"] : null;
$good_we_morning = true;
$we_evening = (isset($_POST["we_evening"])) ? $_POST["we_evening"] : null;
$good_we_evening = true;

$th_morning = (isset($_POST["th_morning"])) ? $_POST["th_morning"] : null;
$good_th_morning = true;
$th_evening = (isset($_POST["th_evening"])) ? $_POST["th_evening"] : null;
$good_th_evening = true;

$fr_morning = (isset($_POST["fr_morning"])) ? $_POST["fr_morning"] : null;
$good_fr_morning = true;
$fr_evening = (isset($_POST["fr_evening"])) ? $_POST["fr_evening"] : null;
$good_fr_evening = true;

$sa_morning = (isset($_POST["sa_morning"])) ? $_POST["sa_morning"] : null;
$good_sa_morning = true;
$sa_evening = (isset($_POST["sa_evening"])) ? $_POST["sa_evening"] : null;
$good_sa_evening = true;

$su_morning = (isset($_POST["su_morning"])) ? $_POST["su_morning"] : null;
$good_su_morning = true;
$su_evening = (isset($_POST["su_evening"])) ? $_POST["su_evening"] : null;
$good_su_evening = true;


// Description validation
if ($description !== null)
{
    $description = trim($description); // Remove spaces from begining and ending
    
    if (strlen($description) < 1)
    {
        $response->data_add(new ResponseElement("E311", "description"));
        $good_description = false;
    } 
    else if (strlen($description) > 1000)
    {
        $response->data_add(new ResponseElement("E312", "description"));
        $good_description = false;
    }

    $description = preg_replace("/[\r\n]+/", "{br}", $description); // Multiple new lines into marker
    $description = preg_replace("/\s+/", " ", $description); // Multiple white characters to space
    $description = preg_replace("/</", "&lt;", $description); // < to \<
    $description = preg_replace("/>/", "&gt;", $description); // < to \<
    $description = preg_replace("/(\s*{br}\s*)/", "{br}", $description); // remove space bofore new line
    $description = preg_replace("/{br}/", "<br>", $description); // market into html new line
} 
else
{
    $response->data_add(new ResponseElement("E301", "description"));
    $good_description = false;
}

// Category validation
if ($category !== null)
{
    $category = trim($category); 

    $query = "SELECT * FROM $db_table_categories WHERE category_key=:category_key";
    $statement = $db->prepare($query);
    $statement->bindParam(":category_key", $category, PDO::PARAM_STR);
    $statement->execute();

    if ($statement->rowCount() > 0) 
    {
        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        $result = $result[0];

        $category = $result->category_id;
    } 
    else 
    {
        $response->data_add(new ResponseElement("E320", "category"));
        $good_category = false;
    }
} 
else
{
    $response->data_add(new ResponseElement("E301", "category"));
    $good_description = false;
}


// Price validation
if ($price !== null)
{
    $price = preg_replace("/,/", ".", $price); // , to .
    $price = preg_replace("/[^0-9\.]/", "", $price); // remove all characters expect rumbers and dots
    $price = floatval($price);
    $price = floor($price * 100) / 100;

    if ($price > 0.0)
    {
        // nothing, price is good
    } 
    else
    {
        $response->data_add(new ResponseElement("E310", "price"));
        $good_price = false;
    }
} 
else
{
    $response->data_add(new ResponseElement("E301", "price"));
    $good_price = false;
}


// Localization validation
if ($localization !== null)
{
    $localization = trim($localization); // Remove spaces from begining and ending
    $localization = preg_replace("/\s+/", " ", $localization); // Multiple white characters to space
    $localization = preg_replace("/</", "&lt;", $localization); // < to \<
    $localization = preg_replace("/>/", "&gt;", $localization); // < to \<
    
    if (strlen($localization) < 1)
    {
        $localization->data_add(new ResponseElement("E311", "localization"));
        $good_localization = false;
    } 
    else if (strlen($localization) > 256)
    {
        $response->data_add(new ResponseElement("E312", "localization"));
        $good_localization = false;
    }
} 
else
{
    $response->data_add(new ResponseElement("E301", "localization"));
    $good_localization = false;
}


// At_teachers_house validation
if ($at_teachers_house !== null)
{
    $at_teachers_house = trim($at_teachers_house); // Remove spaces from begining and ending
    $at_teachers_house = filter_var($at_teachers_house, FILTER_VALIDATE_BOOLEAN);
} 
else
{
    $response->data_add(new ResponseElement("E301", "at_teachers_house"));
    $good_at_teachers_house = false;
}


// At_students_house validation
if ($at_students_house !== null)
{
    $at_students_house = trim($at_students_house); // Remove spaces from begining and ending
    $at_students_house = filter_var($at_students_house, FILTER_VALIDATE_BOOLEAN);
} 
else
{
    $response->data_add(new ResponseElement("E301", "at_students_house"));
    $good_at_students_house = false;
}


// At_students_house validation
if ($get_to_student_for_free !== null)
{
    $get_to_student_for_free = trim($get_to_student_for_free); // Remove spaces from begining and ending
    $get_to_student_for_free = filter_var($get_to_student_for_free, FILTER_VALIDATE_BOOLEAN);
} 
else
{
    $response->data_add(new ResponseElement("E301", "get_to_student_for_free"));
    $good_get_to_student_for_free = false;
}


function days_validation(&$day_time, $day_name, &$good_day_time, &$response)
{
    if ($day_time !== null)
    {
        $day_time = trim($day_time); // Remove spaces from begining and ending
        $day_time = filter_var($day_time, FILTER_VALIDATE_BOOLEAN);
    } 
    else
    {
        $response->data_add(new ResponseElement("E301", $day_name));
        $good_day_time = false;
    }
}

days_validation($mo_morning, "mo_morning", $good_mo_morning, $response);
days_validation($mo_evening, "mo_evening", $good_mo_evening, $response);
days_validation($tu_morning, "tu_morning", $good_tu_morning, $response);
days_validation($tu_evening, "tu_evening", $good_tu_evening, $response);
days_validation($we_morning, "we_morning", $good_we_morning, $response);
days_validation($we_evening, "we_evening", $good_we_evening, $response);
days_validation($th_morning, "th_morning", $good_th_morning, $response);
days_validation($th_evening, "th_evening", $good_th_evening, $response);
days_validation($fr_morning, "fr_morning", $good_fr_morning, $response);
days_validation($fr_evening, "fr_evening", $good_fr_evening, $response);
days_validation($sa_morning, "sa_morning", $good_sa_morning, $response);
days_validation($sa_evening, "sa_evening", $good_sa_evening, $response);
days_validation($su_morning, "su_morning", $good_su_morning, $response);
days_validation($su_evening, "su_evening", $good_su_evening, $response);








if (!($good_description &&
    $good_category &&
    $good_price &&
    $good_localization &&
    $good_at_teachers_house &&
    $good_at_students_house &&
    $good_get_to_student_for_free &&
    
    $good_mo_morning &&
    $good_mo_evening &&
    $good_tu_morning &&
    $good_tu_evening &&
    $good_we_morning &&
    $good_we_evening &&
    $good_th_morning &&
    $good_th_evening &&
    $good_fr_morning &&
    $good_fr_evening &&
    $good_sa_morning &&
    $good_sa_evening &&
    $good_su_morning &&
    $good_su_evening))
{
    $response->set_status("NO_OK");
    die(json_encode($response));
}

$time_stamp = microtime(true);
$time_stamp_db = date($db_date_format, $time_stamp);
$time_expires_db = date($db_date_format, $time_stamp + $two_weeks);

$query = "INSERT INTO $db_table_offers (
    `offer_id`,
    `owner_id`,
    `is_active`,
    `is_archived`,
    `description`,
    `category_id`,
    `price`,
    `localization`,
    `at_teachers_house`,
    `at_students_house`,
    `get_to_student_for_free`,
    `creation_timestamp`,
    `last_modification_timestamp`,
    `visibility_expire_timestamp`,
    `promoted_expire_timestamp`
) VALUES (
    null,
    :owner_id,
    '1',
    '0',
    :description,
    :category_id,
    :price,
    :localization,
    :at_teachers_house,
    :at_students_house,
    :get_to_student_for_free,
    :creation_timestamp,
    null,
    :visibility_expire_timestamp,
    null
)";

$statement = $db->prepare($query);
$statement->bindParam(":owner_id", $user->user_id, PDO::PARAM_INT);
$statement->bindParam(":description", $description, PDO::PARAM_STR);
$statement->bindParam(":category_id", $category, PDO::PARAM_INT);
$statement->bindParam(":price", $price, PDO::PARAM_STR);
$statement->bindParam(":localization", $localization, PDO::PARAM_STR);
$statement->bindParam(":at_teachers_house", $at_teachers_house, PDO::PARAM_STR);
$statement->bindParam(":at_students_house", $at_students_house, PDO::PARAM_STR);
$statement->bindParam(":get_to_student_for_free", $get_to_student_for_free, PDO::PARAM_STR);
$statement->bindParam(":creation_timestamp", $time_stamp_db, PDO::PARAM_STR);
$statement->bindParam(":visibility_expire_timestamp", $time_expires_db, PDO::PARAM_STR);
$statement->execute();



$offer_id = $db->lastInsertId(); 

$query = "INSERT INTO $db_table_available_days (
    `offer_id`,
    `mo_morning`,
    `mo_evening`,
    `tu_morning`,
    `tu_evening`,
    `we_morning`,
    `we_evening`,
    `th_morning`,
    `th_evening`,
    `fr_morning`,
    `fr_evening`,
    `sa_morning`,
    `sa_evening`,
    `su_morning`,
    `su_evening`
) VALUES (
    :offer_id,
    :mo_morning,
    :mo_evening,
    :tu_morning,
    :tu_evening,
    :we_morning,
    :we_evening,
    :th_morning,
    :th_evening,
    :fr_morning,
    :fr_evening,
    :sa_morning,
    :sa_evening,
    :su_morning,
    :su_evening
)";

$statement = $db->prepare($query);
$statement->bindParam(":offer_id", $offer_id, PDO::PARAM_INT);
$statement->bindParam(":mo_morning", $mo_morning, PDO::PARAM_STR);
$statement->bindParam(":mo_evening", $mo_evening, PDO::PARAM_STR);
$statement->bindParam(":tu_morning", $tu_morning, PDO::PARAM_STR);
$statement->bindParam(":tu_evening", $tu_evening, PDO::PARAM_STR);
$statement->bindParam(":we_morning", $we_morning, PDO::PARAM_STR);
$statement->bindParam(":we_evening", $we_evening, PDO::PARAM_STR);
$statement->bindParam(":th_morning", $th_morning, PDO::PARAM_STR);
$statement->bindParam(":th_evening", $th_evening, PDO::PARAM_STR);
$statement->bindParam(":fr_morning", $fr_morning, PDO::PARAM_STR);
$statement->bindParam(":fr_evening", $fr_evening, PDO::PARAM_STR);
$statement->bindParam(":sa_morning", $sa_morning, PDO::PARAM_STR);
$statement->bindParam(":sa_evening", $sa_evening, PDO::PARAM_STR);
$statement->bindParam(":su_morning", $su_morning, PDO::PARAM_STR);
$statement->bindParam(":su_evening", $su_evening, PDO::PARAM_STR);
$statement->execute();

$response->set_status("OK");
die(json_encode($response));

?>