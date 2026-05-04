<?php
/***********************************************************
*
* Simple Private Messaging Tutorial Class
*
***********************************************************/
//require_once('../files/bd.php');  

/*if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }*/

/* verificar la conexión 
if (mysqli_connect_errno()) {
    printf("Conexión fallida: %s\n", mysqli_connect_error());
    exit();
}*/ 
/*ini_set('display_errors', 'On');
error_reporting(E_ALL);
 mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);*/



class cpm {
    var $userid = '';			//the id of current user ('orden')
    var $useremail = '';		//the email of the user
    var $messages = array();	//the messages (this can be updated with calling $this->getmessages() with different params)
    var $receivers = array();	//the possible receivers of a PM
    var $dateformat = '';		//the format of the date
	
	//this is the password to encrypr/decrypt messages to/from db. Once set it cannot be changed, as messages won't be decoded right.
	var $secretPass = 'kljhflk73#OO#*U$O(*YO'; 	
       
    // Constructor gets initiated with userid
    function cpm($user,$date="d.m.Y - H:i") {
        // defining the given userid to the classuserid
        $this->userid = $user; 
		//printf("usuario es : %s\n", $user); 
        // Define that date_format
        $this->dateformat = $date;
		//printf("fecha es  : %s\n", $date); 
		//we initiate with new messages
		//$this->getmessages(0); 
		//we get the email
	$this->useremail = $this->getemail($this->userid);
		//printf("email es   : %s\n", $useremail); 
		//we get the possible receivers;
	$this->getMessageReceivers();
    }
    
    // Fetch all messages from this user
	//$type = 0 : New messages
	//$type = 1 : Read messages
	//$type = 2 : Sent messages
	//$type = 3 : Deleted messages
	//$type = 4	: All messages
    function getmessages($type=0) {
        // Specify what type of messages you want to fetch
        require('../files/bd.php');
        switch($type) {
            /*New */	case "0": $sql = "SELECT * FROM messages WHERE `to` = '".$this->userid."' && `to_viewed` = '0' && `to_deleted` = '0' ORDER BY `created` DESC"; break; // New messages
            /*Read */	case "1": $sql = "SELECT * FROM messages WHERE `to` = '".$this->userid."' && `to_viewed` = '1' && `to_deleted` = '0' ORDER BY `to_vdate` DESC"; break; // Read messages
            /*Sent */	case "2": $sql = "SELECT * FROM messages WHERE `from` = '".$this->userid."' ORDER BY `created` DESC"; break; // Send messages
            /*Deleted */case "3": $sql = "SELECT * FROM messages WHERE `to` = '".$this->userid."' && `to_deleted` = '1' ORDER BY `to_ddate` DESC"; break; // Deleted messages
			/*All */	case "4": $sql = "SELECT * FROM messages WHERE `to` = '".$this->userid."' OR `from` = '".$this->userid."' ORDER BY `created` DESC"; break; // All messages
            /*New */	default: $sql = "SELECT * FROM messages WHERE `to` = '".$this->userid."' && `to_viewed` = '0' ORDER BY `created` DESC"; break; // New messages
        }
	//echo $sql;
	//printf("sql  : %s\n", $sql);	
	/*if ($link){
                printf(" Link funciona   : %s\n", $link);
                }else{
                 printf(" Link ERROR   : %s\n", $link);       
                }*/
        //$result = mysql_query($sql) or die (mysql_error());
		$result=mysqli_query($link, $sql); //die($result);
    
    // Check if there are any results
	   
	//printf(" row count   : %s\n", $row_cnt);
		
		if(mysqli_num_rows($result)) {
            $i=0;
			// reset the array
            $this->messages = array();
            // if yes, fetch them!
            while($row = mysqli_fetch_assoc($result)) {
                $this->messages[$i]['id'] = $row['id'];
                $this->messages[$i]['title'] = $row['title'];
                $this->messages[$i]['message'] = $this->decode($row['message'],$this->secretPass);
                $this->messages[$i]['fromid'] = $row['from'];
                $this->messages[$i]['toid'] = $row['to'];
                $this->messages[$i]['from'] = $this->getusername($row['from']);
                $this->messages[$i]['to'] = $this->getusername($row['to']);
                $this->messages[$i]['from_viewed'] = $row['from_viewed'];
                $this->messages[$i]['to_viewed'] = $row['to_viewed'];
                $this->messages[$i]['from_deleted'] = $row['from_deleted'];
                $this->messages[$i]['to_deleted'] = $row['to_deleted'];
				
				//problemas con php8, por eso ponemos if
				
				if( !is_null($row['from_vdate']) )
					$this->messages[$i]['from_vdate'] = date($this->dateformat, strtotime($row['from_vdate']));
				
				
				//problemas con php8, por eso ponemos if
				
				if( !is_null($row['to_vdate']) )				
					$this->messages[$i]['to_vdate'] = date($this->dateformat, strtotime($row['to_vdate']));
				
				
				
					//problemas con php8, por eso ponemos if
				
				if( !is_null($row['from_ddate']) )						
					$this->messages[$i]['from_ddate'] = date($this->dateformat, strtotime($row['from_ddate']));
			
					//problemas con php8, por eso ponemos if
				
				if( !is_null($row['to_ddate']) )					
					$this->messages[$i]['to_ddate'] = date($this->dateformat, strtotime($row['to_ddate']));
				
				
				
                $this->messages[$i]['created'] = date($this->dateformat, strtotime($row['created']));
                $i++;
            }
        } else {
			// If not return false
            return false;
			
        }
    }
    
