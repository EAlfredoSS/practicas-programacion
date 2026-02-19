<?php 
session_start();
$mi_identificador = $_SESSION['orden2017'];

require('../templates/header_simplified.html');
require('../files/bd.php');

// Zona horaria del usuario
$query77 = "SELECT timeshift FROM mentor2009 WHERE orden='$mi_identificador'";
$result77 = mysqli_query($link, $query77);
if (!mysqli_num_rows($result77)) die("User unregistered 1.");
$fila77 = mysqli_fetch_array($result77);
$my_timeshift = $fila77['timeshift'];

// Función para fechas
function fechaFormateada($timestamp, $timezone) {
    $date = new DateTime("@$timestamp");
    $date->setTimezone(new DateTimeZone($timezone));
    return $date->format('Y-m-d H:i:s');
}

// CONSULTA - Payments received
$query22 = "SELECT * FROM tracker WHERE id_user_teacher = '$mi_identificador' AND paid=1 ORDER BY start_time_unix ASC";
$result22 = mysqli_query($link, $query22);
$total_pagos = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment history - Teacher Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
<style>
:root {
    --primary-orange: #d35400;
    --accent-orange: #e67e22;
    --light-orange: #fdf2e9;
    --confirmed-green: #27ae60;
    --confirmed-bg: #eafaf1;
    --waiting-grey: #95a5a6;
    --waiting-bg: #f2f3f4;
    --text-dark: #2c3e50;
    --text-grey: #7f8c8d;
    --bg-grey: #f4f7f6;
}

body {
    font-family: 'Roboto', sans-serif;
    background-color: var(--bg-grey);
    margin: 0;
    padding: 0;
}

/* Stats Card */
.stats-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 20px 25px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-left: 5px solid var(--confirmed-green);
}

.stats-label {
    display: flex;
    align-items: center;
    gap: 10px;
}

.stats-label i {
    font-size: 28px;
    color: var(--confirmed-green);
}

.stats-label h3 {
    color: var(--text-dark);
    font-size: 18px;
    margin: 0;
}

.stats-amount .amount {
    font-size: 32px;
    font-weight: 700;
    color: var(--confirmed-green);
}

/* Proposal Card */
.proposal-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    padding: 20px;
    border-left: 5px solid var(--confirmed-green);
}

/* Left */
.card-left {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-right: 20px;
    min-width: 80px;
}

.payment-icon-circle {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #eafaf1, #d5f0e0);
    border: 3px solid var(--confirmed-green);
    display: flex;
    align-items: center;
    justify-content: center;
}

.payment-icon-circle i {
    font-size: 30px;
    color: var(--confirmed-green);
}

.status-badge {
    margin-top: 10px;
    font-size: 11px;
    font-weight: bold;
    padding: 4px 12px;
    border-radius: 12px;
    text-transform: uppercase;
}

.status-confirmed {
    background-color: var(--confirmed-bg);
    color: var(--confirmed-green);
    border: 1px solid #27ae60;
}

.status-cancelled {
    background-color: var(--waiting-bg);
    color: var(--waiting-grey);
    border: 1px solid #95a5a6;
    text-decoration: line-through;
}

/* Middle */
.card-middle {
    flex: 1;
    padding-right: 15px;
}

.card-title {
    font-size: 18px;
    font-weight: bold;
    color: var(--text-dark);
    margin-bottom: 10px;
}

.meta-row {
    display: flex;
    gap: 20px;
    font-size: 14px;
    color: var(--text-dark);
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.meta-item i { 
    color: var(--accent-orange); 
}

.lang-tag {
    background-color: var(--light-orange);
    color: var(--primary-orange);
    padding: 4px 10px;
    border-radius: 5px;
    font-size: 12px;
    display: inline-block;
    margin-right: 5px;
    margin-bottom: 5px;
    border: 1px solid #f5dcc4;
}

/* Right */
.card-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    min-width: 150px;
}

.date-info {
    font-size: 13px;
    color: var(--text-grey);
    margin-bottom: 10px;
}

.payment-amount {
    font-size: 22px;
    font-weight: 700;
    color: var(--confirmed-green);
    margin-bottom: 12px;
}

