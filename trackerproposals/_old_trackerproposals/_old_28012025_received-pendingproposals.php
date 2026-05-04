<?php 
require('../templates/header_simplified.html');
require('../files/bd.php');

$mi_identificador=4588;
$time_shift_teacher='';

//AND cancelled=0
$query="SELECT * FROM tracker WHERE id_user_teacher ='" . $mi_identificador . "'  AND proposal_accepted_teacher=0 AND cancelled=0  ORDER BY start_time_unix ASC";
//echo " $query ";
$result = mysqli_query($link, $query);
$nuevos=mysqli_num_rows($result);

 if (!$nuevos)
    die(" No sessions for this user yet");
?>
<body>
    <div class="wrapper">
        <section class="forum-sec">
            <div class="container">
				<div class="forum-links">
    				<ul>
        				<li><a href="./received-futureclasses.php" title="">Next lessons as teacher</a></li>
        				<li class="active"><a href="" title="">Received proposals as teacher</a></li>
						<li><a href="#" title="">Past lessons as teacher</a></li>
					</ul>
                </div>
            </div>
        </section>
<?php
for($i=0;$i<$nuevos;$i++)
{
	$fila=mysqli_fetch_array($result);
	$id_of_class=$fila['id_tracking'];	
	$creation_timestamp=$fila['created_timestamp'];
	$recurrent=$fila['created_from_recurrent'];
	$id_student=$fila['id_user_student'];
	$time_shift_student=$fila['time_shift_student'];
	$dateofstart_utc0=$fila['date_start_utc0'];
	$dateofend_utc0=$fila['date_end_utc0'];
	$unixtimestart=$fila['start_time_unix'];
	$unixtimeend=$fila['end_time_unix'];
	$duration_min=$fila['session_lenght_minutes'];
	$language_to_teach=$fila['language_taught'];
	$hourly_price=$fila['hourly_rate_original'];
	$total_price=$fila['price_session_total'];
	$descriptionofsession=$fila['description_session'];
	$teacher_accepted=$fila['proposal_accepted_teacher'];
	$teacher_accepted_timestamp=$fila['proposal_accepted_timestamp'];
	$session_paid=$fila['paid'];
	$session_paid_timestamp=$fila['timestamp_paid'];
	$cancelled=$fila['cancelled'];
	$fee_percentage=$fila['price_fee_percentage'];
	$amount_received_by_teacher=$total_price*(100-$fee_percentage)/100;
	$style_1='';
	/* if($cancelled==1){  $style_1="style=\"text-decoration:line-through;\" ";  }   
	echo "<ul   $style_1   >";
	echo "<li>ID: $id_of_class</li>";
	echo "<li>Student id: $id_student</li>";
	echo "<li>Student timeshift: $time_shift_student</li>";
	echo "<li>Start Date UTC-0: $dateofstart_utc0</li>";
	echo "<li>End Date UTC-0: $dateofend_utc0</li>";
	echo "<li>Start Unix Time: $unixtimestart</li>";
	echo "<li>End Unix Time: $unixtimeend</li>";	
	echo "<li>Duration (min): $duration_min</li>";
	echo "<li>Language to teach: $language_to_teach</li>";
	echo "<li>Price per hour: $hourly_price</li>";	
	echo "<li>Total session price paid by the student: $total_price</li>";
	echo "<li>Percentage fee: $fee_percentage%</li>";
	echo "<li>Total session amount received by the teacher: $amount_received_by_teacher</li>";
	echo "<li>Description of session: $descriptionofsession</li>";
	echo "<li>Teacher accepted?: $teacher_accepted</li>";
	echo "<li>Session has been paid?: $session_paid</li>";	
	echo "<li>Session has been cancelled?: $cancelled</li>";
	echo "<li>Info created from recurrently: $recurrent</li>";
	echo "<li>Info Teacher accepted timestamp: $teacher_accepted_timestamp</li>";
	echo "<li>Info Session payment timestamp: $session_paid_timestamp</li>";
	if($teacher_accepted==0) //when the session has not been accepted or declined yet
	{
	?>
	<li><a href="./teacheracceptdecline.php?trackid=<?php echo "$id_of_class"; ?>&action=2">Accept</a></li>
	<li><a href="./teacheracceptdecline.php?trackid=<?php echo "$id_of_class"; ?>&action=1">Decline</a></li>
	<?php
	}
	else
	{
		if($session_paid==0)
		{
			echo "<li style=\"color:red;\">Awaiting payment from student</li>";
		}
		else if($session_paid==1)
		{
			echo "<li style=\"color:green;\">Paid</li>";
		}
		if( $session_paid==0 AND $cancelled==0)
		{
		?>
			<li><a href="./teachercancel.php?trackid=<?php echo "$id_of_class"; ?>">Cancel session</a></li>
	<?php
		}
	}
	echo "</ul>";*/
	// Empieza el HTML para mostrar la clase
    ?>
    <div class="usr-question">
        <div class="usr_img">
            <?php
                echo "<div class=\"usr-img\"><img src=\"$path_photo\" alt=\"Student Image\"></div>";
            ?>
        </div>
        <div class="usr_quest">
            <h3 class="class-name" data-id="class<?php echo $id_of_class; ?>"><?php echo $language_to_teach?></h3>
            <h4><?php echo $descriptionofsession; ?></h4>
            <h6><i class="far fa-hourglass"></i> <?php echo $duration_min . ' min'; ?> &nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-coins"></i> <?php echo $total_price . '€'; ?></h6>

            <?php
            if($teacher_accepted==0){ //when the session has not been accepted or declined yet
            ?>
                <ul class="quest-tags">
				    <li><a class="btn-aceptar" data-url="./teacheracceptdecline.php?trackid=<?php echo "$id_of_class"; ?>&action=2" >Accept</a></li>
				    <li><a class="btn-denegar" style="background-color: #e77667;" data-url="./teacheracceptdecline.php?trackid=<?php echo "$id_of_class"; ?>&action=1">Decline</a></li>
                    <!-- Elemento de notificación -->
                    <div id="notification" class="notification">Your action was successful!</div>
			    </ul>
            <?php
            }
            else
            {
                if($session_paid==0)
                {
                    echo "<li style=\"color:red;\">Awaiting payment from student</li>";
                }
                else if($session_paid==1)
                {
                    echo "<li style=\"color:green;\">Paid</li>";
                }
                if( $session_paid==0 AND $cancelled==0)
                {
                ?>
                    <li><a href="./teachercancel.php?trackid=<?php echo "$id_of_class"; ?>">Cancel session</a></li>
            <?php
                }
            }
            ?>

        </div>
        <span class="quest-posted-time"><i class="fa fa-clock-o" style="font-size: 15px;margin-right: -5%;" ></i><?php echo date('Y-m-d H:i:s', $unixtimestart); ?></span>
        
        <!-- Detalles de la clase (ocultos inicialmente) -->
        <div class="class-details" id="details-class<?php echo $id_of_class; ?>" style="display: none;">
            <h4>Detalles de la Clase <?php echo $id_of_class; ?></h4>
            <p><strong>Fecha de inicio:</strong> <?php echo date('Y-m-d H:i:s', $unixtimestart); ?></p>
            <p><strong>Fecha de fin:</strong> <?php echo date('Y-m-d H:i:s', $unixtimeend); ?></p>
            <p><strong>Duración:</strong> <?php echo $duration_min; ?> minutos</p>
            <p><strong>Precio total:</strong> <?php echo $total_price . '€'; ?></p>
            <p><strong>Descripción:</strong> <?php echo $descriptionofsession; ?></p>
        </div>
    </div>
    <?php
}
?>

