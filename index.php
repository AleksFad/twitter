<?php
include('libs/php/config.php');
// checks whether $_GET['id'] is set and it's not empty
$page_user = get_user((isset($_GET['id']) ? $_GET['id'] : 0));

if (isset($_POST['username']) && isset($_POST['fullname']) && isset($_POST['email']) && isset($_POST['password'])){
		
	$username = $_POST['username'];
	$fullname = $_POST['fullname'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	$query = ('INSERT INTO '.$prefix.'_users (username, fullname, email, password) values("'.$username.'","'.$fullname.'", "'.$email.'", "'.md5($password).'")');
	$result = $mysqli->query($query);
}

if (isset($_SESSION['vorgurakendused_4'])) {
	$logged_user = get_user();
	
	if (!$page_user) {
		$error = 'Can not get user data!'; 
	}
	
	if (isset($_POST['email']) && isset($_POST['age']) && isset($_POST['hobby']) && isset($_POST['music']) && isset($_POST['town'])){
			
		$email = $_POST['email'];
		$age = $_POST['age'];
		$hobby = $_POST['hobby'];
		$music = $_POST['music'];
		$town = $_POST['town'];

		$query = ('UPDATE '.$prefix.'_users SET email = "'.$email.'", age = "'.$age.'", hobby = "'.$hobby.'", music = "'.$music.'", town = "'.$town.'" WHERE session = "'.$_SESSION['vorgurakendused_4'].'"');
		$result = $mysqli->query($query);
		
		$page_user = get_user(0);
	}
	
	if (isset($_FILES['image'])){
		$name = $_FILES['image']['name'];
		$image = $_FILES['image']['tmp_name'];
		$extension = pathinfo($name, PATHINFO_EXTENSION);
		$filename = uniqid().'.'.$extension;
		$target = 'libs/profiles/'.$filename;
		
		$check = getimagesize($image);
		
		if ($check === false) {
			$error = 'The uploaded file is not an image!';
		}
		if ($_FILES['image']['size'] > $limit) {
			$error = 'The uploaded file exceeds limit of '.$limit.' bytes!';
		}
		if ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'png' && $extension != 'gif') {
			$error = 'The uploaded file extension is not allowed!';
		}
		
		if (!isset($error)) {
			move_uploaded_file($image, $target);
			
			$query = 'UPDATE '.$prefix.'_users SET image = "'.$filename.'" WHERE session = "'.$_SESSION['vorgurakendused_4'].'"';
			$result = $mysqli->query($query);
			
			$page_user = get_user(0);
		}
	}	
	
	if (isset($_POST['tweet'])){
			
		$text = $_POST['tweet'];

		$query = ('INSERT INTO '.$prefix.'_tweets (user_id, message) VALUES ("'.$page_user['id'].'", "'.$text.'")');
		$result = $mysqli->query($query);
	}
	
	if (isset($_GET['follow'])) {
		$tmp = get_user($_GET['follow']);
		
		if ($tmp !== false) {
			$query = ('INSERT INTO '.$prefix.'_followers (hash, user_id, following_id) VALUES ("'.md5( $logged_user['id'].'.'.$_GET['follow'] ).'", "'.$logged_user['id'].'", "'.$_GET['follow'].'")');
			$result = $mysqli->query($query);
			
			$message = 'You are following '.$tmp['fullname'];
		} else {
			$error = 'There is no such user_id, that you are trying to follow!';
		}
	}
	
	if (isset($_GET['unfollow'])) {
		$tmp = get_user($_GET['unfollow']);
		
		if ($tmp !== false) {
			$query = ('DELETE FROM '.$prefix.'_followers WHERE user_id = "'.$logged_user['id'].'" AND following_id = "'.$_GET['unfollow'].'"');
			$result = $mysqli->query($query);
			
			$message = 'You are unfollowing '.$tmp['fullname'];
		} else {
			$error = 'There is no such user_id, that you are trying to unfollow!';
		}
	}
}

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Twitter</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<!--<script src="libs/cjs/jquery-3.1.1.min.js"></script>-->
<script type="text/javascript" src="libs/js/script.js"></script>
<link rel="stylesheet" type="text/css" href="libs/css/style.css">
</head>
<body>
<div id="wrapper">
	<div id="header">
		<a href="<?php echo $path; ?>" class="logo">
			<img src="libs/images/logo.png" alt="" />
		</a>
		<?php if (isset($_SESSION['vorgurakendused_4'])) { ?>
		<span class="welcome">Welcome: <a href="<?php echo $path; ?>"><?php echo $logged_user['fullname']; ?></a> | <a href="logout.php">Logout</a></span>
		<?php } ?>
		<div class="search">
			<form method="GET">
			<input type="text" class="input_text" name="q" value="<?php echo (isset($_GET['q']) ? $_GET['q'] : ''); ?>" /><button type="submit" class="button">Search</button>
			</form>
		</div>
	</div>
	<div id="content" class="clearfix">
	
		<?php if (isset($message)) { ?>
			<div class="message"><h2><?php echo $message; ?></h2></div>
		<?php } ?>
		
		<?php if (isset($error)) { ?>
		
			<h1><?php echo $error; ?></h1>
			
		<?php } elseif (!isset($_GET['q']) && $page_user !== false) { ?>
		
			<div id="profile">
				<h2>Profile</h2>
				<div class="picture">
					<?php if (isset($_SESSION['vorgurakendused_4']) && $page_user['session'] == $_SESSION['vorgurakendused_4']) { ?>
					<form method="post" enctype="multipart/form-data">
					<input type="file" name="image" class="user-picture-file" />
					<img src="<?php echo ($page_user['image'] != '' ? 'libs/profiles/'.$page_user['image'] : 'libs/images/cat.jpg'); ?>" alt="" />
					</form>						
					<?php } else { ?>
					<img src="<?php echo ($page_user['image'] != '' ? 'libs/profiles/'.$page_user['image'] : 'libs/images/cat.jpg'); ?>" alt="" />
					<?php } ?>
				</div>
				
				<div class="block info">
					<?php if (isset($_SESSION['vorgurakendused_4']) && $page_user['session'] == $_SESSION['vorgurakendused_4']) { ?>
					<form method="POST">
					<?php } ?>
					
					<div class="cols"><span class="col col-1-3">Name:</span><span class="col col-2-3"><a href="<?php echo $path; ?>?id=<?php echo $page_user['id']; ?>"><?php echo $page_user['fullname']; ?></a></span></div>
					<?php if (isset($_SESSION['vorgurakendused_4']) && $page_user['session'] == $_SESSION['vorgurakendused_4']) { ?>
					<div class="cols"><span class="col col-1-3">Email:</span><span class="col col-2-3"><input type="text" class="input_text" name="email" value="<?php echo $page_user['email']; ?>" /></span></div>
					<div class="cols"><span class="col col-1-3">Age:</span><span class="col col-2-3"><input type="text" class="input_text" name="age" value="<?php echo $page_user['age']; ?>" /></span></div>
					<div class="cols"><span class="col col-1-3">Hobby:</span><span class="col col-2-3"><input type="text" class="input_text" name="hobby" value="<?php echo $page_user['hobby']; ?>" /></span></div>
					<div class="cols"><span class="col col-1-3">Music:</span><span class="col col-2-3"><input type="text" class="input_text" name="music" value="<?php echo $page_user['music']; ?>" /></span></div>
					<div class="cols"><span class="col col-1-3">Town:</span><span class="col col-2-3"><input type="text" class="input_text" name="town" value="<?php echo $page_user['town']; ?>" /></span></div>
					<div class="cols"><span class="col col-1-1"><button type="submit" class="button">Update</button></span></div>
					<?php } else { ?>
					<div class="cols"><span class="col col-1-3">Age:</span><span class="col col-2-3"><?php echo $page_user['age']; ?></span></div>
					<div class="cols"><span class="col col-1-3">Hobby:</span><span class="col col-2-3"><?php echo $page_user['hobby']; ?></span></div>
					<div class="cols"><span class="col col-1-3">Music:</span><span class="col col-2-3"><?php echo $page_user['music']; ?></span></div>
					<div class="cols"><span class="col col-1-3">Town:</span><span class="col col-2-3"><?php echo $page_user['town']; ?></span></div>
					<?php } ?>
					
					<?php if (isset($_SESSION['vorgurakendused_4']) && $page_user['session'] == $_SESSION['vorgurakendused_4']) { ?>
					</form>
					<?php } ?>
				</div>
				
				<div class="block following">
						<h2>I follow</h2>
						<div class="following-list">
						<?php
							$query = ('SELECT u.id, u.fullname FROM '.$prefix.'_users AS u LEFT JOIN '.$prefix.'_followers AS f ON u.id = f.following_id WHERE user_id = "'.$page_user['id'].'"');
							$result = $mysqli->query($query);
							if ($result && $result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									
									echo '<div class="following-item">';
									echo '<a href="'.$path.'?id='.$row['id'].'">'.$row['fullname'].'</a>';
									if (isset($_SESSION['vorgurakendused_4'])) {
										echo '<a href="'.$path.'?unfollow='.$row['id'].'" class="action">Unfollow</a>';
									}
									echo '</div>';
									
								}
							} else {
								echo '<h3>You are not following anyone</h3>';
							}
						?>
						</div>
				</div>
			</div>
			
			<div id="tweets">
				<div class="block tweets">
					<h2>Tweets timeline</h2>
					<?php 
						$query = ('SELECT t.id AS tweet_id, t.message AS message, t.added AS added, u.id AS following_id, u.fullname AS name FROM '.$prefix.'_tweets AS t LEFT JOIN '.$prefix.'_users AS u ON u.id = t.user_id LEFT JOIN '.$prefix.'_followers AS f ON f.following_id = t.user_id WHERE f.user_id = '.$page_user['id'].' ORDER BY t.added DESC');
						$result = $mysqli->query($query);
						if ($result && $result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
								echo '<div class="tweet-item"><div class="date"><a href="'.$path.'?id='.$row['following_id'].'">'.$row['name'].'</a> '.date('d.m.Y H:i', strtotime($row['added'])).'</div><div class="text">'.$row['message'].'</div></div>';
							}
						} else {
							echo '<h3>No tweets</h3>';
						}
					?>
				</div>
				<?php if (isset($_SESSION['vorgurakendused_4']) && $page_user['session'] == $_SESSION['vorgurakendused_4']) { ?>
				<div class="block tweet-add">
					<h2>Add a Tweet</h2>
					<form method="POST">
						<textarea name="tweet" class="textarea" rows="3" placeholder="What is happening?"></textarea>
						<button type="submit" class="button">Submit</button>
					</form>
				</div>
				<?php } ?>
				<div class="block tweets-list"> 
					<h2><?php echo $page_user['fullname']; ?>'s latest Tweets</h2>
					<?php 
						$query = ('SELECT * FROM '.$prefix.'_tweets WHERE user_id = '.$page_user['id'].' ORDER BY added Desc LIMIT 3');
						$result = $mysqli->query($query);
						if ($result && $result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
								echo '<div class="tweet-item"><div class="date">'.date('d.m.Y H:i', strtotime($row['added'])).'</div><div class="text">'.$row['message'].'</div></div>';
							}
						} else {
							echo '<h3>No tweets</h3>';
						}
					?>
				</div>
			</div>
		
		<?php } elseif (isset($_GET['q'])) { ?>
		
			<div id="search">
				<h2>Search</h2>
				<div class="search-list">
					<?php 
						$query = ('SELECT * FROM '.$prefix.'_users WHERE fullname LIKE "%'.$_GET['q'].'%" ORDER BY fullname Desc');
						$result = $mysqli->query($query);
						if ($result && $result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
								echo '<div class="search-item">';
								echo '<a href="'.$path.'?id='.$row['id'].'">'.$row['fullname'].'</a>';
								if (isset($_SESSION['vorgurakendused_4']) && $page_user['id'] != $row['id']) {
									if (check_follow($row['id'])) {
										echo '<a href="'.$path.'?unfollow='.$row['id'].'" class="action">Unfollow</a>';
									} else {
										echo '<a href="'.$path.'?follow='.$row['id'].'" class="action">Follow</a>';
									}
								}
								echo '</div>';
							}
						} else {
							echo '<h3>Nothing found, <a href="'.$path.'">Home</a></h3>';
						}
					?>
				</div>
			</div>
			
		<?php } else { ?>
		
			<div id="sign_in">
				<h2>Log in</h2>
				<form method="post" action="login.php">
				<input type="text" class="input_text" name="login_username" placeholder="Username" required>
				<input type="password" class="input_text" name="login_password" placeholder="Password" required>
				<button type="submit" class="button">Log in</button>
				</form>
			</div>
			<div id="sign_up">
				<h2>New to Twitter? Sign up and enjoy!</h2>
				<form method="post">
				<input type="text" placeholder="Username" class="input_text" name="username" required />
				<input type="text" placeholder="Full name" class="input_text" name="fullname" required />
				<input type="text" placeholder="Email" class="input_text" name="email" required>
				<input type="password" placeholder="Password" class="input_text" name="password" required>
				<button type="submit" class="button">Sign up for Twitter</button>
				</form>
			</div>
			
		<?php } ?>
	</div>
</div>
</body>
</html>