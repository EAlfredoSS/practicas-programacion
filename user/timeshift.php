<?php

require('../files/bd.php');
session_start();

$identificador2017=$_SESSION['orden2017'];

if(empty($identificador2017))
{
	die("forbidden...$identificador2017");
}

// Obtener todos los husos horarios disponibles
$timezones = DateTimeZone::listIdentifiers();

		//aqui extraemos el nombre del usuario
		
		$query77="SELECT nombre FROM mentor2009 WHERE orden='$identificador2017' ";
		$result77=mysqli_query($link,$query77);
		if(!mysqli_num_rows($result77))
				die("User unregistered 1.");
		$fila77=mysqli_fetch_array($result77);

		$nombreusu77=$fila77['nombre'];
		
		//die($nombreusu77);






$huso_horario=$_POST['timezone-list'];








function esHusoHorarioValido($timezone) {
    // Obtener todos los husos horarios válidos
    $timezonesValidos = DateTimeZone::listIdentifiers();

    // Verificar si el huso horario está en la lista de válidos
    return in_array($timezone, $timezonesValidos);
}





if( isset($huso_horario) )
{
	
	//hay que comprobar que el huso horario que nos quieren introducir existe realmente
	if (esHusoHorarioValido($huso_horario)) 
	{
    
			$query="UPDATE mentor2009 SET timeshift='$huso_horario' WHERE orden='$identificador2017'"; //ponemos Emparejado a un valor por debajo del 0
			//die($query);
			
			$result=mysqli_query($link,$query); 
			
			header('Location: ./me.php');
			die();
	} 
	else 
	{
		exit(0);
	}
	

}
	




?>


<!DOCTYPE html>
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

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
<title>Lingua2 Timeshift</title>




<!--
    <style>
        #search {
            width: 300px;
            padding: 10px;
            font-size: 16px;
        }
        #timezone-list {
            margin-top: 10px;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            width: 320px;
        }
        #timezone-list option {
            padding: 8px;
            cursor: pointer;
        }
        #timezone-list option:hover {
            background-color: #f0f0f0;
        }
    </style>

-->





</head>
<body>









<?php require("../templates/header_simplified.html"); ?>
<main>

<div class="main-section">
    <div class="container">
        <div class="main-section-data">
            <div class="row">							
				<div class="col-lg-3 col-md-4 pd-left-none no-pd"></div>
					<div class="col-lg-6 col-md-7 no-pd" >
						<div	class="main-ws-sec" >
                            <div class="top-profiles ">
                                <div class="pf-hd">
                                    <h3><?php echo "$nombreusu77"; ?>: update your timeshift</p>
                                    </h3>
                                </div>
									<div class="post-bar" style="margin: 10px 10px 10px 10px; width: 96%">
                                        <div class="post_topbar" >
										
										
										
                                            <div class="usy-dt" >
											

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="post">





<input type="text" id="search" placeholder="Search for your timeshift...">
 
<br><br>

 <select id="timezone-list" name="timezone-list" size="10">
        <?php foreach ($timezones as $timezone): ?>
            <option value="<?php echo htmlspecialchars($timezone); ?>">
                <?php echo htmlspecialchars($timezone); ?>
            </option>
        <?php endforeach; ?>
    </select>











<br><br>
												
												
	<input type="submit" value="Update Timeshift" name="Update timeshift" style="background-color: #e65f00;  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  border-radius: 10px;
  " />
													

												
													
	
	
	</form>
	
	
												</br></br>
												
	</div>	
	
	
	
	
	</div>	</div>	</div>	</div>	</div>	</div>	</div>	</div>	</div>								
	



</main>	


    <script>
        // Función para filtrar los husos horarios
        function filterTimezones() {
            const input = document.getElementById('search').value.toUpperCase();
            const options = document.getElementById('timezone-list').options;

            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                if (option.text.toUpperCase().indexOf(input) > -1) {
                    option.style.display = ''; 
                } else {
                    option.style.display = 'none';
                }
            }
        }

        // Escuchar el evento de entrada en el campo de búsqueda
        document.getElementById('search').addEventListener('input', filterTimezones);

</script>






</body>
</html>
