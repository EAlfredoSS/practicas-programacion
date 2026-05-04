<?php
session_start();
require('../files/bd.php');
require('../templates/header_simplified.html');

// Define time variables
$mi_identificador = $_SESSION['orden2017'];
$tiempoUnix = time();

// --- TEACHER LOGIC ---

// 1. Host Lessons (Active Courses)
// Logic: Teacher is user, proposal accepted, not cancelled, start time in future
$query_teacher_active = "SELECT count(*) FROM tracker 
    WHERE id_user_teacher = '$mi_identificador' 
    AND proposal_accepted_teacher = 2 
    AND cancelled = 0 
    AND end_time_unix > '$tiempoUnix'";
$result_teacher_active = mysqli_query($link, $query_teacher_active);
$count_teacher_active = mysqli_fetch_row($result_teacher_active)[0];


// 2. Received Proposals (Due Soon)
// Logic: Teacher is user, proposal pending (0), not cancelled, start time in future
$query_teacher_proposals = "SELECT count(*) FROM tracker 
    WHERE id_user_teacher = '$mi_identificador' 
    AND proposal_accepted_teacher = 0 
    AND cancelled = 0 
    AND start_time_unix > '$tiempoUnix'";
$result_teacher_proposals = mysqli_query($link, $query_teacher_proposals);
$count_teacher_proposals = mysqli_fetch_row($result_teacher_proposals)[0];


// 3. Pending Exch. Releases (Active Released / Pending Release)
// Logic: Classes passed, paid, but funds not released yet (or just 'Active Released' could mean ready to be released?)
// Based on received-pendingreleasefunds.php/received-futureclasses.php: 
// "paid=1 AND releasefunds=0 AND start_time_unix <= $tiempoUnix"
$query_teacher_releases = "SELECT count(*) FROM tracker 
    WHERE id_user_teacher = '$mi_identificador' 
    AND proposal_accepted_teacher = 2 
    AND cancelled = 0 
    AND paid = 1 
    AND releasefunds = 0 
    AND start_time_unix <= '$tiempoUnix'";
$result_teacher_releases = mysqli_query($link, $query_teacher_releases);
$count_teacher_releases = mysqli_fetch_row($result_teacher_releases)[0];


// --- STUDENT LOGIC ---

// 1. Host Lessons (Active Courses) - Student Side
// Logic: Student is user, proposal accepted, not cancelled, paid, end time in future
$query_student_active = "SELECT count(*) FROM tracker 
    WHERE id_user_student = '$mi_identificador' 
    AND proposal_accepted_teacher = 2 
    AND cancelled = 0 
    AND paid = 1 
    AND end_time_unix > '$tiempoUnix'";
$result_student_active = mysqli_query($link, $query_student_active);
$count_student_active = mysqli_fetch_row($result_student_active)[0];


// 2. Pending Payments (Due Soon)
// Logic: Student is user, accepted, not cancelled, not paid, start time in future
$query_student_payments = "SELECT count(*) FROM tracker 
    WHERE id_user_student = '$mi_identificador' 
    AND proposal_accepted_teacher = 2 
    AND cancelled = 0 
    AND paid = 0 
    AND start_time_unix >= '$tiempoUnix'";
$result_student_payments = mysqli_query($link, $query_student_payments);
$count_student_payments = mysqli_fetch_row($result_student_payments)[0];


// 3. Payment Released (All Clear / Payment releases pending)
// Logic: Student is user, accepted, not cancelled, paid, funds NOT released, time passed.
// Logic from sent-futureclasses.php for "Payment releases pending":
// "paid=1 AND releasefunds=0 AND $tiempoUnix > end_time_unix"
$query_student_releases = "SELECT count(*) FROM tracker 
    WHERE id_user_student = '$mi_identificador' 
    AND proposal_accepted_teacher = 2 
    AND cancelled = 0 
    AND paid = 1 
    AND releasefunds = 0 
    AND end_time_unix < '$tiempoUnix'";
