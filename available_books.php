<?php
require("functions_user.php");
session_start();

$connection = mysqli_connect("localhost", "root", "", "lms");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}


$query_books = "SELECT * FROM view_books_author_category";
$query_run_books = mysqli_query($connection, $query_books);

?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="user_dashboard.php">Library Management System (LMS)</a>
            </div>
            <font style="color: white"><span><strong>Welcome: <?php echo $_SESSION['student_name'];?></strong></span></font>
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
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav><br>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd">
		<div class="container-fluid">
		    <ul class="nav navbar-nav navbar-center">
            <li class="nav-item">
		        <a class="nav-link" href="user_dashboard.php">User Dashboard  </a>
                </li>
		      <li class="nav-item">
		        <a class="nav-link" href="available_books.php">Available Books  </a>
                </li>
		    </ul>
		</div>
	</nav><br>
    <span><marquee>This is library management system. Library opens at 8:00 AM and closes at 8:00 PM</marquee></span><br><br>
    <center><h4>Available Books</h4><br></center>
	<div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-12">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Book ID</th>
                        <th>Book Name</th>
                        <th>Book Quantity</th>
                        <th>ISBN</th>
                        <th>Category Name</th>
                        <th>Author Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($query_run_books)) {
                        echo '<tr>';
                        echo '<td>' . $row["book_id"] . '</td>';
                        echo '<td>' . $row["book_name"] . '</td>';
                        echo '<td>' . $row["book_quantity"] . '</td>';
                        echo '<td>' . $row["ISBN"] . '</td>';
                        echo '<td>' . $row["category_name"] . '</td>';
                        echo '<td>' . $row["author_name"] . '</td>';
                        echo '</tr>';
                    }
                    ?>

</body>
</html>