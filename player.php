<?php

session_start();
$_SESSION;

include("Config.php");
include("functions.php");


$user_data = check_login($pdo);


$sqlUserID = "SELECT user_id FROM reg_data_bank where user_name = '" . $_SESSION['user_name'] . "'";
$userID = $pdo->query($sqlUserID)->fetchAll(PDO::FETCH_COLUMN)[0];

$sqlUserPremium = "SELECT isPremium FROM reg_data_bank where user_name = '" . $_SESSION['user_name'] . "'";
$userPremium = $pdo->query($sqlUserPremium)->fetchAll(PDO::FETCH_COLUMN)[0];

$sqlUserPods = "SELECT podcast_payment FROM user_podcast_access where user_name = '" . $_SESSION['user_name'] . "'";
$sqlPods = '-';
if ($pdo->query($sqlUserPods)->fetchAll(PDO::FETCH_COLUMN)) {
  $sqlPods = $pdo->query($sqlUserPods)->fetchAll(PDO::FETCH_COLUMN)[0];
}

$_SESSION['user_id'] = $userID;
$_SESSION['isPremium'] = $userPremium;

// show all playlists
$my_play_list = $pdo->query("SELECT * FROM playlists WHERE author ='" . $_SESSION['user_id'] . "'");
$my_play_list->execute();
$my_play_list = $my_play_list->fetchAll(PDO::FETCH_ASSOC);

