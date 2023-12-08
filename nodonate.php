<?php
//require_once('./includes/header.php');

session_start();
$_SESSION;

include("./Config.php");
include("./functions.php");

$user_data = check_login($pdo);

$audio_id = $_GET["pod"];
$sql = "select podcast_address from podcast_details where pID =". $audio_id . ";";
$podcast_address = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN)[0];

try {
    $select_stmt = $pdo->prepare("SELECT user_name FROM user_podcast_access 
										WHERE user_name=:uname"); // sql select query
    $select_stmt->execute([
      ":uname" => $_SESSION["user_name"],
    ]); //execute query
    $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
    if ($select_stmt->rowCount() > 0) {

      $sql = "UPDATE user_podcast_access SET podcast_payment=CONCAT(podcast_payment,',','" . $podcast_address . "') ,  podcast_charity= CONCAT(podcast_charity,',','" . $podcast_address . "') WHERE user_name=?";
      $stmt = $pdo->prepare($sql);

      if ($stmt->execute([$_SESSION["user_name"]])) {
        header("Location: player.php?id=" . $podcast_address);
      }
    } else {
      $insert_stmt = $pdo->prepare("INSERT INTO user_podcast_access(user_name, podcast_payment, podcast_charity) VALUES (?,?,?)"); //sql insert query
      if ($insert_stmt->execute([$_SESSION["user_name"], $audio_id, $audio_id])) {
        header("Location: player.php?id=" . $podcast_address);
      }
    }
  } catch (PDOException $e) {
    echo $e->getMessage();
  }



?>