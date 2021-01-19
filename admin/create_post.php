<?php  include('../config.php'); ?>
<?php  include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
<?php  include(ROOT_PATH . '/admin/includes/post_functions.php'); ?>
<?php  include(ROOT_PATH . '/admin/includes/topics_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/header.php'); ?>
<!-- Obtener las Etiquetas -->
<?php $topics = getAllTopics();	?>
	<title>Admin | Crear artículo</title>
</head>
<body>	
	<?php include(ROOT_PATH . '/admin/includes/navbar.php') ?>
	<div class="container content">		
		<?php include(ROOT_PATH . '/admin/includes/menu.php') ?>		
		<div class="action create-post-div">
			<h1 class="page-title">Crear/Editar Artículo</h1>
			<form method="post" enctype="multipart/form-data" action="<?php echo BASE_URL . 'admin/create_post.php'; ?>" >
				<!-- Validar Errores -->
				<?php include(ROOT_PATH . '/includes/errors.php') ?>

				<?php if ($isEditingPost === true): ?>
					<input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
				<?php endif ?>

				<input type="text" name="title" value="<?php echo $title; ?>" placeholder="Título">
				<label style="float: left; margin: 5px auto 5px;">Imagen</label>
				<input type="file" name="featured_image" >
				<textarea name="body" id="body" cols="30" rows="10"><?php echo $body; ?></textarea>
				<select name="topic_id">
					<option value="" selected disabled>Elegir tema</option>
					<?php foreach ($topics as $topic): ?>
						<option value="<?php echo $topic['id']; ?>">
							<?php echo $topic['name']; ?>
						</option>
					<?php endforeach ?>
				</select>
				
				<!-- Solo los admins pueden ocultar las publicaciones -->
				<?php if ($_SESSION['user']['role'] == "Admin"): ?>
					<?php if ($published == true): ?>
						<label for="publish">
							Publicar
							<input type="checkbox" value="1" name="publish" checked="checked">&nbsp;
						</label>
					<?php else: ?>
						<label for="publish">
							Publicar
							<input type="checkbox" value="1" name="publish">&nbsp;
						</label>
					<?php endif ?>
				<?php endif ?>
				
				<?php if ($isEditingPost === true): ?> 
					<button type="submit" class="btn" name="update_post">Actualizar</button>
				<?php else: ?>
					<button type="submit" class="btn" name="create_post">Guardar Artículo</button>
				<?php endif ?>

			</form>
		</div>
	</div>
</body>
</html>
