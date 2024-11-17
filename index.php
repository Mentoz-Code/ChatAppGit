<?php
session_start();
// If the user is already logged in, redirect them to the users page
if (isset($_SESSION['unique_id'])) {
    header("location: users.php");
    exit();
}

// Include database connection
include_once "php/config.php";

// Handle form submission
if (isset($_POST['submit'])) {
    // Collect form data and sanitize it
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $image = $_FILES['image']; // Profile picture
    
    // Validate inputs (basic checks)
    if (!empty($fname) && !empty($lname) && !empty($email) && !empty($password) && !empty($image)) {
        
        // Check if the email is already registered
        $check_email_query = "SELECT * FROM users WHERE email = '$email'";
        $check_email_result = mysqli_query($conn, $check_email_query);
        if (mysqli_num_rows($check_email_result) > 0) {
            echo "Email already exists. Please try a different one.";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Handle the image upload
            $image_name = $image['name'];
            $image_tmp_name = $image['tmp_name'];
            $image_size = $image['size'];
            $image_error = $image['error'];
            $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

            // Allowed file types
            $allowed_exts = array('jpg', 'jpeg', 'png', 'gif');
            
            if (in_array($image_ext, $allowed_exts) && $image_error === 0 && $image_size <= 5000000) {
                // Create a unique name for the image
                $image_new_name = uniqid("", true) . "." . $image_ext;
                $image_dest = "php/images/" . $image_new_name;
                move_uploaded_file($image_tmp_name, $image_dest);

                // Insert user data into the database
                $insert_query = "INSERT INTO users (fname, lname, email, password, img) VALUES ('$fname', '$lname', '$email', '$hashed_password', '$image_new_name')";
                $insert_result = mysqli_query($conn, $insert_query);

                if ($insert_result) {
                    // Retrieve the unique_id of the newly inserted user
                    $last_inserted_id = mysqli_insert_id($conn);

                    // Start the session and store the user ID for later use
                    $_SESSION['unique_id'] = $last_inserted_id;

                    // Redirect to users.php (chat page)
                    header("location: users.php");
                    exit();
                } else {
                    echo "Something went wrong. Please try again.";
                }
            } else {
                echo "Invalid image or file too large. Please upload a valid image (max 5MB).";
            }
        }
    } else {
        echo "All fields are required. Please fill out the form completely.";
    }
}
?>

<?php
// Include the header file for consistent page layout
include_once "header.php";
?>

<body>
    <div class="wrapper">
        <section class="form signup">
            <header>ChatApp</header>
            <form action="" method="post" enctype="multipart/form-data" autocomplete="off">
                <div class="error-text"></div>

                <div class="name-details">
                    <div class="field input">
                        <label>First Name</label>
                        <input type="text" name="fname" placeholder="First Name" required>
                    </div>
                    
                    <div class="field input">
                        <label>Last Name</label>
                        <input type="text" name="lname" placeholder="Last Name" required>
                    </div>
                </div>

                <div class="field input">
                    <label>Email</label>
                    <input type="text" name="email" placeholder="Email" required>
                </div>

                <div class="field input">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fas fa-eye"></i>
                </div>

                <div class="field image">
                    <label>Profile Picture</label>
                    <input type="file" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg" required>
                </div>

                <div class="field button">
                    <input type="submit" name="submit" value="Continue to Chat">
                </div>

            </form>
            
            <div class="link">Already a member? <a href="login.php">Login</a></div>
        </section>
    </div>

    <script type="text/javascript" src="js/pass-hide.js"></script>
    <script type="text/javascript" src="js/signup.js"></script>
</body>
</html>
