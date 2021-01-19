<?php 
// Variables de usuario administrador
$admin_id = 0;
$isEditingUser = false;
$username = "";
$role = "";
$email = "";
// variables generales
$errors = [];
// Variables de temas
$topic_id = 0;
$isEditingTopic = false;
$topic_name = "";

/* - - - - - - - - - - 
-  Acciones de los usuarios administradores
- - - - - - - - - - -*/
// si el usuario hace clic en el botón crear administrador
if (isset($_POST['create_admin'])) {
	createAdmin($_POST);
}
// si el usuario hace clic en el botón Editar administrador
if (isset($_GET['edit-admin'])) {
	$isEditingUser = true;
	$admin_id = $_GET['edit-admin'];
	editAdmin($admin_id);
}
// si el usuario hace clic en el botón actualizar administrador
if (isset($_POST['update_admin'])) {
	updateAdmin($_POST);
}
// si el usuario hace clic en el botón Eliminar administrador
if (isset($_GET['delete-admin'])) {
	$admin_id = $_GET['delete-admin'];
	deleteAdmin($admin_id);
}
/* - - - - - - - - - - 
-  Funciones de temas
- - - - - - - - - - -*/
// obtener todos los temas de DB
function getAllTopics() {
	global $conn;
	$sql = "SELECT * FROM topics";
	$result = mysqli_query($conn, $sql);
	$topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
	return $topics;
}
function createTopic($request_values){
	global $conn, $errors, $topic_name;
	$topic_name = esc($request_values['topic_name']);
	// crea slug: si el tema es "Life Advice", devuelve "life-advice" como slug
	$topic_slug = makeSlug($topic_name);
	// validar formulario
	if (empty($topic_name)) { 
		array_push($errors, "Tema requerido"); 
	}
	// Asegúrese de que ningún tema se guarde dos veces.
	$topic_check_query = "SELECT * FROM topics WHERE slug='$topic_slug' LIMIT 1";
	$result = mysqli_query($conn, $topic_check_query);
	if (mysqli_num_rows($result) > 0) { // si el tema existe
		array_push($errors, "Ya existe este tema");
	}
	// registrar tema si no hay errores en el formulario
	if (count($errors) == 0) {
		$query = "INSERT INTO topics (name, slug) 
				  VALUES('$topic_name', '$topic_slug')";
		mysqli_query($conn, $query);

		$_SESSION['message'] = "Tema creado";
		header('location: topics.php');
		exit(0);
	}
}
/* * * * * * * * * * * * * * * * * * * * *
* - Toma la identificación del tema como parámetro
* - Obtiene el tema de la base de datos
* - establece campos de tema en el formulario para editar
* * * * * * * * * * * * * * * * * * * * * */
function editTopic($topic_id) {
	global $conn, $topic_name, $isEditingTopic, $topic_id;
	$sql = "SELECT * FROM topics WHERE id=$topic_id LIMIT 1";
	$result = mysqli_query($conn, $sql);
	$topic = mysqli_fetch_assoc($result);
	// establecer valores de formulario ($ topic_name) en el formulario que se actualizará
	$topic_name = $topic['name'];
}
function updateTopic($request_values) {
	global $conn, $errors, $topic_name, $topic_id;
	$topic_name = esc($request_values['topic_name']);
	$topic_id = esc($request_values['topic_id']);
	// crea slug: si el tema es "Life Advice", devuelve "life-advice" como slug
	$topic_slug = makeSlug($topic_name);
	// validar formulario
	if (empty($topic_name)) { 
		array_push($errors, "Falta tema"); 
	}
	// registrar tema si no hay errores en el formulario
	if (count($errors) == 0) {
		$query = "UPDATE topics SET name='$topic_name', slug='$topic_slug' WHERE id=$topic_id";
		mysqli_query($conn, $query);

		$_SESSION['message'] = "Tema actualizado";
		header('location: topics.php');
		exit(0);
	}
}
// eliminar tema 
function deleteTopic($topic_id) {
	global $conn;
	$sql = "DELETE FROM topics WHERE id=$topic_id";
	if (mysqli_query($conn, $sql)) {
		$_SESSION['message'] = "Tema eliminado";
		header("location: topics.php");
		exit(0);
	}
}
?>