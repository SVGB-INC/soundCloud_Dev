<?php
session_start();
$_SESSION;

include("Config.php");
include("functions.php");
include("mp3file.class.php");


$user_data = check_login($pdo);

$authorPP = "select paypal from user_detail where user ='" . $_SESSION["user_name"] . "';";
$authorPayPal = $pdo->query($authorPP)->fetchAll(PDO::FETCH_COLUMN)[0];

if ($authorPayPal === "") {
  $authorPayPal = "NA";
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $mp3file = new MP3File($_SESSION['file_name_audio']);
  $duration1 = $mp3file->getDurationEstimate(); //(faster) for CBR only
  //$duration2 = $mp3file->getDuration();//(slower) for VBR (or CBR)
  //echo "duration: $duration1 seconds"."\n";
  //echo "estimate: $duration2 seconds"."\n";
  //echo MP3File::formatTime($duration2)."\n";

  function cleanSpecialCharacters($string)
  {
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9.-]/', "", $string); // Removes special chars.

  }

  try {

    //$rand = rand(10, 100);
    $streplaceFileName = cleanSpecialCharacters($_FILES['uploaded-image']['name']);
    //$audioFile = $rand . "-" . $streplaceFileName;
    $audioFile = $streplaceFileName;
    $ds = DIRECTORY_SEPARATOR;
    $storeFolder = 'pod_thumbs/' . $_SESSION['user_name'];

    if (is_dir($storeFolder)) {
    } else {
      mkdir('pod_thumbs/' . $_SESSION['user_name']);
    }

    if ((!empty($_FILES)) && !empty($_FILES['uploaded-image']['name'])) {
      $filename = $streplaceFileName;
      $tempFile = $_FILES['uploaded-image']['tmp_name'];
      $targetPath = $storeFolder . $ds;
      $targetFile = $targetPath . $filename;
      $check = move_uploaded_file($tempFile, $targetFile);
      if ($check) {
        //$uploadMSG = "Details Uploaded Successfully!";
        //header("Location: my-podcasts.php");

      }
    }

    try {

      $sqlUserID = "SELECT user_id FROM reg_data_bank where user_name = '" . $_SESSION['user_name'] . "'";
      $userID = $pdo->query($sqlUserID)->fetchAll(PDO::FETCH_COLUMN)[0];

      $date = date("Y-m-d h:i:s");

      $tags = '';

      if (isset($_POST['pod_tags'])) {
        foreach (array_map('strip_tags', $_POST['pod_tags']) as $tagChosen) {
          $tags .= $tagChosen . ',';
        }
      }

      $tagsNew = rtrim(trim($tags), ',');


      //genre 

      $genres = '';

      if (isset($_POST['pod_genre'])) {
        // foreach (array_map('strip_tags', $_POST['pod_genre']) as $tagChosen)
        // {
        //     $genres .= $tagChosen . ',';
        // }
        foreach ($_POST['pod_genre'] as $tagChosen) {
          $genres .= $tagChosen . '|';
        }
      }

      $genresNew = rtrim(trim($genres), '|');



      $channelName = '';

      //echo $_POST['uploadCat'];

      if ($_POST['uploadCat'] == 'TdChanList') {
        $channelName = $_POST['channelList'];
      } else if ($_POST['uploadCat'] == 'Solo') {
        $channelName = 'Solo File';
      } else if ($_POST['uploadCat'] == 'addChannel') {
        $channelName = $_POST['newChannelName'];
      }


      $insert_stmt = $pdo->prepare("INSERT INTO podcast_details(user_ID, user_name, upload_date, podcast_address, image_address, podcast_title, podcast_desc, podcast_tags, podcast_genre, podcast_likes, play_count, podcast_time, podcast_channel, podcast_add_payment, podcast_charity) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"); //sql insert query

      if ($insert_stmt->execute([$userID, $_SESSION['user_name'], $date, $_SESSION['file_name_audio'], $targetFile, $_POST['pod_title'], $_POST['pod_desc'], $tagsNew, $genresNew, '0', '0', $duration1, $channelName, $_POST['additionalPlans'], $_POST['CharityPlans']])) {
        $fileNameNow = substr($_SESSION['file_name_audio'], strrpos($_SESSION['file_name_audio'], '\\'));

        //echo $fileNameNow;

        $pIDSQL = "SELECT pID FROM podcast_details where  replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $_SESSION['file_name_audio'])) . "'";

        //echo $pIDSQL ."\n \n";

        $pID = $pdo->query($pIDSQL)->fetchAll(PDO::FETCH_COLUMN)[0];

        //echo $fileNameNow . "\n \n" . $pID;

        $advAmount = '0';

        $chrAmount = '0';
        $weblink = 'NA';
        $name = 'NA';
        $charity_account = "NA";

        if ($_POST['CharityPlans'] != 'chrNone') {

          if ($_POST['CharityPlans'] == 'chrOpt') {
            $chrAmount = $_POST['chrAmount_Opt'];
            $weblink = $_POST['chrOpt_weblink'];
            $name = $_POST['chrOpt_name'];
            $charity_account = $_POST['chrOpt_account'];
          }

          if ($_POST['CharityPlans'] == 'chrMst') {
            $chrAmount = $_POST['chrAmount_Mst'];
            $weblink = $_POST['chrMst_weblink'];
            $name = $_POST['chrMst_name'];
            $charity_account = $_POST['chrMst_account'];
          }
        }

        if ($_POST['CharityPlans'] == 'chrOpt') {
          $chrAmount = $_POST['chrAmount_Opt'];
          $weblink = $_POST['chrOpt_weblink'];
          $name = $_POST['chrOpt_name'];
          $charity_account = $_POST['chrOpt_account'];
        }

        if ($_POST['CharityPlans'] == 'chrMst') {
          $chrAmount = $_POST['chrAmount_Mst'];
          $weblink = $_POST['chrMst_weblink'];
          $name = $_POST['chrMst_name'];
          $charity_account = $_POST['chrMst_account'];
        }


        if ($_POST['additionalPlans'] != 'advNone') {
          if (isset($_POST['advAmount_Opt'])) {
            $advAmount = $_POST['advAmount_Opt'];
          } else if (isset($_POST['advAmount_Mst']) && $_POST['advAmount_Mst'] != '') {
            $advAmount = $_POST['advAmount_Mst'];
          }
        }

        if ($_POST['additionalPlans'] == 'advOpt') {
          $advAmount = $_POST['advAmount_Opt'];
        }

        if ($_POST['additionalPlans'] == 'advMst') {
          $advAmount = $_POST['advAmount_Mst'];
        }

        if(strlen(trim($chrAmount))<=0){
          $chrAmount = '0';
        }

        if(strlen(trim($advAmount))<=0){
          $advAmount = '0';
        }


        // echo $_POST['CharityPlans'] . " -- " .
        //   $chrAmount . " -- " .
        //   $_POST['additionalPlans'] . " -- " .
        //   $advAmount . " -- " .
        //   $weblink . " -- " .
        //   $name . " -- " .
        //   $charity_account . " -- ";
        // die;


        // var_dump($pID, $advAmount, $chrAmount,$weblink,$name); die;
        $insert_stmt = $pdo->prepare("INSERT INTO podcast_payments(pID,podcast_payment,podcast_charity,website_url,name, charity_acc) VALUES (?,?,?,?,?,?)");

        $insert_stmt->execute([$pID, $advAmount, $chrAmount, $weblink, $name, $charity_account]);

        //echo "INSERT INTO podcast_payments(pID,podcast_payment,podcast_charity,website_url,name) VALUES ($pID, $advAmount, $chrAmount,$weblink,$name)";
        //echo "I am here;";
        $uploadMSG = "Uploaded Successfully"; //execute query success message
        header("Location: my-podcasts.php");
      }
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}

$stmt = $pdo->query("SELECT title FROM channels WHERE author = '" . $_SESSION['user_id'] . "'");
$stmt->execute();
$all_channels = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- select2 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
  <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.min.js"></script>
  <!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->

  <!-- bootstrap cdn -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
  <!-- fontawesome cdn -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />



  <link rel="stylesheet" href="./Styles/styles.css" />
  <title>Chhogori | Upgrade</title>
</head>

<body class="sign-up-page upload-details">
  <div class="bodyclone">

    <header class="justify-content-center align-items-center">
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
      <div class="container mt-5">
        <div class="row mt-5">
          <div class="col-md-2 d-none d-md-block"></div>
          <div class="col-md-8 p-0">
            <form class="form py-5 px-md-4 px-2 px-lg-4 bg-opacity-75" enctype="multipart/form-data" name="podcastdetails" method="post" style="min-height: 33rem;">
              <div class="container-section">
                <h1 class="pb-4">Podcast Details</h1>
                <div class="mb-3">
                  <span>Upload Podcast Image</span>
                  <label for="upload-podcast-image" id="imgUploadLabel">
                    <button type="button">
                      <i class="fas fa-file-upload"></i>
                      Upload Image
                    </button>
                  </label>
                  <input type="file" class="form-control" name="uploaded-image" id="upload-podcast-image" onchange="loadFile(event)" hidden />
                  <img alt="outputImg" id="outputImage" width="100">
                </div>
                <div class="mb-3">
                  <label for="Title" class="form-label">Title</label>
                  <input type="text" class="form-control" name="pod_title" id="Title" placeholder="Title" required>
                </div>
                <div class="mb-3">
                  <label for="ShortDescription" class="form-label">Short Description</label>
                  <textarea class="form-control" name="pod_desc" id="ShortDescription" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-check-label" for="tags">Select Tags:</label><br>
                  <select name="pod_tags[]" id="tags" data-select2-id="tags" class="w-100" multiple>
                    <option value="podcast">Podcast</option>
                  </select>
                </div>
                <!-- <div class="mb-3">
                  <label class="form-check-label" for="selectGenre">Select Genre:</label><br>
                  <select name="pod_genre" id="selectGenre" data-select2-id="genre" class="w-100" multiple>
                    <option value="Random">Random</option>
                    <option value="Technology">Technology</option>
                    <option value="Health">Health</option>
                    <option value="Education">Education</option>
                  </select>
                </div> -->
                <div class="mb-3">
                  <label class="form-check-label" for="selectGenre">
                    Select Genre
                  </label>
                  <select name="pod_genre[]" id="selectGenre" data-select2-id="genre" class="w-100" multiple>
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
                  </select>
                </div>
                <h6 class="h6 mt-3 mb-1">Do you want to upload this podcast as solo file, or in one of your existing channels?</h6>
                <div class="">
                  <input class="form-check-input closerINput" checked type="radio" name="uploadCat" id="soloFile" value="Solo">
                  <label class="form-check-label text-capitalize" for="soloFile">
                    Solo File
                  </label>
                </div>
                <div class="">
                  <input class="form-check-input closerINput" type="radio" name="uploadCat" id="TdChanList" value="TdChanList">
                  <label class="form-check-label text-capitalize" for="TdChanList">
                    Top-down channels list
                  </label>
                  <div class="ms-3 upload-details_additionalOptional_details">
                    <div class="details_single chanListClass">
                      <label for="channelList">Channel:</label>
                      <select name="channelList" class="form-select" id="channelList" aria-label="Default select example">
                        <?php foreach ($all_channels as $channel) { ?>
                          <option value="<?php echo $channel['title'] ?>"><?php echo $channel['title'] ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <h6 class="my-3"><strong>*Note:</strong> You can create a new channel from your profile. <a href="./my-channels.php">Follow the link here.</a></h6>
                </div>
                <!-- <div class="">
                  <input class="form-check-input closerINput" type="radio" name="uploadCat" id="addChannel" hidden value="addChannel">
                  <label class="mt-2 btn btn-sm text-white pink-bg addChannelBtn" for="addChannel">
                    Add New Channel +
                  </label>
                  <div class="ms-3 addChannel-div upload-details_addChannelDiv_details">
                    <div class="details_single chanListClass">
                      <div class="d-flex flex-column input-div">
                        <label for="addChn">Channel Name:</label>
                        <input class="form-control" type="text" id="addChn" name="newChannelName">
                        <button type="button" class="btn mt-2 ms-auto btn-sm pink-bg text-white okChannelBtn">OK</button>
                      </div>
                    </div>
                  </div>
                </div> -->
                <!-- <input type="button" value="Add New Channel +" name="uploadCat" class="mt-2 btn btn-sm text-white pink-bg"> -->
                <!-- <div class="mb-3">
                          <label class="form-check-label" for="selectChannel">
                                  Do you want to upload this podcast as solo file, or in one of your existing channels?
                          </label>
                          <select name="pod_genre" class="form-select" id="selectChannel" aria-label="Default select example">
                              <option value="Solo File" selected>Solo File</option>
                              <option value="Top-down channels list">Top-down channels list</option>
                              <option value="Add new channel">Add new channel</option>
                          </select>
                      </div> -->

                <h5 class="h5 mt-3 mb-1">Payment Plans</h5>
                <!-- <div>
                          <input class="form-check-input" type="radio" name="additionalPlans" id="free" value="free">
                          <label class="form-check-label text-capitalize" for="free">
                              podcast accessible for free to anyone
                          </label>
                      </div>
                      <div>
                          <input class="form-check-input" type="radio" name="additionalPlans" id="paid" value="paid">
                          <label class="form-check-label text-capitalize" for="paid">
                              podcast accessible to paying members only
                          </label>
                      </div> -->
                <h6 class="h6 mt-3 mb-1">Additional Payment To Author</h6>
                <div class="">
                  <input class="form-check-input closerINput" checked type="radio" name="additionalPlans" id="advNone" value="advNone">
                  <label class="form-check-label text-capitalize" for="advNone">
                    None
                  </label>
                </div>
                <div class="">
                  <input class="form-check-input closerINput" type="radio" name="additionalPlans" id="advOpt" value="advOpt">
                  <label class="form-check-label text-capitalize" for="advOpt">
                    Optional
                  </label>
                  <div class="ms-3 upload-details_additionalOptional_details">
                    <div class="details_single input-div">
                      <label for="advOptAmount">Amount:</label>
                      <input class="form-control" type="number" step="0.01" id="advOptAmount" name="advAmount_Opt">
                    </div>

                    <div class="details_single">
                      <span>Account</span>
                      <p><strong><?= $authorPayPal ?></strong></p>
                    </div>
                  </div>
                </div>
                <div class="">
                  <input class="form-check-input closerINput" type="radio" name="additionalPlans" id="advMst" value="advMst">
                  <label class="form-check-label text-capitalize" for="advMst">
                    Must
                  </label>
                  <div class="ms-3 upload-details_additionalOptional_details">
                    <div class="details_single input-div">
                      <label for="advMstAmount">Amount:</label>
                      <input class="form-control" type="number" step="0.01" id="advMstAmount" name="advAmount_Mst">
                    </div>

                    <div class="details_single">
                      <span>Account</span>
                      <p><strong><?= $authorPayPal ?></strong></p>
                    </div>
                  </div>
                </div>

                <!-- charity -->
                <h2 class="h6 mt-3 mb-1">Charity Contribution</h2>
                <div class="">
                  <input class="form-check-input closerINput" type="radio" checked name="CharityPlans" id="chrNone" value="chrNone">
                  <label class="form-check-label text-capitalize" for="chrNone">
                    None
                  </label>
                </div>

                <div class="">
                  <input class="form-check-input closerINput" type="radio" name="CharityPlans" id="chrOpt" value="chrOpt">
                  <label class="form-check-label text-capitalize" for="chrOpt">
                    Optional
                  </label>
                  <div class="ms-3 upload-details_additionalOptional_details">
                    <div class="details_single input-div">
                      <label for="chrOptAmount">Amount:</label>
                      <input class="form-control" type="number" step="0.01" id="chrOptAmount" name="chrAmount_Opt">
                    </div>
                    <div class="details_single input-div">
                      <label for="chrOpt_weblink">web link:</label>
                      <input class="form-control" type="text" id="chrOpt_weblink" name="chrOpt_weblink">
                    </div>
                    <div class="details_single">
                      <div class="details_single input-div">
                        <label for="chrOpt_name">Name:</label>
                        <input class="form-control" name="chrOpt_name" id="chrOpt_name">
                      </div>
                    </div>
                    <div class="details_single">
                      <span>Account</span>
                      <input class="form-control" name="chrOpt_account" id="chrOpt_account">
                    </div>
                  </div>
                </div>

                <div class="">
                  <input class="form-check-input closerINput" type="radio" name="CharityPlans" id="chrMst" value="chrMst">
                  <label class="form-check-label text-capitalize" for="chrMst">
                    Must
                  </label>
                  <div class="ms-3 upload-details_additionalOptional_details">
                    <div class="details_single input-div">
                      <label for="chrMstAmount">Amount:</label>
                      <input class="form-control" type="number" step="0.01" id="chrMstAmount" name="chrAmount_Mst">
                    </div>

                    <div class="details_single input-div">
                      <label for="chrMst_weblink">web link:</label>
                      <input class="form-control" type="text" id="chrMst_weblink" name="chrMst_weblink">
                    </div>

                    <div class="details_single">
                      <div class="details_single input-div">
                        <label for="chrMst_name">Name:</label>
                        <input class="form-control" type="text" id="chrMst_name" name="chrMst_name">
                      </div>
                    </div>

                    <div class="details_single">
                      <span>Account</span>
                      <input class="form-control" name="chrMst_account" id="chrMst_account">
                    </div>
                  </div>
                </div>

                <button type="submit" class="btn mt-3 pink-bg text-white w-100">Submit</button>
                <!-- </form> -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </header>

    <footer class="container-fluid bg-dark">
      <div class="container p-5 px-3 px-md-5">
        <div class="px-0 px-md-5">
          <div class="row">
            <div class="col-12">
              <a href="#" class="link-secondary text-nowrap">Main Link</a>
            </div>
          </div>
          <div class="row my-5">
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-secondary text-nowrap">Hello World</a>
              <a href="#" class="link-secondary text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-secondary text-nowrap">Hello World</a>
              <a href="#" class="link-secondary text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-secondary text-nowrap">Hello World</a>
              <a href="#" class="link-secondary text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-secondary text-nowrap">Hello World</a>
              <a href="#" class="link-secondary text-nowrap">Hello World</a>
            </div>
          </div>
        </div>
      </div>
    </footer>

  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

  <script src="./js/tagsInput.js"></script>
  <script>
    function loadFile(event) {
      var image = document.getElementById('outputImage');
      image.src = URL.createObjectURL(event.target.files[0]);
      image.classList.add('active')
    };
  </script>

  <script>
    const addChannelBtn = document.querySelector(".addChannelBtn"),
      okChannelBtn = document.querySelector(".okChannelBtn"),
      addChannelDiv = document.querySelector(".addChannel-div.upload-details_addChannelDiv_details"),
      addChnInput = document.querySelector("#addChn");

    addChannelBtn.addEventListener('click', () => {

      addChannelBtn.style.display = 'none'
      addChannelDiv.style.display = 'flex';

    });

    okChannelBtn.addEventListener('click', () => {

      addChnInput.value = "";
      addChannelDiv.style.display = 'none';
      addChannelBtn.style.display = 'inline-block';

    });
  </script>

</body>

</html>