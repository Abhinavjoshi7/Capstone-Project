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
  <link rel="stylesheet" href="css/testimonial.css">
  <!-- Strawberry Favicon -->
  <link href="/img/favicon.ico" rel="icon" type="image/x-icon">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

  <!-- Bootstrap icon library -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
  <!-- js library -->
  <script src="js/doc.js"></script>

  <title>Strawberry Fields</title>
</head>


<body>

  <?php
  include("header.php");

  if (isset($_SESSION["user"])) {
    if (isset($_SESSION["message"])) {
      $message = $_SESSION["message"];
    }
  }
  ?>

  <!-- Jumbotron -->
  <!-- <div class="jumbotron jumbotron-fluid">
    <div class="container">
      <h1 class="display-4">Welcome to Strawberry Fields</h1>
      <p class="lead">We are a family-run, pesticide-free Strawberry U-Pick farm located in Southwest .</p>
    </div>
  </div> -->
  <?php if (!empty($message)) { ?>
    <p class="text-success" style="font-size: 20px;">
      <?php echo $message; ?>
    </p>
  <?php } ?>

  <section class="hero" id="hero">
    <h1><div class="opacity">Welcome to Strawberry Fields</div></h1>
    <p>We offer the freshest, most delicious strawberries you'll ever taste. And we're committed to sustainability and
      quality.</p>
  </section>

  <!-- importantUpdates -->

  <div class="bg-light pb-4 ps-4 heightfix span8 topmargin" id="events-link">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-10 col-lg-11">
          <div class="card border-0 shadow rounded-3">
            <div class="card-body p-0">
              <h2 class="display-3 text-center text-success pt-4">Stay Updated with Our Latest News!</h2>
              <hr class="mb-4">
              <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner bgc">
                  <div class="carousel-item active">
                    <img src="img/importantUpdates/importantUpdates1.png" class="d-block h w-100 "
                      alt="End of Picking Season">
                    <div class="carousel-caption d-md-block text-dark ali">
                      <h3 class="mb-3">End of Picking Season</h3>
                      <p class="mb-4">The 2022 picking season is officially over. Thanks to everyone who came out and
                        picked this season! We hope to see you all next year!</p>
                      <p><small>Posted on October 13, 2022</small></p>
                    </div>
                  </div>
                  <div class="carousel-item">
                    <img src="img/importantUpdates/importantUpdates2.jpg" class="d-block h w-100" alt="waiting list!">
                    <div class="carousel-caption d-md-block text-dark ali">
                      <h3 class="mb-3">Appointments are now open for those not on our waiting list!</h3>
                      <p class="mb-4">We have made it through our waiting list and have opened up appointments to anyone
                        who would like to book a spot to come and pick strawberries.
                        <br> best time to check for appointments are Tuesday, Friday and Sunday Evenings!<br>
                        However, last minute appointments also open up the night before if people cancel or if we have
                        more strawberries than we expected!
                      </p>
                      <p><small>Posted on September 11, 2022</small></p>
                    </div>
                  </div>
                  <div class="carousel-item">
                    <img src="img/importantUpdates/importantUpdates3.png" class="d-block h w-100 "
                      alt="Peak Picking Season is Here!">
                    <div class="carousel-caption d-md-block text-dark ali">
                      <h3 class="mb-3">Peak Picking Season is Here!</h3>
                      <p class="mb-4">The strawberry plants are in full production! The end of August and into September
                        is when the plants produce the most amount of strawberries.
                        <br> have almost made it through the waiting list and are hoping to open up appointments to
                        everyone again after September long weekend.
                        <br> Our plants produce strawberries until frost. So there are still lots of opportunity to come
                        out and pick this year!
                      </p>
                      <p><small>Posted on August 29, 2022</small></p>
                    </div>
                  </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"
                  data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls"
                  data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <section id="commitment">
    <h2>Our Commitment to Sustainability and Quality</h2>
    <p>We believe in taking care of the earth and our customers. That's why we use sustainable farming practices and
      hand-select only the best strawberries for our customers.</p>
  </section>

  <section id="mission-vision-values">
    <div class="container">
      <h2>Our Mission, Vision, and Values</h2>
      <div class="row">
        <div class="col-md-4">
          <div class="card">

            <div class="card-body why-choose-us-item">
              <i class="fas fa-bullseye"></i>
              <h3>Mission</h3>
              <p>Our mission at Strawberry Fields is to provide fresh, delicious, and healthy strawberries to customers
                everywhere. We strive to deliver the highest quality fruit through sustainable farming practices,
                innovative technology</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">

            <div class="card-body why-choose-us-item">
              <i class="fas fa-eye"></i>
              <h3>Vision</h3>
              <p>Our vision is to be the preferred source for fresh, premium strawberries globally. We aim to
                revolutionize the way people enjoy and access the sweet, juicy, and healthy fruit by fostering a
                community of dedicated farmers, innovative growers, and loyal customers.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">

            <div class="card-body why-choose-us-item">
              <i class="fas fa-trophy"></i>
              <h3>Values</h3>
              <p>Our values are the foundation of our business, and we are committed to upholding them as we continue to
                bring the taste of nature's sweetness to the world. We believe in Quality, Sustainability and Health.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="why-choose-us">
    <div class="container">
      <h2 class="text-center mb-5">Why Choose Us?</h2>
      <div class="row">
        <div class="col-lg-4 col-md-6">
          <div class="why-choose-us-item">
            <i class="fas fa-leaf"></i>
            <h3>Freshness</h3>
            <p>We take pride in sourcing our strawberries organically, ensuring that they are always fresh and in
              season.</p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="why-choose-us-item">
            <i class="fas fa-seedling"></i>
            <h3>Sustainability</h3>
            <p>We believe in responsible farming practices that prioritize the health of the environment and the people
              who work on our farms.</p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="why-choose-us-item">
            <i class="fas fa-shopping-basket"></i>
            <h3>Tastier</h3>
            <p>Our strawberries tend to have a more natural and intense flavour.They are more than just clean - with
              every bite, you'll savor a taste that's supreme!</p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="why-choose-us-item">
            <i class="fas fa-bullseye"></i>
            <h3>Pesticide Free</h3>
            <p>Our strawberries are grown pesticide-free, so every bite is a dream! They are Juicy, sweet, and always
              pristine.</p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="why-choose-us-item">
            <i class="fas fa-users"></i>
            <h3>Community</h3>
            <p>We are proud to be a part of the local community and are committed to giving back through charitable
              donations and other initiatives.</p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="why-choose-us-item">
            <i class="fas fa-headset"></i>
            <h3>Customer Service</h3>
            <p>Our friendly and knowledgeable staff are always here to help you with any questions or concerns you may
              have.</p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <section id="testimonials">
    <div class="container">
      <h2 class="section-title">What Our Customers Say</h2>
      <div class="testimonial-slider">
        <div class="testimonial">
          <div class="testimonial-content">
            <p class="testimonial-text">"I recently visited the U-pick strawberry farm and had an amazing time! The
              strawberries were incredibly fresh and delicious, and it was great to be able to pick them straight from
              the vine."</p>
            <div class="testimonial-author">
              <img class="testimonial-img" src="img/testimonials/customer1.jpg" alt="Customer 1">
              <h4 class="testimonial-name">Nadine Berger</h4>

            </div>
          </div>
        </div>
        <div class="testimonial">
          <div class="testimonial-content">
            <p class="testimonial-text">"Visiting the U-pick farm was a highlight of my summer. I will definitely be
              going back next year and bringing my friends and family along."</p>
            <div class="testimonial-author">
              <img class="testimonial-img" src="img/testimonials/customer2.jpg" alt="Customer 2">
              <h4 class="testimonial-name">Jane Smith</h4>

            </div>
          </div>
        </div>
        <div class="testimonial">
          <div class="testimonial-content">
            <p class="testimonial-text">"I have been to many U-pick farms over the years, but this one takes the cake.
              The quality of the strawberries was unmatched, The U-pick farm offers a unique and enjoyable experience. I
              loved being able to pick my own fresh strawberries."</p>
            <div class="testimonial-author">
              <img class="testimonial-img" src="img/testimonials/customer3.jpg" alt="Customer 3">
              <h4 class="testimonial-name">David Lee</h4>

            </div>
          </div>
        </div>
        <div class="testimonial">
          <div class="testimonial-content">
            <p class="testimonial-text">"The U-pick strawberry farm is a hidden gem in the community. The strawberries
              were perfect, and the experience was so much fun for the whole family."</p>
            <div class="testimonial-author">
              <img class="testimonial-img" src="img/testimonials/customer4.jpg" alt="Customer 4">
              <h4 class="testimonial-name">Pascal Hass</h4>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>




  <?php
  include("footer.php")
    ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>

</body>

</html>