<?php
function getDBConnection() {
    $connection = new mysqli("localhost", "root", "", "LMS");

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    return $connection;
}

function adminApproval($borrowId) {
    $connection = getDBConnection();

    if (!$connection) {
        return "Error: Could not connect to the database.";
    }

    try {
        $stmt = $connection->prepare("CALL AdminApproval(?, @result)");
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $connection->error);
        }

        $stmt->bind_param("i", $borrowId);
        $stmt->execute();

        $result = $connection->query("SELECT @result")->fetch_assoc();

        if ($result['@result'] == 'Book approved successfully.') {
            return "Book approved successfully.";
        } else {
            return "Error: Book is not pending approval.";
        }
    } catch (mysqli_sql_exception $e) {
        return "Error: " . $e->getMessage();
    } finally {
        $stmt->close();
        $connection->close();
    }
}