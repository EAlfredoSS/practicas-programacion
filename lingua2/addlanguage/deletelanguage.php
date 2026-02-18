<?php
    session_start();

    $logged_user_id = $_SESSION['orden2017'];
    //$logged_user_id = 5549;
	
	require('../files/bd.php'); 
	
	
	
	$query234 = "SELECT * FROM mentor2009 WHERE orden='" . $logged_user_id . "'"; //seleccionamos todos los campos 

	//echo "$query";

	$result234 = mysqli_query($link, $query234);
	if (!mysqli_num_rows($result234))
		die("User unregistered. <a href=\"http://www.lingua2.com\">Information</a>");
	$fila234 = mysqli_fetch_array($result234);

	 
	
	
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-139626327-1');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Language | Lingua2</title>
    <link rel="stylesheet" href="./addlanguage.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php require("../templates/header_simplified.html"); ?>
    <main>
        <div class="main-section">
            <div class="container">
                <div class="main-section-data">
                    <div class="row">
                        <div class="col-lg-3 col-md-4 pd-left-none no-pd"></div>
                        <div class="col-lg-6 col-md-7 no-pd">
                            <div class="main-ws-sec">
                                <div class="top-profiles">
                                    <div class="pf-hd">
                                        <h3>Delete language | Lingua2</h3>
                                    </div>
                                    <div class="post-bar" style="margin: 10px 10px 10px 10px; width: 96%">
                                        <div class="post_topbar">
										
	  Remark: it is possible to delete sublanguages. To do so, just delete the main language and all its sublanguages will be automatically deleted.
	  <br><br>
    <?php

    echo "<script>let userId = " . $logged_user_id . "</script>";
	
	/*
    var_dump($logged_user_id);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
	*/
    

    $query = "SELECT DISTINCT ln.Id 
    FROM languages_names ln
    JOIN my_langs ml ON ln.Id = ml.lang_id
    WHERE ml.id = ?
    AND ln.Id NOT IN (SELECT I_Id FROM languages_macrolanguages);";

    if ($stmt = $link->prepare($query)) {
        $stmt->bind_param("i", $logged_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }
    ?>
    <div class='titulos'>I Know:</div>
    <table border='1'>
    <tr><th>Language Code</th><th>Action</th></tr>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Id']) . "</td>";
     // echo "<td>" . htmlspecialchars($row['Inverted_name']) . "</td>";
        echo "<td><button onclick=\"deleteLanguage('" . $row['Id'] . "', $logged_user_id, 'my_langs')\">Delete</button></td>";
        echo "</tr>";
    }
    ?>
    </table>
    
    <?php
    $query = "SELECT DISTINCT ln.Id 
    FROM languages_names ln
    JOIN learn_langs ll ON ln.Id = ll.lang_id
    WHERE ll.id = ?
    AND ln.Id NOT IN (SELECT I_Id FROM languages_macrolanguages);";

    if ($stmt = $link->prepare($query)) {
        $stmt->bind_param("i", $logged_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }
    ?>
    
    <!--<table>-->
    <div class='titulos'>I want to learn:</div>
    <table border='1'>
    <tr><th>Language Code</th><th>Action</th></tr>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Id']) . "</td>";
       // echo "<td>" . htmlspecialchars($row['Inverted_name']) . "</td>";
        echo "<td><button onclick=\"deleteLanguage('" . $row['Id'] . "', $logged_user_id, 'learn_langs')\">Delete</button></td>";
        echo "</tr>";
    }
    ?>
    </table>    

    <script>
        console.log(userId);

        function deleteLanguage(langId, userId, tabla) {
            fetch('api.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        lang_id: langId,
                        user_id: userId,
                        tabla: tabla // Aquí se incluye el valor de la tabla
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        var popup = $("<div></div>")
                            .text("Language successfully deleted") 
                            .css({
                                "position": "fixed",
                                "top": "50%",  
                                "left": "50%", 
                                "transform": "translate(-50%, -50%)", 
                                "background-color": "#e77667",
                                "color": "white",
                                "padding": "15px 20px",
                                "border-radius": "5px",
                                "box-shadow": "0px 0px 10px rgba(0, 0, 0, 0.2)",
                                "font-size": "16px",
                                "z-index": "1000",
                                "text-align": "center"
                            })
                            .hide(); 
                        
                        $("body").append(popup); 
                        popup.fadeIn(300).delay(1000).fadeOut(300, function() { 
                            location.reload(); // Recargar la página después de que desaparezca el pop-up
                        });                        
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require("../templates/footer.php"); ?>
</body>
</html>