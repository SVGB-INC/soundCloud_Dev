<?php

session_start();
$_SESSION;

include("./Config.php");
include("./functions.php");

  
// $user_data = check_login($pdo);

if (isset($_SESSION['user_name']))
    {
        
        try
        {

            $username = $_SESSION['user_name'];
            $select_stmt = $pdo->prepare("SELECT * FROM reg_data_bank WHERE user_name=:uname limit 1;");
            $select_stmt->execute(array(
                ':uname' => $username
            ));
            $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
            
            
            if ($select_stmt->rowCount() > 0)
            {
                
                $user_data = $row;
            }
            else
            {
                //redirect to login page if the above code is unsuccessful
                header("Location: sign-in.php");
            
            }
        }
        catch(PDOException $e)
        {
            $e->getMessage();
        }

    }
    else
    {
        //redirect to login page if the above code is unsuccessful
        header("Location: sign-in.php");
        
    }

$sqlUserID = "SELECT user_id FROM reg_data_bank where user_name = '" . $_SESSION['user_name'] . "'";
$userID = $pdo->query($sqlUserID)->fetchAll(PDO::FETCH_COLUMN)[0];

$sqlUserPremium = "SELECT isPremium FROM reg_data_bank where user_name = '" . $_SESSION['user_name'] . "'";
$userPremium = $pdo->query($sqlUserPremium)->fetchAll(PDO::FETCH_COLUMN)[0];

$sqlUserPods = "SELECT podcast_payment FROM user_podcast_access where user_name = '" . $_SESSION['user_name'] . "'";
$sqlPods = '-';
if ($pdo->query($sqlUserPods)->fetchAll(PDO::FETCH_COLUMN)) {
  $sqlPods = $pdo->query($sqlUserPods)->fetchAll(PDO::FETCH_COLUMN)[0];
}

$_SESSION['user_id'] = $userID;
$_SESSION['isPremium'] = $userPremium;

// show all playlists
$my_play_list = $pdo->query("SELECT * FROM playlists WHERE author ='". $_SESSION['user_id'] ."'");
$my_play_list->execute();
$my_play_list = $my_play_list->fetchAll(PDO::FETCH_ASSOC);

$my_fvrt_audio = $pdo->query("SELECT * FROM fvrt_audio WHERE user ='". $_SESSION['user_id'] ."'");
$my_fvrt_audio->execute();
$my_fvrt_audio = $my_fvrt_audio->fetchAll(PDO::FETCH_ASSOC);


// session for audio path
if (isset($_POST["audioSourceImage"])) {

  $_SESSION["podName"] = $_POST["audioSourceImage"];

}

