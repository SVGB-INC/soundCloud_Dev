<?php

session_start();
$_SESSION;

include("Config.php");
include("functions.php");

$user_data = check_login($pdo);
if ($_SERVER['REQUEST_METHOD'] === "GET"):
  if(isset($_GET['playlist'])):
    
      $query = "SELECT * 
                        FROM playlists as pl
                            INNER JOIN liked_audio as la
                                ON
                                pl.ID = la.playlist
                                AND
                                pl.author = la.user
                            INNER JOIN podcast_details as pd
                                ON
                                la.podcast = pd.pID
                            WHERE
                            la.user = '" . $_SESSION['user_id'] . "'
                          AND
                          la.playlist = '" . $_GET['playlist'] . "'
                        ";

      $sql = $pdo->query($query);
      $sql->execute();
      $playlist_data = $sql->fetchAll(PDO::FETCH_ASSOC);

      if(!$playlist_data){
        $query = " SELECT *
                      FROM playlists
                      WHERE
                      author = '" . $_SESSION['user_id'] . "'
                      AND
                      ID = '" . $_GET['playlist'] . "'
        ";
        $sql = $pdo->query($query);
        $sql->execute();
        $playlist = $sql->fetchAll(PDO::FETCH_ASSOC);
      }

    //   var_dump($playlist_data);
    //   die;

?>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
      crossorigin="anonymous"
    />

    <link rel="stylesheet" href="./Styles/styles.css" />

    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
      integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <title>Chhogori | Playlist</title>
  </head>
  <body class="player-page">
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
      <?php if($playlist_data){ ?>
      <img class="p-back-cover" src="<?= './images/user_playlist_pic/'.$playlist_data[0]['img'] ?>" alt="">
      <?php } else{ ?>
        <img class="p-back-cover" src="<?= './images/user_playlist_pic/'.$playlist[0]['img'] ?>" alt="">
        <?php }?>
      <div class="d-flex flex-column">

      <?php if($playlist_data){ ?>
        <img class="p-cover" src="<?= './images/user_playlist_pic/'.$playlist_data[0]['img'] ?>" alt="">
        <?php } else{ ?>
          <img class="p-cover" src="<?= './images/user_playlist_pic/'.$playlist[0]['img'] ?>" alt="">
          <?php }?>
        <div class="text">
        <?php if($playlist_data){ ?>
          <h2 class="song-heading text-center text-white"><?= $playlist_data[0]['title'] ?></h2>
          <?php } else{ ?>
            <h2 class="song-heading text-center text-white"><?= $playlist[0]['title'] ?></h2>
            <?php }?>
          <!-- <p class="song-writer text-white">
            by: Lorem Ipsum
          </p> -->
        </div>
      </div>    
    </div>
    <main class="container-fluid player-page_body">
      <div class="container">
        <div class="account-name mt-sm-5 mt-3 d-flex flex-wrap justify-content-start align-items-center">
        <?php if($playlist_data){ ?>
          <h2 class="h2 d-inline text-nowrap"><?= $playlist_data[0]['title'] ?></h2>
          <?php } else{ ?>
            <h2 class="h2 d-inline text-nowrap"><?= $playlist[0]['title'] ?></h2>
            <?php }?>
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
                <?php foreach($playlist_data as $playlist): ?>
                <div class="side-col_single">
                  <img src="<?= $playlist['image_address'] ?>" alt="">
                  <div class="text">
                    <h4><?= $playlist['podcast_title'] ?></h4>
                    <p><?= $playlist['podcast_desc'] ?></p>
                    <div class="btns d-flex gap-2 flex-wap align-self-end">
                      <button class="btn btn-sm pink-bg text-white">Play</button><button class="btn btn-sm pink-bg text-white">Add to List</button>
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
          <audio controls>
            <source src="./audio/dam.mp3" type="audio/mpeg">
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
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
      crossorigin="anonymous"
    ></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
  </body>
</html>
<?php endif; endif; ?>