<?php
require("functions.php");
session_start();

$connection = mysqli_connect("localhost", "root", "", "lms");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$name = "";
$email = "";
$mobile = "";

$query = "SELECT * FROM admins WHERE email = '$_SESSION[email]'";
$query_run = mysqli_query($connection, $query);

while ($row = mysqli_fetch_assoc($query_run)) {
    $name = $row['name'];
    $email = $row['email'];
    $mobile = $row['mobile'];
}

$query_books = "SELECT * FROM view_books_author_category";
$query_run_books = mysqli_query($connection, $query_books);
?>

<!DOCTYPE html>
<html>
<head>
	<title>View Books</title>
	<meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../bootstrap-4.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="../bootstrap-4.4.1/js/bootstrap.min.js"></script>
  	<script type="text/javascript">
  		function alertMsg(){
  			alert("Book added successfully...");
  			window.location.href = "admin_dashboard.php";
  		}
  	</script>
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
	        		<a class="dropdown-item" href="">View Profile</a>
	        		<div class="dropdown-divider"></div>
	        		<a class="dropdown-item" href="#">Edit Profile</a>
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
	<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd">
		<div class="container-fluid">
		    <ul class="nav navbar-nav navbar-center">
		      <li class="nav-item">
		        <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
		      <li class="nav-item dropdown">
	        	<a class="nav-link dropdown-toggle" data-toggle="dropdown">Category </a>
	        	<div class="dropdown-menu">
	        		<a class="dropdown-item" href="add_cat.php">Add New Category</a>
	        		<div class="dropdown-divider"></div>
	        		<a class="dropdown-item" href="manage_cat.php">Manage Category</a>
	        	</div>
		      </li>
		      <li class="nav-item dropdown">
	        	<a class="nav-link dropdown-toggle" data-toggle="dropdown">Authors</a>
	        	<div class="dropdown-menu">
	        		<a class="dropdown-item" href="add_author.php">Add New Author</a>
	        		<div class="dropdown-divider"></div>
	        		<a class="dropdown-item" href="manage_author.php">Manage Author</a>
	        	</div>
		      </li>
	          <li class="nav-item">
		         <a class="nav-link" href="admin_approval.php">Admin Approval Book</a>
		      </li>
              <li class="nav-item">
		        <a class="nav-link" href="audit_books.php">Audit Books</a>
		      </li>
              <li class="nav-item">
		        <a class="nav-link" href="view_page.php">View Books</a>
		      </li>
			  </li>
                <li class="nav-item">
                <a class="nav-link" href="ViewUserBookIssued.php">View User and Book Issued</a>
		      </li>
		    </ul>
		</div>
	</nav><br>
	<span><marquee>This is library management system. Library opens at 8:00 AM and closes at 8:00 PM</marquee></span><br><br>
    <center><h4>View Books, Categories and Authors</h4><br></center>
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
                </tbody>
            </table>
        </div>
        <div class="col-md-2"></div>
    </div>

	<div class="row">
        <div class="col-md-4">
            <h4>All Books</h4>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Book ID</th>
                        <th>Book Name</th>
                        <th>ISBN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_books = "SELECT * FROM book_view";
                    $query_run_books = mysqli_query($connection, $query_books);
                    while ($row = mysqli_fetch_assoc($query_run_books)) {
                        echo '<tr>';
                        echo '<td>' . $row["book_id"] . '</td>';
                        echo '<td>' . $row["book_name"] . '</td>';
                        echo '<td>' . $row["ISBN"] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-4">
            <h4>All Categories </h4>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Category ID</th>
                        <th>Category Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_categories = "SELECT * FROM category_view";
                    $query_run_categories = mysqli_query($connection, $query_categories);
                    while ($row = mysqli_fetch_assoc($query_run_categories)) {
                        echo '<tr>';
                        echo '<td>' . $row["cat_id"] . '</td>';
                        echo '<td>' . $row["category_name"] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-4">
            <h4>All Authors</h4>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Author ID</th>
                        <th>Author Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_authors = "SELECT * FROM author_view";
                    $query_run_authors = mysqli_query($connection, $query_authors);
                    while ($row = mysqli_fetch_assoc($query_run_authors)) {
                        echo '<tr>';
                        echo '<td>' . $row["author_id"] . '</td>';
                        echo '<td>' . $row["author_name"] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>


</body>
</html>

<?php
mysqli_close($connection);
?>