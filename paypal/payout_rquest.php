<?php
$headers = $data = [];
//--- Headers for payout request

$additional_payment = $_POST['additional_payment'];
$charity_payment = $_POST['charity_payment'];
$charity_acc = $_POST['charity_acc'];
$auth_acc = $_POST['auth_acc'];


$headers[] = "Content-Type: application/json";
$headers[] = "Authorization: Bearer $token_request->access_token";

$time = time();
//--- Prepare sender batch header
$sender_batch_header["sender_batch_id"] = $time;
$sender_batch_header["email_subject"]   = "Payout Received";
$sender_batch_header["email_message"]   = "You have received a payout, Thank you for using our services";

//--- Addtional payment receiver
if($additional_payment > 0){
    
    $receiver["recipient_type"] = "EMAIL";
    $receiver["note"] = "Thank you for your services";
    $receiver["sender_item_id"] = $time++;
    $receiver["receiver"] = $auth_acc;
    $receiver["amount"]["value"] = $additional_payment;
    $receiver["amount"]["currency"] = "USD";
    $items[] = $receiver;
}

//--- Charity Payment receiver
if($charity_payment > 0){
    $receiver["recipient_type"] = "EMAIL";
    $receiver["note"] = "You received a payout for your services";
    $receiver["sender_item_id"] = $time++;
    $receiver["receiver"] = $charity_acc;
    $receiver["amount"]["value"] = $charity_payment;
    $receiver["amount"]["currency"] = "USD";
    $items[] = $receiver;
}

$data["sender_batch_header"] = $sender_batch_header;
$data["items"] = $items;


//--- Send payout request
$payout = curl_request(PYPAL_PAYOUTS_URL, "POST", $headers, json_encode($data));
return $payout;

?>