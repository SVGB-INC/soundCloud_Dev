<script>
    const popupOverlay = document.querySelector('.popup-overlay');

    var audioIntervalGlobal;
    var isPodActive = false;
    var startTime;
    let audio_id;

    popupOverlay.addEventListener('click', () => {
      popupOverlay.classList.remove('active');
      document.querySelectorAll('.popup, .popups-main').forEach(each => each.classList.remove('active'))
    })

    ////image click function start
    $('.imgPod').click(function() {

      var authorNamePHP = $("#authName").val();

      var isPremium = $("#isPremium").val();
      var freeTimeUser = $("#freeTime").val();

      var advPay = $(this).prevAll('a').first();
      var advNew = advPay.prevAll('a').first();
      var advPayment = advNew.prevAll('a').first().attr("href"); //additional payment info

      var charityOption = advNew.prevAll('a').first();
      var chrPayment = charityOption.prevAll('a').first().attr("href"); //charity info

      var audioSource = $(this).prevAll('a').first().attr("href");

      var imageSource = $(this).prevAll('a').first();
      var imageLink = imageSource.prevAll('a').first().attr("href");

      var authNameNew = charityOption.prevAll('a').first();
      var authName = authNameNew.prevAll('a').first().attr("href"); //author name

      var podTitleNew = authNameNew.prevAll('a').first();
      var podTitle = podTitleNew.prevAll('a').first().attr("href").replace(/_/g, ' '); //author name

      var audio = $("#podcast-audio");

      var userPodcasts = $("#userPods").val();

      var audioSourceImage = audioSource;

      $.ajax({
        url: "./home-page.php",
        method: "POST",
        data: {
          audioSourceImage
        },
        success: function(data) {
          console.log(data);
        },
        error: function(xhr, status, error) {
          console.error(xhr);
        },
      });

      audioIntervalGlobal = audioSource;

      var link = "./player.php?id=" + audioSource;
      
      var currentPod = audioSource.substring(
        audioSource.lastIndexOf("\\") + 1,
        audioSource.lastIndexOf("."));

      if (userPodcasts.indexOf(currentPod) >= 0) {
        
        window.location.href = link;

      } else {
        if (authName == authorNamePHP) {
          window.location.href = link;
        } else {


          if (isPremium == "No") //this is free user
          {

            if (freeTimeUser <= 7200) { // user with free time
              if (advPayment == 'advNone' && chrPayment == 'chrNone') {

                window.location.href = link;

              } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
                $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
                $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
                $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
                $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup10}`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
                $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
                $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
                $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
                $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
              } 
            } else if (freeTimeUser > 7200) { // user with no free time
              if (advPayment == 'advNone' && chrPayment == 'chrNone') {
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupNN`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
                $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
                $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
                $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
                $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup10`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
                $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
                $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
                $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
                $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
              }
            }

          } else if (isPremium == "Yes") // premium user
          {

            if (advPayment == 'advNone' && chrPayment == 'chrNone') {

              window.location.href = link;

            } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
              $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
              $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
              $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
              $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup10`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
              $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
              $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
              $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
              $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
            }

          }

        }
      }
      


    });

    ////click function start
    $('.clickPod').click(function() {
      // console.log("ye wala this",$(this).parent().parent().prevAll('a').first() );

      var authorNamePHP = $("#authName").val();
      var isPremium = $("#isPremium").val();
      var freeTimeUser = $("#freeTime").val();

      var advPay = $(this).parent().parent().prevAll('a').first();
      var advNew = advPay.prevAll('a').first();
      var advPayment = advNew.prevAll('a').first().attr("href"); //additional payment info

      var charityOption = advNew.prevAll('a').first();
      var chrPayment = charityOption.prevAll('a').first().attr("href"); //charity info

      var audioSource = $(this).parent().parent().prevAll('a').first().attr("href");

      var imageSource = $(this).parent().parent().prevAll('a').first();
      var imageLink = imageSource.prevAll('a').first().attr("href");

      var authNameNew = charityOption.prevAll('a').first();
      var authName = authNameNew.prevAll('a').first().attr("href"); //author name

      var podTitleNew = authNameNew.prevAll('a').first();
      var podTitle = podTitleNew.prevAll('a').first().attr("href").replace(/_/g, ' '); //author name

      var audio = $("#podcast-audio");

      var userPodcasts = $("#userPods").val();

      //audioSource: audioSource.substring(audioSource.lastIndexOf('\\') + 1)

      $.ajax({
        url: "./home-page.php",
        method: "POST",
        data: {
          audioSource: audioSource
        },
        success: function(data) {
          console.log(data);
        },
        error: function(xhr, status, error) {
          console.error(xhr);
        },
      });

      audioIntervalGlobal = audioSource;
      //setInterval(audioAirtime, 20000);
      // setInterval(someFunc, 5000);

      var currentPod = audioSource.substring(
        audioSource.lastIndexOf("\\") + 1,
        audioSource.lastIndexOf("."));

      if (userPodcasts.indexOf(currentPod) >= 0) {
        $("#podcast-source").attr("src", audioSource);
        audio[0].pause();
        audio[0].load();
        audio[0].oncanplaythrough = audio[0].play();
        $("#podcast_image").attr("src", imageLink);
        $("#podcastTitle_Player").html(podTitle);
        $("#podcastAuthor_Player").html(authName);

      } else {
        if (authName == authorNamePHP) {
          $("#podcast-source").attr("src", audioSource);
          audio[0].pause();
          audio[0].load();
          audio[0].oncanplaythrough = audio[0].play();
          $("#podcast_image").attr("src", imageLink);
          $("#podcastTitle_Player").html(podTitle);
          $("#podcastAuthor_Player").html(authName);
        } else {


          if (isPremium == "No") //this is free user
          {

            if (freeTimeUser <= 7200) { // user with free time
              if (advPayment == 'advNone' && chrPayment == 'chrNone') {

                $("#podcast-source").attr("src", audioSource);
                audio[0].pause();
                audio[0].load();
                audio[0].oncanplaythrough = audio[0].play();
                $("#podcast_image").attr("src", imageLink);
                $("#podcastTitle_Player").html(podTitle);
                $("#podcastAuthor_Player").html(authName);

              } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
                $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
                $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
                $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
                $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup10}`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
                $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
                $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
                $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
                $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
              }
            } else if (freeTimeUser > 7200) { // user with no free time
              if (advPayment == 'advNone' && chrPayment == 'chrNone') {
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupNN`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
                $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
                $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
                $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
                $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup10`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
                $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
                $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
                $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
              } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
                $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
                document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
              }
            }

          } else if (isPremium == "Yes") // premium user
          {

            if (advPayment == 'advNone' && chrPayment == 'chrNone') {

              $("#podcast-source").attr("src", audioSource);
              audio[0].pause();
              audio[0].load();
              audio[0].oncanplaythrough = audio[0].play();
              $("#podcast_image").attr("src", imageLink);
              $("#podcastTitle_Player").html(podTitle);
              $("#podcastAuthor_Player").html(authName);


            } else if (advPayment == 'advNone' && chrPayment == 'chrOpt') {
              $("#popN0").attr("href", "./addChrPayN0.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popupN0`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advNone' && chrPayment == 'chrMst') {
              $("#popN1").attr("href", "./addChrPayN1.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popupN1`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrNone') {
              $("#pop1N").attr("href", "./addChrPay1N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup1N`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrOpt') {
              $("#pop10").attr("href", "./addChrPay10.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup10`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advMst' && chrPayment == 'chrMst') {
              $("#pop11").attr("href", "./addChrPay11.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup11`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrNone') {
              $("#pop0N").attr("href", "./addChrPay0N.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup0N`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrOpt') {
              $("#pop00").attr("href", "./addChrPay00.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup00`).forEach(each => each.classList.add('active'));
            } else if (advPayment == 'advOpt' && chrPayment == 'chrMst') {
              $("#pop01").attr("href", "./addChrPay01.php?pod=" + /[^\\]*$/.exec(audioSource)[0]);
              document.querySelectorAll(`.popup-overlay, .popups-main, .popup01`).forEach(each => each.classList.add('active'));
            }

          }

        }
      }



    });

    $('.clickAddToPlayList').click(function() {
   

      audio_id =  $(this).parent().parent().prevAll('#audio_id').first().attr("href")
     document.querySelectorAll(`.popup-overlay, .popups-main, .popup_addToPlayList`).forEach(each => each.classList.add('active'));
    })

    $('[name="pod_add_playlsit"]').on('submit', (e)=> {
      e.preventDefault();
      // alert($('#playList').val())
      // alert(audio_id_for_add_playlist);
      $.ajax({
        url: "./callbacks/ajax_calls.php",
        method: "POST",
        data: { audio_id: audio_id, playlist_id: $('#playList').val() },
        success: function(data) {
          // 
          document.querySelectorAll(`.popup-overlay, .popups-main, .popup_addToPlayList`).forEach(each => each.classList.remove('active'));
          // 
          console.log(data);
        },
        error: function(xhr, status, error) {
          console.error(xhr);
        },
      });
      
    })

    $('.clickAddToFavrt').click( function(){
      audio_id =  $(this).parent().prevAll('#audio_id').first().attr("href")
      console.log(audio_id)
      $.ajax({
        url: "./callbacks/ajax_calls.php",
        method: "POST",
        data: { audio_id: audio_id},
        success: function(data) {
          console.log(data);
          $('.downAlert').removeClass('danger')
          $('.downAlert').addClass('success')
          $('.downAlert').addClass('active')
          $('.downAlert p').html('add to favorite songs')
          // $('.likeing-button span').html('Liked');
          $(`.likeing-button i#heartId_${audio_id}`).removeClass('fa-solid');
          $(`.likeing-button i#heartId_${audio_id}`).removeClass('fa-regular');
          $(`.likeing-button i#heartId_${audio_id}`).addClass('fa-solid');
          setTimeout(()=>{
            // $('.downAlert').removeClass('success')
            $('.downAlert').removeClass('active')
          }, 2000)
        },
        error: function(err, status) {
          // console.error(xhr);
          $('.downAlert').removeClass('success')
          $('.downAlert').addClass('danger')
          $('.downAlert').addClass('active')
          $('.downAlert p').html('reomve to favorite songs')
          // $('.likeing-button span').html('Like');
          $(`.likeing-button i#heartId_${audio_id}`).removeClass('fa-solid');
          $(`.likeing-button i#heartId_${audio_id}`).removeClass('fa-regular');
          $(`.likeing-button i#heartId_${audio_id}`).addClass('fa-regular');
          setTimeout(()=>{
            $('.downAlert').removeClass('active')
          }, 2000)
        },
      }); 
    })
    ////click function end
    
    function audioAirtime() {
      //alert("Hello World!");

      $.ajax({
        url: "./home-page.php",
        method: "POST",
        data: {
          audioIntervalGlobal: audioIntervalGlobal.substring(audioIntervalGlobal.lastIndexOf('\\') + 1)
        },
        success: function(data) {
          console.log(data);
        },
        error: function(xhr, status, error) {
          console.error(xhr);
        },
      });

      //alert("End World!");
    }

    $('body').keydown(function(e) {
      if (e.keyCode == 32) {
        e.preventDefault();
        // user has pressed space
        var audio = $("#podcast-audio");
        if (audio[0].paused) {
          audio[0].play();
        } else {
          audio[0].pause();
        }

      }
    });
  </script>

</body>

</html>