.btn {
    border: 2px solid #e67e22;
    background: linear-gradient(135deg, #fdf2e9, #fce8d6);
    padding: 10px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #d35400;
    text-transform: uppercase;
    width: 100%;
    justify-content: center;
    text-decoration: none;
}

.btn:hover {
    background: linear-gradient(135deg, #e67e22, #d35400);
    color: white;
}

/* Class Details */
.class-details {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #eee;
    margin-top: 15px;
    display: none;
    font-size: 13px;
    line-height: 1.8;
}

.class-details h6 {
    color: var(--primary-orange);
    font-size: 15px;
    margin-top: 0;
    margin-bottom: 10px;
}

.class-details strong {
    color: var(--primary-orange);
    min-width: 110px;
    display: inline-block;
}

.class-details hr {
    border: none;
    border-top: 1px solid #ddd;
    margin: 12px 0;
}

/* Empty State */
.empty-state {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 50px 30px;
    text-align: center;
    max-width: 500px;
    margin: 40px auto;
    border-top: 4px solid var(--primary-orange);
}

.empty-state-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #fdf2e9, #fce8d6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-state-icon i {
    font-size: 30px;
    color: var(--primary-orange);
}

.empty-state h3 {
    color: var(--text-dark);
    font-size: 22px;
    margin-bottom: 10px;
}

.empty-state p {
    color: var(--text-grey);
    font-size: 15px;
}

/* Media query */
@media (max-width: 768px) {
    .proposal-card {
        flex-direction: column;
    }
    .card-left, .card-middle, .card-right {
        width: 100%;
        align-items: flex-start;
        margin-right: 0;
        margin-bottom: 12px;
    }
    .card-left {
        flex-direction: row;
        gap: 15px;
    }
    .card-right {
        align-items: flex-start;
    }
    .stats-card {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
</head>

<body>
    <div class="wrapper">
        <section class="forum-page">
            <div class="container">
                <div class="forum-questions-sec" style="width: 100%">
                    <div class="forum-questions">
                        
                        <?php
                        $nuevos22 = mysqli_num_rows($result22);
                        
                        if (!$nuevos22) {
                            echo '
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <h3>No Payments Received</h3>
                                <p>You haven\'t received any payments yet.</p>
                            </div>';
                        } else {
                            // Calcular total
                            mysqli_data_seek($result22, 0);
                            while ($fila = mysqli_fetch_array($result22)) {
                                $total_pagos += $fila['price_session_total'];
                            }
                            mysqli_data_seek($result22, 0);
                            
                            // Stats Card
                            echo '
                            <div class="stats-card">
                                <div class="stats-label">
                                    <i class="fas fa-euro-sign"></i>
                                    <h3>Total Earnings</h3>
                                </div>
                                <div class="stats-amount">
                                    <span class="amount">€' . number_format($total_pagos, 2) . '</span>
                                </div>
                            </div>';
                            
                            // Tarjetas de pago
                            while ($fila = mysqli_fetch_array($result22)) {
                                // Variables que usamos
                                $id_class = $fila['id_tracking'];
                                $id_student = $fila['id_user_student'];
                                $id_teacher = $fila['id_user_teacher'];
                                $fecha_clase = fechaFormateada($fila['start_time_unix'], $my_timeshift);
                                $duracion = $fila['session_lenght_minutes'];
                                $idioma = $fila['language_taught'];
                                $precio_hora = $fila['hourly_rate_original'];
                                $total = $fila['price_session_total'];
                                $descripcion = $fila['description_session'];
                                $pagado = $fila['timestamp_paid'];
                                $cancelado = $fila['cancelled'];
                                $dateofstart_utc0 = $fila['date_start_utc0'];
                                $recurrent = $fila['created_from_recurrent'] ? 'Yes' : 'No';
                                $teacher_accepted = $fila['proposal_accepted_teacher'];
                                
                                // Nombre del estudiante
                                $q = "SELECT nombre FROM mentor2009 WHERE orden='$id_student'";
                                $r = mysqli_query($link, $q);
                                $nombre = 'Student';
                                if (mysqli_num_rows($r)) {
                                    $f = mysqli_fetch_array($r);
                                    $partes = explode(" ", $f['nombre']);
                                    $nombre = ucfirst(strtolower($partes[0]));
                                }
                                
                                $badge_class = $cancelado ? 'status-cancelled' : 'status-confirmed';
                                $badge_text = $cancelado ? 'CANCELLED' : 'PAID';
                                $icono = $cancelado ? 'fa-ban' : 'fa-check-double';
                                $fecha_pago = !empty($pagado) ? date('M d, Y', strtotime($pagado)) : 'Pendiente';
                                $fecha_clase_corta = date('M d, Y', strtotime($dateofstart_utc0));
                        ?>
                        
                        <div class="proposal-card">
                            <!-- LEFT -->
                            <div class="card-left">
                                <div class="payment-icon-circle">
                                    <i class="fas <?php echo $icono; ?>"></i>
                                </div>
                                <div class="status-badge <?php echo $badge_class; ?>">
                                    <?php echo $badge_text; ?>
                                </div>
                            </div>
                            
                            <!-- MIDDLE -->
                            <div class="card-middle">
                                <div class="card-title">
                                    <?php echo "$idioma · $nombre"; ?>
                                </div>
                                
                                <div class="meta-row">
                                    <span class="meta-item"><i class="far fa-clock"></i> <?php echo $duracion; ?> min</span>
                                    <span class="meta-item"><i class="fas fa-tag"></i> €<?php echo $precio_hora; ?>/h</span>
                                    <span class="meta-item"><i class="far fa-calendar-alt"></i> <?php echo $fecha_clase_corta; ?></span>
                                </div>
                                
                                <div>
                                    <span class="lang-tag"><i class="fas fa-user"></i> <?php echo $nombre; ?></span>
                                    <span class="lang-tag"><i class="fas fa-credit-card"></i> <?php echo $fecha_pago; ?></span>
                                    <span class="lang-tag"><i class="fas fa-hashtag"></i> ID: <?php echo $id_class; ?></span>
                                </div>
                                
                                <!-- DETALLES OCULTOS -->
                                <div class="class-details" id="details-<?php echo $id_class; ?>">
                                    <h6><i class="fas fa-info-circle"></i> Detalles del pago</h6>
                                    
                                    <strong>Clase ID:</strong> <?php echo $id_class; ?><br>
                                    <strong>Estudiante:</strong> <?php echo $nombre; ?> (ID: <?php echo $id_student; ?>)<br>
                                    <strong>Profesor ID:</strong> <?php echo $id_teacher; ?><br>
                                    
                                    <hr>
                                    
                                    <strong>Fecha clase:</strong> <?php echo $fecha_clase; ?><br>
                                    <strong>Duración:</strong> <?php echo $duracion; ?> min<br>
                                    
                                    <hr>
                                    
                                    <strong>Idioma:</strong> <?php echo $idioma; ?><br>
                                    <strong>Precio/hora:</strong> €<?php echo $precio_hora; ?><br>
                                    <strong style="color: #27ae60; font-size: 16px;">Total: €<?php echo number_format($total, 2); ?></strong><br>
                                    <strong>Pagado:</strong> <?php echo $pagado ?: 'Pendiente'; ?><br>
                                    
                                    <hr>
                                    
                                    <strong>Descripción:</strong><br><?php echo nl2br($descripcion); ?><br><br>
                                    <small>Creado: <?php echo $fila['created_timestamp']; ?> | Recurrente: <?php echo $recurrent; ?> | Teacher accepted: <?php echo $teacher_accepted; ?></small>
                                </div>
                            </div>
                            
                            <!-- RIGHT -->
                            <div class="card-right">
                                <div class="date-info">
                                    <i class="far fa-calendar-alt"></i> <?php echo $fecha_clase_corta; ?>
                                </div>
                                <div class="payment-amount">
                                    €<?php echo number_format($total, 2); ?>
                                </div>
                                <button class="btn class-name" data-id="<?php echo $id_class; ?>">
                                    <i class="fas fa-receipt"></i> Details
                                </button>
                            </div>
                        </div>
                        
                        <?php
                            }
                        }
                        ?>
                        
                    </div>
                </div>
            </div>
        </section>
        
        <?php require('../templates/footer.php'); ?>
    </div>

    <script src="../public/js/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.class-name').click(function() {
            var id = $(this).data('id');
            $('#details-' + id).slideToggle(200);
        });
    });
    </script>
</body>
</html>
<?php mysqli_close($link); ?>