<?	

	session_start();

	require('../bd.php');
	$query="SELECT * FROM mentor2009 WHERE Codigoborrar='$c' AND foto<5 "; //seleccionamos todos los campos
	$result=mysql_query($query,$link);
	if(!mysql_num_rows($result))
			mail('staff@lingua2.eu','usuario sospechoso de usar rename de fotos','renombrarimagen.php');
	$fila=mysql_fetch_array($result); 
	$id_user=$fila['orden'];
	
	$extension=strtolower(end(explode('.',$n1)));
	

	//si hemos subido un .png y ahora queremos sobreescribirlo pero metemos un .jpg tenemos un problema. Hay que borrar todo lo anterior.
	$foto_nombre=$fila['orden']; 

	
	$jpg_name="./upload_pic/$foto_nombre.jpg";
	$png_name="./upload_pic/$foto_nombre.png";
	$gif_name="./upload_pic/$foto_nombre.gif";
	$bmp_name="./upload_pic/$foto_nombre.bmp";
	
	$jpg_th="./upload_pic/thumb_$foto_nombre.jpg";
	$png_th="./upload_pic/thumb_$foto_nombre.png";
	$gif_th="./upload_pic/thumb_$foto_nombre.gif";
	$bmp_th="./upload_pic/thumb_$foto_nombre.bmp";
	
	
	if (file_exists($jpg_name) ){
		$foto_nombre=$jpg_name;
		$thumb_nombre=$jpg_th;
		echo unlink($foto_nombre); //echo"$foto_nombre";
		echo unlink($thumb_nombre); //echo"$thumb_nombre";
		}
	else if (file_exists($png_name) ){
		$foto_nombre=$png_name;
		$thumb_nombre=$png_th;
		echo unlink($foto_nombre);  //echo"$foto_nombre";
		echo unlink($thumb_nombre); //echo"$thumb_nombre";
		}
	else if(file_exists($gif_name)){
		$foto_nombre=$gif_name;
		$thumb_nombre=$gif_th;
		echo unlink($foto_nombre); //echo"$foto_nombre";
		echo unlink($thumb_nombre); //echo"$thumb_nombre";
		}
	else if(file_exists($bmp_name) ) { 
		$foto_nombre=$bmp_name;
		$thumb_nombre=$bmp_th;
		echo unlink($foto_nombre);  //echo"$foto_nombre";
		echo unlink($thumb_nombre);	 //echo"$thumb_nombre";
		}
		
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	//evitamos que alguien intente renombrar desde url archivos existentes
	//$n1 y $n2 vienen de upload.php con la siguiente pinta respectivamente: upload_pic/resize_1338848398.jpg   y   upload_pic/thumbnail_1338848398.jpg
	
	if(	strpos($n1,'resize')==false  OR	strpos($n2,'thumbnail')==false)	 
		die('Forbidden. Contact webmaster@lingua2.eu informing with your user name, please.');  
	
	rename("$n1","./upload_pic/$id_user.$extension");
	rename("$n2","./upload_pic/thumb_$id_user.$extension");
	
	chmod("./upload_pic/$id_user.$extension",0644);
	chmod("./upload_pic/thumb_$id_user.$extension",0644); 
	
	//sumamos 1 al numero de veces que se sube una foto
	$query22="UPDATE mentor2009 SET foto=foto+1, fotoext='$extension' WHERE Codigoborrar='$c'"; 
	$result22=mysql_query($query22,$link);
	if(!mysql_affected_rows($link))
		  die('Error 10. Contact webmaster@lingua2.eu');   

	
	if($_SESSION['registroconfacebook']=='si' OR !empty($_SESSION['estoyenpersonalarea']))
	{	header("location: http://www.lingua2.eu/facebook/registration/index.php");	 	}
	else
	{	//header("location: ../mensajes.php?c=$c"); 
		header("location: http://www.lingua2.eu/intercambio-de-idiomas/registrationend.php");
    }
?>