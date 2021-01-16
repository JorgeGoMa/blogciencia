<?php 
	session_start();

    // conctar a la BD
    $conn = mysqli_connect("localhost", "root", "", "blogciencia");

	if (!$conn) {
		die("Error connecting to database: " . mysqli_connect_error());
	}
       
	define ('ROOT_PATH', realpath(dirname(__FILE__)));
	define('BASE_URL', 'http://localhost/blogciencia/');
?>
