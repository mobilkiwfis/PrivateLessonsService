<?php

require_once "configuration.php";

$a;
$a["a"] = array();
$a["a"]["b"] = "b";

echo serialize($a);




$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
echo serialize($a);
$query = "SELECT 1 FROM users LIMIT 1";
$statement = $db->prepare($query);
$statement->execute();



echo serialize($a);


?>