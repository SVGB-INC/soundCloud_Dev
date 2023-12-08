<?php

define('ROOT_DIR', dirname(__FILE__));
define('USER_PROFILE', ROOT_DIR . "/images/user_profile");
define('USER_COVER_PIC', ROOT_DIR . "/images/user_cover_pic");
define('USER_CHANNEL_PIC', ROOT_DIR . "/images/user_channel_pic");
define('USER_PLAYLIST_PIC', ROOT_DIR . "/images/user_playlist_pic");


// PayPal Configuration
define('PAYPAL_EMAIL', 'sb-hfzuq15242316@business.example.com');
define('RETURN_URL', 'http://localhost:3000/sign-up-step-02.php');
define('CANCEL_URL', 'http://localhost:3000/payment_cancel.php');
define('NOTIFY_URL', 'http://localhost:3000/notify.php'); 
define('CURRENCY', 'USD'); 
define('SANDBOX', TRUE); // TRUE or FALSE 
define('LOCAL_CERTIFICATE', FALSE); // TRUE or FALSE

// split payment
define("PAYPAL_CLIENT_ID", "Ad1PQgptZJpq6fwmkKR8XzLmIAo0tmrP3_fk6YX1CsFtW1tnBpOUXtAOzwCaogflbfKX5asMjFEMGk24");
define("PAYPAL_CLIENT_SECRET","EIJB7BTz6cQS-SV4hKJqYnsuXmMw8_7qsfcIJgAchLRnx3J-9YsB15R2B1qkM76xyrK6iHkedc90G1HR");
define("PAYPAL_TOKEN_URL", "https://api.sandbox.paypal.com/v1/oauth2/token");
define("PYPAL_PAYOUTS_URL", "https://api.sandbox.paypal.com/v1/payments/payouts");

define("ADDITIONAL_ACCOUNT", "sb-homog18059569@business.example.com");
define("CHARITY_ACCOUNT", "sb-jkcgl18058684@business.example.com");

if (SANDBOX === TRUE){
	$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
}else{
	$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
}
// PayPal IPN Data Validate URL
define('PAYPAL_URL', $paypal_url);


$host = 'localhost';
$db = 'chiligori';
$user = 'root';
$password = '';

$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

try {
   $pdo = new PDO($dsn, $user, $password);

   if ($pdo) {
      //echo "Connected to the $db database successfully!";
   }
} catch (PDOException $e) {
   echo $e->getMessage();
}
