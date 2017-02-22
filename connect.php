<?php
$HostName = "localhost";
$Username = "root";
$Password = "2335171";
$Db = "home_automation";
$conn = mysqli_connect($HostName, $Username, $Password, $Db);
// $db = mysql_select_db($Db);
if(!$conn) {
	echo mysqli_error();
}
echo "string";
?>