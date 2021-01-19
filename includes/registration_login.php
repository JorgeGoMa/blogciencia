<?php 
	// declaración de variable
	$username = "";
	$email    = "";
	$errors = array(); 
	$role = "Usuario";

	// regístra usuario
	if (isset($_POST['reg_user'])) {
		// recibe todos los valores del formulario
		$username = esc($_POST['username']);
		$email = esc($_POST['email']);
		$password_1 = esc($_POST['password_1']);
		$password_2 = esc($_POST['password_2']);

		// revisa que el formulario está llenado correctamente
		if (empty($username)) {  array_push($errors, "Necesitamos tu nombre de usuario"); }
		if (empty($email)) { array_push($errors, "Falta el correo"); }
		if (empty($password_1)) { array_push($errors, "Falta contraseña"); }
		if ($password_1 != $password_2) { array_push($errors, "No coinciden las contraseñas");}

		// Revisar que no hayan dos usuarios registrados
		// el email y usuario deben ser únicos
		$user_check_query = "SELECT * FROM users WHERE username='$username' 
								OR email='$email' LIMIT 1";

		$result = mysqli_query($conn, $user_check_query);
		$user = mysqli_fetch_assoc($result);

		if ($user) { // Si el usuario existe
			if ($user['username'] === $username) {
			  array_push($errors, "Ya existe este nombre de usuario");
			}
			if ($user['email'] === $email) {
			  array_push($errors, "Ya existe este email");
			}
		}
		// Registra usuario si es que no hay errores en el formulario
		if (count($errors) == 0) {
			$password = md5($password_1);//Encripta la contraseña antes de subirla a la BD
			$query = "INSERT INTO users (username, email,role, password, created_at, updated_at) 
					  VALUES('$username', '$email','$role', '$password', now(), now())";
			mysqli_query($conn, $query);

			// obtener id de usuario creado
			$reg_user_id = mysqli_insert_id($conn); 

			// poner logeado al usuario
			$_SESSION['user'] = getUserById($reg_user_id);

			// Si el usuario es admin, reedirigírlo al área de admin
			if ( in_array($_SESSION['user']['role'], ["Admin", "Autor"])) {
				$_SESSION['message'] = "Ya estas logeado";
				// direccionar al área de admin
				header('location: ' . BASE_URL . 'admin/dashboard.php');
				exit(0);
			} else {
				$_SESSION['message'] = "Ya estas logeado";
				// direccionar al área general
				header('location: index.php');				
				exit(0);
			}
		}
	}

	// Iniciar sesión de usuario
	if (isset($_POST['login_btn'])) {
		$username = esc($_POST['username']);
		$password = esc($_POST['password']);

		if (empty($username)) { array_push($errors, "Falta Nombre de Usuario"); }
		if (empty($password)) { array_push($errors, "Falta Contraseña"); }
		if (empty($errors)) {
			$password = md5($password); // encriptar contraseña
			$sql = "SELECT * FROM users WHERE username='$username' and password='$password' LIMIT 1";

			$result = mysqli_query($conn, $sql);
			if (mysqli_num_rows($result) > 0) {
				// obtener id del usuario creado
				$reg_user_id = mysqli_fetch_assoc($result)['id']; 

				// poner logeado a usuario
				$_SESSION['user'] = getUserById($reg_user_id); 

				// Si el usuario es admin, direccionarlo al área de admin
				if ( in_array($_SESSION['user']['role'], ["Admin", "Autor"])) {
					$_SESSION['message'] = "Ya estas logeado";
					// direcciona al área de admin
					header('location: ' . BASE_URL . '/admin/dashboard.php');
					exit(0);
				} else {
					$_SESSION['message'] = "Ya estas logeado";
					// direcciona al área general
					header('location: index.php');				
					exit(0);
				}
			} else {
				array_push($errors, 'Datos erroneos');
			}
		}
	}
	// acar valor del formulario
	function esc(String $value)
	{	
		// Llevar la BD global hacia la función
		global $conn;

		$val = trim($value); // quitar espacios vacíos 
		$val = mysqli_real_escape_string($conn, $value);

		return $val;
	}
	// Obtener información del usuario con id
	function getUserById($id)
	{
		global $conn;
		$sql = "SELECT * FROM users WHERE id=$id LIMIT 1";

		$result = mysqli_query($conn, $sql);
		$user = mysqli_fetch_assoc($result);

		return $user; 
	}
?>