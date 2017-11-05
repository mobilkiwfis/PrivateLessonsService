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


////////////////////////////////////////////////////////
// Set connection.
$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
response_message("CONNECTION", "OK");







////////////////////////////////////////////////////////
// Users table fulfill.
// password = password123
// activation_key = activation_key123
$time_stamp = microtime(true);
$query = "INSERT INTO $db_table_users (
    user_id,
    name,
    surname,
    password,
    email,
    photo,
    activation_key,
    is_activated
    ) VALUES (
    'null',
    'Jon',
    'Creative',
    'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f',
    'jcreat@test.org',
    'avatar_$time_stamp.jpg',
    '7caa234220764d94027cd5e16d64230f0b56a0e9fd1edef171dd1e65b0e31699',
    'FALSE'
)";
$statement = $db->prepare($query);
$statement->execute();

// password = pass123
// activation_key = act123
$time_stamp = microtime(true);
$query = "INSERT INTO $db_table_users (
    user_id,
    name,
    surname,
    password,
    email,
    photo,
    activation_key,
    is_activated
    ) VALUES (
    'null',
    'Tom',
    'Everstrong',
    '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c',
    'tom4ever@test.com',
    'avatar_$time_stamp.jpg',
    '55f8991076ee574b5c6aba63f7195fe9185b7b777d5fbe83284c0e707fe04429',
    'FALSE'
)";
$statement = $db->prepare($query);
$statement->execute();

// password = notpass123
// activation_key = notact123
$time_stamp = microtime(true);
$query = "INSERT INTO $db_table_users (
    user_id,
    name,
    surname,
    password,
    email,
    photo,
    activation_key,
    is_activated
    ) VALUES (
    'null',
    'Dżesika',
    'Brzęczyszczykiewicz',
    '89232f32d9b8cfb6d1d8ca470089b80f31eb72548a78c0063df3cfca23e9c27f',
    'sweetbrokacik@test.pl',
    'avatar_$time_stamp.jpg',
    '873aecfe427ef83e85221aea70e63d44e0bd6c9be5c4942a7ba9948c2d4d3131',
    'FALSE'
)";
$statement = $db->prepare($query);
$statement->execute();


response_message("TABLE $db_table_users", "FILLED");






////////////////////////////////////////////////////////
// Offers table fulfill.

// password = notpass123
// activation_key = notact123
$time_stamp = microtime(true);
$query = "INSERT INTO $db_table_users (
    user_id,
    name,
    surname,
    password,
    email,
    photo,
    activation_key,
    is_activated
    ) VALUES (
    'null',
    'Dżesika',
    'Brzęczyszczykiewicz',
    '89232f32d9b8cfb6d1d8ca470089b80f31eb72548a78c0063df3cfca23e9c27f',
    'sweetbrokacik@test.pl',
    'avatar_$time_stamp.jpg',
    '873aecfe427ef83e85221aea70e63d44e0bd6c9be5c4942a7ba9948c2d4d3131',
    'FALSE'
)";
$statement = $db->prepare($query);
$statement->execute();


response_message("TABLE $db_table_users", "FILLED");




?>