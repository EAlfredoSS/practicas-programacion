<?php

require('../files/bd.php');

$codborr2=$_GET['c'];

?>
<head>

<title>Email verification | Lingua2</title>

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


</body>


<?php


if(empty($codborr2))
{
	die('Forbidden');	
}

//vemos si ya fue validado anteriormente
$query="SELECT * FROM mentor2009 WHERE Codigoborrar='$codborr2' AND Emailverif<>0";  

$result=mysqli_query($link, $query);
if(mysqli_num_rows($result))
		die("Your email was already verified previously.");


//vemos si existe el código. si no existe, error
$query="SELECT * FROM mentor2009 WHERE Codigoborrar='$codborr2'";  

$result=mysqli_query($link, $query);
if(!mysqli_num_rows($result))
		die("error 8565.");



$time1=time();

$sql = "UPDATE mentor2009 SET Emailverif='$time1' WHERE Codigoborrar='$codborr2'";

if (mysqli_query($link, $sql)) {
  echo "Email verified successfully. Now log in <a href=\"https://www.languageexchanges.com/\">Lingua2</a>.";
} else {
  echo "Error updating record: " . mysqli_error($link);
}

sleep(2);

?>



