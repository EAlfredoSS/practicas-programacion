<?php
require('../files/bd.php');

$id_local_num=1;

if($id_local_num!=-1){
    //$query_id_local="SELECT lc.full_address_google FROM locales lc WHERE lc.id_local=$id_local_num";
    $query="
		SELECT 
		lc.full_address_google, lc.name_local_google, lc.id_local

		FROM locales lc

        WHERE lc.id_local = $id_local_num

		";
    $result=mysqli_query($link,$query);
		
		$num_rows_locals=mysqli_num_rows($result);
		
		if($num_rows_locals)
		{

        $fila=mysqli_fetch_array($result);
				
				$local_name=$fila['name_local_google'];
				$id_loc=$id_local_num;
				$full_addr=$fila['full_address_google'];
        $newad = $local_name . " - " . $full_addr . " - " . $id_loc ;
        echo $newad;
    }

}else{
	$id_loc = $id_local_num;
	$newad = $id_loc;
	echo $newad;
}

?>