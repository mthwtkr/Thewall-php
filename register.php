<?php
	require_once('connection.php');
?>
<!doctype HTML>
<html>
<head>
	<meta charset="UTF-8" />
	<meta name="description" content="" />
	<title>Registration</title>
	<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/dark-hive/jquery-ui.css"/>
	<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/thewall.css" />
	<script type="text/javascript" src="http://code.jquery.com/jquery-2.0.3.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	
	<script>
	$(document).ready(function(){
		<?php
			if (isset($_SESSION['error']))
			{
				$errors = "";
				foreach ($_SESSION['error'] as $name => $message) 
				{
					$errors .= $message . '\n';
					echo "$('#{$name}').addClass('highlight');";
				}

				echo "alert('$errors');";
			}
		?>
	});
	</script>
</head>
<body>
	<div class="container">
		<div id="header" class="row">
			<div class="col-md-3 inline-block">
				<h1><a href='index.php'>CodingDojo Wall</a></h1>
			</div>
			<div class="col-md-2 pull-right inline-block">
				<form class="inline-block" action="process.php" method="post">
					<input type="hidden" name="action" value="login">
					<input type="text" id="login_email" name="login_email" placeholder="Email:">
					<input type="password" id="login_password" name="login_password" placeholder="Password">
					<input class="submit" type="submit" value="Login">
				</form>
			</div>
		</div>
		<div id="main_content">
			<div class="row">
				<form class="inline-block" action="process.php" method="post">
					<input type="hidden" name="action" value="register">
					<input type="text" id="first_name" name="first_name" placeholder="First Name:">
					<input type="text" id="last_name" name="last_name" placeholder="Last Name:">
					<input type="text" id="email" name="email" placeholder="Email:">
					<input type="password" id="password" name="password" placeholder="Password">
					<input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password">
					<input type="submit" value="Register">
				</form>
				<div id="register_success" class="inline-block">
					<?php
						if (isset($_SESSION['success']))
						{
							echo "<h2>{$_SESSION['success']}</h2>";
							echo "<img src='./images/success.png' alt='' />";
						}
					?>
				</div>
			</div>
		</div>
	</div> <!--End of #container -->
</body>
</html>
<?php
	unset($_SESSION['error']);
	unset($_SESSION['message']);
	unset($_SESSION['success']);
?>