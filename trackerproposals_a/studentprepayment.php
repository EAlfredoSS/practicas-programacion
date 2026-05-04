<?php
session_start();
$student_id=$_SESSION['orden2017'];
$id_class=$_GET['trackid'];

require('../templates/header_simplified.html');
require('../files/bd.php');

$query="SELECT * FROM tracker
WHERE id_tracking='$id_class' AND id_user_student='$student_id' AND paid=0 AND cancelled=0 AND proposal_accepted_teacher=2";
$result=mysqli_query($link,$query);
if(mysqli_num_rows($result)!=1) die("Error 4562. Contact webmaster.");

$fila=mysqli_fetch_array($result);
$id_of_class=$fila['id_tracking'];
$creation_timestamp=$fila['created_timestamp'];
$recurrent=$fila['created_from_recurrent'];
$id_student=$fila['id_user_student'];
$id_teacher=$fila['id_user_teacher'];
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
$onlineonsite=$fila['onlineonsite'];
$local_encuentro=$fila['id_local'];

// Nombre del profesor
$query_teacher="SELECT nombre FROM mentor2009 WHERE orden='$id_teacher'";
$result_teacher=mysqli_query($link,$query_teacher);
$teacher_data=mysqli_fetch_array($result_teacher);
$teacher_name=ucfirst(explode(" ",trim($teacher_data['nombre']))[0]);

// Modalidad
$mode_label="Online";
$mode_location="";
if ($onlineonsite==2 && is_numeric($local_encuentro)) {
    $ql="SELECT name_local_google, city_google FROM locales WHERE id_local=".intval($local_encuentro);
    $rl=mysqli_query($link,$ql);
    $dl=mysqli_fetch_array($rl);
    if ($dl) {
        $mode_label="Onsite";
        $mode_location=$dl['name_local_google'].", ".$dl['city_google'];
    }
}

function formatUnix($ts) { return date('d/m/Y H:i', $ts); }

// Encriptación para el formulario de pago
if ($teacher_accepted==2 && $session_paid==0) {
    $key='Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z';
    $encoded_class  = base64_encode(openssl_encrypt($id_of_class, 'AES-256-CBC', $key));
    $encoded_amount = base64_encode(openssl_encrypt($total_price,  'AES-256-CBC', $key));
}

$key='Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z';
$itemid1          ='single_payment';
$itemname1        ="Class (language: $language_to_teach) ($duration_min min)";
$productname1     ="Single class";
$itemdescription1 ="$duration_min min class (language: $language_to_teach)";
$internalcodename1="$encoded_class||||||||||||||$encoded_amount";
$amountprice1     =$total_price;

