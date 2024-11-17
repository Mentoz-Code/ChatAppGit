<?php
session_start();

if (isset($_SESSION['unique_id'])) {
    include_once "config.php";

    // Retrieve and sanitize inputs
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = isset($_POST['incoming_id']) ? mysqli_real_escape_string($conn, $_POST['incoming_id']) : null;
    $message = isset($_POST['message']) ? mysqli_real_escape_string($conn, $_POST['message']) : null;

    // Ensure message and IDs are not empty
    if (!empty($message) && !empty($incoming_id)) {
        // Use prepared statements for better security
        $stmt = $conn->prepare("INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $incoming_id, $outgoing_id, $message);

        if (!$stmt->execute()) {
            // Log or handle database execution errors
            error_log("Error inserting message: " . $stmt->error);
        }

        $stmt->close();
    } else {
        // Handle cases where input is invalid or missing
        error_log("Invalid input: Missing message or recipient ID.");
    }
} else {
    // Redirect to login if session is not set
    header("location: ../ChatAppGit/login.php");
    exit();
}
?>
