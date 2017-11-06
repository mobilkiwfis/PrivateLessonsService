<?php

$useLocalHost = true;

$localhost_database_host_address = "localhost";
$localhost_database_name = "mobilki";
$localhost_database_username = "root";
$localhost_database_password = "";


$server_database_host_address = "piotrbartela.pl";
$server_database_name = "mobilki";
$server_database_username = "mobilki";
$server_database_password = "qwe123";


$db_table_users = "users";
$db_table_offers = "offers";
$db_table_categories = "categories";
$db_table_available_days = "available_days";
$db_table_payments_history = "payments_history";
$db_table_favourites = "favourites";




if ($useLocalHost == true)
{
    $db_host = $localhost_database_host_address;
    $db_name = $localhost_database_name;
    $db_username = $localhost_database_username;
    $db_password = $localhost_database_password;
}
else
{
    $db_host = $server_database_host_address;
    $db_name = $server_database_name;
    $db_username = $server_database_username;
    $db_password = $server_database_password;
}

chmod("configuration.php", 0600); 
?>