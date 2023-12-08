

$("#updateProfileImage").change(function (event) {
  var property = event.target.files[0];
  //   console.log("property ", property);

  var image_name = property.name;
  var image_extention = image_name.split(".").pop().toLowerCase();

  var file_type = property.type.split("/");
  file_type = file_type[0];

  if (
    jQuery.inArray(image_extention, ["png", "jpg", "jpeg"]) == -1 ||
    file_type != "image"
  ) {
    alert("Invalid Image File");
  } else {
    src = URL.createObjectURL(property);
    // console.log("src ", src);
    document.getElementById("profileImage").setAttribute("src", src);

    var form_data = new FormData(document.forms.namedItem("upload_user_image"));
    console.log("form_data ", form_data);
    form_data.append("file", property);
    // console.log(form_data);
    $.ajax({
      //   url: '<?php ROOT_DIR."my-account.php" ?>',
      url: "upload_profile_pic.php",
      method: "POST",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      success: function (data) {
        console.log(data);
      },
      error: function (xhr, status, error) {
        console.error(xhr);
      },
    });
  }
});

$("#updateCoverImage").change(function (event) {
  var property = event.target.files[0];
  //   console.log("property ", property);

  var image_name = property.name;
  var image_extention = image_name.split(".").pop().toLowerCase();

  var file_type = property.type.split("/");
  file_type = file_type[0];

  if (
    jQuery.inArray(image_extention, ["png", "jpg", "jpeg"]) == -1 ||
    file_type != "image"
  ) {
    alert("Invalid Image File");
  } else {
    src = URL.createObjectURL(property);
    // console.log("src ", src);
    // document.getElementById("profileImage").setAttribute("src", src);

    $(".coverImageInBackground").css(
      "background",
      "linear-gradient(90deg, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.5) 100%) , url(" +
        src +
        ")"
    );
    $(".coverImageInBackground").css("background-repeat", "no-repeat");
    $(".coverImageInBackground").css("background-size", "cover");
    $(".coverImageInBackground").css("background-position", "center");

    var form_data = new FormData(document.forms.namedItem("upload_cover_img"));
    // console.log("form_data ", form_data);
    form_data.append("file", property);
    // console.log(form_data);
    $.ajax({
      //   url: '<?php ROOT_DIR."my-account.php" ?>',
      url: "upload_cover.php",
      method: "POST",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      success: function (data) {
        console.log(data);
      },
      error: function (xhr, status, error) {
        console.error(xhr);
      },
    });
  }
});
