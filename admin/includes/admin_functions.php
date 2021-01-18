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
?>