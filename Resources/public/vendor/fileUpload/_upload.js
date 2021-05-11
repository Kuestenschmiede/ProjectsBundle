/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

/** *********************************************************************************************** *\
|** Image Upload for wswgEditor
|** Version: 0.1 (alpha)
|** Last Modified Date: 2013-Sptember-25
|** License: LGPL (http://opensource.org/licenses/lgpl-3.0.html)
|** Author: Tobias Dobbrunz (Küstenschmiede GmbH Software & Design)
|** EMail: tobias.dobbrunz@kuestenschmiede.de
|** URL: http://www.kuestenschmiede.de
\** *********************************************************************************************** */

function handleFiles( files ) 
{ 
    var i = 0;
    var previewDiv = document.getElementById('preview');
 
    var fileList = files;
 
    for(i = 0; i < fileList.length; i++) {
        var img = document.createElement("img");    
        img.height = 110;
        img.file = fileList[i];
        img.name = 'pic_'+ i;
        img.classList.add("obj");

        var reader = new FileReader();
        reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
        reader.readAsDataURL(fileList[i]);

        previewDiv.appendChild(img);
        previewDiv.setAttribute("style", "border:none;");    
    }
}

function handleFile( files ) 
{ 
    var i = 0;
    var previewDiv = document.getElementById('preview');

    var fileList = files;
 
    for(i = 0; i < fileList.length; i++) {
        var img = document.createElement("img");    
        img.height = 110;
        img.file = fileList[i];
        img.name = 'img_'+ i;
        img.classList.add("obj");

        var reader = new FileReader();
        reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
        reader.readAsDataURL(fileList[i]);  

        previewDiv.replaceChild( img, previewDiv.firstChild );
        previewDiv.setAttribute( "style", "border:none;" );    
    }
}

function sendFiles()
{
    var i = 0;
    var imgs = document.querySelectorAll(".obj");
  
    for(i = 0; i < imgs.length; i++) {
        new FileUpload(imgs[i], imgs[i].file);
    }
 
}

function sendFile( path )
{
    var img = document.querySelectorAll(".obj");  
    FileUpload(img[0].file, path);
}

function FileUpload( file, path ) 
{
    var xhr = new XMLHttpRequest();
    
    //var percent;
 
    // xhr.upload.addEventListener("progress", function(e) {
    //     if (e.lengthComputable) {
    //         prozent = Math.round((e.loaded * 100) / e.total);
    //     }
    // }, false);
  
    // xhr.upload.addEventListener("load", function(e){
    //     prozent  = 100;
    // }, false);

    var fd = new FormData;
    fd.append("File", file);
    fd.append("Path", path);
    fd.append("REQUEST_TOKEN", c4g_rq);

    xhr.onreadystatechange = function(){
        if (xhr.readyState==4 && xhr.status==200){
            wswgEditor.insertImage( xhr.responseText, true );
        }
    }

    xhr.open("POST", "bundles/con4giscore/vendor/fileUpload/upload.php", true);
    xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
 
    xhr.send(fd);
}