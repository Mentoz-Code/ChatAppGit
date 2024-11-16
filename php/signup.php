<?php
session_start();
include_once "config.php";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($fname) && !empty($lname) && !empty($email) && !empty($password)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Prepare statement to check if email already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo "$email - email already exists";
                } else {
                    // Check if an image is uploaded
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $img_name = $_FILES['image']['name'];
                        $img_type = $_FILES['image']['type'];
                        $tmp_name = $_FILES['image']['tmp_name'];
                        $img_size = $_FILES['image']['size'];

                        // Get the file extension
                        $img_explode = explode('.', $img_name);
                        $img_ext = strtolower(end($img_explode)); // Use strtolower for consistency

                        // Allowed extensions types
                        $allowed_extensions = ["jpeg", "png", "jpg"];
                        $allowed_types = ["image/jpeg", "image/jpg", "image/png"];

                        // Validate the image extension and type
                        if (in_array($img_ext, $allowed_extensions) && in_array($img_type, $allowed_types)) {
                            if ($img_size <= 4 * 1024 * 1024) { // Check file size
                                $time = time();
                                $new_img_name = $time . "_" . basename($img_name); // Avoid overwriting files

                                // Move the uploaded file
                                if (move_uploaded_file($tmp_name, "images/" . $new_img_name)) {
                                    $ran_id = rand(time(), 100000000);
                                    $status = "Online";
                                    $encrypt_pass = password_hash($password, PASSWORD_DEFAULT);

                                    // Prepare statement to insert user data
                                    $insert_stmt = $conn->prepare("INSERT INTO users (unique_id, fname, lname, email, password, img, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                                    if ($insert_stmt) {
                                        $insert_stmt->bind_param("issssss", $ran_id, $fname, $lname, $email, $encrypt_pass, $new_img_name, $status);

                                        if ($insert_stmt->execute()) {
                                            // Fetch the newly created user
                                            $select_stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                                            if ($select_stmt) {
                                                $select_stmt->bind_param("s", $email);
                                                $select_stmt->execute();
                                                $select_result = $select_stmt->get_result();

                                                if ($select_result->num_rows > 0) {
                                                    $result = $select_result->fetch_assoc();
                                                    $_SESSION['unique_id'] = $result['unique_id'];
                                                    echo "Success";
                                                } else {
                                                    echo "Error fetching user after signup.";
                                                }
                                                $select_stmt->close();
                                            } else {
                                                echo "Error preparing select statement.";
                                            }
                                        } else {
                                            echo "Error: " . $insert_stmt->error;
                                        }
                                        $insert_stmt->close();
                                    } else {
                                        echo "Error preparing insert statement.";
                                    }
                                } else {
                                    echo "Error moving uploaded image.";
                                }
                            } else {
                                echo "File size must be less than 4MB.";
                            }
                        } else {
                            echo "Upload a valid image file (jpeg, png, jpg).";
                        }
                    } else {
                        echo "Image upload error. Please try again.";
                    }
                }
                $stmt->close();
            } else {
                echo "Error preparing statement for email check.";
            }
        } else {
            echo "$email is not valid, enter a valid email.";
        }
    } else {
        echo "Fill out all the input fields.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
