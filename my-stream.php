<?php
session_start();
$_SESSION;

include("./Config.php");
include("./functions.php");


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


$sqlTags = "Select podcast_tags from podcast_details";
$sqlTagList = $pdo->query($sqlTags)->fetchAll(PDO::FETCH_ASSOC);
$uniqueTags = '';
foreach ($sqlTagList as $tag) {
  $uniqueTags .= $tag["podcast_tags"] . ",";
}
$newTags = implode(',', array_unique(explode(",", $uniqueTags)));
$uniqTags = explode(',', $newTags);
$uniqTags = array_filter($uniqTags);
sort($uniqTags);


$_SESSION['user_id'] = $userID;
$_SESSION['isPremium'] = $userPremium;

// show all playlists
$my_play_list = $pdo->query("SELECT * FROM playlists WHERE author ='". $_SESSION['user_id'] ."'");
$my_play_list->execute();
$my_play_list = $my_play_list->fetchAll(PDO::FETCH_ASSOC);

$my_fvrt_audio = $pdo->query("SELECT * FROM fvrt_audio WHERE user ='". $_SESSION['user_id'] ."'");
$my_fvrt_audio->execute();
$my_fvrt_audio = $my_fvrt_audio->fetchAll(PDO::FETCH_ASSOC);


// session for audio path
if (isset($_POST["audioSourceImage"])) {

  $_SESSION["podName"] = $_POST["audioSourceImage"];

}

function get_string_between($string, $start, $end)
{
  $string = ' ' . $string;
  $ini = strpos($string, $start);
  if ($ini == 0) return '';
  $ini += strlen($start);
  $len = strpos($string, $end, $ini) - $ini;
  return substr($string, $ini, $len);
}

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
}

//recently listend
// $query = "SELECT *
//               FROM history as hs
//                 INNER JOIN podcast_details as pd
//                   ON
//                   pd.pID = hs.podcast
//                   WHERE
//                   hs.user = '" . $_SESSION['user_id'] . "'

//                   ORDER BY hs.ID DESC LIMIT 25
//                 ";

// $query  = "SELECT * FROM `user_listened_pod` WHERE user_name = '" . $_SESSION['user_name'] . "' and pod_time > listen_time";
 
$query  = "SELECT * FROM `user_listened_pod` WHERE user_name = '" . $_SESSION['user_name'] . "'";

$sql = $pdo->query($query);
$sql->execute();
$recently_listend = $sql->fetchAll(PDO::FETCH_ASSOC);

// fvrt Podcasts
$query = "SELECT *
              FROM fvrt_audio as fa
                INNER JOIN podcast_details as pd
                  ON
                  pd.pID = fa.podcast
                  WHERE
                  fa.user = '" . $_SESSION['user_id'] . "'

                  ORDER BY fa.ID DESC LIMIT 25
                ";
 
$sql = $pdo->query($query);
$sql->execute();
$fvrt_listend = $sql->fetchAll(PDO::FETCH_ASSOC);

//MOST Listened
$query = "SELECT *
              FROM user_listened_pod as ulp
                INNER JOIN podcast_details as pd
                  ON
                  pd.pID = ulp.podcast
                  WHERE
                  ulp.user = '" . $_SESSION['user_id'] . "'

                  ORDER BY ulp.ID DESC LIMIT 25
                ";
 
$sql = $pdo->query($query);
$sql->execute();
$most_listend_podcast = $sql->fetchAll(PDO::FETCH_ASSOC);

// My followed POdcasts
// echo $_SESSION['user_name'];
$query = "SELECT *
              FROM user_follow as uf
                INNER JOIN  podcast_details as pd
                  ON
                  pd.user_name = uf.user_follow
                  WHERE
                  uf.user_name = '" . $_SESSION['user_name'] . "'

                  ORDER BY pd.pID DESC LIMIT 25
                ";
 
$sql = $pdo->query($query);
$sql->execute();
$followed_podcast = $sql->fetchAll(PDO::FETCH_ASSOC);
// var_dump($followed_podcast); die;