	//returns all possible PM receivers based on the messages from table couples2009antiguos
	//also... I won't be able to send to users that blocked me
	function getMessageReceivers()
	{	
		require('../files/bd.php');
		$sql1 = "orden not in (select blocker from blocked_users where blocked = ".$this->userid.")";
        
		
		
		//ESTA QUERY TARDA MUCHO!!
		
		// ESTA ES CON BORRADOS //$sql = "SELECT distinct (orden), nombre FROM mentor2009 WHERE ".$sql1." and Email in (select id_1 from couples2009antiguos where id_2='".$this->useremail."' union select id_2 from couples2009antiguos where id_1='".$this->useremail."') ORDER BY nombre ASC;";
        
		// ESTA ES SIN BORRADOS // $sql = "SELECT distinct (orden), nombre FROM mentor2009 WHERE Email in (select id_1 from couples2009antiguos where id_2='".$this->useremail."' union select id_2 from couples2009antiguos where id_1='".$this->useremail."') ORDER BY nombre ASC;";
        
		
		//aquí no quitamos a los bloqueados
		$sql="SELECT distinct (m.orden), m.nombre FROM mentor2009 m INNER JOIN couples2009antiguos c ON m.Email = c.id_1 WHERE c.id_2='".$this->useremail."' UNION SELECT distinct (m.orden), m.nombre FROM mentor2009 m INNER JOIN couples2009antiguos c ON m.Email = c.id_2 WHERE c.id_1='".$this->useremail."'";
		 
		 
		 
		 
		//$sql = "SELECT distinct (orden), nombre FROM mentor2009 WHERE 1";
        
		// echo "$sql";
		
		//$result = @mysqli_query($sql);
		$result = @mysqli_query($link, $sql);
        // Check if there is someone with this id
        if(@mysqli_num_rows($result)) {
			$i=0;
            // reset the array
            $this->receivers = array();
            // if yes, fetch them!
            while($row = mysqli_fetch_assoc($result)) {
                $this->receivers[$i]['id'] = $row['orden'];
                $this->receivers[$i]['username'] = $row['nombre'];
                $i++;
            }
        } else {
            // If not return false
            return false;
        }
	}
	
    // Fetch the username from a userid, I made this function because I don't know how you did build your usersystem, that's why I also didn't use left join... this way you can easily edit it
    /*function getusername($userid) {
        $sql = "SELECT nombre FROM mentor2009 WHERE `orden` = '".$userid."' LIMIT 1";
        $result = mysqli_query($link, $sql);
        // Check if there is someone with this id
        if(mysqli_num_rows($result)) {
            // if yes get his username
            $row = mysqli_fetch_row($result);
            return $row[0];
        } else {
            // if not, name him Unknown
            return "Unknown";
        }
		
	}*/

