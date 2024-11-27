<?php
// session_start();
// Simulate user login status
$isLoggedIn = isset($_SESSION['user']) ? true : false;
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="">
        

<nav class="navbar navbar-expand-lg bg-secondary bg-gradient  ">
  <div class="container-fluid">
    <!-- <a class="navbar-brand" href="index.php">Home</a> -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active text-light" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-light" href="basephoto.php">about</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled text-light" aria-disabled="true">Disabled</a>
        </li>
      </ul>
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn   " type="submit">
        <?php
        // Using ternary operator to display login status message
        echo $isLoggedIn ? '<a class=" text-light"  href="logout.php">Logout</a>' : '<a class=" text-light"  href="login.php">login</a>';
        ?>
        <!-- <a href=""></a> -->
      
      </button>
        <!-- <button class="btn btn-outline-success" type="submit"><a class="navbar-brand" href="logout.php">Logout</a></button> -->
      </form>
    </div>
  </div>
</nav>
    </div>
</body>
</html>









