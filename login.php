<html>
<head>

<!--
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
-->

<style>

	form { 
	margin: 0 auto; 
	width:800px;
	}
	/* Full-width input fields */
	input[type=text] {
		width: 40%;
		padding: 12px 20px;
		margin: 8px 0;
		display: inline-block;
		border: 1px solid #ccc;
	}

	/* Set a style for all buttons */
	button {
		background-color: #4CAF50;
		color: white;
		padding: 10px;
		margin: 8px 0;
		border: none;
		cursor: pointer;
		width: 10%;
	}

	button:hover {
		opacity: 0.8;
	}

	/* Center the image and position the close button */
	.imgcontainer {
		text-align: center;
		margin: 24px 0 12px 0;
	}
	.container {
		padding: 40px;
		text-align: center;
	}
	img.login-face {
		width: 40%;
		border-radius: 50%;
	}


</style>   
 
</head>
<body>
	 <form action="posts.php" method="post">
	  <div class="imgcontainer">
		<img src="login.png" alt="login-face" class="login-face">
	  </div>

	  <div class="container">
		<input type="text" placeholder="Enter Username" name="username" required>
		<br>

		<button type="submit">Login</button>
	  </div>

	</form> 
</body>
</html>
