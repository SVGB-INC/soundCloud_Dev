<?php
include("Config.php");
session_start();
$_SESSION;

if(isset($_FILES['file']['name']) && isset($_FILES['file']['name']) != ''){

   $img_name = explode(".",$_FILES['file']['name']);
   $extention = end($img_name);
   $img_id = str_replace('.', '', time() . uniqid(rand(100,999), true));
   $img_name = $img_id. '.' . $extention;
   $location = USER_PROFILE.'/'.$img_name;
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
    $query = "UPDATE user_detail
                        SET 
                          user_img = '".  $img_name ."'
                          
                        WHERE
                          user = '". $_SESSION['user_name']."'
                          
              ";
  }
  else{
    $query = "INSERT into user_detail(user, user_img) VALUES(  '". $_SESSION['user_name']."','$img_name')";
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