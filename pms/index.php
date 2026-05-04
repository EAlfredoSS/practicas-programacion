<?php

    /*************************************************************************
    *
    * Simple Private Messaging Tutorial for Pixel2Life Community
    * 
    * Features:
    * 
    * - Messaging using Usernames 
    * - No HTML allowed (bbcode can simply be included) 
    * - You can see if somebody has deleted or read the pm 
    * - On reply, the old mail will be quoted
    *
    * by Christian Weber
    * edited by danvbe@gmail.com
    * 
    *************************************************************************/
    session_start();
    // Load the config file!
    // include('../testlingua/intercambio-de-idiomas/bd.php');
	
	
	// Set the userid to 1623 for testing purposes... you should have your own usersystem, so this should contain the userid
    
	//$userid=$_SESSION['userid1'];//1625;
	
	
	
	$userid=$_SESSION['orden2017'];
	

//echo "$userid";

	
    require('../files/bd.php');
    
    // Load the class
    require('cpm.class.php');
	//load the captcha
	require_once('securimage/securimage.php');
	
    
	
	

	
	
	
	
	//die($userid);
    
    /* //2017
    $userid=$_SESSION['orden2017'];
    $_SESSION['userid1']=$_SESSION['orden2017'];
    
    */
    
    // initiate a new pm class
    $pm = new cpm($userid);
	
	
	
	
	
	
	//print_r($pm);
	
	
	
	
	
	//die("usuario _          $userid");
	
	
	//printf("usuario es   : %s\n", $user); 
    //printf("email es   : %s\n", $useremail); 
    // check if a new message had been send
    if(isset($_POST['newmessage'])) {
		//for captcha validation
		$securimage = new Securimage();
        
		if ($securimage->check($_POST['captcha_code']) == false) {
			// the code was incorrect
			// you should handle the error so that the form processor doesn't continue
			// or you can use the following code if there is no validation or you do not know how
			$_SESSION["message"]["message"] = "You copied the security letters wrong.";
			$_SESSION["message"]["type"] = "error";
		}
		else {
			// check if there is an error while sending the message (beware, the input hasn't been checked, you should never trust users input!)
			if($pm->sendmessage($_POST['to'],$_POST['subject'],$_POST['message'])) {
				// Tell the user it was successful
				$_SESSION["message"]["message"] = "Message successfully sent!";
				$_SESSION["message"]["type"] = "ok";
				
				////////////////////////////////////////////////////////////////////////////////////////////
				//we send the email here
				require('../emailtemplates/email.php');

				$emailBody = readTemplateFile("../emailtemplates/template_privatemessage.html"); 

				//Replace all the variables in template file
				$nameofsender = $pm->getusername($pm->userid);
				$nameofreceiver = $pm->getusername($_POST['to']);
				//we get the foto of sender
				$sender_foto = $pm->getuserfoto($pm->userid);
				if($sender_foto != 'default.jpg')
					$sender_foto = "../uploader/upload_pic/".$sender_foto;
				else
					$sender_foto = "../uploader/default.jpg";
				
				$mensaje_recortado=substr($_POST['message'],0,20);
				$mensaje_recortado.='... <br/>[Read more entering in your Personal Area and Mailbox]';
				$emailBody = str_replace("#receptor#",$nameofreceiver,$emailBody);
				$emailBody = str_replace("#emisor#",$nameofsender,$emailBody);
				$emailBody = str_replace("#mensajeemisor#",$mensaje_recortado,$emailBody);
				$emailBody = str_replace("#photoname#",$sender_foto,$emailBody);

				//Send email 
				$to_email = $pm->getemail($_POST['to']);
				$emailStatus = sendEmail ("Lingua2 Private Message", "notifications@languageexchanges.com", $to_email, "You received a new message from $nameofsender", $emailBody);

				//If email function return false 
				if ($emailStatus != 1) {
					$_SESSION["message"]["message"] = "An error occured while sending email. Please try again later.";
					$_SESSION["message"]["type"] = "error";
				} 	
				///////////////////////////////////////////////////////////////////////////////////////////////
				
			} else {
				// Tell user something went wrong it the return was false
				$_SESSION["message"]["message"] = "Error, couldn't send PM. Maybe wrong user.";
				$_SESSION["message"]["type"] = "error";
			}
		}
    }
    
    // check if a message had been deleted
    if(isset($_POST['delete'])) {
        // check if there is an error during deletion of the message
        if($pm->deleted($_POST['did'])) {
            $_SESSION["message"]["message"] = "Message successfully deleted!";
			$_SESSION["message"]["type"] = "ok";
        } else {
            $_SESSION["message"]["message"] =  "Error, couldn't delete PM!";
			$_SESSION["message"]["type"] = "error";
        }
    }
	
	// check if a user needs to be blocked/unblocked
    if(isset($_POST['blockuser'])) {
        if($pm->isblocked($_POST['uid'])) {
			$res = $pm->unblockuser($_POST['uid']);
			$op = 'unblock';
		}
		else {
			$res = $pm->blockuser($_POST['uid']);
			$op = 'block';
		}
		
		if($res) {
			$_SESSION["message"]["message"] = "User was sucessfully ".$op."ed!";
			$_SESSION["message"]["type"] = "ok";
        } else {
            $_SESSION["message"]["message"] =  "Error, couldn't ".$op." user!";
			$_SESSION["message"]["type"] = "error";
        }
    }
    
