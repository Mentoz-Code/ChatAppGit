<?php
session_start();
include_once "php/config.php";

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit();
}

include_once "header.php";
?>

<body>
    <div class="wrapper">
        <section class="chat-area">
            <header>
                <?php
                // Validate and sanitize the incoming user ID
                if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
                    header("location: users.php");
                    exit();
                }

                $user_id = intval($_GET['user_id']); // Sanitize user ID as an integer

                // Fetch the user details securely using a prepared statement
                $stmt = $conn->prepare("SELECT * FROM users WHERE unique_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                } else {
                    // Redirect to users page if the user is not found
                    header("location: users.php");
                    exit();
                }

                // Escape user data for secure output
                $fname = htmlspecialchars($row['fname']);
                $lname = htmlspecialchars($row['lname']);
                $status = htmlspecialchars($row['status']);
                $img = htmlspecialchars($row['img']);
                ?>

                <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
                <img src="php/images/<?php echo $img; ?>" alt="User Profile Picture">
                <div class="details">
                    <span><?php echo $fname . " " . $lname; ?></span>
                    <p><?php echo $status; ?></p>
                </div>
            </header>

            <div class="chat-box">
                <!-- Messages will be dynamically loaded via JavaScript -->
            </div>

            <form action="#" class="typing-area">
                <input type="hidden" name="incoming_id" class="incoming_id" value="<?php echo $user_id; ?>">
                <input type="text" name="message" class="input-field" placeholder="Type message here..." autocomplete="off">
                <button><i class="fab fa-telegram-plane"></i></button>
            </form>
        </section>
    </div>

<script src="js/chat.js"></script>

</body>
</html>
