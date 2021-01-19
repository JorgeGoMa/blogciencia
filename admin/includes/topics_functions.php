<?php 
// Admin user variables
$admin_id = 0;
$isEditingUser = false;
$username = "";
$role = "";
$email = "";
// general variables
$errors = [];
// Topics variables
$topic_id = 0;
$isEditingTopic = false;
$topic_name = "";

/* - - - - - - - - - - 
-  Admin users actions
- - - - - - - - - - -*/
// if user clicks the create admin button
if (isset($_POST['create_admin'])) {
	createAdmin($_POST);
}
// if user clicks the Edit admin button
if (isset($_GET['edit-admin'])) {
	$isEditingUser = true;
	$admin_id = $_GET['edit-admin'];
	editAdmin($admin_id);
}
// if user clicks the update admin button
if (isset($_POST['update_admin'])) {
	updateAdmin($_POST);
}
// if user clicks the Delete admin button
if (isset($_GET['delete-admin'])) {
	$admin_id = $_GET['delete-admin'];
	deleteAdmin($admin_id);
}
/* - - - - - - - - - - 
-  Topics functions
- - - - - - - - - - -*/
// get all topics from DB
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
	// create slug: if topic is "Life Advice", return "life-advice" as slug
	$topic_slug = makeSlug($topic_name);
	// validate form
	if (empty($topic_name)) { 
		array_push($errors, "Tema requerido"); 
	}
	// Ensure that no topic is saved twice. 
	$topic_check_query = "SELECT * FROM topics WHERE slug='$topic_slug' LIMIT 1";
	$result = mysqli_query($conn, $topic_check_query);
	if (mysqli_num_rows($result) > 0) { // if topic exists
		array_push($errors, "Ya existe este tema");
	}
	// register topic if there are no errors in the form
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
* - Takes topic id as parameter
* - Fetches the topic from database
* - sets topic fields on form for editing
* * * * * * * * * * * * * * * * * * * * * */
function editTopic($topic_id) {
	global $conn, $topic_name, $isEditingTopic, $topic_id;
	$sql = "SELECT * FROM topics WHERE id=$topic_id LIMIT 1";
	$result = mysqli_query($conn, $sql);
	$topic = mysqli_fetch_assoc($result);
	// set form values ($topic_name) on the form to be updated
	$topic_name = $topic['name'];
}
function updateTopic($request_values) {
	global $conn, $errors, $topic_name, $topic_id;
	$topic_name = esc($request_values['topic_name']);
	$topic_id = esc($request_values['topic_id']);
	// create slug: if topic is "Life Advice", return "life-advice" as slug
	$topic_slug = makeSlug($topic_name);
	// validate form
	if (empty($topic_name)) { 
		array_push($errors, "Falta tema"); 
	}
	// register topic if there are no errors in the form
	if (count($errors) == 0) {
		$query = "UPDATE topics SET name='$topic_name', slug='$topic_slug' WHERE id=$topic_id";
		mysqli_query($conn, $query);

		$_SESSION['message'] = "Tema actualizado";
		header('location: topics.php');
		exit(0);
	}
}
// delete topic 
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