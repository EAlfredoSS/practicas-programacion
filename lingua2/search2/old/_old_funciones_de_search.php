<?php


require('../files/bd.php');



function lenguas_que_conoce_usuario($usuario_input,$link)
{

	//SACAMOS LENGUAS QUE CONOCE EL USUARIO my_langs

	$query_my_langs = "
	SELECT my_l.*, l_names.Print_Name AS full_lang_name, l.lang_id AS lang_codigo2letras
	FROM my_langs my_l
	LEFT JOIN  languages_names l_names
	ON my_l.lang_id=l_names.Id
	LEFT JOIN languages1 l
	ON  my_l.lang_id=l.Id
	WHERE my_l.id='$usuario_input' 
	ORDER BY my_l.level_id 
	DESC;";

	$result_my_langs = mysqli_query($link, $query_my_langs);
	$num_my_langs = mysqli_num_rows($result_my_langs);

	$my_langs_array=array();
	$my_langs_2letters_array=array();
	$my_langs_full_name_array=array();
	$my_langs_level_array=array();
	$my_langs_forshare_array=array();
	$my_langs_price_array=array();
	$my_langs_typeofexchange_array=array();
	$my_langs_priceorexchangetext_array=array();
	$my_langs_level_image_array=array();



	for ($jjj = 0; $jjj < $num_my_langs; $jjj++) 
	{
		$fila_my_langs = mysqli_fetch_array($result_my_langs);
		array_push($my_langs_full_name_array,$fila_my_langs['full_lang_name']);
		array_push($my_langs_array,$fila_my_langs['lang_id']);
		array_push($my_langs_2letters_array,$fila_my_langs['lang_codigo2letras']);
		array_push($my_langs_level_array,$fila_my_langs['level_id']);
		array_push($my_langs_forshare_array,$fila_my_langs['for_share']);
		array_push($my_langs_price_array,$fila_my_langs['lang_price']);
		
	}


	//print_r($my_langs_2letters_array);


	// en la tabla languages_names, para algunos idiomas hay id repetido, por ejemplo para spa hay dos lineas (spanish, castilian)
	//por eso hay que detectar las repeticiones y borrarlas, pero el nombre del idioma queda: Castilian, Spanish

	$duplicate_langs = array_count_values($my_langs_array);
	$lista_idiomas_dup=array();

	//print_r($duplicate_langs);

	//$n_dups=0;

	for ($jjj = 0; $jjj < $num_my_langs; $jjj++) 
	{
		$lang1=$my_langs_array[$jjj];
		if($duplicate_langs["$lang1"]==1)
		{
			unset($duplicate_langs["$lang1"]);
			//$n_dups++;
		}
	}
	
	$n_dups=count($duplicate_langs);

	//print_r($duplicate_langs);
	$lista_idiomas_dup=array_keys($duplicate_langs);
	//print_r($lista_idiomas_dup);





	for ($iiii = 0; $iiii < $n_dups; $iiii++) 
	{
		$nombre_idiomas_array='';
		$lang2=$lista_idiomas_dup[$iiii];
		//echo "$lang2";
			
		//$cnt tiene el número de veces que se repite el valor $lang
		$tmp = array_count_values($my_langs_array);
		$cnt = $tmp[$lang2];
		
		for($jjjj=0;$jjjj<$cnt;$jjjj++) 
		{
			$key2 = array_search($lang2, $my_langs_array);
			if($jjjj<$cnt-1)//nos cargamos todos menos 1 valor
			{
				$nombre_idiomas_array.="$my_langs_full_name_array[$key2] | ";
				$my_langs_array[$key2]='_delete_';
			}
			else
			{
				$nombre_idiomas_array.="$my_langs_full_name_array[$key2]";
			}
			
			$my_langs_full_name_array[$key2]=$nombre_idiomas_array;
		}
			
	} 


	//print_r($my_langs_array);






	// escogemos la imagen de los idiomas my_langs



	for($iii=0;$iii<count($my_langs_level_array);$iii++)
	{
		switch ($my_langs_level_array[$iii]) 
		{
			case 0:
				$my_langs_level_image_array[$iii]='no_data.png';
				break;
			case 1:
				$my_langs_level_image_array[$iii]='zero_knowledge.png';
				break;
			case 2:
				$my_langs_level_image_array[$iii]='a1.png';
				break;
			case 3:
				$my_langs_level_image_array[$iii]='a2.png';
				break;
			case 4:
				$my_langs_level_image_array[$iii]='b1.png';
				break;
			case 5:
				$my_langs_level_image_array[$iii]='b2.png';
				break;
			case 6:
				$my_langs_level_image_array[$iii]='c1.png';
				break;
			case 7:
				$my_langs_level_image_array[$iii]='c2.png';
				break;
		}
	}


	//segun el idioma tres opciones: no comparte, comparte por dinero o comparte por otro idioma

	for($iii=0;$iii<count($my_langs_array);$iii++)
	{
		if($my_langs_forshare_array[$iii]==0)
		{
			$my_langs_typeofexchange_array[$iii]='I know this language, but I do not want to exchange or teach it.';
			$my_langs_priceorexchangetext_array[$iii]='';
		}
		else if($my_langs_price_array[$iii]==null)
		{
			$my_langs_typeofexchange_array[$iii]='I know this language and I want to exchange it for another user\'s language (exchange free of cost).';
			$my_langs_priceorexchangetext_array[$iii]="EXCH.";
		}
		else
		{
			$my_langs_typeofexchange_array[$iii]="I know this language and I want to teach it for money.";
			$my_langs_priceorexchangetext_array[$iii]="$my_langs_price_array[$iii] &#8364;/h";
		}
	}
	 

	/*
	print_r($my_langs_array);
	print_r($my_langs_level_array); 
	print_r($my_langs_level_image_array);
	print_r($my_langs_forshare_array); 
	print_r($my_langs_price_array); 
	print_r($my_langs_typeofexchange_array); 
	*/


	//aqui quitamos las lenguas borradas con '_delete_' (pero quedan desorganizados)

	$key3 = array_search('_delete_', $my_langs_array);
	while($key3!==false)
	{
		
		//echo "key:-$key3-";
		
		unset($my_langs_array[$key3]);
		unset($my_langs_full_name_array[$key3]);
		unset($my_langs_level_array[$key3]);
		unset($my_langs_forshare_array[$key3]);
		unset($my_langs_price_array[$key3]);
		unset($my_langs_typeofexchange_array[$key3]);
		unset($my_langs_priceorexchangetext_array[$key3]);
		unset($my_langs_level_image_array[$key3]);
		unset($my_langs_2letters_array[$key3]);
			
		$key3 = array_search('_delete_', $my_langs_array);
	}


	//print_r($my_langs_array);

	$tmp1_array=array();
	$tmp2_array=array();
	$tmp3_array=array();
	$tmp4_array=array();
	$tmp5_array=array();
	$tmp6_array=array();
	$tmp7_array=array();
	$tmp8_array=array();
	$tmp9_array=array();



	$n_lenguas=count($my_langs_array);

	for ($i=0;$i<$n_lenguas;$i++)
	{
		$tmp1_array[$i]=array_pop($my_langs_array);
		$tmp2_array[$i]=array_pop($my_langs_full_name_array);
		$tmp3_array[$i]=array_pop($my_langs_level_array);
		$tmp4_array[$i]=array_pop($my_langs_forshare_array);
		$tmp5_array[$i]=array_pop($my_langs_price_array);
		$tmp6_array[$i]=array_pop($my_langs_typeofexchange_array);
		$tmp7_array[$i]=array_pop($my_langs_priceorexchangetext_array);
		$tmp8_array[$i]=array_pop($my_langs_level_image_array);
		$tmp9_array[$i]=array_pop($my_langs_2letters_array);
	}

	$my_langs_array=array_reverse($tmp1_array);
	$my_langs_full_name_array=array_reverse($tmp2_array);
	$my_langs_level_array=array_reverse($tmp3_array);
	$my_langs_forshare_array=array_reverse($tmp4_array);
	$my_langs_price_array=array_reverse($tmp5_array);
	$my_langs_typeofexchange_array=array_reverse($tmp6_array);
	$my_langs_priceorexchangetext_array=array_reverse($tmp7_array);
	$my_langs_level_image_array=array_reverse($tmp8_array);
	$my_langs_2letters_array=array_reverse($tmp9_array);


	//print_r($my_langs_array);
	
		return array($my_langs_array, $my_langs_full_name_array, $my_langs_level_array, $my_langs_forshare_array, 
				$my_langs_price_array, $my_langs_typeofexchange_array, $my_langs_priceorexchangetext_array, 
				$my_langs_level_image_array, $my_langs_2letters_array);





// FIN SACAMOS LENGUAS QUE CONOCE EL USUARIO my_langs
}




