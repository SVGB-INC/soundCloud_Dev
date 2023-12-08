<?php

session_start();
$_SESSION;

include("Config.php");
include("functions.php");


$user_data = check_login($pdo);


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
    <title>Messages</title>
  </head>
  <body>
    <header class="container-fluid d-flex justify-content-center align-items-center">
      <nav class="navbar navbar_cstm navbar-expand-xl bg-dark navbar-dark home-head-bg fixed-top">
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
                <a class="nav-link  active" aria-current="page" href="messages.php"><i class="fas fa-envelope"></i></a>
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
    </header>
    <div class="container pt-5">
      <div class="row bg-light pt-5">
        <div class="col-md-4 p-4">
          <div class="main-heading d-flex flex-wrap justify-content-between align-items-center mb-2">
            <div>
              <h2>Messages</h2>
            </div>
            <div>
              <button type="button" class="btn btn-outline-secondary btn-sm"><i class="fas fa-plus"></i> New message</button>
            </div>
          </div>
          <div class="chat-user-select py-2 border-start border-3 bg-white ps-1">
            <div class="d-flex align-items-center">
              <img src="images/placeholder-100by100.png" class="rounded-circle w-sm-50 img-thumbnail" alt="Place Holder" />
              <div class="d-inline-block ms-sm-2">
                <p class="ms-1 mb-0"><strong>John Doe</strong></p>
                <p class="ms-1 mb-0">Lorem Ipsum is simply dummy text...</p>
              </div>
            </div>
          </div>
          <hr />
          <div class="chat-user-select py-2 ps-1">
            <div class="d-flex align-items-center">
              <img src="images/placeholder-100by100.png" class="rounded-circle w-sm-50 img-thumbnail" alt="Place Holder" />
              <div class="d-inline-block ms-sm-2">
                <p class="ms-1 mb-0"><strong>John Doe</strong></p>
                <p class="ms-1 mb-0">Lorem Ipsum is simply dummy text...</p>
              </div>
            </div>
          </div>
          <hr />
          <div class="chat-user-select py-2 ps-1">
            <div class="d-flex align-items-center">
              <img src="images/placeholder-100by100.png" class="rounded-circle w-sm-50 img-thumbnail" alt="Place Holder" />
              <div class="d-inline-block ms-sm-2">
                <p class="ms-1 mb-0"><strong>John Doe</strong></p>
                <p class="ms-1 mb-0">Lorem Ipsum is simply dummy text...</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8 border-start p-4">
          <div class="full-chat-head mt-2 d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <h4 class="d-inline">John Doe</h4>
              <button type="button" class="btn btn-outline-secondary btn-sm mx-2"><i class="fas fa-ban"></i> Block</button>
              <button type="button" class="btn btn-outline-secondary btn-sm">Report</button>
            </div>
            <div>
              <button type="button" class="btn btn-outline-secondary btn-sm">Mark as read</button>
              <button type="button" class="btn btn-outline-secondary btn-sm"><i class="fas fa-trash"></i> Delete</button>
            </div>
          </div>
          <hr />
          <div class="message-content py-2">
            <div class="d-flex justify-content-start align-items-center">
              <img src="images/placeholder-100by100.png" class="rounded-circle w-sm-50 img-thumbnail" alt="Place Holder" />
              <div class="ms-1">
                <p class="ms-1 mb-0"><strong> John Doe</strong></p>
                <p class="ms-1 mb-0 text-secondary">31 minutes ago</p>
              </div>
            </div>
            <p class="ms-2 mt-2 p-2">
              Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever
              since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.
            </p>
          </div>
          <div class="message-content py-2">
            <div class="d-flex justify-content-start align-items-center">
              <img src="images/placeholder-100by100.png" class="rounded-circle w-sm-50 img-thumbnail" alt="Place Holder" />
              <div class="ms-1">
                <p class="ms-1 mb-0"><strong>Me</strong></p>
                <p class="ms-1 mb-0 text-secondary">31 minutes ago</p>
              </div>
            </div>
            <p class="ms-2 mt-2 p-2">
              Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever
              since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.
            </p>
          </div>
          <div>
            <form action="">
              <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
              <button type="submit" class="btn pink-bg text-white mt-2">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
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
          <audio controls controlsList="nodownload">
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
    <!-- header color change on scroll -->
    <script src="./js/header.js"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->


  </body>
</html>
