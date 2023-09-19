<?php 

session_start();
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

	<title>Strawberry Fields: Directions</title>
</head>

<body>
	<?php
	include("header.php");
	echo '<link rel="stylesheet" href="css/directions.css">';
	?>
	<h1 class="mb-3">U-Pick Strawberry Fields - Map</h1>

	<div class="map">
		<div class="map-wrapper">
			<iframe
				src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2376.751536758858!2d-113.65964194870776!3d53.4371506755089!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xd5f4d3f8db6fbcb7!2sStrawberry+Fields!5e0!3m2!1sen!2sca!4v1556808786774!5m2!1sen!2sca"
				allowfullscreen></iframe>
		</div>
	</div>

	<h2 class="px-3 text-center"><i class="bi bi-geo-alt"></i> Directions</h2>
	<h5 class="p-3 text-center">140 Grandisle Road NW, Edmonton, AB</h5>
	<div class="d-flex justify-content-center">
		<div>
			<p class="text-center">If you are coming from the City of Edmonton the best driving directions are:</p>
			<ul class="list-group m-3">
				<li class="list-group-item text-center">Take Anthony Henday Drive SW. Take the Cameron Heights exit and go south west along Maskekosihk Trail</li>
				<li class="list-group-item text-center">
Take 199 St NW to Grandisle Rd NW</li>
				<li class="list-group-item text-center">Continue straight for 1.8 km, then Slight right onto 23 Ave NW/Maskêkosihk Trail</li>
				<li class="list-group-item text-center">Turn left to stay on 199 St NW</li>
				<li class="list-group-item text-center">Turn left onto Grandisle Rd NW</li>
				<li class="list-group-item text-center">Destination will be on the left. Feel free to park on the side of the road or by the gate.</li>
			</ul>
		</div>
	</div>
	<p class="px-3 text-center">Picking strawberries is by APPOINTMENT ONLY! Please make an appointment before making your way to our strawberry field.</p>
    <?php
  include("footer.php")
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>


</html>