	//getusername TEST1
	function getusername($userid) {
    require('../files/bd.php');
    
	if ($link) {
        $sql = "SELECT nombre FROM mentor2009 WHERE `orden` = '".$userid."' LIMIT 1";
		
	
        if ($result = mysqli_query($link, $sql)) {
            // Check if there is someone with this id
            if(mysqli_num_rows($result)) {
                // if yes get his username
                $row = mysqli_fetch_row($result);
                return $row[0];
            } else {
                // if not, name him Unknown
                $output="Unknown, getusername(userid)";
            }
        } else {
            #Esto es sólo para depuración. No conviene mostrar errores internos de la BD
            #Cambia luego mysqli_error($link) por un error personalizado si quieres
            $output= "Error en la consulta getusername(userid): {mysqli_error($link)}";
        }
    }else{
        $output= "No hay conexion, getusername(userid)";
    }
    return $output;
	}
    
	
	// Fetch the email from a userid
    /*function getemail($userid) {
        $sql = "SELECT Email FROM mentor2009 WHERE `orden` = '".$userid."' LIMIT 1";
		
		echo "<br><br>$sql<br><br>";
		
        $result = mysqli_query($link, $sql);
        // Check if there is someone with this id
		
        if(mysqli_num_rows($result)) {
            // if yes get his username
            $row = mysqli_fetch_row($result);
            return $row[0];
        } else {
            // if not, name him Unknown
			
			echo "<br><br>aqui hay un error porque no extraemos el email. ver codigo.<br><br>";
            return "Unknown";
        }

		
		
	}*/
	//Prueba de GetEmail TEST1
	function getemail($userid) {
	require('../files/bd.php');	
    if($link) {
        $sql = "SELECT Email FROM mentor2009 WHERE `orden` = '".$userid."' LIMIT 1";    
		
	
        if ($result = mysqli_query($link, $sql)) {
            // Check if there is someone with this id
            if(mysqli_num_rows($result)) {
                // if yes get his username
                $row = mysqli_fetch_row($result);
                return $row[0];
            } else {
                // if not, name him Unknown
                $output="Unknown, getemail(userid)";
            }
        } else {
            #Esto es sólo para depuración. No conviene mostrar errores internos de la BD
            #Cambia luego mysqli_error($link) por un error personalizado si quieres
            $output= "Error en la consulta getemail(userid): {mysqli_error($link)}";
        }
    }else{
        $output= "No hay conexion,getemail(userid)";
    }
    return $output;
	}

	

    
    
    // Fetch a specific message
    function getmessage($message) {
		require('../files/bd.php');
        $sql = "SELECT * FROM messages WHERE `id` = '".$message."' && (`from` = '".$this->userid."' || `to` = '".$this->userid."') LIMIT 1";
        //$result = mysql_query($sql);
		$result=mysqli_query($link,$sql);
        if(mysqli_num_rows($result)) {
            // reset the array
            $this->messages = array();
            // fetch the data
            $row = mysqli_fetch_assoc($result);
            $this->messages[0]['id'] = $row['id'];
            $this->messages[0]['title'] = $row['title'];
            $this->messages[0]['message'] = $this->decode($row['message'],$this->secretPass);
            $this->messages[0]['fromid'] = $row['from'];
            $this->messages[0]['toid'] = $row['to'];
            $this->messages[0]['from'] = $this->getusername($row['from']);
            $this->messages[0]['to'] = $this->getusername($row['to']);
            $this->messages[0]['from_viewed'] = $row['from_viewed'];
            $this->messages[0]['to_viewed'] = $row['to_viewed'];
            $this->messages[0]['from_deleted'] = $row['from_deleted'];
            $this->messages[0]['to_deleted'] = $row['to_deleted'];
            $this->messages[0]['from_vdate'] = date($this->dateformat, strtotime($row['from_vdate']));
            $this->messages[0]['to_vdate'] = date($this->dateformat, strtotime($row['to_vdate']));
            $this->messages[0]['from_ddate'] = date($this->dateformat, strtotime($row['from_ddate']));
            $this->messages[0]['to_ddate'] = date($this->dateformat, strtotime($row['to_ddate']));
            $this->messages[0]['created'] = date($this->dateformat, strtotime($row['created']));
        } else {
            return false;
        }
    }
    
    // We need the userid for pms, but we only let users input usernames, so we need to get the userid of the username :)
    /*function getuserid($username) {
        $sql = "SELECT orden FROM mentor2009 WHERE `nombre` = '".$username."' LIMIT 1";
        $result = mysqli_query($link, $sql);
        if(mysqli_num_rows($result)) {
            $row = mysqli_fetch_row($result);
            return $row[0];
        } else {
            return false;
        }
    }*/


