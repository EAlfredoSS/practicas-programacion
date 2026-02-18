<?	

	require('../basedatos.php');
	$query="SELECT * FROM mentor2009 WHERE Codigoborrar='$c' "; //seleccionamos todos los campos
	$result=mysql_query($query,$link);
	if(!mysql_num_rows($result))
			mail('staff@lingua2.eu','error renombrarimagen.php','renombrarimagen.php');
	$fila=mysql_fetch_array($result); 
	$id_user=$fila['orden']; 
	
	$extension=strtolower(end(explode('.',$n1)));

	rename("$n1","./upload_pic/$id_user.$extension");
	rename("$n2","./upload_pic/thumb_$id_user.$extension");
	
	header("location: ../mensajes.php?c=$c");  
?>