<style>
/* Style for the navigation links */
.forum-links {
    background-color: #fff;
    padding: 10px 0;
    margin-bottom: 10px;
    width: 100%;
    margin-top: -3.1%;
}


.forum-links ul {
    display: flex;
    padding-left: 400px;
}

.forum-links ul li {
    margin-right: 20px;
}

.forum-links ul li a {
    display: inline-block;
    padding: 10px 20px;
    text-decoration: none;
    color: #999;
    font-weight: normal;
    font-size: 16px;
    transition: color 0.3s ease;
}

.forum-links ul li.active a {
    color: #e65f00;
    font-weight: bold;
    position: relative;
}

.forum-links ul li.active a::after {
    content: "";
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background-color: #e65f00;
}

.usr-question {
    background-color: #fff;
    padding: 20px;
    border-radius: 1px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: row;
    margin-bottom: 1px;
    width: 58%;
    margin-left:21%;
}

.usr-img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    margin-top: 10px;
    margin-right: 15px;
    flex-shrink: 0;
}

.usr-img img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
}

.usr_quest {
    flex: 1;
    width: 75%;
}

.usr_quest h3, .usr_quest h4, .usr_quest h6 {
    margin: 10px 0;
    width:100%;
}

.usr_quest h3 {
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

.usr_quest h4 {
    color: #555;
}

.usr_quest h6 {
    font-size: 14px;
    color: #666;
}

/* Styles for the additional information tooltip */
.tooltip-container {
    position: relative;
    display: inline-block;
}

.tooltip-text {
    font-size: 16px;
    visibility: hidden;
    width: 380px;
    background-color: #000;
    color: #fff;
    text-align: left;
    border-radius: 6px;
    padding: 15px;
    position: absolute;
    z-index: 1;
    top: 30%;
    left: 50%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.3s;
}

.tooltip-container:hover .tooltip-text {
    visibility: visible;
    opacity: 0.75;
}

.fas.fa-info-circle {
    cursor: pointer;
    font-size: 20px;
}

/* Posted time style */
.quest-posted-time {
    font-size: 12px;
    color: #aaa;
    margin-top: 8%;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Styles for the class details section */
.class-details {
    background-color: #f4f4f4;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
    margin-left:20px;
}

.class-details h4 {
    font-size: 16px;
    color: #333;
}

.class-details p {
    font-size: 14px;
    color: #666;
}

/* Estilos para los botones de Aceptar y Declinar */
.quest-tags {
    display: flex;
    gap: 10px; 
    padding: 0;
    list-style-type: none;
}

.quest-tags li {
    display: inline-block;
}

.quest-tags li a {
    display: inline-block;
    padding: 7px 25px;
    color: #fff;
    border-radius: 3px;
}

.quest-tags li a:first-child { /* Verde para Aceptar */
    background-color: #53d690; 
}

.quest-tags li a:nth-child(2) { /* Rojo para Declinar */
    background-color: #e77667; 
}

.quest-tags li a:hover { /
    opacity: 0.8; 
    cursor: pointer;
}

.notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #53d690; /* Verde para indicar éxito */
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    font-size: 16px;
    z-index: 1000;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.4s ease-out, transform 0.4s ease-out; /* Suavizar el efecto de entrada/salida */
}

.notification.show {
    opacity: 1;
    transform: translateY(0);
}

/* Responsive Styles */
@media (max-width: 991px) {
    .usr-question {
        width: 90%;
        margin-left: 5%;
    }

    .usr-img {
        margin-bottom: 10px;
    }

    .usr_quest {
        width: 100%;
    }

    .class-details {
        margin-top: 10px;
        padding: 10px;
    }
    .quest-posted-time {
        font-size: 12px;
        color: #aaa;
        margin-top: 10%;
    }
    .quest-posted-time i {
        margin-left:-130%;
    }
    .forum-links {
        
    } 
}
@media (min-width: 1200px){
	.container {
		max-width: 100%;
		margin: 0;
		padding: 0;
	}
}
</style>
</div>
<?php
	require('../templates/footer.php');
?>
<script>
        document.addEventListener('DOMContentLoaded', function () {
            var classNames = document.querySelectorAll('.class-name');
			var acceptBtn = document.getElementById('acceptBtn<?php echo $id_of_class; ?>');
    		var declineBtn = document.getElementById('declineBtn<?php echo $id_of_class; ?>');

            classNames.forEach(function (className) {
                // Añadimos un evento de clic a cada uno
                className.addEventListener('click', function () {
                    // Obtenemos el id del contenedor de detalles correspondiente
                    var classId = className.getAttribute('data-id');
                    var classDetails = document.getElementById('details-' + classId);

                    // Alternamos la visibilidad del detalle de la clase específica
                    if (classDetails.style.display === 'none' || classDetails.style.display === '') {
                        classDetails.style.display = 'block';
                    } else {
                        classDetails.style.display = 'none';
                    }
                });
            });

            // Cuando se hace clic fuera de una clase, se ocultan todos los detalles
            document.addEventListener('click', function (e) {
                if (!e.target.classList.contains('class-name')) {
                    var allDetails = document.querySelectorAll('.class-details');
                    allDetails.forEach(function (detail) {
                        detail.style.display = 'none';
                    });
                }
            });

			// Button to accept and decline
    const btnAceptars = document.querySelectorAll('.btn-aceptar');
    const btnDenegars = document.querySelectorAll('.btn-denegar');
    let notification = document.querySelector('.notification');
    
    // Si no existe, la creamos dinámicamente
    if (!notification) {
        notification = document.createElement('div');
        notification.classList.add('notification');
        document.body.appendChild(notification); // La agregamos al body o el contenedor que prefieras
    }

    // Procesar cada botón de "Aceptar"
    btnAceptars.forEach(function(btnAceptar) {
        btnAceptar.addEventListener('click', function(event) {
            event.preventDefault(); // Prevenir que el enlace realice la acción de navegación
            console.log('Botón Aceptar presionado'); 
            const url = btnAceptar.getAttribute('data-url'); // Obtener la URL del atributo data-url

            fetch(url, { // Realizar la solicitud HTTP sin redirigir (usando fetch)
                method: 'GET',
            })
            .then(response => response.text())
            .then(data => {
                console.log('Respuesta recibida:', data); // Mostrar la respuesta del servidor si es necesario
            })
            .catch(error => {
                console.error('Error en la solicitud:', error); // Manejo de errores
            });

            // Mostrar la notificación
            notification.classList.add('show');
            notification.style.display = 'block';

            // Ocultar la notificación después de 3 segundos
            setTimeout(() => {
                notification.classList.remove('show');
                notification.style.display = 'none';
            }, 3000);

            // Redirigir a la URL
            setTimeout(function() { window.location.href = btnAceptar.href;}, 100);
        });
    });

    // Procesar cada botón de "Denegar"
    btnDenegars.forEach(function(btnDenegar) {
        btnDenegar.addEventListener('click', function(event) {
            event.preventDefault(); // Prevenir que el enlace realice la acción de navegación
            console.log('Botón Denegar presionado');
            const url = btnDenegar.getAttribute('data-url'); // Obtener la URL desde el atributo data-url

            fetch(url, { // Realizar la solicitud HTTP sin redirigir (usando fetch)
                method: 'GET',
            })
            .then(response => response.text())
            .then(data => {
                console.log('Respuesta recibida:', data);
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
            });

            // Mostrar la notificación (puedes personalizar el mensaje)
            notification.classList.add('show');
            notification.style.display = 'block';
            notification.innerText = 'You have declined the session!'; // Mensaje personalizado para Denegar

            // Ocultar la notificación después de 3 segundos
            setTimeout(() => {
                notification.classList.remove('show');
                notification.style.display = 'none';
            }, 3000);

            // Redirigir a la URL
            setTimeout(function() { window.location.href = btnDenegar.href;}, 100);
        });
    });
		})
    </script>
</body>
</html>