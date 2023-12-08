// file uploader
(function () {
  const uploadBox = document.querySelector(".uploadBox");
  const uploadHeading = document.querySelector(".uploadBox h2");
  const uploadBtn = document.querySelector(".uploadBox label");
  const uploadSpan = document.querySelector(".uploadBox span");
  const uploadIcon = document.querySelector(".uploadBox i");
  const uploadInput = document.querySelector('.uploadBox input[type="file"]');
  let file;

  const uploadedActions = filename => {
    uploadBox.style.justifyContent = "center";
    uploadHeading.innerHTML = "Uploading!";
    uploadSpan.innerHTML = filename;
    uploadBtn.style.display = "none";
    uploadIcon.style.display = "none";
  };

  // if user drags over the file
  uploadBox.addEventListener("dragover", e => {
    e.preventDefault();
    // console.log('File is dragged over!! ');
    uploadBox.classList.add("active");
    uploadHeading.innerHTML = "Drop to Upload File";
  });
  // if user leaver drag area file
  uploadBox.addEventListener("dragleave", () => {
    // console.log('File is not dragged over!! ');
    uploadBox.classList.remove("active");
    uploadHeading.innerHTML = "Drag & Drop to Upload File";
  });
  // if the user drops the file on drag area or browses it
  uploadBox.addEventListener("drop", e => {
    e.preventDefault();
    console.log("File dropd over drag area!! ");
    // uploadBox.classList.remove("active")
    file = e.dataTransfer.files[0];
    let fileType = file.type;
    console.log(fileType.split("/")[0]);

    if (fileType.split("/")[0] === "audio") {
      uploadedActions(file.name);
      console.log("This is an audio file.");
      let fileReader = new FileReader();

      fileReader.onload = function () {
        let fileURL = fileReader.result;
        console.log(fileURL);
      };
      fileReader.readAsDataURL(file);
    } else {
      alert("This is not an audio file.");
      uploadBox.classList.remove("active");
    }
  });
  // if the user browses the file
  uploadInput.addEventListener("change", e => {
    const filename = e.target.files[0].name,
      fileType = e.target.files[0].type;
    if (fileType.split("/")[0] === "audio") {
      uploadedActions(filename);
    } else {
      alert("This is not an audio file.");
      uploadBox.classList.remove("active");
    }
  });
})();
