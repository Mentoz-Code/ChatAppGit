<?php
// Ensure that $result is set

if (isset($result) && $result->num_rows > 0) {
    // Iterate through each user row from the previous query
    while ($row = mysqli_fetch_assoc($result)) {
        // Use a prepared statement to fetch the last message securely
        $stmt = $conn->prepare("
            SELECT msg, outgoing_msg_id 
            FROM messages 
            WHERE 
                (incoming_msg_id = ? OR outgoing_msg_id = ?) 
                AND (outgoing_msg_id = ? OR incoming_msg_id = ?) 
            ORDER BY msg_id DESC 
            LIMIT 1
        ");
        $stmt->bind_param("iiii", $row['unique_id'], $row['unique_id'], $outgoing_id, $outgoing_id);
        $stmt->execute();
        $result2 = $stmt->get_result();
        $row2 = $result2->fetch_assoc();

        // Determine the message to display
        if ($result2->num_rows > 0) {
            $message = $row2['msg'];
        } else {
            $message = "No new messages";
        }

        // Truncate the message if it exceeds 28 characters
        $msg = strlen($message) > 28 ? substr($message, 0, 28) . '...' : $message;

        // Add "You: " prefix if the message is sent by the current user
        $you = (isset($row2['outgoing_msg_id']) && $outgoing_id === $row2['outgoing_msg_id']) ? "You: " : "";

        // Determine if the user is offline
        $offline = ($row['status'] === "Offline") ? "offline" : "";

        // Hide the current user's entry in the list
        $hide_me = ($outgoing_id === $row['unique_id']) ? "hide" : "";

        // Safely escape user details for output
        $fname = htmlspecialchars($row['fname']);
        $lname = htmlspecialchars($row['lname']);
        $img = htmlspecialchars($row['img']);
        $user_id = htmlspecialchars($row['unique_id']);

        // Append the user card to the output
        $output .= '
            <a href="chat.php?user_id=' . $user_id . '" class="' . $hide_me . '">
                <div class="content">
                    <img src="php/images/' . $img . '" alt="Profile Picture">
                    <div class="details">
                        <span>' . $fname . ' ' . $lname . '</span>
                        <p>' . $you . htmlspecialchars($msg) . '</p>
                    </div>
                </div>
                <div class="status-dot ' . $offline . '"><i class="fas fa-circle"></i></div>
            </a>
        ';
    }
} else {
    echo "No users found.";
}
?>
