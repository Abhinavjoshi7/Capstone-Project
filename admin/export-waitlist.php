<?php
session_start();
if (!isset($_SESSION["admin-username"])) {
    header("location: ../login.php");
}
?>
<?php
include_once("../database-connection.php");

function export_csv($data, $filename = "Waitlist.csv") {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');

    $file = fopen('php://output', 'w');
    fputcsv($file, array('Name', 'Date Available', 'Time Available', 'Available Until', 'Quantity','Email'));

    foreach ($data as $row) {
        fputcsv($file, $row);
    }
    fclose($file);
    exit();
}

$sql = "SELECT CONCAT(FirstName, ' ', LastName) as Name, Date_Available, Time_Available, Time_Available_Until, Quantity, customer.Email_ID FROM customer
INNER JOIN waitlist on waitlist.Email_ID = customer.Email_ID 
Where ConfirmYN = 'Y' AND CONCAT(Date_Available, ' ', Time_Available) > NOW()
ORDER BY Date_Available, Time_Available";
$result = mysqli_query($connect, $sql);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
export_csv($data);

?>