<?php
session_start();
if (!isset($_SESSION["admin-username"])) {
    header("location: ../login.php");
}

if (isset($_POST['waitlist_id'])) {
    $waitlist_id = $_POST['waitlist_id'];
} else {
    $waitlist_id = "";
}

// Delete
if (isset($_POST['delete'])) {
    include_once("../database-connection.php");
    $sql = "Delete from waitlist where Waitlist_ID = ?";
    $stmt = mysqli_stmt_init($connect);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 's', $waitlist_id);
        mysqli_stmt_execute($stmt);
        $message_delete = "Waitlist with Waitlist ID: " . $waitlist_id . " has been deleted! ";
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

    <title>Strawberry Fields: Waitlist</title>
</head>

<body>

    <?php include("navbar.php"); ?>

    <main class="vh-100">

        <h2 class="m-3 my-5 d-flex justify-content-center">
            View you waitlisted users here
        </h2>

        <?php
        include_once("../database-connection.php");

        // SQL query to fetch the data
        $sql = "SELECT CONCAT(FirstName, ' ', LastName) as Name, Date_Available, Time_Available, Time_Available_Until, Waitlist_ID, Quantity, customer.Email_ID FROM customer
INNER JOIN waitlist on waitlist.Email_ID = customer.Email_ID 
Where ConfirmYN = 'Y' AND CONCAT(Date_Available, ' ', Time_Available) > NOW()
ORDER BY Date_Available, Time_Available";
        $result = mysqli_query($connect, $sql);
        $num_rows = mysqli_num_rows($result);

        if ($num_rows > 0) {
            echo "<div class='container'>";
            echo "<div class='row'>";
            echo "<div class='col-12 mb-3'>";
            echo "<form action='export-waitlist.php' method='post'>";
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
            echo "<th>Date Available</th>";
            echo "<th>Time Available</th>";
            echo "<th>Available Until</th>";
            echo "<th>Basket Quantity</th>";
            echo "<th>Delete</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            while ($row = mysqli_fetch_assoc($result)) {
                $date = date("l, F j, Y", strtotime($row['Date_Available']));
                $time = date("g:i A", strtotime($row['Time_Available']));
                $time_utnil = date("g:i A", strtotime($row['Time_Available_Until']));

                echo "<tr>";
                echo "<td>" . $row['Email_ID'] . "</td>";
                echo "<td>" . $row['Name'] . "</td>";
                echo "<td>" . $date . "</td>";
                echo "<td>" . $time . "</td>";
                echo "<td>" . $time_utnil . "</td>";
                echo "<td>" . $row['Quantity'] . "</td>";
                // delete btn
                echo "<td class='delete-cell'>";
                echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' class='form-container' method='POST'>";
                echo "<input type='hidden' name='waitlist_id' value='" . $row['Waitlist_ID'] . "'>";
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
            echo "<p class='text-success px-3' style='font-size: 20px;'> You don't have any waitlisted users </p>";
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