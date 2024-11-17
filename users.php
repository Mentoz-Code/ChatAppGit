<?php
// Start the session to manage user login state
session_start();

// Include database configuration for connection
include_once "php/config.php";

// Check if the user is logged in by verifying the session
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Include the header file for consistent page layout
include_once "header.php";
?>

<body>
    <div class="wrapper">
        <section class="users">
            <header>
                <div class="content">
                    <?php
                    // Use a prepared statement to fetch user data securely
                    $stmt = $conn->prepare("SELECT * FROM users WHERE unique_id = ?");
                    $stmt->bind_param("i", $_SESSION['unique_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Check if user data is retrieved successfully
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc(); // Fetch the user data
                    } else {
                        // Handle the case where the user data is not found
                        echo "User not found.";
                        exit(); // Stop further execution if user not found
                    }
                    ?>

                    <!-- Display user profile image and details -->
                    <img src="php/images/<?php echo htmlspecialchars($row['img']); ?>" alt="Profile Picture">
                    <div class="details">
                        <span><?php echo htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['lname']); ?></span>
                        <p><?php echo htmlspecialchars($row['status']); ?></p>
                    </div>
                </div>
                
                <!-- Logout link -->
                <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout">Logout</a>
            </header>

            <!-- Search bar to find users -->
            <div class="search">
                <span class="text">Select a user to start chat</span>
                <input type="text" placeholder="Enter name to search...">
                <button><i class="fas fa-search"></i></button>
            </div>

            <!-- Placeholder for the list of users -->
            <div class="users-list">
                <!-- JavaScript will populate this dynamically -->
            </div>
        </section>
    </div>
    <script type="text/javascript" src="js/users.js"></script>
</body>
</html>
