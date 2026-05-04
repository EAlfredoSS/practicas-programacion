<?php
session_start();
require('../files/bd.php');
require('../templates/header_simplified.html');
require('./listofqueries.php');

// Define time variables


//$mi_identificador = $_SESSION['orden2017'];  //esto lo cogemos del require de listofqueries.php




//$tiempo_corte = time();   // esta puesto ya en require de listofqueries.php




//TEACHER FUTURE LESSONS
$query_teacher_active =$QUERY_TEACHER_FUTURE_LESSONS;	
$result_teacher_active = mysqli_query($link, $query_teacher_active);
$count_teacher_active = mysqli_num_rows($result_teacher_active);

// TEACHER PENDING PAYMENTS
$query_teacher_proposals = $QUERY_TEACHER_PENDING_LESSONS;
$result_teacher_proposals = mysqli_query($link, $query_teacher_proposals);
//$count_teacher_proposals = mysqli_fetch_row($result_teacher_proposals)[0];
$count_teacher_proposals = mysqli_num_rows($result_teacher_proposals);


// TEACHER AWAITING RELEASE PAYMENT VALIDATION	
$query_teacher_releases =$QUERY_TEACHER_PENDING_RELEASE_FUNDS_VALIDATION;	
$result_teacher_releases = mysqli_query($link, $query_teacher_releases);
$count_teacher_releases = mysqli_num_rows($result_teacher_releases);


// STUDENT FUTURE LESSONS	
$query_student_active =	$QUERY_STUDENT_FUTURE_LESSONS;
$result_student_active = mysqli_query($link, $query_student_active);
//$count_student_active = mysqli_fetch_row($result_student_active)[0];
$count_student_active = mysqli_num_rows($result_student_active);

// STUDENT PENDING PAYMENTS
$query_student_payments=$QUERY_STUDENT_PENDING_PAYMENTS;  //esto esta en el require de listofqueries
//die($query_student_payments);
$result_student_payments = mysqli_query($link, $query_student_payments);
$count_student_payments = mysqli_num_rows($result_student_payments);

// STUDENT PAST LESSONS PENDING VALIDATION	
$query_student_releases = $QUERY_STUDENT_PAST_LESSONS_PENDING_VALIDATION;
$result_student_releases = mysqli_query($link, $query_student_releases);
$count_student_releases = mysqli_num_rows($result_student_releases);






/*
// 1. Host Lessons (Active Courses)
// Logic: Teacher is user, proposal accepted, not cancelled, start time in future
$query_teacher_active = "SELECT count(t.id_tracking) FROM tracker t 
    INNER JOIN mentor2009 m ON t.id_user_teacher=m.orden 
    WHERE t.id_user_teacher = '" . $mi_identificador . "' 
    AND t.proposal_accepted_teacher = 2 
    AND t.cancelled = 0 
    AND t.end_time_unix > $tiempo_corte";
*/	


/*
// 2. Received Proposals (Due Soon)
// Logic: Teacher is user, proposal pending (0), not cancelled, start time in future
$query_teacher_proposals = "SELECT count(t.id_tracking) FROM tracker t 
    INNER JOIN mentor2009 m ON t.id_user_teacher=m.orden 
    WHERE t.id_user_teacher = '" . $mi_identificador . "' 
    AND t.proposal_accepted_teacher = 0 
    AND t.cancelled = 0 
    AND t.start_time_unix > $tiempo_corte";
*/	



// 3. Pending Exch. Releases (Active Released / Pending Release)
// Logic: Classes passed, paid, but funds not released yet (or just 'Active Released' could mean ready to be released?)
// Based on received-pendingreleasefunds.php/received-futureclasses.php: 
// "paid=1 AND releasefunds=0 AND start_time_unix <= $tiempo_corte"

/*$query_teacher_releases = "SELECT count(t.id_tracking) FROM tracker t 
    INNER JOIN mentor2009 m ON t.id_user_teacher=m.orden 
    WHERE t.id_user_teacher = '" . $mi_identificador . "'  
    AND t.proposal_accepted_teacher=2 
    AND t.cancelled=0 
    AND t.paid=1 
    AND t.releasefunds=0 
    AND t.end_time_unix <= $tiempo_corte";
*/



// --- STUDENT LOGIC ---

/*
// 1. Host Lessons (Active Courses) - Student Side
// Logic: Student is user, proposal accepted, not cancelled, paid, end time in future
$query_student_active = "SELECT count(t.id_tracking) FROM tracker t 
    INNER JOIN mentor2009 m ON t.id_user_student=m.orden 
    WHERE t.id_user_student = '" . $mi_identificador . "' 
    AND t.proposal_accepted_teacher = 2 
    AND t.cancelled = 0 
    AND t.paid = 1 
    AND t.end_time_unix > $tiempo_corte";
	*/

