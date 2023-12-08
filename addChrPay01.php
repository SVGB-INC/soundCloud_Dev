<?php
session_start();
$_SESSION;

include("Config.php");
include("functions.php");


$user_data = check_login($pdo);

$audio_id = $_GET["pod"];

$sql = "select podcast_address from podcast_details where pID =" . $audio_id . ";";

$podcast_address = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN)[0];

$authorName = "select user_name from podcast_details where pID =" . $audio_id . ";";
$authorNameNew = $pdo->query($authorName)->fetchAll(PDO::FETCH_COLUMN)[0];

$authorPP = "select paypal from user_detail where user ='" . $authorNameNew . "';";
$authorPayPal = $pdo->query($authorPP)->fetchAll(PDO::FETCH_COLUMN)[0];

$sql = $pdo->prepare("SELECT pp.podcast_payment, pp.podcast_charity, pp.website_url, pp.name, pp.charity_acc from podcast_details as pd inner join podcast_payments as pp
on pd.pID = pp.pID where pd.pID = " . $audio_id . " LIMIT 1;");
$sql->execute();
$payDetails = $sql->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  //PayPal Functions

  try {
    $select_stmt = $pdo->prepare("SELECT user_name FROM user_podcast_access 
										WHERE user_name=:uname"); // sql select query
    $select_stmt->execute([
      ":uname" => $_SESSION["user_name"],
    ]); //execute query
    $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

    if ($select_stmt->rowCount() > 0) {

      $sql = "UPDATE user_podcast_access SET podcast_payment=CONCAT(podcast_payment,',','" . $podcast_address . "') ,  podcast_charity= CONCAT(podcast_charity,',','" . $podcast_address . "') WHERE user_name=?";
      $stmt = $pdo->prepare($sql);

      if ($stmt->execute([$_SESSION["user_name"]])) {


        //getting admin and creator share
        $select_stmt = $pdo->prepare("SELECT add_share_admin, add_share_creator FROM admin_details LIMIT 1;");
        $select_stmt->execute(); //execute query
        $admin_details = $select_stmt->fetch(PDO::FETCH_ASSOC);
        //getting admin and creator share

        //insert payment details
        $date = date("Y-m-d");
        $insert_stmt = $pdo->prepare("INSERT INTO podcast_payment_history(pID, author, user, admin_payment, creator_payment, charity, date) VALUES (?,?,?,?,?,?,?)"); //sql insert query
        $insert_stmt->execute([$audio_id, $authorNameNew, $_SESSION["user_name"], ($_POST["additional-pay-ammount"] * ($admin_details["add_share_admin"] / 100)), ($_POST["additional-pay-ammount"] * ($admin_details["add_share_creator"] / 100)), $payDetails["podcast_charity"], $date]);
        //insert payment details



        // $admin_share = $_POST["additional-pay-ammount"] * ($admin_details["add_share_admin"]/100);
        // $creator_share = $_POST["additional-pay-ammount"] * ($admin_details["add_share_creator"]/100);

        // $insert_stmt = $pdo->prepare("INSERT INTO payment_final(pID, add_share_admin, add_share_creator, user_pay_ID) VALUES (?,?,?,?)"); 
        // $insert_stmt->execute([$audio_id, $admin_share  , $creator_share , $_SESSION['user_id']]);

        $upgrade_message = "You Have Successfully Upgraded";
        header("refresh:2; player.php?id=" . $podcast_address);
      }
    } else {
      $insert_stmt = $pdo->prepare("INSERT INTO user_podcast_access(user_name, podcast_payment, podcast_charity) VALUES (?,?,?)"); //sql insert query
      if ($insert_stmt->execute([$_SESSION["user_name"], $audio_id, $audio_id])) {


         //getting admin and creator share
         $select_stmt = $pdo->prepare("SELECT add_share_admin, add_share_creator FROM admin_details LIMIT 1;");
         $select_stmt->execute(); //execute query
         $admin_details = $select_stmt->fetch(PDO::FETCH_ASSOC);
         //getting admin and creator share
 
         //insert payment details
         $date = date("Y-m-d");
         $insert_stmt = $pdo->prepare("INSERT INTO podcast_payment_history(pID, author, user, admin_payment, creator_payment, charity, date) VALUES (?,?,?,?,?,?,?)"); //sql insert query
         $insert_stmt->execute([$audio_id, $authorNameNew, $_SESSION["user_name"], ($_POST["additional-pay-ammount"] * ($admin_details["add_share_admin"] / 100)), ($_POST["additional-pay-ammount"] * ($admin_details["add_share_creator"] / 100)), $payDetails["podcast_charity"], $date]);
         //insert payment details
         

        // $select_stmt = $pdo->prepare("SELECT add_share_admin, add_share_creator FROM admin_details LIMIT 1;");
        // $select_stmt->execute(); //execute query
        // $admin_details = $select_stmt->fetch(PDO::FETCH_ASSOC);

        // $admin_share = $_POST["additional-pay-ammount"] * ($admin_details[0]/100);
        // $creator_share = $_POST["additional-pay-ammount"] * ($admin_details[1]/100);

        // $insert_stmt = $pdo->prepare("INSERT INTO payment_final(pID, add_share_admin, add_share_creator, user_pay_ID) VALUES (?,?,?,?)"); 
        // $insert_stmt->execute([$audio_id, $admin_share  , $creator_share , $_SESSION['user_id']]);

        $upgrade_message = "You Have Successfully Upgraded";
        header("refresh:2; player.php?id=" . $podcast_address);
      }
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
  <!--<title>Cliliogori | Upgrade</title>-->
  <title>Chhogori | Upgrade</title>
</head>

<body class="sign-up-page">
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
    <div class="container pt-5">
      <div class="row pt-5">
        <div class="col-md-2 d-none d-md-block"></div>
        <div class="col-md-8 p-0">
          <form class="form py-5 px-md-4 px-2 px-lg-4 bg-opacity-75" name="signupform" method="post" style="min-height: 33rem;">
            <h1 class="text-center">Optional Payment and Additional Charity</h1>
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


                <!-- <div class="input my-1">
                  <label class="text-start" for="payPalName">PayPal Account *</label>
                  <input type="text" class="form-control" required name="payPalName" placeholder="PayPal Account" maxlength="16" />

                </div> -->
              </div>
              <div class="payment-select-container">
                <h6 class="h6">Select Payment</h6>
                <!-- <input type="checkbox" name="additional-pay" id="additional-pay">
                <label for="additional-pay">Additional</label>
                <input type="checkbox" name="charity-pay" id="charity-pay">
                <label for="charity-pay">Charity</label> -->
                <div class="form-check d-flex justify-content-between">
                  <div class="input d-flex justify-content-center align-items-center gap-3 text-md-nowrap">
                    <input class="form-check-input" type="checkbox" value="" checked name="additional-pay" id="additional-pay">
                    <label class="form-check-label" for="additional-pay">
                      Additional Payment
                    </label>
                    <label class=" form-check-label">To <a class="link-primary" href=<?php echo "profile.php?user=" . $authorNameNew; ?>><?php echo $authorNameNew; ?><?= " [ " . $authorPayPal . " ] "  ?></a></label>
                  </div>
                  <div class="input d-flex justify-content-center align-items-center gap-2">
                    <label class="ms-auto form-check-label" for="additional-pay">
                      Amount USD
                    </label>
                    <input class="form-control p-1" style="width: 5rem;" type="text" value=<?php echo $payDetails["podcast_payment"]; ?> name="additional-pay-ammount" id="additional-pay-ammount">
                  </div>
                </div>
                <div class="form-check d-flex justify-content-between">
                  <div class="input d-flex justify-content-center align-items-center gap-3 text-md-nowrap">
                    <input class="form-check-input" type="checkbox" value="" checked disabled name="charity-pay" id="charity-pay">
                    <label class="form-check-label opacity-100" for="charity-pay">
                      Charity Payment
                    </label>
                    <label class=" form-check-label opacity-100">

                      To Charity: <a class="link-primary" target="_blank" href="<?php echo $payDetails["website_url"]; ?>"><?= $payDetails["name"]; ?><?php echo " [ " . $payDetails['charity_acc'] . " ]"; ?></a>
                    </label>
                  </div>
                  <div class="input d-flex justify-content-center align-items-center gap-2">
                    <label class="ms-auto form-check-label" for="additional-pay">
                      Amount USD
                    </label>
                    <input class="form-control p-1" style="width: 5rem;" type="text" value=<?php echo $payDetails["podcast_charity"]; ?> disabled name="must-pay-charity" id="must-pay-charity">
                  </div>
                </div>
                <!-- <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" name="additional-pay" id="additional-pay">
                  <label class="form-check-label" for="additional-pay" >
                    Additional Payment
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" checked disabled name="charity-pay" id="charity-pay">
                  <label class="form-check-label" for="charity-pay">
                    Charity Payment
                  </label>
                </div> -->
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
              <div id="paypal_payment">

              </div>
              <style>
                #paypal_payment {
                  padding-top: 20px !important;
                }
              </style>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID ?>&disable-funding=credit,card"></script>

<script>
  let charity_acc = '<?= $payDetails["charity_acc"] ?>';
  let auth_acc = '<?= $authorPayPal ?>';

  let charity_payment = document.getElementById('must-pay-charity').value;

  let additional_payment_element = document.getElementById('additional-pay-ammount');
  let additional_payment = additional_payment_element.value;

  let toal_payment = parseFloat(additional_payment) + parseFloat(charity_payment)

  additional_payment_element.addEventListener('keyup', function(e) {
    additional_payment = e.target.value;
    toal_payment = parseFloat(additional_payment) + parseFloat(charity_payment);

  })

  paypal.Buttons({
    style: {
      color: 'white',
      shape: 'pill',
      label: 'pay',
      shape: 'rect'

    },
    createOrder: (data, actions) => {
      return actions.order.create({

        purchase_units: [{
          reference_id: "REFID-1",
          amount: {
            value: toal_payment
            // value: '400' 
          },
          // payee: {
          //       email_address: "sb-mkjba16849544@business.example.com",
          //       merchant_id: "QT4WXVVS6N6DU"
          // }
        }]
      });
    },

    onApprove: (data, actions) => {
      return actions.order.capture().then(function(orderData) {

        console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
        const transaction = orderData.purchase_units[0].payments.captures[0];
        setTimeout(() => {
          $.ajax({
            url: "./paypal/get_access_token.php",
            method: "POST",
            data: {
              additional_payment,
              charity_payment,
              charity_acc,
              auth_acc
            },
            success: function(data) {
              console.log(data);
              document.forms[0].submit();
            },
            error: function(xhr, status, error) {
              console.error(xhr);
            },
          });

        }, 1000);
        alert(`Transaction ${transaction.status}: ${transaction.id}\n\nSee console for all available details`);

      });
    }

  }).render('#paypal_payment')
</script>

</html>