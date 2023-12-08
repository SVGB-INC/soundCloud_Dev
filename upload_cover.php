<?php 
include("Config.php");
session_start();
$_SESSION;

if(isset($_FILES['file']['name']) && isset($_FILES['file']['name']) != ''){
  
    $img_name = explode(".",$_FILES['file']['name']);
    $extention = end($img_name);
    $img_id = str_replace('.', '', time() . uniqid(rand(100,999), true));
    $img_name = $img_id. '.' . $extention;
    $location = USER_COVER_PIC.'/'.$img_name;
    move_uploaded_file($_FILES['file']['tmp_name'], $location);
    $query = "SELECT user 
                     from user_detail
                     INNER JOIN reg_data_bank
                       ON user_detail.user = reg_data_bank.user_name
                       WHERE
                       user_detail.user ='". $_SESSION['user_name']."'
                       ORDER BY
                        user DESC LIMIT 1
                       ";
 
   $q = $pdo->prepare($query);
               $q->execute();
   $user = $q->fetch();
   
   if($user){
    echo "user";
    echo $_SESSION['user_name'];
     $query = "UPDATE user_detail
                         SET 
                           cover_img = '".  $img_name ."'
                         WHERE
                           user = '". $_SESSION['user_name']."'
                           
               ";
   }
   else{
       echo "not user";
     $query = "INSERT into user_detail(user, cover_img) VALUES('". $_SESSION['user_name']."','$img_name')";
   }
   try {
   $q = $pdo->prepare($query);
   $q->execute();
   }
   catch (PDOException $e) {
     echo $e->getMessage();
   }
   }

?>