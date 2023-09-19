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
    $message_appointment = "";
    $username = "";
    $username = $_SESSION["username"];
    $_SESSION["username"] = $username;
  }

  // $message_appointment =  "Hello $username";
} else {
  header("Location: login.php");
}
?>


<?php
// Initialize variables
$_SESSION["message"] = "";
$form_good = null;

// Get the current date and time
$current_date = date('Y-m-d');
$current_time = date('H:i:s');
// the default time zone in php is in UTC 
$timezone = new DateTimeZone('America/Denver'); 

$date_time = new DateTime("$current_date $current_time");
$date_time->setTimezone($timezone);

$new_date = $date_time->format('Y-m-d');
$new_time = $date_time->format('H:i:s');

// Handle form submission
if (isset($_POST["Submit"])) {
  $form_good = true;

  // Set the timezone to Mountain Time
  date_default_timezone_set("America/Denver");

  // Validate date and time
  $date = $_POST['date'] ?? "";
  $time = $_POST['time'] ?? "";
  if (empty($date) || empty($time)) {
    $message_appointment = "Please enter a valid date and time";
    $form_good = false;
  } else {
    $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
    if (!$datetime || $datetime->format('Y-m-d H:i:s') !== $date . ' ' . $time) {
      $message_appointment = "The inputted date is not in the current format, Date should follow YYYY-MM-DD, Time should follow HH:MM:SS ";
      $form_good = false;
    } elseif ($datetime < new DateTime()) {
      $message_appointment = "The selected date and time have already passed, please select a future date and time";
      $form_good = false;
    }
  }

  // Validate id and basket quantity fields
  $availability_id = $_POST['availability_id'] ?? "";
  if (empty($availability_id)) {
    $message_appointment .= "No Availability ID was passed";
    $form_good = false;
  }

  $basket_quantity = $_POST['basket_quantity'] ?? "";
  if (empty($basket_quantity)) {
    $message_appointment .= "No Basket Quantity was passed";
    $form_good = false;
  }
  if ($basket_quantity > 4) {
    $message_appointment .= "Basket Quantity can not be more than 4";
    $form_good = false;
  }
    include_once("database-connection.php");
  $sql_blocked = "Select * from customer where Email_ID = '$username' and Blocked = 'Y'";
  $result_blocked = mysqli_query($connect, $sql_blocked);
  $row_blocked = mysqli_num_rows($result_blocked);
  if($row_blocked > 0){
    $message_appointment .= "Your account has been blocked, please contact the business owner";
    $form_good = false;
  }
}
if ($form_good) {
  //check if the appointment id already exists in the database 
  include_once("database-connection.php");

  //verify that the user doesn't have already an appointment coming 
  $sql_verify = "SELECT * FROM appointment WHERE (Date_Booked > '$new_date' OR (Date_Booked = '$new_date' AND Time_Booked > '$new_time')) AND Email_ID = '$username'";
  $result_verify = mysqli_query($connect, $sql_verify);
  $row_verify = mysqli_num_rows($result_verify);
  if ($row_verify > 0) {
    $message_appointment .= "You already have an upcoming appointment, please wait until it has passed to book another one.";
    $form_good = false;
  } else {
   
    $sql_check = "Select Availability_ID From appointment where Availability_ID = '$availability_id'";
    $result_check = mysqli_query($connect, $sql_check);
    $row_count = mysqli_num_rows($result_check);
    if ($result_check && $row_count > 0) {
      $message_appointment .= "The availability ID already exists, pick a different one";
    } if ($form_good) {
      $confirmYN = 'Y';
      $bookedYN = true;
      $sql_insert = "Insert Into appointment (Email_ID, Availability_ID, Date_Booked, Time_Booked, Quantity, ConfirmYN)Values (?,?,?,?,?,?)";
      $sql_update = "Update availability Set Booked_YN = ? Where Availability_ID = ?";

      mysqli_autocommit($connect, FALSE);
      $error = FALSE;
      $initialize_statement_insert = mysqli_stmt_init($connect);
      $prepare_statement_insert = mysqli_stmt_prepare($initialize_statement_insert, $sql_insert);

      //initialize and prepare the availability table statement 
      $initialize_statement_update = mysqli_stmt_init($connect);
      $prepare_statement_update = mysqli_stmt_prepare($initialize_statement_update, $sql_update);

      if ($prepare_statement_insert && $prepare_statement_update) {
        mysqli_stmt_bind_param($initialize_statement_insert, "ssssss", $username, $availability_id, $date, $time, $basket_quantity, $confirmYN);
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
        $body .= "<p>Email Address: $username</p>";
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
          $mail->addAddress($username, ); //Add a recipient
          $mail->addReplyTo($username, );
          $mail->addBCC('write2abhij@gmail.com');

          //Content
          $mail->isHTML(true); //Set email format to HTML
          $mail->Subject = 'You Have Booked Your Appointment With Strawberry Fields ';
          $mail->Body = $body;

          $mail->send();
          $_SESSION["user"] = "yes";
          $_SESSION["message"] = "Thank You for Booking Appointment. A confirmation email has been sent to you!";
          $_SESSION["username"] = $username;
          header("Location: index.php");
        } catch (Exception $e) {
          echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
      }
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
  <link rel="stylesheet" href="css/styles.css">
   <!-- Strawberry Favicon -->
   <link href="/img/favicon.ico" rel="icon" type="image/x-icon">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

  <!-- Bootstrap icon library -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

  <title>Strawberry Fields: Appointment</title>
  <style>
    .basket {
      min-width: 50px;
    }
    
    </style>
</head>

<body class="bg-light">
  <?php include("header.php"); ?>
  <main class="container py-5" style="height:100%">
    <h1 class="display-4 mb-4 text-center">Booking Appointment</h1>
    <?php
    if (!empty($message_appointment)) { ?>
      <div class="text-warning" style="font-size: 20px;">
        <?php echo $message_appointment; ?>
      </div>
    <?php } ?>

    <?php
    include_once("database-connection.php");
    $sql_verify = "SELECT * FROM availability WHERE Booked_YN = False AND CONCAT(Date_Available, ' ', Time_Available) > NOW()
ORDER BY Date_Available, Time_Available;
";
    $result = mysqli_query($connect, $sql_verify);
    // where Booked_YN = False and Date_Available > '$current_date' and Time_Available > '$current_time'
    
    //display the available dates and times 
    if (mysqli_num_rows($result) > 0) {
      echo "<div class='container'>";
      echo "<h2 class='text-center'>Available Dates and Times</h2>";
      echo "<div class='table-responsive'>";
      echo "<table class='table'>";
      echo "<thead class='thead-dark'>
        <tr>
          <th scope='col'>Date</th>
          <th scope='col'>Time</th>
          <th scope='col'>Basket(s)</th>
        </tr>
      </thead>";
      echo "<tbody>";
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        //strtotime($row['Date_Available']) converts the string value of Date_Available to a Unix timestamp, which represents the number of seconds since January 1 1970 00:00:00 UTC.
        //date("l, F j, Y", strtotime($row['Date_Available'])) formats the Unix timestamp as a string in the format "Day of the week, Month Day, Year
        $date = date("l, F j, Y", strtotime($row['Date_Available']));
        // /date("g:i A", strtotime($row['Time_Available'])) formats the Unix timestamp as a string in the format "Hour:Minute AM/PM".
        $time = date("g:i A", strtotime($row['Time_Available']));
        echo "<td>" . $date . "</td>";
        echo "<td>" . $time . "</td>";
        //<input type='hidden' name='time' value='".htmlspecialchars($row['Time_Available'])."'>: creates a hidden form field that contains the time of the available appointment as a value. This field is used to send the time to the server as part of the booking request.
        echo "<td>
    <form method='post' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' class='d-flex align-items-center'>
    <input type='hidden' name='availability_id' value='" . htmlspecialchars($row['Availability_ID']) . "'>
    <input type='hidden' name='date' value='" . htmlspecialchars($row['Date_Available']) . "'>
    <input type='hidden' name='time' value='" . htmlspecialchars($row['Time_Available']) . "'>
    <div class='mr-2'>
      <input type='number' name='basket_quantity' value='1' min='1' max='4' class='form-control text-center basket'>
    </div>
    <button type='submit' name='Submit' class='btn btn-primary mx-3'>Book Appointment</button>
  </form>
          </td>";
        echo "</tr>";
      }
      echo "</tbody>";
      echo "</table>";
      echo "</div>";
      echo "</div>";
    } else {
      echo "<h1>No dates and times are currently available</h1>.";
    }
    ?>

    <?php
    echo "If there is no available dates, You can regiter for the waitlist, and our team will get back to you once we get any available slots."
      ?>

    <a href="waitlist.php">Click here to register for waitlist</a>




  </main>
  <?php include("footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>