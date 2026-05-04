<?php
 
session_start(); //tiene que estar para hacer un sessiondestroy

session_unset();
session_destroy();

// $_SESSION['idusuario2019']=NULL;

 
// echo "<br><br>Before you leave Download our app at www.lingua2.net . Thank you.<br><br>";
 
//sleep(3);

header("Location: https://www.languageexchanges.com");
 
 ?>
 <html>
 <head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
 <script>
 window.dataLayer = window.dataLayer || [];
 function gtag(){dataLayer.push(arguments);}
 gtag('js', new Date());
 gtag('config', 'UA-139626327-1');
 </script>
 
 
 
</head>
<body>



</body>
</html>