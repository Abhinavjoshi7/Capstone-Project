<?php
session_start();
if (!isset($_SESSION["admin-username"])) {
    header("location: ../login.php");
}
if (isset($_POST['email_id'])) {
    $email = $_POST['email_id'];
} else {
    $email = "";
}
if (isset($_POST['unblock'])) {
    $blocked = 'N';
    include_once("../database-connection.php");
    $sql = "Update customer Set Blocked = ? where Email_ID = ?";
    $stmt = mysqli_stmt_init($connect);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 'ss', $blocked, $email);
        mysqli_stmt_execute($stmt);
        $message_unblock = "User with email: " . $email . " has been unblocked ";
    }
}

//block 
if (isset($_POST['block'])) {
    $blocked = 'Y';
    include_once("../database-connection.php");
    $sql = "Update customer Set Blocked = ? where Email_ID = ?";
    $stmt = mysqli_stmt_init($connect);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 'ss', $blocked, $email);
        mysqli_stmt_execute($stmt);
        $message_block = "User with email: " . $email . " has been blocked ";
    }
}

//delete 
if (isset($_POST['delete'])) {
    include_once("../database-connection.php");
    $email = $_POST['email_id'];
    $sql_waitlist = "DELETE FROM waitlist WHERE Email_ID = ?";
    $sql_appt = "DELETE FROM appointment WHERE Email_ID = ?";
    $sql_cust = "DELETE FROM customer WHERE Email_ID = ?";
    $sql_auth = "DELETE FROM authentication WHERE Email_ID = ?";

    mysqli_autocommit($connect, FALSE);

    $stmt1 = mysqli_stmt_init($connect);
    $stmt2 = mysqli_stmt_init($connect);
    $stmt3 = mysqli_stmt_init($connect);
    $stmt4 = mysqli_stmt_init($connect);

    if (mysqli_stmt_prepare($stmt1, $sql_waitlist) && mysqli_stmt_prepare($stmt2, $sql_appt) && mysqli_stmt_prepare($stmt3, $sql_cust) && mysqli_stmt_prepare($stmt4, $sql_auth)) {
        mysqli_stmt_bind_param($stmt1, 's', $email);
        mysqli_stmt_bind_param($stmt2, 's', $email);
        mysqli_stmt_bind_param($stmt3, 's', $email);
        mysqli_stmt_bind_param($stmt4, 's', $email);

        if (mysqli_stmt_execute($stmt1)) {
            if (mysqli_stmt_execute($stmt2)) {
                if (mysqli_stmt_execute($stmt3)) {
                    if (mysqli_stmt_execute($stmt4)) {
                        mysqli_commit($connect);
                        $message_delete = "User with email: " . $email . " has been deleted ";
                    } else {
                        mysqli_rollback($connect);
                        $message_delete = "<p>Failed to delete record from the authentication table. Error: " . mysqli_stmt_error($stmt4) . "</p>";
                    }
                } else {
                    mysqli_rollback($connect);
                    $message_delete = "<p>Failed to delete record from the customer table. Error: " . mysqli_stmt_error($stmt3) . "</p>";
                }
            } else {
                mysqli_rollback($connect);
                $message_delete = "<p>Failed to delete record from the appointment table. Error: " . mysqli_stmt_error($stmt2) . "</p>";
            }
        } else {
            mysqli_rollback($connect);
            $message_delete = "<p>Failed to delete record from the waitlist table. Error: " . mysqli_stmt_error($stmt1) . "</p>";
        }
    } else {
        $message_delete = "<p>Failed to prepare statements. Error: " . mysqli_error($connect) . "</p>";
    }

    mysqli_stmt_close($stmt1);
    mysqli_stmt_close($stmt2);
    mysqli_stmt_close($stmt3);
    mysqli_stmt_close($stmt4);
}







?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="user-styles.css">
    <link rel="stylesheet" href="../css/styles.css">
       <!-- Strawberry Favicon -->
   <link href="/img/favicon.ico" rel="icon" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Bootstrap icon library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <title>Strawberry Fields: User</title>
  </head>

<body>

    <?php include("navbar.php"); ?>

    <main class="vh-100">

        <h2 class="m-3 my-5 d-flex justify-content-center">
            View Your Customers Here
           
        </h2>
        <p class="text-center" style="font-size: 20px;">
                If you delete a user account, all their data including their bookings will be deleted
            </p>
        <?php if (!empty($message_block)) { ?>
            <p class="text-warning text-center" style="font-size: 20px;">
                <?php echo $message_block; ?>
            </p>
        <?php } ?>
        <?php if (!empty($message_unblock)) { ?>
            <p class="text-success text-center" style="font-size: 20px;">
                <?php echo $message_unblock; ?>
            </p>
        <?php } ?>
        <?php if (!empty($message_delete)) { ?>
            <p class="text-success text-center" style="font-size: 20px;">
                <?php echo $message_delete; ?>
            </p>
        <?php } ?>
        <?php
        include_once("../database-connection.php");
        $sql = "Select * from customer ";
        $result = mysqli_query($connect, $sql);
        $num = mysqli_num_rows($result);
        if ($num > 0) {
            echo "<div class='container'>";
            echo "<div class='row'>";
            echo "<div class='col-12'>";
            echo "<div class='table-responsive'>";
            echo "<table class='table table-striped table-hover'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Email ID</th>";
            echo "<th>First Name</th>";
            echo "<th>Last Name</th>";
            echo "<th>Phone</th>";
            echo "<th>Blocked</th>";
            echo "<th>Action</th>";
            echo "<th>Delete</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
        
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['Email_ID'] . "</td>";
                echo "<td>" . $row['FirstName'] . "</td>";
                echo "<td>" . $row['LastName'] . "</td>";
                echo "<td>" . $row['Phone'] . "</td>";
                echo "<td>" . $row['Blocked'] . "</td>";
                echo "<td class='action-cell'>";
        
                echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' class='form-container' method='POST'>";
                echo "<input type='hidden' name='email_id' value='" . $row['Email_ID'] . "'>";
        
                if ($row['Blocked'] == 'Y') {
                    echo "<button type='submit' name='unblock' value='unblock' class='btn btn-warning fixed-width-btn'>Unblock</button>";
                } else {
                    echo "<button type='submit' name='block' value='block' class='btn btn-danger fixed-width-btn'>Block</button>";
                }
        
                echo "</form>";
        
                echo "</td>";
                echo "<td class='delete-cell'>";
        
                echo "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' class='form-container' method='POST'>";
                echo "<input type='hidden' name='email_id' value='" . $row['Email_ID'] . "'>";
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
        }
         else {
            echo "<p class='text-success px-3' style='font-size: 20px;'> You don't have any customers signed up </p>";
        }
        ?>
    </main>

    <div class="">
        <?php include("../footer.php"); ?>
    </div>

</body>

</html>