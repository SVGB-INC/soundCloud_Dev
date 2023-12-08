<?php

session_start();
$_SESSION;

include("Config.php");
include("functions.php");


$user_data = check_login($pdo);


if ($_SERVER['REQUEST_METHOD'] === "POST") {

  function cleanSpecialCharacters($string)
  {
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9.-]/', "", $string); // Removes special chars.
  }

  //$rand = rand(10, 100);
  $streplaceFileName = cleanSpecialCharacters($_FILES['uploaded-file']['name']);
  //$audioFile = $rand . "-" . $streplaceFileName;
  $audioFile = $streplaceFileName;
  $ds = DIRECTORY_SEPARATOR;
  $storeFolder = 'audio/' . $_SESSION['user_name'];

  if (is_dir($storeFolder)) {
  } else {
    mkdir('audio/' . $_SESSION['user_name']);
  }


  if ((!empty($_FILES)) && !empty($_FILES['uploaded-file']['name'])) {
    // if (preg_match('/[.](mp3)|(mp4)$/', $_FILES['uploaded-file']['name'])) {
    // $filename = $rand . "-" . $streplaceFileName;
    $filename = $streplaceFileName;
    $tempFile = $_FILES['uploaded-file']['tmp_name'];
    $targetPath = $storeFolder . $ds;
    $targetFile = $targetPath . $filename;

    // echo "select count(*) from podcast_details where SUBSTRING_INDEX(podcast_address,'\\\',-1) ='" . $filename . "'"; die;

    $nRows = $pdo->query("select count(*) from podcast_details where user_name = '" . $_SESSION['user_name'] . "' and SUBSTRING_INDEX(podcast_address,'\\\',-1) ='" . $filename . "'")->fetchColumn();

    $newRows = $pdo->query("select count(*) from podcast_details where podcast_address ='audio/" . $_SESSION['user_name'] . "/" .$filename . "'")->fetchColumn();

    // echo "select count(*) from podcast_details where SUBSTRING_INDEX(podcast_address,'\\\',-1) ='" . $filename . "'" . "<br>";
    // echo "select count(*) from podcast_details where podcast_address ='audio/" . $_SESSION['user_name'] . "/" .$filename . "'" . "<br>";
    // echo $nRows . "<br>";
    // echo $newRows . "<br>"; 
    // die;

    if ($nRows > 0 || $newRows > 0) {
      $errorMsg = "A podcast with this file name already exists.";

    } else {
      $_SESSION['file_name_audio'] = $targetFile;
      $check = move_uploaded_file($tempFile, $targetFile);
      if ($check) {
        $uploadMSG = "File Uploaded Successfully!";
        header("Location: upload-details.php");
      }
    }

  }
}


?>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />

  <!-- fontawesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- slick links start-->
  <!-- <link rel="stylesheet" href="./Styles/slick.css" />
    <link rel="stylesheet" href="./Styles/slick-theme.css" /> -->
  <!-- slick links end -->

  <link rel="stylesheet" href="./Styles/styles.css" />

  <!-- <script src="./js/jquery-3.6.0.min.js" type="text/javascript"></script> -->
  <!-- <script src="./js/chosen.jquery.min.js" type="text/javascript"></script>
  <link rel="stylesheet" href="./Styles/chosen.min.css" /> -->
  <title>Chhogori | Upload</title>
</head>

