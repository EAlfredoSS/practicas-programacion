<?php
$id_contactado=$_GET['uid'];
?> 

<html>
<head>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-BKST5BVLT3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-BKST5BVLT3');
</script>
 
</head>
<body>

<?php echo "<br>";   ?>  
	
	
<br/><br/><center>

Message being sent...

<br/><br/>

    <progress value="0" max="10" id="progressBar"></progress>
	
	<script type="text/javascript">
        var timeleft = 3;
var downloadTimer = setInterval(function(){
  document.getElementById("progressBar").value = 10 - timeleft;
  timeleft -= 1;
  if(timeleft <= 0){
    clearInterval(downloadTimer);
	
	window.location.href='../user/u.php?identificador=<?php echo $id_contactado; ?>';
	
  }
}, 1000);
    </script>
	
</center>
	
</body>
</html>
