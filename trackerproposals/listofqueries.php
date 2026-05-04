<?php
session_start();
$tiempo_corte=time();
$mi_identificador=$_SESSION['orden2017']; 


// --- TEACHER LOGIC ---


$QUERY_TEACHER_PENDING_RELEASE_FUNDS_VALIDATION = "
    SELECT t.*, m.*
    FROM tracker t
    INNER JOIN mentor2009 m ON t.id_user_teacher=m.orden
    WHERE t.id_user_teacher ='".$mi_identificador."' 
      AND proposal_accepted_teacher=2 
      AND cancelled=0 
      AND paid=1 
      AND releasefunds=0 
      AND end_time_unix<=$tiempo_corte   
    ORDER BY t.start_time_unix ASC";


$QUERY_TEACHER_FUTURE_LESSONS = "
    SELECT t.*, m.*
    FROM tracker t
    INNER JOIN mentor2009 m ON t.id_user_teacher = m.orden
    WHERE t.id_user_teacher = " .$mi_identificador . "
      AND t.proposal_accepted_teacher = 2
      AND t.cancelled = 0
      AND t.end_time_unix > $tiempo_corte
    ORDER BY t.start_time_unix ASC
"; 


$QUERY_TEACHER_PENDING_LESSONS="
    SELECT t.*, m.*
    FROM tracker t
    INNER JOIN mentor2009 m ON t.id_user_teacher=m.orden
    WHERE t.id_user_teacher='".$mi_identificador."' AND proposal_accepted_teacher=0 AND cancelled=0 AND end_time_unix>$tiempo_corte
    ORDER BY t.start_time_unix ASC";
	

	 
	
// --- STUDENT LOGIC ---


$QUERY_STUDENT_PAST_LESSONS_PENDING_VALIDATION = "
    SELECT t.*, m.*
    FROM tracker t
    INNER JOIN mentor2009 m ON t.id_user_student=m.orden
    WHERE t.id_user_student ='".$mi_identificador."'  
      AND t.proposal_accepted_teacher=2 
      AND t.cancelled=0 
      AND t.paid=1 
      AND releasefunds=0 
      AND $tiempo_corte>t.end_time_unix
    ORDER BY t.start_time_unix ASC";



$QUERY_STUDENT_FUTURE_LESSONS  = "
    SELECT t.*, m.*
    FROM tracker t
    INNER JOIN mentor2009 m ON t.id_user_student=m.orden
    WHERE t.id_user_student ='".$mi_identificador."'  
      AND proposal_accepted_teacher=2 
      AND cancelled=0 
      AND paid=1  
      AND end_time_unix>$tiempo_corte
    ORDER BY t.start_time_unix ASC";



$QUERY_STUDENT_PENDING_PAYMENTS = "
    SELECT t.*, m.*
    FROM tracker t
    INNER JOIN mentor2009 m ON t.id_user_student=m.orden
    WHERE t.id_user_student ='".$mi_identificador."'  
      AND t.proposal_accepted_teacher=2 
      AND t.cancelled=0 
      AND t.paid=0 
      AND ".$tiempo_corte."<=t.start_time_unix
    ORDER BY t.start_time_unix ASC";


	

/*
// 1. Host Lessons (Active Courses)
// Logic: Teacher is user, proposal accepted, not cancelled, start time in future
$query_teacher_active = "SELECT count(t.id_tracking) FROM tracker t 
    INNER JOIN mentor2009 m ON t.id_user_teacher=m.orden 
    WHERE t.id_user_teacher = '" . $mi_identificador . "' 
    AND t.proposal_accepted_teacher = 2 
    AND t.cancelled = 0 
    AND t.end_time_unix > $tiempoUnix";
	
*/

/*
$result_teacher_active = mysqli_query($link, $query_teacher_active);
$count_teacher_active = mysqli_fetch_row($result_teacher_active)[0];
*/



/*

// 2. Received Proposals (Due Soon)
// Logic: Teacher is user, proposal pending (0), not cancelled, start time in future
$query_teacher_proposals = "SELECT count(t.id_tracking) FROM tracker t 
    INNER JOIN mentor2009 m ON t.id_user_teacher=m.orden 
    WHERE t.id_user_teacher = '" . $mi_identificador . "' 
    AND t.proposal_accepted_teacher = 0 
    AND t.cancelled = 0 
    AND t.start_time_unix > $tiempoUnix";
	*/
/*
$result_teacher_proposals = mysqli_query($link, $query_teacher_proposals);
$count_teacher_proposals = mysqli_fetch_row($result_teacher_proposals)[0];
*/

/*

// 3. Pending Exch. Releases (Active Released / Pending Release)
// Logic: Classes passed, paid, but funds not released yet (or just 'Active Released' could mean ready to be released?)
// Based on received-pendingreleasefunds.php/received-futureclasses.php: 
// "paid=1 AND releasefunds=0 AND start_time_unix <= $tiempoUnix"
$query_teacher_releases = "SELECT count(t.id_tracking) FROM tracker t 
    INNER JOIN mentor2009 m ON t.id_user_teacher=m.orden 
    WHERE t.id_user_teacher = '" . $mi_identificador . "'  
    AND t.proposal_accepted_teacher=2 
    AND t.cancelled=0 
    AND t.paid=1 
    AND t.releasefunds=0 
    AND t.end_time_unix <= $tiempoUnix";
*/


/*
$result_teacher_releases = mysqli_query($link, $query_teacher_releases);
$count_teacher_releases = mysqli_fetch_row($result_teacher_releases)[0];
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
    AND t.end_time_unix > $tiempoUnix";

$result_student_active = mysqli_query($link, $query_student_active);
$count_student_active = mysqli_fetch_row($result_student_active)[0];
*/



// 2. Pending Payments (Due Soon)
// Logic: Student is user, accepted, not cancelled, not paid, start time in future


/*
$query_student_payments = "SELECT count(t.id_tracking) FROM tracker t 
    INNER JOIN mentor2009 m ON t.id_user_student=m.orden 
    WHERE t.id_user_student = '" . $mi_identificador . "' 
    AND t.proposal_accepted_teacher = 2 
    AND t.cancelled = 0 
    AND t.paid = 0 
    AND t.start_time_unix >= $tiempoUnix";
*/	
/*
$result_student_payments = mysqli_query($link, $query_student_payments);
$count_student_payments = mysqli_fetch_row($result_student_payments)[0];

*/

/*
// 3. Payment Released (All Clear / Payment releases pending)
// Logic: Student is user, accepted, not cancelled, paid, funds NOT released, time passed.
// Logic from sent-futureclasses.php for "Payment releases pending":
// "paid=1 AND releasefunds=0 AND $tiempoUnix > end_time_unix"
$query_student_releases = "SELECT count(t.id_tracking) FROM tracker t 
    INNER JOIN mentor2009 m ON t.id_user_student=m.orden 
    WHERE t.id_user_student = '" . $mi_identificador . "' 
    AND t.proposal_accepted_teacher = 2 
    AND t.cancelled = 0 
    AND t.paid = 1 
    AND t.releasefunds = 0 
    AND t.end_time_unix < $tiempoUnix";

$result_student_releases = mysqli_query($link, $query_student_releases);
$count_student_releases = mysqli_fetch_row($result_student_releases)[0];
*/


?>