/*
// 2. Pending Payments (Due Soon)
// Logic: Student is user, accepted, not cancelled, not paid, start time in future
$query_student_payments = "SELECT count(t.id_tracking) FROM tracker t 
    INNER JOIN mentor2009 m ON t.id_user_student=m.orden 
    WHERE t.id_user_student = '" . $mi_identificador . "' 
    AND t.proposal_accepted_teacher = 2 
    AND t.cancelled = 0 
    AND t.paid = 0 
    AND t.start_time_unix >= $tiempo_corte";
$result_student_payments = mysqli_query($link, $query_student_payments);
$count_student_payments = mysqli_fetch_row($result_student_payments)[0];
*/

/*
// 3. Payment Released (All Clear / Payment releases pending)
// Logic: Student is user, accepted, not cancelled, paid, funds NOT released, time passed.
// Logic from sent-futureclasses.php for "Payment releases pending":
// "paid=1 AND releasefunds=0 AND $tiempo_corte > end_time_unix"


$query_student_releases = "SELECT count(t.id_tracking) FROM tracker t 
    INNER JOIN mentor2009 m ON t.id_user_student=m.orden 
    WHERE t.id_user_student = '" . $mi_identificador . "' 
    AND t.proposal_accepted_teacher = 2 
    AND t.cancelled = 0 
    AND t.paid = 1 
    AND t.releasefunds = 0 
    AND t.end_time_unix < $tiempo_corte";
*/


?>

<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .dashboard-container {
        padding: 40px 0;
        max-width: 1200px;
        margin: 0 auto;
    }

    .dashboard-header {
        margin-bottom: 30px;
    }

    .dashboard-header h1 {
        font-weight: 700;
        font-size: 32px;
        margin-bottom: 10px;
        color: #000;
    }

    .dashboard-header p {
        color: #666;
        font-size: 16px;
        margin-bottom: 20px;
    }

    .btn-new-proposal {
        background-color: #e65f00;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s;
    }

    .btn-new-proposal:hover {
        background-color: #cc5500;
        color: white;
        text-decoration: none;
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #000;
        margin-top: 40px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #e65f00;
    }

    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .card-box {
        padding: 25px;
        border-radius: 4px; /* Slightly rounded, simpler look */
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 160px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        cursor: pointer;
        position: relative;
    }

    .card-box:hover {
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Orange Card Style */
    .card-orange {
        background-color: #e65f00;
        color: white;
        border: none;
    }

    .card-orange .card-label {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.9;
        margin-bottom: 10px;
    }

    .card-orange .card-number {
        font-size: 48px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 15px;
    }

    .card-orange .card-footer-text {
        font-size: 14px;
        opacity: 0.9;
    }
    
    .card-orange:hover {
        color: white;
    }

    /* White Card Style */
    .card-white {
        background-color: white;
        color: #333;
        border: 1px solid #eee;
    }

    .card-white .card-label {
        font-size: 14px; /* Slightly larger for white card readability */
        color: #555;
        margin-bottom: 10px;
    }

    .card-white .card-number {
        font-size: 48px;
        font-weight: 700;
        line-height: 1;
        color: #000;
        margin-bottom: 15px;
    }

    .card-white .card-footer-text {
        font-size: 14px;
        color: #888;
    }

    /* Dark Card Style (Payment) */
    .card-dark {
        background-color: #343a40;
        color: white;
        flex-direction: row;
        align-items: center;
        min-height: auto; 
        padding-top: 20px;
        padding-bottom: 20px;
        padding-left: 30px;
        padding-right: 30px;
        margin-top: 15px; /* Added spacing from title */
    }
    
    .card-dark:hover {
        color: white;
    }
	
	
	
	
	    /* Dark Card Style (Payment) */
    .card-green {
        background-color: green;
        color: white;
        flex-direction: row;
        align-items: center;
        min-height: auto; 
        padding-top: 20px;
        padding-bottom: 20px;
        padding-left: 30px;
        padding-right: 30px;
        margin-top: 15px; /* Added spacing from title */
    }
    
    .card-green:hover {
        color: white;
    }
	
	
	

    .payment-icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        font-size: 24px;
    }

    .payment-info-content {
        flex-grow: 1;
    }

    .payment-info-title {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 5px;
    }

    .payment-info-subtitle {
        font-size: 14px;
        opacity: 0.8;
        font-weight: 400;
    }

    .btn-manage {
        background-color: white;
        color: #333;
        padding: 8px 20px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
    }
    
    .btn-manage:hover {
        background-color: #f0f0f0;
        color: #000;
    }

    .history-link {
        float: right;
        color: #e65f00;
        font-size: 14px;
        font-weight: 600;
        margin-top: 5px; /* Reduced from 45px to align with title in new container */
    }
    
    /* Utility to clear float */
    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 20px;
        }
        .section-title {
            margin-top: 30px;
        }
    }
