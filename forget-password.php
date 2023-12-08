<?php
session_start();
$_SESSION;

include("Config.php");
include("functions.php");



if ($_SERVER['REQUEST_METHOD'] === "POST") {

  $user_email = $_POST['emailData'];

  if (!empty($user_email)) {

    try {
      $select_stmt = $pdo->prepare("SELECT user_email FROM reg_data_bank 
										WHERE user_email=:uname"); // sql select query
      $select_stmt->execute(array(
        ':uname' => $user_email
      )); //execute query
      $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
      if ($select_stmt->rowCount() > 0) {
        $_SESSION["reset_userid"] = $user_email;

        try {
          $insert_stmt = $pdo->prepare("INSERT INTO reset_pass(user_email,user_code) VALUES (?,?)"); //sql insert query
          if ($insert_stmt->execute([$user_email,mt_rand(1111,9999)])) {

            $registerMsg = "We have sent a code to your email for password reset"; //execute query success message
            header("Location: reset-password.php");
          }
        } catch (PDOException $e) {
          echo $e->getMessage();
        }
      } else {

        $errorMsg[] = "This Email Doesn't Exist."; //check condition email already exists

      }
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
  }
}

?>

<!DOCTYPE html>
<html>

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />

  <link rel="stylesheet" href="./Styles/styles.css" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Forget Password</title>
</head>

<body>
  <header class="container-fluid landing-page-banner justify-content-center align-items-center">
    <nav class="navbar navbar_cstm navbar-expand-lg navbar-dark">
      <div class="container">
        <a class="navbar-brand fs-3 fw-bolder" href="./"><img class="logo" src="./images/Logo.png" alt="" /></a>
      </div>
    </nav>
    <div class="container d-flex h-100">
      <div class="row w-100 justify-content-center align-self-center">
        <div class="col-lg-4 d-none d-lg-block"></div>
        <div class="col-lg-4 p-0">
          <form class="form py-5 bg-black px-4 px-lg-4 bg-opacity-75" name="signinform" method="post">
            <h2 class="text-white mx-auto">Enter Email to Reset Password</h2>
            <input type="email" name="emailData" class="form-control mx-auto mt-4 text-white" placeholder="Email" style="background-color: #333; border-radius: 5px" required="" />
            <div class="d-grid mt-4">
              <button type="submit" class="btn pink-bg text-white mx-auto w-100 px-4" style="border-radius: 5px">Continue</button>
            </div>

            <?php
            if (isset($errorMsg)) {
              foreach ($errorMsg as $error) {
            ?>
                <div class="alert alert-danger text-center my-3">
                  <strong><?php echo $error; ?></strong>
                </div>

              <?php
              }
            }
            if (isset($registerMsg)) {
              ?>
              <div class="alert alert-success text-center my-3">
                <strong><?php echo $registerMsg; ?></strong>
              </div>
            <?php
            }
            ?>

          </form>
        </div>
        <div class="col-lg-4 d-none d-lg-block"></div>
      </div>
    </div>
  </header>
</body>

</html>