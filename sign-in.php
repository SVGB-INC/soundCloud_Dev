<?php
session_start();
$_SESSION;

include ("Config.php");
include ("functions.php");


if(isset($_SESSION["user_name"]))
{
  header("Location: home-page.php");
}

$message = "";  

if ($_SERVER['REQUEST_METHOD'] === "POST")
{
    $email = $_POST['emailData'];
    $password = $_POST['passData'];

    // try
    // {

    //     $query = "SELECT * FROM reg_data_bank WHERE user_email = :useremail or user_name = :username";
    //     $statement = $pdo->prepare($query);
    //     $statement->execute(array(
    //         'useremail' => $email,
    //         'username' => $email
    //     ));

    //     $results = $select_stmt->fetch(PDO::FETCH_ASSOC);

    //     $count = $statement->rowCount();
    //     if ($count > 0)
    //     {
    //         $_SESSION["username"] = $email;
    //         header("Location: home-page.php");
    //     }
    //     else
    //     {
    //         $message = '<label>Wrong User Name or Password</label>';
    //     }

    // }
    // catch(PDOException $e)
    // {
    //     echo $e->getMessage();
    // }

    //the HTML PHP code
    // PHP START tag here
    //             if(isset($message))
    //             {  
    //               if(strlen($message) >= 1)
    //               {
    //               //echo '<label class="text-danger">'.$message.'</label>'; 
    //               echo '<div class="alert alert-danger text-center py-1 my-2" role="alert">' . $message . '</div>';
    //               }
    //             }  
    // PHP end tag here


    try
    {

    $select_stmt=$pdo->prepare("SELECT * FROM reg_data_bank WHERE user_name=:uname OR user_email=:uemail");
		$select_stmt->execute(array(':uname'=>$email, ':uemail'=>$email));
    $row=$select_stmt->fetch(PDO::FETCH_ASSOC);

    if($select_stmt->rowCount() > 0)	//check condition database record greater zero after continue
			{
				if($email==$row["user_name"] OR $email==$row["user_email"])
				{
					if(password_verify($password, $row["password"]))
					{
						$_SESSION["user_name"] = $row["user_name"];	//session name is "user_login"

            $sqlUserID = "SELECT user_id FROM reg_data_bank where user_name = '" . $_SESSION["user_name"] . "'";
            $_SESSION['user_id'] = $pdo->query($sqlUserID)->fetchAll(PDO::FETCH_COLUMN)[0];
            
            $_SESSION["userAirTime"] = 0;
            
            $sqlUserPremium = "SELECT isPremium FROM reg_data_bank where user_name = '" . $_SESSION['user_name'] . "'";
            $userPremium = $pdo->query($sqlUserPremium)->fetchAll(PDO::FETCH_COLUMN)[0];
            $_SESSION["isPremium"] = $userPremium;

						$loginMsg = "Successfully Logged In...";		//user login success message
						header("refresh:2; home-page.php");			//refresh 2 second after redirect to "welcome.php" page
					}
					else
					{
						$errorMsg[]="Wrong Password";
					}
				}
				else
				{
					$errorMsg[]="Wrong Username or Email";
				}
			}
			else
			{
				$errorMsg[]="Wrong Username or Email";
			}
    }
    catch(PDOException $e)
		{
			$e->getMessage();
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
     <title>Chhogori | Sign In</title>
  </head>
  <body>
       <script>
    setInterval(() => {
      sessionStorage.setItem("songState", "true");
    }, 200);
  </script>
    <header class="container-fluid landing-page-banner justify-content-center align-items-center">
      <nav class="navbar navbar_cstm navbar-expand-lg navbar-dark">
        <div class="container">
          <a class="navbar-brand fs-3 fw-bolder" href="./"><img class="logo" src="./images/Logo.png" alt="" /></a>
        </div>
      </nav>
      <div class="container">
        <div class="row">
          <div class="col-lg-4 d-none d-lg-block"></div>
          <div class="col-lg-4 p-0">
            <form class="form py-5 bg-black px-4 px-lg-4 bg-opacity-75" name = "signinform" method = "post">
              <h1 class="text-white mx-auto">Sign In</h1>
              <input
                name = "emailData"
                class="form-control mx-auto mt-4 text-white"
                placeholder="Username or Email"
                style="background-color: #333; border-radius: 5px"
                required=""
              />
              <input
                name = "passData"
                class="form-control mx-auto mt-3 mb-4 text-white"
                placeholder="Password"
                type="password"
                style="background-color: #333; border-radius: 5px"
                required=""
              />
              <div class="d-grid">
                <button type="submit" href="" class="btn pink-bg text-white mx-auto w-100 px-4" style="border-radius: 5px">Sign In</button>
              </div>
              <div class="form-check mx-auto mt-2 d-flex flex-wrap justify-content-between">
                <div class="chechin">
                  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" />
                  <label class="form-check-label gap-2" style="color: #737373" for="flexCheckDefault">Remember Me</label>
                </div>
                <a href="forget-password.php" class="text-decoration-none text-white">Forget Password?</a>
                </div>

                <!-- place PHP here for old code -->
                <?php
                  if (isset($errorMsg))
                  {
                    foreach ($errorMsg as $error)
                    {
                ?>
				          <div class="alert alert-danger text-center py-1 my-2">
					        <strong><?php echo $error; ?></strong>
				          </div>
                <?php
                    }
                  }
                if (isset($loginMsg))
                {
                ?>
			            <div class="alert alert-success text-center py-1 my-2">
				          <strong><?php echo $loginMsg; ?></strong>
			            </div>
                  <?php
                }
                ?>


              <!-- <div class="fb-login mt-4 mx-auto" data-uia="fb-login" bis_skin_checked="1">
                <img class="icon-facebook" style="width: 20px; height: 20px" src="images/fb-logo-blue.png" />
                <span class="fbBtnText" style="color: #737373">Login with Facebook</span>
              </div> -->
              <div class="mx-auto">
                <p class="mt-2" style="color: #737373">
                  New to our site?
                  <a href="sign-up.php" class="text-decoration-none text-white">Sign Up Now</a>
                </p>
              </div>
              <div class="text-white mx-auto">
                <p style="font-size: 14px">
                  This page is Protected by Google reCAPTCHA to ensure you're not a bad.<a href="" class="text-decoration-none">Learn More</a>
                </p>
              </div>
            </form>

            

          </div>
          <div class="col-lg-4 d-none d-lg-block"></div>
        </div>
      </div>
    </header>
  </body>
</html>
