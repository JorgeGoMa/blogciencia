<<<<<<< HEAD
<?php include('config.php'); ?>
<?php include('includes/public_functions.php'); ?>
<?php include('includes/header.php'); ?>
<?php 
	// Obtener publicaciones sobre un tema en particular
	if (isset($_GET['topic'])) {
		$topic_id = $_GET['topic'];
		$posts = getPublishedPostsByTopic($topic_id);
	}
?>
	<title>Blog Ciencia | Principal </title>
</head>
<body>
<div class="container">
<!-- Navbar -->
	<?php include( ROOT_PATH . '/includes/navbar.php'); ?>
<!-- // Navbar -->
<?php include( ROOT_PATH . '/includes/banner2.php'); ?>

<!-- content -->
<div class="content">
	<h2 class="content-title">
		Artículos en <u><?php echo getTopicNameById($topic_id); ?></u>
	</h2>
	<hr>
	<?php foreach ($posts as $post): ?>
		<div class="post" style="margin-left: 0px;">
			<img src="<?php echo BASE_URL . '/static/images/' . $post['image']; ?>" class="post_image" alt="">
			<a href="single_post.php?post-slug=<?php echo $post['slug']; ?>">
				<div class="post_info">
					<h3><?php echo $post['title'] ?></h3>
					<div class="info">
						<span><?php echo date("F j, Y ", strtotime($post["created_at"])); ?></span>
						<span class="read_more">Seguir leyendo...</span>
					</div>
				</div>
			</a>
		</div>
	<?php endforeach ?>
	<?php include( ROOT_PATH . '/includes/sidebar.php') ?>

    
</div>

<!-- // content -->
</div>
<!-- // container -->

<!-- Footer -->
	<?php include( ROOT_PATH . '/includes/footer.php'); ?>
<!-- // Footer -->
=======
<?php 
// Post variables
$post_id = 0;
$isEditingPost = false;
$published = 1;
$title = "";
$post_slug = "";
$body = "";
$featured_image = "";
$post_topic = "";
$user_id = $_SESSION['user']['id'];


/* - - - - - - - - - - 
-  Funciones
- - - - - - - - - - -*/
// obtener todas las publicaciones de DB
function getAllPosts()
{
	global $conn;
	
	// El administrador puede ver todas las publicaciones
	// El autor solo puede ver sus publicaciones
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
// Obtener el autor / nombre de usuario de una publicación.
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

/* - - - - - - - - - - 
-  Acciones
- - - - - - - - - - -*/
// si el usuario hace clic en el botón crear publicación
if (isset($_POST['create_post'])) { createPost($_POST); }
// si el usuario hace clic en el botón Editar publicación
if (isset($_GET['edit-post'])) {
	$isEditingPost = true;
	$post_id = $_GET['edit-post'];
	editPost($post_id);
}
// si el usuario hace clic en el botón de publicación de actualización
if (isset($_POST['update_post'])) {
	updatePost($_POST);
}
// si el usuario hace clic en el botón Eliminar publicación
if (isset($_GET['delete-post'])) {
	$post_id = $_GET['delete-post'];
	deletePost($post_id);
}

/* - - - - - - - - - - 
-  Funciones
- - - - - - - - - - -*/
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
		// crear babosa: si el título es "La tormenta ha terminado", devuelve "la-tormenta-terminó"
		$post_slug = makeSlug($title);
		// validate form
		if (empty($title)) { array_push($errors, "Título del artículo"); }
		if (empty($body)) { array_push($errors, "Cuerpo del artículo"); }
		if (empty($topic_id)) { array_push($errors, "Tema del artículo"); }
		// Obtener el nombre de la imagen
	  	$featured_image = $_FILES['featured_image']['name'];
	  	if (empty($featured_image)) { array_push($errors, "Falta imagen"); }
	  	// directorio de archivos de imagen
	  	$target = "../static/images/" . basename($featured_image);
	  	if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
	  		array_push($errors, "Error al cargar imagen. Compruebe la configuración de archivos de su servidor");
	  	}
		// Asegúrese de que ninguna publicación se guarde dos veces.
		$post_check_query = "SELECT * FROM posts WHERE slug='$post_slug' LIMIT 1";
		$result = mysqli_query($conn, $post_check_query);

		if (mysqli_num_rows($result) > 0) { // if post exists
			array_push($errors, "Ya existe un artículo con ese nombre.");
		}
		// crear publicación si no hay errores en el formulario
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

	/* * * * * * * * * * * * * * * * * * * * *
	* - Toma la identificación de la publicación como parámetro
	* - Obtiene la publicación de la base de datos
	* - establece campos de publicación en el formulario para editar
	* * * * * * * * * * * * * * * * * * * * * */
	function editPost($role_id)
	{
		global $conn, $title, $post_slug, $body, $published, $isEditingPost, $post_id;
		$sql = "SELECT * FROM posts WHERE id=$role_id LIMIT 1";
		$result = mysqli_query($conn, $sql);
		$post = mysqli_fetch_assoc($result);
		// establecer valores de formulario en el formulario que se actualizará
		$title = $post['title'];
		$body = $post['body'];
		$published = $post['published'];
	}

	function updatePost($request_values)
	{
		global $conn, $errors, $post_id, $title, $featured_image, $topic_id, $body, $published;

		$title = esc($request_values['title']);
		$body = esc($request_values['body']);
		$post_id = esc($request_values['post_id']);
		if (isset($request_values['topic_id'])) {
			$topic_id = esc($request_values['topic_id']);
		}
		// crear babosa: si el título es "La tormenta ha terminado", devuelve "la-tormenta-terminó"
		$post_slug = makeSlug($title);

		if (empty($title)) { array_push($errors, "Título del artículo"); }
		if (empty($body)) { array_push($errors, "Cuerpo del artículo"); }
		// si se ha proporcionado una nueva imagen destacada
		$featured_image = $_FILES['featured_image']['name'];
		if (isset($_POST['featured_image'])) {
			// Obtener el nombre de la imagen
		  	// directorio de archivos de imagen
		  	$target = "../static/images/" . basename($featured_image);
		  	if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
		  		array_push($errors, "Error al cargar imagen. Compruebe la configuración de archivos de su servidor");
		  	}
		}

		// registrar tema si no hay errores en el formulario
		if (count($errors) == 0) {
			$query = "UPDATE posts SET title='$title', slug='$post_slug', views=0, image='$featured_image', body='$body', published=$published, updated_at=now() WHERE id=$post_id";
			// adjuntar tema para publicar en la tabla post_topic
			if(mysqli_query($conn, $query)){ // if post created successfully
				if (isset($topic_id)) {
					$inserted_post_id = mysqli_insert_id($conn);
					// crear una relación entre la publicación y el tema
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
	// eliminar publicación de blog
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
    // si el usuario hace clic en el botón publicar publicación
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
// eliminar publicación de blog
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
>>>>>>> gustavo
