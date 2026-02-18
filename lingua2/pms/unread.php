<?php
//esta funciona la importa personalarea.php con un require

//te da el numero de mensajes que no has leido
require_once('../files/bd.php');
require('../cpm.class.php');
session_start();
$ur_userid=$_SESSION['userid1'];

//require('../pms/cpm.class.php');
$ur_pm = new cpm($ur_userid);
$ur_new_msg = $ur_pm->getunreadmessages();
if($ur_new_msg!=0)
	echo "<a href=\"http://www.lingua2.eu/pms/pm.php\" target=\"_blank\" style=\"color: red; font-weight: bold;\">You have $ur_new_msg new private messages</a> &nbsp; 
	<a href=\"http://www.lingua2.eu/pms/pm.php\" target=\"_blank\" style=\"color:#05F\"; font-weight: bold;\">My mailbox</a>";
else
	echo "<a href=\"http://www.lingua2.eu/pms/pm.php\" target=\"_blank\" style=\"color: #05F; font-weight: normal;\">No new messages</a> &nbsp; &nbsp; 
	<a href=\"http://www.lingua2.eu/pms/pm.php\" target=\"_blank\" style=\"color:#05F\"; font-weight: bold;\">My mailbox</a>";
?> 