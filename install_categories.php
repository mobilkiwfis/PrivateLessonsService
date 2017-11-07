<?php

require_once "configuration.php";


/**
 * Shows status to user.
 *
 * @param [string] $what
 * @param [string] $status
 * @return void
 */
function response_message($what, $status) {
    echo "$what = $status <br>";
}


/**
 * Check if table exists.
 *
 * @param [PDO] $db
 * @param [string] $table_name
 * @return bool
 */
function check_if_table_exist($db, $table_name) {
    $query = "SELECT 1 FROM $table_name LIMIT 1";
    $statement = $db->prepare($query);

    $table_exist = true;
    try {
        $statement->execute();
    } catch (Exception $e) {
        $table_exist = false;
    }

    return $table_exist;
}



////////////////////////////////////////////////////////
// Set connection.
$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
response_message("CONNECTION", "OK");





////////////////////////////////////////////////////////
// Fill categories table.
if (check_if_table_exist($db, $db_table_categories))
{
    // Clear table:
    $query = "DELETE FROM $db_table_categories";
    $statement = $db->prepare($query);
    $statement->execute();

    response_message("TABLE $db_table_categories", "CLEARED");


    $query = "INSERT INTO $db_table_categories (
        `category_id`,
        `subcategory_of`,
        `category_key`
    ) VALUES (
        '1',
        NULL,
        'root'
    ), 
    
    
    (
        '2',
        '1',
        'math'
    ), (
        '3',
        '1',
        'biology'
    ), (
        '4',
        '1',
        'polish'
    ), (
        '5',
        '1',
        'chemistry'
    ), (
        '6',
        '1',
        'physics'
    ), (
        '7',
        '1',
        'history'
    ), (
        '8',
        '1',
        'music'
    ), (
        '9',
        '1',
        'law'
    ), (
        '10',
        '1',
        'languages'
    ), 
    
    
    (
        '801',
        '8',
        'music/guitar'
    ), (
        '802',
        '8',
        'music/piano'
    ), 
    
    
    (
        '901',
        '9',
        'law/polish'
    ), (
        '902',
        '9',
        'law/international'
    ), 
    
    
    (
        '1001',
        '10',
        'languages/english'
    ), (
        '1002',
        '10',
        'languages/german'
    ), (
        '1003',
        '10',
        'languages/french'
    ), (
        '1004',
        '10',
        'languages/russian'
    )";
    $statement = $db->prepare($query);
    $statement->execute();

    response_message("TABLE $db_table_categories", "FILLED");

}
else 
{
    response_message("TABLE $db_table_categories DOES NOT EXIST", "FAILED");
}


?>