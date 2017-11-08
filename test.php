<?php

require_once "configuration.php";

$a;
$a["a"] = array();
$a["a"]["b"] = "b";

echo serialize($a);

?>