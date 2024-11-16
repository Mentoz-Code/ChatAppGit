<?php
    session_start();
    if(isset($_SESSION['unique_id'])) {
        header("location: users.php");
    }
?>

<?php
    include_once "header.php";
?>

<body>
    <div class="wrapper">
        <section class="form login">
            <header>ChatApp</header>
            <form action="#" method="post" enctype="multipart/form-data" autocomplete="off">
                <div class="error-text"></div>

                <div class="field input">
                    <label>Email</label>
                    <input type="text" name="email" placeholder="Email" required>
                </div>

                <div class="field input">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fas fa-eye" ></i>
                </div>

                <div class="field button">
                    <input type="submit" name="submit" value="Continue to Chat">
                </div>

            </form>
            <div class="link">Not a member yet? <a href="index.php">Register</a></div>
        </section>
    </div>

    <script type="text/javascript" src="js/pass-hide.js" ></script>
    <script type="text/javascript" src="js/login.js" ></script>


</body>
</html>