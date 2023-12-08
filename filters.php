<?php

session_start();
$_SESSION;

include("./Config.php");
include("./functions.php");

$user_data = check_login($pdo);

if (!empty($_POST['pod_genre'])) {
    if ($_POST['pod_genre'][0] === 'Choose a Genre') {
    } else {
        $chosenGenre = $_POST['pod_genre'][0];
    }
}

if (!empty($_POST['pod_tags'])) {
    if ($_POST['pod_tags'][0] === 'Choose a Tag') {
    } else {
        $chosenTag = $_POST['pod_tags'][0];
    }
}



if (!empty($_POST['order'])) {
    if ($_POST['order'][0] === 'A to Z') {
        $chosenOrder = " Order by podcast_title ASC;";
    } elseif ($_POST['order'][0] === 'Z to A') {
        $chosenOrder = " Order by podcast_title DESC;";
    } elseif ($_POST['order'][0] === 'Upload Date ASC') {
        $chosenOrder = " Order by upload_date ASC;";
    } elseif ($_POST['order'][0] === 'Upload Date DESC') {
        $chosenOrder = " Order by upload_date DESC;";
    }
}

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

// show all playlists
$my_play_list = $pdo->query("SELECT * FROM playlists WHERE author ='" . $_SESSION['user_id'] . "'");
$my_play_list->execute();
$my_play_list = $my_play_list->fetchAll(PDO::FETCH_ASSOC);

$my_fvrt_audio = $pdo->query("SELECT * FROM fvrt_audio WHERE user ='" . $_SESSION['user_id'] . "'");
$my_fvrt_audio->execute();
$my_fvrt_audio = $my_fvrt_audio->fetchAll(PDO::FETCH_ASSOC);