?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml"> -->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">


<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-139626327-1');
        </script>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!-- <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />-->
<link rel="stylesheet" type="text/css" href="pm2.css">
<title>Lingua2 Private Messaging</title>



</head>
<body>

<?php require_once("../templates/header_simplified.html"); ?>

<!--
<center>

<a href="../user/me.php" style="font-size: 12px;font-weight: bold; color: #e65f00">
<img src="../images/logo_orange-150px.png" alt="Logo Lingua2" width="100"> </br>
Back to home page
</a>

</center>
-->

<div class="main-section">
    <div class="container">
        <div class="main-section-data">
            <div class="row" style="justify-content:center">
			<div class="col-lg-8 col-md-7 no-pd">
                    <div class="main-ws-sec">



<div id="wrap" class="wrap with_side s_clear">
    <div class="main">
        <div class="content">
			<?php if (isset($_SESSION['message'])):?>
				<div class="message-box <?php echo $_SESSION['message']['type']?>">
					<?php echo $_SESSION['message']['message']?>
				</div>
				<?php $_SESSION['message'] = null ?>
			<?php endif?>


<?php
// In this switch we check what page has to be loaded, this way we just load the messages we want using numbers from 0 to 3 (0 is standart, so we don't need to type this)
if(isset($_GET['p'])) {
    switch($_GET['p']) {
        // get all new / unread messages
        case 'new': $pm->getmessages(); break;
        // get all send messages
        case 'sent': $pm->getmessages(2); break;
        // get all read messages
        case 'read': $pm->getmessages(1); break;
        // get all deleted messages
        case 'deleted': $pm->getmessages(3); break;
        // get a specific message
        case 'view': $pm->getmessage($_GET['mid']); break;
		// get all new / unread messages
        case 'all': $pm->getmessages(4); break;
        // get all new / unread messages
		case 'send_message': $pm->getmessages(); break;
        // get all new / unread messages
        default: $pm->getmessages(); break;
    }
} else {
    // get all new / unread messages
    //$pm->getmessages();
	
	$pm->getmessages(4);
	
}
// Standard links
$p = isset($_GET['p'])?$_GET['p']:'new';
?>
<div class="pm_header colplural itemtitle s_clear" style="border:none;height: 35px; padding:0px;">
	<ul style="height:100%;width:100%;">
		<li style="height:100%; padding: 5px 5px;" <?php echo ($p=='all')?'class="current"':''?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=all" hidefocus="true"><span>Messages</span></a></li>
		<!--<li <?php echo ($p=='new')?'class="current"':''?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=new" hidefocus="true"><span>Unread</span></a></li> -->
		<!--<li <?php echo ($p=='read')?'class="current"':''?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=read"><span>Read</span></a></li>-->
		<!--<li <?php echo ($p=='sent')?'class="current"':''?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=sent" ><span>Sent</span></a></li>-->
		<!--<li <?php echo ($p=='deleted')?'class="current"':''?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=deleted"><span>Deleted</span></a></li>-->
		<li style="float:right;" <?php echo ($p=='send_message')?'class="current"':''?>><a style="background-color: #e65f00;  border: none; color: white; padding: 5px 5px; text-align: center; border-radius: 10px; height:100%" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=send_message&type=new"><span>Write new message</span></a></li>
		<!--<li> <a href="<?php echo "../user/me.php"; ?>" ><span>Back to my profile</span></a></li>-->
	</ul>
</div>
<hr  />

<?php
// if the user wants a detail view and the message id is set...
if(isset($_GET['p']) && $_GET['p'] == 'view' && isset($_GET['mid'])) {
	$msg = $pm->messages[0];
    // if the users id is the recipients id and the message hadn't been viewed yet
    if($userid == $msg['toid'] && !$msg['to_viewed']) {
        // set the messages flag to viewed
        $pm->viewed($msg['id']);
    }
	$pm->singledisplaymessage($msg);

	
// aqui debajo el link para escribir mensaje
?>



<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=send_message&type=reply&mid=<?php echo $_GET['mid'] ?>" style="font-size: 12px;font-weight: bold; color: #e65f00">Reply to this message</a>





 <!--   <form name='reply' method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
        <input type='hidden' name='rfrom' value='<?php echo $pm->messages[0]['from']; ?>' />
        <input type='hidden' name='rsubject' value='Re: <?php echo $pm->messages[0]['title']; ?>' />
        <input type='hidden' name='rmessage' value='[quote]<?php echo $pm->messages[0]['message']; ?>[/quote]' />
        <input type='submit' name='reply' value='Reply' />
    </form>
	-->
	
<?php
}
elseif(isset($_GET['p']) && $_GET['p'] == 'send_message') {
	//do nothing... will display only the send message form
}
else {	//we have a list of messages
?>
	<ul class="pm_list">
<?php 
		// If there are messages, show them
        if(count($pm->messages)) {
            // message loop
            for($i=0;$i<count($pm->messages);$i++) {
				$msg = $pm->messages[$i];
				$pm->listdisplaymessage($msg);
            }
        } else {
            // else... tell the user that there are no new messages
            echo (isset($_GET['p']))?"<strong>No messages found</strong>":"";
        }
?>
	</ul>
<?php
}
?>
</ul>	<?php //we end the list of messages



