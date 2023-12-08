<?php
session_start();
$_SESSION;

include("Config.php");
include("functions.php");

if(isset($_SESSION["user_name"]))
{
  header("Location: home-page.php");
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

  $first_name = $_POST['fname'];
  $last_name = $_POST['lname'];
  $user_name = $_POST['usrname'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $country = $_POST['select-country'];
  $news = $_POST['newsletters'] ? "Yes" : "No";


  if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($password) && !empty($user_name)) {

    $user_id = str_replace('.', '', time() . uniqid(rand(), true));
    $date = date('Y-m-d');
    $hashPass = password_hash($password, PASSWORD_DEFAULT);

    // echo $user_id;
    // $query = "INSERT INTO reg_data_bank(user_ID, user_name, first_name, last_name, user_email, date_creation, country, password) VALUES('$user_id','$user_name','$first_name', '$last_name','$email','$date','$country',
    // '$hashPass')";
    // echo $query;
    // try
    // {
    //     $sql = "INSERT INTO reg_data_bank(user_ID, user_name, first_name, last_name, user_email, date_creation, country, password) VALUES (?,?,?,?,?,?,?,?)";
    //     $stmt = $pdo->prepare($sql);
    //     $stmt->execute([$user_id, $user_name, $first_name, $last_name, $email, $date, $country, $hashPass]);
    // }
    // catch(PDOException $e)
    // {
    //   if ($e->errorInfo[1] === 1062)
    //   {
    //     if(strpos($e->getMessage(), 'uniqueUserEmail') !== false)
    //     {
    //       echo "Email Already Taken";
    //     }
    //     else if(strpos($e->getMessage(), 'uniqueUserName') !== false)
    //     {
    //       echo "User Name Already Taken";
    //     }
    //     else
    //     {
    //       echo $e->getMessage();
    //       // echo '\n\n';
    //       // echo "This username is already taken!";
    //     }
    //   }
    // }


    try {
      $select_stmt = $pdo->prepare("SELECT user_name, user_email FROM reg_data_bank 
										WHERE user_name=:uname OR user_email=:uemail"); // sql select query
      $select_stmt->execute(array(
        ':uname' => $user_name,
        ':uemail' => $email
      )); //execute query
      $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
      if ($select_stmt->rowCount() > 0) {
        if ($row["user_name"] == $user_name) {
          $errorMsg[] = "The username already exists"; //check condition username already exists

        } else if ($row["user_email"] == $email) {
          $errorMsg[] = "This email is already registered"; //check condition email already exists

        }
      } else {

        $_SESSION["insert_userid"] = $user_id;
        $_SESSION["insert_username"] = $user_name;
        $_SESSION["insert_fname"] = $first_name;
        $_SESSION["insert_lname"] = $last_name;
        $_SESSION["insert_useremail"] = $email;
        $_SESSION["insert_date"] = $date;
        $_SESSION["insert_country"] = $country;
        $_SESSION["insert_pass"] = $hashPass;
        $_SESSION["insert_news"] = $news;

        header("Location: sign-up-step-01.php");

        // $insert_stmt = $pdo->prepare("INSERT INTO reg_data_bank(user_ID, user_name, first_name, last_name, user_email, date_creation, country, password) VALUES (?,?,?,?,?,?,?,?)"); //sql insert query

        // if ($insert_stmt->execute([$user_id, $user_name, $first_name, $last_name, $email, $date, $country, $hashPass]))
        // {

        // $registerMsg = "Registered Successfully..."; //execute query success message
        // header("Location: sign-up-step-01.php");

        // }

      }
    } catch (PDOException $e) {
      echo $e->getMessage();
    }

    // mysqli_query($con, $query);
    //header("Location: sign-in.php");
    //echo "Inserted";
    //die;

  } else {

    //echo "Kindly Fill Out All Fields";

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
    <div class="container">
      <div class="row">
        <div class="col-md-2 d-none d-md-block"></div>

        

        <div class="col-md-8 p-0">
        
          <form class="form py-5 px-md-4 px-2 px-lg-4 bg-opacity-75" name="signupform" method="post">
          
            <div class="container-section">
            <?php
        if (isset($errorMsg)) {
          foreach ($errorMsg as $error) {
        ?>
            <div class="alert alert-danger text-center">
              <strong><?php echo $error; ?></strong>
              <a href="forget-password.php" class="text-decoration-none">(Forgot Password?)</a>
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

              <h5 class="text-dark text-start mt-3">Account Info</h5>
              <div class="row px-0">
                <div class="input my-1 px-0 col-md-6 pe-1">
                  <label class="text-start" for="fname">First Name</label>
                  <input class="form-control" name="fname" placeholder="First Name" type="name" required />
                </div>
                <div class="input my-1 px-0 col-md-6 ps-1">
                  <label class="text-start" for="lname">Last Name</label>
                  <input class="form-control" name="lname" placeholder="Last Name" type="name" required />
                </div>
              </div>
              <div class="input my-1">
                <label class="text-start" for="usrname">User Name</label>
                <input class="form-control" name="usrname" placeholder="User Name" type="name" required />
              </div>
              <div class="input my-1">
                <label class="text-start" for="email">Email</label>
                <?php

                if (!empty($_SESSION['getstarted_email'])) {

                  //echo $_SESSION['getstarted_email'];
                  echo '<input class="form-control" name="email" type="email" value=' . $_SESSION['getstarted_email'] . ' required />';
                } else {

                  //echo "HI";
                  echo '<input class="form-control" name="email" placeholder="email@gmail.com" type="email" required />';
                }

                ?>


              </div>
              <div class="input my-1">
                <label class="text-start" for="select-country">Select Country</label>
                <select class="form-select" name="select-country" id="selectCountry">
                  <option value="Afghanistan">Afghanistan</option>
                  <option value="Aland Islands">Aland Islands</option>
                  <option value="Albania">Albania</option>
                  <option value="Algeria">Algeria</option>
                  <option value="American Samoa">American Samoa</option>
                  <option value="Andorra">Andorra</option>
                  <option value="Angola">Angola</option>
                  <option value="Anguilla">Anguilla</option>
                  <option value="Antarctica">Antarctica</option>
                  <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                  <option value="Argentina">Argentina</option>
                  <option value="Armenia">Armenia</option>
                  <option value="Aruba">Aruba</option>
                  <option value="Australia">Australia</option>
                  <option value="Austria">Austria</option>
                  <option value="Azerbaijan">Azerbaijan</option>
                  <option value="The Bahamas">The Bahamas</option>
                  <option value="Bahrain">Bahrain</option>
                  <option value="Bangladesh">Bangladesh</option>
                  <option value="Barbados">Barbados</option>
                  <option value="Belarus">Belarus</option>
                  <option value="Belgium">Belgium</option>
                  <option value="Belize">Belize</option>
                  <option value="Benin">Benin</option>
                  <option value="Bermuda">Bermuda</option>
                  <option value="Bhutan">Bhutan</option>
                  <option value="Plurinational State of Bolivia">Plurinational State of Bolivia</option>
                  <option value="Bonaire Sint Eustatius and Saba">Bonaire, Sint Eustatius and Saba</option>
                  <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                  <option value="Botswana">Botswana</option>
                  <option value="Bouvet Island">Bouvet Island</option>
                  <option value="Brazil">Brazil</option>
                  <option value="The British Indian Ocean Territory">The British Indian Ocean Territory</option>
                  <option value="Brunei Darussalam">Brunei Darussalam</option>
                  <option value="Bulgaria">Bulgaria</option>
                  <option value="Burkina Faso">Burkina Faso</option>
                  <option value="Burundi">Burundi</option>
                  <option value="Cabo Verde">Cabo Verde</option>
                  <option value="Cambodia">Cambodia</option>
                  <option value="Cameroon">Cameroon</option>
                  <option value="Canada">Canada</option>
                  <option value="The Cayman Islands">The Cayman Islands</option>
                  <option value="The Central African Republic">The Central African Republic</option>
                  <option value="Chad">Chad</option>
                  <option value="Chile">Chile</option>
                  <option value="China">China</option>
                  <option value="Christmas Island">Christmas Island</option>
                  <option value="">The Cocos (Keeling) Islands</option>
                  <option value="Colombia">Colombia</option>
                  <option value="The Comoros">The Comoros</option>
                  <option value="The Democratic Republic of The Congo">The Democratic Republic of The Congo</option>
                  <option value="The Congo">The Congo</option>
                  <option value="The Cook Islands">The Cook Islands</option>
                  <option value="Costa Rica">Costa Rica</option>
                  <option value="Croatia">Croatia</option>
                  <option value="Cuba">Cuba</option>
                  <option value="Curaçao">Curaçao</option>
                  <option value="Cyprus">Cyprus</option>
                  <option value="Czechia">Czechia</option>
                  <option value="Cote dIvoire">Cote dIvoire</option>
                  <option value="Denmark">Denmark</option>
                  <option value="Djibouti">Djibouti</option>
                  <option value="Dominica">Dominica</option>
                  <option value="The Dominican Republic">The Dominican Republic</option>
                  <option value="Ecuador">Ecuador</option>
                  <option value="Egypt">Egypt</option>
                  <option value="El Salvador">El Salvador</option>
                  <option value="Equatorial Guinea">Equatorial Guinea</option>
                  <option value="Eritrea">Eritrea</option>
                  <option value="Estonia">Estonia</option>
                  <option value="Eswatini">Eswatini</option>
                  <option value="Ethiopia">Ethiopia</option>
                  <option value="The Falkland Islands [Malvinas]">The Falkland Islands [Malvinas]</option>
                  <option value="The Faroe Islands">The Faroe Islands</option>
                  <option value="Fiji">Fiji</option>
                  <option value="Finland">Finland</option>
                  <option value="France">France</option>
                  <option value="French Guiana">French Guiana</option>
                  <option value="French Polynesia">French Polynesia</option>
                  <option value="The French SouThern Territories">The French SouThern Territories</option>
                  <option value="Gabon">Gabon</option>
                  <option value="The Gambia">The Gambia</option>
                  <option value="Georgia">Georgia</option>
                  <option value="Germany">Germany</option>
                  <option value="Ghana">Ghana</option>
                  <option value="Gibraltar">Gibraltar</option>
                  <option value="Greece">Greece</option>
                  <option value="Greenland">Greenland</option>
                  <option value="Grenada">Grenada</option>
                  <option value="Guadeloupe">Guadeloupe</option>
                  <option value="Guam">Guam</option>
                  <option value="Guatemala">Guatemala</option>
                  <option value="Guernsey">Guernsey</option>
                  <option value="Guinea">Guinea</option>
                  <option value="Guinea-Bissau">Guinea-Bissau</option>
                  <option value="Guyana">Guyana</option>
                  <option value="Haiti">Haiti</option>
                  <option value="Heard Island and McDonald Islands">Heard Island and McDonald Islands</option>
                  <option value="Holy See">Holy See</option>
                  <option value="Honduras">Honduras</option>
                  <option value="Hong Kong">Hong Kong</option>
                  <option value="Hungary">Hungary</option>
                  <option value="Iceland">Iceland</option>
                  <option value="India">India</option>
                  <option value="Indonesia">Indonesia</option>
                  <option value="Islamic Republic of Iran">Islamic Republic of Iran</option>
                  <option value="Iraq">Iraq</option>
                  <option value="Ireland">Ireland</option>
                  <option value="Isle of Man">Isle of Man</option>
                  <option value="Israel">Israel</option>
                  <option value="Italy">Italy</option>
                  <option value="Jamaica">Jamaica</option>
                  <option value="Japan">Japan</option>
                  <option value="Jersey">Jersey</option>
                  <option value="Jordan">Jordan</option>
                  <option value="Kazakhstan">Kazakhstan</option>
                  <option value="Kenya">Kenya</option>
                  <option value="Kiribati">Kiribati</option>
                  <option value="The Republic of Korea">The Republic of Korea</option>
                  <option value="Kuwait">Kuwait</option>
                  <option value="Kyrgyzstan">Kyrgyzstan</option>
                  <option value="The Lao People's Democratic Republic">The Lao People's Democratic Republic</option>
                  <option value="Latvia">Latvia</option>
                  <option value="Lebanon">Lebanon</option>
                  <option value="Lesotho">Lesotho</option>
                  <option value="Liberia">Liberia</option>
                  <option value="Libya">Libya</option>
                  <option value="Liechtenstein">Liechtenstein</option>
                  <option value="Lithuania">Lithuania</option>
                  <option value="Luxembourg">Luxembourg</option>
                  <option value="Macao">Macao</option>
                  <option value="Madagascar">Madagascar</option>
                  <option value="Malawi">Malawi</option>
                  <option value="Malaysia">Malaysia</option>
                  <option value="Maldives">Maldives</option>
                  <option value="Mali">Mali</option>
                  <option value="Malta">Malta</option>
                  <option value="The Marshall Islands">The Marshall Islands</option>
                  <option value="Martinique">Martinique</option>
                  <option value="Mauritania">Mauritania</option>
                  <option value="Mauritius">Mauritius</option>
                  <option value="Mayotte">Mayotte</option>
                  <option value="Mexico">Mexico</option>
                  <option value="Federated States of Micronesia">Federated States of Micronesia</option>
                  <option value="The Republic of Moldova">The Republic of Moldova</option>
                  <option value="Monaco">Monaco</option>
                  <option value="Mongolia">Mongolia</option>
                  <option value="Montenegro">Montenegro</option>
                  <option value="Montserrat">Montserrat</option>
                  <option value="Morocco">Morocco</option>
                  <option value="Mozambique">Mozambique</option>
                  <option value="Myanmar">Myanmar</option>
                  <option value="Namibia">Namibia</option>
                  <option value="Nauru">Nauru</option>
                  <option value="Nepal">Nepal</option>
                  <option value="NeTherlands">The NeTherlands</option>
                  <option value="New Caledonia">New Caledonia</option>
                  <option value="New Zealand">New Zealand</option>
                  <option value="Nicaragua">Nicaragua</option>
                  <option value="Niger">Niger (The)</option>
                  <option value="Nigeria">Nigeria</option>
                  <option value="Niue">Niue</option>
                  <option value="Norfolk Island">Norfolk Island</option>
                  <option value="The NorThern Mariana Islands">The NorThern Mariana Islands</option>
                  <option value="Norway">Norway</option>
                  <option value="Oman">Oman</option>
                  <option value="Pakistan">Pakistan</option>
                  <option value="Palau">Palau</option>
                  <option value="Palestine">Palestine</option>
                  <option value="Panama">Panama</option>
                  <option value="Papua New Guinea">Papua New Guinea</option>
                  <option value="Paraguay">Paraguay</option>
                  <option value="Peru">Peru</option>
                  <option value="Philippines">The Philippines</option>
                  <option value="Pitcairn">Pitcairn</option>
                  <option value="Poland">Poland</option>
                  <option value="Portugal">Portugal</option>
                  <option value="Puerto Rico">Puerto Rico</option>
                  <option value="Qatar">Qatar</option>
                  <option value="Republic of North Macedonia">Republic of North Macedonia</option>
                  <option value="Romania">Romania</option>
                  <option value="The Russian Federation">The Russian Federation</option>
                  <option value="Rwanda">Rwanda</option>
                  <option value="Reunion">Reunion</option>
                  <option value="Saint Barthelemy">Saint Barthelemy</option>
                  <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                  <option value="Saint Lucia">Saint Lucia</option>
                  <option value="Saint Martin - French part">Saint Martin - French part</option>
                  <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                  <option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option>
                  <option value="Samoa">Samoa</option>
                  <option value="San Marino">San Marino</option>
                  <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                  <option value="Saudi Arabia">Saudi Arabia</option>
                  <option value="Senegal">Senegal</option>
                  <option value="Serbia">Serbia</option>
                  <option value="Seychelles">Seychelles</option>
                  <option value="Sierra Leone">Sierra Leone</option>
                  <option value="Singapore">Singapore</option>
                  <option value="Sint Maarten">Sint Maarten</option>
                  <option value="Slovakia">Slovakia</option>
                  <option value="Slovenia">Slovenia</option>
                  <option value="Solomon Islands">Solomon Islands</option>
                  <option value="Somalia">Somalia</option>
                  <option value="South Africa">South Africa</option>
                  <option value="South Sudan">South Sudan</option>
                  <option value="Spain">Spain</option>
                  <option value="Sri Lanka">Sri Lanka</option>
                  <option value="Sudan">Sudan</option>
                  <option value="Suriname">Suriname</option>
                  <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                  <option value="Sweden">Sweden</option>
                  <option value="Switzerland">Switzerland</option>
                  <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                  <option value="Taiwan">Taiwan</option>
                  <option value="Tajikistan">Tajikistan</option>
                  <option value="United Republic of Tanzania">United Republic of Tanzania</option>
                  <option value="Thailand">Thailand</option>
                  <option value="Timor-Leste">Timor-Leste</option>
                  <option value="Togo">Togo</option>
                  <option value="Tokelau">Tokelau</option>
                  <option value="Tonga">Tonga</option>
                  <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                  <option value="Tunisia">Tunisia</option>
                  <option value="Turkey">Turkey</option>
                  <option value="Turkmenistan">Turkmenistan</option>
                  <option value="The Turks and Caicos Islands">The Turks and Caicos Islands</option>
                  <option value="Tuvalu">Tuvalu</option>
                  <option value="Uganda">Uganda</option>
                  <option value="Ukraine">Ukraine</option>
                  <option value="The United Arab Emirates">The United Arab Emirates</option>
                  <option value="The United Kingdom of Great Britain and NorThern Ireland">The United Kingdom of Great Britain and NorThern Ireland</option>
                  <option value="The United States Minor Outlying Islands">The United States Minor Outlying Islands</option>
                  <option value="The United States of America" selected>The United States of America</option>
                  <option value="Uruguay">Uruguay</option>
                  <option value="Uzbekistan">Uzbekistan</option>
                  <option value="Vanuatu">Vanuatu</option>
                  <option value="Bolivarian Republic of Venezuela">Bolivarian Republic of Venezuela</option>
                  <option value="Viet Nam">Viet Nam</option>
                  <option value="British Virgin Islands">British Virgin Islands</option>
                  <option value="U.S Virgin Islands">U.S Virgin Islands</option>
                  <option value="Wallis and Futuna">Wallis and Futuna</option>
                  <option value="Western Sahara">Western Sahara</option>
                  <option value="Yemen">Yemen</option>
                  <option value="Zambia">Zambia</option>
                  <option value="Zimbabwe">Zimbabwe</option>
                </select>
              </div>
              <div class="input my-1">
                <label class="text-start" for="Password">Password</label>
                <!-- <input class="form-control mt-2 mb-2" placeholder="password" required type="password" /> -->
                <input class="form-control" placeholder="password" required type="password" name="password" />
              </div>
              <div class="form-check d-flex align-items-center justify-content-start">
                <input class="form-check-input" name="newsletters" type="checkbox" value="false" id="newsletters" checked />
                <label class="text-start ms-2 mt-1" class="form-check-label" for="newsletters"> Sign Up for Newsletters</label>
              </div>
              <button type="submit" class="btn mt-3 pink-bg text-white w-100">Sign up</button>
            </div>
          </form>
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
  <script src="./js/sign-up.js"></script>
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