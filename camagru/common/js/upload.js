
document.getElementById('uploadButton').addEventListener('change', function() {
    
    var xhr = getXMLHttpRequest();
    var form = new FormData();

    xhr.open('POST', '/controllers/image.php?action=upload');
    xhr.addEventListener('load', function() {
        alert(xhr.responseText);
        getLastImg();
    });
    if (document.getElementById('uploadButton').files[0].size <= 150000)
    {
        form.append('image', document.getElementById('uploadButton').files[0]);
        xhr.send(form);
    }
    else
        alert('file too large (150KB Max)');
}, false);
