<head>
<link rel="stylesheet" href="styles.css">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
<script src="reimg.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="jquery.tancolor.js"></script>
<script src="jscolor.js"></script>
</head>
<body>
<canvas width="1280" height="720" id="canvas2" style="z-index: 0; position: absolute;"></canvas>
<canvas width="1280" height="720" id="canvas" style="background-color: none; z-index: 1; position: absolute; background: none;"></canvas>
<span class="sidebar">
<input class="jscolor" onchange="update(this.jscolor)" value="000000" id="color1"><br>
<input class="jscolor" onchange="update2(this.jscolor)" value="FFFFFF" id="color2"><br>
<button onclick="ReImg.fromCanvas(document.getElementById('canvas2')).downloadPng()"  class="buttonstyle">Save</button><br>
<button onclick="undo()" class="buttonstyle">Undo</button><br>
<button onclick="mode()" id="mode" class="buttonstyle">Eraser</button><br>
<button onclick="pickf()" id="pick" class="buttonstyle">Pick</button><br>
<button onclick="panf()" id="pan" class="buttonstyle">Pan</button>
 <div id="myDIV" class="Brushes">
<button class="btn active" id="1"><img src="brush2.png.1" height="50%" width="50%"></button>
<button class="btn" id="2"><img src="brush2.png.2" height="50%" width="50%"></button><br>
<button class="btn" id="3"><img src="brush2.png.3" height="50%" width="50%"></button>
<button class="btn" id="4"><img src="brush2.png.4" height="50%" width="50%"></button>
</div> 
<span style="display: flex;" class="slidebox">

<input title="Brush Diameter" id="slide" class="slide" orient="vertical" type="range" min="5" max="100" step="1" value="32" onchange="updateSlider(this.value)">

<input title="Canvas Zoom (todo)" id="slide2" class="slide" orient="vertical" type="range" min="90" max="400" step="1" value="100" onchange="updateZoom(this.value)">

</span>
<span style="display: flex;" class="slidebox">
<div id="sliderAmount" class="amount">32</div>
<div id="sliderAmount2" class="amount">100</div>
</span>
</span>
<script>
//----------MAIN VARIABLE DEFINE START----------
var r, g, b;
r = 0;
g = 0;
b = 0;
var r2, g2, b2;
var click;
var id = 0;
r2 = 255;
g2 = 255;
b2 = 255;
var pick = false;
var pan = false;
var erasing = false;
var img = new Image();
var img2 = new Image();
var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
var ro = 1;
//Brush image from http://www.tricedesigns.com/wp-content/uploads/2012/01/brush2.png
img.src = 'brush2.png.1';
img2.src = 'brush1.png.1';
img.id = 'brush';
img.style.width = 10;
var el = document.getElementById('canvas');
var back = document.getElementById('canvas2');
var context = back.getContext('2d');
var ctx = el.getContext('2d');
ctx.lineJoin = ctx.lineCap = 'round';
var isDrawing, lastPoint;
var list = [];
var right;
right = 2;
//----------MAIN VARIABLE DEFINE END----------
//----------MOBILE DEVICE FUNCTIONS START---------

//----------MOBILE DEVICE FUNCTIONS END---------
//----------MOUSE RELATED FUNCTIONS START-----------
//----------MOUSE CLICK START CALL FUNCTION START----------
el.onmousedown = function(e) {
  if (pan == true) {
    if (e.button === right) {
back.style.top = "0.5%";
    back.style.left = "5.5%";
    el.style.top = "0.5%";
    el.style.left = "5.5%";
    }
     e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
}
 else if (e.button === right) {
 click = 'right';
 }
 else if (e.button != right) {
 click = 'left';
 }
 if (pick == true) {
  pickcolor(e);
 }
 isDrawing = true;
    var rect = canvas.getBoundingClientRect();
    var xf = e.clientX - rect.left;
    var yf = e.clientY - rect.top;
  lastPoint = { x: xf, y: yf };
};