    //getuserid TEST1

    function getuserid($username) {
		require('../files/bd.php');
    if ($link) {
        $sql = "SELECT orden FROM mentor2009 WHERE `nombre` = '".$username."' LIMIT 1";
		
	
        if ($result = mysqli_query($link, $sql)) {
            // Check if there is someone with this id
            if(mysqli_num_rows($result)) {
                // if yes get his username
                $row = mysqli_fetch_row($result);
                return $row[0];
            } else {
                // if not, name him Unknown
                $output="Unknown, getuserid(username)";
            }
        } else {
            #Esto es sólo para depuración. No conviene mostrar errores internos de la BD
            #Cambia luego mysqli_error($link) por un error personalizado si quieres
            $output= "Error en la consulta getuserid(username): {mysqli_error($link)}";
        }
    }else{
        $output= "No hay conexion, getiserid(username)";
    }
    return $output;
	}
    
	// We need the userid of a specific email
    function getuseridbyemail($useremail) {
		require('../files/bd.php');
        $sql = "SELECT orden FROM mentor2009 WHERE `Email` = '".$useremail."' LIMIT 1";
        //$result = mysql_query($sql);
		$result=mysqli_query($link,$sql);
        if(mysqli_num_rows($result)) {
            $row = mysqli_fetch_row($result);
            return $row[0];
        } else {
            return false;
        }
    }
	
    // Flag a message as viewed
    function viewed($message) {
		require('../files/bd.php');
		//I can flag a message as viewed ONLY IF I am the beneficiary 
		$sql = "SELECT * FROM messages WHERE `id` = '".$message."' and `to` = '".$this->userid."' LIMIT 1";
        //$result = mysql_query($sql);
		$result=mysqli_query($link,$sql);
        if(mysqli_num_rows($result)) {
			$sql = "UPDATE messages SET `to_viewed` = '1', `to_vdate` = NOW() WHERE `id` = '".$message."' LIMIT 1";
			//return (@mysql_query($sql)) ? true:false;
			return (@mysqli_query($link,$sql)) ? true:false;
		}
		return false;
    }
    
    // Flag a message as deleted
    function deleted($message) {
		require('../files/bd.php');
		//I can flag a message as deleted ONLY IF I am the beneficiary 
		$sql = "SELECT * FROM messages WHERE `id` = '".$message."' and `to` = '".$this->userid."' LIMIT 1";
        //$result = mysql_query($sql);
		$result = mysqli_query($link,$sql);
        if(mysqli_num_rows($result)) {
			$sql = "UPDATE messages SET `to_deleted` = '1', `to_ddate` = NOW() WHERE `id` = '".$message."' LIMIT 1";
			//return (@mysql_query($sql)) ? true:false;
			return (@mysqli_query($link,$sql)) ? true:false;
		}
		return false;
    }
    
    // Add a new personal message
    function sendmessage($to,$title,$message) {
		require('../files/bd.php');
	
	
		///le quitamos los emails para que no salgan de lingua2
		
		function quitaremails($string)
		{
			$pattern = "/[^@\s]*@[^@\s]*\.[^@\s]*/"; 
			$replacement = " ";
			$output_string= preg_replace($pattern, $replacement, $string);

			$pattern = "/[^@\s]*(at)[^@\s]*\.[^@\s]*/";
			$replacement = " ";
			$output_string=  preg_replace($pattern, $replacement, $output_string);

			$pattern = "/yahoo/";
			$replacement = " ";
			$output_string=  preg_replace($pattern, $replacement, $output_string);

			$pattern = "/ymail/";
			$replacement = " ";
			$output_string=  preg_replace($pattern, $replacement, $output_string);

			$pattern = "/hotmail/";
			$replacement = " ";
			$output_string=  preg_replace($pattern, $replacement, $output_string);

			$pattern = "/gmail/";
			$replacement = " ";
			return preg_replace($pattern, $replacement, $output_string);
		}

		$message= quitaremails($message);
		
		//$message=htmlentities($message, ENT_QUOTES, "UTF-8");  //decimos que lo que escribe el usu es utf-8 
	

        //$to = $this->getuserid($to);
		
		
		//si está bloqueado por el receptor no le dejamos enviar
		$sql_comprobar_bloqueado="SELECT * FROM blocked_users WHERE blocked=".$this->userid." AND blocker=$to";
		$result_bloqueado=mysqli_query($link,$sql_comprobar_bloqueado);
		$n_bloqueados=mysqli_num_rows($result_bloqueado);
		
		if($n_bloqueados){ die ("You cannot send messages to this user, since you have been blocked."); }
		
		
		$message = $this->encode($message,$this->secretPass);
        $sql = "INSERT INTO messages SET `to` = '".$to."', `from` = '".$this->userid."', `title` = '".$title."', `message` = '".$message."', `created` = NOW()";
        //return (@mysql_query($sql)) ? true:false;
		return (@mysqli_query($link,$sql)) ? true:false;
    }
    
