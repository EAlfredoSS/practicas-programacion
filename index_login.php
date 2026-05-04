<?php

session_start();
//si recibimos algo por POST (input)
//ojo porque al enviar el formulario los espacios del input se convierten en '+'. el trim no vale

 // php print htmlspecialchars(print_r($user_profile, true)) 
//info del usu

require('./files/bd.php'); 

// Corrección para evitar errores "Notice/Warning" en PHP moderno
$useremail1 = $_POST['useremail'] ?? null; 
$userpassword1 = $_POST['userpassword'] ?? null; 
$formalreadysent1 = $_POST['formalreadysent'] ?? null;


//pasamos los datos al formulario por sesiones 
//session_start();  //session_start() arriba



// PHP8: Passing null to parameter #1 ($string) of type string is deprecated in /home/customer/www/languageexchanges.com/public_html/index.php
// para evitar el warning ponemos un if
if(!is_null($userpassword1) && $userpassword1 !== "")
    $userpassword1=substr(md5($userpassword1),4,84); //tal y como se encripta en el registro


// Solo ejecutamos si se ha enviado el formulario
if($formalreadysent1) {
    $query="SELECT * FROM mentor2009 WHERE Email='$useremail1' AND Password_cod='$userpassword1'"; //seleccionamos todos los campos
    $result=mysqli_query($link,$query); //die($result);

    if(!mysqli_num_rows($result))
        $credenciales_incorrectas=1;

    if($result && mysqli_num_rows($result) > 0) {
        $fila=mysqli_fetch_array($result); //print_r($fila); 
        $codigo3=$fila['Codigoborrar'];
        $user_id22=$fila['orden'];
        $_SESSION['codigolingua2']=$codigo3;
        $_SESSION['loginconfb']=$codigo3;
        //este userid1 es para entrar en los mensajes privados
        $_SESSION['userid1']=$user_id22;    


        //NUEVOS 2017
        $_SESSION['codigoborrar2017']=$fila['Codigoborrar'];
        $_SESSION['orden2017']=$fila['orden'];

        //2019
        //$_SESSION['mensajesenviadosdurantelasesion']=0;

        header("Location: ./user/me.php");
        exit(); 
    }
}
?> 


<!DOCTYPE html>
<html>
    
    <head>
    
        <link rel="canonical" href="https://www.languageexchanges.com/" />
                
        <link rel="alternate" hreflang="x-default" href="https://www.languageexchanges.com/" />
        <link rel="alternate" hreflang="en" href="https://www.languageexchanges.com/" />
        
        <link rel="alternate" hreflang="ru" href="https://www.languageexchanges.com/ru/" />
        <link rel="alternate" hreflang="es" href="https://www.languageexchanges.com/es/" />     
        <link rel="alternate" hreflang="ca" href="https://www.languageexchanges.com/ca/" />     
        <link rel="alternate" hreflang="el" href="https://www.languageexchanges.com/el/" />     
        <link rel="alternate" hreflang="de" href="https://www.languageexchanges.com/de/" />     
        <link rel="alternate" hreflang="it" href="https://www.languageexchanges.com/it/" />     
        
    
    
    
<script async src="https://www.googletagmanager.com/gtag/js?id=G-NYB9FFBL5J"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-NYB9FFBL5J');
</script>

