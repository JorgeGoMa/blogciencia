<?php 
	session_start();

    // conexión a la Base de Datos
    $conn = mysqli_connect("localhost", "root", "", "blogciencia");

	if (!$conn) {
		die("Error al conectar a la BD: " . mysqli_connect_error());
	}
       
	define ('ROOT_PATH', realpath(dirname(__FILE__)));
	define('BASE_URL', 'http://localhost:3000/');
?>