// $stmtContinue = $pdo->query("SELECT pd.upload_date,pd.podcast_address,pd.image_address,pd.podcast_title,pd.podcast_desc,pd.podcast_likes, pd.play_count, pd.podcast_time, pd.podcast_add_payment, pd.podcast_charity, pd.user_name, pd.pID FROM podcast_details as pd INNER JOIN user_listened_pod as ud on pd.pID = ud.podcast where ud.user_name = '" . $_SESSION['user_name'] . "' and ud.pod_time > ud.listen_time order by podcast_likes DESC, upload_date DESC");

$stmtContinue = $pdo->query("SELECT pd.upload_date,pd.podcast_address,pd.image_address,pd.podcast_title,pd.podcast_desc,pd.podcast_likes, pd.play_count, pd.podcast_time, pd.podcast_add_payment, pd.podcast_charity, pd.user_name, pd.pID FROM podcast_details as pd INNER JOIN user_listened_pod as ud on pd.pID = ud.podcast where ud.user_name = '" . $_SESSION['user_name'] . "' order by podcast_likes DESC, upload_date DESC");

?>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />

  <link rel="stylesheet" href="./Styles/styles.css" />
  <!-- slick links start-->
  <link rel="stylesheet" href="./Styles/slick.css" />
  <link rel="stylesheet" href="./Styles/slick-theme.css" />
  <!-- slick links end -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Chhogori | Stream</title>
</head>

