<?php

session_start();
$_SESSION;

include("Config.php");
include("functions.php");

$user_data = check_login($pdo); // reg_data_bank;


$sqlUserPods = "SELECT podcast_payment FROM user_podcast_access where user_name = '" . $_SESSION['user_name'] . "'";
$sqlPods = '-';
if ($pdo->query($sqlUserPods)->fetchAll(PDO::FETCH_COLUMN)) {
  $sqlPods = $pdo->query($sqlUserPods)->fetchAll(PDO::FETCH_COLUMN)[0];
}

$mypods = "SELECT * FROM podcast_details where user_name = '" . $_SESSION["user_name"] . "';";
$my_podcasts = $pdo->query($mypods)->fetchAll(PDO::FETCH_ASSOC);

// .................... Edit USER INFO  ...................

if ($_SERVER['REQUEST_METHOD'] === "POST") {

  if (isset($_POST['display_name']) && isset($_POST['bio']) && isset($_POST['email']) && isset($_POST['paypal'])) {
    $query = "UPDATE user_detail
                        SET 
                          display_name = '" .  $_POST['display_name'] . "',
                          bio = '" . $_POST['bio'] . "',
                          paypal = '". $_POST['paypal'] ."'
                        WHERE
                          user = '" . $_SESSION['user_name'] . "'
                          
              ";
    $q = $pdo->prepare($query);
    $q->execute();

    $query = "UPDATE reg_data_bank
                        SET user_email = '" .  $_POST['email'] . "' WHERE
                        user_name = '" . $_SESSION['user_name'] . "';";
    
    $q = $pdo->prepare($query);
    $q->execute();
  }
}

// .................... fetch User Data  ...................
$query = " SELECT  user_detail.bio, user_detail.display_name , user_detail.user_img ,user_detail.paypal, user_detail.cover_img , reg_data_bank.user_email, reg_data_bank.isPremium 
                  from user_detail
                    INNER JOIN reg_data_bank
                      ON user_detail.user = reg_data_bank.user_name
                    WHERE
                      user_detail.user ='" . $_SESSION['user_name'] . "'
                    ORDER BY
                      user DESC LIMIT 1
  ";
  
 
$q = $pdo->prepare($query);
$q->execute();
$user = $q->fetch();

// .................... My Podcasts  ...................
$query = " SELECT  *
                  FROM podcast_details
                    WHERE user_id = '" . $_SESSION['user_id'] . "'
                    ORDER BY upload_date DESC
                     ";
$q = $pdo->prepare($query);
$q->execute();
$my_podcasts = $q->fetchAll(PDO::FETCH_ASSOC);

foreach ($my_podcasts as $podcast) {
  $podcast['upload_date'];
}
// .................... End Functions  ...................
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

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> <!-- for j query  -->


  <title>Chhogori | My Account</title>
</head>


