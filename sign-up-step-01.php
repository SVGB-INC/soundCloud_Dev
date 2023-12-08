<?php
session_start();
$_SESSION;

include("Config.php");
include("functions.php");

// if(isset($_SESSION["user_name"]))
// {
//   header("Location: home-page.php");
// }

if ($_SERVER['REQUEST_METHOD'] === "POST") {

  $chosenPlan = $_POST['pricePlan'];

  if ($chosenPlan == "Free") {
    // select query add by hamza
    $select_stmt = $pdo->prepare("SELECT user_name, user_email FROM reg_data_bank 
      WHERE user_name=:u_name"); // sql select query
    $select_stmt->execute(array(
      ':u_name' => $_SESSION["user_name"],
    )); //execute query
    $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

    if ($select_stmt->rowCount() < 1) {
      try {
        $insert_stmt = $pdo->prepare("INSERT INTO reg_data_bank(user_ID, user_name, first_name, last_name, user_email, date_creation, country, password,user_plan,user_news, isPremium) VALUES (?,?,?,?,?,?,?,?,?,?,?)"); //sql insert query
        if ($insert_stmt->execute([
          $_SESSION["insert_userid"], $_SESSION["insert_username"],
          $_SESSION["insert_fname"], $_SESSION["insert_lname"], $_SESSION["insert_useremail"],
          $_SESSION["insert_date"], $_SESSION["insert_country"], $_SESSION["insert_pass"],

          'Free', $_SESSION["insert_news"], "No"
        ])) {

          $registerMsg = "Registered Successfully..."; //execute query success message
          header("Location: sign-in.php");
        }
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
    }
  } else {
    header("Location: sign-up-step-02.php");
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
  <!-- cstm styles-->
  <link rel="stylesheet" href="./Styles/sign-up-step.css" />

  <!--<title>Cliliogori | Sign Up</title>-->
   <title>Chhogori | Sign Up</title>
</head>

<body class="sign-up-page">
  <header class="justify-content-center align-items-center">
    <nav class="navbar navbar_cstm navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand fs-3 fw-bolder" href="index.php"><img class="logo" style="height: 3rem" src="./images/Logo.png" alt="" /></a>
        <a href="sign-in.php" class="btn pink-bg text-white">Sign in</a>
      </div>
    </nav>
  </header>
  <style>
    [type="submit"] {
      width: 10rem;
    }
  </style>
  <div class="container mt-4">
    <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
      <h1 class="display-4 fw-normal">Select a Plan</h1>
      <p class="fs-5 text-muted">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer.</p>
    </div>
    <main class="mx-2">


      <!-- ----- end info --------- -->
      
        <div class="row row-cols-1 row-cols-md-2 mb-3 text-center gx-5">
          <form method="post">
            <div class="col">
              <input class="" name="pricePlan" type="radio" hidden value="Free" id="Free">
              <label for="Free" class="card mb-4 rounded-3">
                <!-- <div class="card mb-4 rounded-3/"> -->
                <div class="card-header py-3">
                  <h4 class="my-0 fw-normal">Free</h4>
                </div>
                <div class="card-body">
                  <h1 class="card-title pricing-card-title">$0<small class="text-muted fw-light">/mo</small></h1>
                  <ul class="list-unstyled mt-3 mb-4">
                    <li>Lorem Ipsum is dummy text</li>
                    <li>Lorem Ipsum is dummy text</li>
                    <li>Lorem Ipsum is dummy text</li>
                    <li>Lorem Ipsum is dummy text</li>
                  </ul>
                  <!-- <a href="sign-in.php" class="w-100 btn btn-lg btn-outline-primary">Sign Up for Free</a> -->
                  <!--<button type="submit" class="w-100 btn btn-lg btn-outline-primary" href="sign-in.php">Sign up for free</button>-->
                  <!-- <div class="form-check">
                                    
                                </div> -->
                </div>
              </label>
            </div>
            <button type="submit" class="btn btn-lg pink-bg text-center mx-auto text-white my-3 pay">Continue</button>
          </form>

          <form name="signupfree" method="post" action='<?php echo PAYPAL_URL; ?>'>
            <div class="col">
              <input class="" name="pricePlan" type="radio" hidden value="Paid" id="Paid" checked>
              <!-- <div class="card mb-4 rounded-3 card-checked"> -->
              <label for="Paid" class="card mb-4 rounded-3 card-checked">

                <div class="card-header py-3">
                  <h4 class="my-0 fw-normal">Pro</h4>
                </div>
                <div class="card-body">
                  <h1 class="card-title pricing-card-title">$15<small class="text-muted fw-light">/mo</small></h1>
                  <ul class="list-unstyled mt-3 mb-4">
                    <li>Lorem Ipsum is dummy text</li>
                    <li>Lorem Ipsum is dummy text</li>
                    <li>Lorem Ipsum is dummy text</li>
                    <li>Lorem Ipsum is dummy text</li>
                  </ul>
                  <!--<a href="sign-up-step-02.php" class="w-100 btn btn-lg btn-primary">Continue</a>-->
                  <!-- <button type="button" class="w-100 btn btn-lg btn-primary">Continue</button> -->
                  <!-- <div class="form-check"> -->
                  <!-- <input class="" name="pricePlan" type="checkbox" value="Paid" id="Paid" checked> -->
                  <!-- <label class="form-check-label" for="Paid">
                                        Continue With Pro Membership
                                    </label> -->
                  <!-- </div> -->
                </div>
              </label>
            </div>

            <!-- user info -->
          <input type='hidden' name='user_name' value='<?php echo $_SESSION["insert_userid"]; ?>'>
          <input type='hidden' name='amount' value='15'>

          <!-- paypal info -->
          <!-- PayPal business email to collect payments -->
          <input type='hidden' name='business' value='<?php echo PAYPAL_EMAIL; ?>'>

          <!-- Details of item that customers will purchase -->

          <input type='hidden' name='currency_code' value='<?php echo CURRENCY; ?>'>
          <input type='hidden' name='no_shipping' value='1'>

          <!-- PayPal return, cancel & IPN URLs -->
          <input type='hidden' name='return' value='<?php echo RETURN_URL; ?>'>
          <input type='hidden' name='cancel_return' value='<?php echo CANCEL_URL; ?>'>
          <input type='hidden' name='notify_url' value='<?php echo NOTIFY_URL; ?>'>
          <input type="hidden" name="cmd" value="_xclick">
          <button type="submit" class="btn btn-lg pink-bg text-center mx-auto text-white my-3 pay">Pay</button>
          
          </form>
        </div>
        
        
      
      <!-- <div class="d-flex justify-content-center align-items-center"> -->
       
          
        

      <!-- </div> -->
      
      <h2 class="display-6 text-center mb-4">Compare plans</h2>
      <div class="table-responsive mb-4">
        <table class="table text-center">
          <thead>
            <tr>
              <th style="width: 34%"></th>
              <th style="width: 33%">Free</th>
              <th style="width: 33%">Pro</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row" class="text-start">Lorem Ipsum</th>
              <td>Lorem Ipsum</td>
              <td>Lorem Ipsum</td>
            </tr>

            <tr>
              <th scope="row" class="text-start">Lorem Ipsum</th>
              <td>Lorem Ipsum</td>
              <td>Lorem Ipsum</td>
            </tr>
          </tbody>

          <tbody>
            <tr>
              <th scope="row" class="text-start">Lorem Ipsum</th>
              <td>Lorem Ipsum</td>
              <td>Lorem Ipsum</td>
            </tr>
            <tr>
              <th scope="row" class="text-start">Lorem Ipsum</th>
              <td>Lorem Ipsum</td>
              <td>Lorem Ipsum</td>
            </tr>
            <tr>
              <th scope="row" class="text-start">Lorem Ipsum</th>
              <td>Lorem Ipsum</td>
              <td>Lorem Ipsum</td>
            </tr>
            <tr>
              <th scope="row" class="text-start">Lorem Ipsum</th>
              <td>Lorem Ipsum</td>
              <td>Lorem Ipsum</td>
            </tr>
          </tbody>
        </table>
        <div class="container-fluid d-flex justify-content-center align-items-center">

        </div>
      </div>


    </main>
  </div>

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