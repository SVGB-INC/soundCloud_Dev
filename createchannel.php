<?php
session_start();
$_SESSION;

include("Config.php");
include("functions.php");

$user_data = check_login($pdo);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $title = $_POST['channel_title'];
  $desc = $_POST['channel_desc'];
  //$tags = serialize($_POST['channel_tags']);
  //$genre = serialize($_POST['channel_genre']);
  $author = $_SESSION['user_id'];

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

  $genre = rtrim(trim($genres), '|');

  $tags = '';

  if (isset($_POST['channel_tags']))
  {
      foreach (array_map('strip_tags', $_POST['channel_tags']) as $tagChosen)
      {
          $tags .= $tagChosen . ',';
      }
  }

  $tagsNew = rtrim(trim($tags) , ',');


  if ((!empty($_FILES)) && !empty($_FILES['channel_image']['name'])) {
    $img_name = explode(".", $_FILES['channel_image']['name']);
    $extention = end($img_name);

    $img_id = str_replace('.', '', time() . uniqid(rand(100, 999), true));

    $img_name = $img_id . '.' . $extention;
    $location = USER_CHANNEL_PIC . '/' . $img_name;

    move_uploaded_file($_FILES['channel_image']['tmp_name'], $location);

    $query = "INSERT into channels(img, title, short_desc, tags, genre, author) VALUES('$img_name','$title','$desc','$tagsNew','$genre','$author')";
    $sql = $pdo->prepare($query);
    if ($sql->execute()) {
      $upgrade_message = "You Have Successfully Created a Channel";
      header("refresh:2; my-channels.php");
    }
  }
}
//     function cleanSpecialCharacters($string)
//     {
//         $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
//         return preg_replace('/[^A-Za-z0-9.-]/', "", $string); // Removes special chars.

//     }

//     try
//     {

//         //$rand = rand(10, 100);
//         $streplaceFileName = cleanSpecialCharacters($_FILES['uploaded-image']['name']);
//         //$audioFile = $rand . "-" . $streplaceFileName;
//         $audioFile = $streplaceFileName;
//         $ds = DIRECTORY_SEPARATOR;
//         $storeFolder = 'pod_thumbs/' . $_SESSION['user_name'];

//         if (is_dir($storeFolder))
//         {
//         }
//         else
//         {
//             mkdir('pod_thumbs/' . $_SESSION['user_name']);
//         }

//         if ((!empty($_FILES)) && !empty($_FILES['uploaded-image']['name']))
//         {
//             $filename = $streplaceFileName;
//             $tempFile = $_FILES['uploaded-image']['tmp_name'];
//             $targetPath = $storeFolder . $ds;
//             $targetFile = $targetPath . $filename;
//             $check = move_uploaded_file($tempFile, $targetFile);
//             if ($check)
//             {
//                 //$uploadMSG = "Details Uploaded Successfully!";
//                 //header("Location: my-podcasts.php");

//             }
//         }

//         try
//         {

//             $sqlUserID = "SELECT user_id FROM reg_data_bank where user_name = '" . $_SESSION['user_name'] . "'";
//             $userID = $pdo->query($sqlUserID)->fetchAll(PDO::FETCH_COLUMN) [0];

//             $date = date("Y-m-d h:i:s");

//             $tags = '';

//             if (isset($_POST['pod_tags']))
//             {
//                 foreach (array_map('strip_tags', $_POST['pod_tags']) as $tagChosen)
//                 {
//                     $tags .= $tagChosen . ',';
//                 }
//             }

//             $tagsNew = rtrim(trim($tags) , ',');

//             $channelName = '';

//             echo $_POST['uploadCat'];

//             if ($_POST['uploadCat'] == 'TdChanList')
//             {
//                 $channelName = $_POST['channelList'];
//             }
//             else if ($_POST['uploadCat'] == 'Solo')
//             {
//                 $channelName = 'Solo File';
//             }
//             else if ($_POST['uploadCat'] == 'addChannel')
//             {
//                 $channelName = $_POST['newChannelName'];
//             }


//             $insert_stmt = $pdo->prepare("INSERT INTO podcast_details(user_ID, user_name, upload_date, podcast_address, image_address, podcast_title, podcast_desc, podcast_tags, podcast_genre, podcast_likes, podcast_comments, play_count, podcast_channel, podcast_add_payment, podcast_charity) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"); //sql insert query
//             if ($insert_stmt->execute([$userID, $_SESSION['user_name'], $date, $_SESSION['file_name_audio'], $targetFile, $_POST['pod_title'], $_POST['pod_desc'], $tagsNew, $_POST['pod_genre'], '0', '0', '0', $channelName, $_POST['additionalPlans'], $_POST['CharityPlans']]))
//             {

//                 if ($_POST['additionalPlans'] != 'advNone')
//                 {

