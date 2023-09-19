<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION["user"])) {
    if (isset($_SESSION["username"])) {
        $message_waitlist = "";
        $username = "";
        $username = $_SESSION["username"];
        $_SESSION["username"] = $username;
    }

} else {
    header("Location: login.php");
}

$form_good = null;

if (isset($_POST["submit"])) {
    //wrong name attribute  
    $form_good = true;
    //you have set the form_good variable to null, the database tracsaction will not work if the form_good is ever null or false 
    //$form_good = null;
    // Get the current date and time
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');

    // Validate date and time
    $date = $_POST['date'] ?? "";
    $time = $_POST['time'] ?? "";
    $time_until = empty($_POST['time-until']) ? date("H:i:s", strtotime('23:59:59')) : $_POST['time-until'];
    if (empty($date) || empty($time)) {
        $message_waitlist = "Please enter a valid date and time";
        $form_good = false;
    } else {
        date_default_timezone_set("America/Denver");
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time . ':00');
        $formatted_date = $datetime->format('Y-m-d');
        $formatted_time = $datetime->format('H:i:s');

        if ($datetime < new DateTime()) {
            $message_form = "The selected date and time have already passed, please select a future date and time";
            $form_good = false;
        }
    }
    //did not set the $basket_quantity variable
    $basket_quantity = $_POST['basket_quantity'] ?? "";
    if (empty($basket_quantity)) {
        $message_appointment .= "No Basket Quantity was passed";
        $form_good = false;
    }
    if ($basket_quantity > 4) {
        $message_appointment .= "Basket Quantity can not be more than 4";
        $form_good = false;
    }
}

if ($form_good) {
    include_once("database-connection.php");

    $confirmYN = 'Y';
    $sql_waitlist = "INSERT INTO waitlist (Email_ID, Date_Available, Time_Available, Quantity, ConfirmYN, Time_Available_Until)VALUES (?,?,?,?,?,?)";

    mysqli_autocommit($connect, FALSE);
    $error = FALSE;
    $initialize_statement_waitlist = mysqli_stmt_init($connect);
    $prepare_statement_waitlist = mysqli_stmt_prepare($initialize_statement_waitlist, $sql_waitlist);

    if ($prepare_statement_waitlist) {
        mysqli_stmt_bind_param($initialize_statement_waitlist, "ssssss", $username, $date, $time, $basket_quantity, $confirmYN, $time_until);
        //wrong parameter value   mysqli_stmt_execute($prepare_statement_waitlist); 
        mysqli_stmt_execute($initialize_statement_waitlist);

        if (mysqli_errno($connect)) {
            $error = True;
        }
    } else {
        $message_form = "<p>Failed to insert record into the database</p>";
    }
    if ($error) {
        mysqli_rollback($connect);
        $message_form = "<p>Failed to insert record into the database</p>";
    }
    //Note: If you set the autocommit to false, your transaction will not be comitted by default, you need to use mysqli_commit function 
    else {
        mysqli_commit($connect);
        //mailer 
        $body = "<h2>Thank You for being on Waitlist!</h2>";
        $body .= "<p>Email Address: $username</p>";
        $body .= "<p>Date Requested: $date</p>";
        $body .= "<p>Time Requested: $time to $time_until </p>";
        $body .= "<p>No of Basket : $basket_quantity</p>";

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
            $mail->setFrom('write2abhij@gmail.com', 'Confirmation of Waitlist');
            $mail->addAddress($username, ); //Add a recipient
            $mail->addReplyTo($username, );
            $mail->addBCC('write2abhij@gmail.com');

            //Content
            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject = 'You are on the waitlist with  Strawberry Fields ';
            $mail->Body = $body;

            $mail->send();
            $_SESSION["user"] = "yes";
            $_SESSION["message"] = "Thank You for joing our waitlist. A confirmation email has been sent to you!";
            $_SESSION["username"] = $username;
            header("Location: index.php");
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
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

    <title>Strawberry Fields: Waitlist</title>
    <style>
        .input-sm {
            max-width: 300px;
        }

        .small-btn {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>

<body class="bg-light vh-100">
    <?php include("header.php"); ?>
    <main class="container py-5">
        <h1 class="display-4 mb-4 text-center">Join the Waitlist</h1>
        <?php
        if (!empty($message_waitlist)) { ?>
            <div class="alert alert-danger">
                <?php echo $message_waitlist; ?>
            </div>
        <?php } ?>
        <?php
        if (!empty($message_form)) { ?>
            <div class="alert alert-danger">
                <?php echo $message_form; ?>
            </div>
        <?php } ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST"
            class="mx-auto w-75 d-flex flex-column justify-content-center">
            <div class="form-group m-2">
                <label for="date" class="mb-1">Preferred Date:</label>
                <input type="date" name="date" class="form-control input-sm" id="date" aria-describedby="datehelp"
                    placeholder="select a date" value="<?php echo $date; ?>">
            </div>
            <div class="form-group m-2">
                <label for="time" class="mb-1">Preferred Start Time :</label>
                <input type="time" name="time" class="form-control input-sm" id="time" placeholder="select a time"
                    value="<?php echo $time; ?>">
            </div>
            <div class="form-group m-2">
                <label for="time-until" class="mb-1">Preferred End Time:</label>
                <input type="time" name="time-until" class="form-control input-sm" id="time-until"
                    placeholder="select a time" value="<?php echo $time_until; ?>" aria-describedby="time-help">
                <div id="time-help" class="form-text">
                    Leave Blank, if available for whole day
                </div>
            </div>
            <div class="form-group m-2">
                <label for="basket_quantity" class="mb-1">Basket(s):</label>
                <input type='number' name='basket_quantity' value='1' min='1' max='4'
                    class='form-control text-center input-sm' id="basket_quantity">
            </div>

            <button type="submit" name="submit" class="btn btn-primary m-2" style="width: 200px;">Add to
                Waitlist</button>

        </form>

    </main>
    <?php include("footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

</body>

</html>