    // Render the text (in here you can easily add bbcode for example)
    function render($message) {
        $message = strip_tags($message, '');
        $message = stripslashes($message); 
        $message = nl2br($message);
        return $message;
    }
	
	//returns the user's photo from database (no user set, will get the curretn user's one)
	//no foto is uploaded... will return 'default.jpg'
	function getuserfoto($userid = null)
	{
		require('../files/bd.php');
		if(! isset($userid))
			$userid = $this->userid;
		
		$ext = null;
		
		$sql = "SELECT fotoext FROM mentor2009 WHERE `orden` = '".$userid."' LIMIT 1";
       // $result = mysql_query($sql);
	   $result = mysqli_query($link,$sql);
        if(mysqli_num_rows($result)) {
            $row = mysqli_fetch_row($result);
            $ext = $row[0];
        }
		
		$ret = 'default.jpg';
		
		if ( isset($ext) && ($ext != '') )	//we have extension
			$ret = 'thumb_'.$userid.'.'.$ext;
		
		return $ret;
	}
	
	function listdisplaymessage($msg)
	{
		?>
				<li id="pm_<?php echo $msg['id']?>" class="s_clear ">
					<a href="<?php echo $_SERVER['PHP_SELF']?>?p=view&mid=<?php echo $msg['id']?>" class="avatar">
						<?php $sender_foto = $this->getuserfoto($msg['fromid'])?>
						<?php $receiver_foto = $this->getuserfoto($msg['toid'])?>
						<?php if(file_exists("../uploader/upload_pic/".$sender_foto)):?>
							<img style="border-radius:50px" src="../uploader/upload_pic/<?php echo $sender_foto?>" />
						<?php else:?>
							<img style="border-radius:50px" src="../uploader/default.jpg" />
						<?php endif?>
						<?php if($msg['fromid'] != $this->userid && !$msg['to_viewed'] ){
							?><span style="-webkit-border-radius: 100px;-moz-border-radius: 100px;-ms-border-radius: 100px;-o-border-radius: 100px;border-radius: 100px;background-color: #e44d3a;position: absolute;top: -3px;right: 0;width: 6px;height: 6px;" class="msg-status"></span>
						<?php
						}
						?>
					</a>
					
					<p class="cite">
						<?php  echo ($msg['fromid'] != $this->userid)?'(in)':'(out)'?>
						<cite style="color: #000000;font-size: 18px;font-weight: 600;">
							<?php echo ($msg['fromid'] != $this->userid)?($this->getusername($msg['fromid'])):($this->getusername($msg['toid']))?>
						</cite>
		
					</p>
					<div class="summary" style="color: #686868;font-size: 16px;">
						
						
						<?php /* if($msg['toid']==$this->userid):?>
							<form name='blockuser' method='post' action='<?php echo $_SERVER['PHP_SELF']?>'>
								<input type='hidden' name='uid' value='<?php echo $msg['fromid']?>' />
								<input type='submit' name='blockuser' value='<?php echo ($this->isblocked($msg['fromid']))?'UnBlock User':'Block User'?>' />
							</form>
						<?php  endif   */   ?>
						
						
						<?php 
						//muestro título si no está en blanco el título
						if(!empty($msg['title']))
						{
							echo substr((($msg['toid'] == $this->userid) && ($msg['to_viewed'] == 0))?('<b>'.$msg['title'].'</b>'):$msg['title'],0,20)."...";
							
						}
						else
						{
							if($msg['to_viewed'] == 0) { echo "<b>";}
							echo "<i>[No title]</i>";
							if($msg['to_viewed'] == 0) { echo "</b>";}
						}
						
						?>
						
						</br>
						<i>
						<?php 
						
						if(!empty($msg['message']))
						{
							echo substr($msg['message'],0,20)."..." ;  
						}
					?>
					</i> 
						
						<a style="color: #E65F00; font-weight: bold; " href="<?php echo $_SERVER['PHP_SELF']?>?p=view&mid=<?php echo $msg['id']?>" class="to">more</a>
			
						
					</div>
					<p style="position:absolute;right:10px;top:10px;">
					<?php echo $msg['created'];
						//la hora se muestra en GMT +00
						//echo " GMT";
						?>
					<?php if($msg['fromid'] == $this->userid):	//I am the sender... I am interested in the message status?>
							<cite><br>
							<?php
								// If a message is deleted and not viewed
								if($msg['to_deleted'] && !$msg['to_viewed']) {
									echo "Deleted without reading";
								// if a message got deleted AND viewed
								} elseif($msg['to_deleted'] && $msg['to_viewed']) {
									echo "Deleted after reading";
								// if a message got not deleted but viewed
								} elseif(!$msg['to_deleted'] && $msg['to_viewed']) {
									echo "<font style=\"color:green;font-weight: 300;\">Read</font>";
								} else {
								// not viewed and not deleted
									echo "<font style=\"color:#1E90FF;font-weight: 300;\">Not read yet</font>";
								}
							?>
							</cite>
						<?php endif?>
					</p>
					<p class="more">
						<?php /* <a style="color: #E65F00; font-weight: bold; " href="<?php echo $_SERVER['PHP_SELF']?>?p=view&mid=<?php echo $msg['id']?>" class="to">See full message</a> */ ?>
					</p>
						<span class="action">
							<?php /* if( ($msg['toid']==$this->userid) && ($msg['to_deleted'] == 0) ) : //I didn't already delete this message?>
								<form name='delete' method='post' action='<?php echo $_SERVER['PHP_SELF']?>'>
									<input type='hidden' name='did' value='<?php echo $msg['id']?>' />
									<input type='submit' name='delete' value='Delete' />
								</form>
							<?php endif  */   ?>
						</span>
				</li>
		<?php
	}
	
