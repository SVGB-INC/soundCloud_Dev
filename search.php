<?php


require_once('./partials/head_links_.php');

?>
<title>Chhogori | Search</title>
<?php  ?>
<?php

require_once('./includes/header.php');

    if(isset($_POST['search'])){
        $search = $_POST['search'];
        $query = "SELECT * 
                FROM podcast_details 
                WHERE
                podcast_title LIKE '%$search%'
                OR
                podcast_tags LIKE '%$search%'
                OR
                podcast_genre LIKE '%$search%'
               
                ";
$sql = $pdo->query($query);
$sql->execute();
$searched_data = $sql->fetchAll(PDO::FETCH_ASSOC);

// for user name

$query = "SELECT * 
                FROM reg_data_bank
                INNER JOIN user_detail
                ON user_detail.user = reg_data_bank.user_name
                WHERE
                user_name LIKE '%$search%'
                ";
$sql = $pdo->query($query);
$sql->execute();
$users = $sql->fetchAll(PDO::FETCH_ASSOC);

$query = "SELECT * 
                FROM channels
                WHERE
                title LIKE '%$search%'
                ";
$sql = $pdo->query($query);
$sql->execute();
$channels = $sql->fetchAll(PDO::FETCH_ASSOC);

    }




?>
    <div class="row mt-5">
      <div class="col ps-4 pb-3">
        <h3 class="d-inline">Messages</h3>
      </div>
    </div>
    
    <div class="responsive-table res1 py-2">
        <?php if($searched_data){  foreach($searched_data as $podcast){ ?>
          <div class="rounded-bottom card card-1 d-flex position-relative top-0 start-0 flex-column">
          <?php if ($podcast["podcast_add_payment"] == "paid") { ?>

            <!-- <span class="btn btn-warning btn-sm position-absolute" style="z-index: 10; top: 0.5rem; right:0.5rem">Paid</span> -->

          <?php } ?>
          <div class="imgContainer">
            <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["podcast_title"]) ?>></a>
            <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["user_name"]) ?>></a>
            <a id="isCharity" name="charity" style="display:none;" href=<?php echo $podcast["podcast_charity"] ?>></a>
            <a id="isPayment" name="payment" style="display:none;" href=<?php echo $podcast["podcast_add_payment"] ?>></a>
            <a id="source-image" name="source-image" style="display:none;" href=<?php echo $podcast["image_address"] ?>></a>
            <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $podcast["podcast_address"] ?>></a>
            <img class="imgPod" src=<?php echo $podcast["image_address"]; ?> />
            <!-- <div class="imageOverlay"></div> -->
          </div>
          <div class="content">
            <a class="audio_id" id="audio_id" name="audio_id" style="display:none;" href="<?= $podcast["pID"] ?>" ></a>
            <a id="podTitle" name="podTitle" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["podcast_title"]) ?>></a>
            <a id="authorName" name="authName" style="display:none;" href=<?php echo str_replace(' ', '_', $podcast["user_name"]) ?>></a>
            <a id="isCharity" name="charity" style="display:none;" href=<?php echo $podcast["podcast_charity"] ?>></a>
            <a id="isPayment" name="payment" style="display:none;" href=<?php echo $podcast["podcast_add_payment"] ?>></a>
            <a id="source-image" name="source-image" style="display:none;" href=<?php echo $podcast["image_address"] ?>></a>
            <a id="source-audio" name="source-audio" style="display:none;" href=<?php echo $podcast["podcast_address"] ?>></a>
            
            <h3><?php echo $podcast["podcast_title"]; ?></h3>
            <h5><?= $podcast["user_name"] ?></h5>
            <p><?= $podcast["podcast_desc"] ?></p>
            <div class="icons">
              <button class="playInformation" >
                <i class="fas fa-play clickPod"></i>
                <span><?= $podcast["play_count"] ?></span>
              </button>
              <button class="addToPlayListInformation">
                <i class="fas fa-plus clickAddToPlayList"></i>
                
              </button>
                <?php
                  $fvrt = false;
                  foreach($my_fvrt_audio as $myfvrt){
                    if($myfvrt['podcast'] == $podcast['pID']){
                      $fvrt = true;
                    }
                  }
                  ?>
              <button class="heartIconCustomMade likeing-button clickAddToFavrt">
                  <i id="heartId_<?= $podcast['pID']  ?>" class="<?php if($fvrt){ ?>fa-solid<?php } else{ ?>fa-regular<?php } ?> fa-heart"></i>
                  <span><?= $podcast["podcast_likes"] ?></span>
                </button>
            </div>
            
          </div>
        </div>
        <?php }} else{?>
          <p style="height:auto;">No any podcast here</p>
          <?php }?>
    </div>

    <!-- show users -->
    <div class="row mt-5">
      <div class="col ps-4 pb-3">
        <h3 class="d-inline">Users</h3>
      </div>
    </div>
    
    <div class="responsive-table res1 py-2">
        <?php if($users){ foreach($users as $user){ ?>
          <div class="rounded-bottom card card-1 d-flex position-relative top-0 start-0 flex-column">
          <div class="imgContainer">
            <img class="imgPod" src="<?php echo './images/user_profile/' . $user['user_img']; ?>" />
            <!-- <div class="imageOverlay"></div> -->
          </div>
          <div class="content">
          <a href="./profile.php?user=<?= str_replace(' ', '_', $user["user_name"]) ?>"><h5>  <?= $user["user_name"] ?></h5></a>
            
          </div>
        </div>
        <?php }} else{?>
          <p style="height:auto;">No any user here</p>
          <?php }?>
    </div>

    <!-- show Channels -->
    <div class="row mt-5">
      <div class="col ps-4 pb-3">
        <h3 class="d-inline">Voices</h3>
      </div>
    </div>

    <div class="responsive-table res1 py-2">
        <?php if($channels){ foreach($channels as $channel){ ?>
          <div class="rounded-bottom card card-1 d-flex position-relative top-0 start-0 flex-column">
          <div class="imgContainer chanel_img">
          <a href="./channel.php?channel=<?= $channel['title'] ?>" >
            <img class="" src="./images/user_channel_pic/<?php echo $channel['img']; ?>" />
            </a>
          </div>
          <div class="content">
            <h5 class="fs-6"><?php echo $channel["title"]; ?></h5>
          </div>
        </div>
        <?php }} else{?>
          <p style="height:auto;">No any Channel here</p>
          <?php }?>
    </div>
    <?php require_once('./includes/footer.php'); ?>
