<?php

session_start();
$_SESSION;

include("Config.php");
include("functions.php");

$user_data = check_login($pdo);

$sqlUserPods = "SELECT podcast_payment FROM user_podcast_access where user_name = '" . $_SESSION['user_name'] . "'";
$sqlPods = '-';
if ($pdo->query($sqlUserPods)->fetchAll(PDO::FETCH_COLUMN)) {
  $sqlPods = $pdo->query($sqlUserPods)->fetchAll(PDO::FETCH_COLUMN)[0];
}

if ($_SERVER['REQUEST_METHOD'] === "GET"):
  if(isset($_GET['channel'])):

    
      $query = "SELECT * 
                        FROM channels as ch
                          INNER JOIN podcast_details as pd
                            ON
                              pd.podcast_channel = ch.title
                            AND
                              pd.user_ID = ch.author
                          WHERE
                          pd.podcast_channel = '" . $_GET['channel'] . "'
                        ";

      $sql = $pdo->query($query);
      $sql->execute();
      $channel_data = $sql->fetchAll(PDO::FETCH_ASSOC);

      if(!$channel_data){
        $query = "
                  SELECT *
                    FROM channels
                  WHERE
                    
                    title = '" . $_GET['channel'] . "'
        ";
        
        $stmt = $pdo->query($query);
        $stmt->execute();
        $channel = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <!--<title>channel Playing</title>-->
  <title>Chhogori | <?= $channel_data[0]['title'] ?></title>

  <script type='text/javascript'> 
   
</script>
</head>
  <body class="player-page">
  <input type="text" id="isPremium" style="display:none;" hidden value=<?php echo $_SESSION['isPremium'] ?>>
  <input type="text" id="authName" style="display:none;" hidden value=<?php echo $_SESSION["user_name"] ?>>
  <input type="text" id="userPods" style="display:none;" hidden value=<?php echo $sqlPods ?>>
    <header class="container-fluid d-flex justify-content-center align-items-center">
      <nav class="navbar navbar_cstm navbar-expand-xl navbar-dark bg-dark fixed-top">
        <!-- <nav class="navbar navbar_cstm navbar-expand-lg navbar-dark  fixed-top"> -->
        <div class="container">
          <a class="navbar-brand fs-3 fw-bolder" href="home-page.php"><img class="logo" src="./images/Logo.png" alt="" /></a>
          <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
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
                  <a
                    class="nav-link active dropdown-toggle"
                    href="#"
                    role="button"
                    id="dropdownMenuLink"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                  >
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
    <div class="container-fluid bg-image player-head" >
      <?php if($channel_data){ ?>
      <img class="p-back-cover" src="<?= './images/user_channel_pic/'.$channel_data[0]['img'] ?>" alt="">
      <?php } else{ ?>
        <img class="p-back-cover" src="<?= './images/user_channel_pic/'.$channel[0]['img'] ?>" alt="">
        <?php } ?>
      <div class="d-flex flex-column">
        
      <?php if($channel_data){ ?>
        <img class="p-cover" src="<?= './images/user_channel_pic/'.$channel_data[0]['img'] ?>" alt="">
        <?php } else{ ?>
          <img class="p-cover" src="<?= './images/user_channel_pic/'.$channel[0]['img'] ?>" alt="">
          <?php } ?>
        <div class="text">

        <?php if($channel_data){ ?>
          <h2 class="song-heading text-center text-white"><?= $channel_data[0]['title'] ?></h2>
          <?php } else{ ?>
            <h2 class="song-heading text-center text-white"><?= $channel[0]['title'] ?></h2>
            <?php } ?>
          <!-- <p class="song-writer text-white">
            by: Lorem Ipsum
          </p> -->
        </div>
      </div>    
    </div>
    <main class="container-fluid player-page_body">
      <div class="container">
        <div class="account-name mt-sm-5 mt-3 d-flex flex-wrap justify-content-start align-items-center">
        <?php if($channel_data){ ?>
          <h2 class="h2 d-inline text-nowrap"><?= $channel_data[0]['title'] ?></h2>
          <?php } else{ ?>
            <h2 class="h2 d-inline text-nowrap"><?= $channel[0]['title'] ?></h2>
            <?php } ?>
          <!-- <p class="d-inline text-wrap mb-0 ms-sm-2">by Lorem Ipsum</p> -->
        </div>
        <hr class="mb-0"/>   
        <div class="row">
          <div class="py-2 comments-col">
            <div class="buttons">
              <div class="l-btns">
                <button class="btn btn-sm "><i class="fas fa-heart"></i> Like</button>
                <button class="btn btn-sm "><i class="fas fa-retweet"></i> Repost</button>
                <button class="btn btn-sm "><i class="fas fa-share-square"></i> Share</button>
                <button class="btn btn-sm "><i class="far fa-copy"></i> Copy Link</button>
                <button class="btn btn-sm "><i class="fas fa-ellipsis-h"></i> More</button>

              </div>
              <div class="r-btns">
                <button class="mx-2"><i class="fas fa-play me-1"></i> 571k</button>
                <button class="mx-2"><i class="fas fa-heart me-1"></i> 9,777</button>
                <button class="mx-2"><i class="fas fa-retweet me-1"></i> 361</button>
              </div>
            </div>
            <!-- <div class="col-md-3 d-md-flex d-none side-col"> -->
            <div class=" d-flex side-col">
              <div class="side-col_head side-col_head_first py-4">
                <button class="btn pink-bg text-white shadow" type="submit">Donate Now</button>
                <button class="btn pink-bg text-white shadow" type="submit">Become Pro</button>
              </div> 
              <div class="side-col_head p-3">
                <h3>Related Tracks</h3>
                <a href="#">View all</a>
              </div>
              <div class="side-col_body">
                <?php foreach($channel_data as $channel): ?>
                <div class="side-col_single">
                  <img src="<?= $channel['image_address'] ?>" alt="">
                  <div class="text">
                    <h4><?= $channel['podcast_title'] ?></h4>
                    <p><?= $channel['podcast_desc'] ?></p>
                    <div class="btns d-flex gap-2 flex-wap align-self-end">
                      <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $channel["podcast_title"]) ?>></a>
                      <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $channel['user_name']) ?>></a>
                      <a id="isCharity" name="charity" style="display:none;" href=<?php echo $channel["podcast_charity"] ?>></a>
                      <a id="isPayment" name="payment" style="display:none;" href=<?php echo $channel["podcast_add_payment"] ?>></a>
                      <a id="source-image" name="source-image" style="display:none;" href=<?php echo $channel["image_address"] ?>></a>
                      <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $channel["podcast_address"] ?>></a>
                      <button class="btn btn-sm pink-bg text-white clickPod">Play</button>
                      <button class="btn btn-sm pink-bg text-white">Add to List</button>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>  
          </div>
          
          </div>
          <!-- <div class="col-md-3 d-md-flex d-none side-col">
            <div class="side-col_head py-4">
              <button class="btn pink-bg text-white shadow" type="submit">Donate Now</button>
              <button class="btn pink-bg text-white shadow" type="submit">Become Pro</button>
            </div> 
            <div class="side-col_head pt-3">
              <h3>Related Tracks</h3>
              <a href="#">View all</a>
            </div>
            <div class="side-col_body">
              <div class="side-col_single">
                <img src="./images/random-user.webp" alt="">
                <div class="text">
                  <h4>Writer Name</h4>
                  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Est, ipsum?</p>
                </div>
              </div>
              <div class="side-col_single">
                <img src="./images/random-user.webp" alt="">
                <div class="text">
                  <h4>Writer Name</h4>
                  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Est, ipsum?</p>
                </div>
              </div>
              <div class="side-col_single">
                <img src="./images/random-user.webp" alt="">
                <div class="text">
                  <h4>Writer Name</h4>
                  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Est, ipsum?</p>
                </div>
              </div>
              <div class="side-col_single">
                <img src="./images/random-user.webp" alt="">
                <div class="text">
                  <h4>Writer Name</h4>
                  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Est, ipsum?</p>
                </div>
              </div>
              <div class="side-col_single">
                <img src="./images/random-user.webp" alt="">
                <div class="text">
                  <h4>Writer Name</h4>
                  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Est, ipsum?</p>
                </div>
              </div>
              <div class="side-col_single">
                <img src="./images/random-user.webp" alt="">
                <div class="text">
                  <h4>Writer Name</h4>
                  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Est, ipsum?</p>
                </div>
              </div>
              <div class="side-col_single">
                <img src="./images/random-user.webp" alt="">
                <div class="text">
                  <h4>Writer Name</h4>
                  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Est, ipsum?</p>
                </div>
              </div>
              <div class="side-col_single">
                <img src="./images/random-user.webp" alt="">
                <div class="text">
                  <h4>Writer Name</h4>
                  <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Est, ipsum?</p>
                </div>
              </div>
            </div>  
          </div> -->
            
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


  <!-- jQuery CDN -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <!-- slick slider int link -->
  <script src="./js/slick.min.js"></script>

  <!-- Slick slider initi script -->
  <script src="./js/slider.js"></script>
  <!-- header color change on scroll -->
  <script src="./js/header.js"></script>

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

      console.log( $(this).prevAll('a').attr("href"))

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
   

      audio_id =  $(this).parent().parent().prevAll('#audio_id').first().attr("href")
     document.querySelectorAll(`.popup-overlay, .popups-main, .popup_addToPlayList`).forEach(each => each.classList.add('active'));
    })

    $('[name="pod_add_playlsit"]').on('submit', (e)=> {
      e.preventDefault();
      // alert($('#playList').val())
      // alert(audio_id_for_add_playlist);
      $.ajax({
        url: "./callbacks/ajax_calls.php",
        method: "POST",
        data: { audio_id: audio_id, playlist_id: $('#playList').val() },
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

    $('.clickAddToFavrt').click( function(){
      audio_id =  $(this).parent().prevAll('#audio_id').first().attr("href")
      console.log(audio_id)
      $.ajax({
        url: "./callbacks/ajax_calls.php",
        method: "POST",
        data: { audio_id: audio_id},
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
          setTimeout(()=>{
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
          setTimeout(()=>{
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
<?php endif; endif; ?>