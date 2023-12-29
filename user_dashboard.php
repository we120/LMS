<?php
require("procedure_handler.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['execute_borrow_book'])) {
        $conn = establishConnection();
        $bookId = mysqli_real_escape_string($conn, $_POST['borrow_book_id']); 
        $userId = getUserId();

        if ($userId === null) {
            echo json_encode(["error" => "User not logged in."]);
            exit();
        }

        $result = executeBorrowBookProcedure($userId, $bookId);

        echo $result;
        mysqli_close($conn);
        exit();

    } elseif (isset($_POST['execute_return_book'])) {
        $conn = establishConnection();
        $borrowId = mysqli_real_escape_string($conn, $_POST['return_book_id']);
        $userId = getUserId();

        if ($userId === null) {
            echo json_encode(["error" => "User not logged in."]);
            exit();
        }

        $result = executeReturnBookProcedure($borrowId);

        echo $result;
        mysqli_close($conn);
        exit();
    }
}

function getUserId()
{
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}
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

    <div class="row justify-content-center">
        <div class="col-md-3" style="margin: 0px">
            <div class="card bg-light" style="width: 300px">
                <div class="card-header">Borrow Book</div>
                <div class="card-body">
                    <form method="post" id="executeBorrowBookForm">
                        <div class="form-group">
                            <label for="borrow_book_id">Book ID:</label>
                            <input type="text" class="form-control" id="borrow_book_id" name="borrow_book_id" placeholder="Enter Book ID" required>
                        </div>
                        <button type="button" class="btn btn-success" id="executeBorrowBook">Borrow Book</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-3" style="margin: 0px">
            <div class="card bg-light" style="width: 300px">
                <div class="card-header">Return Book</div>
                <div class="card-body">
                    <form method="post" id="executeReturnBookForm">
                        <div class="form-group">
                            <label for="return_book_id">Book ID:</label>
                            <input type="text" class="form-control" id="return_book_id" name="return_book_id" placeholder="Enter Book ID" required>
                        </div>
                        <button type="button" class="btn btn-warning" id="executeReturnBook">Return Book</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#executeBorrowBook").on("click", function () {
                var bookId = $("#borrow_book_id").val();

                var data = {
                    execute_borrow_book: true,
                    borrow_book_id: bookId, 
                };

                $.ajax({
                    type: "POST",
                    url: "user_dashboard.php",
                    data: data,
                    success: function (response) {
                        alert(response);
                    },
                    error: function (error) {
                        console.error("Error executing borrow book procedure:", error);
                    }
                });
            });

       $("#executeReturnBook").on("click", function () {
                var borrowId = $("#return_book_id").val();

                var data = {
                    execute_return_book: true,
                    return_book_id: borrowId,
                };

                $.ajax({
                    type: "POST",
                    url: "user_dashboard.php",
                    data: data,
                    success: function (response) {
                        alert(response);
                    },
                    error: function (error) {
                        console.error("Error executing return book procedure:", error);
                    }
                });
            });
        });
    </script>
</body>
</html>