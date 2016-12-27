
<?php session_start() ?>
<!DOCTYPE HTML>
<html>
<head>
	<script>
		function getchatroom(){

			window.open ("http://192.168.0.10:55151/?room_id="+mySelect+"", "<?php echo $_SESSION['user'];?>");
		}
	</script>
  <title>Canvas Based Thumbnail</title>
  <style type="text/css">
    body {
    background: black;
    color: white;
    /*font: 20pt Cambria, Georgia, Times, Times New Roman, serif;*/
    font: 24pt Baskerville, Times, Times New Roman, serif;
    padding: 0;
    margin: 0;
    overflow: hidden;
    }	
  </style>
<style>
div.pos
{
position:absolute;
left:45%
}
</style>
<style>
#dark {
	background-color: #333;
	border: 1px solid #000;
	padding: 10px;
	margin-top: 20px;
}

#light {
	background-color: #FFF;
	border: 1px solid #dedede;
	padding: 10px;
	margin-top: 20px;
}

li {
	list-style: none;
	padding-top: 10px;
	padding-bottom: 10px;
}

.button,.button:visited {
	background: #222 repeat-x;
	display: inline-block;
	padding: 5px 10px 6px;
	color: #fff;
	text-decoration: none;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	-moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
	-webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
	text-shadow: 0 -1px 1px rgba(0, 0, 0, 0.25);
	border-bottom: 1px solid rgba(0, 0, 0, 0.25);
	position: relative;
	cursor: pointer
}

.button:hover {
	background-color: #111;
	color: #fff;
}

.button:active {
	top: 1px;
}
.super.button,.super.button:visited {
	font-size: 34px;
	padding: 8px 14px 9px;
}

.pink.button,.magenta.button:visited {
	background-color: #749a02;
}

.pink.button:hover {
	background-color: #c81e82;
}

</style>

</head>
<body>
 <div> <canvas id="canvas"></canvas></div>
 <script type="text/javascript" src="thumbnail.js"></script>
<div id="showDiv"  class = "pos"><a class="super button pink"  onclick="getchatroom()">START</a></div>

</body>
</html>
