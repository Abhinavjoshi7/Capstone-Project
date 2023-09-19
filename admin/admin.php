<?php
session_start();
use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

if (!isset($_SESSION["admin-username"])) {
    header("location: ../login.php");
}

if (isset($_POST['email_id'])) {
    $email = $_POST['email_id'];
} else {
    $email = "";
}

if (isset($_POST['date'])) {
    $date = $_POST['date'];
} else {
    $date = "";
}

if (isset($_POST['time'])) {
    $time = $_POST['time'];
} else {
    $time = "";
}

if (isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];
} else {
    $appointment_id = "";
}

//delete 
if (isset($_POST['delete'])) {
    include_once("../database-connection.php");
    $sql = "Delete from appointment where Appointment_ID = ?";
    $stmt = mysqli_stmt_init($connect);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 's', $appointment_id);
        mysqli_stmt_execute($stmt);
        $message_delete = "Appointment with Appointment ID: " . $appointment_id . " has been deleted! ";
    }

    mysqli_commit($connect);
    $body = "<h2>Update on your Appointment with Us!</h2>";
    $body .= "<p>Email Address: $email</p>";
    $body .= "<p>Date Booked: $date</p>";
    $body .= "<p>Time Booked: $time</p>";
    
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
        $mail->setFrom('write2abhij@gmail.com', 'Cancellation of Appointment');
        $mail->addAddress($email, ); //Add a recipient
        $mail->addReplyTo($email, );
        $mail->addBCC('write2abhij@gmail.com');

        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = 'An Appointment has been Cancelled for you by Strawberry Fields! ';
        $mail->Body = $body;

        $mail->send();
        $_SESSION["user"] = "yes";
        $_SESSION["message"] = "Appointment Cancelled sucessfully, A Cancelation email has been sent to the customer";
        $_SESSION["username"] = $email;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Bootstrap icon library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <title>Strawberry Fields: Admin</title>
</head>

<body>

    <?php
    include("navbar.php");
    ?>

    <main class="vh-100">

        <h2 class="m-3 my-5 d-flex justify-content-center">
            Welcome to Admin Page, View your bookings here
        </h2>

        <?php
        include_once("../database-connection.php");

        // SQL query to fetch the data
        $sql = "SELECT CONCAT(FirstName, ' ', LastName) as Name, Date_Booked, Appointment_ID, Time_Booked, Quantity, customer.Email_ID, Comments FROM customer
INNER JOIN appointment on appointment.Email_ID = customer.Email_ID 
Where ConfirmYN = 'Y' AND CONCAT(Date_Booked, ' ', Time_Booked) > NOW()
ORDER BY Date_Booked, Time_Booked";
        $result = mysqli_query($connect, $sql);
        $num_rows = mysqli_num_rows($result);

        if ($num_rows > 0) {
            echo "<div class='container'>";
            echo "<div class='row'>";
            echo "<div class='col-12 mb-3'>";
            echo "<form action='export.php' method='post'>";
            echo "<button type='submit' name='export_csv' class='btn btn-primary'>Export to CSV</button>";
            echo "</form>";
            echo "</div>";

            echo "<div class='col-12'>";
            echo "<div class='table-responsive'>";
            echo "<table class='table table-striped table-hover'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Email ID</th>";
            echo "<th>Name</th>";
            echo "<th>Date Booked</th>";
            echo "<th>Time Booked</th>";
            echo "<th>Quantity</th>";
            echo "<th>Comments</th>";
            echo "<th>Delete</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            while ($row = mysqli_fetch_assoc($result)) {
                $formattedDate = date("F j, Y", strtotime($row['Date_Booked']));
                $formattedTime = date("g:i A", strtotime($row['Time_Booked']));
                echo "<tr>";
                echo "<td>" . $row['Email_ID'] . "</td>";
                echo "<td>" . $row['Name'] . "</td>";
                echo "<td>" . $formattedDate . "</td>";
                echo "<td>" . $formattedTime . "</td>";
                echo "<td>" . $row['Quantity'] . "</td>";
                echo "<td>" . $row['Comments'] . "</td>";

                // delete btn
                echo "<td class='delete-cell'>";
                echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' class='form-container' method='POST'>";
                echo "<input type='hidden' name='date' value='" . $row['Date_Booked'] . "'>";
                echo "<input type='hidden' name='time' value='" . $row['Time_Booked'] . "'>";
                echo "<input type='hidden' name='email_id' value='" . $row['Email_ID'] . "'>";
                echo "<input type='hidden' name='appointment_id' value='" . $row['Appointment_ID'] . "'>";
                echo "<button type='submit' name='delete' value='delete' class='btn btn-danger fixed-width-btn'>Delete</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<p class='text-success px-3' style='font-size: 20px;'> You don't have any bookings coming up </p>";
        }

        // Close the database connection
        mysqli_close($connect);
        ?>
    </main>

    <div>
        <?php include("../footer.php"); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

</body>

</html>