function get_string_between($string, $start, $end)
{
  $string = ' ' . $string;
  $ini = strpos($string, $start);
  if ($ini == 0) return '';
  $ini += strlen($start);
  $len = strpos($string, $end, $ini) - $ini;
  return substr($string, $ini, $len);
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {


  if (isset($_POST["audioSource"])) {

    echo "Audio Source Here \n \n";

    $_SESSION["podName"] = $_POST["audioSource"];

    $podTimeTot = 0;

    $podcastAddress = $_POST["audioSource"];

    //get_string_between($podcastAddress, '/', '\\');
    //update play count in the podcast_details table
    try {
      $sql = "UPDATE podcast_details SET play_count=play_count+1 WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $podcastAddress)) . "';";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
    } catch (PDOException $e) {
      echo $e->getMessage();
    }

    //if user is premium/paid, go to the following code. Otherwise, do nothing.
    if ($_SESSION['isPremium'] == "Yes") {
      try {

        //check if user is listening to something.
        $select_stmt = $pdo->prepare("SELECT user_name FROM current_podcast 
                      WHERE user_name=:uname and isActive='true' LIMIT 1"); // sql select query
        $select_stmt->execute(array(
          ':uname' => $_SESSION['user_name']
        )); //execute query
        $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

        if ($select_stmt->rowCount() > 0) {
          //this means that there already exists a podcast that is active
          //
          //get the name of previous podcast from current_podcast
          $sqlPod = "SELECT podcast_address FROM current_podcast where user_name = '" . $_SESSION['user_name'] . "' and isActive='true' LIMIT 1; ";
          $sqlPrevPod = $pdo->query($sqlPod)->fetchAll(PDO::FETCH_COLUMN)[0];
          $sqlPrevPod = basename($sqlPrevPod);
          $sqlPrevPod = "audio/" . $_SESSION['user_name'] . "\\" . $sqlPrevPod;

          //// get previous podcast time from podcast_details
          $select_stmt = $pdo->prepare("SELECT podcast_time FROM podcast_details WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "' LIMIT 1;");
          $select_stmt->execute();
          $podTimeTot = $select_stmt->fetchColumn();

          $minDuration = 0.20 * $podTimeTot;

          //update existing record in current_podcast to inseert endtime
          $sql = "UPDATE current_podcast SET end_time = current_timestamp() WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();

          //update existing record in current podcast to set total listen time
          $sql = "update current_podcast set total_time = IF(TIMESTAMPDIFF(SECOND, start_time,end_time) > pod_time, pod_time, TIMESTAMPDIFF(SECOND, start_time,end_time)) WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();

          //check if the current total time listened is greater than minimum duration and 60 seconds
          $sqlPod = "SELECT total_time FROM current_podcast WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "' LIMIT 1;";
          $curTotalTime = $pdo->query($sqlPod)->fetchAll(PDO::FETCH_COLUMN)[0];

          if ($curTotalTime >= $minDuration && $curTotalTime > 60) {


            //check if user already listened to this podcast
            $select_stmt = $pdo->prepare("SELECT user_name FROM user_listened_pod WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';"); // sql select query
            $select_stmt->execute(); //execute query
            $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

            if ($select_stmt->rowCount() > 0) {
              //check if user already listened to this podcast and its current duration
              $select_stmt = $pdo->prepare("SELECT user_name FROM user_listened_pod WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "' and listen_time < " . $curTotalTime . " LIMIT 1;"); // sql select query
              $select_stmt->execute(); //execute query
              $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

              if ($select_stmt->rowCount() > 0) {

                //update the record from current podcast to user_listened_podcast
                $sql = "UPDATE user_listened_pod SET listen_time = " . $curTotalTime . " WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                //update the listen_time in podcast_details
                $sql = "UPDATE podcast_details SET podcast_airtime = podcast_airtime + " . $curTotalTime . " WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                //// get podcast time from podcast_details
                $select_stmt = $pdo->prepare("SELECT podcast_time FROM podcast_details WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $podcastAddress)) . "' LIMIT 1;");
                $select_stmt->execute();
                $podTimeTot = $select_stmt->fetchColumn();

                //update the current podcast to set data for new clicked podcast
                $sql = "UPDATE current_podcast SET isActive='true', curr_time = '0', podcast_address = '" . str_replace("\\", "\\\\", $podcastAddress) . "', start_time = current_timestamp(), end_time = current_timestamp(), total_time = '0',pod_time = '" . $podTimeTot . "' WHERE user_name = '" . $_SESSION['user_name'] . "';";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
              } else {

                //// get podcast time from podcast_details
                $select_stmt = $pdo->prepare("SELECT podcast_time FROM podcast_details WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $podcastAddress)) . "' LIMIT 1;");
                $select_stmt->execute();
                $podTimeTot = $select_stmt->fetchColumn();

                //update the current podcast to set data for new clicked podcast
                $sql = "UPDATE current_podcast SET isActive='true', curr_time = '0', podcast_address = '" . str_replace("\\", "\\\\", $podcastAddress) . "', start_time = current_timestamp(), end_time = current_timestamp(), total_time = '0',pod_time = '" . $podTimeTot . "' WHERE user_name = '" . $_SESSION['user_name'] . "';";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
              }
            } else {
              //move the record from current podcast to user_listened_podcast
              $sql = "insert into user_listened_pod select user_name, podcast_address, pod_time, total_time from current_podcast WHERE user_name = '" . $_SESSION['user_name'] . "' and replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';";
              $stmt = $pdo->prepare($sql);
              $stmt->execute();

              //update the listen_time in podcast_details
              $sql = "UPDATE podcast_details SET podcast_airtime = podcast_airtime + " . $curTotalTime . " WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $sqlPrevPod)) . "';";
              $stmt = $pdo->prepare($sql);
              $stmt->execute();

              //// get podcast time from podcast_details
              $select_stmt = $pdo->prepare("SELECT podcast_time FROM podcast_details WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $podcastAddress)) . "' LIMIT 1;");
              $select_stmt->execute();
              $podTimeTot = $select_stmt->fetchColumn();

              //update the current podcast to set data for new clicked podcast
              $sql = "UPDATE current_podcast SET isActive='true', curr_time = '0', podcast_address = '" . str_replace("\\", "\\\\", $podcastAddress) . "', start_time = current_timestamp(), end_time = current_timestamp(), total_time = '0',pod_time = '" . $podTimeTot . "' WHERE user_name = '" . $_SESSION['user_name'] . "';";
              $stmt = $pdo->prepare($sql);
              $stmt->execute();
            }
          } else {
            //// get podcast time from podcast_details
            $select_stmt = $pdo->prepare("SELECT podcast_time FROM podcast_details WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $podcastAddress)) . "' LIMIT 1;");
            $select_stmt->execute();
            $podTimeTot = $select_stmt->fetchColumn();

            //update the current podcast to set data for new clicked podcast
            $sql = "UPDATE current_podcast SET isActive='true', curr_time = '0', podcast_address = '" . str_replace("\\", "\\\\", $podcastAddress) . "', start_time = current_timestamp(), end_time = current_timestamp(), total_time = '0',pod_time = '" . $podTimeTot . "' WHERE user_name = '" . $_SESSION['user_name'] . "';";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
          }
        } else {

          //// get podcast time from podcast_details
          $select_stmt = $pdo->prepare("SELECT podcast_time FROM podcast_details WHERE replace(replace(podcast_address, '\\\\', '_'),'/','_') = '" . str_replace("/", "_", str_replace("\\", "_", $podcastAddress)) . "' LIMIT 1;");
          $select_stmt->execute();
          $podTimeTot = $select_stmt->fetchColumn();

          $sql = "INSERT into current_podcast(user_name, podcast_address, isActive, pod_time, curr_time,start_time, end_time, total_time) values('" . $_SESSION['user_name'] . "','" . str_replace('\\', '\\\\', $podcastAddress) . "','true'," . $podTimeTot . ", '0', current_timestamp(), current_timestamp(), '0');";
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
        }
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
    }
  }
}


?>