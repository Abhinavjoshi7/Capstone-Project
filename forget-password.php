<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

$email = (isset($_POST['submit'])) ? trim($_POST['email']) : "";

$message_email = "";
$message_pass = "";
$form_good = null;

if (isset($_POST['submit'])) {
    $form_good = true;
    if (empty($email)) {
        $message_email = "<p>Please enter your email address.</p>";
        $form_good = FALSE;
    } else {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message_email = "<p>Please enter a valid email address, including an @ and a top-level domain.</p>";
            $form_good = FALSE;
        } else {
            $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";
            if (preg_match($pattern, $email) == false) {
                $message_email = "<p>Please enter a valid email address, including an @ and a top-level domain.</p>";
                $form_good = FALSE;
            }
        }
    }
}
if ($form_good == true) {
    include_once("database-connection.php");
    $sql_verify = "SELECT * FROM authentication WHERE Email_ID = '$email'";
    $result = mysqli_query($connect, $sql_verify);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if ($user) {
        $otp = rand(100000, 999999); // generate a random 6-digit OTP
        $expiry_time = time() + 600; // set OTP expiry time to 10 minutes from now
        $sql_update = "UPDATE authentication SET reset_token = '$otp' WHERE Email_ID = '$email'";
        mysqli_query($connect, $sql_update);
        $body = "<h2>Hello!</h2>";
        $body .= "<p>Email: $email</p>";
        $body .= "<p>OTP: $otp</p>";

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
            $mail->isSMTP(); //Send using SMTP
            $mail->Host = 'smtp.gmail.com'; //Set the SMTP server to send through
            $mail->SMTPAuth = true; //Enable SMTP authentication
            $mail->Username = 'write2abhij@gmail.com'; //SMTP username
            $mail->Password = 'mdrvzybnefufrdxo'; //SMTP password
            $mail->SMTPSecure = 'tls'; //Enable implicit TLS encryption
            $mail->Port = 587; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('write2abhij@gmail.com', 'Forget Pasword OTP');
            $mail->addAddress($email, ); //Add a recipient
            $mail->addReplyTo($email, );
            $mail->addBCC('write2abhij@gmail.com');

            //Content
            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject = 'Password Reset Link ';
            $mail->Body = $body;

            $mail->send();
            $message_pass = "<p>A password reset token has been sent to your email address.</p>";
            header("Location:reset.php");

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }


    } else {
        $message_email = "<p>The email address you entered does not exist in our database.</p>";
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
    <link rel="stylesheet" href="css/styles.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Bootstrap icon library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <title>Strawberry Fields: Forgot Password</title>
</head>


<body class="bg-secondary-subtle">

    <?php
    include("header.php");
    ?>

    <main class="d-flex justify-content-center align-items-center min-vh-100 p-3">
        <section class="row">
            <div class="col bg-white rounded p-5 border border-secondary-subtle">
                <h1 class="fw-light">Forgot Passowrd</h1>
                <p class="text-muted">Please Enter Your Email Address </p>

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
                    <!-- Submit Button -->
                    <div class="row justify-content-center">
                        <div class="col-md-6 text-center">
                            <input type="submit" name="submit" class="btn btn-primary" value="Get OTP">
                        </div>


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

</body>

</html>