	function singledisplaymessage($msg)
	{
		?>
			<ul style="margin-bottom:10px" class="pm_list">
				<li id="pm_<?php echo $msg['id']?>" class="s_clear ">
					<a href="<?php echo $_SERVER['PHP_SELF']?>?p=view&mid=<?php echo $msg['id']?>" class="avatar">
						<?php $sender_foto = $this->getuserfoto($msg['fromid'])?>
						<?php $receiver_foto = $this->getuserfoto($msg['toid'])?>
						
						<?php $foto_url="../uploader/upload_pic/$sender_foto";   
						
						//echo $foto_url;
						?>
						
						<?php if( file_exists($foto_url) ):?>
							<img style="border-radius:50px" src="../uploader/upload_pic/<?php echo $sender_foto?>" />
						<?php else:?>
							<img style="border-radius:50px" src="../uploader/default.jpg" />
						<?php endif?>
						
						
						<?php if($msg['toid']==$this->userid):?>
						
							<form name='blockuser' method='post' action='<?php echo $_SERVER['PHP_SELF']?>'>
								<input type='hidden' name='uid' value='<?php echo $msg['fromid']?>' />
								<input type='submit' style="cursor: pointer;background-color: white;border: none;color: #e65f00;margin-top: 5px;font-size: 12px;font-weight: bold;"  name='blockuser' value='<?php echo ($this->isblocked($msg['fromid']))?'UnBlock':'Block'?>' />
							</form>
							
							
							
						<?php endif?>
					</a>
					<p class="cite">
						<?php echo ($msg['fromid'] != $this->userid)?'from':'to'?>
						<cite>
							<?php echo ($msg['fromid'] != $this->userid)?($this->getusername($msg['fromid'])):($this->getusername($msg['toid']))?>
						</cite>
						<?php echo $msg['created'];
						//indicamos que estamos en GMT +00
						echo " GMT";
						?>
						<?php if($msg['fromid'] == $this->userid):	//I am the sender... I am interested in the message status?>
							<cite>
							<?php
								// If a message is deleted and not viewed
								if($msg['to_deleted'] && !$msg['to_viewed']) {
									echo "Deleted without reading";
								// if a message got deleted AND viewed
								} elseif($msg['to_deleted'] && $msg['to_viewed']) {
									echo "Deleted after reading";
								// if a message got not deleted but viewed
								} elseif(!$msg['to_deleted'] && $msg['to_viewed']) {
									echo "<font style=\"color:green;font-weight: 300;\">Read  &#10004;</font>";
								} else {
								// not viewed and not deleted
									echo "<font style=\"color:#1E90FF;font-weight: 300;\">Not read yet</font>";
								}
							?>
							</cite>
						<?php endif?>
					</p>
					<div class="summary">
						<?php if($msg['toid']==$this->userid):?>
							
							
						<!--	<form name='blockuser' method='post' action='<?php echo $_SERVER['PHP_SELF']?>'>
								<input type='hidden' name='uid' value='<?php echo $msg['fromid']?>' />
								<input type='submit' name='blockuser' value='<?php echo ($this->isblocked($msg['fromid']))?'UnBlock User':'Block User'?>' />
							</form>
						-->
							
						<?php endif?>
						<?php echo $msg['title']?>
					</div>
					<p class="more">
						<?php echo $this->render($msg['message'])?>
					</p>
						<span class="action">
							<?php if( ($msg['toid']==$this->userid) && ($msg['to_deleted'] == 0) ) : //I didn't already delete this message?>
							

							<!--	<form name='delete' method='post' action='<?php echo $_SERVER['PHP_SELF']?>'>
									<input type='hidden' name='did' value='<?php echo $msg['id']?>' />
									<input type='submit' name='delete' value='Delete' />
																
								</form>  -->
								
								
							<?php endif?>
						</span>
				</li>
			</ul>
		<?php
	}
	
