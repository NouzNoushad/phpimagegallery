<?php

$conn = mysqli_connect('localhost', 'root', '', 'imagegallery');

if(!$conn){
	die("Connection error: " . mysqli_connect_error());
}	

?>