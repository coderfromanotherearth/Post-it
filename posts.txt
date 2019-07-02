<?php
	$username=$_POST['username'];
?>
<html>
	<head>
	  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	</head>
	<body>
		<div class="container pull-right">
			<span class="glyphicon glyphicon-user pull-right">  Logged in as  <label><?php echo $username ?>  </label></span>
		</div>
		<div class="container pull-right">
			<form action="login.php">
				<button type="submit" class="pull-right">Logout</button>
			</form>
		</div>

		 <form action="" method="post" >
			
				<input type="hidden" value="<?php echo $username ?>" name="username"> 
				<br>
		  	<div class="container">
				<input type="text" placeholder="Write something.." name="posted_text" required>
				<br><br>
				<button type="submit" class="btn btn-info" > Post</button>
		  	
	

		  </form> 
	</body>
</html>

<?php

	#$collection_1 = 'likes_db';
	#$collection_2 = 'likes_consistency';	
	$username=$_POST['username'];
	$posted_text = $_POST['posted_text'];
	$mng = new MongoDB\Driver\Manager("mongodb://localhost:27017");	#connection
	
	#at creation of a new user
	$res = $mng->executeQuery("likes_db.likes_consistency", new MongoDB\Driver\Query([ 'user' => $username ] ));
	$res = current($res->toArray());
	if (empty($res)) {
		$bulk = new MongoDB\Driver\BulkWrite;
		$doc = [_id => new MongoDB\BSON\ObjectID, 
				user => $username,has_liked => array(),has_disliked => array()];
		$bulk->insert($doc);
		$mng->executeBulkWrite('likes_db.likes_consistency', $bulk);
    } 
    
	$likes_count = $_POST['likes_count'];
	$dislikes_count = $_POST['dislikes_count'];
	$feedback_user = $_POST['feedback_user'];
	$current_post = $_POST['post_time'];
	
	#find the people liked/disliked by the user
	$res = $mng->executeQuery("likes_db.likes_consistency", new MongoDB\Driver\Query([ 'user' => $username ]));
	foreach($res as $r)
	{
		$has_liked_arr = $r->has_liked;
		$has_disliked_arr = $r->has_disliked;
	}
	#print_r($has_liked_arr);
	#print_r($has_disliked_arr);
	
	$like_flag = 0;
	$dislike_flag = 0;
	foreach($has_liked_arr as $liked_post)
	{	
		if ($liked_post == $current_post)
		{
			$like_flag = 1;
			break;
		}
	}
	foreach($has_disliked_arr as $disliked_post)
	{	
		if ($disliked_post == $current_post)
		{
			$dislike_flag = 1;
			break;
		}
	}
	if (isset($_POST['like']))
	{
		if ($username != $feedback_user)	#user cannot like/dislike own post
		{
			if ($like_flag == 0)		#0 means not found, 1 means found
			{
				if ($dislike_flag == 1)
					{
						$has_disliked_arr = array_values(array_diff($has_disliked_arr, array($current_post)));
						$dislikes_count -= 1;
					}
				$likes_count = $likes_count+1;
				array_push($has_liked_arr,$current_post);
				$bulk = new MongoDB\Driver\BulkWrite;
				$bulk->update(['user' => $username], ['$set' => ['has_liked' => $has_liked_arr,
																	'has_disliked' => $has_disliked_arr]]);
				$mng->executeBulkWrite('likes_db.likes_consistency', $bulk);
			}
			$bulk = new MongoDB\Driver\BulkWrite;
			$bulk->update(['posted_at' => $current_post], 
							['$set' => ['likes' => $likes_count,'dislikes' => $dislikes_count]]);
			$mng->executeBulkWrite('likes_db.comments', $bulk);
		}
	}
	
	if (isset($_POST['dislike']))
	{
		if ($username != $feedback_user)
		{
			if ($dislike_flag == 0)		#0 means not found, 1 means found
			{
				if ($like_flag == 1)
				{
					$has_liked_arr = array_values(array_diff($has_liked_arr, array($current_post)));
					$likes_count -= 1;
				}
				$dislikes_count = $dislikes_count+1;
				array_push($has_disliked_arr,$current_post);
				$bulk = new MongoDB\Driver\BulkWrite;
				$bulk->update(['user' => $username], ['$set' => ['has_liked' => $has_liked_arr,
																	'has_disliked' => $has_disliked_arr]]);
				$mng->executeBulkWrite('likes_db.likes_consistency', $bulk);
			}
			$bulk = new MongoDB\Driver\BulkWrite;
			$bulk->update(['posted_at' => $current_post], 
							['$set' => ['likes' => $likes_count,'dislikes' => $dislikes_count]]);
			$mng->executeBulkWrite('likes_db.comments', $bulk);
		}
	}
	
	if (isset($_POST['delete']))
	{
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->delete(['posted_at' => $current_post]);
		$mng->executeBulkWrite('likes_db.comments', $bulk);
		$query = new MongoDB\Driver\Query([]);     
		$rows = $mng->executeQuery("likes_db.comments", $query); 
	}
	
	if (isset($_POST['comment']))
	{
		$commented_text = $username." : ".$_POST['commented_text'];
		$res = $mng->executeQuery("likes_db.comments", new MongoDB\Driver\Query([ 'posted_at' => $current_post ]));
		foreach($res as $r)
		{
			$comments = $r->comments;
		}
		array_push($comments,$commented_text);
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->update(['posted_at' => $current_post], ['$set' => ['comments' => $comments]]);
		$mng->executeBulkWrite('likes_db.comments', $bulk);
	}
	
	if (isset($_POST['posted_text']))
	{
		$bulk = new MongoDB\Driver\BulkWrite;
		$posted_at = date('l jS \of F Y h:i:s A');
		$doc = [_id => new MongoDB\BSON\ObjectID, 
				text => $posted_text,user => $username,
				likes => 0,dislikes => 0, posted_at => $posted_at,comments => array()];
		$bulk->insert($doc);
		$mng->executeBulkWrite('likes_db.comments', $bulk);
	  	
	}
	$query = new MongoDB\Driver\Query([]);     
	$rows = $mng->executeQuery("likes_db.comments", $query); 
	echo "<br>";
	foreach ($rows as $row) { 
			echo "<br>
				<b>Post : </b>$row->text <br> <b>Posted by : </b> $row->user <br> 
				<b>Posted on : </b> $row->posted_at  &nbsp;<br> 
				<form action='' method='post'>
					<input type='hidden' value='$row->likes' name='likes_count'> 
					<input type='hidden' value='$row->dislikes' name='dislikes_count'> 
					<input type='hidden' value='$row->posted_at' name='post_time'> 
					<input type='hidden' value='$row->user' name='feedback_user'> 
					<input type='hidden' value='$username' name='username'> 
					<button type='submit' title='Like' class='glyphicon glyphicon-thumbs-up btn btn-link' name='like'>  </button>
					$row->likes&nbsp
					<button type='submit' title='Dislike' class='glyphicon glyphicon-thumbs-down btn btn-link' name='dislike'>  </button>
					$row->dislikes&nbsp
					<input type='text' placeholder='Write a comment..' name='commented_text'>&nbsp
					<button type='submit' title='Post' class='btn btn-link glyphicon glyphicon-comment'  name='comment' alt='post'> </button>";
					if ($row->user == $username)
					{
						echo "<button type='submit' title='Delete' class='glyphicon glyphicon-trash btn btn-link' name='delete'>  </button>";
					}
					if (count($row->comments) != 0)
					{
						echo "<div class='container' style='background-color: lightblue;width: 30% ; padding:20px; margin: 20px;'><b>Comments</b><br>";
						foreach($row->comments as $comm){
							echo "$comm<br>";
						}
						echo "</div>";
					}
					echo "</form>";
					
		}
		echo "</div>";
?>
