<?php
include(dirname(__FILE__).'/includes/init.php');
$db_write->query("");
$db_write->query("insert into  (parent_id,action_code) values ('38','da_exp')");
echo 'ok';