<?php  include('../config.php'); ?>
<?php  include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
<?php  include(ROOT_PATH . '/admin/includes/post_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/header.php'); ?>

<!-- Obtiene rodos las publicaciones de la BD -->
<?php $posts = getAllPosts(); ?>
	<title>Admin | Administrar Artículos</title>
</head>
<body>
	<?php include(ROOT_PATH . '/admin/includes/navbar.php') ?>

	<div class="container content">
		<?php include(ROOT_PATH . '/admin/includes/menu.php') ?>
		<div class="table-div"  style="width: 80%;">
			<?php include(ROOT_PATH . '/includes/messages.php') ?>
            <!--Verifica si existen publicaciones -->
			<?php if (empty($posts)): ?>
				<h1 style="text-align: center; margin-top: 20px;">No hay artículos.</h1>
            <!--Muestra las publicaciones en BD -->    
			<?php else: ?>
				<table class="table">
						<thead>
						<th>N</th>
						<th>Autor</th>
						<th>Título</th>
						<!-- Solo los administradores pueden cambiar la visibilidad de la publicación -->
						<?php if ($_SESSION['user']['role'] == "Admin"): ?>
							<th><small>Público</small></th>
						<?php endif ?>
						<th><small>Editar</small></th>
						<th><small>Eliminar</small></th>
					</thead>
					<tbody>
					<?php foreach ($posts as $key => $post): ?>
						<tr>
							<td><?php echo $key + 1; ?></td>
							<td><?php echo $post['autor']; ?></td>
							<td>
								<a 	target="_blank"
								href="<?php echo BASE_URL . 'single_post.php?post-slug=' . $post['slug'] ?>">
									<?php echo $post['title']; ?>	
								</a>
							</td>
							
							<?php if ($_SESSION['user']['role'] == "Admin" ): ?>
								<td>
								<?php if ($post['published'] == true): ?>
									<a class="fa fa-check btn unpublish"
										href="posts.php?unpublish=<?php echo $post['id'] ?>">
									</a>
								<?php else: ?>
									<a class="fa fa-times btn publish"
										href="posts.php?publish=<?php echo $post['id'] ?>">
									</a>
								<?php endif ?>
								</td>
							<?php endif ?>

							<td>
								<a class="fa fa-pencil btn edit"
									href="create_post.php?edit-post=<?php echo $post['id'] ?>">
								</a>
							</td>
							<td>
								<a  class="fa fa-trash btn delete" 
									href="create_post.php?delete-post=<?php echo $post['id'] ?>">
								</a>
							</td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			<?php endif ?>
		</div>
	</div>
</body>
</html>