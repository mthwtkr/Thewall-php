<?php
	require_once('connection.php');
?>
<!doctype HTML>
<html>
<head>
	<meta charset="UTF-8" />
	<meta name="description" content="" />
	<title>Profile</title>
	<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/dark-hive/jquery-ui.css"/>
	<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/thewall.css" />
	<script type="text/javascript" src="http://code.jquery.com/jquery-2.0.3.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	
	<script>
	$(document).ready(function(){
		$('#post_comment form').hide();
		$('#post_comment h2').click(function(){
			$(this).siblings().toggle();
		});		

		$('#post_comment h2').mouseenter(function(){
				$(this).css('color', 'blue');
			});
		$('#post_comment h2').mouseleave(function(){
			$(this).removeAttr('style');
		});

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
		<?php
			$query = "SELECT first_name
					  FROM users
					  WHERE id = '{$_GET['id']}'";
			$user = fetch_record($query);
		?>
		<div id="header" class="row">
			<div class="col-md-3 inline-block">
				<h1><a href='index.php'>CodingDojo Wall</a></h1>
			</div>
			<div class="col-md-2 pull-right inline-block">
				<?php
						if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $_GET['id'])
					{
						$first_name = $user['first_name'];
						echo "<p class='inline-block'>Welcome {$user['first_name']}";
						echo "<a id='logout' class='inline-block' href='process.php'>logout</a></p>";
					}
				?>
			</div>
		</div>
	<div id="main_content">
			<div class="row">
				<div id="post_message" class="col-md-5">
					<h2>Post a message</h2>
					<form action="process.php" method="post">
						<input type="hidden" name="action" value="post_message">
						<textarea type="text" id="message" name="message"></textarea>
						<input id="message_submit" type="submit" value="Post a message">
					</form>
				</div>
				<div class="col-md-5">
					<?php
						$query = "SELECT messages.id, users.first_name, users.last_name, message, messages.created_at, user_id 
								  FROM messages
								  JOIN users ON messages.user_id = users.id
								  ORDER BY messages.created_at DESC";
						$messages = fetch_all($query);

						$query = "SELECT comment, message_id, comments.created_at, users.first_name, users.last_name
								  FROM comments
								  JOIN users ON comments.user_id = users.id
								  JOIN messages ON comments.message_id = messages.id
								  ORDER BY comments.created_at ASC";
						$comments = fetch_all($query);

						if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $_GET['id'])
						{
							foreach($messages as $message)
							{
								$phpdate = strtotime($message['created_at']);
								$created_at = date( 'M jS Y', $phpdate);

								echo "<div class='message_box'>";
								echo "<p class='message_title inline-block'>{$message['first_name']} {$message['last_name']} - {$created_at}</p>";
								if ($_SESSION['user_id'] == $message['user_id'])
								{
									echo "
										<div id='delete_message' class='inline-block'>
											<form class='inline-block' action='process.php' method='post'>
												<input type='hidden' name='action' value='delete'>
												<input type='hidden' name='id' value='{$message['id']}'>
												<input id='delete_submit' type='submit' value='Delete'>
											</form>
											<button class='inline-block' id='edit'>Edit</button>
										</div>
										";
								}
								echo "<p class='message'>{$message['message']}</p>";
								echo "</div>";

								foreach ($comments as $comment)
								{
									if ($message['id'] == $comment['message_id'])
									{
										$phpdate = strtotime($comment['created_at']);
										$created_at = date( 'M jS Y', $phpdate);

										echo "<p class='comment_title'>{$comment['first_name']} {$comment['last_name']} - {$created_at}</p>";
										echo "<p class='comment'>{$comment['comment']}</p>";
									}
								}
								
								echo "
										<div id='post_comment' class='col-md-5'>
											<h2>Post a comment</h2>
											<form action='process.php' method='post'>
												<input type='hidden' name='action' value='post_comment'>
												<input type='hidden' name='message_id' value='{$message['id']}'>
												<textarea wrap='soft'; type='text' id='comment' name='comment'></textarea>
												<input id='comment_submit' type='submit' value='Post a comment'>
											</form>
										</div>
									";
							}
						}
					?>
				</div> <!--End of div class="col-md-5" -->
			</div> <!--End of div class="row" -->
		</div> <!--End of #main_content -->
	</div> <!--End of #container -->
</body>
</html>
<?php
	unset($_SESSION['error']);
	unset($_SESSION['message']);
	unset($_SESSION['success']);
?>