//                     $fileNameNow = substr($_SESSION['file_name_audio'], strrpos($_SESSION['file_name_audio'], '\\') + 1);

//                     $pIDSQL = "SELECT pID FROM podcast_details where SUBSTRING_INDEX(podcast_address,'\\\',-1)  = '" . $fileNameNow . "'";

//                     $pID = $pdo->query($pIDSQL)->fetchAll(PDO::FETCH_COLUMN) [0];

//                     $advAmount = '0';
//                     $chrAmount = '0';

//                     if (isset($_POST['advAmount_Opt']) && $_POST['advAmount_Opt'] != '')
//                     {
//                         $advAmount = $_POST['advAmount_Opt'];
//                     }
//                     else if (isset($_POST['advAmount_Mst']) && $_POST['advAmount_Mst'] != '')
//                     {
//                         $advAmount = $_POST['advAmount_Mst'];
//                     }

//                     if (isset($_POST['chrAmount_Opt']) && $_POST['chrAmount_Opt'] != '')
//                     {
//                         $chrAmount = $_POST['chrAmount_Opt'];
//                     }
//                     else if (isset($_POST['chrAmount_Mst']) && $_POST['chrAmount_Mst'] != '')
//                     {
//                         $chrAmount = $_POST['chrAmount_Mst'];
//                     }

//                     $insert_stmt = $pdo->prepare("INSERT INTO podcast_payments(pID,podcast_payment,podcast_charity) VALUES (?,?,?)");

//                     $insert_stmt->execute([$pID, $advAmount, $chrAmount]);
//                 }
//                 $uploadMSG = "Uploaded Successfully"; //execute query success message
//                 header("Location: my-podcasts.php");

//             }
//         }
//         catch(PDOException $e)
//         {
//             echo $e->getMessage();
//         }
//     }
//     catch(PDOException $e)
//     {
//         echo $e->getMessage();
//     }

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- select2 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.min.js"></script>

  <!-- bootstrap cdn -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
  <!-- fontawesome cdn -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />



  <link rel="stylesheet" href="./Styles/styles.css" />
  <title>Chhogori | Creat Channel</title>
</head>

<body class="sign-up-page upload-details">
  <div class="bodyclone">

    <header class="justify-content-center align-items-center">
       <nav class="navbar navbar_cstm navbar-expand-xl navbar-dark bg-dark fixed-top">
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
              <a class="nav-link text-nowrap active" aria-current="page" href="./home-page.php">Messages</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="./channels.php">Voices</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="./new_and_popular.php">New &amp; Popular</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="./my-stream.php">My Stream</a>
            </li>
            <li class="nav-item mx-1">
              <a class="nav-link text-nowrap" aria-current="page" href="./my-playlist.php">My Playlist</a>
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
      <div class="container mt-5">
        <div class="row mt-5">
          <div class="col-md-2 d-none d-md-block"></div>
          <div class="col-md-8 p-0">
            <form class="form py-5 px-md-4 px-2 px-lg-4 bg-opacity-75" enctype="multipart/form-data" name="podcastdetails" method="post" style="min-height: 33rem;">
              <div class="container-section">
                <h1 class="pb-4">Create Channel</h1>

                <?php if (isset($upgrade_message)) {
                ?>
                  <div class="alert alert-success text-center py-1 my-2">
                    <strong><?php echo $upgrade_message; ?></strong>
                  </div>
                <?php
                }
                ?>
                <div class="mb-3">
                  <span>Upload Channel Image</span>
                  <label for="upload-channel-image" id="imgUploadLabel">
                    <button type="button">
                      <i class="fas fa-file-upload"></i>
                      Upload Image
                    </button>
                  </label>
                  <input type="file" class="form-control" name="channel_image" id="upload-channel-image" onchange="loadFile(event)" hidden />
                  <img alt="outputImg" id="outputImage" width="100">
                </div>
                <div class="mb-3">
                  <label for="Title" class="form-label">Title</label>
                  <input type="text" class="form-control" name="channel_title" id="Title" placeholder="Title" required>
                </div>
                <div class="mb-3">
                  <label for="ShortDescription" class="form-label">Short Description</label>
                  <textarea class="form-control" name="channel_desc" id="ShortDescription" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-check-label" for="tags">Select Tags:</label><br>
                  <select name="channel_tags[]" id="tags" data-select2-id="tags" class="w-100" multiple>
                    <option value="podcast">Podcast</option>
                  </select>
                </div>
                <!-- <div class="mb-3">
                  <label class="form-check-label" for="selectGenre">Select Genre:</label><br>
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
                <button type="submit" class="btn mt-3 pink-bg text-white w-100">Submit</button>

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

  <script src="./js/tagsInput.js"></script>
  <script>
    function loadFile(event) {
      var image = document.getElementById('outputImage');
      image.src = URL.createObjectURL(event.target.files[0]);
      image.classList.add('active')
    };
  </script>
  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>



</body>

</html>