<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TSHHJ2LL');</script>
<title>Language exchange, AI, teachers, jobs and group activities | Lingua2</title>
        
        <meta name="description" content="Language Exchange Tandems, AI, Teachers and Events. Find language jobs, translation jobs and international companies.">
        <meta name="keywords" content="language exchange, tandem, language teacher, AI, language jobs, translation jobs, job offers, companies">
                
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

        <link href="h/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="h/css/font-awesome.min.css" rel="stylesheet">
        <link href="h/fonts/icon-7-stroke/css/pe-icon-7-stroke.css" rel="stylesheet">
        <link href="h/css/animate.css" rel="stylesheet" media="screen">
        <link href="h/css/owl.theme.css" rel="stylesheet">
        <link href="h/css/owl.carousel.css" rel="stylesheet">

        <link href="h/css/css-index-orange.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css" />
    </head>

    <body data-spy="scroll" data-target="#navbar-scroll">
    
    
    
    
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TSHHJ2LL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<div id="preloader"></div>
        <div id="top"></div>

        <div class="fullscreen landing parallax" style="background-image:url('h/images/bg.jpg');" data-img-width="2000" data-img-height="1333" data-diff="100">

            <div class="overlay">
                <div class="container">
                    <div class="row">
                        <div class="col-md-7">

                            <div class="logo wow fadeInDown"> <a href=""><img src="h/images/logo.png" alt="logo"></a></div>

                            <h1 class="wow fadeInLeft">
                                Language exchange assisted by AI with Teachers, Groups and Job offers
                            </h1>

                            <div class="landing-text wow fadeInUp">
                                <p>Find language partners for <b>more than 8,000 languages and sublanguages</b> and practice languages onsite and online with native speakers: one-on-one conversations assited by AI or group meetings. 
                                Find your language job offers and translation jobs from international companies. Get in touch with interesting people from different cultures willing to practice languages! ...and if you speak a minority language, find your community!</p>

                        
                            
                            
                            
                            
                            </div>                

                            <div class="head-btn wow fadeInLeft">
                                <a href="./registration/" class="btn-primary">Register now</a>
                                </div>



                        </div> 

                        <div class="col-md-5">

                            <div class="signup-header wow fadeInUp">
                                <h3 class="form-title text-center">ENTER</h3>
                                <form class="form-header" action="<?php echo $_SERVER['PHP_SELF']; ?>" role="form" method="POST" id="loginform">
                                    
                    <div class="form-group">
                                        <input class="form-control input-lg" name="useremail" id="email" type="email" autocomplete="email" placeholder="Your email" required>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control input-lg" name="userpassword" id="password" type="password" autocomplete="current-password" placeholder="Your password" required>
                                    </div>
                                    <div class="form-group last">
                                        <input type="submit" class="btn btn-warning btn-block btn-lg" name="formalreadysent" value="LOG IN">
                                    </div>
                    
                                 <p class="privacy text-center" style="color:red; font-size: 18px; font-weight: 400;">  
                                    <?php 
                                        if(isset($credenciales_incorrectas)) {echo "Wrong email or password. Try again.";}
                                    ?>
                                </p>
                                    
                                    <p class="privacy text-center">Not registered yet? <a href="./registration">Sign up now</a>.</br> 
                                    Forgot password? <a href="./recoveryandunregistration/passwordrecovery.php">Password recovery</a>.</p>
                                </form>
                            </div>              

                        </div>
                    </div>
                </div> 
            </div> 
        </div>

        <div id="menu">
            <nav class="navbar-wrapper navbar-default" role="navigation">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-backyard">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        </div>

                    <div id="navbar-scroll" class="collapse navbar-collapse navbar-backyard navbar-right">
                        <ul class="nav navbar-nav">
                            
                            <li><a href="./registration/">Registration</a></li>
                            <?php //<li><a href="https://blog.languageexchanges.com/">Our Blog</a></li>  ?>
                            <?php //<li><a href="https://blog.languageexchanges.com/index.php/funders/">Our Funders</a></li>  ?>
                            <li><a href="https://www.languageexchanges.com/terms.pdf" target="_blank">Terms</a></li>
                            <li><a href="https://www.languageexchanges.com/privacy-policy.pdf" target="_blank">Privacy Policy</a></li>
                            <li><a href="https://blog.languageexchanges.com/about-us/">About us</a></li>
                            <li><a href="https://blog.languageexchanges.com/contact-us/">Contact us</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>

        <div id="intro">
            <div class="container">
                <div class="row">

                    <div class="col-md-6 intro-pic wow slideInLeft">
                        <img src="h/images/intro-image.jpg" alt="image" class="img-responsive">
                    </div>  

                    <div class="col-md-6 wow slideInRight">
                        <h2>Language exchange onsite or online with cutting edge Artificial Intelligence tools</h2>
                        <p>A conversation exchange or tandem consists of contacting a language partner and meet up. You can practice languages 50% of the time in each of your native languages. We will provide you AI support in order to make your learning process more efficient. </p>

                    </div>
                </div>            
            </div>
        </div>

        <div id="feature">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1 col-sm-12 text-center feature-title">

                        <h2>Enterprises, Language Schools or Independent Users</h2>
                        <p>We offer taylor-made solutions for not only independent users. International companies can do team-building among their employees, promote the use of foreign languages and connect them with the best language schools.</p>
                    </div>
                </div>
                <div class="row row-feat">
                    <div class="col-md-4 text-center">

                        <div class="feature-img">
                            <img src="h/images/feature-image.jpg" alt="image" class="img-responsive wow fadeInLeft">
                        </div>
                    </div>

                    <div class="col-md-8">

                        <div class="col-sm-6 feat-list">
                            <i class="pe-7s-chat pe-5x pe-va wow fadeInUp"></i>
                            <div class="inner">
                                <h4>Artificial Intelligence (AI) applied to languages</h4>
                                <p>AI will help you to determine the level of your languages and assist your during your meetings.
                                </p>
                            </div>
                        </div>

                        <div class="col-sm-6 feat-list">
                            <i class="pe-7s-cash pe-5x pe-va wow fadeInUp" data-wow-delay="0.2s"></i>
                            <div class="inner">
                                <h4>Professional or amateur language teachers</h4>
                                <p>If you are a language teacher, find customers easily. You can teach one-on-one or you can teach a group of students offline or online.</p>
                            </div>
                        </div>

                        <div class="col-sm-6 feat-list">
                            <i class="pe-7s-world pe-5x pe-va wow fadeInUp" data-wow-delay="0.4s"></i>
                            <div class="inner">
                                <h4>Minority languages are welcomed</h4>
                                <p>Find any of the +8,000 living or extincted languages around the world an build a community.</p>
                            </div>
                        </div>

                        <div class="col-sm-6 feat-list">
                            <i class="pe-7s-users pe-5x pe-va wow fadeInUp" data-wow-delay="0.6s"></i>
                            <div class="inner">
                                <h4>Team-building for organizations</h4>
                                <p>If your organization is registered in Lingua2, you can find other co-workers around the world.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="feature-2">
            <div class="container">
                <div class="row">

                    <div class="col-md-6 wow fadeInLeft">
                        <h2>Attend or set up language and cultural meetings in your city</h2>
                        <p>You will not only improve your language skills, but also make new friends worlwide and learn their culture. You can either join to any event published in Lingua2 or create your own in any city of the world. 
                         </p>

                        <div class="btn-section"><a href="http://www.lingua2.net" class="btn-default">Donate to Lingua2</a></div>

                    </div>

                    <div class="col-md-6 feature-2-pic wow fadeInRight">
                        <img src="h/images/feature2-image.jpg" alt="macbook" class="img-responsive">
                    </div>                
                </div>            

            </div>
        </div>


        <div id="download">
            <div class="action fullscreen parallax" style="background-image:url('h/images/bg3.jpg');" data-img-width="2000" data-img-height="1333" data-diff="100">
                <div class="overlay">
                    <div class="container">
                        <div class="col-md-8 col-md-offset-2 col-sm-12 text-center">

                            <h2 class="wow fadeInRight">A language tandem now?</h2>
                            <p class="download-text wow fadeInLeft">There are thousands of people waiting for you to practice languages.</p>

                            <div class="download-cta wow fadeInLeft">
                                <a href="#top" class="btn-secondary">Start now</a>
                            </div>
                        </div>  
                    </div>  
                </div>
            </div>
        </div>


 

        <div id="contact">
            <div class="contact fullscreen parallax" style="background-image:url('h/images/bg.jpg');" data-img-width="2000" data-img-height="1334" data-diff="100">
                <div class="overlay">
                    <div class="container">
                        <div class="row contact-row">

                            <div class="col-sm-5 contact-left wow fadeInUp">
                                <h2><span class="highlight">Get</span> in touch</h2>
                                <ul class="ul-address">
                                    <li><i class="pe-7s-map-marker"></i>Spain</br>
                                    </li>
                                    <li><i class="pe-7s-phone"></i>not available
                                    </li>
                                    <li><i class="pe-7s-mail"></i><a href="mailto:webmaster@languageexchanges.com">webmaster@languageexchanges.com</a></li>
                                    <li><i class="pe-7s-look"></i><a href="#">www.lingua2.com</a></li>
                                </ul>   

                            </div>

                            </div>
                    </div>
                </div>
            </div>
        </div>

        <footer id="footer">
            <div class="container">
                <div class="col-sm-4 col-sm-offset-4">
                    <div class="social text-center">
                        <ul>
                            <li><a class="wow fadeInUp" href="https://twitter.com/lingua2"><i class="fa fa-twitter"></i></a></li>
                            <li><a class="wow fadeInUp" href="https://www.facebook.com/lingua2web/" data-wow-delay="0.2s"><i class="fa fa-facebook"></i></a></li>
                            <li><a class="wow fadeInUp" href="https://www.instagram.com/lingua2_" data-wow-delay="0.2s"><i class="fa fa-instagram"></i></a></li>
                            </ul>
                    </div>  
                    <div class="text-center wow fadeInUp" style="font-size: 14px;">Copyright Lingua2 &reg;</a></div>
                    <a href="#" class="scrollToTop"><i class="pe-7s-up-arrow pe-va"></i></a>
                </div>  
            </div>  
        </footer>

        <script src="h/js/jquery.js"></script>
        <script src="h/js/bootstrap.min.js"></script>
        <script src="h/js/custom.js"></script>
        <script src="h/js/jquery.sticky.js"></script>
        <script src="h/js/wow.min.js"></script>
        <script src="h/js/owl.carousel.min.js"></script>
        <script>
                                    new WOW().init();
        </script>
        <script src="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js" data-cfasync="false"></script>
        <script>
            window.cookieconsent.initialise({
            "palette": {
                "popup": {
                    "background": "#237afc"
                },
            "button": {
                "background": "#fff",
                "text": "#237afc"
                }
            },
            "theme": "classic",
            "position": "top",
            "static": true
            });
            </script>
    </body>
</html>