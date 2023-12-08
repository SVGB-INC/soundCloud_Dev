<?php
session_start();
$_SESSION;

include("Config.php");
include("functions.php");


$user_data = check_login($pdo);

if ($_SERVER['REQUEST_METHOD'] === "POST") {

  try {
    $sql = "UPDATE reg_data_bank SET isPremium=? WHERE user_name=?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute(["Yes", $_SESSION["user_name"]])) {
      $upgrade_message = "You Have Successfully Upgraded";
      header("refresh:2; home-page.php");
    }
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- bootstrap cdn -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
  <!-- fontawesome cdn -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <link rel="stylesheet" href="./Styles/styles.css" />
  <title>Chhogori | Upgrade</title>
</head>

<body class="sign-up-page">
  <header class="justify-content-center align-items-center">
    <nav class="navbar navbar_cstm navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand fs-3 fw-bolder" href="index.php"><img class="logo" style="height: 3rem" src="./images/Logo.png" alt="" /></a>
        <a href="sign-in.php" class="btn pink-bg text-white">Sign in</a>
      </div>
    </nav>
    <div class="container">
      <div class="row">
        <div class="col-md-2 d-none d-md-block"></div>
        <div class="col-md-8 p-0">
          <form class="form py-5 px-md-4 px-2 px-lg-4 bg-opacity-75" name="signupform" method="post" style="min-height: 33rem;">
            <div class="container-section">
              <h5 class="text-dark text-start mt-3">Choose Payment Method</h5>
              <!-- <div class="row mt-2">
                <div class="col-md-6">
                  <input type="radio" checked name="paymentMethod" class="me-2" id="cardPayment" />
                  <label for="cardPayment"><i class="fas text-primary fa-credit-card"></i> Credit Card</label>
                </div>
                <div class="col-md-6">
                  <input type="radio" name="paymentMethod" class="me-2" id="paypalPayment" /><label for="paypalPayment"><i class="fab text-primary fa-paypal"></i> Paypal</label>
                </div>
              </div> -->
              <!-- <div class="payment-select-container cardPaymentContainer">
                <div class="input my-1">
                  <label class="text-start" for="creditCardName">Name on Card *</label>
                  <input type="text" class="form-control" name="creditCardName" placeholder="Name on Card" maxlength="16" required />
                </div>
                <div class="input my-1">
                  <label class="text-start" for="creditCardNumber">Card Number *</label>
                  <input type="number" class="form-control" name="creditCardNumber" placeholder="Card Number" maxlength="16" required />
                </div>
                <div class="input my-1">
                  <label class="text-start" for="creditExpirationMonth">Expiry Date *</label>
                  <input class="form-control" name="creditExpirationMonth" placeholder="MM/YYYY" required type="month" />
                </div>
                <div class="input my-1">
                  <label class="text-start" for="creditCVV">Security Code *</label>
                  <input class="form-control" id="creditCVV" required placeholder="Security Code (CVV)" maxlength="3" type="tel" />
                </div>
              </div> -->
              <div class="payment-select-container paypalPaymentContainer">


                <div class="input my-1">
                  <label class="text-start" for="payPalName">PayPal Account *</label>
                  <input type="text" class="form-control" required name="payPalName" placeholder="PayPal Account" maxlength="16" />

                </div>
              </div>
              <!-- <div class="form-check d-flex align-items-center justify-content-start">
                  <input class="form-check-input" name="newsletters" type="checkbox" value="" id="newsletters" />
                  <label class="text-start" class="form-check-label" for="newsletters"> Sign Up for Newsletters</label>
                </div> -->

              <!-- <h5 class="text-black text-center mt-2 fs-2 fw-bolder">Finish setting up your Account</h5> -->
              <!-- <p class="paragraph text-black text-center my-3">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed incidunt maxime officiis ipsam.
                </p> -->

              <!-- <div class="d-grid">
                  <a type="submit" href="Signup2.php" class="btn btn-danger mx-auto px-4 form-control" style="border-radius: 5px"> Next </a>
                </div> -->
              <!-- <form name="signuppaid" method="post"> -->
              <button type="submit" class="btn mt-3 pink-bg text-white w-100">Submit</button>
              <!-- </form> -->
            </div>
          </form>

          <?php if (isset($upgrade_message)) {
          ?>
            <div class="alert alert-success text-center py-1 my-2">
              <strong><?php echo $upgrade_message; ?></strong>
            </div>
          <?php
          }
          ?>

        </div>
        <div class="col-md-2 d-none d-md-block"></div>
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
  <!-- <script src="./js/sign-up.js"></script> -->
  <!--<script src="https://gist.github.com/incredimike/1469814.js"></script>-->
  <!--<script>-->
  <!--  var select = document.getElementById("selectCountry");-->

  <!--  for (var i = 0; i < countryList.length; i++) -->
  <!--  {-->
  <!--      var opt = countryList[i];-->
  <!--      var el = document.createElement("option");-->
  <!--      el.textContent = opt;-->
  <!--      el.value = opt;-->
  <!--      select.appendChild(el);-->
  <!--  }-->
  <!--</script>-->
</body>

</html>