	//returnes the unread messages of specified userid
	function getunreadmessages($userid = null)
	{
		require('../files/bd.php');
		//if no user is specified, we use current user
		$userid = (isset($userid))?$userid:$this->userid;
		
		$sql = "SELECT count(*) FROM messages WHERE `to` = '".$userid."' && `to_viewed` = '0' && `to_deleted` = '0' ORDER BY `created` DESC";
		//$result = mysql_query($sql);
		$result = mysqli_query($link,$sql);
		$row = mysqli_fetch_row($result);
		return $row[0];
	}
	
	//encodes messages to save them to db
	function encode($string,$key) {
		$key = sha1($key);
		$strLen = strlen($string);
		$keyLen = strlen($key);
		$j=0;
		$hash='';
		for ($i = 0; $i < $strLen; $i++) {
			$ordStr = ord(substr($string,$i,1));
			if ($j == $keyLen) { $j = 0; }
			$ordKey = ord(substr($key,$j,1));
			$j++;
			$hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
		}
		return $hash;
	}

	//decodes messages saved to db
	function decode($string,$key) {
		$key = sha1($key);
		$strLen = strlen($string);
		$keyLen = strlen($key);
		$j=0;
		$hash='';
		for ($i = 0; $i < $strLen; $i+=2) {
			$ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
			if ($j == $keyLen) { $j = 0; }
			$ordKey = ord(substr($key,$j,1));
			$j++;
			$hash .= chr($ordStr - $ordKey);
		}
		return $hash;
	}
	
	//function to check if a user is blocked by current user
	function isblocked($userid)
	{
		require('../files/bd.php');
		$sql = "SELECT count(*) FROM blocked_users WHERE `blocker` = ".$this->userid." && `blocked` = ".$userid;
		
		//$result = mysql_query($sql);
		$result = mysqli_query($link,$sql);
		$row = mysqli_fetch_row($result);
		return ($row[0] > 0)?true:false;
	}
	
	//function to block a user by current user
	function blockuser($userid)
	{
		require('../files/bd.php');
		if(! $this->isblocked($userid))
		{
			$sql = "INSERT INTO blocked_users SET `blocker` = ".$this->userid.", `blocked` = ".$userid.", `created_at` = NOW()";
		
			//return (@mysql_query($sql)) ? true:false;
			return (@mysqli_query($link,$sql)) ? true:false;
		}
		return true;
	}
	
	//function to unblock a user by current user
	function unblockuser($userid)
	{
		require('../files/bd.php');
		if( $this->isblocked($userid))
		{
			$sql = "DELETE FROM blocked_users WHERE `blocker` = ".$this->userid." and `blocked` = ".$userid;
		
			//return (@mysql_query($sql)) ? true:false;
			return (@mysqli_query($link,$sql)) ? true:false;
		}
		return true;
	}

}

?>