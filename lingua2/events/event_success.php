<?php    

$iddelevento=$_GET['evid'];

?>  

<html>
<head>

<title>Event success | Lingua2 </title>

	<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-NYB9FFBL5J"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-NYB9FFBL5J');
</script>



<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TSHHJ2LL');</script>
<!-- End Google Tag Manager -->

 
</head>
<body>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TSHHJ2LL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->



<?php echo "<br>";   ?>  
	
	
<br/><br/><center>

Event created successfully. Detecting users near the event.

<br/><br/>

    <progress value="0" max="10" id="progressBar"></progress>
	
	<script type="text/javascript">
        var timeleft = 5;
var downloadTimer = setInterval(function(){
  document.getElementById("progressBar").value = 10 - timeleft;
  timeleft -= 1;
  if(timeleft <= 0){
    clearInterval(downloadTimer);
	
	window.location.href='./radaruserevent.php?justcreated=true&evid=<?php echo $iddelevento; ?>';
	
  }
}, 1000);
    </script>
	
</center>
	
</body>
</html>
