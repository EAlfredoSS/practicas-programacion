<html>
<head>

  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
 <script>
 window.dataLayer = window.dataLayer || [];
 function gtag(){dataLayer.push(arguments);}
 gtag('js', new Date());
 gtag('config', 'UA-139626327-1');
 </script>
 
</head>
<body>

<?php echo "<br>";   ?>  
	
	
<br/><br/><center>

Message sent successfully. You will be redirected to your personal area.

<br/><br/>

    <progress value="0" max="10" id="progressBar"></progress>
	
	<script type="text/javascript">
        var timeleft = 3;
var downloadTimer = setInterval(function(){
  document.getElementById("progressBar").value = 10 - timeleft;
  timeleft -= 1;
  if(timeleft <= 0){
    clearInterval(downloadTimer);
	
	window.location.href='../user/me.php';
	
  }
}, 1000);
    </script>
	
</center>
	
</body>
</html>
