<?php  include('../config.php'); ?>
	<?php include(ROOT_PATH . '/admin/includes/admin_functiones.php'); ?>
	<?php include(ROOT_PATH . '/admin/includes/header.php'); ?>
	<title>Admin | Panel</title>
</head>
<body>
	<div class="header">
		<div class="logo">
			<a href="<?php echo BASE_URL .'admin/dashboard.php' ?>">
				<h1>Blog Cientifico - Admin</h1>
			</a>
		</div>
		<?php if (isset($_SESSION['user'])): ?>
			<div class="user-info">
				<span><?php echo $_SESSION['user']['username'] ?></span> &nbsp; &nbsp; 
				<a href="<?php echo BASE_URL . '/logout.php'; ?>" class="logout-btn">Salir</a>
			</div>
		<?php endif ?>
	</div>
	<div class="container dashboard">
		<h1>Bienvenido</h1>
		<div class="stats">
			<a href="users.php" class="first">
				<span>Usuarios</span>
			</a>
			<a href="posts.php">
				<span>Publicaciones</span>
			</a>
			
		</div>
		<br><br><br>
		<div class="buttons">
			<a href="users.php">Añadir Usuario</a>
			<a href="posts.php">Añadir Publicación</a>
		</div>
	</div>
</body>
</html>