//----------MOUSE CLICK START CALL FUNCTION END----------
//----------MOUSE MOVE CALL FUNCTION START----------
el.onmousemove = function(e) {
  if (!isDrawing) return;
  else if (pan == true) {
    e = e || window.event;
    e.preventDefault();
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    back.style.top = (back.offsetTop - pos2) + "px";
    back.style.left = (back.offsetLeft - pos1) + "px";
    el.style.top = (el.offsetTop - pos2) + "px";
    el.style.left = (el.offsetLeft - pos1) + "px";
  }
  else if (erasing == true) {
eraser(e);
return;
} 
else {
  var rect = canvas.getBoundingClientRect();
  var scale = el.style.width.slice(0, -1) / 100;
    var xf = e.clientX - rect.left;
    var yf = e.clientY - rect.top;
  var currentPoint = { x: xf, y: yf };
  var dist = distanceBetween(lastPoint, currentPoint);
  var angle = angleBetween(lastPoint, currentPoint);
    var sliderDiv = document.getElementById("sliderAmount");
   var htmlstring = sliderDiv.innerHTML;
   htmlstring = (htmlstring.trim) ? htmlstring.trim() : htmlstring.replace(/^\s+/,'');
  var add = htmlstring - 2;
  for (var i = 0; i < dist; i++) {
    x = lastPoint.x + (Math.sin(angle) * i) - sliderDiv.innerHTML;
    y = lastPoint.y + (Math.cos(angle) * i) - sliderDiv.innerHTML;
   if (htmlstring == '') {
    if (click == 'left') {
    var Currentpoint = { x: x + 16, y: y + 16, w: 32, r: r, g: g, b: b, id: id};
    }
    else if (click == 'right') {
    var Currentpoint = { x: x + 16, y: y + 16, w: 32, r: r2, g: g2, b: b2, id: id};
    }
  list.push(Currentpoint);
    setcolors(x + 16, y + 16, 32);
    ctx.drawImage(img, (x + 16), (y + 16), 30, 30);
    setcolors(x + 16, y + 16, 32);
    }
    else {
    if (click == 'left') {
    var Currentpoint = { x: x + htmlstring / 2, y: y + htmlstring / 2, w: htmlstring, r: r2, g: g2, b: b2, id: id, ro: ro };
    }
    else if (click == 'right') {
    var Currentpoint = { x: x + htmlstring / 2, y: y + htmlstring / 2, w: htmlstring, r: r2, g: g2, b: b2, id: id, ro: ro };
    }
  list.push(Currentpoint);
    setcolors(x + htmlstring / 2, y + htmlstring / 2, htmlstring);
      ctx.drawImage(img, (x + htmlstring / 2), (y + htmlstring / 2), add, add);
    setcolors(x + htmlstring / 2, y + htmlstring / 2, htmlstring);
    }
  }
  lastPoint = currentPoint;
}
};
//----------MOUSE MOVE FUNCTION CALL END----------
//----------MOUSE CLICK RELATED FUNCTIONS START----------
el.onmouseout = async function(e) {
if (isDrawing != false) {
isDrawing = false;
id = id + 1;
context.drawImage(el, 0, 0);
ctx.clearRect(0, 0, el.width, el.height);
}
};
el.onmouseup = function(e) {
  isDrawing = false;
  id = id + 1;
context.drawImage(el, 0, 0);
ctx.clearRect(0, 0, el.width, el.height);
};
//----------MOUSE CLICK RELATED FUNCTIONS END----------
//----------MOUSE RELATED FUNCTIONS END----------
//----------SIDEBAR TOOLS RELATED FUNCTIONS START----------
//----------COLOR PICKER FUNCTION START----------
function pickcolor(e) {
  var rect = canvas.getBoundingClientRect();
    var xf = e.clientX - rect.left;
    var yf = e.clientY - rect.top;
    var imageData = context.getImageData(xf, yf, 1, 1);
    var data = imageData.data;
for (var i = 0; i < data.length; i+= 4) {
if (click === 'left') {
r = data[i];
g = data[i + 1];
b = data[i + 2];
var color1 = document.getElementById("color1");
color1.value = rgb2hex(r, g, b);
}
else if (click === 'right') {
r2 = data[i];
g2 = data[i + 1];
b2 = data[i + 2];
var color2 = document.getElementById("color2");
color2.value = rgb2hex(r2, g2, b2);
}
}
pickf();
}
//----------COLOR PICKER FUNCTION END----------
//----------ERASER FUNCTION START----------
function eraser(e) {
  var rect = canvas.getBoundingClientRect();
    var xf = e.clientX - rect.left;
    var yf = e.clientY - rect.top;
  var currentPoint = { x: xf, y: yf };
  var dist = distanceBetween(lastPoint, currentPoint);
  var angle = angleBetween(lastPoint, currentPoint);
  for (var i = 0; i < dist; i++) {
    var sliderDiv = document.getElementById("sliderAmount");
   var htmlstring = sliderDiv.innerHTML;
    x = lastPoint.x + (Math.sin(angle) * i) - sliderDiv.innerHTML;
    y = lastPoint.y + (Math.cos(angle) * i) - sliderDiv.innerHTML;
   htmlstring = (htmlstring.trim) ? htmlstring.trim() : htmlstring.replace(/^\s+/,'');
   if (htmlstring == '') {
    var imageData = context.getImageData(x, y, 32, 32);
var data = imageData.data;
for (var i = 0; i < data.length; i+= 4) {
data[i+3] = 0;
}
context.putImageData(imageData, x, y);
    }
    else {
var imageData = context.getImageData(x, y, htmlstring, htmlstring);
var canvas2 = document.createElement('canvas');
var context2 = canvas2.getContext('2d');
canvas2.width = htmlstring;
canvas2.height = htmlstring;
context2.drawImage(img2, 0, 0, htmlstring, htmlstring );
var myData = context2.getImageData(0, 0, htmlstring, htmlstring);
var data = imageData.data;
for (var i = 0; i < data.length; i+= 4) {
if (myData.data[i+3] < 255) {
data[i+3] = 0;
}
}
context.putImageData(imageData, x, y);
    }
  }
  lastPoint = currentPoint;
}
//----------ERASER FUNCTION END----------
//----------UNDO LAST DRAW FUNCTION START----------
function undo() {
var counts = [];
var search = id - 1;
var counts = list.reduce(function(n, val) {
    return n + (val.id === search);
}, 0);
var latest = list[list.length - 1];
var canvas2 = document.createElement('canvas');
var context2 = canvas2.getContext('2d');
canvas2.width = latest.w;
canvas2.height = latest.w;
var imgtemp = new Image();
imgtemp.src = 'brush1.png.' + latest.ro;
context2.drawImage(imgtemp, 0, 0, latest.w, latest.w );
var myData = context2.getImageData(0, 0, latest.w, latest.w);
for (var e = 0; e < counts; e+= 1) {
var latest = list[list.length - 1];
var imageDatas = context.getImageData(Math.round(latest.x), Math.round(latest.y), latest.w, latest.w);
var data = imageDatas.data;
for (var i = 0; i < data.length; i+= 4) {
if (myData.data[i+3] < 255) {
data[i+3] = 0;
}
}
context.putImageData(imageDatas, Math.round(latest.x), Math.round(latest.y));
list.splice(list.length - 1, 1);
}
id = id - 1;
}
//----------UNDO LAST DRAW FUNCTION END----------
//----------SIDEBAR TOOLS RELATED FUNCTIONS END----------
//----------BRUSH RELATED FUNCTIONS START---------
//----------BRUSH POST-DRAW COLOR FUNCTION START----------
function setcolors(x, y, w) {
var imageData = ctx.getImageData(x, y, w, w);
var data = imageData.data;
var latest = list[list.length - 1];
for (var i = 0; i < data.length; i+= 4) {
var red = data[i];
var green = data[i + 1];
var blue = data[i + 2];
var alpha = data[i + 3];
if (click == 'left') {
data[i] = r;
data[i+1] = g;
data[i+2] = b;
data[i+3] = alpha;
}
else if (click == 'right') {
data[i] = r2;
data[i+1] = g2;
data[i+2] = b2;
data[i+3] = alpha;
}
}
ctx.putImageData(imageData, x, y);
}
//----------BRUSH POST-DRAW COLOR FUNCTION END----------
//----------UPDATE BRUSH SIZE FUNCTION START----------
function updateSlider(slideAmount) {
var sliderDiv = document.getElementById("sliderAmount");
sliderDiv.innerHTML = slideAmount;
}
//----------UPDATE BRUSH SIZE FUNCTION END----------
function updateZoom(slideAmount) {
  var sliderDiv = document.getElementById("sliderAmount2");
  sliderDiv.innerHTML = slideAmount;
  var image = ReImg.fromCanvas(document.getElementById('canvas2')).toImg();
  image.onload = function () {
    document.getElementById("canvas").width = 1280 / 100 * slideAmount;
  document.getElementById("canvas2").width = 1280 / 100 * slideAmount;
    document.getElementById("canvas").height = 720 / 100 * slideAmount;
  document.getElementById("canvas2").height = 720 / 100 * slideAmount;
  
context.drawImage(image, 0, 0, 1280 / 100 * slideAmount, 720 / 100 * slideAmount);
}
}

