<?php
// Start the session to manage user login state
session_start();

// Include database configuration for connection
include_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION['unique_id'])) {
    echo "User is not logged in.";
    exit();
}

$outgoing_id = $_SESSION['unique_id'];

// Check if the search term is provided
if (!isset($_POST['searchTerm'])) {
    echo "No search term provided.";
    exit();
}

// Escape the search term to prevent injection
$searchTerm = trim($_POST['searchTerm']);

try {
    // Use a prepared statement to securely search for users
    $stmt = $conn->prepare("
        SELECT * FROM users 
        WHERE NOT unique_id = ? 
        AND (fname LIKE CONCAT('%', ?, '%') OR lname LIKE CONCAT('%', ?, '%'))
    ");
    $stmt->bind_param("iss", $outgoing_id, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize output
    $output = "";

    if ($result->num_rows > 0) {
        // Include data.php to display user data dynamically
        include_once "data.php";
    } else {
        // Handle no results case
        $output .= 'No user found.';
    }

    // Return the output
    echo htmlspecialchars($output);

} catch (Exception $e) {
    // Log and handle errors gracefully
    error_log("Database error: " . $e->getMessage());
    echo "An error occurred while searching for users.";
}
?>