/*php8 dice que no encuentra la constante send_message, pero yo creo que es un texto*/
//if($_GET['p']==send_message)  //added by Aitor



if($_GET['p']=='send_message')  //added by Aitor in 2024 for php8
{

	$pm->getmessage($_GET['mid']);
	$msg = $pm->messages[0];
?>



<div class="posts-section">
<div class="posty">
<div class="post-bar no-margin p-3">
<div class="job-description">


<form name="new" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="form-group"></div>
<h1><?php echo isset($_POST['reply'])?"Reply to ":"Send "?>Message</h1><p>You can only send private messages to users that you contacted through their profile before.<br/>In order to find a user profile use our search engine and click on the name of the user.<br/>
If a user who you contacted before did not answer to your request write again. Many times users do not see or forget requests.<br/><br/>
</p>
</div>
		<label for="to">To:</label>
		<?php
		if($_GET['type']=='new')  //added by Aitor
		{
		?>
			<select style="width:100%" class="form-control" name='to'>
			<?php $reply_to = isset($_POST['reply'])?$pm->getuserid($_POST['rfrom']):null?>
			<?php foreach($pm->receivers as $i=>$receiver):?>
				<option value="<?php echo $receiver['id']?>" <?php echo ($reply_to==$receiver['id'])?'selected':''?>><?php echo $receiver['username']?></option>
			<?php endforeach?>
			</select>
			<br>
			<label for="subject">Subject:</label>
			<input type='text' class="form-control" style='width:100%;' name='subject' value='<?php if(isset($_POST['reply'])) { echo $_POST['rsubject']; } ?>' /><br>
		<?php
		}else{
		?>
			<select style="width:100%" class="form-control" name='to'>
			<?php $reply_to = isset($_POST['reply'])?$pm->getuserid($_POST['rfrom']):null?>
			<? if($msg['fromid'] != $userid){ ?>
			<option value="<?php echo $msg['fromid'] ?>"><?php echo $msg['from'] ?></option> 
			<?php } ?>
			<?php foreach($pm->receivers as $i=>$receiver):
				if($receiver['id'] != $msg['fromid']){ ?>
				<option value="<?php echo $receiver['id']?>" <?php echo ($reply_to==$receiver['id'])?'selected':''?>><?php echo $receiver['username']?></option>
				<?php } ?>
			<?php endforeach?>
			</select>
			<br>
			<label for="subject">Subject:</label>
			<input type='text' style="background-color:white;" class="form-control" style='width:100%;' name='subject' value='RE: <?php echo $msg['title'] ?>'><br>
		<?php
		}
		?>
		<label for="message">Message:</label><br  />
		<textarea rows="5" class="form-control" style="width:100%;" name='message'><?php if(isset($_POST['reply'])) { echo $_POST['rmessage']; } ?></textarea>		<br/><br/>
		<img id="captcha" style="margin-bottom:5px; margin-right:10px;" src="securimage/securimage_show.php" alt="CAPTCHA Image" />
 <!--style="padding-left:100px;"-->
			<input type="text" class="form-control" style="width:64%" name="captcha_code" size="10" maxlength="6" />
			<a href="#" onclick="document.getElementById('captcha').src = 'securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a><br/>
			<span style="font-size:90%; color:red;">Be aware that if you copy the code incorrectly your message will not be sent and you will lose your inserted data. Check it twice.</span><br/><br/>

	<tr>
		<td colspan="<?php echo isset($_POST['reply'])?'1':'2'?>" style="text-align:center">
		<button type="submit" name="newmessage" value="Send" style="background-color: #e65f00;width:100%;  border: none;color: white;padding: 10px 11px;text-align: center;border-radius: 5px;">Send</button>
</td>
		<?php if(isset($_POST['reply'])):?>
			<td style="text-align:center"><input type='button' name='cancel' value='Cancel' onclick="history.go(-1)"/></td>
		<?php endif?>
	</tr>
</table>
</form>
		</div>
		</div>
		</div>


<?php ?>



	</div>	<?php //end content?>
	</div>	<?php //end main?>
	</div>	<?php //end wrap?>
	</div>
	</div>
	</div>
	</div>
		</div>
		</div>
</body>
</html>