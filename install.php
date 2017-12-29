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
// Users table validation.
if (check_if_table_exist($db, $db_table_users))
{
    response_message("TABLE $db_table_users", "ALREADY EXISTS");
}
else 
{
    // Create table.
    $query = "CREATE TABLE `$db_table_users` (
        `user_id` int(11) NOT NULL AUTO_INCREMENT,
        `firstname` varchar(100) NOT NULL,
        `surname` varchar(100) NOT NULL,
        `password` varchar(128) NOT NULL,
        `email` varchar(100) NOT NULL UNIQUE,
        `photo` varchar(128) NOT NULL,
        `phone_number` varchar(30),
        `creation_timestamp` TIMESTAMP NOT NULL DEFAULT '1970-01-02 00:00:01',
        `is_activated` bool NOT NULL DEFAULT '0',
        `activation_key` varchar(128) NOT NULL UNIQUE,
        `is_banned` bool NOT NULL DEFAULT '0',

        PRIMARY KEY (`user_id`)
    )";
    $statement = $db->prepare($query);
    $statement->execute();

    response_message("TABLE $db_table_users", "CREATED");
}






////////////////////////////////////////////////////////
// Offers table validation.
if (check_if_table_exist($db, $db_table_offers))
{
    response_message("TABLE $db_table_categories", "ALREADY EXISTS");
}
else 
{
    // Create table.
    $query = "CREATE TABLE `$db_table_offers` (
        `offer_id` int(11) NOT NULL AUTO_INCREMENT,
        `owner_id` int(11) NOT NULL,
        `is_active` bool NOT NULL DEFAULT '1',
        `is_archived` bool NOT NULL DEFAULT '0',
        `description` varchar(1000) NOT NULL,
        `category_id` int(11) NOT NULL,
        `price` FLOAT NOT NULL,
        `localization` varchar(256) NOT NULL,
        `views` int(11) NOT NULL DEFAULT 0,
        `at_teachers_house` bool NOT NULL DEFAULT '0',
        `at_students_house` bool NOT NULL DEFAULT '0',
        `get_to_student_for_free` bool NOT NULL DEFAULT '0',
	    `creation_timestamp` TIMESTAMP NOT NULL DEFAULT '1970-01-02 00:00:01',
	    `last_modification_timestamp` TIMESTAMP NULL DEFAULT NULL,
        `visibility_expire_timestamp` TIMESTAMP NULL DEFAULT NULL,
        `promoted_expire_timestamp` TIMESTAMP NULL DEFAULT NULL,
        
        PRIMARY KEY (`offer_id`)
    )";
    $statement = $db->prepare($query);
    $statement->execute();

    response_message("TABLE $db_table_offers", "CREATED");
}









////////////////////////////////////////////////////////
// Categories table validation.
if (check_if_table_exist($db, $db_table_categories))
{
    response_message("TABLE $db_table_categories", "ALREADY EXISTS");
}
else 
{
    // Create table.
    $query = "CREATE TABLE `$db_table_categories` (
        `category_id` int(11) NOT NULL AUTO_INCREMENT,
        `subcategory_of` int(11),
        `category_key` varchar(100) NOT NULL UNIQUE,
        
        PRIMARY KEY (`category_id`)
    )";
    $statement = $db->prepare($query);
    $statement->execute();



    response_message("TABLE $db_table_categories", "CREATED");
}








////////////////////////////////////////////////////////
// Available days table validation.
if (check_if_table_exist($db, $db_table_available_days))
{
    response_message("TABLE $db_table_available_days", "ALREADY EXISTS");
}
else 
{
    // Create table.
    $query = "CREATE TABLE `$db_table_available_days` (
        `offer_id` int(11) NOT NULL,
        `mo_morning` bool NOT NULL DEFAULT '0',
        `mo_evening` bool NOT NULL DEFAULT '0',
        `tu_morning` bool NOT NULL DEFAULT '0',
        `tu_evening` bool NOT NULL DEFAULT '0',
        `we_morning` bool NOT NULL DEFAULT '0',
        `we_evening` bool NOT NULL DEFAULT '0',
        `th_morning` bool NOT NULL DEFAULT '0',
        `th_evening` bool NOT NULL DEFAULT '0',
        `fr_morning` bool NOT NULL DEFAULT '0',
        `fr_evening` bool NOT NULL DEFAULT '0',
        `sa_morning` bool NOT NULL DEFAULT '0',
        `sa_evening` bool NOT NULL DEFAULT '0',
        `su_morning` bool NOT NULL DEFAULT '0',
        `su_evening` bool NOT NULL DEFAULT '0',

        PRIMARY KEY (`offer_id`)
    )";
    $statement = $db->prepare($query);
    $statement->execute();

    response_message("TABLE $db_table_available_days", "CREATED");
}











