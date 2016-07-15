<?php
/****************************************************************	
*Session: This is called upon login to store/retrieve user data*/
 if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

$dbhost = 'localhost';
$dbuser = 'myusername';
$dbpass = 'mypassword';
$dbname = 'productdatabase';

$link = mysql_connect($dbhost, $dbuser, $dbpass);

if (!$link) {
    die('Could not connect: ' . mysql_error());
}else{
	mysql_select_db($dbname);
	
/**Set character_set_client and character_set_connection**/
  mysql_query("SET character_set_client=utf8", $link);
  mysql_query("SET character_set_connection=utf8", $link);
}
?>
