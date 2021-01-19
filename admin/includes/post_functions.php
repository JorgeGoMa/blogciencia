<?php 
// Variables de las Publicaciones
$post_id = 0;
$isEditingPost = false;
$published = 1;
$title = "";
$post_slug = "";
$body = "";
$featured_image = "";
$post_topic = "";
$user_id = $_SESSION['user']['id'];


//obtener todas las publicaciones
function getAllPosts()
{
	global $conn;
	
	//Los admin pueden ver todas las publicaciones, los editores solo pueden ver las suyas.
	if ($_SESSION['user']['role'] == "Admin") {
		$sql = "SELECT * FROM posts";
	} elseif ($_SESSION['user']['role'] == "Autor") {
		$user_id = $_SESSION['user']['id'];
		$sql = "SELECT * FROM posts WHERE user_id=$user_id";
	}
	$result = mysqli_query($conn, $sql);
	$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

	$final_posts = array();
	foreach ($posts as $post) {
		$post['autor'] = getPostAuthorById($post['user_id']);
		array_push($final_posts, $post);
	}
	return $final_posts;
}

// obtiene el autor de una publicación
function getPostAuthorById($user_id)
{
	global $conn;
	$sql = "SELECT username FROM users WHERE id=$user_id";
	$result = mysqli_query($conn, $sql);
	if ($result) {
		// return username
		return mysqli_fetch_assoc($result)['username'];
	} else {
		return null;
	}
}

// Botón crear publicación
if (isset($_POST['create_post'])) { 
    createPost($_POST); 
}
// Botón editar publicación
if (isset($_GET['edit-post'])) {
	$isEditingPost = true;
	$post_id = $_GET['edit-post'];
	editPost($post_id);
}
// Botón actualizar publicación
if (isset($_POST['update_post'])) {
	updatePost($_POST);
}
// Botón eliminar publicación
if (isset($_GET['delete-post'])) {
	$post_id = $_GET['delete-post'];
	deletePost($post_id);
}

//Crear publicaciones
function createPost($request_values)
	{
		global $conn, $errors, $title, $featured_image, $topic_id, $body, $published, $user_id;
		$title = esc($request_values['title']);
		$body = htmlentities(esc($request_values['body']));
		if (isset($request_values['topic_id'])) {
			$topic_id = esc($request_values['topic_id']);
		}
		if (isset($request_values['publish'])) {
			$published = esc($request_values['publish']);
		}
		$post_slug = makeSlug($title);
		// validar formulario
		if (empty($title)) { array_push($errors, "Título del artículo"); }
		if (empty($body)) { array_push($errors, "Cuerpo del artículo"); }
		if (empty($topic_id)) { array_push($errors, "Tema del artículo"); }
		// obtener nombre de imagen
	  	$featured_image = $_FILES['featured_image']['name'];
	  	if (empty($featured_image)) { array_push($errors, "Falta imagen"); }
	  	$target = "../static/images/" . basename($featured_image);
	  	if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
	  		array_push($errors, "Error al cargar imagen. Compruebe la configuración de archivos de su servidor");
	  	}
        //Verifica que el nombre de la pubñlicación sea unico
        $post_check_query = "SELECT * FROM posts WHERE slug='$post_slug' LIMIT 1";
		$result = mysqli_query($conn, $post_check_query);
		if (mysqli_num_rows($result) > 0) { 
			array_push($errors, "Ya existe un artículo con ese nombre.");
        }
        //Crea la publicación si no se encuentran errores
		if (count($errors) == 0) {
			
			$query = "INSERT INTO posts (user_id, title, slug, image, body, published, created_at, updated_at) VALUES('$user_id', '$title', '$post_slug', '$featured_image', '$body', $published, now(), now())";
			if(mysqli_query($conn, $query)){ // if post created successfully
				$inserted_post_id = mysqli_insert_id($conn);
				// create relationship between post and topic
				$sql = "INSERT INTO post_topic (post_id, topic_id) VALUES($inserted_post_id, $topic_id)";
				mysqli_query($conn, $sql);

				$_SESSION['message'] = "Artículo creado";
				header('location: posts.php');
				exit(0);
			}
		}
	}

	//editar publicación
	function editPost($role_id)
	{
		global $conn, $title, $post_slug, $body, $published, $isEditingPost, $post_id;
		$sql = "SELECT * FROM posts WHERE id=$role_id LIMIT 1";
		$result = mysqli_query($conn, $sql);
		$post = mysqli_fetch_assoc($result);
		// set form values on the form to be updated
		$title = $post['title'];
		$body = $post['body'];
		$published = $post['published'];
	}

    //Actualizar publicación
	function updatePost($request_values)
	{
		global $conn, $errors, $post_id, $title, $featured_image, $topic_id, $body, $published;

		$title = esc($request_values['title']);
		$body = esc($request_values['body']);
		$post_id = esc($request_values['post_id']);
		if (isset($request_values['topic_id'])) {
			$topic_id = esc($request_values['topic_id']);
		}
		$post_slug = makeSlug($title);
		if (empty($title)) { array_push($errors, "Título del artículo"); }
		if (empty($body)) { array_push($errors, "Cuerpo del artículo"); }
		$featured_image = $_FILES['featured_image']['name'];
		if (isset($_POST['featured_image'])) {
		  	$target = "../static/images/" . basename($featured_image);
		  	if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
		  		array_push($errors, "Error al cargar imagen. Compruebe la configuración de archivos de su servidor");
		  	}
		}

		if (count($errors) == 0) {
			$query = "UPDATE posts SET title='$title', slug='$post_slug', views=0, image='$featured_image', body='$body', published=$published, updated_at=now() WHERE id=$post_id";
			if(mysqli_query($conn, $query)){ 
				if (isset($topic_id)) {
					$inserted_post_id = mysqli_insert_id($conn);
					// Se crea una relación entre las publiicaciones y las etiquetas
					$sql = "INSERT INTO post_topic (post_id, topic_id) VALUES($inserted_post_id, $topic_id)";
					mysqli_query($conn, $sql);
					$_SESSION['message'] = "Artículo creado";
					header('location: posts.php');
					exit(0);
				}
			}
			$_SESSION['message'] = "Artículo editado";
			header('location: posts.php');
			exit(0);
		}
	}
    
    // Eliminar publicación
	function deletePost($post_id)
	{
		global $conn;
		$sql = "DELETE FROM posts WHERE id=$post_id";
		if (mysqli_query($conn, $sql)) {
			$_SESSION['message'] = "Artículo eliminado";
			header("location: posts.php");
			exit(0);
		}
    }
    // Alterna la visibilidad de la publicación
if (isset($_GET['publish']) || isset($_GET['unpublish'])) {
	$message = "";
	if (isset($_GET['publish'])) {
		$message = "Artículo público exitoso";
		$post_id = $_GET['publish'];
	} else if (isset($_GET['unpublish'])) {
		$message = "Artículo sin publicar exitoso";
		$post_id = $_GET['unpublish'];
	}
	togglePublishPost($post_id, $message);
}

function togglePublishPost($post_id, $message)
{
	global $conn;
	$sql = "UPDATE posts SET published=!published WHERE id=$post_id";
	
	if (mysqli_query($conn, $sql)) {
		$_SESSION['message'] = $message;
		header("location: posts.php");
		exit(0);
	}
}
?>