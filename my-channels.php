<?php

session_start();
$_SESSION;

include("Config.php");
include("functions.php");


$user_data = check_login($pdo);

$sqlUserID = "SELECT user_id FROM reg_data_bank where user_name = '" . $_SESSION['user_name'] . "'";
$userID = $pdo->query($sqlUserID)->fetchAll(PDO::FETCH_COLUMN)[0];

$stmt = $pdo->query("SELECT * FROM channels WHERE author = '" . $_SESSION['user_id'] . "'");
$stmt->execute();
$all_channels = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  try {
    $channel_id = $_POST['channel_id'];

    // get channel name by id
    $query = "SELECT title 
                FROM channels
                  WHERE
                  ID = '" . $_POST['channel_id'] . "'
                  LIMIT 1
                ";

    $sql = $pdo->query($query);
    $sql->execute();
    $channel_name = $sql->fetch(PDO::FETCH_COLUMN);


    // delete all related data from differetn tabels

    $query = "DELETE ch,pd,fa,la
                    FROM channels as ch 
                      LEFT JOIN podcast_details as pd
                      ON
                        ch.author = pd.user_ID
                        AND
                        ch.title = pd.podcast_channel
                      LEFT JOIN fvrt_audio as fa
                        ON
                        fa.podcast = pd.pID
                      LEFT JOIN liked_audio as la
                        ON
                        la.podcast = pd.pID
                      WHERE
                        ch.author = '" . $_SESSION['user_id'] . "'
                      AND
                      ch.title = '" . $channel_name . "'
                    ";
    $sql = $pdo->query($query);
    $sql->execute();
    $channel_data = $sql->fetchAll(PDO::FETCH_ASSOC);

    header("Location: my-channels.php");
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
  <title>Chhogori | My Voices</title>

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
          <h1 class="h2 text-nowrap">My Channels</h1>
          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
            <button type="button" class="btn btn-sm btn-labeled btn-outline-primary ms-2">
              <span class="btn-label me-2"><i class="fas fa-edit"></i></span>Edit Podcasts</button>
            <a type="button" href="createchannel.php" class="btn btn-sm btn-labeled btn-outline-primary ms-2">
              <span class="btn-label me-2"><i class="fas fa-layer-group"></i></span>Add to Channels</a>
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
      foreach($all_channels as $channel){
      ?>

        <form name="imageform" method="post">
          <div class="py-4 d-flex align-items-center parentPodcast">
            <div>
              <input class="form-check-input" type="checkbox" value="<?php echo $channel['ID']; ?>" id="<?php echo $channel['ID']; ?>">
            </div>
            <div class="ms-3 d-flex flex-wrap flex-sm-nowrap w-100">
              <img src="<?php echo './images/user_channel_pic/'.$channel['img']; ?>" class="img-fluid me-2" alt="Responsive-image" style="width:10rem; object-fit:cover;">
              <div style="flex: 1 1 auto;" class="mySingleChannel">
                <span class="text-secondary"><a href="channel.php?channel=<?php echo $channel['title']; ?>"><b><?php echo $channel['title']; ?></b></a></span>

                <input type="text" style="display:none" class="form-control" name="pod_image_source" value=<?php  ?>>
                <input type="text" style="display:none" class="form-control" name="pod_source_add" value=<?php   ?>>
                <input type="text" style="display:none" class="form-control" name="channel_id" value=<?=   $channel['ID']; ?>>

                <p><?php echo $channel['short_desc']; ?></p>
                <div class="d-flex gap-md-3 gap-2 flex-wrap flex-sm-nowrap thingsToBeHidden">
                  <a id="source-image" name="source-image" style="display:none;" href=<?php echo 'likes'; ?>></a>
                  <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $channel['short_desc'];  ?>></a>
                  <!-- <span class="me-auto btn btn-primary text-nowrap clickPod"><i class="fas fa-play me-1"></i>Play</span> -->
                  <span class="ms-auto text-nowrap d-flex justify-content-center align-items-center"><a href="#" class="text-decoration-none" style="cursor:default;"><i class="fa-solid fa-list-check"></i> <?php echo 'likes'; ?></a></span>
                  <span class="ms-2 text-secondary text-nowrap d-flex justify-content-center align-items-center"><?php echo 'date'; ?></span>
                  <button type="submit" class="ms-4 btn btn-primary text-nowrap d-flex justify-content-center align-items-center"><i class="far fa-trash-alt"></i></button>
                  <a href=<?php echo "editchannel.php?id=" . $channel["ID"]; ?> class="ms-1 btn btn-primary text-nowrap d-flex justify-content-center align-items-center"><i class="fa-solid fa-pen-to-square"></i></a>
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
    // $('.clickPod').click(function() {
    //   var audioSource = $(this).prevAll('a').first().attr("href");
    //   var audio = $("#podcast-audio");
    //   $("#podcast-source").attr("src", audioSource);
    //   audio[0].pause();
    //   audio[0].load();
    //   audio[0].oncanplaythrough = audio[0].play();

    //   var imageSource = $(this).prevAll('a').first();
    //   var imageLink = imageSource.prevAll('a').first().attr("href");
    //   $("#podcast_image").attr("src", imageLink);


    // });
  </script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
</body>

</html>