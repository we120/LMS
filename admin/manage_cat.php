<?php
require("functions.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["execute_update_procedure"])) {
    if (isset($_POST['update_cat_id'], $_POST['update_category_name'])) {
        $catId = $_POST['update_cat_id'];
        $CategoryName = $_POST['update_category_name'];

        $updateResult = update_categories($catId, $CategoryName);

        error_log("Update result: " . var_export($updateResult, true));

        if ($updateResult === true) {
            echo "Category updated successfully!";
			exit;
        } else {
            echo " " . $updateResult;
			exit;
        }
    } else {
        echo "Error: Parameters are missing!";
		exit;
    }
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["execute_delete_category"])) {
    $catId = isset($_POST['delete_cat_id']) ? (int)$_POST['delete_cat_id'] : null;

    if ($catId !== null) {
        $deleteResult = delete_category($catId);

        echo json_encode(["success" => "Category deleted successfully!"]);
        exit;
    } else {
        echo json_encode(["error" => "Invalid or missing category ID!"]);
        exit;
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Category</title>
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
            <font style="color: white">
                <span><strong>Welcome: <?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?></strong></span>
            </font>
            <font style="color: white">
                <span><strong>Email: <?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?></strong></span>
            </font>
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
                <a class="nav-link" href="admin_approval.php">Admin Approval Book</a>
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

    <div class="row justify-content-center">
        <div class="col-md-3" style="margin: 0px">
            <div class="card bg-light" style="width: 300px">
                <div class="card-body">
                    <form method="post" id="executeUpdateCategoryForm">
                        <div class="form-group">
                            <label for="update_cat_id">Category ID:</label>
                            <input type="text" class="form-control" id="update_cat_id" name="update_cat_id" placeholder="Enter Category ID" required>
                        </div>
                        <div class="form-group">
                            <label for="update_category_name">Category Name:</label>
                            <input type="text" class="form-control" id="update_category_name" name="update_category_name" placeholder="Enter Category Name" required>
                        </div>
                        <button type="button" class="btn btn-primary" id="executeUpdateProcedure">Update Category</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3" style="margin: 0px">
            <div class="card bg-light" style="width: 300px">
                <div class="card-body">
<form method="post" id="executeDeleteCategoryForm">
    <div class="form-group">
        <label for="delete_cat_id">Category ID:</label>
        <input type="text" class="form-control" id="delete_cat_id" name="delete_cat_id" placeholder="Enter Category ID" required>
    </div>
    <button type="button" class="btn btn-danger" id="executeDeleteCategory">Delete Category</button>
</form>

                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        $("#executeUpdateProcedure").on("click", function () {
            var catId = $("#update_cat_id").val();
            var categoryName = $("#update_category_name").val();

            var data = {
                execute_update_procedure: true,
                update_cat_id: catId,
                update_category_name: categoryName,
            };

            $.ajax({
                type: "POST",
                url: "manage_cat.php", 
                data: data,
                success: function (response) {
                    alert(response);
                },
                error: function (error) {
                    console.error("Error executing update procedure:", error);
                }
            });
        });
        
    $("#executeDeleteCategory").on("click", function () {
        var catId = $("#delete_cat_id").val();

        var data = {
            execute_delete_category: true,
            delete_cat_id: catId,
        };

        $.ajax({
            type: "POST",
            url: "manage_cat.php",
            data: data,
            dataType: "json", 
            success: function (response) {
                alert(response.success || response.error);
            },
            error: function (error) {
                console.error("Error executing delete category procedure:", error);
            }
        });
    });
});
</script>

</body>

</html>