<body class="home-page">
  <input type="text" id="isPremium" style="display:none;" hidden value=<?php echo $_SESSION['isPremium'] ?>>
  <input type="text" id="authName" style="display:none;" hidden value=<?php echo $_SESSION["user_name"] ?>>
  <input type="text" id="userPods" style="display:none;" hidden value=<?php echo $sqlPods ?>>
  <!-- <input type="text" id="isPremium" style="display:none;" hidden value='No'> -->
  <input type="text" id="freeTime" style="display:none;" hidden value='7300'>
  <header class="container-fluid featured-banner d-flex justify-content-center align-items-center">
    <nav class="navbar navbar_cstm navbar-expand-xl navbar-dark home-head-bg fixed-top">
      <!-- <nav class="navbar navbar_cstm navbar-expand-lg navbar-dark  fixed-top"> -->
      <div class="container">
        <a class="navbar-brand fs-3 fw-bolder" href="./home-page.php"><img class="logo" src="./images/Logo.png" alt="" /></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- <ul class="navbar-nav me-auto mb-2 mb-lg-0 d-flex align-items-center"> -->
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="./home-page.php">Messages</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="./channels.php">Voices</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="./new_and_popular.php">New &amp; Popular</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap active" aria-current="page" href="./my-stream.php">My Stream</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="./my-playlist.php">My Playlist</a>
            </li>
            <li class="nav-item mx-1">
              <div class="input-group  nav-search_cstm">
                <form action="search.php" method="post" class="d-flex flex-nowrap">
                <input type="search" name="search" id="searchFunc"  class="form-control bg-transparent text-white" style="border-right: none; border-top-right-radius: 0; border-bottom-right-radius: 0;" placeholder="Search..." aria-label="search" aria-describedby="search" />
                <button type="submit" class="input-group-text bg-transparent text-white" style=" border-top-left-radius: 0; border-bottom-left-radius: 0;"><span class="" id="basic-addon1"><i class="text-white fas fa-search"></i></span></button>
                </form>
              </div>
            </li>
          </ul>
          <ul class="navbar-nav mb-2 mb-lg-0 ms-md-auto">
            <li class="nav-item mx-1">
              <a class="nav-link pink-bg btn text-white fw-bolder btn-sm" aria-current="page" href="./upload.php">Upload</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link" aria-current="page" href="./notifications.php"><i class="fas fa-bell"></i></a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link" aria-current="page" href="./messages.php"><i class="fas fa-envelope"></i></a>
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
    <div class="container d-flex align-items-center">
      <div class="row w-100 mx-auto">
        <div class="col-md-6 header_text">
          <h1 class="text-white text-center text-md-start">
            Featured <br />
            file
          </h1>
          <p class="text-white text-center text-md-start">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. <br />
            Debitis beatae doloremque mollitia distinctio autem.
          </p>
          <button class="btn btn-outline-light pink-button mx-auto d-block me-md-auto ms-md-0">&nbsp;&nbsp; More Info &nbsp;&nbsp;</button>
        </div>
        <div class="col-md-6 d-none d-md-block"></div>
      </div>
    </div>
  </header>

  <main class="container py-5">
  <form action="filters.php" method="post">
      <div class="row g-3 align-items-center">
        <div class="col-auto">
          <label for="genreFilter" class="col-form-label">Genre:</label>
        </div>
        <div class="col-auto">
          <select name="pod_genre[]" class="form-select" id="genreFilter" aria-label="Default select example">
            <option selected>Choose a Genre</option>
            <option value="Activism, Politics & Political science">Activism, Politics & Political science</option>
            <option value="Arts">Arts</option>
            <option value="Being Human">Being Human</option>
            <option value="Business, Enterprise, Economics, Money">Business, Enterprise, Economics, Money</option>
            <option value="Career & Worklife">Career & Worklife</option>
            <option value="Culture & Entertainment">Culture & Entertainment</option>
            <option value="Discovering & Understanding the World">Discovering & Understanding the World</option>
            <option value="Family">Family</option>
            <option value="Food & Drink, Going Out">Food & Drink, Going Out</option>
            <option value="Global Issues, Future & Prospective">Global Issues, Future & Prospective</option>
            <option value="Health">Health</option>
            <option value="History & Evolution">History & Evolution</option>
            <option value="Home & Garden">Home & Garden</option>
            <option value="Humanities, Studies & Social Science">Humanities, Studies & Social Science</option>
            <option value="Industries, Fields of Activity">Industries, Fields of Activity</option>
            <option value="Insiders Views">Insiders Views</option>
            <option value="Lifestyle">Lifestyle</option>
            <option value="LGBTQIA+">LGBTQIA+</option>
            <option value="Media">Media</option>
            <option value="Military & Defense">Military & Defense</option>
            <option value="Music">Music</option>
            <option value="Nature & Environment">Nature & Environment</option>
            <option value="News, World Affairs & Analysis">News, World Affairs & Analysis</option>
            <option value="Religion & Spirituality">Religion & Spirituality</option>
            <option value="Science & Tech.">Science & Tech.</option>
            <option value="Self Awareness, Inspiration & Improvement">Self Awareness, Inspiration & Improvement</option>
            <option value="Sexuality, Genders">Sexuality, Genders</option>
            <option value="Society">Society</option>
            <option value="Space">Space</option>
            <option value="Misc: Other">Misc: Other</option>
          </select>
        </div>
        <div class="col-auto">
          <label for="tagsFilter" class="col-form-label">Filter Tags:</label>
        </div>
        <div class="col-auto">
          <select name="pod_tags[]" class="form-select" id="tagsFilter" aria-label="Default select example">
            <option selected>Choose a Tag</option>
            <?php
            foreach ($uniqTags as $tag) {
              echo '<option value="' . $tag . '">' . $tag . '</option>';
            }
            ?>

          </select>
        </div>
        <div class="col-auto">
          <label for="listOrder" class="col-form-label">Order By:</label>
        </div>
        <div class="col-auto">
          <select name="order[]" class="form-select" aria-label="Default select example">
            <option selected>Choose an Order</option>
            <option value="A to Z">A to Z</option>
            <option value="Z to A">Z to A</option>
            <option value="Upload Date ASC">Upload Date ASC</option>
            <option value="Upload Date DESC">Upload Date DESC</option>
          </select>
        </div>
        <div class="col-auto">
          <button class="nav-link pink-bg btn text-white fw-bolder btn-sm" type="submit">Filter</button>
        </div>
      </div>
    </form>
    <hr />

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
          <strong id="podcastTitle_Player">Song Head</strong>
          <p id="podcastAuthor_Player">song writer</p>
        </div>
        <div class="buttons">
          <i class="fas fa-heart clickAddToFavrt"></i>
          <!-- <i class="fas fa-user-plus"></i> -->
        </div>
      </div>
    </div>
  </div>

    <div class="row mt-5">
      <div class="col ps-4 pb-3">
        <h3 class="d-inline">Continue Listening</h3>
        <!-- <span class="ps-3 pb-2"><a href="#" class="pink-text">View More</a></span> -->
      </div>
    </div>
    <div class="responsive-table res21 py-2">

      <?php
        foreach($stmtContinue as $continue){
      ?>

        <div class="rounded-bottom card card-1 d-flex position-relative top-0 start-0 flex-column">
          <?php if ($continue["podcast_add_payment"] == "paid") { ?>

            <span class="btn btn-warning btn-sm position-absolute" style="z-index: 10; top: 0.5rem; right:0.5rem">Paid</span>

          <?php } ?>
          <div class="imgContainer">
            <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $continue["podcast_title"]) ?>></a>
            <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $continue["user_name"]) ?>></a>
            <a id="isCharity" name="charity" style="display:none;" href=<?php echo $continue["podcast_charity"] ?>></a>
            <a id="isPayment" name="payment" style="display:none;" href=<?php echo $continue["podcast_add_payment"] ?>></a>
            <a id="source-image" name="source-image" style="display:none;" href=<?php echo $continue["image_address"] ?>></a>
            <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $continue["podcast_address"] ?>></a>
            <img class="imgPod" src=<?php echo $continue["image_address"]; ?> />
            <!-- </a> -->
          </div>
          <div class="content">
          <a class="audio_id" id="audio_id" name="audio_id" style="display:none;" href="<?= $continue["pID"] ?>" ></a>
            <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $continue["podcast_title"]) ?>></a>
            <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $continue["user_name"]) ?>></a>
            <a id="isCharity" name="charity" style="display:none;" href=<?php echo $continue["podcast_charity"] ?>></a>
            <a id="isPayment" name="payment" style="display:none;" href=<?php echo $continue["podcast_add_payment"] ?>></a>
            <a id="source-image" name="source-image" style="display:none;" href=<?php echo $continue["image_address"] ?>></a>
            <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $continue["podcast_address"] ?>></a>

            <h3><?php echo $continue["podcast_title"]; ?></h3>
            <h5><?= $continue["user_name"] ?></h5>
            <p><?= $continue["podcast_desc"] ?></p>
            <div class="icons">
              <button class="playInformation" >
                <i class="fas fa-play clickPod"></i>
                <span><?= $continue["play_count"] ?></span>
              </button>
              <button class="addToPlayListInformation">
                <i class="fas fa-plus clickAddToPlayList"></i>
                
              </button>
                <?php
                  $fvrt = false;
                  foreach($my_fvrt_audio as $myfvrt){
                    if($myfvrt['podcast'] == $continue['pID']){
                      $fvrt = true;
                    }
                  }
                  ?>
              <button class="heartIconCustomMade likeing-button clickAddToFavrt">
                  <i id="heartId_<?= $continue['pID']  ?>" class="<?php if($fvrt){ ?>fa-solid<?php } else{ ?>fa-regular<?php } ?> fa-heart"></i>
                  <span><?= $continue["podcast_likes"] ?></span>
                </button>
            </div>
          </div>
        </div>

      <?php } ?>

    </div>
    <hr />

    <div class="row mt-5">
      <div class="col ps-4 pb-3">
        <h3 class="d-inline">Favorite Podcast</h3>
        <!-- <span class="ps-3 pb-2"><a href="#" class="pink-text">View More</a></span> -->
      </div>
    </div>
    <div class="responsive-table res2 py-2">


      <?php
      foreach($fvrt_listend as $listend) {
        
      ?>

        <div class="rounded-bottom card card-1 d-flex position-relative top-0 start-0 flex-column">
          <?php if ($listend["podcast_add_payment"] == "paid") { ?>

            <span class="btn btn-warning btn-sm position-absolute" style="z-index: 10; top: 0.5rem; right:0.5rem">Paid</span>

          <?php } ?>
          <div class="imgContainer">
            <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $listend["podcast_title"]) ?>></a>
            <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $listend["user_name"]) ?>></a>
            <a id="isCharity" name="charity" style="display:none;" href=<?php echo $listend["podcast_charity"] ?>></a>
            <a id="isPayment" name="payment" style="display:none;" href=<?php echo $listend["podcast_add_payment"] ?>></a>
            <a id="source-image" name="source-image" style="display:none;" href=<?php echo $listend["image_address"] ?>></a>
            <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $listend["podcast_address"] ?>></a>
            <img class="imgPod" src=<?php echo $listend["image_address"]; ?> />
          </div>
          <div class="content">
          <a class="audio_id" id="audio_id" name="audio_id" style="display:none;" href="<?= $listend["pID"] ?>" ></a>
            <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $listend["podcast_title"]) ?>></a>
            <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $listend["user_name"]) ?>></a>
            <a id="isCharity" name="charity" style="display:none;" href=<?php echo $listend["podcast_charity"] ?>></a>
            <a id="isPayment" name="payment" style="display:none;" href=<?php echo $listend["podcast_add_payment"] ?>></a>
            <a id="source-image" name="source-image" style="display:none;" href=<?php echo $listend["image_address"] ?>></a>
            <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $listend["podcast_address"] ?>></a>
            
            <h3><?php echo $listend["podcast_title"]; ?></h3>
            <h5><?= $listend["user_name"] ?></h5>
            <p><?= $listend["podcast_desc"] ?></p>
            <div class="icons">
              <button class="playInformation" >
                <i class="fas fa-play clickPod"></i>
                <span><?= $listend["play_count"] ?></span>
              </button>
              <button class="addToPlayListInformation">
                <i class="fas fa-plus clickAddToPlayList"></i>
                
              </button>
                <?php
                  $fvrt = false;
                  foreach($my_fvrt_audio as $myfvrt){
                    if($myfvrt['podcast'] == $listend['pID']){
                      $fvrt = true;
                    }
                  }
                  ?>
              <button class="heartIconCustomMade likeing-button clickAddToFavrt">
                  <i id="heartId_<?= $listend['pID']  ?>" class="<?php if($fvrt){ ?>fa-solid<?php } else{ ?>fa-regular<?php } ?> fa-heart"></i>
                  <span><?= $listend["podcast_likes"] ?></span>
                </button>
            </div>
          </div>
        </div>


      <?php } ?>


    </div>
    <hr />

    <div class="row mt-5">
      <div class="col ps-4 pb-3">
        <h3 class="d-inline">Most Listened</h3>
        <!-- <span class="ps-3 pb-2"><a href="#" class="pink-text">View More</a></span> -->
      </div>
    </div>
    <div class="responsive-table res23 py-2">

      <?php
      foreach($most_listend_podcast as $podcast){
      ?>

        <div class="rounded-bottom card card-1 d-flex position-relative top-0 start-0 flex-column">
          <?php if ($podcast["podcast_add_payment"] == "paid") { ?>

            <span class="btn btn-warning btn-sm position-absolute" style="z-index: 10; top: 0.5rem; right:0.5rem">Paid</span>

          <?php } ?>
          <div class="imgContainer">
            <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["podcast_title"]) ?>></a>
            <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["user_name"]) ?>></a>
            <a id="isCharity" name="charity" style="display:none;" href=<?php echo $podcast["podcast_charity"] ?>></a>
            <a id="isPayment" name="payment" style="display:none;" href=<?php echo $podcast["podcast_add_payment"] ?>></a>
            <a id="source-image" name="source-image" style="display:none;" href=<?php echo $podcast["image_address"] ?>></a>
            <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $podcast["podcast_address"] ?>></a>
            <img class="imgPod" src=<?php echo $podcast["image_address"]; ?> />
          </div>
          <div class="content">
          <a class="audio_id" id="audio_id" name="audio_id" style="display:none;" href="<?= $podcast["pID"] ?>" ></a>
            <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["podcast_title"]) ?>></a>
            <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["user_name"]) ?>></a>
            <a id="isCharity" name="charity" style="display:none;" href=<?php echo $podcast["podcast_charity"] ?>></a>
            <a id="isPayment" name="payment" style="display:none;" href=<?php echo $podcast["podcast_add_payment"] ?>></a>
            <a id="source-image" name="source-image" style="display:none;" href=<?php echo $podcast["image_address"] ?>></a>
            <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $podcast["podcast_address"] ?>></a>
            
            <h3><?php echo $podcast["podcast_title"]; ?></h3>
            <h5><?= $podcast["user_name"] ?></h5>
            <p><?= $podcast["podcast_desc"] ?></p>
            <div class="icons">
              <button class="playInformation" >
                <i class="fas fa-play clickPod"></i>
                <span><?= $podcast["play_count"] ?></span>
              </button>
              <button class="addToPlayListInformation">
                <i class="fas fa-plus clickAddToPlayList"></i>
                
              </button>
                <?php
                  $fvrt = false;
                  foreach($my_fvrt_audio as $myfvrt){
                    if($myfvrt['podcast'] == $podcast['pID']){
                      $fvrt = true;
                    }
                  }
                  ?>
              <button class="heartIconCustomMade likeing-button clickAddToFavrt">
                  <i id="heartId_<?= $podcast['pID']  ?>" class="<?php if($fvrt){ ?>fa-solid<?php } else{ ?>fa-regular<?php } ?> fa-heart"></i>
                  <span><?= $podcast["podcast_likes"] ?></span>
                </button>
            </div>
          </div>
        </div>


      <?php } ?>

    </div>

    <div class="row mt-5">
      <div class="col ps-4 pb-3">
        <h3 class="d-inline">Recent Messages of Followed Creators</h3>
        <!-- <span class="ps-3 pb-2"><a href="#" class="pink-text">View More</a></span> -->
      </div>
    </div>
    <div class="responsive-table res23 py-2">

      <?php
      foreach($followed_podcast as $podcast){
      ?>

        <div class="rounded-bottom card card-1 d-flex position-relative top-0 start-0 flex-column">
          <?php if ($podcast["podcast_add_payment"] == "paid") { ?>

            <span class="btn btn-warning btn-sm position-absolute" style="z-index: 10; top: 0.5rem; right:0.5rem">Paid</span>

          <?php } ?>
          <div class="imgContainer">
            <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["podcast_title"]) ?>></a>
            <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["user_name"]) ?>></a>
            <a id="isCharity" name="charity" style="display:none;" href=<?php echo $podcast["podcast_charity"] ?>></a>
            <a id="isPayment" name="payment" style="display:none;" href=<?php echo $podcast["podcast_add_payment"] ?>></a>
            <a id="source-image" name="source-image" style="display:none;" href=<?php echo $podcast["image_address"] ?>></a>
            <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $podcast["podcast_address"] ?>></a>
            <img class="imgPod" src=<?php echo $podcast["image_address"]; ?> />
          </div>
          <div class="content">
          <a class="audio_id" id="audio_id" name="audio_id" style="display:none;" href="<?= $podcast["pID"] ?>" ></a>
            <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["podcast_title"]) ?>></a>
            <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["user_name"]) ?>></a>
            <a id="isCharity" name="charity" style="display:none;" href=<?php echo $podcast["podcast_charity"] ?>></a>
            <a id="isPayment" name="payment" style="display:none;" href=<?php echo $podcast["podcast_add_payment"] ?>></a>
            <a id="source-image" name="source-image" style="display:none;" href=<?php echo $podcast["image_address"] ?>></a>
            <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $podcast["podcast_address"] ?>></a>
            
            <h3><?php echo $podcast["podcast_title"]; ?></h3>
            <h5><?= $podcast["user_name"] ?></h5>
            <p><?= $podcast["podcast_desc"] ?></p>
            <div class="icons">
              <button class="playInformation" >
                <i class="fas fa-play clickPod"></i>
                <span><?= $podcast["play_count"] ?></span>
              </button>
              <button class="addToPlayListInformation">
                <i class="fas fa-plus clickAddToPlayList"></i>
                
              </button>
                <?php
                  $fvrt = false;
                  foreach($my_fvrt_audio as $myfvrt){
                    if($myfvrt['podcast'] == $podcast['pID']){
                      $fvrt = true;
                    }
                  }
                  ?>
              <button class="heartIconCustomMade likeing-button clickAddToFavrt">
                  <i id="heartId_<?= $podcast['pID']  ?>" class="<?php if($fvrt){ ?>fa-solid<?php } else{ ?>fa-regular<?php } ?> fa-heart"></i>
                  <span><?= $podcast["podcast_likes"] ?></span>
                </button>
            </div>
          </div>
        </div>


      <?php } ?>

    </div>

    </div>
    <div class="downAlert">
    <p>The Pod Cast was liked</p>
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
<div class="popup popup_addToPlayList">
  
  <form name="pod_add_playlsit" class="pod_add_playlsit" action="">
    <div class="popup_addToPlayList_inputDiv">
      <label for="playList">Add to Playlist:</label>
      <select name="playList" id="playList">
      <?php
        foreach($my_play_list as $playlist) {
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

  <script>
     sessionStorage.removeItem("audio");
      sessionStorage.removeItem("fvrt");
    const popupOverlay = document.querySelector('.popup-overlay');

    var audioIntervalGlobal;
    var isPodActive = false;
    var startTime;
    let audio_id;

    popupOverlay.addEventListener('click', () => {
      popupOverlay.classList.remove('active');
      document.querySelectorAll('.popup, .popups-main').forEach(each => each.classList.remove('active'))
    })

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
      sessionStorage.removeItem("audio");
      sessionStorage.removeItem("fvrt");
      // console.log("ye wala this",$(this).parent().parent().prevAll('a').first() );

      var authorNamePHP = $("#authName").val();
      var isPremium = $("#isPremium").val();
      var freeTimeUser = $("#freeTime").val();

      var advPay = $(this).parent().parent().prevAll('a').first();
      var advNew = advPay.prevAll('a').first();
      var advPayment = advNew.prevAll('a').first().attr("href"); //additional payment info

      var charityOption = advNew.prevAll('a').first();
      var chrPayment = charityOption.prevAll('a').first().attr("href"); //charity info

      var audioSource = $(this).parent().parent().prevAll('a').first().attr("href");

      var imageSource = $(this).parent().parent().prevAll('a').first();
      var imageLink = imageSource.prevAll('a').first().attr("href");

      var authNameNew = charityOption.prevAll('a').first();
      var authName = authNameNew.prevAll('a').first().attr("href"); //author name

      var podTitleNew = authNameNew.prevAll('a').first();
      var podTitle = podTitleNew.prevAll('a').first().attr("href").replace(/_/g, ' '); //author name

      var audio = $("#podcast-audio");

      audio_id = $(this).parent().parent().prevAll('#audio_id').first().attr("href");

      sessionStorage.setItem("audio", audio_id);
        var fvrt = $(this).prevAll('a').first().attr("href");
      sessionStorage.setItem("fvrt", fvrt);
      if(fvrt){
        console.log(fvrt)
      }

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

    $('.clickAddToFavrt').click(function() {
      audio_id = $(this).parent().prevAll('#audio_id').first().attr("href")
      if(!audio_id){
        audio_id = sessionStorage.getItem("audio");
      }
      if(audio_id){
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

      }
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
        if ($("#searchFunc").is(":focus")) {
          //alert("Search")
        } else {
          //alert("not searhc");
          e.preventDefault();
          // user has pressed space
          var audio = $("#podcast-audio");
          if (audio[0].paused) {
            audio[0].play();
          } else {
            audio[0].pause();
          }
        }
      }
    });
  </script>

</body>

</html>
 