<body class="accounts">
  <input type="text" id="isPremium" style="display:none;" hidden value=<?php echo $_SESSION['isPremium'] ?>>
  <input type="text" id="authName" style="display:none;" hidden value=<?php echo $_SESSION["user_name"] ?>>
  <input type="text" id="userPods" style="display:none;" hidden value=<?php echo $sqlPods ?>>

  
  <header class="container-fluid d-flex justify-content-center align-items-center">
  <nav class="navbar navbar_cstm navbar-expand-xl navbar-dark home-head-bg fixed-top">
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

  <div class="container-fluid bg-image pt-5 coverImageInBackground" style="background: linear-gradient(90deg, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.5) 100%),url(<?php echo './images/user_cover_pic/' . $user['cover_img']; ?>); background-repeat: no-repeat; background-size: cover; background-position: center;">
    <div class="container position-relative pt-5">
      <form id="upload_cover_img" name="upload_cover_img" method="post" enctype="multipart/form-data">
        <!-- <input type="text" name="upload_type" value="cover_pic"  hidden id="upload_type"> -->

        <input type="file" name="updateCoverImage" id="updateCoverImage" hidden>
        <label for="updateCoverImage" style="z-index: 1;" class="btn btn-sm text-nowrap btn-light position-absolute top-5 end-0 mt-2 me-2">
          <i class="fas fa-camera"></i> Update Cover Image
        </label>
      </form>
      <!-- <img src="./images/76.jpg" alt="" class="coverImage"> -->
      <div class="d-flex flex-wrap align-items-center justify-content-start py-sm-5 py-3 ps-sm-5">
        <form id="upload_user_image" name="upload_user_image" method="post" enctype="multipart/form-data">
          <!-- <input type="text" name="upload_type" value="profile_pic"  hidden id="upload_type"> -->
          <div class="user-profile-img position-relative">
            <img src="<?php echo './images/user_profile/' . $user['user_img']; ?>" class="img-fluid rounded-circle w-sm-50" id="profileImage" alt="Place Holder" />
            <input type="file" name="updateProfileImage" hidden id="updateProfileImage">
            <label for="updateProfileImage" class="btn btn-sm text-nowrap btn-light position-absolute bottom-0 start-50 translate-middle-x">
              <i class="fas fa-camera"></i> Update Profile Image
            </label>
          </div>
        </form>
        <div class="user-details-top px-3 py-3">
          <h3 class="h3 text-nowrap text-white" style="font-weight: bolder; margin-bottom: 0;"><?php 
                                    if($user['display_name']){
                                        echo $user['display_name'];
                                    }
                                    else{
                                        echo $user_data['user_name'];
                                    }
                                ?></h3>
          <h4 class="text-nowrap text-white" style="font-weight: bolder; font-size: 0.9rem;"><?php echo $user['user_email']; ?></h4>
          <p class="text-nowrap text-white opacity-75"><?php echo $user['bio']; ?></p>
        </div>
      </div>
    </div>
  </div>

  <main class="container-fluid player-page_body">
    <div class="container">
      <div class="d-flex justify-content-center">
      <div class="col-md-6 pt-3 pb-3">
      <form method="post" class="d-flex flex-column gap-3">
        <div class="form-group cstm-form-group">
          <label for="editDisplayName">Display Name <span class="text-danger">*</span></label>
          <input type="text" value="<?php 
                                    if($user['display_name']){
                                        echo $user['display_name'];
                                    }
                                    else{
                                        echo $user_data['user_name'];
                                    }
                                ?>" class="form-control" name='display_name' required id="editDisplayName" placeholder="Enter Display Name">
          <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
        </div>

        <div class="form-group cstm-form-group">
          <label for="editemail">Email <span class="text-danger">*</span></label>
          <input type="email" value="<?= $user['user_email']; ?>" class="form-control" name='email' required id="editemail" placeholder="Enter Email">
          <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
        </div>

        <div class="form-group cstm-form-group">
          <label for="editpaypal">PayPal <span class="text-danger">*</span></label>
          <input type="text" value="<?= $user['paypal']; ?>" class="form-control" name='paypal' required id="editpaypal" placeholder="Enter PayPal account">
          <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
        </div>

        <div class="form-group cstm-form-group">
          <label for="editBio">Bio <span class="text-danger">*</span></label>
          <textarea type="text" class="form-control" name='bio' required id="editDisplayName" style="height:7rem" placeholder="Enter Bio"><?= $user['bio'];?></textarea>
          <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
        </div>

        <div class="form-group cstm-form-group">
            
          <label for="account_type"><?php  if($user['isPremium'] == 'Yes'){ echo 'Paid Account';} else{ ?>Free Account ( <a href="./sign-up-step-01.php">Upgrade Account</a> ) <?php } ?></label>
        </div>

        <button type="submit" class="btn btn-sm pink-bg text-white align-self-end">Done Editing</button>
      </form>
      </div>
      </div>
    </div>
  </main>
  <footer class="container-fluid bg-dark pb-5">
    <div class="container p-5 px-3 px-md-5">
      <div class="px-0 px-md-5 pb-5">
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

  <div class="EditModal">
    <div class="overlay"></div>
    <div class="actualModal p-4">
      
    </div>
  </div>




  <!-- Option 1: Bootstrap Bundle with Popper -->






  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


  <!-- jQuery CDN -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <!-- slick slider int link -->
  <script src="./js/slick.min.js"></script>

  <!-- Slick slider initi script -->
  <script src="./js/slider.js"></script>
  <!-- header color change on scroll -->
  <script src="./js/header.js"></script>
  <script src="./js/myAccount.js"> </script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->

  <script>
    const popupOverlay = document.querySelector('.popup-overlay');

    var audioIntervalGlobal;
    var isPodActive = false;
    var startTime;
    let audio_id;



    ////image click function start
    $('.imgPod').click(function() {

      var authorNamePHP = $("#authName").val();

      var isPremium = $("#isPremium").val();
      var freeTimeUser = $("#freeTime").val();

      var advPay = $(this).prevAll('a').first();
      var advNew = advPay.prevAll('a').first();
      var advPayment = advNew.prevAll('a').first().attr("href"); //additional payment info

      var charityOption = advNew.prevAll('a').first();
      var chrPayment = charityOption.prevAll('a').first().attr("href"); //charity info

      var audioSource = $(this).prevAll('a').first().attr("href");

      var imageSource = $(this).prevAll('a').first();
      var imageLink = imageSource.prevAll('a').first().attr("href");

      var authNameNew = charityOption.prevAll('a').first();
      var authName = authNameNew.prevAll('a').first().attr("href"); //author name

      var podTitleNew = authNameNew.prevAll('a').first();
      var podTitle = podTitleNew.prevAll('a').first().attr("href").replace(/_/g, ' '); //author name

      var audio = $("#podcast-audio");

      var userPodcasts = $("#userPods").val();

      var audioSourceImage = audioSource;

      $.ajax({
        url: "./home-page.php",
        method: "POST",
        data: {
          audioSourceImage
        },
        success: function(data) {
          console.log(data);
        },
        error: function(xhr, status, error) {
          console.error(xhr);
        },
      });

      audioIntervalGlobal = audioSource;

      var link = "./player.php?id=" + audioSource;

      var currentPod = audioSource.substring(
        audioSource.lastIndexOf("\\") + 1,
        audioSource.lastIndexOf("."));

      if (userPodcasts.indexOf(currentPod) >= 0) {

        window.location.href = link;

      } else {
        if (authName == authorNamePHP) {
          window.location.href = link;
        } else {


          if (isPremium == "No") //this is free user
          {

            if (freeTimeUser <= 7200) { // user with free time
              if (advPayment == 'advNone' && chrPayment == 'chrNone') {

                window.location.href = link;

              } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
                $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
                $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
                $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
                $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup10}`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
                $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
                $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
                $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
                $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
              }
            } else if (freeTimeUser > 7200) { // user with no free time
              if (advPayment == 'advNone' && chrPayment == 'chrNone') {
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupNN`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
                $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
                $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
                $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
                $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup10`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
                $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
                $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
                $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
                $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
              }
            }

          } else if (isPremium == "Yes") // premium user
          {

            if (advPayment == 'advNone' && chrPayment == 'chrNone') {

              window.location.href = link;

            } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
              $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
              $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
              $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
              $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup10`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
              $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
              $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
              $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
              $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
            }

          }

        }
      }



    });

    ////click function start
    $('.clickPod').click(function() {
      // console.log("ye wala this",$(this).parent().parent().prevAll('a').first() );

      var authorNamePHP = $("#authName").val();
      var isPremium = $("#isPremium").val();
      var freeTimeUser = $("#freeTime").val();

      var advPay = $(this).parent().prevAll('a').first();
      var advNew = advPay.prevAll('a').first();
      var advPayment = advNew.prevAll('a').first().attr("href"); //additional payment info

      var charityOption = advNew.prevAll('a').first();
      var chrPayment = charityOption.prevAll('a').first().attr("href"); //charity info

      var audioSource = $(this).parent().prevAll('a').attr("href");
      console.log(audioSource)
      var imageSource = $(this).parent().prevAll('a').first();
      var imageLink = imageSource.prevAll('a').first().attr("href");

      var authNameNew = charityOption.prevAll('a').first();
      var authName = authNameNew.prevAll('a').first().attr("href"); //author name

      var podTitleNew = authNameNew.prevAll('a').first();
      var podTitle = podTitleNew.prevAll('a').first().attr("href").replace(/_/g, ' '); //author name

      var audio = $("#podcast-audio");

      var userPodcasts = $("#userPods").val();

      //audioSource: audioSource.substring(audioSource.lastIndexOf('\\') + 1)

      $.ajax({
        url: "./home-page.php",
        method: "POST",
        data: {
          audioSource: audioSource
        },
        success: function(data) {
          console.log(data);
        },
        error: function(xhr, status, error) {
          console.error(xhr);
        },
      });

      audioIntervalGlobal = audioSource;
      //setInterval(audioAirtime, 20000);
      // setInterval(someFunc, 5000);

      var currentPod = audioSource.substring(
        audioSource.lastIndexOf("\\") + 1,
        audioSource.lastIndexOf("."));

      if (userPodcasts.indexOf(currentPod) >= 0) {
        $("#podcast-source").attr("src", audioSource);
        audio[0].pause();
        audio[0].load();
        audio[0].oncanplaythrough = audio[0].play();
        $("#podcast_image").attr("src", imageLink);
        $("#podcastTitle_Player").html(podTitle);
        $("#podcastAuthor_Player").html(authName);

      } else {
        if (authName == authorNamePHP) {
          $("#podcast-source").attr("src", audioSource);
          audio[0].pause();
          audio[0].load();
          audio[0].oncanplaythrough = audio[0].play();
          $("#podcast_image").attr("src", imageLink);
          $("#podcastTitle_Player").html(podTitle);
          $("#podcastAuthor_Player").html(authName);
        } else {


          if (isPremium == "No") //this is free user
          {

            if (freeTimeUser <= 7200) { // user with free time
              if (advPayment == 'advNone' && chrPayment == 'chrNone') {

                $("#podcast-source").attr("src", audioSource);
                audio[0].pause();
                audio[0].load();
                audio[0].oncanplaythrough = audio[0].play();
                $("#podcast_image").attr("src", imageLink);
                $("#podcastTitle_Player").html(podTitle);
                $("#podcastAuthor_Player").html(authName);

              } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
                $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
                $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
                $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
                $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup10}`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
                $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
                $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
                $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
                $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
              }
            } else if (freeTimeUser > 7200) { // user with no free time
              if (advPayment == 'advNone' && chrPayment == 'chrNone') {
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupNN`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
                $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
                $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
                $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
                $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup10`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
                $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
                $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
                $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
                $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
              }
            }

          } else if (isPremium == "Yes") // premium user
          {

            if (advPayment == 'advNone' && chrPayment == 'chrNone') {

              $("#podcast-source").attr("src", audioSource);
              audio[0].pause();
              audio[0].load();
              audio[0].oncanplaythrough = audio[0].play();
              $("#podcast_image").attr("src", imageLink);
              $("#podcastTitle_Player").html(podTitle);
              $("#podcastAuthor_Player").html(authName);


            } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
              $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
              $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
              $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
              $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup10`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
              $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
              $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
              $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
              $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
            }

          }

        }
      }



    });

    $('.clickAddToPlayList').click(function() {


      audio_id = $(this).parent().parent().prevAll('#audio_id').first().attr("href")
      document.querySelectorAll(`.popup-overlay, .popups-main, .popup_addToPlayList`).forEach(each => each.classList.add('active'));
    })

    $('[name="pod_add_playlsit"]').on('submit', (e) => {
      e.preventDefault();
      // alert($('#playList').val())
      // alert(audio_id_for_add_playlist);
      $.ajax({
        url: "./callbacks/ajax_calls.php",
        method: "POST",
        data: {
          audio_id: audio_id,
          playlist_id: $('#playList').val()
        },
        success: function(data) {
          // 
          document.querySelectorAll(`.popup-overlay, .popups-main, .popup_addToPlayList`).forEach(each => each.classList.remove('active'));
          // 
          console.log(data);
        },
        error: function(xhr, status, error) {
          console.error(xhr);
        },
      });

    })

    $('.clickAddToFavrt').click(function() {
      audio_id = $(this).parent().prevAll('#audio_id').first().attr("href")
      console.log(audio_id)
      $.ajax({
        url: "./callbacks/ajax_calls.php",
        method: "POST",
        data: {
          audio_id: audio_id
        },
        success: function(data) {
          console.log(data);
          $('.downAlert').removeClass('danger')
          $('.downAlert').addClass('success')
          $('.downAlert').addClass('active')
          $('.downAlert p').html('add to favorite songs')
          // $('.likeing-button span').html('Liked');
          $(`.likeing-button i#heartId_${audio_id}`).removeClass('fa-solid');
          $(`.likeing-button i#heartId_${audio_id}`).removeClass('fa-regular');
          $(`.likeing-button i#heartId_${audio_id}`).addClass('fa-solid');
          setTimeout(() => {
            // $('.downAlert').removeClass('success')
            $('.downAlert').removeClass('active')
          }, 2000)
        },
        error: function(err, status) {
          // console.error(xhr);
          $('.downAlert').removeClass('success')
          $('.downAlert').addClass('danger')
          $('.downAlert').addClass('active')
          $('.downAlert p').html('reomve to favorite songs')
          // $('.likeing-button span').html('Like');
          $(`.likeing-button i#heartId_${audio_id}`).removeClass('fa-solid');
          $(`.likeing-button i#heartId_${audio_id}`).removeClass('fa-regular');
          $(`.likeing-button i#heartId_${audio_id}`).addClass('fa-regular');
          setTimeout(() => {
            $('.downAlert').removeClass('active')
          }, 2000)
        },
      });
    })
    ////click function end

    function audioAirtime() {
      //alert("Hello World!");

      $.ajax({
        url: "./home-page.php",
        method: "POST",
        data: {
          audioIntervalGlobal: audioIntervalGlobal.substring(audioIntervalGlobal.lastIndexOf('\\') + 1)
        },
        success: function(data) {
          console.log(data);
        },
        error: function(xhr, status, error) {
          console.error(xhr);
        },
      });

      //alert("End World!");
    }

    $('body').keydown(function(e) {
      if (e.keyCode == 32) {
        e.preventDefault();
        // user has pressed space
        var audio = $("#podcast-audio");
        if (audio[0].paused) {
          audio[0].play();
        } else {
          audio[0].pause();
        }

      }
    });
  </script>
</body>

</html>