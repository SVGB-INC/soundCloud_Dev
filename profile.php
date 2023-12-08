<?php
session_start();
$_SESSION;

include("Config.php");
include("functions.php");

$user_data = check_login($pdo); // reg_data_bank;
$username = $_GET["user"];

$sqlUserPods = "SELECT podcast_payment FROM user_podcast_access where user_name = '" . $_SESSION["user_name"] . "'";

$sqlPods = '-';
if ($pdo->query($sqlUserPods)->fetchAll(PDO::FETCH_COLUMN)) {
    $sqlPods = $pdo->query($sqlUserPods)->fetchAll(PDO::FETCH_COLUMN)[0];
}

$mypods = "SELECT * FROM podcast_details where user_name = '" . $username . "';";
$my_podcasts = $pdo->query($mypods)->fetchAll(PDO::FETCH_ASSOC);

$myFollowUsers = "SELECT user_follow FROM user_follow where user_name = '" . $_SESSION["user_name"] . "';";
$my_followed_users = $pdo->query($myFollowUsers)->fetchAll(PDO::FETCH_ASSOC);

// .................... Edit USER INFO  ...................
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    if (isset($_POST["userFollow"])) {

        $userToFollow = $_POST["userFollow"];

        $stmt = $pdo->prepare("SELECT * FROM user_follow WHERE user_name=? and user_follow=?");
        $stmt->execute([$_SESSION["user_name"], $userToFollow]);
        $user = $stmt->fetch();
        if ($user) {
        } else {
            $query = $pdo->prepare("Insert into user_follow (user_name,user_follow) values(?,?)");
            $query->execute([$_SESSION["user_name"], $userToFollow]);
            //header("location: ./profile.php?user=" . $userToFollow);

        }
    }

    if (isset($_POST['display_name']) && isset($_POST['bio'])) {
        $query = "UPDATE user_detail
                            SET 
                              display_name = '" . $_POST['display_name'] . "',
                              bio = '" . $_POST['bio'] . "'
                            WHERE
                              user = '" . $username . "'
                              
                  ";
        $q = $pdo->prepare($query);
        $q->execute();
    }
}

// .................... fetch User Data  ...................
$query = " SELECT  user_detail.bio, user_detail.display_name , user_detail.user_img , user_detail.cover_img
                      from user_detail
                        INNER JOIN reg_data_bank
                          ON user_detail.user = reg_data_bank.user_name
                        WHERE
                          user_detail.user ='" . $username . "'
                        ORDER BY
                          user DESC LIMIT 1
      ";
$q = $pdo->prepare($query);
$q->execute();
$user = $q->fetch();

// .................... My Podcasts  ...................
$query = " SELECT  *
                      FROM podcast_details
                        WHERE user_name = '" . $username . "'
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


    <title><?php echo $username ?> User Profile</title>
</head>


