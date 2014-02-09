<?php
	require_once('connection.php');

	function register($connection, $post)
	{
		// For each field in register form
		foreach ($post as $name => $value) 
		{
			// If form field is empty
			if (empty($value))
			{
				$_SESSION['error'][$name] = "sorry, " . $name . " cannot be blank";
			}
			else
			{
				switch ($name)
				{
					case 'first_name':
					case 'last_name':
						if (is_numeric($value))
						{
							$_SESSION['error'][$name] = $name . ' cannot contain numbers';
						}
						break;
					case 'email':
						if (!filter_var($value, FILTER_VALIDATE_EMAIL))
						{
							$_SESSION['error'][$name] = $name . ' is not a valid email';
						}
						break;
					case 'password':
						$password = $value;
						if (strlen($value) < 5)
						{
							$_SESSION['error'][$name] = $name . ' must be greater than 5 characters';
						}
						break;
					case 'confirm_password':
						if ($password != $value)
						{
							$_SESSION['error'][$name] = ' Passwords do not match';
						}
						break;
				}
			}
		}

		// If there are not any values in $_SESSION['error']
			// Display successful registration
		if (!isset($_SESSION['error']))
		{
			$_SESSION['success'] = 'Thanks for joining the CodingDojo Wall!';

			$salt = bin2hex(round(microtime(true) * 1000));
			$hash = crypt($post['password'], $salt);

			$first_name = $connection->real_escape_string($post['first_name']);
			$last_name = $connection->real_escape_string($post['last_name']);
			$email = $connection->real_escape_string($post['email']);

			$query = "INSERT INTO users (first_name, last_name, email, password, created_at, updated_at)
					  VALUES ('$first_name', '$last_name', '$email', '$hash', NOW(), NOW())";
			run_mysql_query($query);
		}
		header('Location: register.php');
		exit;
	}

	// Validate posted user information
	// If user exists in database, login the user
	function login($connection, $post)
	{
		// Check to see if  email or passwords fields are empty
			// Throw an error
		if (empty($post['login_email']) || empty($post['login_password']))
			$_SESSION['error']['message'] = "Email or Password cannot be blank";
		// Fields are not empty
			// Query database for valid user information
		else
		{
			$query = "SELECT id, password
					  FROM users
					  WHERE email = '{$post['login_email']}'";
			$row = fetch_record($query);

			// Query returned an empty row
				// Throw could not find entry error
			if (empty($row))
				$_SESSION['error']['message'] = 'Could not find Email in database';
			// Query returned a valid entry
			else
			{
				if (crypt($post['login_password'], $row['password']) != $row['password'])
				{
					$_SESSION['error']['message'] = 'Incorrect Password';
				}
				else
				{
					$_SESSION['user_id'] = $row['id'];
					header('Location: profile.php?id='.$row['id']);
					exit;
				}
			}
		}
		header('Location: index.php');
		exit;
	}

	function post_message($connection, $post)
	{
		if (empty($post['message']))
		{
			$_SESSION['error']['message'] = "Cannot post a blank message!";
		}
		else
		{
			$message = $connection->real_escape_string($post['message']);
			$user_id = $connection->real_escape_string($_SESSION['user_id']);

			$query = "INSERT INTO messages (message, created_at, updated_at, user_id)
					  VALUES ('$message', NOW(), NOW(), $user_id)";
			run_mysql_query($query);
		}
		header('Location: profile.php?id='.$_SESSION['user_id']);
		exit;
	}

	function post_comment($connection, $post)
	{
		if (empty($post['comment']))
		{
			$_SESSION['error']['message'] = "Cannot post a blank comment!";
		}
		else
		{
			$comment = $connection->real_escape_string($post['comment']);
			$message_id = $connection->real_escape_string($post['message_id']);
			$user_id = $connection->real_escape_string($_SESSION['user_id']);

			$query = "INSERT INTO comments (comment, created_at, updated_at, message_id, user_id)
					  VALUES ('$comment', NOW(), NOW(), $message_id, $user_id)";
			run_mysql_query($query);
		}
		header('Location: profile.php?id='.$_SESSION['user_id']);
		exit;
	}

	function delete_message($connection, $post)
	{
		$query = "SELECT user_id FROM messages WHERE id = {$post['id']}";
		$user = fetch_record($query);

		if ($user['user_id'] == $_SESSION['user_id'])
		{
			$query = "DELETE FROM comments WHERE message_id =". $post['id'];
			run_mysql_query($query);
			$query = "DELETE FROM messages WHERE id =". $post['id'];
			run_mysql_query($query);
			header('Location: profile.php?id='.$_SESSION['user_id']);
			exit;
		}
		
		header('Location: nope.php?id='.$_SESSION['user_id']);
		exit;
	}

	function logout()
	{
		$_SESSION = array();
		session_destroy();
	}

	if (isset($_POST['action']) && $_POST['action'] == 'register')
	{
		register($connection, $_POST);
	}
	elseif (isset($_POST['action']) && $_POST['action'] == 'login')
	{
		login($connection, $_POST);
	}
	elseif (isset($_POST['action']) && $_POST['action'] == 'post_message')
	{
		post_message($connection, $_POST);
	}
	elseif (isset($_POST['action']) && $_POST['action'] == 'post_comment')
	{
		post_comment($connection, $_POST);
	}
	elseif (isset($_POST['action']) && $_POST['action'] == 'delete')
	{
		delete_message($connection, $_POST);
	}
	elseif (isset($_GET['logout']))
	{
		logout();
	}

header('Location: index.php');
?>