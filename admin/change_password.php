<?php
	require("functions.php");
	session_start();

	
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $old_password = $_POST["old_password"];
    $new_password = $_POST["new_password"];
    $user_email = $_SESSION["email"];

    $connection = mysqli_connect("localhost", "root", "", "LMS");

    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "CALL change_admin_password('$user_email', '$old_password', '$new_password', @message)";
    if (mysqli_query($connection, $query)) {
        $result = mysqli_query($connection, "SELECT @message AS message");
        $row = mysqli_fetch_assoc($result);
        $message = $row['message'];

        if (strpos($message, 'error') !== false) {
            echo "Database Error: $message";
        } else {
            echo "Password Update Message: $message";
        }
    } else {
        echo "Error updating password: " . mysqli_error($connection);
    }

    mysqli_query($connection, "COMMIT");

    mysqli_close($connection);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Dashboard</title>
	<meta charset="utf-8" name="viewport" content="width=device-width,intial-scale=1">
	<link rel="stylesheet" type="text/css" href="../bootstrap-4.4.1/css/bootstrap.min.css">
  	<script type="text/javascript" src="../bootstrap-4.4.1/js/juqery_latest.js"></script>
  	<script type="text/javascript" src="../bootstrap-4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="admin_dashboard.php">Library Management System (LMS)</a>
			</div>
			<font style="color: white"><span><strong>Welcome: <?php echo $_SESSION['name'];?></strong></span></font>
			<font style="color: white"><span><strong>Email: <?php echo $_SESSION['email'];?></strong></font>
		    <ul class="nav navbar-nav navbar-right">
		      <li class="nav-item dropdown">
	        	<a class="nav-link dropdown-toggle" data-toggle="dropdown">My Profile </a>
	        	<div class="dropdown-menu">
	        		<a class="dropdown-item" href="view_profile.php">View Profile</a>
	        		<div class="dropdown-divider"></div>
	        		<a class="dropdown-item" href="edit_profile.php">Edit Profile</a>
	        		<div class="dropdown-divider"></div>
	        		<a class="dropdown-item" href="change_password.php">Change Password</a>
	        	</div>
		      </li>
		      <li class="nav-item">
		        <a class="nav-link" href="../logout.php">Logout</a>
		      </li>
		    </ul>
		</div>
	</nav><br>
	<span><marquee>This is library mangement system. Library opens at 8:00 AM and close at 8:00 PM</marquee></span><br><br>
	<center><h4>Change Admin Password</h4><br></center>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <?php if(isset($message)): ?>
                <div class="alert <?php echo strpos($message, 'success') !== false ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form action="change_password.php" method="post">
                <div class="form-group">
                    <label for="password">Enter Current Password:</label>
                    <input type="password" class="form-control" name="old_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Enter New Password:</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <button type="submit" name="update" class="btn btn-primary">Update Password</button>
            </form>
        </div>
        <div class="col-md-4"></div>
    </div>
</body>
</html>
