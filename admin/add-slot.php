<?php
session_start();
if (!isset($_SESSION["admin-username"])) {
    header("location:../login.php");
}

// $date = (isset($_POST['submit'])) ? trim($_POST['date']) : "";
// $time = (isset($_POST['submit'])) ? trim($_POST['time']) : "";
// $booked = (isset($_POST['submit'])) ? trim($_POST['booked']): "";

$message_date = "";
$message_time = "";
// $message_booked = "";
$message_form = "";
$form_good = null;

if (isset($_POST['submit'])) {
    $form_good = TRUE;
    $date = $_POST['date'] ?? "";
    $time = $_POST['time'] ?? "";
    if (empty($date) || empty($time)) {
        $message_form = "Please enter a valid date and time";
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
}


if ($form_good) {
    include_once("../database-connection.php");
    $sql_appointment_count = "select count(Availability_ID) from availability where Date_Available = '$formatted_date' and Time_Available = '$formatted_time'";
    $result_appointment_count = mysqli_query($connect, $sql_appointment_count);
    $row_appointment_count = mysqli_fetch_row($result_appointment_count)[0];
    if ($row_appointment_count > 3) {
        // 3 is because indexing is starting from 0
        $message_form .= "You cannot have more than 4 available slots for the same date and time combination";
    } else {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $sql_avai = "INSERT INTO availability(Date_Available, Time_Available) VALUES(?,?)";

        mysqli_autocommit($connect, FALSE);
        $error = FALSE;

        $initialize_statement_avai = mysqli_stmt_init($connect);

        //mysqli_stmt_prepare  Prepares a SQL query for execution with a statement object. 
        $prepare_statement_avai = mysqli_stmt_prepare($initialize_statement_avai, $sql_avai);

        if ($prepare_statement_avai) {
            mysqli_stmt_bind_param($initialize_statement_avai, "ss", $date, $time);
            mysqli_stmt_execute($initialize_statement_avai);
        } else {
            $message_form = "Failed to insert record into the database";
            if (mysqli_errno($connect)) {
                $error = True;
            }
        }

        // commit or rollback the transaction
        if ($error) {
            mysqli_rollback($connect);
            $message_form = "Failed to insert record into the database";
        } else {
            mysqli_commit($connect);
            $message_form = "Time Slot has been sucessfully added";
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
    <link rel="stylesheet" href="../css/styles.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Bootstrap icon library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <title>Strawberry Fields: Add Slot</title>
</head>

<body>

    <?php
    include("navbar.php");
    //include the other navbar for admin that has different hrefs
    ?>


    <main class="vh-100">
        <h2 class="m-3 my-5 d-flex justify-content-center test">
            Create a availability slot
        </h2>
        <?php if (!empty($message_form)) { ?>
            <p class="text-success" style="font-size: 20px !important;">
                <?php echo $message_form; ?>
            </p>

        <?php } ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="m-5 d-flex flex-wrap justify-content-center slot_mobile"
            method="POST">
            <div class="form-group m-2 d-flex">
                <label for="date" class="my-4">Date:</label>
                <input type="date" name="date" class="form-control m-3" id="date" value="<?php echo $date; ?>">

            </div>
            <div class="form-group m-2 d-flex">
                <label for="time" class="my-4">Time:</label>
                <input type="time" name="time" class="form-control m-3" id="time" value="<?php echo $time; ?>">

            </div>

            <button type="submit" name="submit" class="btn btn-primary m-4">Add</button>

        </form>
    </main>


    <?php include("../footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>


