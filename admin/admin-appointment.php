<?php
session_start();


if (isset($_SESSION["user"])) {
  if (isset($_SESSION["username"])) {
    $message_appointment = "";
    $username = "";
    $username = $_SESSION["username"];
    $_SESSION["username"] = $username;
    if (isset($_SESSION["message"])) {
      $message = $_SESSION["message"];
    }
  }

  // $message_appointment =  "Hello $username";
} else {
  header("location: ../login.php");
}
?>


<?php
// Initialize variables
$_SESSION["message"] = "";
$form_good = null;

// Get the current date and time
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

// Handle form submission
if (isset($_POST["Submit"])) {
  $form_good = true;


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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

  <!-- Bootstrap icon library -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

  <title>Strawberry Fields: Admin</title>
</head>

<body class="bg-light">
  <?php include("navbar.php"); ?>
  <main class="container py-5" style="height:100%">
    <h1 class="display-4 mb-4 text-center">Booking Appointment</h1>
    <?php
    if (!empty($message_appointment)) { ?>
      <div class="alert alert-danger">
        <?php echo $message_appointment; ?>
      </div>
    <?php } ?>
    <?php if (!empty($message)) { ?>
      <p class="text-success" style="font-size: 20px;">
        <?php echo $message; ?>
      </p>
    <?php } ?>

    <?php
    include_once("../database-connection.php");
    $sql_verify = "SELECT * FROM availability WHERE Booked_YN = False AND CONCAT(Date_Available, ' ', Time_Available) > NOW()
ORDER BY Date_Available, Time_Available;
";
    $result = mysqli_query($connect, $sql_verify);
    // where Booked_YN = False and Date_Available > '$current_date' and Time_Available > '$current_time'
    
    //display the available dates and times 
    if (mysqli_num_rows($result) > 0) {
      echo "<div class='container'>";
      echo "<h2 class='text-center'>Available Dates and Times</h2>";
      echo "<table class='table'>";
      echo "<thead class='thead-dark'>
        <tr>
          <th scope='col'>Date</th>
          <th scope='col'>Time</th>
          <th scope='col'>Basket Quantity</th>
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
    <form method='post' action='handle-appointment.php' class='d-flex align-items-center'>
    <input type='hidden' name='availability_id' value='" . htmlspecialchars($row['Availability_ID']) . "'>
    <input type='hidden' name='date' value='" . htmlspecialchars($row['Date_Available']) . "'>
    <input type='hidden' name='time' value='" . htmlspecialchars($row['Time_Available']) . "'>
    <div class='mr-2'>
      <input type='number' name='basket_quantity' value='1' min='1' max='4' class='form-control text-center'>
    </div>
    <button type='submit' name='Submit' class='btn btn-primary mx-3'>Book Appointment</button>
  </form>
          </td>";
        echo "</tr>";
      }
      echo "</tbody>";
      echo "</table>";
      echo "</div>";
    } else {
      echo "<h1>No dates and times are currently available.</h1>";
    }
    ?>

  </main>
  <?php include("../footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>