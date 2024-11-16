<?php
    session_start(); // Start the session to manage user login state
    include_once "config.php"; // Include database configuration for connection

    // Check if the form is submitted via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize user inputs
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Validate inputs
        if (!empty($email) && !empty($password)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Use a prepared statement to fetch the user data
                $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if a user with the provided email exists
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $hashed_password = $row['password']; // Get the hashed password from the database

                    // Verify the provided password against the hashed password
                    if (password_verify($password, $hashed_password)) {
                        // Update the user's status to "Online"
                        $status = "Online";
                        $update_stmt = $conn->prepare("UPDATE users SET status = ? WHERE unique_id = ?");
                        $update_stmt->bind_param("si", $status, $row['unique_id']);
                        
                        if ($update_stmt->execute()) {
                            $_SESSION['unique_id'] = $row['unique_id']; // Store user ID in the session
                            echo "success"; // Indicate successful login
                        } else {
                            echo "Something went wrong, please try again."; // Error updating status
                        }
                    } else {
                        echo "Incorrect email or password."; // Password mismatch
                    }
                } else {
                    echo "$email - email doesn't exist."; // No user found with the provided email
                }
            } else {
                echo "Invalid email format."; // Email validation failed
            }
        } else {
            echo "Please fill out all the input fields."; // Empty fields
        }
    } else {
        echo "Invalid request method."; // Non-POST requests
    }
?>
