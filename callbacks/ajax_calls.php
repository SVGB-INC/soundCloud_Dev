<?php
session_start();
$_SESSION;
require_once("../Config.php");

  // add audio in playlist
  if (isset($_POST["audio_id"]) && isset($_POST["playlist_id"])) {
   
    $user = $_SESSION['user_id'];
    $audio_id =$_POST['audio_id'];
    $playlist_id = $_POST['playlist_id'];
    $query = "SELECT * FROM liked_audio WHERE user='$user' AND playlist=$playlist_id AND podcast=$audio_id LIMIT 1";
    $sql = $pdo->prepare($query);
    $sql->execute();
    $data = $sql->fetch(PDO::FETCH_ASSOC);
    if(!$data){
      $query = "INSERT into liked_audio(user, playlist, podcast) VALUES('$user',$playlist_id,$audio_id )";
      $sql = $pdo->prepare($query);
      $sql->execute();
    }
    else{
      echo "Already exist data";
    }
    die;
  }

// for add fvrt
if(isset($_POST['audio_id'])){
    $query = "SELECT * FROM fvrt_audio WHERE user='" . $_SESSION['user_id']."' AND podcast= '".$_POST['audio_id'] ."' ";
    $sql = $pdo->prepare($query);
    $sql->execute();
    $audio_status = $sql->fetch(PDO::FETCH_ASSOC);

    if(@!$audio_status){
      $query = "INSERT into fvrt_audio(user, podcast) values('" . $_SESSION['user_id'] . "','" . $_POST['audio_id'] . "');";
            $sql = $pdo->prepare($query);
            $sql->execute();
      
      $query = "UPDATE podcast_details
                  SET podcast_likes = podcast_likes + 1
                    WHERE pID = '" . $_POST['audio_id'] . "'
                  
                  ";
      $sql = $pdo->prepare($query);
      $sql->execute();
            echo "add in to fvrt";
            die;
    }
    if($audio_status){
      header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
      $query = "DELETE FROM fvrt_audio WHERE user= '" . $_SESSION['user_id'] . "' AND podcast= '" . $_POST['audio_id'] . "' ";
      $sql = $pdo->prepare($query);
      $sql->execute();

      $query = "UPDATE podcast_details
      SET podcast_likes = podcast_likes - 1
        WHERE pID = '" . $_POST['audio_id'] . "'
        ";
        $sql = $pdo->prepare($query);
        $sql->execute();
      echo "remove to fvrt";
      die;
    }
  }

?>