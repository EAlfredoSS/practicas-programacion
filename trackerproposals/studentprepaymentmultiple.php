<?php
session_start();
$student_id=$_SESSION['orden2017'];

require('../templates/header_simplified.html');
require('../files/bd.php');

$amount_of_classes_pending_of_payment=0;
$id_of_classes_to_pay=array();
$classes_summary = array(); // para almacenar info de cada clase y mostrarla en la tarjeta

$tiempo_corte=time();

$query=" 
SELECT * 
FROM tracker
WHERE id_user_student='$student_id' AND paid=0 AND cancelled=0 AND proposal_accepted_teacher=2 AND $tiempo_corte<=start_time_unix
";

$result = mysqli_query($link, $query);
$nuevos=mysqli_num_rows($result);

if (!$nuevos)
    die(" Error 4530. Contact webmaster.  ");

for($iii=0; $iii<$nuevos; $iii++) {
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

    // Obtener nombre del profesor
    $id_teacher = $fila['id_user_teacher'];
    $query_teacher = "SELECT nombre FROM mentor2009 WHERE orden='$id_teacher'";
    $result_teacher = mysqli_query($link, $query_teacher);
    $teacher_data = mysqli_fetch_array($result_teacher);
    $teacher_name = ucfirst(explode(" ", trim($teacher_data['nombre']))[0]);

    $amount_of_classes_pending_of_payment += $total_price;
    array_push($id_of_classes_to_pay, "$id_of_class");

    $classes_summary[] = array(
        'id' => $id_of_class,
        'teacher' => $teacher_name,
        'language' => $language_to_teach,
        'duration' => $duration_min,
        'price' => $total_price,
        'hourly' => $hourly_price,
        'date' => date('d/m/Y H:i', $unixtimestart),
        'description' => $descriptionofsession
    );
}

$list_classes_to_encode = implode('|||', $id_of_classes_to_pay);

$longitud_string = strlen($list_classes_to_encode);
if($longitud_string > 500) {
    die('Too many classes to be payed. Error 46210. Contact webmaster.');
}

$key = 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z';
$encoded_classes = base64_encode(openssl_encrypt($list_classes_to_encode, 'AES-256-CBC', $key));
$encoded_total_amount = base64_encode(openssl_encrypt($amount_of_classes_pending_of_payment, 'AES-256-CBC', $key));

$itemid1='multiple_payment';
$itemname1="Multiple classes";
$productname1="Multiple classes";
$itemdescription1="Multiple classes pending of payment";
$internalcodename1="$encoded_classes|||$encoded_total_amount";
$amountprice1=$amount_of_classes_pending_of_payment;

$itemid1 = base64_encode(openssl_encrypt($itemid1, 'AES-256-CBC', $key));
$itemname1 = base64_encode(openssl_encrypt($itemname1, 'AES-256-CBC', $key));
$productname1 = base64_encode(openssl_encrypt($productname1, 'AES-256-CBC', $key));
$itemdescription1 = base64_encode(openssl_encrypt($itemdescription1, 'AES-256-CBC', $key));
$internalcodename1 = base64_encode(openssl_encrypt($internalcodename1, 'AES-256-CBC', $key));
$amountprice1 = base64_encode(openssl_encrypt($amountprice1, 'AES-256-CBC', $key));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm & Pay - Multiple Classes</title>
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
            margin-right: 170px;
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
            max-width: 660px;
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
        .header-text h2 { font-size: 20px; font-weight: 700; color: var(--text-dark); }
        .header-text p  { font-size: 15px; color: var(--text-grey); margin-top: 1px; }

        .card-body { padding: 14px 18px; }

        .class-list {
            list-style: none;
            margin-bottom: 14px;
        }
        .class-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
            color: var(--text-dark);
        }
        .class-item:last-child { border-bottom: none; }
        .class-item .avatar-sm {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: var(--light-orange);
            border: 2px solid var(--primary-orange);
            display: flex; align-items: center; justify-content: center;
            color: var(--primary-orange);
            font-size: 13px;
            flex-shrink: 0;
        }
        .class-item .info {
            flex: 1;
            line-height: 1.4;
        }
        .class-item .info strong {
            display: block;
            font-size: 16px;
            color: var(--text-dark);
        }
        .class-item .info .meta {
            font-size: 11px;
            color: var(--text-grey);
        }
        .class-item .price {
            font-weight: 700;
            font-size: 18px;
            color: var(--text-dark);
            white-space: nowrap;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--light-orange);
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }
        .total-row .total-label {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
        }
        .total-row .total-amount {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-orange);
        }
        .total-row .total-amount small { font-size: 15px; margin-left: 1px; }

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
            text-align: center;
        }
        .back-link {
            font-size: 12px;
            color: var(--text-grey);
            text-decoration: none;
            display: inline-flex;
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
            .total-amount {
                font-size: 20px;
            }
            .total-amount small { font-size: 12px; }
            .btn-pay {
                font-size: 13px;
                padding: 10px 14px;
            }
        }
    </style>
</head>
<body>

<!-- Botón Dashboard alineado a la derecha -->
<div style="text-align: right; padding: 10px 15px;">
    <a href="../trackerproposals/dashboard.php" class="btn-dashboard">
        Return to Dashboard
    </a>
</div>

<div class="payment-wrapper">
    <div class="card">

        <div class="card-header">
            <div class="header-icon"><i class="fas fa-layer-group"></i></div>
            <div class="header-text">
                <h2>Confirm &amp; Pay Multiple Classes</h2>
                <p>Review the list before proceeding</p>
            </div>
        </div>

        <div class="card-body">

            <ul class="class-list">
                <?php foreach ($classes_summary as $cls): ?>
                <li class="class-item">
                    <div class="avatar-sm"><i class="fas fa-user"></i></div>
                    <div class="info">
                        <strong><?php echo htmlspecialchars($cls['teacher']); ?> &mdash; <?php echo htmlspecialchars($cls['language']); ?></strong>
                        <span class="meta">
                            <?php echo $cls['date']; ?> &middot; <?php echo $cls['duration']; ?> min &middot; <?php echo number_format($cls['hourly'],2); ?>€/h
                        </span>
                    </div>
                    <div class="price"><?php echo number_format($cls['price'],2); ?>€</div>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="total-row">
                <span class="total-label">Total to pay</span>
                <span class="total-amount"><?php echo number_format($amount_of_classes_pending_of_payment,2); ?><small>€</small></span>
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