<?php
session_start();

if (isset($_SESSION['unique_id'])) {
    include_once "config.php";

    // Sanitize input values
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = isset($_POST['incoming_id']) ? mysqli_real_escape_string($conn, $_POST['incoming_id']) : null;

    // Check if incoming_id is valid
    if (!$incoming_id) {
        echo json_encode(['error' => 'Invalid or missing incoming ID']);
        exit;
    }

    $output = "";

    // Prepare the SQL query to fetch messages between outgoing and incoming users
    $sql = "
        SELECT messages.msg, users.img, messages.outgoing_msg_id
        FROM messages
        LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
        WHERE 
            (outgoing_msg_id = ? AND incoming_msg_id = ?) 
            OR (outgoing_msg_id = ? AND incoming_msg_id = ?)
        ORDER BY msg_id ASC
    ";

    // Prepare the statement to prevent SQL injection
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters securely
        $stmt->bind_param("iiii", $outgoing_id, $incoming_id, $incoming_id, $outgoing_id);

        // Execute the prepared statement
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if any messages were returned
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $msg = htmlspecialchars($row['msg']); // XSS Protection
                $img = htmlspecialchars($row['img']); // XSS Protection

                // Format outgoing vs incoming messages
                if ($row['outgoing_msg_id'] === $outgoing_id) {
                    // Message from the current user (outgoing)
                    $output .= '<div class="chat outgoing">
                                    <div class="details">
                                        <p>' . $msg . '</p>
                                    </div>
                                </div>';
                } else {
                    // Message from the other user (incoming)
                    $output .= '<div class="chat incoming">
                                    <img src="php/images/' . $img . '" alt="Profile Picture">
                                    <div class="details">
                                        <p>' . $msg . '</p>
                                    </div>
                                </div>';
                }
            }
        } else {
            $output .= '<div class="text">No messages available</div>';
        }

        // Close the statement
        $stmt->close();
    } else {
        // Log SQL preparation error and send a response
        error_log("Failed to prepare SQL query: " . $conn->error);
        echo json_encode(['error' => 'Error fetching messages']);
        exit;
    }

    // Return the output for chat messages
    echo $output;

} else {
    // If session is not set, redirect to login page
    header("location: ../login.php");
    exit();
}
?>