</style>

<div class="wrapper">
    <div class="container dashboard-container">
        
        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <p>Welcome back, we're ready for your next language exchange.</p>
            <a href="../trackerproposals/new-proposal.php" class="btn-new-proposal">+ New Proposal</a>
        </div>

        <div class="section-title">
            <i class="fas fa-graduation-cap"></i> Lessons as Teacher
        </div>
        
        <div class="cards-grid">
		
		
            <a href="received-pendingreleasefunds.php" class="card-box <?php echo ($count_teacher_releases > 0) ? 'card-orange' : 'card-white'; ?>">
                <div>
                    <div class="card-label">Previous lessons pendings student's validation</div>
                    <div class="card-number"><?php echo $count_teacher_releases; ?></div>
                </div>
                <div class="card-footer-text">Lessons awaiting student's validation &rarr;</div>
            </a>		
		
            <a href="received-futureclasses.php" class="card-box <?php echo ($count_teacher_active > 0) ? 'card-orange' : 'card-white'; ?>">
                <div>
                    <div class="card-label">Upcoming lessons</div>
                    <div class="card-number"><?php echo $count_teacher_active; ?></div>
                </div>
                <div class="card-footer-text">Next scheduled lessons &rarr;</div>
            </a>

            <a href="received-pendingproposals.php" class="card-box <?php echo ($count_teacher_proposals > 0) ? 'card-orange' : 'card-white'; ?>">
                <div>
                    <div class="card-label">Proposed lessons</div>
                    <div class="card-number"><?php echo $count_teacher_proposals; ?></div>
                </div>
                <div class="card-footer-text">Lessons proposed to you awaiting your approval &rarr;</div>
            </a>


        </div>


        <div class="section-title">
            <i class="fas fa-book-open"></i> Lessons as Student
        </div>

        <div class="cards-grid">
		
		    <a href="sent-pastclassespaymentnotreleased.php" class="card-box <?php echo ($count_student_releases > 0) ? 'card-orange' : 'card-white'; ?>">
                <div>
                    <div class="card-label">Previous lessons pending validation</div>
                    <div class="card-number"><?php echo $count_student_releases; ?></div>
                </div>
                <div class="card-footer-text">Lessons awaiting your validation &rarr;</div>
            </a>
		
		
		
            <a href="sent-futureclasses.php" class="card-box <?php echo ($count_student_active > 0) ? 'card-orange' : 'card-white'; ?>">
                <div>
                    <div class="card-label">Upcoming lessons</div>
                    <div class="card-number"><?php echo $count_student_active; ?></div>
                </div>
                <div class="card-footer-text">Next scheduled lessons &rarr;</div>
            </a>

            <a href="sent-pendingpayments.php" class="card-box <?php echo ($count_student_payments > 0) ? 'card-orange' : 'card-white'; ?>">
                <div>
                    <div class="card-label">Pending Payments</div>
                    <div class="card-number"><?php echo $count_student_payments; ?></div>
                </div>
                <div class="card-footer-text">Lessons awaiting your payment &rarr;</div>
            </a>


        </div>


        <!-- history  -->

        <div class="clearfix" style="margin-top: 50px;"> <a href="" class="history-link">Balance withdrawal</a>
            <div class="section-title" style="margin-top: 0;"> <i class="fas fa-wallet"></i> Past lessons and Wallet
            </div>
        </div>

	
		
		
		<div class="card-box card-green" style="margin-top: 0;"> <div class="payment-icon-circle">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="payment-info-content">
                <div class="payment-info-title">Past lessons as teacher pending withdrawal</div>
                <div class="payment-info-subtitle">Check the history of past lessons as teacher that you still need to charge.</div>
            </div>
            <a href="./received-infopreviouslessonspendingwithdrawal.php" class="btn-manage">See lessons</a>
        </div>
		<br><br>
		
		<div class="card-box card-dark" style="margin-top: 0;"> <div class="payment-icon-circle">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="payment-info-content">
                <div class="payment-info-title">Past lessons as teacher already charged</div>
                <div class="payment-info-subtitle">Check the history of past lessons as teacher that you already charged.</div>
            </div>
            <a href="./received-infopreviouslessonsalreadywithdrawn.php" class="btn-manage">See lessons</a>
        </div>
		<br><br>
		        <div class="card-box card-dark" style="margin-top: 0;"> <div class="payment-icon-circle">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="payment-info-content">
                <div class="payment-info-title">Past lessons as student</div>
                <div class="payment-info-subtitle">Check the history of past lessons as student.</div>
            </div>
            <a href="sent-infopayments.php" class="btn-manage">See lessons</a>
        </div>
		
		

    </div>
</div>

<?php require('../templates/footer.php'); ?>