$sql = $pdo->query("SELECT * FROM podcast_details where replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $_GET["id"])) . "';");
$podData = $sql->fetch(PDO::FETCH_ASSOC);

// var_dump($podData['pID']);
// die;

$sqlTracks = $pdo->query("SELECT * FROM podcast_details where user_name = '" . $podData["user_name"] . "' LIMIT 10;");



function get_string_between($string, $start, $end)
{
  $string = ' ' . $string;
  $ini = strpos($string, $start);
  if ($ini == 0) return '';
  $ini += strlen($start);
  $len = strpos($string, $end, $ini) - $ini;
  return substr($string, $ini, $len);
}

$query = "SELECT * FROM fvrt_audio WHERE user='" . $_SESSION['user_id'] . "' AND podcast= '" . $podData['pID'] . "' ";
$sql = $pdo->prepare($query);
$sql->execute();
$audio_status = $sql->fetch(PDO::FETCH_ASSOC);

// var_dump($audio_status); die;

if ($_SERVER['REQUEST_METHOD'] === "POST") {


  if (isset($_POST["audioSource"])) {

    echo "Audio Source Here \n \n";

    $_SESSION["podName"] = $_POST["audioSource"];

    $podTimeTot = 0;

    $podcastAddress = $_POST["audioSource"];

    //get_string_between($podcastAddress, '/', '\\');
    //update play count in the podcast_details table
    try {
      $sql = "UPDATE podcast_details SET play_count=play_count+1 WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $podcastAddress)) . "';";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
    } catch (PDOException $e) {
      echo $e->getMessage();
    }

    //if user is premium/paid, go to the following code. Otherwise, do nothing.
    if ($_SESSION['isPremium'] == "Yes") {
      try {

        //check if user is listening to something.
        $select_stmt = $pdo->prepare("SELECT user_name FROM current_podcast 
                      WHERE user_name=:uname and isActive='true' LIMIT 1"); // sql select query
        $select_stmt->execute(array(
          ':uname' => $_SESSION['user_name']
        )); //execute query
        $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

        if ($select_stmt->rowCount() > 0) {
          //this means that there already exists a podcast that is active
          //
          //get the name of previous podcast from current_podcast
          $sqlPod = "SELECT podcast_address FROM current_podcast where user_name = '" . $_SESSION['user_name'] . "' and isActive='true' LIMIT 1; ";
          $sqlPrevPod = $pdo->query($sqlPod)->fetchAll(PDO::FETCH_COLUMN)[0];
          $sqlPrevPod = basename($sqlPrevPod);
          $sqlPrevPod = "audio/" . $_SESSION['user_name'] . "\\" . $sqlPrevPod;

          //// get previous podcast time from podcast_details
          $select_stmt = $pdo->prepare("SELECT podcast_time FROM podcast_details WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "' LIMIT 1;");
          $select_stmt->execute();
          $podTimeTot = $select_stmt->fetchColumn();

          $minDuration = 0.20 * $podTimeTot;

          //update existing record in current_podcast to inseert endtime
          $sql = "UPDATE current_podcast SET end_time = current_timestamp() WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();

          //update existing record in current podcast to set total listen time
          $sql = "update current_podcast set total_time = IF(TIMESTAMPDIFF(SECOND, start_time,end_time) > pod_time, pod_time, TIMESTAMPDIFF(SECOND, start_time,end_time)) WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();

          //check if the current total time listened is greater than minimum duration and 60 seconds
          $sqlPod = "SELECT total_time FROM current_podcast WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "' LIMIT 1;";
          $curTotalTime = $pdo->query($sqlPod)->fetchAll(PDO::FETCH_COLUMN)[0];

          if ($curTotalTime >= $minDuration && $curTotalTime > 60) {


            //check if user already listened to this podcast
            $select_stmt = $pdo->prepare("SELECT user_name FROM user_listened_pod WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';"); // sql select query
            $select_stmt->execute(); //execute query
            $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

            if ($select_stmt->rowCount() > 0) {
              //check if user already listened to this podcast and its current duration
              $select_stmt = $pdo->prepare("SELECT user_name FROM user_listened_pod WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "' and listen_time < " . $curTotalTime . " LIMIT 1;"); // sql select query
              $select_stmt->execute(); //execute query
              $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

              if ($select_stmt->rowCount() > 0) {

                //update the record from current podcast to user_listened_podcast
                $sql = "UPDATE user_listened_pod SET listen_time = " . $curTotalTime . " WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                //update the listen_time in podcast_details
                $sql = "UPDATE podcast_details SET podcast_airtime = podcast_airtime + " . $curTotalTime . " WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                //// get podcast time from podcast_details
                $select_stmt = $pdo->prepare("SELECT podcast_time FROM podcast_details WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $podcastAddress)) . "' LIMIT 1;");
                $select_stmt->execute();
                $podTimeTot = $select_stmt->fetchColumn();

                //update the current podcast to set data for new clicked podcast
                $sql = "UPDATE current_podcast SET isActive='true', curr_time = '0', podcast_address = '" . str_replace("\\", "\\\\", $podcastAddress) . "', start_time = current_timestamp(), end_time = current_timestamp(), total_time = '0',pod_time = '" . $podTimeTot . "' WHERE user_name = '" . $_SESSION['user_name'] . "';";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
              } else {

                //// get podcast time from podcast_details
                $select_stmt = $pdo->prepare("SELECT podcast_time FROM podcast_details WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $podcastAddress)) . "' LIMIT 1;");
                $select_stmt->execute();
                $podTimeTot = $select_stmt->fetchColumn();

                //update the current podcast to set data for new clicked podcast
                $sql = "UPDATE current_podcast SET isActive='true', curr_time = '0', podcast_address = '" . str_replace("\\", "\\\\", $podcastAddress) . "', start_time = current_timestamp(), end_time = current_timestamp(), total_time = '0',pod_time = '" . $podTimeTot . "' WHERE user_name = '" . $_SESSION['user_name'] . "';";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
              }
            } else {
              //move the record from current podcast to user_listened_podcast
              $sql = "insert into user_listened_pod select user_name, podcast_address, pod_time, total_time from current_podcast WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';";
              $stmt = $pdo->prepare($sql);
              $stmt->execute();

              //update the listen_time in podcast_details
              $sql = "UPDATE podcast_details SET podcast_airtime = podcast_airtime + " . $curTotalTime . " WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';";
              $stmt = $pdo->prepare($sql);
              $stmt->execute();

              //// get podcast time from podcast_details
              $select_stmt = $pdo->prepare("SELECT podcast_time FROM podcast_details WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $podcastAddress)) . "' LIMIT 1;");
              $select_stmt->execute();
              $podTimeTot = $select_stmt->fetchColumn();

              //update the current podcast to set data for new clicked podcast
              $sql = "UPDATE current_podcast SET isActive='true', curr_time = '0', podcast_address = '" . str_replace("\\", "\\\\", $podcastAddress) . "', start_time = current_timestamp(), end_time = current_timestamp(), total_time = '0',pod_time = '" . $podTimeTot . "' WHERE user_name = '" . $_SESSION['user_name'] . "';";
              $stmt = $pdo->prepare($sql);
              $stmt->execute();
            }
          } else {
            //// get podcast time from podcast_details
            $select_stmt = $pdo->prepare("SELECT podcast_time FROM podcast_details WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $podcastAddress)) . "' LIMIT 1;");
            $select_stmt->execute();
            $podTimeTot = $select_stmt->fetchColumn();

            //update the current podcast to set data for new clicked podcast
            $sql = "UPDATE current_podcast SET isActive='true', curr_time = '0', podcast_address = '" . str_replace("\\", "\\\\", $podcastAddress) . "', start_time = current_timestamp(), end_time = current_timestamp(), total_time = '0',pod_time = '" . $podTimeTot . "' WHERE user_name = '" . $_SESSION['user_name'] . "';";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
          }
        } else {

          //// get podcast time from podcast_details
          $select_stmt = $pdo->prepare("SELECT podcast_time FROM podcast_details WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $podcastAddress)) . "' LIMIT 1;");
          $select_stmt->execute();
          $podTimeTot = $select_stmt->fetchColumn();

          $sql = "INSERT into current_podcast(user_name, podcast_address, isActive, pod_time, curr_time,start_time, end_time, total_time) values('" . $_SESSION['user_name'] . "','" . str_replace('\\', '\\\\', $podcastAddress) . "','true'," . $podTimeTot . ", '0', current_timestamp(), current_timestamp(), '0');";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
        }
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
    }
  }



  if (isset($_POST["audioSourceImage"])) {

    $_SESSION["podName"] = $_POST["audioSourceImage"];
  }


  if (isset($_POST['audio_id'])) {
    $query = "SELECT * FROM fvrt_audio WHERE user='" . $_SESSION['user_id'] . "' AND podcast= '" . $_POST['audio_id'] . "' ";
    $sql = $pdo->prepare($query);
    $sql->execute();
    $audio_status = $sql->fetch(PDO::FETCH_ASSOC);

    if (@!$audio_status) {
      $query = "INSERT into fvrt_audio(user, podcast) values('" . $_SESSION['user_id'] . "','" . $_POST['audio_id'] . "');";
      $sql = $pdo->prepare($query);
      $sql->execute();

      $query = "UPDATE podcast_details
                  SET podcast_likes = podcast_likes + 1
                    WHERE pID = '" . $_POST['audio_id'] . "'
                  
                  ";
      $sql = $pdo->prepare($query);
      $sql->execute();
      echo "add in to fvrt";
      
    }
    if ($audio_status) {
      header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
      $query = "DELETE FROM fvrt_audio WHERE user= '" . $_SESSION['user_id'] . "' AND podcast= '" . $_POST['audio_id'] . "' ";
      $sql = $pdo->prepare($query);
      $sql->execute();

      $query = "UPDATE podcast_details
      SET podcast_likes = podcast_likes - 1
        WHERE pID = '" . $_POST['audio_id'] . "'
        ";
      $sql = $pdo->prepare($query);
      $sql->execute();
      echo "remove to fvrt";
      
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

  <link rel="stylesheet" href="./Styles/styles.css" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Chhogori | <?= $podData["podcast_title"] ?></title>

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
              <div class="input-group flex-nowrap nav-search_cstm">
                <input type="search" class="form-control bg-transparent" placeholder="Search..." aria-label="search" aria-describedby="search" />
                <span class="input-group-text bg-transparent" id="basic-addon1"><i class="text-white fas fa-search"></i></span>
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
  <div class="container-fluid bg-image player-head">
    <!-- card back image start -->
    <img class="p-back-cover" src=<?php echo $podData["image_address"]; ?> alt="">
    <div class="d-flex imgClass">
      <!-- Card Image start -->
      <img class="p-cover" src=<?php echo $podData["image_address"]; ?> alt="">
      <div class="text">
        <!-- song title start -->
        <h2 class="song-heading text-center text-white" id="podTit"><?php echo $podData["podcast_title"]; ?></h2>
        <!-- song writer name start -->
        <p class="song-writer text-white fs-2 text-capitalize" id="podAuth">
          <?php echo "By: " . ucfirst($podData["user_name"]); ?>
        </p>
      </div>
    </div>
  </div>
  <main class="container-fluid player-page_body">
    <div class="container flex-row">
      <div class="account-name mt-sm-5 mt-3 d-flex flex-wrap justify-content-start align-items-center">

        <!-- song title start -->
        <h2 class="h2 d-inline text-nowrap"><?php echo $podData["podcast_title"]; ?></h2>
        <!-- song writer name start -->
        <p class="d-inline text-wrap mb-0 ms-sm-2"><?php echo "By: " . ucfirst($podData["user_name"]); ?></p>
      </div>
      <hr class="mb-0" />
      <div class="row">
        <div class="py-2 comments-col">
          <!-- song buttons -->
          <div class="buttons">
            <div class="l-btns">

              <button onclick="add_fvrt(this.id)" class="btn btn-sm likeing-button" id="<?= $podData['pID']; ?>"><i class="<?php if ($audio_status) { ?> fa-solid <?php } else { ?> fa-regular <?php } ?>  fa-heart"></i> <span><?php if ($audio_status) { ?>Liked<?php } else { ?> Like <?php } ?></span> </button>

              <!-- <button class="btn btn-sm ButtonlikeButton" ><i class="fas fa-heart"></i> Like</button> -->
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
              <?php
              while ($row = $sqlTracks->fetch(PDO::FETCH_NUM)) {
              ?>
                <div class="side-col_single">
                  <img src=<?php echo $row[5]; ?> alt="">
                  <div class="text">
                    <h4><?php echo ucfirst($row[6]); ?></h4>
                    <p><?php echo ($row[7]); ?></p>
                    <div class="btns d-flex gap-2 flex-wap align-self-end">
                      <a class="audio_id" id="audio_id" name="audio_id" style="display:none;" href="<?= $row[0] ?>"></a>
                      <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $row[6]) ?>></a>
                      <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $row[2]) ?>></a>
                      <a id="isCharity" name="charity" style="display:none;" href=<?php echo $row[16] ?>></a>
                      <a id="isPayment" name="payment" style="display:none;" href=<?php echo $row[15] ?>></a>
                      <a id="source-image" name="source-image" style="display:none;" href=<?php echo $row[5] ?>></a>
                      <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $row[4] ?>></a>
                      <button class="btn btn-sm pink-bg text-white clickPod">Play</button>
                      <!-- <button class="btn btn-sm pink-bg text-white">
                        Add to List
                      </button> -->

                      <button class="btn btn-sm pink-bg text-white addToPlayListInformation">
                        <i class="clickAddToPlayList">Add to List</i>
                      </button>
                     
                      
                    </div>
                  </div>
                </div>
              <?php } ?>
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
  <div class="downAlert">
    <p>The Pod Cast was liked</p>
  </div>
  <div class="audio-player fixed-bottom container-fluid">
    <div class="container d-flex flex-wrap">
      <div class="actual-player">
      <audio id="podcast-audio" controls autoplay>
            <source id="podcast-source" src="./audio/dam.mp3" type="audio/mpeg">
            Your browser does not support the audio element.
          </audio>

          <!-- http://localhost:3000/audio/dam.mp3 -->


          <script>
            window.onload = function currentPlayFunction() {
              var nowAudioSrc = sessionStorage.getItem("songAdd");
              var nowAudioDuration = sessionStorage.getItem("songdur");
              var nowImage = sessionStorage.getItem("songPic");
              var nowTitle = sessionStorage.getItem("songTitle");
              var nowAuthor = sessionStorage.getItem("songAuthor");

              if (nowAudioSrc.split('/')[4] === "dam.mp3") {
                // alert("No Song Playing");
              } else {
                // alert(nowAudioSrc + " - " + nowAudioDuration + " - " + nowImage + " - " + nowTitle + " - " + nowAuthor);

                //setting currently playing audio
                $("#podcast_image").attr("src", nowImage);
                $("#podcastTitle_Player").html(nowTitle);
                $("#podcastAuthor_Player").html(nowAuthor);

                var audio = $("#podcast-audio");
                $("#podcast-source").attr("src", nowAudioSrc);
                audio[0].pause();
                audio[0].load();
                audio[0].oncanplaythrough = audio[0].currentTime = nowAudioDuration;
                audio[0].oncanplaythrough = audio[0].play();

              }
            }

            setInterval(() => {
              var song = document.getElementsByTagName('audio')[0];
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

              // console.log(document.getElementById("podcast-source").src);
              // console.log(sessionStorage.getItem("songAdd") + " - " + sessionStorage.getItem("songdur"));
              // console.log(sessionStorage.getItem("songPic") + " - " + sessionStorage.getItem("songTitle")+ " - " + sessionStorage.getItem("songAuthor"));

            }, 5000);
          </script>
      </div>
      <div class="det-player">
        <img id="podcast_image" src="./images/song-cover.jpg" alt="" class="image-fluid">
        <div class="text px-2">
          <strong id="podcastTitle_Player">Song Head</strong>
          <p id="podcastAuthor_Player">song writer</p>
        </div>
        <div class="buttons">
          <i class="fas fa-heart"></i>
          <i class="fas fa-user-plus"></i>
        </div>
      </div>
    </div>
  </div>




  <!-- this is popup code start -->
  <div class="popups-main">

      <div class="popup-overlay">
      </div>

      <div class="popup popupNN">
        <p>You have exhausted your free time. Please upgrade to continue.</p>
        <div class="btns">
          <a id="popNN" href="./upgrade-pro.php" class="pink-bg">Access payment page</a>
        </div>
      </div>

      <div class="popup popup00">
        <p>This Podcast Requires Optional Payment and Optional Charity.</p>
        <div class="btns">
          <a id="pop00" href="./addChrPay00.php" class="pink-bg">Access payment page</a>
        </div>
      </div>

      <div class="popup popup01">
        <p>This Podcast Requires Optional Payment and Additional Charity.</p>
        <div class="btns">
          <a id="pop01" href="./addChrPay01.php" class="pink-bg">Access upgrade page</a>
        </div>
      </div>

      <div class="popup popup10">
        <p>This Podcast Requires Additional Payment and Optional Charity.</p>
        <div class="btns">
          <a id="pop10" href="./addChrPay10.php" class="pink-bg">Access upgrade page</a>
        </div>
      </div>

      <div class="popup popup11">
        <p>This Podcast Requires Additional Payment and Additional Charity.</p>
        <div class="btns">
          <a id="pop11" href="./addChrPay11.php" class="pink-bg">Access page for additional payment</a>
        </div>
      </div>

      <div class="popup popupN0">
        <p>This Podcast Requires Optional Charity.</p>
        <div class="btns">
          <a id="popN0" href="./addChrPayN0.php" class="pink-bg">Access page for optional payments</a>
        </div>
      </div>

      <div class="popup popupN1">
        <p>This Podcast Requires Additional Charity.</p>
        <div class="btns">
          <a id="popN1" href="./addChrPayN1.php" class="pink-bg">Access page for optional payments</a>
        </div>
      </div>
      <div class="popup popup0N">
        <p>This Podcast Requires Optional Payment.</p>
        <div class="btns">
          <a id="pop0N" href="./addChrPay0N.php" class="pink-bg">Access page for optional payments</a>
        </div>
      </div>

      <div class="popup popup1N">
        <p>This Podcast Requires Additional Payment.</p>
        <div class="btns">
          <a id="pop1N" href="./addChrPay1N.php" class="pink-bg">Access page for optional payments</a>
        </div>
      </div>



      <!-- -->
      <div class="popup popup_op">
        <p>The author is requesting an additional payment or donation.</p>
        <p>
          This is to support her/his work or a charity of her/his choice.
        </p>
        <div class="btns">
          <a id="pop_new_one" href="./addChrPay1N.php" class="pink-bg">OK, take me to the donation page.</a>
        </div>
      </div>

      <div class="popup popup_add">
        <p>The author is asking you to consider additional support for her/his work or a charity of her/his choice.</p>
        <p>
          Payment is optional.
        </p>
        <div class="btns">
          <a id="pop_new_two_add" href="./addChrPay1N.php" class="pink-bg">OK, take me to the donation page.</a>
        </div>
        <hr />
        <div class="btns">
          <a id="pop_new_two" href="./addChrPay1N.php" class="pink-bg">No, let me listen without any additional donation.</a>
        </div>
      </div>


      <!-- yahan tak -->


      <div class="popup pop_upload">
        <p>Kindly Upgrade to Upload a Message.</p>
        <div class="btns">
          <a id="pop_upload" href="./sign-up-step-01.php" class="pink-bg">Upgrade Now</a>
        </div>
      </div>


      <!-- new upload pop-up -->






      <!-- new upload pop-up -->


      <div class="popup popup_addToPlayList">

        <form name="pod_add_playlsit" class="pod_add_playlsit" action="">
          <div class="popup_addToPlayList_inputDiv">
            <label for="playList">Add to Playlist:</label>
            <select name="playList" id="playList">
              <?php
              foreach ($my_play_list as $playlist) {
              ?>
                <option value="<?= $playlist['ID'] ?>"><?= $playlist['title'] ?></option>
              <?php } ?>
            </select>
            <div class="btns align-self-end">
              <button type="submit" id="addToPlaylistBtn" class="pink-bg text-white btn btn-sm">Submit</button>
            </div>
          </div>
        </form>
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
  $('.clickAddToPlayList').click(function() {

  audio_id = $(this).parent().prevAll('#audio_id').first().attr("href");
  document.querySelectorAll(`.popup-overlay, .popups-main, .popup_addToPlayList`).forEach(each => each.classList.add('active'));
  })
  
  $('[name="pod_add_playlsit"]').on('submit', (e) => {
      e.preventDefault();
      // alert($('#playList').val())
      // alert(audio_id_for_add_playlist);
      alert(audio_id)
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
</script>
  <script>
    function add_fvrt(id) {
      $.ajax({
        url: "player.php",
        method: "POST",
        data: {
          audio_id: id
        },
        success: function(data) {
          console.log(data);
          $('.downAlert').removeClass('danger')
          $('.downAlert').addClass('success')
          $('.downAlert').addClass('active')
          $('.downAlert p').html('add to favorite songs')
          $('.likeing-button span').html('Liked');
          $('.likeing-button i').removeClass('fa-solid');
          $('.likeing-button i').removeClass('fa-regular');
          $('.likeing-button i').addClass('fa-solid');
          // $('.fa-regular.fa-heart')
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
          // $('.likeing-button.active').attr('class').split(/\s+/)
          $('.likeing-button span').html('Like');
          $('.likeing-button i').removeClass('fa-solid');
          $('.likeing-button i').removeClass('fa-regular');
          $('.likeing-button i').addClass('fa-regular');
          setTimeout(() => {
            // $('.downAlert').removeClass('success')
            $('.downAlert').removeClass('active')
          }, 2000)
        },
      });
    }

    function parseURLParams(url) {
      var queryStart = url.indexOf("?") + 1,
        queryEnd = url.indexOf("#") + 1 || url.length + 1,
        query = url.slice(queryStart, queryEnd - 1),
        pairs = query.replace(/\+/g, " ").split("&"),
        parms = {},
        i, n, v, nv;

      if (query === url || query === "") return;

      for (i = 0; i < pairs.length; i++) {
        nv = pairs[i].split("=", 2);
        n = decodeURIComponent(nv[0]);
        v = decodeURIComponent(nv[1]);

        if (!parms.hasOwnProperty(n)) parms[n] = [];
        parms[n].push(nv.length === 2 ? v : null);
      }
      return parms;
    }

    const popupOverlay = document.querySelector('.popup-overlay');

    var audioIntervalGlobal;
    var isPodActive = false;
    var startTime;


    $(document).ready(function() {




      var url_string = window.location;
      var url = new URL(url_string);
      var audioSource = url.searchParams.get("id");

      var audio = $("#podcast-audio");

      $("#podcast-source").attr("src", audioSource);
      audio[0].pause();
      audio[0].load();
      audio[0].oncanplaythrough = audio[0].play();
      $("#podcast_image").attr("src", document.querySelector('.imgClass > img').src);
      $("#podcastTitle_Player").html(document.getElementById('podTit').innerHTML);
      $("#podcastAuthor_Player").html(document.getElementById('podAuth').innerHTML.trim().substring(4));


    });

    popupOverlay.addEventListener('click', () => {
      popupOverlay.classList.remove('active');
      document.querySelectorAll('.popup, .popups-main').forEach(each => each.classList.remove('active'))
    });



    ////click function start
    $('.clickPod').click(function() {


console.dir(document.querySelector('#podcast-audio'));



sessionStorage.removeItem("audio");
sessionStorage.removeItem("fvrt");

// console.log("ye wala this",$(this).prevAll('a').first() );

var authorNamePHP = $("#authName").val();
var isPremium = $("#isPremium").val();
var freeTimeUser = $("#freeTime").val();

if (isPremium == 'Yes') {
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

  audio_id = $(this).prevAll('#audio_id').first().attr("href");

  sessionStorage.setItem("audio", audio_id);
  var fvrt = $(this).prevAll('a').first().attr("href");
  sessionStorage.setItem("fvrt", fvrt);
  if (fvrt) {
    // console.log("fvrt",fvrt)
    $('.fas.fa-heart.clickAddToFavrt').addClass("text-danger")
  } else {
    $('.fas.fa-heart.clickAddToFavrt').removeClass("text-danger")

  }

  var userPodcasts = $("#userPods").val();

  //alert(audioSource);

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
            $("#pop_new_two_add").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
            $("#pop_new_one").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
            $("#pop_new_one").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
            $("#pop_new_one").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op}`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
            $("#pop_new_one").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
            $("#pop_new_two_add").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
            $("#pop_new_two_add").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
            $("#pop_new_one").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
          }
        } else if (freeTimeUser > 7200) { // user with no free time
          if (advPayment == 'advNone' && chrPayment == 'chrNone') {
            document.querySelectorAll(`.popup-overlay, .popups-main, .popupNN`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
            $("#pop_new_two_add").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
            $("#pop_new_one").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
            $("#pop_new_one").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
            $("#pop_new_one").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op}`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
            $("#pop_new_one").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
            $("#pop_new_two_add").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
            $("#pop_new_two_add").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
            $("#pop_new_one").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
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
          $("#pop_new_two_add").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
          $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
          document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
        } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
          $("#pop_new_one").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
          document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
        } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
          $("#pop_new_one").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
          document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
        } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
          $("#pop_new_one").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
          document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op}`).forEach(each => each.classList.add('active'));
        } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
          $("#pop_new_one").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
          document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
        } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
          $("#pop_new_two_add").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
          $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
          document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
        } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
          $("#pop_new_two_add").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
          $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
          document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
        } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
          $("#pop_new_one").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
          document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
        }

      }

    }
  }
} else { //is premium = no meaning free user
  if (freeTimeUser <= 0) { // user with free time
    window.location.href = "./upgrade-pro.php";
  } else {

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

    audio_id = $(this).prevAll('#audio_id').first().attr("href");

    sessionStorage.setItem("audio", audio_id);
    var fvrt = $(this).prevAll('a').first().attr("href");
    sessionStorage.setItem("fvrt", fvrt);
    if (fvrt) {
      // console.log("fvrt",fvrt)
      $('.fas.fa-heart.clickAddToFavrt').addClass("text-danger")
    } else {
      $('.fas.fa-heart.clickAddToFavrt').removeClass("text-danger")

    }

    var userPodcasts = $("#userPods").val();

    //alert(audioSource);

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


        isPremium = "Yes";

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
              $("#pop_new_two_add").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
              $("#pop_new_one").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
              $("#pop_new_one").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
              $("#pop_new_one").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op}`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
              $("#pop_new_one").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
              $("#pop_new_two_add").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
              $("#pop_new_two_add").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
              $("#pop_new_one").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
            }
          } else if (freeTimeUser > 7200) { // user with no free time
            if (advPayment == 'advNone' && chrPayment == 'chrNone') {
              document.querySelectorAll(`.popup-overlay, .popups-main, .popupNN`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
              $("#pop_new_two_add").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
              $("#pop_new_one").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
              $("#pop_new_one").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
              $("#pop_new_one").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op}`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
              $("#pop_new_one").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
              $("#pop_new_two_add").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
              $("#pop_new_two_add").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
              $("#pop_new_one").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
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
            $("#pop_new_two_add").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
            $("#pop_new_one").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
            $("#pop_new_one").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
            $("#pop_new_one").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op}`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
            $("#pop_new_one").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
            $("#pop_new_two_add").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
            $("#pop_new_two_add").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            $("#pop_new_two").attr("href", "./nodonate.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_add`).forEach(each => each.classList.add('active'));
          } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
            $("#pop_new_one").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
            document.querySelectorAll(`.popup-overlay, .popups-main, .popup_op`).forEach(each => each.classList.add('active'));
          }

        }

      }
    }
  }
}





});

    ////click function end

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