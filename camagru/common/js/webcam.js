(function() {
  
  var width = 300;
  var height = 0;

  var streaming = false;
  var canvas = null;
  var video = null;
  var startbutton = null;

  function startup() {
    video = document.getElementById('video');
    startbutton = document.getElementById('startbutton');
    canvas = document.getElementById('canvas');

    // Sream webcam video
        navigator.mediaDevices.getUserMedia({video: { facingMode: "user" }, audio: false}).then(function(stream) {
          video.srcObject = stream;
          video.play();
          getCalque();
          document.getElementById('stream').style.visibility="visible";
          
        })
        .catch(function(err) {
          nowebcam();
        });

    //set wdth and height of stream
        video.addEventListener('canplay', function(ev){
          if (!streaming) {
            height = video.videoHeight / (video.videoWidth/width);
            if (isNaN(height)) {
              height = width / (4/3);
            }
          
            video.setAttribute('width', width);
            video.setAttribute('height', height);
            streaming = true;
          }
        }, false);

    //call takepicture function when we click on
      startbutton.addEventListener('click', function(ev){
        takepicture();
        ev.preventDefault();
      }, false);
  }

  function takepicture() {
    var xhr = getXMLHttpRequest();

    if (selected === null)
      alert("You need to select a calque");
    else
    {
        canvas.style.display="none";
        context = canvas.getContext('2d');
        canvas.setAttribute('width', width);
        canvas.setAttribute('height', height);
        context.drawImage(video, 0, 0, width, height);
        data = canvas.toDataURL('image/png');


        xhr.onreadystatechange = function() {
          if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
          {
            if (xhr.responseText == 'ok')
            {
                alert("Well done! See your photo in your gallery! ");
                getLastImg();
            }
            else
              alert('Your picture cannot be saved in ou database');
          }
        };

        xhr.open("POST", "/controllers/image.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("action=create&id=" + selected + "&data=" + encodeURIComponent(data));
    }
  }

  // Run startup when page is loading
  window.addEventListener('load', startup, false);
})();