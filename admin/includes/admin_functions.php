<?php 
// variables de usuario Admin
$admin_id = 0;
$isEditingUser = false;
$username = "";
$role = "";
$email = "";
// variables generales
$errors = [];

//Acciones del usuario Admin

// Botón crear Admin
if (isset($_POST['create_admin'])) {
	createAdmin($_POST);
}
// Botón editar Admin
if (isset($_GET['edit-admin'])) {
	$isEditingUser = true;
	$admin_id = $_GET['edit-admin'];
	editAdmin($admin_id);
}
// Botón actualizar Admin
if (isset($_POST['update_admin'])) {
	updateAdmin($_POST);
}
// Botón Eliminar Admin
if (isset($_GET['delete-admin'])) {
	$admin_id = $_GET['delete-admin'];
	deleteAdmin($admin_id);
}


// Retorna a los usuarios Admin junto con su Rol
function getAdminUsers(){
	global $conn, $roles;
	$sql = "SELECT * FROM users WHERE role IS NOT NULL";
	$result = mysqli_query($conn, $sql);
	$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

	return $users;
}
// Evita la inyeccion de SQL
function esc(String $value){
	global $conn;
	$val = trim($value); 
	$val = mysqli_real_escape_string($conn, $value);
	return $val;
}


//Retorna una URL amigable 
function makeSlug(String $string){
	$string = strtolower($string);
	$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
	return $slug;
}
/* Funciones de Administrador*/
/* Crear un usuario */
function createAdmin($request_values){
	global $conn, $errors, $role, $username, $email;
	$username = esc($request_values['username']);
	$email = esc($request_values['email']);
	$password = esc($request_values['password']);
	$passwordConfirmation = esc($request_values['passwordConfirmation']);

	if(isset($request_values['role'])){
		$role = esc($request_values['role']);
	}
	// Validación del formulario
	if (empty($username)) { array_push($errors, "Vamos a necesitar el nombre de usuario"); }
	if (empty($email)) { array_push($errors, "Falta el correo"); }
	if (empty($role)) { array_push($errors, "Falta el rol para admin");}
	if (empty($password)) { array_push($errors, "Olvidaste la contraseña"); }
	if ($password != $passwordConfirmation) { array_push($errors, "No coinciden las contraseñas"); }
	$user_check_query = "SELECT * FROM users WHERE username='$username' 
							OR email='$email' LIMIT 1";
	$result = mysqli_query($conn, $user_check_query);
	$user = mysqli_fetch_assoc($result);
	//verifica que el nombre de usuario y el correo sean unicos
	if ($user) { 
		if ($user['username'] === $username) {
		  array_push($errors, "Ya existe ese nombre de usuario");
		}

		if ($user['email'] === $email) {
		  array_push($errors, "Ya existe ese email");
		}
	}
	//registra al usuario si no hay errores
	if (count($errors) == 0) {
		//encripta la contraseña
		$password = md5($password);
		$query = "INSERT INTO users (username, email, role, password, created_at, updated_at) 
				  VALUES('$username', '$email', '$role', '$password', now(), now())";
		mysqli_query($conn, $query);

		$_SESSION['message'] = "Admin creado";
		header('location: users.php');
		exit(0);
	}
}
// Editar usuario
function editAdmin($admin_id)
{
	global $conn, $username, $role, $isEditingUser, $admin_id, $email;

	$sql = "SELECT * FROM users WHERE id=$admin_id LIMIT 1";
	$result = mysqli_query($conn, $sql);
	$admin = mysqli_fetch_assoc($result);

	$username = $admin['username'];
	$email = $admin['email'];
}

//Actualiza usuario
function updateAdmin($request_values){
	global $conn, $errors, $role, $username, $isEditingUser, $admin_id, $email;
	$admin_id = $request_values['admin_id'];
	$isEditingUser = false;

	$username = esc($request_values['username']);
	$email = esc($request_values['email']);
	$password = esc($request_values['password']);
	$passwordConfirmation = esc($request_values['passwordConfirmation']);
	if(isset($request_values['role'])){
		$role = $request_values['role'];
	}
	if (count($errors) == 0) {
		$password = md5($password);

		$query = "UPDATE users SET username='$username', email='$email', role='$role', password='$password' WHERE id=$admin_id";
		mysqli_query($conn, $query);

		$_SESSION['message'] = "Admin actualizado";
		header('location: users.php');
		exit(0);
	}
}
// Eliminar usuario
function deleteAdmin($admin_id) {
	global $conn;
	$sql = "DELETE FROM users WHERE id=$admin_id";
	if (mysqli_query($conn, $sql)) {
		$_SESSION['message'] = "Usuario eliminado";
		header("location: users.php");
		exit(0);
	}
}


?>