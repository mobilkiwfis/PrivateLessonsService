
<?php

require_once "configuration.php";

$time_stamp = microtime(true);
echo date($db_date_format, $time_stamp + $one_week);

?>