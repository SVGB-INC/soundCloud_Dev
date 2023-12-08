<?php
require_once("../Config.php");
require_once('../paypal/Send_cURL_Requests.php');

// set_time_limit(0);
//--- Headers for our token request

$headers[] = "Accept: application/json";
$headers[] = "Content-Type: application/x-www-form-urlencoded";

//--- Data field for our token request
$data = "grant_type=client_credentials";

//--- Pass client id & client secrent for authorization
$curl_options[CURLOPT_USERPWD] = PAYPAL_CLIENT_ID . ":" . PAYPAL_CLIENT_SECRET;

$token_request = curl_request(PAYPAL_TOKEN_URL, "POST", $headers, $data, $curl_options);
$token_request = json_decode($token_request);

require_once('../paypal/payout_rquest.php');

if(isset($token_request->error)){
    die("Paypal Token Error: ". $token_request->error_description);
}
?>