if (isset($chosenGenre) || isset($chosenTag) || isset($chosenOrder)) {
    $query = "SELECT * FROM podcast_details ";
    if (isset($chosenGenre)) {
        $query .= " WHERE podcast_genre like '%" . $chosenGenre . "%' ";
    }
    if (isset($chosenGenre) && isset($chosenTag)) {
        $query .= " and podcast_tags like '%" . $chosenTag . "%' ";
    } elseif (!isset($chosenGenre) && isset($chosenTag)) {
        $query .= " WHERE podcast_tags like '%" . $chosenTag . "%' ";
    }
    if (isset($chosenOrder)) {
        $query .= $chosenOrder;
    }

    
    $sql = $pdo->query($query);
    $sql->execute();
    $searched_data = $sql->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "No Filter Selected";
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
    <!-- slick links start-->
    <link rel="stylesheet" href="./Styles/slick.css" />
    <link rel="stylesheet" href="./Styles/slick-theme.css" />
    <!-- slick links end -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Chhogori | Filters</title>
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
                                <form action="search.php" method="post" class="d-flex flex-nowrap">
                                    <input type="search" id="searchFunc" name="search" class="form-control bg-transparent text-white" style="border-right: none; border-top-right-radius: 0; border-bottom-right-radius: 0;" placeholder="Search..." aria-label="search" aria-describedby="search" />
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
                                    <li><a class="dropdown-item" href="./my-podcasts.php">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="./my-channels.php">Create New Channels</a></li>
                                    <li><a class="dropdown-item" href="./my_playlists.php">Create New Playlists</a></li>
                                    <li><a class="dropdown-item" href="./my-account.php">Settings</a></li>
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

        <div class="row mt-5">
            <div class="col ps-4 pb-3">
                <h3 class="d-inline">
                    <?php
                    $outputString = "";
                    if (isset($chosenGenre))
                    {
                        $outputString .= $chosenGenre;
                        $outputString .= " ";
                    }
                    if (isset($chosenTag))
                    {
                        $outputString .= $chosenTag;
                        $outputString .= " ";
                    }
                    if (isset($chosenOrder))
                    {
                        $outputString .= $_POST['order'][0];
                        
                    }
                    echo $outputString;
                    ?>
                </h3>
                <span class="ps-3 pb-2"><a href="#" class="pink-text">View More</a></span>
            </div>
        </div>


        <div class="responsive-table res1 py-2">
            <?php foreach ($searched_data as $data) { ?>
                <div class="rounded-bottom card card-1 d-flex position-relative top-0 start-0 flex-column">
                    <?php if ($data['podcast_add_payment'] == "paid") { ?>
                        <span class="btn btn-warning btn-sm position-absolute" style="z-index: 10; top: 0.5rem; right:0.5rem">Paid</span>
                    <?php } ?>
                    <div class="imgContainer">
                        <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $data['podcast_title']) ?>></a>
                        <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $data['user_name']) ?>></a>
                        <a id="isCharity" name="charity" style="display:none;" href=<?php echo $data['podcast_charity'] ?>></a>
                        <a id="isPayment" name="payment" style="display:none;" href=<?php echo $data['podcast_add_payment'] ?>></a>
                        <a id="source-image" name="source-image" style="display:none;" href=<?php echo $data['image_address'] ?>></a>
                        <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $data['podcast_address'] ?>></a>
                        <img class="imgPod" src=<?php echo $data['image_address']; ?> />
                        <!-- </a> -->
                    </div>
                    <div class="content">
                        <a class="audio_id" id="audio_id" name="audio_id" style="display:none;" href="<?= $data['pID'] ?>"></a>
                        <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $data['podcast_title']) ?>></a>
                        <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $data['user_name']) ?>></a>
                        <a id="isCharity" name="charity" style="display:none;" href=<?php echo $data['podcast_charity'] ?>></a>
                        <a id="isPayment" name="payment" style="display:none;" href=<?php echo $data['podcast_add_payment'] ?>></a>
                        <a id="source-image" name="source-image" style="display:none;" href=<?php echo $data['image_address'] ?>></a>
                        <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $data['podcast_address'] ?>></a>
                        <h3><?php $data['podcast_title'] ?></h3>
                        <!-- <h5><?= str_replace(' ', '_', $data['user_name']) ?></h5> -->
                        <h5><a style="text-decoration:none;color:inherit;" href=<?php
                                                                                if (str_replace(' ', '_', $data['user_name']) == str_replace(' ', '_', $_SESSION["user_name"])) {
                                                                                    echo "./my-account.php";
                                                                                } else {
                                                                                    echo "./profile.php?user=" . str_replace(' ', '_', $data['user_name']);
                                                                                }
                                                                                ?>><?= str_replace(' ', '_', $data['user_name']) ?></a></h5>
                        <p><?= $data["podcast_desc"] ?></p>
                        <div class="icons">
                            <button class="playInformation">
                                <i class="fas fa-play clickPod"></i>
                                <span><?= $data["play_count"] ?></span>
                            </button>
                            <button class="addToPlayListInformation">
                                <i class="fas fa-plus clickAddToPlayList"></i>

                            </button>
                            <?php
                            $fvrt = false;
                            foreach ($my_fvrt_audio as $myfvrt) {
                                if ($myfvrt['podcast'] == $data["pID"]) {
                                    $fvrt = true;
                                }
                            }
                            ?>
                            <button class="heartIconCustomMade likeing-button clickAddToFavrt">
                                <i id="heartId_<?= $data["pID"]  ?>" class="<?php if ($fvrt) { ?>fa-solid<?php } else { ?>fa-regular<?php } ?> fa-heart"></i>
                                <span><?= $data["podcast_likes"] ?></span>
                            </button>


                        </div>
                    </div>

                </div>
            <?php } ?>

            <!-- ......................... -->

            <div class="downAlert">
                <p>The Podcast was liked</p>
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
                        <strong id="podcastTitle_Player">Podcast Title</strong>
                        <p id="podcastAuthor_Player">Podcast Author</p>
                    </div>
                    <div class="buttons">
                        <i class="fas fa-heart"></i>
                        <i class="fas fa-user-plus"></i>
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
                    //console.log(data);
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

            console.log(audioSource);

            var userPodcasts = $("#userPods").val();

            //audioSource: audioSource.substring(audioSource.lastIndexOf('\\') + 1)

            $.ajax({
                url: "./home-page.php",
                method: "POST",
                data: {
                    audioSource: audioSource
                },
                success: function(data) {
                    //console.log(data);
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
                                $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
                                $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
                                $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
                                $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popup10}`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
                                $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
                                $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
                                $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
                                $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
                            }
                        } else if (freeTimeUser > 7200) { // user with no free time
                            if (advPayment == 'advNone' && chrPayment == 'chrNone') {
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popupNN`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
                                $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
                                $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
                                $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
                                $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popup10`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
                                $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
                                $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
                                $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                                document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
                            } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
                                $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
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
                            $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                            document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
                        } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
                            $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                            document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
                        } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
                            $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                            document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
                        } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
                            $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                            document.querySelectorAll(`.popup-overlay, .popups-main, .popup10`).forEach(each => each.classList.add('active'));
                        } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
                            $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                            document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
                        } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
                            $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                            document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
                        } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
                            $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                            document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
                        } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
                            $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audio_id)[0]);
                            document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
                        }

                    }

                }
            }



        });

        $('.clickAddToPlayList').click(function() {


            audio_id = $(this).parent().parent().prevAll('#audio_id').first().attr("href");
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
                    //console.log(data);
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

        // $(function () {
        //     $("#genreFilter").change(function () {
        //         var selectedText = $(this).find("option:selected").text();
        //         var selectedValue = $(this).val();
        //         //alert("Selected Text: " + selectedText + " Value: " + selectedValue);
        //         location.href = "/filters?genre=" + selectedText;
        //     });
        // });
    </script>

</body>

</html>