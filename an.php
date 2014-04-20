<?php
require(dirname(__FILE__) . '/includes/init.php');
$re = $db_write->query("update set password");
if($re){
	
}