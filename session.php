<?php
session_start();
if(!isset($_SESSION['reid'])){
	header('location:http://clicktoclass.lk/');
	exit();
}

$fullname = $_SESSION['fullname'];
$contactnumber = $_SESSION['contactnumber'];

?>