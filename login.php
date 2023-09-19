<?php

session_start();
if (isset($_SESSION["user"])) {
    header("Location: appointment.php");
}
$email = (isset($_POST['submit'])) ? trim($_POST['email']) : "";
$password = (isset($_POST['submit'])) ? trim($_POST['password']) : "";
// Message Variables
$message_email = "";
$message_password = "";
$message_pass = "";
$form_good = null;
if (isset($_POST['submit'])) {
    $form_good = TRUE;

    // EMAIL VALIDATION
    if ($email == "") {
        $message_email = "<p>Please enter your email address.</p>";
        $form_good = FALSE;
    } else {

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    }
    if ($email == FALSE) {
        $message_email = "<p>Please enter a valid email address.</p>";
        $form_good = FALSE;
    } else {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    if ($email == FALSE) {
        $message_email = "<p>Please enter a valid email address, including an @ and a top-level domain.</p>";
        $form_good = FALSE;
    } else {

        $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";

        if (preg_match($pattern, $email) == false) {
            $message_email = "<p>Please enter a valid email address, including an @ and a top-level domain.</p>";
            $form_good = FALSE;
        }
    }
    //Password Validation 
    if (strlen($password) < 8) {
        $message_password = "<p>Your password cannot be less than 8 characters long </p>";
        $form_good = FALSE;
    }


}

if ($form_good == TRUE) {
    include_once("database-connection.php");
    $sql_verify = "SELECT * FROM authentication WHERE Email_ID = '$email'";
    $result = mysqli_query($connect, $sql_verify);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if ($user) {
        if (password_verify($password, $user["Password"])) {
            //$message_pass = "<p>You have sucessfully logged in </p>";
            // session_start();
            if ($email == "darshiljogani@gmail.com" || $email == "write2abhij@gmail.com") {
                $_SESSION["user"] = "yes";
                //pass the email to the session variable too 
                $_SESSION["admin-username"] = $user["Email_ID"];
                $_SESSION["username"] = $user["Email_ID"];
                header("Location: admin/admin.php");
            } else {
                $_SESSION["user"] = "yes";
                //pass the email to the session variable too 
                $_SESSION["username"] = $user["Email_ID"];
                header("Location: appointment.php");
            }

        } else {
            $message_password = "<p>Your password does not match </p>";
            $form_good = FALSE;
        }
    } else {
        $message_email = "<p>Email Does not exists</p>";
        $form_good = FALSE;
    }

}

?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Strawberry Favicon -->
    <link href="/img/favicon.ico" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="css/styles.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Bootstrap icon library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <title>Strawberry Fields: Login</title>
</head>

<body class="bg-secondary-subtle">

    <?php
    include("header.php");
    ?>

    <main class="d-flex flex-wrap justify-content-center align-items-center min-vh-100 p-3">
        <section class="row">
            <div class="col bg-white rounded p-5 border border-secondary-subtle">
                <h1 class="fw-light">Login</h1>
                <p class="text-muted">Please Login/Register to Book Appointment </p>

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="my-3" method="POST">

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" name="email" type="text" class="form-control" aria-describedby="email-help"
                            value="<?php echo $email; ?>">
                        <div class="form-text" id="email-help">
                            <?php echo $message_email; ?>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" name="password" type="password" class="form-control"
                            aria-describedby="password-help">
                        <div class="form-text" id="password-help">
                            <?php echo $message_password; ?>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <input type="submit" name="submit" class="btn btn-primary mb-2" value="Login">
                        </div>
                        <div class="col-md-6 text-center">
                            <a href="register.php" class="btn btn-primary">Register</a>
                        </div>

                    </div>
                    <!-- Forget Password Button -->
                    <div class="text-center">
                        <a href="forget-password.php" class="btn btn-link">Forgot Password?</a>
                    </div>

                </form>

                <?php if ($form_good == true): ?>
                    <div class="alert alert-primary my-4" role="alert">
                        <?php echo $message_pass; ?>
                    </div>
                <?php endif ?>
            </div>
        </section>

    </main>
    <?php
    include("footer.php")
        ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

</body>

</html>