$itemid1          =base64_encode(openssl_encrypt($itemid1,          'AES-256-CBC',$key));
$itemname1        =base64_encode(openssl_encrypt($itemname1,        'AES-256-CBC',$key));
$productname1     =base64_encode(openssl_encrypt($productname1,     'AES-256-CBC',$key));
$itemdescription1 =base64_encode(openssl_encrypt($itemdescription1, 'AES-256-CBC',$key));
$internalcodename1=base64_encode(openssl_encrypt($internalcodename1,'AES-256-CBC',$key));
$amountprice1     =base64_encode(openssl_encrypt($amountprice1,     'AES-256-CBC',$key));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm & Pay</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-orange: #d35400;
            --accent-orange:  #e67e22;
            --light-orange:   #fdf2e9;
            --text-dark:      #2c3e50;
            --text-grey:      #7f8c8d;
            --bg-grey:        #f4f7f6;
            --border:         #e0e0e0;
            --green:          #27ae60;
        }
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Roboto', sans-serif;
            background: var(--bg-grey);
            margin: 0;
        }

        header, #header, .header, .site-header, .main-header, .navbar, nav,
        body > header, body > div:first-child {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }

        .btn-dashboard {
            border: 2px solid #d35400 !important;
            background: #d35400 !important;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            color: #fdf2e9 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 5px rgba(230,126,34,0.15);
            text-decoration: none;
            margin-right: 190px;
        }
        .btn-dashboard:hover {
            background: #fdf2e9 !important;
            color: #d35400 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(230,126,34,0.3);
        }

        .payment-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 16px 30px;
        }

        .card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.09);
            width: 100%;
            max-width: 620px;
            border-top: 4px solid var(--primary-orange);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #fdf2e9, #fce8d6);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-icon {
            width: 38px; height: 38px;
            background: white;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 8px rgba(211,84,0,0.15);
            flex-shrink: 0;
        }
        .header-icon i { font-size: 16px; color: var(--primary-orange); }
        .header-text h2 { font-size: 17px; font-weight: 700; color: var(--text-dark); }
        .header-text p  { font-size: 11px; color: var(--text-grey); margin-top: 1px; }
        .lesson-id-badge {
            margin-left: auto;
            background: white;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: 600;
            color: var(--text-grey);
        }

        .card-body { padding: 14px 18px; }

        .teacher-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 12px;
        }
        .teacher-avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: var(--light-orange);
            border: 2px solid var(--primary-orange);
            display: flex; align-items: center; justify-content: center;
            color: var(--primary-orange);
            font-size: 13px;
        }
        .teacher-label { font-size: 10px; color: var(--text-grey); }
        .teacher-name  { font-size: 14px; font-weight: 700; color: var(--text-dark); line-height: 1.2; }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 8px;
        }
        .info-cell {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-cell i { color: var(--accent-orange); font-size: 14px; width: 16px; text-align: center; }
        .info-cell .txt small {
            display: block;
            font-size: 9px;
            color: var(--text-grey);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-cell .txt span { font-size: 13px; font-weight: 600; color: var(--text-dark); }

        .location-cell {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        .location-cell i { color: var(--accent-orange); font-size: 14px; width: 16px; text-align: center; }
        .location-cell .txt small { display:block; font-size:9px; color:var(--text-grey); text-transform:uppercase; letter-spacing:0.3px; }
        .location-cell .txt span  { font-size:13px; font-weight:600; color:var(--text-dark); }

        .desc-row {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 10px;
            font-size: 12px;
            color: var(--text-dark);
            line-height: 1.4;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .desc-row i { color: var(--accent-orange); font-size: 13px; margin-top: 1px; flex-shrink: 0; }
        .desc-text { flex: 1; }
        .desc-text small { display:block; font-size:9px; color:var(--text-grey); text-transform:uppercase; letter-spacing:0.3px; margin-bottom:2px; }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--light-orange);
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }
        .total-row .breakdown {
            font-size: 11px;
            color: var(--text-grey);
            margin-top: 2px;
        }
        .total-label { font-size: 13px; font-weight: 700; color: var(--text-dark); }
        .total-amount {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-orange);
            line-height: 1;
        }
        .total-amount small { font-size: 14px; margin-left: 1px; }

        .btn-pay {
            width: 100%;
            background: #d35400;
            border: 2px solid #d35400;
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .btn-pay:hover {
            background: #fdf2e9;
            color: #d35400;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(211,84,0,0.3);
        }
        .btn-pay i { font-size: 14px; transition: transform 0.3s; }
        .btn-pay:hover i { transform: scale(1.1); }

        .card-footer {
            padding: 8px 18px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .back-link {
            font-size: 12px;
            color: var(--text-grey);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--primary-orange); }

        @media (max-width: 600px) {
            .btn-dashboard {
                width: 100%;
                justify-content: center;
                font-size: 13px;
                padding: 10px 16px;
            }
            .payment-wrapper {
                padding: 16px 10px 24px;
            }
            .card {
                border-radius: 10px;
            }
            .card-header {
                padding: 12px 14px;
                gap: 8px;
            }
            .card-body {
                padding: 10px 14px;
            }
            .card-footer {
                padding: 6px 14px 12px;
            }
            .header-icon {
                width: 32px; height: 32px;
            }
            .header-icon i { font-size: 14px; }
            .header-text h2 { font-size: 15px; }
            .info-grid {
                grid-template-columns: 1fr;
                gap: 6px;
            }
            .total-amount {
                font-size: 20px;
            }
            .total-amount small { font-size: 12px; }
            .btn-pay {
                font-size: 13px;
                padding: 10px 14px;
            }
            .lesson-id-badge {
                font-size: 9px;
                padding: 2px 6px;
            }
        }

        @media (max-width: 380px) {
            .card-header {
                flex-wrap: wrap;
            }
            .lesson-id-badge {
                margin-left: 0;
                margin-top: 4px;
            }
            .teacher-row {
                flex-wrap: wrap;
            }
            .total-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .total-amount {
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>

<!-- Botón Dashboard alineado a la derecha -->
<div style="text-align: right; padding: 10px 0;">
    <a href="../trackerproposals/dashboard.php" class="btn-dashboard">
        Return to Dashboard
    </a>
</div>

<div class="payment-wrapper">
    <div class="card">

        <div class="card-header">
            <div class="header-icon"><i class="fas fa-credit-card"></i></div>
            <div class="header-text">
                <h2>Confirm &amp; Pay</h2>
                <p>Review your lesson before proceeding</p>
            </div>
            <span class="lesson-id-badge">#<?php echo $id_of_class; ?></span>
        </div>

        <div class="card-body">

            <div class="teacher-row">
                <div class="teacher-avatar"><i class="fas fa-user"></i></div>
                <div>
                    <div class="teacher-label">Teacher</div>
                    <div class="teacher-name"><?php echo htmlspecialchars($teacher_name); ?></div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-cell">
                    <i class="fas fa-language"></i>
                    <div class="txt"><small>Language</small><span><?php echo htmlspecialchars($language_to_teach); ?></span></div>
                </div>
                <div class="info-cell">
                    <i class="far fa-clock"></i>
                    <div class="txt"><small>Duration</small><span><?php echo $duration_min; ?> min</span></div>
                </div>
                <div class="info-cell">
                    <i class="far fa-calendar-alt"></i>
                    <div class="txt"><small>Start time</small><span><?php echo formatUnix($unixtimestart); ?></span></div>
                </div>
                <div class="info-cell">
                    <i class="fas fa-<?php echo $onlineonsite==1 ? 'wifi' : 'map-marker-alt'; ?>"></i>
                    <div class="txt"><small>Mode</small><span><?php echo $mode_label; ?></span></div>
                </div>
            </div>

            <?php if ($onlineonsite==2 && !empty($mode_location)): ?>
            <div class="location-cell">
                <i class="fas fa-map-pin"></i>
                <div class="txt"><small>Location</small><span><?php echo htmlspecialchars($mode_location); ?></span></div>
            </div>
            <?php endif; ?>

            <?php if (!empty($descriptionofsession)): ?>
            <div class="desc-row">
                <i class="fas fa-pencil-alt"></i>
                <div class="desc-text">
                    <small>Topics to practice</small>
                    <?php echo htmlspecialchars($descriptionofsession); ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="total-row">
                <div>
                    <div class="total-label">Total to pay</div>
                    <div class="breakdown"><?php echo number_format($hourly_price,2); ?>€/h &middot; <?php echo $duration_min; ?> min</div>
                </div>
                <div class="total-amount"><?php echo number_format($total_price,2); ?><small>€</small></div>
            </div>

            <form name="payment_event_promotion" action="../payments/index.php" enctype="multipart/form-data" method="POST">
                <input type="hidden" name="itemid"          value="<?php echo $itemid1; ?>">
                <input type="hidden" name="itemname"        value="<?php echo $itemname1; ?>">
                <input type="hidden" name="productname"     value="<?php echo $productname1; ?>">
                <input type="hidden" name="itemdescription" value="<?php echo $itemdescription1; ?>">
                <input type="hidden" name="internalcodename"value="<?php echo $internalcodename1; ?>">
                <input type="hidden" name="amountprice"     value="<?php echo $amountprice1; ?>">
                <button type="submit" class="btn-pay">
                    <i class="fas fa-lock"></i> Continue to secure payment
                </button>
            </form>

        </div>

        <div class="card-footer">
            <a href="javascript:history.back()" class="back-link">
                <i class="fas fa-arrow-left"></i> Go back
            </a>
        </div>

    </div>
</div>

</body>
</html>