////////////////////////////////////////////////////////
// Payments history table validation.
if (check_if_table_exist($db, $db_table_payments_history))
{
    response_message("TABLE $db_table_payments_history", "ALREADY EXISTS");
}
else 
{
    // Create table.
    $query = "CREATE TABLE `$db_table_payments_history` (
        `payment_id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `offer_id` int(11) NOT NULL,
        `charge` FLOAT NOT NULL,
        `payment_method` varchar(100) NOT NULL,
        `payment_date` TIMESTAMP NOT NULL DEFAULT '1970-01-02 00:00:01',
        `start_date` TIMESTAMP NOT NULL DEFAULT '1970-01-02 00:00:01',
        `end_date` TIMESTAMP NOT NULL DEFAULT '1970-01-02 00:00:01',

        PRIMARY KEY (`payment_id`)
    )";
    $statement = $db->prepare($query);
    $statement->execute();

    response_message("TABLE $db_table_payments_history", "CREATED");
}











////////////////////////////////////////////////////////
// Favourites table validation.
if (check_if_table_exist($db, $db_table_favourites))
{
    response_message("TABLE $db_table_favourites", "ALREADY EXISTS");
}
else 
{
    // Create table.
    $query = "CREATE TABLE `$db_table_favourites` (
        `offer_id` int(11) NOT NULL,
        `user_id` int(11) NOT NULL
    )";
    $statement = $db->prepare($query);
    $statement->execute();

    response_message("TABLE $db_table_favourites", "CREATED");
}








////////////////////////////////////////////////////////
// Teaching leveles table validation.
if (check_if_table_exist($db, $db_table_teaching_levels))
{
    response_message("TABLE $db_table_teaching_levels", "ALREADY EXISTS");
}
else 
{
    // Create table.
    $query = "CREATE TABLE `$db_table_teaching_levels` (
        `offer_id` int(11) NOT NULL,
        `elementary_school` bool NOT NULL DEFAULT '0',
        `junior_high_school` bool NOT NULL DEFAULT '0',
        `high_school` bool NOT NULL DEFAULT '0',
        `vocational_school` bool NOT NULL DEFAULT '0',
        `college` bool NOT NULL DEFAULT '0',
        `other` bool NOT NULL DEFAULT '0',
        
        PRIMARY KEY (`offer_id`)
    )";
    $statement = $db->prepare($query);
    $statement->execute();

    response_message("TABLE $db_table_teaching_levels", "CREATED");
}










// Link table.
$query = "ALTER TABLE 
    `$db_table_offers` ADD CONSTRAINT `${db_table_offers}_fk0` 
    FOREIGN KEY (`owner_id`) 
    REFERENCES `$db_table_users`(`user_id`)
";
$statement = $db->prepare($query);
$statement->execute();

response_message("TABLE $db_table_offers LINKED TO $db_table_users", "OK");




// Link table.
$query = "ALTER TABLE 
    `$db_table_offers` ADD CONSTRAINT `${db_table_offers}_fk1` 
    FOREIGN KEY (`category_id`) 
    REFERENCES `$db_table_categories`(`category_id`)
";
$statement = $db->prepare($query);
$statement->execute();

response_message("TABLE $db_table_offers LINKED TO $db_table_categories", "OK");





// Link table.
$query = "ALTER TABLE 
    `$db_table_categories` ADD CONSTRAINT `${db_table_categories}_fk0` 
    FOREIGN KEY (`subcategory_of`) 
    REFERENCES `$db_table_categories`(`category_id`)
";
$statement = $db->prepare($query);
$statement->execute();

response_message("TABLE $db_table_categories LINKED TO $db_table_categories", "OK");







// Link table.
$query = "ALTER TABLE 
    `$db_table_available_days` ADD CONSTRAINT `${db_table_available_days}_fk0` 
    FOREIGN KEY (`offer_id`) 
    REFERENCES `$db_table_offers`(`offer_id`)
";
$statement = $db->prepare($query);
$statement->execute();

response_message("TABLE $db_table_available_days LINKED TO $db_table_offers", "OK");







// Link table.
$query = "ALTER TABLE 
    `$db_table_payments_history` ADD CONSTRAINT `${db_table_payments_history}_fk0` 
    FOREIGN KEY (`user_id`) 
    REFERENCES `$db_table_users`(`user_id`)
";
$statement = $db->prepare($query);
$statement->execute();

response_message("TABLE $db_table_payments_history LINKED TO $db_table_users", "OK");






// Link table.
$query = "ALTER TABLE 
    `$db_table_payments_history` ADD CONSTRAINT `${db_table_payments_history}_fk1` 
    FOREIGN KEY (`offer_id`) 
    REFERENCES `$db_table_offers`(`offer_id`)
";
$statement = $db->prepare($query);
$statement->execute();

response_message("TABLE $db_table_payments_history LINKED TO $db_table_offers", "OK");




// Link table.
$query = "ALTER TABLE 
    `$db_table_favourites` ADD CONSTRAINT `${db_table_favourites}_fk0` 
    FOREIGN KEY (`offer_id`) 
    REFERENCES `$db_table_offers`(`offer_id`)
";
$statement = $db->prepare($query);
$statement->execute();

response_message("TABLE $db_table_favourites LINKED TO $db_table_offers", "OK");




// Link table.
$query = "ALTER TABLE 
    `$db_table_favourites` ADD CONSTRAINT `${db_table_favourites}_fk1` 
    FOREIGN KEY (`user_id`) 
    REFERENCES `$db_table_users`(`user_id`)
";
$statement = $db->prepare($query);
$statement->execute();

response_message("TABLE $db_table_favourites LINKED TO $db_table_users", "OK");







// Link table.
$query = "ALTER TABLE 
    `$db_table_teaching_levels` ADD CONSTRAINT `${db_table_teaching_levels}_fk0` 
    FOREIGN KEY (`offer_id`) 
    REFERENCES `$db_table_offers`(`offer_id`)
";
$statement = $db->prepare($query);
$statement->execute();

response_message("TABLE $db_table_teaching_levels LINKED TO $db_table_offers", "OK");












?>