<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: appointment.php");
}
$first_name = (isset($_POST['submit'])) ? trim($_POST['first-name']) : "";
$last_name = (isset($_POST['submit'])) ? trim($_POST['last-name']) : "";
$email = (isset($_POST['submit'])) ? trim($_POST['email']) : "";
$password = (isset($_POST['submit'])) ? trim($_POST['password']) : "";
$confirm_password = (isset($_POST['submit'])) ? trim($_POST['confirm-password']) : "";
$phone = (isset($_POST['submit'])) ? trim($_POST['phone']) : "";

//Encryption of password 
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Message Variables
$message_first_name = "";
$message_last_name = "";
$message_email = "";
$message_phone = "";
$message_password = "";
$message_confirm_password = "";
$message_pass = "";
$message_form = "";
// Initialisation for our boolean (when the user has not touched the submit button yet!)
$form_good = null;

if (isset($_POST['submit'])) {
    // This boolean will keep track of whether or not we've passed validation.
    $form_good = TRUE;

    // First Name Validation
    if ($first_name == "") {
        $message_first_name = "<p>Please enter your first name.</p>";
        $form_good = FALSE;
    } else {
        $first_name = filter_var($first_name, FILTER_SANITIZE_STRING);
    }
    if ($first_name == FALSE) {
        $message_first_name = "<p>Please enter a valid first name.</p>";
        $form_good = FALSE;
    }
    // Last Name Validation
    if ($last_name == "") {
        $message_last_name = "<p>Please enter your last name.</p>";
        $form_good = FALSE;
    } else {
        $last_name = filter_var($last_name, FILTER_SANITIZE_STRING);
    }
    if ($last_name == FALSE) {
        $message_last_name = "<p>Please enter a valid last name.</p>";
        $form_good = FALSE;
    }

    // EMAIL VALIDATION
    if ($email == "") {
        $message_email = "<p>Please enter your email address.</p>";
        $form_good = FALSE;
    } else {

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    }
    if ($email == FALSE) {
        $message_email = "<p>Please enter a valid email address.</p>";
        $form_good = FALSE;
    } else {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    if ($email == FALSE) {
        $message_email = "<p>Please enter a valid email address, including an @ and a top-level domain.</p>";
        $form_good = FALSE;
    } else {

        $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";

        if (preg_match($pattern, $email) == false) {
            $message_email = "<p>Please enter a valid email address, including an @ and a top-level domain.</p>";
            $form_good = FALSE;
        }

    }
    //Password Validation 
    if (strlen($password) < 8) {
        $message_password = "<p>Your password cannot be less than 8 characters long </p>";
        $form_good = FALSE;
    }
    if ($password !== $confirm_password) {
        $message_confirm_password = "<p>Your passwords do not match </p>";
        $form_good = FALSE;
    }


    //  PHONE NUMBER
    if ($phone == "") {
        $message_phone = "<p>Please enter your phone number.</p>";
        $form_good = FALSE;
    } else {
        $phone = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
    }
    if ($phone == FALSE) {
        $message_phone = "<p>Please enter your phone number without letters or special characters.</p>";
        $form_good = FALSE;
    } else {
        // Let's strip out any potential extra characters the users punched in.
        $phone = str_replace('-', '', $phone);
        $phone = str_replace('+', '', $phone);
        $phone = str_replace('.', '', $phone);
        $phone = str_replace('(', '', $phone);
        $phone = str_replace(')', '', $phone);
    }

    if (!is_numeric($phone)) {
        $message_phone = "<p>Please enter your phone number without letters or special characters.</p>";
        $form_good = FALSE;
    } else {

        if (strlen($phone) != 10) {
            $message_phone = "<p>Please enter a ten-digit Canadian phone number without the country code.</p>";
            $form_good = FALSE;
        }
    }
}

if ($form_good == TRUE) {
    include_once("database-connection.php");
    //check if the email already exists
    $sql_check = "SELECT * FROM authentication where Email_ID = '$email'";
    $result = mysqli_query($connect, $sql_check);
    $row_count = mysqli_num_rows($result);
    if ($result && $row_count > 0) {
        $message_form = "<p>The email already exists</p>";
        $form_good = false;
    } else {
        $sql_auth = "INSERT INTO authentication(Email_ID, Password) VALUES (?,?)";
        $sql_cust = "INSERT INTO customer(Email_ID, FirstName, LastName, Phone) VALUES (?,?,?,?)";
        // using transactions to ensure both inserts succeed or fail together
        mysqli_autocommit($connect, FALSE);
        $error = FALSE;

        //mysqli_stmt_init() is used to initialize a new statement object.A statement object is an object that represents a prepared statement that you can execute multiple times with different parameters.
        $initialize_statement_auth = mysqli_stmt_init($connect);

        //mysqli_stmt_prepare  Prepares a SQL query for execution with a statement object. 
        $prepare_statement_auth = mysqli_stmt_prepare($initialize_statement_auth, $sql_auth);

        $initialize_statement_cust = mysqli_stmt_init($connect);
        $prepare_statement_cust = mysqli_stmt_prepare($initialize_statement_cust, $sql_cust);

        if ($prepare_statement_auth && $prepare_statement_cust) {
            //mysqli_stmt_bind_param. Binds parameters to a prepared statement. Once you've prepared a statement object with a SQL query, you can use this function to bind parameter values to the placeholders in the query. 
            //the first argument should be the statement object, second argument should be the type of values
            //the ss means that two variables are being bound, and both variables are string 
            mysqli_stmt_bind_param($initialize_statement_auth, "ss", $email, $password_hash);
            mysqli_stmt_execute($initialize_statement_auth);

            mysqli_stmt_bind_param($initialize_statement_cust, "ssss", $email, $first_name, $last_name, $phone);
            //mysqli_stmt_execute. Executes a prepared statement. Once you've prepared a statement object with a SQL query and bound parameter values to it, you can use this function to execute the query. The function takes a single argument, which is the statement object.
            mysqli_stmt_execute($initialize_statement_cust);
            // mysqli_errno the error number for the most recent MySQLi function call
            //The error number is a unique code that corresponds to a specific error condition. When a MySQLi function call fails, it sets an error code, which can be retrieved using mysqli_errno().
            if (mysqli_errno($connect)) {
                $error = TRUE;
            }
        }

        // commit or rollback the transaction
        if ($error) {
            mysqli_rollback($connect);
            $message_form = "<p>Failed to insert record into the database</p>";
        } else {
            mysqli_commit($connect);
            if ($email == "darshiljogani@gmail.com" || $email == "write2abhij@gmail.com") {
                $_SESSION["user"] = "yes";
                //pass the email to the session variable too 
                $_SESSION["admin-username"] = $email;
                $_SESSION["username"] = $email;
                header("Location: admin/admin.php");
            } else {
                $_SESSION["user"] = "yes";
                //pass the email to the session variable too 
                $_SESSION["username"] = $email;
                header("Location: appointment.php");
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
    <!-- Strawberry Favicon -->
    <link href="/img/favicon.ico" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="css/styles.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Bootstrap icon library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <title>Strawberry Fields: Register Yourself</title>
</head>

<body class="bg-secondary-subtle">
    <?php
    include("header.php");
    ?>
    <main class="d-flex justify-content-center align-items-center min-vh-100 p-3">
        <section class="row">
            <div class="col bg-white rounded p-5 border border-secondary-subtle">
                <h1 class="fw-light">Register</h1>
                <p class="text-muted">Please fill out all of the fields below to register.</p>

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="my-3" method="POST">
                    <!-- First & Last Name -->
                    <div class="mb-3">
                        <label for="first-name" class="form-label">First Name</label>
                        <input id="first-name" type="text" name="first-name" aria-describedby="first-name-help"
                            class="form-control" value="<?php echo $first_name; ?>">
                        <div id="first-name-help" class="form-text">
                            <?php echo $message_first_name; ?>
                        </div>
                    </div>
                    <!-- Last Name  -->
                    <div class="mb-3">
                        <label for="last-name" class="form-label">Last Name</label>
                        <input id="last-name" type="text" name="last-name" aria-describedby="last-name-help"
                            class="form-control" value="<?php echo $last_name; ?>">
                        <div id="last-name-help" class="form-text">
                            <?php echo $message_last_name; ?>
                        </div>
                    </div>
                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" name="email" type="text" class="form-control" aria-describedby="email-help"
                            value="<?php echo $email; ?>">
                        <div class="form-text" id="email-help">
                            <?php echo $message_email, $message_form; ?>
                        </div>
                    </div>
                    <!-- Password -->
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
                    <!-- Phone Number -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input id="phone" name="phone" type="tel" class="form-control" aria-describedby="phone-help"
                            value="<?php echo $phone; ?>">
                        <div class="form-text" id="phone-help">
                            <?php echo $message_phone; ?>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="d-flex justify-content-center">
                        <input type="submit" name="submit" class="btn btn-primary" value="Register">
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
    <?php
    include("footer.php")
        ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

</body>

</html>