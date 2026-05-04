<?php
#####################################
# Include PHP Mailer Class
#####################################
require("class.phpmailer.php");
?>
<?php
#####################################
# Function to send email
#####################################
function sendEmail ($fromName, $fromEmail, $toEmail, $subject, $emailBody) {
	$mail = new PHPMailer();
	$mail->FromName = $fromName;
	$mail->From = $fromEmail;
	$mail->AddAddress("$toEmail");
		
	$mail->Subject = $subject;
	$mail->Body = $emailBody;
	$mail->isHTML(true);
	$mail->WordWrap = 150;
		
	if(!$mail->Send()) {
		return false;
	} else {
		return true;
	}
}

#####################################
# Function to Read a file 
# and store all data into a variable
#####################################
function readTemplateFile($FileName) {
		$fp = fopen($FileName,"r") or exit("Unable to open File ".$FileName);
		$str = "";
		while(!feof($fp)) {
			$str .= fread($fp,1024);
		}	
		return $str;
}
?>
<?php
#####################################
# Finally send email
#####################################

	//Data to be sent (Ideally fetched from Database)
	$NameOfUser = "XYZ";
	$Username = "abcdef";
	$password = "123456";
	//$UserEmail = "a.amitoj@gmail.com";
	$UserEmail = "friasaitor-tfc@yahoo.es,a.amitoj@gmail.com";
	//Send email to user containing username and password
	//Read Template File 
	$emailBody = readTemplateFile("template.html");
			
	//Replace all the variables in template file
	$emailBody = str_replace("#name#",$NameOfUser,$emailBody);
	$emailBody = str_replace("#username#",$Username,$emailBody);
	$emailBody = str_replace("#password#",$password,$emailBody);
			
	//Send email
	$emailStatus = sendEmail ("Lingua2", "friasaitor-tfc@yahoo.es", $UserEmail, "Email Subject", $emailBody);
	
	//If email function return false
	if ($emailStatus != 1) {
		echo "An error occured while sending email. Please try again later.";
	} else {
		echo "Email with account details were sent successfully.";
	}
?>