<body id="upload-body">
  <header class="container-fluid d-flex justify-content-center upload-header align-items-center">
    <nav class="navbar navbar_cstm navbar-expand-xl navbar-dark bg-dark home-head-bg fixed-top">
      <!-- <nav class="navbar navbar_cstm navbar-expand-lg navbar-dark  fixed-top"> -->
      <div class="container">
        <a class="navbar-brand fs-3 fw-bolder" href="home-page.php"><img class="logo" src="./images/Logo.png" alt="" /></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- <ul class="navbar-nav me-auto mb-2 mb-lg-0 d-flex align-items-center"> -->
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="home-page.php">Messages</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="channels.php">Voices</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="new_and_popular.php">New &amp; Popular</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="my-stream.php">My Stream</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="my-playlist.php">My Playlist</a>
            </li>
            <li class="nav-item mx-1">
              <div class="input-group  nav-search_cstm">
                <form action="./search.php" method="post" class="d-flex flex-nowrap">
                  <input type="search" name="search" class="form-control bg-transparent text-white" style="border-right: none; border-top-right-radius: 0; border-bottom-right-radius: 0;" placeholder="Search..." aria-label="search" aria-describedby="search" />
                  <button type="submit" class="input-group-text bg-transparent text-white" style=" border-top-left-radius: 0; border-bottom-left-radius: 0;"><span class="" id="basic-addon1"><i class="text-white fas fa-search"></i></span></button>
                </form>
              </div>
            </li>
          </ul>
          <ul class="navbar-nav mb-2 mb-lg-0 ms-md-auto">
            <li class="nav-item mx-1">
              <a class="nav-link pink-bg btn text-white fw-bolder btn-sm" aria-current="page" href="upload.php">Upload</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link" aria-current="page" href="notifications.php"><i class="fas fa-bell"></i></a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link" aria-current="page" href="messages.php"><i class="fas fa-envelope"></i></a>
            </li>
            <li class="nav-item mx-1">
              <!-- <a class="nav-link" aria-current="page" href="#"><i class="fas fa-user" style="user-select: auto"></i></a> -->
              <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fas fa-user" style="user-select: auto"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-lg-end" aria-labelledby="dropdownMenuLink">
                  <li><a class="dropdown-item" href="./my-podcasts.php">My Messages</a></li>
                  <li><a class="dropdown-item" href="./my-channels.php">My Voices</a></li>
                  <li><a class="dropdown-item" href="./my_playlists.php">My Playlists</a></li>
                  <li><a class="dropdown-item" href="./my-account.php">Settings / Account</a></li>
                  <li><a class="dropdown-item" href="./logout.php">Sign Out</a></li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>


  <main class="container">
    <div>
 <form action="" class="w-100 pt-5" enctype="multipart/form-data" method="POST">
 <?php
      if (isset($errorMsg)) {
      ?>
        <div class="alert alert-danger text-center">
          <strong><?php echo $errorMsg; ?></strong>
        </div>
      <?php
      }
      ?>
        <div class="uploadBox">
          <i class="fas fa-cloud-upload-alt"></i>
          <h2 class="text-center">Drag & Drop to Upload File</h2>
          <span class="text-center">OR</span>
          <label class="btn btn-outline-dark btn-lg" for="uploadBTN">&nbsp;&nbsp;Browse File&nbsp;&nbsp;</label>
          <input type="file" name="uploaded-file" id="uploadBTN" hidden />
        </div>
        <div class="tagsInput mt-3">
          <div class="form-div d-flex row justify-content-between">

          </div>
          <div class="form-div row d-flex justify-content-between mt-3">

            <input type="submit" name="Submit" class="btn pink-bg text-white mx-auto w-100 px-4 my-3" value="Upload Podcast">

            <?php
            if (isset($uploadMSG)) {
            ?>
              <div class="alert alert-success text-center my-3">
                <strong><?php echo $uploadMSG; ?></strong>
              </div>

            <?php

            }
            ?>

          </div>
        </div>
      </form>
    </div>
  </main>

  <footer class="container-fluid bg-dark">
    <div class="container p-5 px-3 px-md-5">
      <div class="px-0 px-md-5">
        <div class="row">
          <div class="col-12">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
        </div>
        <div class="row my-5">
          <div class="col-md-2 col-sm-3 col-6">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
          <div class="col-md-2 col-sm-3 col-6">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
          <div class="col-md-2 col-sm-3 col-6">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
          <div class="col-md-2 col-sm-3 col-6">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
          <div class="col-md-2 col-sm-3 col-6">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
          <div class="col-md-2 col-sm-3 col-6">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
          <div class="col-md-2 col-sm-3 col-6">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
          <div class="col-md-2 col-sm-3 col-6">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
          <div class="col-md-2 col-sm-3 col-6">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
          <div class="col-md-2 col-sm-3 col-6">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
          <div class="col-md-2 col-sm-3 col-6">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
          <div class="col-md-2 col-sm-3 col-6">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <a href="#" class="link-secondary text-nowrap">Hello World</a>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <div class="audio-player fixed-bottom container-fluid">
    <div class="container d-flex flex-wrap">
      <div class="actual-player">
        <audio controls>
          <source src="./audio/dam.mp3" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
      </div>
      <div class="det-player">
        <img src="./images/song-cover.jpg" alt="" class="image-fluid">
        <div class="text px-2">
          <strong>Song Head</strong>
          <p>song writer</p>
        </div>
        <div class="buttons">
          <i class="fas fa-heart"></i>
          <i class="fas fa-user-plus"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

  <!-- slick slider int link -->
  <!-- <script src="./js/slick.min.js"></script> -->
  <script src="./js/header.js"></script>



  <!-- Uploader script -->
  <script src="./js/uploader.js"></script>
  <!-- <script src="./js/tagsInput.js"></script> -->
  <!-- header color change on scroll -->


  <!--  -->
</body>

</html>