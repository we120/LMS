<?php
$connection = mysqli_connect("localhost", "root", "", "LMS");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

function select_all_books() {
    global $connection;

    $procedureName = "SelectAllBooks";
    $sql = "CALL $procedureName()";
    $result = mysqli_query($connection, $sql);

    if (!$result) {
        $error = mysqli_error($connection);
        return json_encode(["error" => $error]);
    }

    $books = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return json_encode(["books" => $books]);
}


function executeRegisterProcedure($name, $email, $password, $mobile, $address) {
    $connection = mysqli_connect("localhost", "root", "", "your_database_name");

    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "CALL register_user('$name', '$email', '$password', '$mobile', '$address')";
    $result = mysqli_query($connection, $query);

    mysqli_close($connection);
}

?>