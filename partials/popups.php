<div class="downAlert">
    <p>The Pod Cast was liked</p>
  </div>
<!-- this is popup code start -->
<div class="popups-main">

<div class="popup-overlay">
</div>

<div class="popup popupNN">
  <p>You have exhausted your free time. Please upgrade to continue.</p>
  <div class="btns">
    <a id="popNN" href="./upgrade-pro.php" class="pink-bg">Access payment page</a>
  </div>
</div>

<div class="popup popup00">
  <p>This Podcast Requires Optional Payment and Optional Charity.</p>
  <div class="btns">
    <a id="pop00" href="./addChrPay00.php" class="pink-bg">Access payment page</a>
  </div>
</div>

<div class="popup popup01">
  <p>This Podcast Requires Optional Payment and Additional Charity.</p>
  <div class="btns">
    <a id="pop01" href="./addChrPay01.php" class="pink-bg">Access upgrade page</a>
  </div>
</div>

<div class="popup popup10">
  <p>This Podcast Requires Additional Payment and Optional Charity.</p>
  <div class="btns">
    <a id="pop10" href="./addChrPay10.php" class="pink-bg">Access upgrade page</a>
  </div>
</div>

<div class="popup popup11">
  <p>This Podcast Requires Additional Payment and Additional Charity.</p>
  <div class="btns">
    <a id="pop11" href="./addChrPay11.php" class="pink-bg">Access page for additional payment</a>
  </div>
</div>

<div class="popup popupN0">
  <p>This Podcast Requires Optional Charity.</p>
  <div class="btns">
    <a id="popN0" href="./addChrPayN0.php" class="pink-bg">Access page for optional payments</a>
  </div>
</div>

<div class="popup popupN1">
  <p>This Podcast Requires Additional Charity.</p>
  <div class="btns">
    <a id="popN1" href="./addChrPayN1.php" class="pink-bg">Access page for optional payments</a>
  </div>
</div>
<div class="popup popup0N">
  <p>This Podcast Requires Optional Payment.</p>
  <div class="btns">
    <a id="pop0N" href="./addChrPay0N.php" class="pink-bg">Access page for optional payments</a>
  </div>
</div>
<div class="popup popup1N">
  <p>This Podcast Requires Additional Payment.</p>
  <div class="btns">
    <a id="pop1N" href="./addChrPay1N.php" class="pink-bg">Access page for optional payments</a>
  </div>
</div>
<div class="popup popup_addToPlayList">
  
  <form name="pod_add_playlsit" class="pod_add_playlsit" action="">
    <div class="popup_addToPlayList_inputDiv">
      <label for="playList">Add to Playlist:</label>
      <select name="playList" id="playList">
      <?php
        foreach($my_play_list as $playlist) {
      ?>
        <option value="<?= $playlist['ID'] ?>"><?= $playlist['title'] ?></option>
      <?php } ?>
      </select>
      <div class="btns align-self-end">
        <button type="submit" id="addToPlaylistBtn" class="pink-bg text-white btn btn-sm">Submit</button>
      </div>
    </div>
  </form>
</div>
</div>
