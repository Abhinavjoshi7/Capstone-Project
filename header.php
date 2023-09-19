
<nav class="navbar sticky-top navbar-expand-lg navbar-dark" id="grad3" style="height:4rem">
  <h2 class="visually-hidden">Strawberry</h2>
  <div class="container-fluid">
    <a class="navbar-brand text-success h6 offset-lg-1" id="sb_i" href="index.php">
      <i class="mb-2"><img src="img/logo.png"></i>
    </a>
    <div class="navbar-header">
      <a class="navbar-brand" id="sb_nav" href="index.php">StrawberryFields</a>
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
      aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-around" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
        <a class="nav-item nav-link" href="direction.php">Directions</a>
        <?php
        if (isset($_SESSION["admin-username"])) {
          echo "<a class='nav-item nav-link' href='admin/admin.php'>Admin</a>";
        }
        ?>
        <a class="nav-item nav-link" href="appointment.php">Appointment</a>
        <a class="nav-item nav-link" href="login.php">Login</a>
        <a class="nav-item  nav-link" href="logout.php">Logout</a>
      </div>
    </div>
  </div>
</nav>
