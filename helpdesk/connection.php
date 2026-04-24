<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "helpdesk";
// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if (!($conn)) {
  echo ("Connection failed");
}
else{
}
?>