<body class="accounts">
    <input type="text" id="isPremium" style="display:none;" hidden value=<?php echo $_SESSION['isPremium'] ?>>
    <input type="text" id="authName" style="display:none;" hidden value=<?php echo $_SESSION["user_name"] ?>>
    <input type="text" id="userPods" style="display:none;" hidden value=<?php echo $sqlPods ?>>
    <input type="text" id="freeTime" style="display:none;" hidden value='7100'>
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

    <div class="container-fluid bg-image pt-5 coverImageInBackground" style="background: linear-gradient(90deg, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.5) 100%),url(<?php echo './images/user_cover_pic/' . $user['cover_img']; ?>); background-repeat: no-repeat; background-size: cover; background-position: center;">
        <div class="container position-relative pt-5">
            <form id="upload_cover_img" name="upload_cover_img" method="post" enctype="multipart/form-data">
                <!-- <input type="text" name="upload_type" value="cover_pic"  hidden id="upload_type"> -->

                <!-- <input type="file" name="updateCoverImage" id="updateCoverImage" hidden>
        <label for="updateCoverImage" style="z-index: 1;" class="btn btn-sm text-nowrap btn-light position-absolute top-5 end-0 mt-2 me-2">
          <i class="fas fa-camera"></i> Update Cover Image
        </label> -->
            </form>
            <!-- <img src="./images/76.jpg" alt="" class="coverImage"> -->
            <div class="d-flex flex-wrap align-items-center justify-content-start py-sm-5 py-3 ps-sm-5">
                <form id="upload_user_image" name="upload_user_image" method="post" enctype="multipart/form-data">
                    <!-- <input type="text" name="upload_type" value="profile_pic"  hidden id="upload_type"> -->
                    <div class="user-profile-img position-relative">
                        <img src="<?php echo './images/user_profile/' . $user['user_img']; ?>" class="img-fluid rounded-circle w-sm-50" id="profileImage" alt="Place Holder" />
                        <!-- <input type="file" name="updateProfileImage" hidden id="updateProfileImage">
            <label for="updateProfileImage" class="btn btn-sm text-nowrap btn-light position-absolute bottom-0 start-50 translate-middle-x">
              <i class="fas fa-camera"></i> Update Profile Image
            </label> -->
                    </div>
                </form>
                <div class="user-details-top px-3 py-3">
                    <h3 class="h3 text-nowrap text-white" style="font-weight: bolder;"><?php echo $user['display_name']; ?></h3>
                    <p class="text-nowrap text-white opacity-75"><?php echo $user['bio']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <main class="container-fluid player-page_body">
        <div class="container">
            <div class="account-name mt-sm-5 mt-3 d-flex flex-wrap justify-content-between align-items-baseline">
                <h2 class="h2 d-inline text-nowrap" ><?php echo $user['display_name']; ?></h2>

                <?php
                $someVarHere = 0;
                if (isset($my_followed_users)) {
                    foreach ($my_followed_users as $item) {
                        if (strval($item["user_follow"]) == $username) {

                            $someVarHere++;
                            break;
                        }
                    }
                    if ($someVarHere > 0) {
                        echo '<button style="pointer-events:none; background:teal; color:white;" class="btn d-flex gap-2 justify-content-center align-items-center"><i class="fa-solid fa-check"></i>Following</button>';
                    } else {
                        echo '<button class="btn btn-outline-dark d-flex followButton gap-2 justify-content-center align-items-center"><i class="fa-solid fa-pen-to-square"></i>Follow</button>';
                    }
                } else {
                    echo '<button class="btn btn-outline-dark d-flex followButton gap-2 justify-content-center align-items-center"><i class="fa-solid fa-pen-to-square"></i>Follow</button>';
                }

                ?>


            </div>
            <hr class="mb-0" />
            <div class="row">
                <div class="py-2 comments-col">
                    <div class="buttons ">

                        <div class="r-btns ms-auto">
                            <button class="mx-2"><i class="fas fa-play me-1"></i> 571k</button>
                            <button class="mx-2"><i class="fas fa-heart me-1"></i> 9,777</button>
                            <button class="mx-2"><i class="fas fa-retweet me-1"></i> 361</button>
                        </div>
                    </div>

                    <div class=" d-flex side-col">
                        <div class="side-col_head p-3">
                            <h3>Related Tracks</h3>
                            <a href="#">View all</a>
                        </div>

                        <?php foreach ($my_podcasts as $podcast) { ?>
                            <form name="podcast" method="post">
                                <div class="py-4 d-flex align-items-center parentPodcast">
                                    <div>
                                        <!-- <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"> -->
                                    </div>
                                    <div class="ms-3 d-flex flex-wrap flex-sm-nowrap w-100">
                                        <img src=<?php echo $podcast["image_address"] ?> class="img-fluid me-2" alt="Responsive-image" style="width:10rem; object-fit:cover;">
                                        <div style="flex: 1 1 auto;">
                                            <a class="audio_id" id="audio_id" name="audio_id" style="display:none;" href="<?= $podcast["pID"] ?>"></a>
                                            <span class="text-secondary"><b><?php echo $podcast["podcast_title"] ?></b></span>

                                            <input type="text" style="display:none" class="form-control" name="pod_image_source" value=<?php echo $podcast["image_address"] ?>>
                                            <input type="text" style="display:none" class="form-control" name="pod_source_add" value=<?php echo $podcast["podcast_address"] ?>>

                                            <p><?php echo $podcast["podcast_desc"] ?></p>

                                            <div class="d-flex gap-md-3 gap-2 flex-wrap flex-sm-nowrap">
                                                <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["podcast_title"]) ?>></a>
                                                <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast['user_name']) ?>></a>
                                                <a id="isCharity" name="charity" style="display:none;" href=<?php echo $podcast["podcast_charity"] ?>></a>
                                                <a id="isPayment" name="payment" style="display:none;" href=<?php echo $podcast["podcast_add_payment"] ?>></a>
                                                <a id="source-image" name="source-image" style="display:none;" href=<?php echo $podcast["image_address"] ?>></a>
                                                <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $podcast["podcast_address"] ?>></a>
                                                <div class="d-flex w-100 flex-wrap gap-3">

                                                    <span class="me-auto btn btn-primary text-nowrap clickPod"><i class="fas fa-play me-1"></i>Play</span>

                                                    <span class="ms-2 text-nowrap d-flex justify-content-center align-items-center"><a href="#" class="text-decoration-none" style="cursor:default;"><i class="fas fa-heart"></i> <?php echo $podcast["podcast_likes"] ?></a></span>
                                                    <span class="ms-2 text-nowrap d-flex justify-content-center align-items-center"><a href="#" class="text-decoration-none" style="cursor:default;"><i class="fas fa-user"></i> <?php echo $podcast["podcast_likes"] ?></a></span>
                                                    <span class="ms-2 text-nowrap d-flex justify-content-center align-items-center"><a href="#" class="text-decoration-none" style="cursor:default;"><i class="fa-regular fa-money-bill-1"></i> <?php echo $podcast["podcast_likes"] ?></a></span>
                                                    <!-- <span class="ms-2 text-nowrap"><a href="#" class="text-decoration-none"><i class="fas fa-redo"></i> 6</a></span> -->
                                                    <span class="text-nowrap d-flex justify-content-center align-items-center"><a href="#" class="text-decoration-none ms-2"><i class="fas fa-play me-1"></i><?php echo $podcast["play_count"] ?></a></span>
                                                    <span class="ms-2 text-secondary text-nowrap d-flex justify-content-center align-items-center"><i class="fas fa-history"></i>&nbsp;<?php echo gmdate("H:i:s", $podcast["podcast_time"]); ?></span>
                                                    <span class="ms-2 text-secondary text-nowrap d-flex justify-content-center align-items-center"><i class="fa-solid fa-clock"></i>&nbsp;<?php echo gmdate("H:i:s", $podcast["podcast_time"]); ?></span>
                                                    <span class="ms-2 text-secondary text-nowrap d-flex justify-content-center align-items-center"><?php echo $podcast["upload_date"] ?></span>
                                                    <!-- <button type="submit" class="ms-4 btn btn-primary text-nowrap d-flex justify-content-center align-items-center"><i class="far fa-trash-alt"></i></button>
                          <a href=<?php echo "edit-details.php?pod=" . $podcast["podcast_address"] ?> class="ms-1 btn btn-primary text-nowrap d-flex justify-content-center align-items-center"><i class="fa-solid fa-pen-to-square"></i></a> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php
                        } ?>
                    </div>

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

    <div class="EditModal">
        <div class="overlay"></div>
        <div class="actualModal p-4">
            <form method="post" class="d-flex flex-column gap-3">
                <div class="form-group cstm-form-group">
                    <label for="editDisplayName">Display Name <span class="text-danger">*</span></label>
                    <input type="text" value="<?= $user['display_name']; ?>" class="form-control" name='display_name' required id="editDisplayName" placeholder="Enter Display Name">
                    <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                </div>

                <div class="form-group cstm-form-group">
                    <label for="editBio">Bio <span class="text-danger">*</span></label>
                    <textarea type="text" class="form-control" name='bio' required id="editDisplayName" style="height:7rem" placeholder="Enter Bio"> <?= $user['bio']; ?> </textarea>
                    <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                </div>

                <button type="submit" class="btn btn-sm pink-bg text-white align-self-end">Done Editing</button>
            </form>
        </div>
    </div>

    <!-- ......................... -->

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

                $('.followButton').click(function() {

                    const queryString = window.location.search;
                    const urlParams = new URLSearchParams(queryString);
                    var userFollow = (urlParams.get('user'));


                    $.ajax({
                        url: "./profile.php?user=" + userFollow,
                        method: "POST",
                        data: {
                            userFollow
                        },
                        success: function(data) {
                            //console.log(data);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr);
                        },
                    });

                    window.location.reload();

                });


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

                    audio_id = $(this).parent().parent().prevAll('#audio_id').first().attr("href");

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