//----------BRUSH RELATED FUNCTIONS END----------
//----------MISC FUNCTIONS START----------
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}
function reverseNumber(num, min, max) {
    return (max + min) - num;
}
  function rgb2hex(red, green, blue) {
        var rgb = blue | (green << 8) | (red << 16);
        return (0x1000000 + rgb).toString(16).slice(1)
  }
  $("#canvas").contextmenu(function(e) {
    //
    e.preventDefault();
    e.stopPropagation();
});
function distanceBetween(point1, point2) {
  return Math.sqrt(Math.pow(point2.x - point1.x, 2) + Math.pow(point2.y - point1.y, 2));
}
function angleBetween(point1, point2) {
  return Math.atan2( point2.x - point1.x, point2.y - point1.y );
}
//----------MISC FUNCTIONS END----------
</script>
<script> 
//----------SIDEBAR UPDATE RELATED FUNCTIONS START----------
//----------PAN SET FUNCTION START----------
function panf() {
  pan = !pan;
  if (pan == false) {
    document.getElementById("pan").style.backgroundColor = "#f1f1f1";
  }
  else {
    document.getElementById("pan").style.backgroundColor = "#666";
  }
}
//----------PAN SET FUNCTION END----------
//----------PICK SET FUNCTION START----------
function pickf() {
  pick = !pick;
  if (pick == false) {
    document.getElementById("pick").style.backgroundColor = "#f1f1f1";
  }
  else {
    document.getElementById("pick").style.backgroundColor = "#666";
  }
}
//----------PICK SET FUNCTION END----------
//----------UPDATE BRUSH DIRECTION BUTTON START----------
var btnContainer = document.getElementById("myDIV");
var btns = btnContainer.getElementsByClassName("btn");
for (var i = 0; i < btns.length; i++) {
  btns[i].addEventListener("click", function() {
    var current = document.getElementsByClassName("active");
    current[0].className = current[0].className.replace(" active", "");
    this.className += " active";
    img.src = 'brush2.png.' + this.id;  
    img2.src = 'brush1.png.' + this.id;
    ro = this.id;
    console.log(img.src);
  });
} 
//-----------UPDATE BRUSH DIRECTION BUTTON END----------
//----------2ND COLOR UPDATE FUNCTION START----------
function update2(hex) {
var bigint = parseInt(hex, 16);
    r2 = (bigint >> 16) & 255;
    g2 = (bigint >> 8) & 255;
    b2 = bigint & 255;
}
//----------2ND COLOR UPDATE FUNCTION END----------
//----------1ST COLOR UPDATE FUNCTION START----------
function update(hex) {
var bigint = parseInt(hex, 16);
    r = (bigint >> 16) & 255;
    g = (bigint >> 8) & 255;
    b = bigint & 255;
}
//----------1ST COLOR UPDATE FUNCTION END----------
//----------ERASER/BRUSH SET FUNCTION START----------
function mode() {
if (erasing == true) {
erasing = false;
var button = document.getElementById("mode");
button.innerHTML = 'Eraser';
}
else {
 erasing = true;
var button = document.getElementById("mode");
button.innerHTML = 'Pen';
}
}
//----------ERASER/BRUSH SET FUNCTION END----------
//-----------SIDEBAR UPDATE RELATED FUNCTIONS END----------
</script>
</body>