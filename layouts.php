

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
            <h5>MICHAEL JACKSON</h5>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Nostrum suscipit voluptates architecto reiciendis, voluptatem vel veniam doloremque exercitationem, ipsam dignissimos commodi excepturi recusandae a. Perferendis in ad autem et odio corporis aliquid necessitatibus nemo voluptas aperiam, aut ipsam illum perspiciatis incidunt quia, asperiores veritatis voluptatibus, blanditiis doloribus recusandae dolore corrupti error. Ducimus nihil expedita voluptates animi fuga quisquam magnam, earum sunt obcaecati incidunt recusandae rem! Sunt dicta, a id repellat dolores reprehenderit excepturi quis obcaecati voluptates, modi, iure mollitia vitae quae distinctio. Cum, illum quis molestias iste earum ab obcaecati, at ipsa sed tenetur inventore, facilis corporis natus facere? Eaque.</p>
            <div class="icons">
              <button class="playInformation" >
                <i class="fas fa-play clickPod"></i>
                <span>55k</span>
              </button>
              <button class="addToPlayListInformation">
                <i class="fas fa-plus clickAddToPlayList"></i>
                <span>55k</span>
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
                  <span>55k</span>
                </button>
            </div>
            
          </div>
        </div>

        