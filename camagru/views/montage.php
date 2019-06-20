<?php
session_start();
if (!isset($_SESSION['logged_on_user']))
{
    $_SESSION['flash']['error'] = "Need to  be connected for see this page";
    header('Location:/controllers/image.php?action=showpage&page=1');
    exit();
}
require_once 'print_header.php';
?>

<html>
<head> 
    <meta charset="utf-8">
    <link rel="stylesheet" href="/common/css/header.css">
    <link rel="stylesheet" href="/common/css/montage.css">
    <script src="/common/js/main.js"></script>
</head>


<?php
require_once 'print_flash.php';
require_once 'print_error.php';  
require_once 'print_header.php'; 
?>
<body>

<div class="wrapper">
        
        <div class="camera" id="camera">

            <div class="calques" id="calques">
                <div id="calque_wrapper"></div>
            </div>

            <div class="stream" id="stream">
                <video id="video">Video stream not available.</video>
                <button class="startbutton" id="startbutton" disabled></button>
            </div>

        </div>

        <div class="result">

            <div class="upload" id="upload">
                <h1>Uplaod</h1>
                <div id="prev"></div>
                <label class="uploadButton" for="uploadButton"> Upload </label>
                <input class="huploadButton" id="uploadButton" type="file"/>
            </div>

            <div class="user_gallery" id="user_gallery">
                <h1> My images </h1>
                <div id="img_wrapper"></div>
                <input type="button" class="btn-more" value="More images" onclick="getImg();"/>
            </div>

        </div>
</div>
<canvas id="canvas"></canvas>



<script>

function getXMLHttpRequest() {
    var xhr = null;
	
	if (window.XMLHttpRequest || window.ActiveXObject) {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} else {
			xhr = new XMLHttpRequest(); 
		}
	} else {
		alert("Your browser does not support XMLHTTPRequest object...");
		return null;
	}
	
	return xhr;
}

function nowebcam()
{
    alert('no webcam');
    
    var h1 = document.createElement("H1");
    var t = document.createTextNode("No webcam avaliable");
    h1.appendChild(t);  
    document.getElementById('stream').appendChild(h1);
}

var page = 1;
var selected = null;

function del(id) {
	var xhr = getXMLHttpRequest();
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
            removeElement(id);
            page--;
		}
	};
	
	xhr.open("GET", "/controllers/image.php?action=del&id=" + id, true);
	xhr.send(null);
}


function getImg() {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
        {
            readImg(xhr.responseText);
            page++;
		}
	};
	
	xhr.open("GET", "/controllers/image.php?action=showimage&imgnb=1&page=" + page, true);
	xhr.send(null);
}


function readImg(data) {

    if (data != "null")
    {
        data = JSON.parse(data);

        var body = document.getElementById('img_wrapper');
        var table = document.createElement('table');
        var tbdy = document.createElement('tbody');

        for (var i = 0, len = data.length; i < len; i++) {

            var tr = document.createElement('tr');
            var td = document.createElement('td');
            var img = new Image();
            var input = document.createElement("input");

            td.id = data[i]['id'];
            td.classList.add('figure');
            img.src = data[i]['path'];
            img.classList.add("gallery-image"); 
            input.type = "button";
            input.value = "Delete it";
            input.classList.add('btn-del');
            input.onclick = function() {
                if (confirm("delete: this image ?")) {
                    del(td.id);
                } 
            };
            td.appendChild(img);
            td.appendChild(input);
            tr.appendChild(td);
            tbdy.appendChild(tr);

        }
        table.appendChild(tbdy);
        body.appendChild(table);
    }
    else
    {
        alert("No more image avaliable");
    }
}


function getCalque() {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
        {
            readCalque(xhr.responseText);
		}
	};
	
	xhr.open("GET", "/controllers/calque.php?action=showcalque", true);
	xhr.send(null);
}

function readCalque(data) {

    if (data != "null")
    {
        data = JSON.parse(data);

        var body = document.getElementById('calque_wrapper');
        var table = document.createElement('table');
        var tbdy = document.createElement('tbody');

        for (var i = 0, len = data.length; i < len; i++) {

            var tr = document.createElement('tr');
            var td = document.createElement('td');
            var img = new Image();

            td.id = data[i]['id'];
            img.src = data[i]['path'];
            img.width = "90";
            img.height = "90";
            td.onclick = function() {
                    if (selected !== null)
                    {
                        old = document.getElementById(selected);
                        old.children[0].border = 0;
                        old.children[0].id = "";
                    }
		document.getElementById('startbutton').disabled = false;
                    selected = this.id;
                    this.children[0].border = 2;
                    this.children[0].id = "selected";
            }
            td.appendChild(img);
            tr.appendChild(td);
            tbdy.appendChild(tr);

        }
        table.appendChild(tbdy);
        body.appendChild(table);
    }
    else
    {
        alert("No more image avaliable");
    }
}


function getLastImg() {
	var xhr = getXMLHttpRequest();
	
	xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) 
        {
            readLastImg(xhr.responseText);
            page++;
		}
	};
	
	xhr.open("GET", "/controllers/image.php?action=showimage&imgnb=1&page=1", true);
	xhr.send(null);
}

function readLastImg(data) {

    data = JSON.parse(data);

    var body = document.getElementById('img_wrapper');
    var table = document.createElement('table');
    var tbdy = document.createElement('tbody');
    var tr = document.createElement('tr');
    var td = document.createElement('td');

    var img = new Image();
    var input = document.createElement("input");

    td.id = data[0]['id'];
    td.classList.add('figure');
    img.src = data[0]['path'];
    img.classList.add("gallery-image"); 
    input.type = "button";
    input.value = "Delete it";
    input.classList.add('btn-del');
    input.onclick = function() {
        if (confirm("delete: this image ?")) {
            del(td.id);
        } 
    };
    td.appendChild(img);
    td.appendChild(input);
    tr.appendChild(td);
    tbdy.appendChild(tr);

    table.appendChild(tbdy);
    body.insertBefore(table, body.firstChild);
}


function removeElement(elementId) {
    var element = document.getElementById(elementId);
    element.parentNode.removeChild(element);
}

</script>
<script src="/common/js/upload.js"></script>
<script src="/common/js/webcam.js"></script>

<?php  require_once 'print_footer.php'; ?>