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
$category = (isset($_GET["category"])) ? $_GET["category"] : null;
$category_id = null;
$good_category = true;
$check_category = false;
$price_from = (isset($_GET["price_from"])) ? $_GET["price_from"] : null;
$good_price_from = true;
$check_price_from = false;
$price_to = (isset($_GET["price_to"])) ? $_GET["price_to"] : null;
$good_price_to = true;
$check_price_to = false;
$localization = (isset($_GET["localization"])) ? $_GET["localization"] : null;
$good_localization = true;
$check_localization = false;
$at_teachers_house = (isset($_GET["at_teachers_house"])) ? $_GET["at_teachers_house"] : null;
$good_at_teachers_house = true;
$check_at_teachers_house = false;
$at_students_house = (isset($_GET["at_students_house"])) ? $_GET["at_students_house"] : null;
$good_at_students_house = true;
$check_at_students_house = false;
$get_to_student_for_free = (isset($_GET["get_to_student_for_free"])) ? $_GET["get_to_student_for_free"] : null;
$good_get_to_student_for_free = true;
$check_get_to_student_for_free = false;


// Category validation
if ($category !== null)
{
    $check_category = true;
    $category = trim($category); 

    $query = "SELECT * FROM $db_table_categories WHERE category_key=:category_key";
    $statement = $db->prepare($query);
    $statement->bindParam(":category_key", $category, PDO::PARAM_STR);
    $statement->execute();

    if ($statement->rowCount() > 0) 
    {
        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        $result = $result[0];

        $category_id = $result->category_id;
    } 
    else 
    {
        $response->data_add(new ResponseElement("E320", "category"));
        $good_category = false;
    }
} 


// Price_from validation
if ($price_from !== null)
{
    $check_price_from = true;
    $price_from = preg_replace("/[^0-9\.,]/", "", $price_from); 

    if (strlen($price_from) < 1)
    {
        $response->data_add(new ResponseElement("E311", "price_from"));
        $good_price_from = false;
    } 

    $price_from = floatval($price_from);

    if ($price_from < 0) 
    {
        $response->data_add(new ResponseElement("E310", "price_from"));
        $good_price_from = false;
    }
} 


// Price_to validation
if ($price_to !== null)
{
    $check_price_to = true;
    $price_to = preg_replace("/[^0-9\.,]/", "", $price_to); 

    if (strlen($price_to) < 1)
    {
        $response->data_add(new ResponseElement("E311", "price_to"));
        $good_price_to = false;
    } 

    $price_to = floatval($price_to);

    if ($price_to < 0) 
    {
        $response->data_add(new ResponseElement("E310", "price_to"));
        $good_price_to = false;
    }
} 

// Invaild price range
if ($good_price_from === true && $good_price_to === true)
{
    if ($price_from > $price_to) 
    {
        $response->data_add(new ResponseElement("E310", "price"));
        $good_price_from = false;
        $good_price_to = false;
    }
}


// Localization validation
if ($localization !== null)
{
    $check_localization = true;
    $localization = trim($localization); 
} 


// At teachers house validation
if ($at_teachers_house !== null)
{
    $check_at_teachers_house = true;
    $at_teachers_house = trim($at_teachers_house); // Remove spaces from begining and ending
    $at_teachers_house = filter_var($at_teachers_house, FILTER_VALIDATE_BOOLEAN);

    if (!$at_teachers_house) $check_at_teachers_house = false;
}


// At students house validation
if ($at_students_house !== null)
{
    $check_at_students_house = true;
    $at_students_house = trim($at_students_house); // Remove spaces from begining and ending
    $at_students_house = filter_var($at_students_house, FILTER_VALIDATE_BOOLEAN);

    if (!$at_students_house) $check_at_students_house = false;
}


// Get to student for free validation
if ($get_to_student_for_free !== null)
{
    $check_get_to_student_for_free = true;
    $get_to_student_for_free = trim($get_to_student_for_free); // Remove spaces from begining and ending
    $get_to_student_for_free = filter_var($get_to_student_for_free, FILTER_VALIDATE_BOOLEAN);

    if (!$get_to_student_for_free) $check_get_to_student_for_free = false;
}




if (!($good_at_students_house &&
    $good_at_teachers_house &&
    $good_category &&
    $good_get_to_student_for_free &&
    $good_localization &&
    $good_price_from &&
    $good_price_to))
{
    $response->set_status("NO_OK");
    die(json_encode($response));
}

//$q_true = 1;
//$q_false = 0;
//$q_promoted_expire_timestamp = "promoted_expire_timestamp";

$query = "SELECT * FROM $db_table_offers AS o 
    INNER JOIN $db_table_categories AS c ON o.category_id=c.category_id 
    INNER JOIN $db_table_available_days AS a ON a.offer_id=o.offer_id WHERE";


$query_options = " (is_archived=0 AND is_active=1 AND visibility_expire_timestamp>:time_stamp_db)";
$query .= $query_options;


$search_begin = "";
$search_end = "";
if ($check_category || 
    $check_price_from || 
    $check_price_to || 
    $check_localization ||
    $check_at_teachers_house ||
    $check_at_students_house ||
    $check_get_to_student_for_free)
{
    $search_begin = " AND (";
    $search_end = ")";
    
    $query_search = "";
    if ($check_category) $query_search .= " AND c.category_id=:category_id";
    if ($check_price_from) $query_search .= " AND price>=:price_from";
    if ($check_price_to) $query_search .= " AND price<=:price_to";
    if ($check_localization) $query_search .= " AND localization LIKE :localization";
    if ($check_at_teachers_house) $query_search .= " AND at_teachers_house=:at_teachers_house";
    if ($check_at_students_house) $query_search .= " AND at_students_house=:at_students_house";
    if ($check_get_to_student_for_free) $query_search .= " AND get_to_student_for_free=:get_to_student_for_free";

    
    $query .= $search_begin;
    $query_search = "(" . substr($query_search, 5) . ")";
    $query .= $query_search;
    $query .= $search_end;
}



$query .= " ORDER BY promoted_expire_timestamp>:promoted_expire_timestamp DESC";

$time_stamp = microtime(true);
$time_stamp_db = date($db_date_format, $time_stamp);

$statement = $db->prepare($query);
$statement->bindParam(":time_stamp_db", $time_stamp_db, PDO::PARAM_INT);
if ($check_category) $statement->bindParam(":category_id", $category_id, PDO::PARAM_INT);
if ($check_price_from) $statement->bindParam(":price_from", $price_from, PDO::PARAM_STR);
if ($check_price_to) $statement->bindParam(":price_to", $price_to, PDO::PARAM_STR);
if ($check_localization) $statement->bindParam(":localization", $localization, PDO::PARAM_STR);
if ($check_at_teachers_house) $statement->bindParam(":at_teachers_house", $at_teachers_house, PDO::PARAM_INT);
if ($check_at_students_house) $statement->bindParam(":at_students_house", $at_students_house, PDO::PARAM_INT);
if ($check_get_to_student_for_free) $statement->bindParam(":get_to_student_for_free", $get_to_student_for_free, PDO::PARAM_INT);
$statement->bindParam(":promoted_expire_timestamp", $time_stamp_db, PDO::PARAM_STR);
$statement->execute();


if ($statement->rowCount() > 0) 
{
    $result = $statement->fetchAll(PDO::FETCH_OBJ);

    foreach ($result as $row)
    {
        unset($row->subcategory_of);

        $response->data_add($row);
    }
} 

$response->set_status("OK");
die(json_encode($response));




/*
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
*/

die(json_encode($response));

?>