<?php
require("functionapproval.php");
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'admin_approval') {
        $borrowId = $_POST['borrow_id'];
        $result = adminApproval($borrowId);

        if ($result === "Book approved successfully.") {
            echo json_encode(["status" => "success", "message" => "Book approved successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => $result]);
        }

        exit();
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Admin Approval</title>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../bootstrap-4.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
                </li>
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
                    <a class="nav-link" href="issue_book.php">Admin Approval Book</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="audit_books.php">Audit Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_page.php">View Books</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="ViewUserBookIssued.php">View User and Book Issued</a>
                </li>
            </ul>
        </div>
    </nav><br>
    <span><marquee>This is library management system. Library opens at 8:00 AM and closes at 8:00 PM</marquee></span><br><br>
    <center><h4>Admin Approval of Book</h4><br></center>
    <div class="row justify-content-center">
        <div class="col-md-3" style="margin: 0px">
            <div class="card bg-light" style="width: 300px">
                <div class="card-header">Admin Approval</div>
                <div class="card-body">
                    <form method="post" id="executeApprovalProcedureForm">
                        <div class="form-group">
                            <label for="approval_borrow_id">Borrower ID:</label>
                            <input type="text" class="form-control" id="approval_borrow_id" name="borrow_id" placeholder="Enter Borrower ID" required>
                        </div>
                        <button type="button" class="btn btn-success" id="executeApprovalProcedure">Approve Borrower</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
       $(document).ready(function() {
    $("#executeApprovalProcedure").on("click", function() {
        var borrowerId = $("#approval_borrow_id").val();

        $.ajax({
            type: "POST",
            url: "admin_approval.php",  
            data: {
                action: "admin_approval",
                borrow_id: borrowerId
            },
            dataType: 'json', 
            success: function(response) {
                console.log(response);

                if (response.status === "success") {
                    alert(response.message);
                } else {
                    console.error(response.message);
                }
            },
            error: function(error) {
                console.error(error);
            }
        });
    });
});

    </script>
</body>
</html>