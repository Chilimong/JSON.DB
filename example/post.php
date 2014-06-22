<?php
	if(!isset($_GET['id'])) die();
	$id = $_GET['id'];
	require('../jsondb.php');
	$o = new JSONDB('db.json');
	$post = $o->Select('posts', 'ID', $id, MYSQLI_ASSOC);
	if(count($post) == 0) die('This post does not exist.');
	else $post = $post[0];
	$comments = $o->Select('comments', 'Post', $id, MYSQLI_ASSOC);
?>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>JSON.DB - "<?=$post['Title'];?>"</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<!-- Bootstrap -->
		<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="container">
			<article>
				<h1 class="text-center"><?=$post['Title'];?></h1>
				<p><?=$post['Message'];?></p>
				<?php
					foreach($comments as $comment)
					{
					?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><?=$comment['Author'];?> </h3>
						</div>
						<div class="panel-body">
							<?=$comment['Message'];?>
						</div>
					</div>
					<?php
					}
				?>
			</article>
		</div>
		<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	</body>
</html>
