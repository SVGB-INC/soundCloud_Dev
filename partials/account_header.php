
  <body class="accounts">
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
                <a class="nav-link text-nowrap" aria-current="page" href="home-page.php">Library</a>
              </li>
              <li class="nav-item mx-1">
                <a class="nav-link text-nowrap" aria-current="page" href="channels.php">Channels</a>
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
                  <li><a class="dropdown-item" href="my-podcasts.php">My Podcasts</a></li>
                  <li><a class="dropdown-item active" href="my-account.php">Settings / Account</a></li>
                  <li><a class="dropdown-item" href="logout.php">Sign Out</a></li>
                </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>