<?php

function establishConnection()
{
    $conn = mysqli_connect("localhost", "root", "", "lms");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}
function executeBorrowBookProcedure($userId, $bookId)
{
    $conn = establishConnection();
    $result = '';

    $query = "CALL BorrowBook(?, ?, @p_result)";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $userId, $bookId);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_error($stmt)) {
            $result = "Error executing BorrowBook procedure: " . mysqli_stmt_error($stmt);
        } else {
            $selectResult = mysqli_query($conn, "SELECT @p_result as result");
            $row = mysqli_fetch_assoc($selectResult);

            if ($row) {
                $result = $row['result'];
            } else {
                $result = "Error fetching result";
            }
        }

        mysqli_stmt_close($stmt);
    } else {
        $result = "Error preparing statement: " . mysqli_error($conn);
    }

    mysqli_close($conn);

    return $result;
}


function executeReturnBookProcedure($borrowId)
{
    $conn = establishConnection();

    $result = '';

    $query = "CALL ReturnBook(?, @p_result)";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $borrowId);
        mysqli_stmt_execute($stmt);
        
        $selectResult = mysqli_query($conn, "SELECT @p_result as result");
        $row = mysqli_fetch_assoc($selectResult);

        if ($row) {
            $result = $row['result'];
        } else {
            $result = "Error fetching result";
        }

        mysqli_stmt_close($stmt);
    } else {
        $result = "Error preparing statement";
    }

    mysqli_close($conn);

    return $result;
}
?>