$result_student_releases = mysqli_query($link, $query_student_releases);
$count_student_releases = mysqli_fetch_row($result_student_releases)[0];


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
        
        <!-- Header -->
        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <p>Welcome back, we're ready for your next language exchange.</p>
            <a href="../trackerproposals/new-proposal.php" class="btn-new-proposal">+ New Proposal</a>
        </div>

        <!-- Lessons as Teacher -->
        <div class="section-title">
            <i class="fas fa-graduation-cap"></i> Lessons as Teacher
        </div>
        
        <div class="cards-grid">
            <!-- Card 1: Host Lessons -->
            <a href="received-futureclasses.php" class="card-box <?php echo ($count_teacher_active > 0) ? 'card-orange' : 'card-white'; ?>">
                <div>
                    <div class="card-label">HOST LESSONS</div>
                    <div class="card-number"><?php echo $count_teacher_active; ?></div>
                </div>
                <div class="card-footer-text">Active Courses &rarr;</div>
            </a>

            <!-- Card 2: Received Proposals -->
            <a href="received-pendingproposals.php" class="card-box <?php echo ($count_teacher_proposals > 0) ? 'card-orange' : 'card-white'; ?>">
                <div>
                    <div class="card-label">Received Proposals</div>
                    <div class="card-number"><?php echo $count_teacher_proposals; ?></div>
                </div>
                <div class="card-footer-text">Due Soon</div>
            </a>

            <!-- Card 3: Pending Releases -->
            <a href="received-pendingreleasefunds.php" class="card-box <?php echo ($count_teacher_releases > 0) ? 'card-orange' : 'card-white'; ?>">
                <div>
                    <div class="card-label">Pending Exch. Releases</div>
                    <div class="card-number"><?php echo $count_teacher_releases; ?></div>
                </div>
                <div class="card-footer-text">Active Released</div>
            </a>
        </div>


        <!-- Lessons as Student -->
        <div class="section-title">
            <i class="fas fa-book-open"></i> Lessons as Student
        </div>

        <div class="cards-grid">
            <!-- Card 1: Host Lessons (Student) -->
            <a href="sent-futureclasses.php" class="card-box <?php echo ($count_student_active > 0) ? 'card-orange' : 'card-white'; ?>">
                <div>
                    <div class="card-label">HOST LESSONS</div>
                    <div class="card-number"><?php echo $count_student_active; ?></div>
                </div>
                <div class="card-footer-text">Active Courses &rarr;</div>
            </a>

            <!-- Card 2: Pending Payments -->
            <a href="sent-pendingpayments.php" class="card-box <?php echo ($count_student_payments > 0) ? 'card-orange' : 'card-white'; ?>">
                <div>
                    <div class="card-label">Pending Payments</div>
                    <div class="card-number"><?php echo $count_student_payments; ?></div>
                </div>
                <div class="card-footer-text">Due Soon</div>
            </a>

            <!-- Card 3: Payment Released -->
            <!-- Note: Linking to something relevant, maybe pastclassespaymentnotreleased.php or just a general history -->
            <a href="sent-pastclassespaymentnotreleased.php" class="card-box <?php echo ($count_student_releases > 0) ? 'card-orange' : 'card-white'; ?>">
                <div>
                    <div class="card-label">Payment Released</div>
                    <div class="card-number"><?php echo $count_student_releases; ?></div>
                </div>
                <div class="card-footer-text">All Clear</div>
            </a>
        </div>


        <!-- Wallet & Payments -->
        <div class="clearfix" style="margin-top: 50px;"> <!-- Increased top margin to separate from above -->
            <a href="sent-infopayments.php" class="history-link">History</a>
            <div class="section-title" style="margin-top: 0;"> <!-- Removed top margin within container, restored default bottom margin -->
                <i class="fas fa-wallet"></i> Wallet & Payments
            </div>
        </div>

        <div class="card-box card-dark" style="margin-top: 0;"> <!-- Removed top margin to stick to title -->
            <div class="payment-icon-circle">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="payment-info-content">
                <div class="payment-info-title">Payment Information</div>
                <div class="payment-info-subtitle">Manage your payment methods and withdrawals.</div>
            </div>
            <a href="sent-infopayments.php" class="btn-manage">Manage Details</a>
        </div>

    </div>
</div>

<?php require('../templates/footer.php'); ?>
