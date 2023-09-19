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

  function export_csv($data, $filename = "Bookings.csv") {
    //$data is an array containing the information to be exported, it comes from the select query 
    //Content-Type and Content-Disposition headers to let the browser know that the response should be treated as a CSV file and trigger a download with the specified filename
    // /Content-Disposition header is being set to attachment, which tells the browser to treat the response as a downloadable file, 
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');

    $file = fopen('php://output', 'w');
    //fputcsv writes the header row of the CSV file with the column names:
    fputcsv($file, array('Name', 'Date Booked', 'Time Booked', 'Quantity', 'Email', 'Comments'));
    //iterate through the data and write it to each row
    foreach ($data as $row) {
        fputcsv($file, $row);
    }
    fclose($file);
    exit();
}
include_once("../database-connection.php");
$sql = "SELECT CONCAT(FirstName, ' ', LastName) as Name, Date_Booked, Time_Booked, Quantity, customer.Email_ID, Comments FROM customer
INNER JOIN appointment on appointment.Email_ID = customer.Email_ID 
Where ConfirmYN = 'Y' AND CONCAT(Date_Booked, ' ', Time_Booked) > NOW()
ORDER BY Date_Booked, Time_Booked";
$result = mysqli_query($connect, $sql);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
export_csv($data);  
} 
else {
  header("location: ../login.php");
}

?>

