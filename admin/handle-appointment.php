<?php
session_start();
use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION["user"])) {
    if (isset($_SESSION["username"])) {
        $message_appointment = "";
        $username = "";
        $username = $_SESSION["username"];
        $_SESSION["username"] = $username;
    }
} else {
    header("location: ../login.php");
}
?>


<?php
// Initialize variables
$_SESSION["message"] = "";

// Get the current date and time
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

//message variables for the final form -- that sends the data to db 
$message_add_id = "";
$message_comment = "";
$message_email = "";
$message_add_date = "";
$message_add_time = "";
$message_basket = "";
$form_add = null;

$email = (isset($_POST['add'])) ? trim($_POST['add-email']) : "";

if (isset($_POST['add'])) {
    $date = $_POST['add-date'] ?? "";
    $time = $_POST['add-time'] ?? "";
    $availability_id = isset($_POST['availability_id']) ? $_POST['availability_id'] : '';
    $basket_quantity = (int) $_POST['basket_quantity'];
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : "";
    include_once("../database-connection.php");
    $form_add = true;

    if (empty($availability_id)) {
        $message_add_id = "No Id was passed from the database";
        $form_add = false;
    }
    if (empty($date)) {
        $message_add_date = "No date was passed from the database";
        $form_add = false;
    }
    if (!empty($comment)) {
        $comment = filter_var($comment, FILTER_SANITIZE_STRING);
    }
    if (empty($time)) {
        $message_add_time = "No time was passed from the database";
        $form_add = false;
    }
    if (empty($email)) {
        $message_email = "Customer Email cannot be emty";
        $form_add = false;
        ;
    } else {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message_email = "<p>Please enter a valid email address, including an @ and a top-level domain.</p>";
            $form_add = FALSE;
        } else {
            $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";
            if (preg_match($pattern, $email) == false) {
                $message_email = "<p>Please enter a valid email address, including an @ and a top-level domain.</p>";
                $form_add = FALSE;
            }
        }
        $sql_email_check = "Select * from customer where Email_ID = '$email'";
        $result_verify = mysqli_query($connect, $sql_email_check);
        $row_verify = mysqli_num_rows($result_verify);
        if ($row_verify == 0) {
            $message_email .= "The email does not exist in our database, Please create an account first";
            $form_add = false;
        }
    }

}
if ($form_add) {
    include_once("../database-connection.php");
    $sql_check = "Select Availability_ID From appointment where Availability_ID = '$availability_id'";
    $result_check = mysqli_query($connect, $sql_check);
    $row_count = mysqli_num_rows($result_check);
    if ($result_check && $row_count > 0) {
        $message_appointment .= "The availability ID already exists, pick a different one";
    } else {
        $confirmYN = 'Y';
        $bookedYN = true;
        $sql_insert = "Insert Into appointment (Email_ID, Availability_ID, Date_Booked, Time_Booked, Quantity, ConfirmYN, Comments)Values (?,?,?,?,?,?,?)";
        $sql_update = "Update availability Set Booked_YN = ? Where Availability_ID = ?";

        mysqli_autocommit($connect, FALSE);
        $error = FALSE;
        $initialize_statement_insert = mysqli_stmt_init($connect);
        $prepare_statement_insert = mysqli_stmt_prepare($initialize_statement_insert, $sql_insert);

        //initialize and prepare the availability table statement 
        $initialize_statement_update = mysqli_stmt_init($connect);
        $prepare_statement_update = mysqli_stmt_prepare($initialize_statement_update, $sql_update);

        if ($prepare_statement_insert && $prepare_statement_update) {
            mysqli_stmt_bind_param($initialize_statement_insert, "sssssss", $email, $availability_id, $date, $time, $basket_quantity, $confirmYN, $comment);
            mysqli_stmt_execute($initialize_statement_insert);

            mysqli_stmt_bind_param($initialize_statement_update, "ss", $bookedYN, $availability_id);
            mysqli_stmt_execute($initialize_statement_update);

            if (mysqli_errno($connect)) {
                $error = True;
            }
        }
        if ($error) {
            mysqli_rollback($connect);
            $message_form = "<p>Failed to insert record into the database</p>";
        } else {
            mysqli_commit($connect);
            $body = "<h2>Thank You for booking Appointment with Us!</h2>";
            $body .= "<p>Email Address: $email</p>";
            $body .= "<p>Date Booked: $date</p>";
            $body .= "<p>Time Booked: $time</p>";
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
                $mail->setFrom('write2abhij@gmail.com', 'Confirmation of Appointment');
                $mail->addAddress($email, ); //Add a recipient
                $mail->addReplyTo($email, );
                $mail->addBCC('write2abhij@gmail.com');

                //Content
                $mail->isHTML(true); //Set email format to HTML
                $mail->Subject = 'An Appointment has been booked for you by Strawberry Fields! ';
                $mail->Body = $body;

                $mail->send();
                $_SESSION["user"] = "yes";
                $_SESSION["message"] = "Appointment booked sucessfully, A confirmation email has been sent to the customer";
                $_SESSION["username"] = $email;
                header("Location: admin-appointment.php");
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }

}
//basket <br /><b>Warning</b>:  Undefined array key "basket_quantity" in <b>C:\Users\write\Desktop\Capstone Project\StrawberryFields\admin\handle-appointment.php</b> on line <b>252</b><br />
if (isset($_POST['cancel'])) {
    header("Location:admin-appointment.php");
    $_SESSION["user"] = "yes";
    $_SESSION["message"] = "Transaction Cancelled";
}

?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/styles.css">
     <!-- Strawberry Favicon -->
   <link href="/img/favicon.ico" rel="icon" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Bootstrap icon library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <title>Strawberry Fields: Handle-Appointment</title>
  </head>

<body class="bg-light">
   
        <?php include("navbar.php"); ?>

    <main class="container py-5 vh-100">
        <h1 class="display-4 mb-4 text-center">Booking Appointment</h1>
        <?php
        if (!empty($message_appointment)) { ?>
            <div class="alert alert-danger">
                <?php echo $message_appointment; ?>

            </div>
        <?php } ?>

        <?php
        include_once("../database-connection.php");


        $availability_id = $_POST['availability_id'];
        $sql = "SELECT * FROM availability where Availability_ID = '$availability_id'";
        $result = mysqli_query($connect, $sql);
        $row = mysqli_fetch_assoc($result);

        ?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="my-3" method="POST">
            <div class="mb-3">
                <label for="add-email" class="form-label">Email</label>
                <input id="add-email" name="add-email" type="email" class="form-control"
                    aria-describedby="add-email-help">
                <div class="form-text" id="add-email-help">
                    <?php echo $message_email ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="availability_id" class="form-label">Availability ID</label>
                <input id="availability_id" name="availability_id" type="text" class="form-control"
                    aria-describedby="id-help" value='<?php echo $row['Availability_ID']; ?>' readonly>
                <div class="form-text" id="id-help">
                    <?php echo $message_add_id; ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="add-date" class="form-label">Date:</label>
                <input type="date" name="add-date" class="form-control" id="add-date" aria-describedby="add-date-help"
                    value='<?php echo htmlspecialchars($row['Date_Available']); ?>' readonly>
                <div class="form-text" id="add-date-help">
                    <?php echo $message_add_date; ?>
                </div>
            </div>
            <div class="mb-3">
                <label for="add-time" class="form-label">Time:</label>
                <input type="time" name="add-time" class="form-control" id="add-time" aria-describedby="add-time-help"
                    value="<?php echo htmlspecialchars($row['Time_Available']); ?>" readonly>
                <div class="form-text" id="add-time-help">
                    <?php echo $message_add_time; ?>
                </div>
            </div>
            <div class="mb-3">
                <label for="basket_quantity" class="form-label">Baskets</label>
                <input type="number" name="basket_quantity" class="form-control" id="basket_quantity"
                    aria-describedby="basket_quantity-help" value='<?php echo $_POST['basket_quantity']; ?>' readonly>
                <div class="form-text" id="basket_quantity-help">
                    <?php echo $message_basket; ?>
                </div>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Comment</label>
                <textarea id="comment" name="comment" aria-describedby="comment-help" class="form-control"></textarea>

                <div id="comment-help" class="form-text">

                </div>
            </div>


            <div class="d-flex">
                <input type="submit" name="add" class="btn btn-primary mx-2" value="Add">
                <input type="submit" name="cancel" class="btn btn-danger mx-2" value="Cancel">
            </div>
        </form>

    </main>

    <?php include("../footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>