<?php

session_start();
$_SESSION;

include("Config.php");
include("functions.php");

if(isset($_SESSION["user_name"]))
{
  header("Location: home-page.php");
}



if ($_SERVER['REQUEST_METHOD'] === "POST")
{

  if (isset($_POST['emailtop']))
  {
    $_SESSION["getstarted_email"] = $_POST['emailtop'];
  }
  elseif (isset($_POST['emailbelow'])) 
  {
    $_SESSION["getstarted_email"] = $_POST['emailbelow'];
  }
  
  // $_SESSION["getstarted_email"] = $_POST['emailbelow'];

  header("Location: sign-up.php");

}

?>

<!DOCTYPE html>
<html>
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

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
      integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <title>Chhogori | Landing Page</title>
  </head>
  <body>
    <div>
      <header
        class="
          container-fluid
          landing-page-banner
          justify-content-center
          align-items-center
        "
      >
        <nav class="navbar navbar_cstm navbar-expand-xl navbar-dark">
          <div class="container">
            <a class="navbar-brand fs-3 fw-bolder" href="index.php"
              ><img class="logo" src="./images/Logo.png" alt=""
            /></a>
            <div>
              <ul class="navbar-nav mb-2 mb-lg-0 ms-md-auto">
                <li class="nav-item mx-1">
                  <a
                    class="
                      nav-link
                      pink-bg
                      btn
                      text-white
                      fw-bolder
                      btn-sm
                      px-3
                      py-2
                    "
                    aria-current="page"
                    href="sign-in.php"
                    >Sign In</a
                  >
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <div
          class="container p-2 d-flex justify-content-center align-items-center"
        >
          <div class="">
            <div class="header_text">
              <h1
                class="text-white text-center"
                style="
                  font-family: 'Montserrat', sans-serif !important;
                  font-weight: 700;
                "
              >
                OPINIONS: Build them. Share them. Gain from them.
              </h1>
              <h5
                class="text-white text-center mt-2 mb-4"
                style="font-family: 'Montserrat', sans-serif !important"
              >
                Speak and Listen Anywhere, Anytime.
              </h5>
              <h6 class="text-white text-center">
                Enter your email to sign-up!
              </h6>
              <div
                class="
                  d-flex
                  justify-content-center
                  align-items-center
                  w-100
                  mx-auto
                  header_input
                "
              >
              <form name="getstartedtop" class = "d-flex w-100" method="post">
              <input class="form-control w-100" name = "emailtop" placeholder="Email Address" />
              <button type="submit"
                  class="btn text-nowrap pink-bg text-white btn-small"
                  >Get Started
                </a>
              </form>
                
              </div>
            </div>
          </div>
        </div>
      </header>
      <main class="container-fluid bg-black">
        <div class="container">
          <div class="row">
            <div class="col-md-6 col-12">
              <div
                class="
                  h-100
                  px-md-5 px-3
                  py-5 py-md-3
                  d-flex
                  justify-content-center
                  flex-column
                "
              >
                <!-- <div class="h-100 px-5 "> -->
                <h1 class="text-white fs-2 text-md-start text-center">
                  Main Heading 1
                </h1>
                <h4 class="text-white text-md-start text-center">
                  Information About Heading 1
                </h4>
              </div>
            </div>
            <div class="col-md-6 col-12 d-flex justify-content-center">
              <img class="img-fluid" src="images/landing-page-right-01.png" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-12 order-md-1">
              <div
                class="
                  h-100
                  px-md-5 px-3
                  py-5 py-md-3
                  d-flex
                  justify-content-center
                  flex-column
                "
              >
                <!-- <div class="h-100 px-5 "> -->
                <h1 class="text-white fs-2 text-md-start text-center">
                  Main Heading 1
                </h1>
                <h4 class="text-white text-md-start text-center">
                  Information About Heading 1
                </h4>
              </div>
            </div>
            <div class="col-md-6 col-12 d-flex justify-content-center">
              <img class="img-fluid" src="images/landing-page-left-02.png" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-12">
              <div
                class="
                  h-100
                  px-md-5 px-3
                  py-5 py-md-3
                  d-flex
                  justify-content-center
                  flex-column
                "
              >
                <!-- <div class="h-100 px-5 "> -->
                <h1 class="text-white fs-2 text-md-start text-center">
                  Main Heading 1
                </h1>
                <h4 class="text-white text-md-start text-center">
                  Information About Heading 1
                </h4>
              </div>
            </div>
            <div class="col-md-6 col-12 d-flex justify-content-center">
              <img class="img-fluid" src="images/landing-page-right-03.png" />
            </div>
          </div>
        </div>
        <!-- <hr class="border" /> -->
        <div class="accordion container" id="accordionExample">
          <h1 class="text-white text-center">Frequently Asked Questions</h1>
          <div class="w-100 accordion-main-div mx-auto mt-4">
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingOne">
                <button
                  class="accordion-button collapsed bg-dark text-light"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseOne"
                  aria-expanded="false"
                  aria-controls="collapseOne"
                >
                  Question No 1
                </button>
              </h2>
              <div
                id="collapseOne"
                class="accordion-collapse collapse"
                aria-labelledby="headingOne"
                data-bs-parent="#accordionExample"
              >
                <div class="accordion-body">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit.
                  Maxime mollitia, molestiae quas vel sint commodi repudiandae
                  consequuntur voluptatum laborum numquam blanditiis harum
                  quisquam eius sed odit fugiat iusto fuga praesentium optio,
                  eaque rerum! Provident similique accusantium nemo autem.
                  Veritatis obcaecati tenetur iure eius earum ut molestias
                  architecto voluptate aliquam nihil, eveniet aliquid culpa
                  officia aut! Impedit sit sunt quaerat, odit, tenetur error,
                  harum nesciunt ipsum debitis quas aliquid. Reprehenderit,
                  quia.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button
                  class="accordion-button collapsed bg-dark text-light"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseTwo"
                  aria-expanded="false"
                  aria-controls="collapseTwo"
                >
                  Question No 2
                </button>
              </h2>
              <div
                id="collapseTwo"
                class="accordion-collapse collapse"
                aria-labelledby="headingTwo"
                data-bs-parent="#accordionExample"
              >
                <div class="accordion-body">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit.
                  Maxime mollitia, molestiae quas vel sint commodi repudiandae
                  consequuntur voluptatum laborum numquam blanditiis harum
                  quisquam eius sed odit fugiat iusto fuga praesentium optio,
                  eaque rerum! Provident similique accusantium nemo autem.
                  Veritatis obcaecati tenetur iure eius earum ut molestias
                  architecto voluptate aliquam nihil, eveniet aliquid culpa
                  officia aut! Impedit sit sunt quaerat, odit, tenetur error,
                  harum nesciunt ipsum debitis quas aliquid. Reprehenderit,
                  quia.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingThree">
                <button
                  class="accordion-button collapsed bg-dark text-light"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseThree"
                  aria-expanded="false"
                  aria-controls="collapseThree"
                >
                  Question No 3
                </button>
              </h2>
              <div
                id="collapseThree"
                class="accordion-collapse collapse"
                aria-labelledby="headingThree"
                data-bs-parent="#accordionExample"
              >
                <div class="accordion-body">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit.
                  Maxime mollitia, molestiae quas vel sint commodi repudiandae
                  consequuntur voluptatum laborum numquam blanditiis harum
                  quisquam eius sed odit fugiat iusto fuga praesentium optio,
                  eaque rerum! Provident similique accusantium nemo autem.
                  Veritatis obcaecati tenetur iure eius earum ut molestias
                  architecto voluptate aliquam nihil, eveniet aliquid culpa
                  officia aut! Impedit sit sunt quaerat, odit, tenetur error,
                  harum nesciunt ipsum debitis quas aliquid. Reprehenderit,
                  quia.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingFour">
                <button
                  class="accordion-button collapsed bg-dark text-light"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseFour"
                  aria-expanded="false"
                  aria-controls="collapseFour"
                >
                  Question no 4
                </button>
              </h2>
              <div
                id="collapseFour"
                class="accordion-collapse collapse"
                aria-labelledby="headingFour"
                data-bs-parent="#accordionExample"
              >
                <div class="accordion-body">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit.
                  Maxime mollitia, molestiae quas vel sint commodi repudiandae
                  consequuntur voluptatum laborum numquam blanditiis harum
                  quisquam eius sed odit fugiat iusto fuga praesentium optio,
                  eaque rerum! Provident similique accusantium nemo autem.
                  Veritatis obcaecati tenetur iure eius earum ut molestias
                  architecto voluptate aliquam nihil, eveniet aliquid culpa
                  officia aut! Impedit sit sunt quaerat, odit, tenetur error,
                  harum nesciunt ipsum debitis quas aliquid. Reprehenderit,
                  quia.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingFive">
                <button
                  class="accordion-button collapsed bg-dark text-light"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseFive"
                  aria-expanded="false"
                  aria-controls="collapseFive"
                >
                  Question No 5
                </button>
              </h2>
              <div
                id="collapseFive"
                class="accordion-collapse collapse"
                aria-labelledby="headingFive"
                data-bs-parent="#accordionExample"
              >
                <div class="accordion-body">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit.
                  Maxime mollitia, molestiae quas vel sint commodi repudiandae
                  consequuntur voluptatum laborum numquam blanditiis harum
                  quisquam eius sed odit fugiat iusto fuga praesentium optio,
                  eaque rerum! Provident similique accusantium nemo autem.
                  Veritatis obcaecati tenetur iure eius earum ut molestias
                  architecto voluptate aliquam nihil, eveniet aliquid culpa
                  officia aut! Impedit sit sunt quaerat, odit, tenetur error,
                  harum nesciunt ipsum debitis quas aliquid. Reprehenderit,
                  quia.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingSix">
                <button
                  class="accordion-button collapsed bg-dark text-light"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseSix"
                  aria-expanded="false"
                  aria-controls="collapseSix"
                >
                  Question No 6
                </button>
              </h2>
              <div
                id="collapseSix"
                class="accordion-collapse collapse"
                aria-labelledby="headingSix"
                data-bs-parent="#accordionExample"
              >
                <div class="accordion-body">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit.
                  Maxime mollitia, molestiae quas vel sint commodi repudiandae
                  consequuntur voluptatum laborum numquam blanditiis harum
                  quisquam eius sed odit fugiat iusto fuga praesentium optio,
                  eaque rerum! Provident similique accusantium nemo autem.
                  Veritatis obcaecati tenetur iure eius earum ut molestias
                  architecto voluptate aliquam nihil, eveniet aliquid culpa
                  officia aut! Impedit sit sunt quaerat, odit, tenetur error,
                  harum nesciunt ipsum debitis quas aliquid. Reprehenderit,
                  quia.
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- <hr class="border" /> -->
        <div class="last_text text-center text-white py-5 container">
          <h6>
            Ready to watch? Enter your email to create or restart your
            membership.
          </h6>
          <div
            class="
              d-flex
              justify-content-center
              align-items-center
              w-100
              mx-auto
              header_input
            "
          >
          <form name="getstarted" class = "d-flex w-100" method="post">
            <input class="form-control w-100" name = "emailbelow" placeholder="Email Address" />
            <button type="submit"
              class="btn text-nowrap pink-bg text-white btn-small"
              >Get Started
            </a>
          </form>
          </div>
        </div>
      </main>
    </div>

    <!-- <footer class="container-fluid bg-black border-top"> -->
    <footer
      class="container-fluid bg-black"
      style="background-color: #1d2227 !important"
    >
      <div class="container p-5 px-3 px-md-5">
        <div class="px-0 px-md-5">
          <div class="row">
            <div class="col-12">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
          </div>
          <div class="row my-5">
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
            <div class="col-md-2 col-sm-3 col-6">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <a href="#" class="link-light text-nowrap">Hello World</a>
            </div>
          </div>
        </div>
      </div>
    </footer> 
    <!-- JavaScript Bundle with Popper -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
