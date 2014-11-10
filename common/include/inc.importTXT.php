<?php
if ((stristr( $_SERVER['REQUEST_URI'], "session.php") ) || ( !defined('T3_ABSPATH') )) die("no access");
/*
 must be ADMIN
 */
if($_SESSION[$_SESSION["CFGURL"]][ssuser_nivel]=='1'){
	// get the variables

	// tests
	$ok = true ;
	$error = array() ;


	if ( $ok && utf8_encode(utf8_decode($content_text)) == $content_text ) {
		$content_text = NULL ;
	}
	else {
		$ok = false ;
		$error[] = "ERROR : encodage of import file must be UTF-8" ;
		// sinon faire conversion automatique
	}





	if (($_POST['taskAdmin']=='importTag') && (file_exists($_FILES["file"]["tmp_name"])) )
	{

		$src_txt= $_FILES["file"]["tmp_name"];
			

		//tag separator
		$separador=":";
		$t_relacion='';
		$tabulador='====';

		//admited tags
		$arrayTiposTermino =  array("BT","NT","RT","UF","SN","CN","HN","Use","CODE",$tabulador);

		//lang
		$thes_lang=$_SESSION["CFGIdioma"];

		/*
		 Procesamiento del file
		 */
		$fd=fopen($src_txt,"r");

		while ( ($contents= fgets($fd)) !== false ) {
			$rw=trim($contents);

			$rwTerms=explode($separador,$rw);

			//Ver si es un array
			if(is_array($rwTerms))
			{

				$term=trim($rwTerms[0]);
					
				//Si es un término o un tag
				$t_relacion=(in_array(trim($rwTerms[0]),$arrayTiposTermino)) ? trim($rwTerms[0]) : $t_relacion;
					
				if ((strlen($term)>0) && (!$rwTerms[1])) {
						
					$term_id=resolveTerm_id($term);
					$i=++$i;
				}

				if (in_array(trim($rwTerms[0]),$arrayTiposTermino)) {

					$objectTerm=trim($rwTerms[1]);

					if($objectTerm)
					
					//Retomar tag
					$label=(trim($rwTerms[0])==$tabulador) ? $past_label : trim($rwTerms[0]);
					
					switch ($label) {
						case 'CODE':
						edit_single_code($term_id,$objectTerm);
						break;

						case 'BT':
						$BTterm_id=resolveTerm_id($objectTerm,"1");
						ALTArelacionXId($BTterm_id,$term_id,"3");
						break;

						case 'RT':
						$RTterm_id=resolveTerm_id($objectTerm,"1");
						ALTArelacionXId($term_id,$RTterm_id,"2");
						ALTArelacionXId($RTterm_id,$term_id,"2");
						break;

						case 'NT':
						$NTterm_id=resolveTerm_id($objectTerm,"1");
						ALTArelacionXId($term_id,$NTterm_id,"3");
						break;

						case 'UF':
							$UFterm_id=resolveTerm_id($objectTerm);
							ALTArelacionXId($UFterm_id,$term_id,"4");
							break;

						case 'USE':
							$UFterm_id=resolveTerm_id($objectTerm,"1");
							ALTArelacionXId($term_id,$UFterm_id,"4");
							break;

						case 'SN';//nota de alcance
						abmNota("A",$term_id,"NA","$thes_lang",trim($objectTerm));
						break;


						case 'HN':
							abmNota("A",$term_id,"NH","$thes_lang",trim($objectTerm));
							break;

						case 'CN'://nota catalográfica
							abmNota("A",$term_id,"NC","$thes_lang",trim($objectTerm));
							break;


						default :

							break;
					}

					$past_label=$label;
				}//fin del if mida algo el termino
					
				//}	//fin del if in_array por tipo de relacions
			}	// fin del if es un array
		}// fin del arbribr archo
		fclose($fd);
		//recreate index
		$sql=SQLreCreateTermIndex();
		echo '<p class="true">'.ucfirst(IMPORT_finish).'</p>' ;
	};
}
/*
 functiones
 */




function ALTArelacionXId($id_mayor,$id_menor,$t_relacion){
	GLOBAL $DBCFG;

	if(($id_mayor>0) && ($id_menor>0))
	{
		return do_r($id_mayor,$id_menor,$t_relacion);
	}
}
?>


