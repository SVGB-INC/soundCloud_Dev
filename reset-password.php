<?php
session_start();
$_SESSION;

include("Config.php");
include("functions.php");


if ($_SERVER['REQUEST_METHOD'] === "POST") {

  $newPass = $_POST['newPass'];
  $newPassCon = $_POST['newPassCon'];
  $passCode = $_POST['passCode'];

  if ($newPass == $newPassCon) {
    try {
      $sql = "UPDATE reg_data_bank SET password=? WHERE user_email=?";
      $stmt = $pdo->prepare($sql);
      if ($stmt->execute([password_hash($newPass, PASSWORD_DEFAULT), $_SESSION["reset_userid"]])) {
        $reset_message = "Your Password Has Been Reset";
        header("refresh:2; sign-in.php");
      }
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
  } else {
    $errorMsg[] = "Passwords Don't Match"; //check condition email already exists
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
  <title>Reset Password</title>
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
            <h2 class="text-white mx-auto">Enter New Password</h2>
            <input type="password" name="newPass" class="form-control mx-auto mt-4 text-white" placeholder="New Password" style="background-color: #333; border-radius: 5px" required="" />
            <input type="password" name="newPassCon" class="form-control mx-auto mt-4 text-white" placeholder="Confirm New Password" style="background-color: #333; border-radius: 5px" required="" />
            <input type="text" name="passCode" class="form-control mx-auto mt-4 text-white" placeholder="Enter Security Code" style="background-color: #333; border-radius: 5px" required="" />
            <div class="d-grid mt-4">
              <button type="submit" href="" class="btn pink-bg text-white mx-auto w-100 px-4" style="border-radius: 5px">Update Password</button>
            </div>

            <?php
            if (isset($errorMsg)) {
              foreach ($errorMsg as $error) {
            ?>
                <div class="alert alert-danger text-center">
                  <strong><?php echo $error; ?></strong>
                </div>

              <?php
              }
            }
            if (isset($registerMsg)) {
              ?>
              <div class="alert alert-success text-center">
                <strong><?php echo $registerMsg; ?></strong>
              </div>
            <?php
            }
            ?>
            <?php
            if (isset($reset_message)) {
            ?>
              <div class="alert alert-success text-center py-1 my-2">
                <strong><?php echo $reset_message; ?></strong>
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