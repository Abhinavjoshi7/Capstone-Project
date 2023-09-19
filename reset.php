<?php 
session_start();
$email = (isset($_POST['submit'])) ? trim($_POST['email']) : "";
$otp = (isset($_POST['submit'])) ? trim($_POST['otp']) : "";
$password = (isset($_POST['submit'])) ? trim($_POST['password']) : "";
$confirm_password = (isset($_POST['submit'])) ? trim($_POST['confirm-password']) : "";
$email = (isset($_POST['submit'])) ? trim($_POST['email']) : "";
$message_email = "";
$message_password = "";
$message_confirm_password = "";
$message_otp = "";
$form_good = null;
if (isset($_POST['submit'])) {
    $form_good = true;
    include_once("database-connection.php");
    $sql_verify = "SELECT * FROM authentication WHERE reset_token = '$otp' and Email_ID = '$email' ";
    $result = mysqli_query($connect, $sql_verify);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if (empty($email)) {
        $message_email = "<p>Please enter your email address.</p>";
        $form_good = FALSE;
    }
    if(strlen($password)<8){
        $message_password = "<p>Your password cannot be less than 8 characters long </p>";
        $form_good = FALSE;
    }
    if($password !== $confirm_password){
        $message_confirm_password = "<p>Your passwords do not match </p>";
        $form_good = FALSE; 
    }
    if(!$user){
        $message_otp = "<p>You have entered wrong pin, please check your email </p>";
        $form_good = FALSE;
    }

    if($form_good == true){
        reset_password($connect, $password, $otp, $email);
    }
}

function reset_password($connect, $password, $otp, $email){
    $hashed_password =   password_hash($password, PASSWORD_DEFAULT);
    //update the databae table using email and otp
    //$sql_update = "UPDATE authentication SET Password = '$hashed_password' WHERE reset_token = '$otp' and Email_ID = '$email'";
    //use the binding parameters method insead of above statment (direct update) for security pupose 
    $sql_update = "UPDATE authentication SET Password = ? WHERE reset_token = ? and Email_ID = ?";
    $stmt = mysqli_stmt_init($connect);
    if (mysqli_stmt_prepare($stmt, $sql_update)) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 'sss', $hashed_password, $otp, $email);
        mysqli_stmt_execute($stmt);
        $_SESSION["user"] = "yes";
        $_SESSION["username"] = $email;
        header("Location: appointment.php");
    }
}



?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/styles.css">
    <!-- Strawberry Favicon -->
    <link href="/img/favicon.ico" rel="icon" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Bootstrap icon library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <title>Strawberry Fields: Reset Password</title>
  </head>

<?php
include("header.php");
?>

<body class="bg-secondary-subtle">
    <main class="d-flex justify-content-center align-items-center min-vh-100 p-3">
        <section class="row">
            <div class="col bg-white rounded p-5 border border-secondary-subtle">
                <h1 class="fw-light">Change Your Password</h1>
                <p class="text-muted">Please enter your OTP with new password </p>

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

                    <!-- OPT -->
                    <div class="mb-3">
                        <label for="OTP" class="form-label">OPT</label>
                        <input id="OTP" name="otp" type="number" class="form-control" aria-describedby="OTP-HELP"
                            value="<?php echo $otp; ?>">
                        <div class="form-text" id="OTP-HELP">
                            <?php echo $message_otp; ?>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" name="password" type="password" class="form-control"
                            aria-describedby="password-help">
                        <div class="form-text" id="password-help">
                            <?php echo $message_password; ?>
                        </div>
                    </div>
                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="confirm-password" class="form-label">Confirm Password</label>
                        <input id="confirm-password" name="confirm-password" type="password" class="form-control"
                            aria-describedby="confirm-password-help">
                        <div class="form-text" id="confirm-password-help">
                            <?php echo $message_confirm_password; ?>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="row justify-content-center">
                        <div class="col-md-6 text-center">
                            <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                        </div>

                    </div>

                </form>

            
            </div>
        </section>

    </main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>