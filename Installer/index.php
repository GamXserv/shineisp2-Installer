<?php require_once "core.php"; ?>
<!doctype html>
<html>
<head>
<title><?php Config::APP_NAME . Config::APP_VERS ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link
	href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css"
	rel="stylesheet">
<link rel="stylesheet" href="css/main.css" type="text/css">
</head>
<body>
	<div id="wrap">
		<?php include_once Config::TPL_DIR . Config::DIR_SEP . "header.php"; ?>
		<?php include_once Config::TPL_DIR . Config::DIR_SEP . "error.php"; ?>
		<?php $router->check(); ?>
   	
	</div>
	<?php include_once Config::TPL_DIR . Config::DIR_SEP . "footer.php"; ?>
</body>
<script src="//code.jquery.com/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>