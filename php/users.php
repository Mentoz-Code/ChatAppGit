<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit();
}

// Include the database configuration
include_once "config.php";

$outgoing_id = $_SESSION['unique_id'];
$output = "";

// Use a prepared statement to fetch all users except the current one
$sql = "SELECT * FROM users WHERE NOT unique_id = ? ORDER BY user_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $outgoing_id);

if ($stmt->execute()) {
    $query = $stmt->get_result(); // Assign the result to `$query`
    
    if ($query->num_rows === 0) {
        $output = "No users available to chat";
    } else {
        // Pass `$query` to `data.php`
        include_once "data.php";
    }
} else {
    // Handle query failure
    $output = "Failed to fetch users. Please try again later.";
}

$stmt->close();
$conn->close();

echo $output;
?>