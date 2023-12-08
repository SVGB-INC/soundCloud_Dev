<?php

session_start();
$_SESSION;

include("Config.php");
include("functions.php");


$user_data = check_login($pdo);

$sqlUserID = "SELECT user_id FROM reg_data_bank where user_name = '" . $_SESSION['user_name'] . "'";
$userID = $pdo->query($sqlUserID)->fetchAll(PDO::FETCH_COLUMN)[0];

$stmt = $pdo->query("SELECT upload_date,podcast_address,image_address,podcast_title,podcast_desc,podcast_likes, podcast_time, play_count, podcast_airtime, pID FROM podcast_details WHERE user_id = '" . $userID . "' order by upload_date DESC");


if ($_SERVER['REQUEST_METHOD'] === "POST") {

  try {
    $fileToDel = $_POST['pod_source_add'];
    !unlink($fileToDel);

    $sql = "DELETE FROM podcast_details WHERE podcast_address=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$fileToDel]);

    header("Location: my-podcasts.php");
  } catch (PDOException $e) {
    echo $e->getMessage();
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

  <link rel="stylesheet" href="./Styles/styles.css" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Chhogori | My Messages</title>
</head>

<body>
  <header class="container-fluid d-flex justify-content-center align-items-center">
    <nav class="navbar navbar_cstm navbar-expand-xl navbar-dark bg-dark fixed-top">
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
                <a class="nav-link active dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
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
  <main class="container-fluid pt-5">

    <div class="container pt-5">
      <div class="d-flex justify-content-between align-items-end flex-wrap">
        <div class="account-name my-sm-4s mt-3 pt-3">
          <h1 class="h2 text-nowrap">My Podcasts</h1>
          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
            <button type="button" class="btn btn-sm btn-labeled btn-outline-primary ms-2">
              <span class="btn-label me-2"><i class="fas fa-edit"></i></span>Edit Podcasts</button>
            <button type="button" class="btn btn-sm btn-labeled btn-outline-primary ms-2">
              <span class="btn-label me-2"><i class="fas fa-layer-group"></i></span>Add to playlist</button>
          </div>
        </div>
        <div>
          <span class="text-secondary"><small>01 of 10</small></span>
          <a class="btn btn-sm bg-secondary" href="#">
            <i class="fas fa-arrow-left text-white"></i>
          </a>
          <a class="btn btn-sm bg-secondary" href="#"><i class="fas fa-arrow-right text-white"></i></a>
        </div>
      </div>
      <hr />

      <?php
      while ($row = $stmt->fetch(PDO::FETCH_NUM)) {


        $sqlUserID = "SELECT count(*) FROM user_listened_pod WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $row[1])) . "'";
        $uniqueUserCount = $pdo->query($sqlUserID)->fetchAll(PDO::FETCH_COLUMN)[0];


        //to be revised
        $sqlUserID = "SELECT sum(add_share_creator) FROM payment_final WHERE pID = '" . $row[9] . "';";
        if ($pdo->query($sqlUserID)->rowCount() > 0) {
          $paymentFinal = $pdo->query($sqlUserID)->fetchAll(PDO::FETCH_COLUMN)[0];
        }

        $sqlUserID = "SELECT payment_share FROM payment_weekly WHERE pID = '" . $row[9] . "';";
        if ($pdo->query($sqlUserID)->rowCount() > 0) {
          $paymentWeekly = $pdo->query($sqlUserID)->fetchAll(PDO::FETCH_COLUMN)[0];
        }

        if (!isset($paymentFinal)) {
          $paymentFinal = "0";
        }

        if (!isset($paymentWeekly)) {
          $paymentWeekly = "0";
        }
        //to be revised


        //getting additional payment and charity
        $sqlQuery = "SELECT sum(creator_payment) FROM podcast_payment_history WHERE pID = '"  . $row[9] . "';";
        $amount = $pdo->query($sqlQuery)->fetchAll(PDO::FETCH_COLUMN)[0];

        $sqlQuery = "SELECT sum(charity) FROM podcast_payment_history WHERE pID = '"  . $row[9] . "';";
        $charity = $pdo->query($sqlQuery)->fetchAll(PDO::FETCH_COLUMN)[0];
        //getting additional payment and charity


      ?>

        <form name="imageform" method="post">
          <div class="py-4 d-flex align-items-center parentPodcast">
            <div>
              <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
            </div>
            <div class="ms-3 d-flex flex-wrap flex-sm-nowrap w-100">
              <img src=<?php echo $row[2] ?> class="img-fluid me-2" alt="Responsive-image" style="width:10rem; object-fit:cover;">
              <div style="flex: 1 1 auto;">
                <span class="text-secondary"><b><?php echo $row[3] ?></b></span>

                <input type="text" style="display:none" class="form-control" name="pod_image_source" value=<?php echo $row[2] ?>>
                <input type="text" style="display:none" class="form-control" name="pod_source_add" value=<?php echo $row[1] ?>>

                <p><?php echo $row[4] ?></p>

                <div class="d-flex gap-md-3 gap-2 flex-wrap flex-sm-nowrap">
                  <a id="source-image" name="source-image" style="display:none;" href=<?php echo $row[2] ?>></a>
                  <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $row[1] ?>></a>
                  <div class="d-flex w-100 flex-wrap gap-3">

                    <span class="me-auto btn btn-primary text-nowrap clickPod"><i class="fas fa-play me-1"></i>Play</span>

                    <span class="ms-2 text-nowrap d-flex justify-content-center align-items-center"><a href="#" class="text-decoration-none" style="cursor:default;"><i class="fas fa-heart"></i> <?php echo $row[5] ?></a></span>
                    <span class="ms-2 text-nowrap d-flex justify-content-center align-items-center"><a href="#" class="text-decoration-none" style="cursor:default;"><i class="fas fa-user"></i> <?php echo $uniqueUserCount; ?></a></span>
                    <span class="ms-2 text-nowrap d-flex justify-content-center align-items-center"><a href="#" class="text-decoration-none" style="cursor:default;"><i class="fa-regular fa-money-bill-1"></i> <?php echo $paymentFinal . " (WS) + " . floor($amount) . " (AA) + " . floor($charity)  . " (C)"?></a></span>

                    <?php $paymentFinal = "0";
                    $paymentWeekly = "0"; ?>

                    <!-- <span class="ms-2 text-nowrap"><a href="#" class="text-decoration-none"><i class="fas fa-redo"></i> 6</a></span> -->
                    <span class="text-nowrap d-flex justify-content-center align-items-center"><a href="#" class="text-decoration-none ms-2"><i class="fas fa-play me-1"></i><?php echo $row[7] ?></a></span>
                    <span class="ms-2 text-secondary text-nowrap d-flex justify-content-center align-items-center"><i class="fas fa-history"></i>&nbsp;<?php echo gmdate("H:i:s", $row[6]); ?></span>
                    <span class="ms-2 text-secondary text-nowrap d-flex justify-content-center align-items-center"><i class="fa-solid fa-clock"></i>&nbsp;<?php echo gmdate("H:i:s", $row[8]); ?></span>
                    <span class="ms-2 text-secondary text-nowrap d-flex justify-content-center align-items-center"><?php echo $row[0] ?></span>
                    <button type="submit" class="ms-4 btn btn-primary text-nowrap d-flex justify-content-center align-items-center"><i class="far fa-trash-alt"></i></button>
                    <a href=<?php echo "edit-details.php?pod=" . $row[1] ?> class="ms-1 btn btn-primary text-nowrap d-flex justify-content-center align-items-center"><i class="fa-solid fa-pen-to-square"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>

        <hr />

      <?php
      }
      ?>
      <div class="text-center mb-3">
        <p>
          More uploads means more Listeners.
        </p>
        <a class="pink-bg btn text-white fw-bolder btn-sm" aria-current="page" href="upload.php">Upload More</a>
      </div>
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
        <audio id="podcast-audio" controls>
          <source id="podcast-source" src="./audio/dam.mp3" type="audio/mpeg">
          Your browser does not support the audio element.
        </audio>
         <script>
            window.onload = function currentPlayFunction() {
              var nowAudioSrc = sessionStorage.getItem("songAdd");
              var nowAudioDuration = sessionStorage.getItem("songdur");
              var nowImage = sessionStorage.getItem("songPic");
              var nowTitle = sessionStorage.getItem("songTitle");
              var nowAuthor = sessionStorage.getItem("songAuthor");
              var nowState = sessionStorage.getItem("songState");

              if (nowAudioSrc.split('/')[4] === "dam.mp3") {
                // alert("No Song Playing");
                var audio = $("#podcast-audio");
                audio[0].pause();
                audio[0].load();
                audio[0].oncanplaythrough = audio[0].pause();
              } else {
                // alert(nowAudioSrc + " - " + nowAudioDuration + " - " + nowImage + " - " + nowTitle + " - " + nowAuthor);

                //setting currently playing audio

                if (nowState === "false") {
                  console.log("False" + sessionStorage.getItem("songState"));

                  $("#podcast_image").attr("src", nowImage);
                  $("#podcastTitle_Player").html(nowTitle);
                  $("#podcastAuthor_Player").html(nowAuthor);

                  var audio = $("#podcast-audio");
                  $("#podcast-source").attr("src", nowAudioSrc);
                  audio[0].pause();
                  audio[0].load();
                  audio[0].oncanplaythrough = audio[0].currentTime = nowAudioDuration;
                  audio[0].oncanplaythrough = audio[0].play();
                } else {
                  console.log("true" + sessionStorage.getItem("songState"));

                  $("#podcast_image").attr("src", nowImage);
                  $("#podcastTitle_Player").html(nowTitle);
                  $("#podcastAuthor_Player").html(nowAuthor);

                  var audio = $("#podcast-audio");
                  $("#podcast-source").attr("src", nowAudioSrc);
                  audio[0].pause();
                  audio[0].load();
                  audio[0].oncanplaythrough = audio[0].currentTime = nowAudioDuration;
                  audio[0].oncanplaythrough = audio[0].pause();
                }


              }
            }

            setInterval(() => {
              var song = document.getElementsByTagName('audio')[0];
              var isPlaying = document.getElementsByTagName('audio')[0].paused;
              var timePlayed = song.currentTime;
              var audioSrc = document.getElementById("podcast-source").src;
              var songImage = document.getElementById("podcast_image").src;
              var songTitle = document.getElementById("podcastTitle_Player").innerHTML;
              var songAuthor = document.getElementById("podcastAuthor_Player").innerHTML;

              sessionStorage.setItem("songAdd", audioSrc);
              sessionStorage.setItem("songdur", timePlayed);

              sessionStorage.setItem("songPic", songImage);
              sessionStorage.setItem("songTitle", songTitle);
              sessionStorage.setItem("songAuthor", songAuthor);

              sessionStorage.setItem("songState", isPlaying);

              // console.log(document.getElementById("podcast-source").src);
              // console.log(sessionStorage.getItem("songAdd") + " - " + sessionStorage.getItem("songdur"));
              // console.log(sessionStorage.getItem("songPic") + " - " + sessionStorage.getItem("songTitle")+ " - " + sessionStorage.getItem("songAuthor"));
              // console.log(sessionStorage.getItem("songState"));



            }, 1900);
          </script>
      </div>
      <div class="det-player">
        <img id="podcast_image" src="./images/song-cover.jpg" alt="" class="image-fluid">
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>



  <script>
    $('.clickPod').click(function() {
      var audioSource = $(this).parent().prevAll('a').first().attr("href");


      var audio = $("#podcast-audio");
      $("#podcast-source").attr("src", audioSource);
      audio[0].pause();
      audio[0].load();
      audio[0].oncanplaythrough = audio[0].play();

      var imageSource = $(this).parent().prevAll('a').first();
      var imageLink = imageSource.prevAll('a').first().attr("href");
      $("#podcast_image").attr("src", imageLink);


    });
  </script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
</body>

</html>