/*

list($my_langs_array, $my_langs_full_name_array, $my_langs_level_array, $my_langs_forshare_array, 
	$my_langs_price_array, $my_langs_typeofexchange_array, $my_langs_priceorexchangetext_array, 
	$my_langs_level_image_array, $my_langs_2letters_array) = lenguas_que_conoce_usuario(5484,$link);

echo "<br><br>1- 3 digitos <br><br>";
print_r($my_langs_array);

echo "<br><br>2- full name <br>";
print_r($my_langs_full_name_array);

echo "<br><br>3- level <br>";
print_r($my_langs_full_name_array);

echo "<br><br>4- for share <br>";
print_r($my_langs_forshare_array);

echo "<br><br>5- price <br>";
print_r($my_langs_price_array);

echo "<br><br>6- type of exchange <br>";
print_r($my_langs_typeofexchange_array);

echo "<br><br>7- price or exchange text <br>";
print_r($my_langs_priceorexchangetext_array);

echo "<br><br>8- learn_langs level image array <br>";
print_r($my_langs_level_image_array);

echo "<br><br>9- learn langas 2 letras <br>";
print_r($my_langs_2letters_array);




echo "<br><br>-------------------------<br><br>";
*/


//----------------------------------------------------------------------------









function lenguas_que_quiere_estudiar_usuario($usuario_input,$link)
{


	//SACAMOS LENGUAS QUE QUIERE APRENDER EL USUARIO my_langs

	$query_learn_langs = "
	SELECT learn_l.*, l_names.Print_Name AS full_lang_name, l.lang_id AS lang_codigo2letras
	FROM learn_langs learn_l
	LEFT JOIN  languages_names l_names
	ON learn_l.lang_id=l_names.Id
	LEFT JOIN languages1 l
	ON  learn_l.lang_id=l.Id
	WHERE learn_l.id='$usuario_input' 
	ORDER BY learn_l.level_id 
	DESC"; 
	

	$result_learn_langs = mysqli_query($link, $query_learn_langs);
	$num_learn_langs = mysqli_num_rows($result_learn_langs);

	$learn_langs_array=array();
	$learn_langs_2letters_array=array();
	$learn_langs_full_name_array=array();
	$learn_langs_level_array=array();
	$learn_langs_forshare_array=array();
	$learn_langs_price_array=array();
	$learn_langs_typeofexchange_array=array();
	$learn_langs_priceorexchangetext_array=array();
	$learn_langs_level_image_array=array(); 



	for ($jjj = 0; $jjj < $num_learn_langs; $jjj++) 
	{
		$fila_learn_langs = mysqli_fetch_array($result_learn_langs);
		array_push($learn_langs_full_name_array,$fila_learn_langs['full_lang_name']);
		array_push($learn_langs_array,$fila_learn_langs['lang_id']);
		array_push($learn_langs_2letters_array,$fila_learn_langs['lang_codigo2letras']);
		array_push($learn_langs_level_array,$fila_learn_langs['level_id']);
		array_push($learn_langs_forshare_array,$fila_learn_langs['for_share']);
		array_push($learn_langs_price_array,$fila_learn_langs['lang_price']);
		
	}


	//print_r($learn_langs_2letters_array);


	// en la tabla languages_names, para algunos idiomas hay id repetido, por ejemplo para spa hay dos lineas (spanish, castilian)
	//por eso hay que detectar las repeticiones y borrarlas, pero el nombre del idioma queda: Castilian, Spanish

	$duplicate_langs = array_count_values($learn_langs_array);
	$lista_idiomas_dup=array();

	//print_r($duplicate_langs);

	//$n_dups=0;

	for ($jjj = 0; $jjj < $num_learn_langs; $jjj++) 
	{
		$lang1=$learn_langs_array[$jjj];
		if($duplicate_langs["$lang1"]==1)
		{
			unset($duplicate_langs["$lang1"]);
			//$n_dups++;
		}
	}
	$n_dups=count($duplicate_langs);

	//print_r($duplicate_langs);
	$lista_idiomas_dup=array_keys($duplicate_langs);
	//print_r($lista_idiomas_dup);





	for ($iiii = 0; $iiii < $n_dups; $iiii++) 
	{
		$nombre_idiomas_array='';
		$lang2=$lista_idiomas_dup[$iiii];
		//echo "$lang2";
			
		//$cnt tiene el número de veces que se repite el valor $lang
		$tmp = array_count_values($learn_langs_array);
		$cnt = $tmp[$lang2];
		
		for($jjjj=0;$jjjj<$cnt;$jjjj++) 
		{
			$key2 = array_search($lang2, $learn_langs_array);
			if($jjjj<$cnt-1)//nos cargamos todos menos 1 valor
			{
				$nombre_idiomas_array.="$learn_langs_full_name_array[$key2] | ";
				$learn_langs_array[$key2]='_delete_';
			}
			else
			{
				$nombre_idiomas_array.="$learn_langs_full_name_array[$key2]";
			}
			
			$learn_langs_full_name_array[$key2]=$nombre_idiomas_array;
		}
			
	} 


	//print_r($learn_langs_array);






	// escogemos la imagen de los idiomas learn_langs



	for($iii=0;$iii<count($learn_langs_level_array);$iii++)
	{
		switch ($learn_langs_level_array[$iii]) 
		{
			case 0:
				$learn_langs_level_image_array[$iii]='no_data.png';
				break;
			case 1:
				$learn_langs_level_image_array[$iii]='zero_knowledge.png';
				break;
			case 2:
				$learn_langs_level_image_array[$iii]='a1.png';
				break;
			case 3:
				$learn_langs_level_image_array[$iii]='a2.png';
				break;
			case 4:
				$learn_langs_level_image_array[$iii]='b1.png';
				break;
			case 5:
				$learn_langs_level_image_array[$iii]='b2.png';
				break;
			case 6:
				$learn_langs_level_image_array[$iii]='c1.png';
				break;
			case 7:
				$learn_langs_level_image_array[$iii]='c2.png';
				break;
		}
	}



	/*
	print_r($learn_langs_array);
	print_r($learn_langs_level_array); 
	print_r($learn_langs_level_image_array);
	print_r($learn_langs_forshare_array); 
	print_r($learn_langs_price_array); 
	print_r($learn_langs_typeofexchange_array); 
	*/


	//aqui quitamos las lenguas borradas con '_delete_' (pero quedan desorganizados)

	$key3 = array_search('_delete_', $learn_langs_array);
	while($key3!==false)
	{
		
		//echo "-$key3-";
		
		unset($learn_langs_array[$key3]);
		unset($learn_langs_full_name_array[$key3]);
		unset($learn_langs_level_array[$key3]);
		unset($learn_langs_forshare_array[$key3]);
		unset($learn_langs_price_array[$key3]);
		unset($learn_langs_typeofexchange_array[$key3]);
		unset($learn_langs_priceorexchangetext_array[$key3]);
		unset($learn_langs_level_image_array[$key3]);
		unset($learn_langs_2letters_array[$key3]);
			
		$key3 = array_search('_delete_', $learn_langs_array);
	}


	//print_r($learn_langs_array);

	$tmp1_array=array();
	$tmp2_array=array();
	$tmp3_array=array();
	$tmp4_array=array();
	$tmp5_array=array();
	$tmp6_array=array();
	$tmp7_array=array();
	$tmp8_array=array();
	$tmp9_array=array();



	$n_lenguas=count($learn_langs_array);

	for ($i=0;$i<$n_lenguas;$i++)
	{
		$tmp1_array[$i]=array_pop($learn_langs_array);
		$tmp2_array[$i]=array_pop($learn_langs_full_name_array);
		$tmp3_array[$i]=array_pop($learn_langs_level_array);
		$tmp4_array[$i]=array_pop($learn_langs_forshare_array);
		$tmp5_array[$i]=array_pop($learn_langs_price_array);
		$tmp6_array[$i]=array_pop($learn_langs_typeofexchange_array);
		$tmp7_array[$i]=array_pop($learn_langs_priceorexchangetext_array);
		$tmp8_array[$i]=array_pop($learn_langs_level_image_array);
		$tmp9_array[$i]=array_pop($learn_langs_2letters_array);
	}

	$learn_langs_array=array_reverse($tmp1_array);
	$learn_langs_full_name_array=array_reverse($tmp2_array);
	$learn_langs_level_array=array_reverse($tmp3_array);
	$learn_langs_forshare_array=array_reverse($tmp4_array);
	$learn_langs_price_array=array_reverse($tmp5_array);
	$learn_langs_typeofexchange_array=array_reverse($tmp6_array);
	$learn_langs_priceorexchangetext_array=array_reverse($tmp7_array);
	$learn_langs_level_image_array=array_reverse($tmp8_array);
	$learn_langs_2letters_array=array_reverse($tmp9_array);




	return array($learn_langs_array, $learn_langs_full_name_array, $learn_langs_level_array, $learn_langs_forshare_array, 
				$learn_langs_price_array, $learn_langs_typeofexchange_array, $learn_langs_priceorexchangetext_array, $learn_langs_level_image_array, $learn_langs_2letters_array);



	//FINAL DE SACAMOS LENGUAS QUE QUIERE APRENDER EL USUARIO learn_langs
	
}


/*
list($learn_langs_array, $learn_langs_full_name_array, $learn_langs_level_array, $learn_langs_forshare_array, 
	$learn_langs_price_array, $learn_langs_typeofexchange_array, $learn_langs_priceorexchangetext_array, 
	$learn_langs_level_image_array, $learn_langs_2letters_array) = lenguas_que_quiere_estudiar_usuario(5484,$link);

echo "<br><br>1- 3 digitos <br><br>";
print_r($learn_langs_array);

echo "<br><br>2- full name <br>";
print_r($learn_langs_full_name_array);

echo "<br><br>3- level <br>";
print_r($learn_langs_full_name_array);

echo "<br><br>4- for share <br>";
print_r($learn_langs_forshare_array);

echo "<br><br>5- price <br>";
print_r($learn_langs_price_array);

echo "<br><br>6- type of exchange <br>";
print_r($learn_langs_typeofexchange_array);

echo "<br><br>7- price or exchange text <br>";
print_r($learn_langs_priceorexchangetext_array);

echo "<br><br>8- learn_langs level image array <br>";
print_r($learn_langs_level_image_array);

echo "<br><br>9- learn langas 2 letras <br>";
print_r($learn_langs_2letters_array);


*/




?>