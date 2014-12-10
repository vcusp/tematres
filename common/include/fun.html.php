<?php
// don't load directly
if ((stristr( $_SERVER['REQUEST_URI'], "session.php") ) || ( !defined('T3_ABSPATH') )) die("no access");
#   TemaTres : aplicación para la gestión de lenguajes documentales #       #
#                                                                        #
#   Copyright (C) 2004-2008 Diego Ferreyra tematres@r020.com.ar
#   Distribuido bajo Licencia GNU Public License, versión 2 (de junio de 1.991) Free Software Foundation
#  
###############################################################################################################
# Funciones HTML. #
#

#
# Armado de resultados de búsqueda
#
function resultaBusca($texto,$tipo=""){

GLOBAL $CFG;

$texto=trim($texto);

//Anulación de sugerencia de términos
$sgs=$_GET[sgs];

//Ctrol lenght string
if((strlen($texto)>=CFG_MIN_SEARCH_SIZE) || ($tipo=='E'))
{
	$sql= ($tipo=='E') ? SQLbuscaExacta("$texto") : SQLbuscaSimple("$texto");
	$sql_cant=SQLcount($sql);
	$classMensaje= ($sql_cant>0) ? 'information' : 'warning';
	
	$resumeResult = '<p class="'.$classMensaje.'"><strong>'.$sql_cant.'</strong> '.MSG_ResultBusca.' <strong> "<em>'.stripslashes($texto).'</em>"</strong></p>';
} 
else
{
	$sql_cant='0';	
	$resumeResult = '<p class="error">'.sprintf(MSG_minCharSerarch,stripslashes($texto),strlen($texto),CFG_MIN_SEARCH_SIZE-1).'</p>';
}

 $body.='<div id="bodyText">';
 $body.='<div class="row">';
 $body.='<div class="col-md-12">';
 $body.='<h1>'.LABEL_busqueda.'</h1>';
 $body.='</div>';
 $body.='</div>';
 $body.='<div class="row">';
 $body.='<div class="col-md-8">';
 $body.=$resumeResult;

if($sql_cant>0)
 {
	 $row_result.='<div id="listaBusca"><ul>';

	 while($resulta_busca=$sql->FetchRow()){

		$ibusca=++$ibusca;
		$acumula_indice.=$resulta_busca[indice];
		
		$acumula_temas.=$resulta_busca[id_definitivo].'|';

		if($ibusca=='1')
		{
			//Guardar el primer término para ver si hay coincidencia exacta
			$primerTermino=$resulta_busca[tema];
			$primerTermino_id=($resulta_busca[id_definitivo]) ? $resulta_busca[id_definitivo] : $resulta_busca[tema_id];
		}
		

		//si hubo coicidencia exacta y están apagadas las sugerencias
		if((strtoupper($primerTermino)==trim(strtoupper($texto))) && (($_GET["sgs"]=='off') || ($sql_cant==1)))
		{
			return HTMLbodyTermino(ARRAYverDatosTermino($primerTermino_id));
		}
		
		
		$leyendaTerminoLibre=($resulta_busca[esTerminoLibre]=='SI') ? ' ('.LABEL_terminoLibre.')' : '';


		$styleClassLink= ($resulta_busca[estado_id]!=='13') ? 'class="estado_termino'.$resulta_busca[estado_id].'"' : '';

	//Si no es un término preferido
		if($resulta_busca[termino_preferido])
		{
				switch($resulta_busca[t_relacion])
				{
				case '4'://UF
				$leyendaConector=USE_termino;
				break;
	
				case '5'://Tipo relacion término equivalente parcialmente
				$leyendaConector='<acronym title="'.LABEL_termino_parcial_equivalente.'" lang="'.LANG.'">'.EQP_acronimo.'</acronym>';
				break;
	
				case '6'://Tipo relacion término equivalente
				$leyendaConector='<acronym title="'.LABEL_termino_equivalente.'" lang="'.LANG.'">'.EQ_acronimo.'</acronym>';
				break;
	
				case '7'://Tipo relacion término no equivalente
				$leyendaConector='<acronym title="'.LABEL_termino_no_equivalente.'" lang="'.LANG.'">'.NEQ_acronimo.'</acronym>';
				break;
	
				case '8'://Tipo relacion término equivalente inexacta
				$leyendaConector='<acronym title="'.LABEL_termino_parcial_equivalente.'" lang="'.LANG.'">'.EQP_acronimo.'</acronym>';
				break;
				}

			
			if ((in_array($resulta_busca[rr_code],$CFG["HIDDEN_EQ"])) && (!$_SESSION[$_SESSION["CFGURL"]][ssuser_id])) 
			{
				$row_result.= '<li><a title="'.LABEL_verDetalle.$resulta_busca[tema].'" href="index.php?tema='.$resulta_busca[id_definitivo].'">'.$resulta_busca[termino_preferido].'</a></li>'."\r\n" ;

			}
			else 
			{
				$row_result.= '<li><em><a '.$styleClassLink.' title="'.LABEL_verDetalle.$resulta_busca[tema].'" href="index.php?tema='.$resulta_busca[tema_id].'&amp;/'.string2url($resulta_busca[tema]).'">'.$resulta_busca[tema].'</a></em> '.$leyendaConector.' <a title="'.LABEL_verDetalle.$resulta_busca[tema].'" href="index.php?tema='.$resulta_busca[id_definitivo].'">'.$resulta_busca[termino_preferido].'</a> </li>'."\r\n" ;
			}
			
		}
		else // es un término preferido
		{
			$row_result.='<li><a '.$styleClassLink.' title="'.LABEL_verDetalle.$resulta_busca[tema].'" href="index.php?tema='.$resulta_busca[id_definitivo].'&amp;/'.string2url($resulta_busca[tema]).'">'.$resulta_busca[tema].'</a> '.$leyendaTerminoLibre.'</li>'."\r\n" ;
		}

	 };//fin del while
	$row_result.='</ul>';


$row_result.='</div>';
$row_result.='</div>';
$row_result.='<div class="col-md-4">';



	//Si no hubo coincidencia exacta
	if((strtoupper($primerTermino)!==trim(strtoupper($texto))) && ($_GET["sgs"]!='off'))
	{

		$body.=HTMLsugerirTermino($texto,$acumula_temas);
	}

}
elseif(strlen($texto)>=CFG_MIN_SEARCH_SIZE)// Si no hay resultados y la expresión es mayor al mínimo
{
		
	$body.=HTMLsugerirTermino($texto);

};// fin de if result

$result_suplementa.=HTMLbusquedaExpandidaTG($acumula_indice,$acumula_temas);
$result_suplementa.=HTMLbusquedaExpandidaTR($acumula_temas);

if(strlen($result_suplementa)>0)
{
		//~ $result_suplementa='<div id="lista_secundaria">'.$result_suplementa.'</div>';
                $result_suplementa='<div>'.$result_suplementa.'</div></div></div>';
                
}


return $body.$row_result.$result_suplementa.'';


};

#######################################################################

#
#  ARMADOR DE HTML CON DATOS DEL TERMINO
#
function doContextoTermino($idTema,$i_profundidad){
	
GLOBAL $CFG;

//recibe de HTMLbodyTermino
//$idTema = id del término
//$i_profundidad= contador de profundidad

//Terminos específicos
$sqlNT=SQLverTerminosE($idTema);

//Se devolverá el tema_id para utilizar en fuentes XML y como tema_id canónico
$tema_id=$idTema;

while ($datosNT=$sqlNT->FetchRow()){
	$int=++$int;

	if($datosNT[id_te]){
		$link_next=' <a href="javascript:expand(\''.$datosNT[id_tema].'\')" title="'.LABEL_verDetalle.' '.$datosNT[tema].'"><span id="expandTE'.$datosNT[id_tema].'">&#x25ba;</span><span id="contraeTE'.$datosNT[id_tema].'" style="display: none">&#x25bc;</span></a>';
		$link_next.=HTMLverTE($datosNT[id_tema],$i_profundidad);
		}else{
		$link_next='';
		};

		//editor de relaciones
	if($_SESSION[$_SESSION["CFGURL"]][ssuser_id]){
		$td_delete='<a id="elimina_'.$datosNT[id_tema].'" title="'.LABEL_borraRelacion.'"  class="eliminar" href="'.$PHP_SELF.'?ridelete='.$datosNT[id_relacion].'&amp;tema='.$idTema.'" onclick="return askData();"><acronym title="'.LABEL_borraRelacion.'" lang="'.LANG.'">x</acronym></a>';
		$row_NT.=' <li  id="t'.$datosNT[id_tema].'">'.$td_delete.'<acronym class="thesacronym" title="'.TE_termino.' '.$datosNT[rr_value].'" lang="'.LANG.'" id="r'.$datosNT[rel_id].'"><span class="editable_selectTE" id="edit_rel_id'.$datosNT[rel_id].'" style="display: inline">'.TE_acronimo.$datosNT[rr_code].'</span>'.$i_profundidad.'</acronym>';	
		
		//Editor de código
		$row_NT.=($CFG["_USE_CODE"]=='1') ? '<div title="term code, click to edit" class="editable_textarea" id="code_tema'.$datosNT[id_tema].'">'.$datosNT[code].'</div>' : '';
		
	}
	else 
	{
		$row_NT.=' <li id="t'.$datosNT[id_tema].'"><acronym class="thesacronym" id="r'.$datosNT[rel_id].'" title="'.TE_termino.' '.$datosNT[rr_value].'" lang="'.LANG.'">'.TE_acronimo.$datosNT[rr_code].$i_profundidad.'</acronym>';			
		//ver  código
		$row_NT.=($CFG["_SHOW_CODE"]=='1') ? ' '.$datosNT[code].' ' : '';
	}

	$css_class_MT=($datosNT["isMetaTerm"]==1) ? ' class="metaTerm" ' : '';
	$label_MT=($datosNT["isMetaTerm"]==1) ? NOTE_isMetaTerm : '';

	$row_NT.='<a '.$css_class_MT.' title="'.LABEL_verDetalle.' '.$datosNT[tema].' ('.TE_termino.') '.$label_MT.'"  href="index.php?tema='.$datosNT[id_tema].'&amp;/'.string2url($datosNT[tema]).'">'.$datosNT[tema].'</a>'.$link_next.'</li>';	
};

// Terminos TG, UF y TR
$sqlTotalRelacionados=SQLverTerminoRelaciones($tema_id);

while($datosTotalRelacionados= $sqlTotalRelacionados->FetchRow()){
	
	if($_SESSION[$_SESSION["CFGURL"]][ssuser_id]){
		$td_delete='<a  class="eliminar" title="'.LABEL_borraRelacion.'" href="'.$PHP_SELF.'?ridelete='.$datosTotalRelacionados[id_relacion].'&amp;tema='.$idTema.'" onclick="return askData();"><acronym title="'.LABEL_borraRelacion.'" lang="'.LANG.'">x</acronym></a>';
		$classAcrnoyn='editable_select'.$datosTotalRelacionados[t_relacion];
		}else{
		$td_delete='';
		$classAcrnoyn='thesacronym';
		};
	
	#Change to metaTerm attributes
	if(($datosTotalRelacionados["BT_isMetaTerm"]==1))
	{
		$css_class_MT= ' class="metaTerm" ';
		$label_MT=NOTE_isMetaTerm;
	}
	else
	{
		$css_class_MT= '';
		$label_MT='';		
	}
	

	switch($datosTotalRelacionados[t_relacion]){
	case '3':// TG
	$itg=++$itg;
	$row_TG.='          <li>'.$td_delete.'<acronym class="'.$classAcrnoyn.'" id="edit_rel_id'.$datosTotalRelacionados[rel_id].'" style="display: inline" title="'.TG_termino.' '.$datosTotalRelacionados[rr_value].'" lang="'.LANG.'">'.TG_acronimo.$datosTotalRelacionados[rr_code].'</acronym>';
	$row_TG.='          <a '.$css_class_MT.' title="'.LABEL_verDetalle.' '.$datosTotalRelacionados[tema].' ('.TG_termino.') '.$label_MT.'"  href="index.php?tema='.$datosTotalRelacionados[tema_id].'&amp;/'.string2url($datosTotalRelacionados[tema]).'">'.$datosTotalRelacionados[tema].'</a></li>';
	break;

	case '4':// UF
	$iuf=++$iuf;
	//hide hidden equivalent terms
	if ((!in_array($datosTotalRelacionados[rr_code],$CFG["HIDDEN_EQ"])) || ($_SESSION[$_SESSION["CFGURL"]][ssuser_id])) 
	{
		$row_UP.='          <li>'.$td_delete.'<acronym class="'.$classAcrnoyn.'" id="edit_rel_id'.$datosTotalRelacionados[rel_id].'" style="display: inline" title="'.UP_termino.' '.$datosTotalRelacionados[rr_value].'" lang="'.LANG.'">'.UP_acronimo.$datosTotalRelacionados[rr_code].'</acronym>';
		$row_UP.='          <a title="'.LABEL_verDetalle.' '.$datosTotalRelacionados[tema].' ('.UP_termino.')"  href="index.php?tema='.$datosTotalRelacionados[tema_id].'&amp;/'.string2url($datosTotalRelacionados[tema]).'">'.$datosTotalRelacionados[tema].'</a></li>';	
	}
	break;

	case '2':// TR
	$irt=++$irt;
	$row_TR.='          <li>'.$td_delete.'<acronym class="'.$classAcrnoyn.'" id="edit_rel_id'.$datosTotalRelacionados[rel_id].'" style="display: inline" title="'.TR_termino.' '.$datosTotalRelacionados[rr_value].'" lang="'.LANG.'">'.TR_acronimo.$datosTotalRelacionados[rr_code].'</acronym>';
	$row_TR.='          <a '.$css_class_MT.' title="'.LABEL_verDetalle.' '.$datosTotalRelacionados[tema].' ('.TR_termino.') '.$label_MT.'"  href="index.php?tema='.$datosTotalRelacionados[tema_id].'&amp;/'.string2url($datosTotalRelacionados[tema]).'">'.$datosTotalRelacionados[tema].'</a></li>';
	break;

	case '5':// parcialmente EQ
	$ieq=++$ieq;
	$row_EQ.='          <li>'.$td_delete.' <acronym class="thesacronym" title="'.LABEL_termino_parcial_equivalente.'" lang="'.LANG.'">'.EQP_acronimo.'</acronym> ';
	$row_EQ.='          <a title="'.LABEL_verDetalle.' '.$datosTotalRelacionados[tema].' ('.LABEL_termino_parcial_equivalente.')"  href="index.php?tema='.$datosTotalRelacionados[tema_id].'&amp;/'.string2url($datosTotalRelacionados[tema]).'">'.$datosTotalRelacionados[tema].'</a> ('.$datosTotalRelacionados[titulo].')</li>';
	break;

	case '6':// EQ
	$ieq=++$ieq;
	$row_EQ.='          <li>'.$td_delete.' <acronym class="thesacronym" title="'.LABEL_termino_equivalente.'" lang="'.LANG.'">'.EQ_acronimo.'</acronym> ';
	$row_EQ.='          <a title="'.LABEL_verDetalle.' '.$datosTotalRelacionados[tema].' ('.LABEL_termino_equivalente.')"  href="index.php?tema='.$datosTotalRelacionados[tema_id].'&amp;/'.string2url($datosTotalRelacionados[tema]).'">'.$datosTotalRelacionados[tema].'</a> ('.$datosTotalRelacionados[titulo].')</li>';
	break;

	case '7':// NO EQ
	$ieq=++$ieq;
	$row_EQ.='          <li>'.$td_delete.' <acronym class="thesacronym" title="'.LABEL_termino_no_equivalente.'" lang="'.LANG.'">'.NEQ_acronimo.'</acronym> ';
	$row_EQ.='          <a title="'.LABEL_verDetalle.' '.$datosTotalRelacionados[tema].' ('.LABEL_termino_no_equivalente.')"  href="index.php?tema='.$datosTotalRelacionados[tema_id].'&amp;/'.string2url($datosTotalRelacionados[tema]).'">'.$datosTotalRelacionados[tema].'</a> ('.$datosTotalRelacionados[titulo].')</li>';
	break;
	}
};

//Si no es un término válido y es un UF.
if(SQLcount($sqlTotalRelacionados)==0){
	
	$sqlTerminosValidosUF=SQLterminosValidosUF($idTema);

	while($arrayTerminosValidosUF= $sqlTerminosValidosUF->FetchRow()){
		
		//Reasignación del tema_id para utilizar en fuentes XML y como tema_id canónico
		$tema_id=$arrayTerminosValidosUF[tema_pref_id];
		
		switch($arrayTerminosValidosUF[t_relacion]){

		case '4':// USE
			$leyendaConector=USE_termino;
			$iuse=++$iuse;
			$row_USE.='<li><em>'.$arrayTerminosValidosUF[tema].'</em> '.$leyendaConector.' <a title="'.LABEL_verDetalle.$arrayTerminosValidosUF[tema_pref].'" href="index.php?tema='.$arrayTerminosValidosUF[tema_pref_id].'">'.$arrayTerminosValidosUF[tema_pref].'</a> </li>'."\r\n" ;
		break;


		case '5':// parcialmente EQ
			$leyendaConector='<acronym class="thesacronym" title="'.LABEL_termino_parcial_equivalente.'" lang="'.LANG.'">'.EQP_acronimo.'</acronym>';
			$ieq=++$ieq;
			$row_EQ='<li><em>'.$arrayTerminosValidosUF[tema].'</em> ('.$arrayTerminosValidosUF[titulo].' / '.$arrayTerminosValidosUF[idioma].') '.$leyendaConector.' <a title="'.LABEL_verDetalle.$arrayTerminosValidosUF[tema_pref].'" href="index.php?tema='.$arrayTerminosValidosUF[tema_pref_id].'">'.$arrayTerminosValidosUF[tema_pref].'</a> </li>'."\r\n" ;
		break;

		case '6':// EQ
			$leyendaConector='<acronym class="thesacronym" title="'.LABEL_termino_equivalente.'" lang="'.LANG.'">'.EQ_acronimo.'</acronym>';
			$ieq=++$ieq;
			$row_EQ='<li><em>'.$arrayTerminosValidosUF[tema].'</em> ('.$arrayTerminosValidosUF[titulo].' / '.$arrayTerminosValidosUF[idioma].') '.$leyendaConector.' <a title="'.LABEL_verDetalle.$arrayTerminosValidosUF[tema_pref].'" href="index.php?tema='.$arrayTerminosValidosUF[tema_pref_id].'">'.$arrayTerminosValidosUF[tema_pref].'</a> </li>'."\r\n" ;
		break;

		case '7':// NO EQ
			$leyendaConector='<acronym class="thesacronym" title="'.LABEL_termino_no_equivalente.'" lang="'.LANG.'">'.NEQ_acronimo.'</acronym>';
			$ieq=++$ieq;
			$row_EQ='<li><em>'.$arrayTerminosValidosUF[tema].'</em> ('.$arrayTerminosValidosUF[titulo].' / '.$arrayTerminosValidosUF[idioma].') '.$leyendaConector.' <a title="'.LABEL_verDetalle.$arrayTerminosValidosUF[tema_pref].'" href="index.php?tema='.$arrayTerminosValidosUF[tema_pref_id].'">'.$arrayTerminosValidosUF[tema_pref].'</a> </li>'."\r\n" ;
		break;

		}
	};
}

$rows=array("UP"=>doListaTag($iuf,"ul",$row_UP,"UP"),
	"USE"=>doListaTag($iuse,"ul",$row_USE,"EQ"),
	"TR"=>doListaTag($irt,"ul",$row_TR,"TR"),
	"TG"=>doListaTag($itg,"ul",$row_TG,"TG"),
	"TE"=>doListaTag($int,"ul",$row_NT,"TE"),
	"EQ"=>doListaTag($ieq,"ul",$row_EQ,"EQ")
	);

$cant_relaciones=array(
		"cantUF"=>$iuf,
		"cantRT"=>$irt,
		"cantTG"=>$itg,
		"cantNT"=>$int,
		"cantTotal"=>$iuf+$irt+$itg+$int+$iuse+$ieq
		);

return array("HTMLterminos"=>$rows,
			 "cantRelaciones"=>$cant_relaciones,
			 "tema_id"=>$tema_id);
};
#######################################################################



function HTMLmenuCustumRel($tema_id,$arrayDataRelation) 
{
	
	$arrayLabel[2]=array(TR_acronimo,TR_termino);
	$arrayLabel[3]=array(TG_acronimo,TG_termino);
	$arrayLabel[4]=array(UP_acronimo,UP_termino);
	
	if($_SESSION[$_SESSION["CFGURL"]][ssuser_id])
	{
		$rows.='<acronym class="editable_select'.$arrayDataRelation[t_relacion].'" id="edit_rel_id'.$arrayDataRelation[rel_id].'" style="display: inline" title="'.$arrayLabel["$arrayDataRelation[t_relacion]"][1].' '.$arrayDataRelation[rr_value].'" lang="'.LANG.'">'.$arrayLabel["$arrayDataRelation[t_relacion]"][0].$arrayDataRelation[rr_code].'</acronym>';
	}
	else 
	{
		$rows.='<acronym class="editable_select'.$arrayDataRelation[t_relacion].'" id="r'.$arrayDataRelation[rel_id].'" style="display: inline" title="'.$arrayLabel["$arrayDataRelation[t_relacion]"][1].' '.$arrayDataRelation[rr_value].'" lang="'.LANG.'">'.$arrayLabel["$arrayDataRelation[t_relacion]"][0].$arrayDataRelation[rr_code].'</acronym>';		
	}
   
return $rows;
}

//home page for term
function HTMLbodyTermino($array){

GLOBAL $MSG_ERROR_RELACION;
GLOBAL $CFG;

  $sqlMiga=SQLarbolTema($array[idTema]);
  
  $cantBT=SQLcount($sqlMiga);

  $i_profundidad=($cantBT>0) ? $cantBT : 1;
  
  $HTMLterminos=doContextoTermino($array[idTema],$i_profundidad);
  
  $fecha_crea=do_fecha($array[cuando]);
  $fecha_estado=do_fecha($array[cuando_estado]);
  
		
//Si tiene padres
if($cantBT>0){
	while($arrayMiga=$sqlMiga->FetchRow()){
		if($arrayMiga[tema_id]!==$array[idTema]){
			$menu_miga.='<li><a title="'.LABEL_verDetalle.$arrayMiga[tema].'" href="index.php?tema='.$arrayMiga[tema_id].'&amp;/'.string2url($arrayMiga[tema]).'" >'.$arrayMiga[tema].'</a></li>';
			}
		}
	};

$row_miga.='<li><a title="'.MENU_Inicio.'" href="index.php">'.ucfirst(MENU_Inicio).'</a></li>'.$menu_miga.'<li><p>'.$array[titTema].'</p></li>';




$body='<div id="bodyText">';

//MENSAJE DE ERROR
$body.=$MSG_ERROR_RELACION;

if($array["isMetaTerm"]==1)
{
	$body.=' <h1 class="metaTerm" title="'.$array[titTema].' - '.NOTE_isMetaTermNote.'" id="T'.$array[tema_id].'">'.$array[titTema].'</h1>';
	$body.=' <div class="alert alert-warning" role="alert" title="'.NOTE_isMetaTermNote.'" id="noteT'.$array[tema_id].'">'.NOTE_isMetaTerm.'</div>';
}
else
{
	$body.=' <h1 id="T'.$array[tema_id].'">'.$array[titTema].'</h1>';
}
//div oculto para eliminar término
if($_SESSION[$_SESSION["CFGURL"]][ssuser_id])
{
	$body.=HTMLconfirmDeleteTerm($array);
}

if(($_SESSION[$_SESSION["CFGURL"]][ssuser_id])||(CFG_VIEW_STATUS=='1'))
	{
	$label_estado='<span class="estado_termino'.$array[estado_id].'"> ' .ucfirst( arrayReplace(array("12","13","14"),array(LABEL_Candidato,LABEL_Aceptado,LABEL_Rechazado),$array[estado_id])). ': '.$fecha_estado[dia].'-'.$fecha_estado[descMes].'-'.$fecha_estado[ano].'</span> ';
   	};



$body.=HTMLNotasTermino($array);

#Div relaciones del terminos
$body.='<div class="row">';
$body.='<div class="col-md-8">';
#Div miga de pan
$body.='<ul id="breadcrumbs-two">';
$body.=$row_miga;
$body.='</ul>';
# fin Div miga de pan
$body.='<div id="relacionesTermino">';
$body.=$HTMLterminos[HTMLterminos]["UP"];
$body.=$HTMLterminos[HTMLterminos]["TG"];


	//Editor de código
	if(($_SESSION[$_SESSION["CFGURL"]][ssuser_id]) && ($CFG["_USE_CODE"]=='1'))
	{
		$body.='<div title="term code, click to edit" class="editable_textarea" id="code_tema'.$array[tema_id].'">'.$array[code].'</div>';
	}
	elseif($CFG["_SHOW_CODE"]=='1')
	{
		$body.= ($array[code]) ? ' <label class="code_tema" for="T'.$array[tema_id].'">'.$array[code].'</label>' : '';
	}


//term menu
if($_SESSION[$_SESSION["CFGURL"]][ssuser_id])
{
	$body.=HTMLtermMenu($array,$HTMLterminos[cantRelaciones]);	
}

	
//el termino
if($_SESSION[$_SESSION["CFGURL"]][ssuser_id])
{	
	//span editable
	$body.=doListaTag('1',"h5",'<span id="edit_tema'.$array[tema_id].'" class="edit_area_term">'.$array[titTema].'</span> ',"term");	
}
else 
{
	$body.=doListaTag('1',"h5",$array[titTema],"term");
}


$body.=$HTMLterminos[HTMLterminos]["USE"];
$body.=$HTMLterminos[HTMLterminos]["TE"];
$body.=$HTMLterminos[HTMLterminos]["TR"];
$body.=$HTMLterminos[HTMLterminos]["EQ"];


$body.=HTMLtargetTerms($array[tema_id]);
$body.=HTMLURI4term($array[tema_id]);


$body.='</div>';
if($array["isMetaTerm"]==1)
{
    $body.='';
}
else
{
$body.='<div class="panel panel-default">';
$body.='<div class="panel-heading">';
$body.='<h3 class="panel-title">Construtor de termo para catalogação</h3>';
$body.='</div>';
$body.='<div class="panel-body">';
$body.='<div class="form-group">';
$body.='<p class="form-control-static" id="titulotermo">'.$array[titTema].'</p>';
$body.='</div>';
$body.='<form method="get" id="qualificador" name="qualificador">';
$body.='<div class="form-group">';
$body.='<label class="sr-only" for="qualificador">Termo</label>';
$body.='<input type="text" class="form-control" id="qualificadorresposta" placeholder="Qualificador">';
$body.='</div>';
$body.='<div class="form-group">';
$body.='<label class="sr-only" for="genero">Gênero e Forma</label>';
$body.='<input type="text" class="form-control" id="generoresposta" placeholder="Gênero e Forma">';
$body.='</div>';
$body.='<div class="form-group">';
$body.='<label class="sr-only" for="exampleInputPassword2">Data</label>';
$body.='<input type="text" class="form-control" id="dataresposta" placeholder="Data">';
$body.='</div>';
$body.='<div class="form-group">';
$body.='<label class="sr-only" for="geografico">Termo</label>';
$body.='<input type="text" class="form-control" id="geograficoresposta" placeholder="Geográfico">';
$body.='</div>';
$body.='<button type="button" class="btn btn-default" onclick="'."with(document.getElementById('resultwrapper').style){ visibility='visible'; display='inline'; } document.querySelector('#resultado').innerText = document.querySelector('#titulotermo').innerText + (document.querySelector('#qualificadorresposta').value.trim()=='' ? '':('\$\$x' + document.querySelector('#qualificadorresposta').value.trim())) + (document.querySelector('#generoresposta').value.trim()==''?'':('\$\$v' + document.querySelector('#generoresposta').value.trim())) + (document.querySelector('#dataresposta').value.trim()==''?'':('\$\$y' + document.querySelector('#dataresposta').value.trim())) + (document.querySelector('#geograficoresposta').value.trim()==''?'':('\$\$z' + document.querySelector('#geograficoresposta').value.trim())) + '\$\$2larpcal';".'">Gerar</button>';
$body.='<br/><br/><div class="form-group" id="resultwrapper" style="visibility:hidden;display:none;">';
$body.='<div id="resultado" class="alert alert-success" style="display:inline-block;float:left;"></div>';
$body.='</form>';
$body.='</div>';
$body.='</div>';
$body.='</div>';
}
$body.='</div>';
$body.='<div class="col-md-4">';
$body.='<div class="panel panel-default">';
/*
 * $HTMLterminos[tema_id] es el ID del término válido siempre
 * 
*/
if($array["isMetaTerm"]==1)
{
$body.='';
}
else
{

$body.='<div class="panel-heading">';
$body.='<h3 class="panel-title">Pesquisar na USP</h3>';
$body.='</div>';
$body.='<div class="panel-body">';
$body.='<a target="_blank" href="http://200.144.190.234/F/?func=scan&scan_code=SUB&scan_start='.$array[titTema].'">Pesquisar no Catálogo DEDALUS</a><br/>';
$body.='<a target="_blank" href="http://www.producao.usp.br/browse?type=subject&value='.$array[titTema].'">Pesquisar na Biblioteca Digital da Produção Intelectual</a><br/>';
$body.='<a target="_blank" href="http://www.teses.usp.br/index.php?option=com_jumi&fileid=19&Itemid=87&lang=pt-br&g=1&c0=p&o0=AND&b0='.$array[titTema].'">Pesquisar na Biblioteca Digital de Teses e Dissertações</a><br/>';
$body.='</div>';
$body.='<div class="panel-heading">';
$body.='<h3 class="panel-title">Pesquisar termo em outras bibliotecas virtuais</h3>';
$body.='</div>';
$body.='<div class="panel-body">';
$body.='<a target="_blank" href="http://www.bv.fapesp.br/pt/metapesquisa/?q='.$array[titTema].'">Pesquisar na Biblioteca Virtual da FAPESP</a>';
$body.='</div>';

$body.='<div class="panel-heading">';
$body.='<h3 class="panel-title">Exportar termo</h3>';
$body.='</div>';
$body.='<div class="panel-body">';

$body.='<ul id="enlaces_xml">';
$body.='        <li><a title="'.LABEL_verEsquema.' BS8723-5"  href="xml.php?bs8723Tema='.$HTMLterminos[tema_id].'">BS8723-5</a></li>';
$body.='        <li><a title="'.LABEL_verEsquema.' Dublin Core"  href="xml.php?dcTema='.$HTMLterminos[tema_id].'">DC</a></li>';
$body.='        <li><a title="'.LABEL_verEsquema.' MADS"  href="xml.php?madsTema='.$HTMLterminos[tema_id].'">MADS</a></li>  ';
$body.='        <li><a title="'.LABEL_verEsquema.' Skos"  href="xml.php?skosTema='.$HTMLterminos[tema_id].'">SKOS-Core</a></li>';
$body.='        <li><a title="'.LABEL_verEsquema.' IMS Vocabulary Definition Exchange (VDEX)"  href="xml.php?vdexTema='.$HTMLterminos[tema_id].'">VDEX</a></li>';
$body.='        <li><a title="'.LABEL_verEsquema.' TopicMap"  href="xml.php?xtmTema='.$HTMLterminos[tema_id].'">XTM</a></li>';
$body.='        <li><a title="'.LABEL_verEsquema.' Zthes" href="xml.php?zthesTema='.$HTMLterminos[tema_id].'">Zthes</a></li>  ';
$body.='        <li><a title="'.LABEL_verEsquema.' JavaScript Object Notation for Linked Data" href="xml.php?jsonTema='.$HTMLterminos[tema_id].'">JSON</a></li>  ';
$body.='        <li><a title="'.LABEL_verEsquema.' JavaScript Object Notation for Linked Data" href="xml.php?jsonldTema='.$HTMLterminos[tema_id].'">JSON-LD</a></li>  ';
$body.='</ul>';
$body.='</div>';

$body.='<div class="panel-heading">';
$body.='<h3 class="panel-title">Pesquisar termo em outras fontes</h3>';
$body.='</div>';
$body.='<div class="panel-body">';
$body.=HTML_URLsearch($CFG[SEARCH_URL_SITES],$array);
$body.='</div>';
}
$body.='<div class="panel-heading">';
$body.='<h3 class="panel-title">Sobre o termo</h3>';
$body.='</div>';
$body.='<div class="panel-body">';
#Div pie de datos
$body.='<ul id="fechas"><li> '.LABEL_Fecha.': '.$fecha_crea[dia].'-'.$fecha_crea[descMes].'-'.$fecha_crea[ano].'</li>';
if($array[cuando_final]){
	$fecha_cambio=do_fecha($array[cuando_final]);
	$body.='<li>'.LABEL_fecha_modificacion.': '.$fecha_cambio[dia].'-'.$fecha_cambio[descMes].'-'.$fecha_cambio[ano].'</li> ';
	}
$body.='</ul>'.$label_estado.' ';
# fin Div pie de datos
$body.='</div>';
$body.='</div>';

#Fin div bodyText
$body.='</div>';
$body.='</div>';




return $body;
};



function HTMLmainMenu(){
	
        $row.='<li class="dropdown">';
        $row.='<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">'.LABEL_Menu.'</a>';
        $row.='<ul class="dropdown-menu" role="menu">';
        $row.='<li><a title="'.ucfirst(MENU_AgregarT).'" href="index.php?taskterm=addTerm&amp;tema=0">'.ucfirst(MENU_AgregarT).'</a></li>';
        $row.='<li role="presentation" class="dropdown-header">'.ucfirst(LABEL_Ver).'</li>';
        $row.='<li><a title="'.ucfirst(LABEL_terminosLibres).'" href="index.php?verT=L">'.ucfirst(LABEL_terminosLibres).'</a></li>';
        $row.='<li><a title="'.ucfirst(LABEL_terminosRepetidos).'" href="index.php?verT=R">'.ucfirst(LABEL_terminosRepetidos).'</a></li>';
        $row.='<li><a title="'.ucfirst(LABEL_termsNoBT).'" href="index.php?verT=NBT">'.ucfirst(LABEL_termsNoBT).'</a></li>';
        $row.='<li><a title="'.ucfirst(LABEL_Rechazados).'" href="index.php?estado_id=14">'.ucfirst(LABEL_Rechazados).'</a></li>';
        $row.='<li><a title="'.ucfirst(LABEL_Candidato).'" href="index.php?estado_id=12">'.ucfirst(LABEL_Candidatos).'</a></li>';       
        $row.='<li role="presentation" class="dropdown-header">Menu do usuário</li>';
        $row.='<li><a title="'.LABEL_FORM_simpleReport.'" href="index.php?mod=csv">'.LABEL_FORM_simpleReport.'</a></li>';
        $row.='<li><a title="'.MENU_MisDatos.'" href="login.php">'.MENU_MisDatos.'</a></li>';
        $row.='<li><a title="'.MENU_Salir.'" href="index.php?cmdlog='.substr(md5(date("Ymd")),"5","10").'">'.MENU_Salir.'</a></li>';        
        $row.='</ul>';
        $row.='</li>';    

/*
 * Admin menu
*/
if($_SESSION[$_SESSION["CFGURL"]][ssuser_nivel]=='1'){

        $row.='<li class="dropdown">';
        $row.='<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">'.LABEL_Admin.'</a>';
        $row.='<ul class="dropdown-menu" role="menu">';

        $row.='<li><a title="'.ucfirst(LABEL_lcConfig).'" href="admin.php?vocabulario_id=list">'.ucfirst(LABEL_lcConfig).'</a></li>';
	$row.='<li><a title="'.ucfirst(MENU_Usuarios).'" href="admin.php?user_id=list">'.ucfirst(MENU_Usuarios).'</a></li>';
	$row.='<li><a title="'.ucfirst(LABEL_export).'" href="admin.php?doAdmin=export">'.ucfirst(LABEL_export).'</a></li>';

        $row.='<li role="presentation" class="dropdown-header">'.ucfirst(LABEL_dbMantenimiento).'</li>';
	$row.='<li><a href="admin.php?doAdmin=reindex">'.ucfirst(LABEL_reIndice).'</a></li>';	

	//Enable or not SPARQL endpoint
	$row.=(CFG_ENABLE_SPARQL==1) ? '<li><a href="admin.php?doAdmin=updateEndpoint">'.ucfirst(LABEL_updateEndpoint).'</a></li>' :'';	

	$row.='<li><a href="admin.php?doAdmin=import" title="'.ucfirst(LABEL_import).'">'.ucfirst(LABEL_import).'</a></li>';
	$row.='<li><a href="admin.php?doAdmin=massiverem" title="'.ucfirst(MENU_massiverem).'">'.ucfirst(MENU_massiverem).'</a></li>';
	$row.='<li><a title="'.ucfirst(MENU_DatosTesauro).'" href="admin.php?opTbl=TRUE">'.ucfirst(LABEL_OptimizarTablas).'</a></li>';
	$row.='<li><a title="'.ucfirst(LABEL_update1_6x1_7).'" href="admin.php?doAdmin=updte1_6x1_7">'.ucfirst(LABEL_update1_6x1_7).'</a></li>';
	$row.='<li><a title="'.ucfirst(LABEL_update1_5x1_6).'" href="admin.php?doAdmin=updte1_5x1_6">'.ucfirst(LABEL_update1_5x1_6).'</a></li>';
	$row.='<li><a title="'.ucfirst(LABEL_update1_4x1_5).'" href="admin.php?doAdmin=updte1_4x1_5">'.ucfirst(LABEL_update1_4x1_5).'</a></li>';
	$row.='<li><a title="'.ucfirst(LABEL_update1_3x1_4).'" href="admin.php?doAdmin=updte1_3x1_4">'.ucfirst(LABEL_update1_3x1_4).'</a></li>';
	$row.='<li><a title="'.ucfirst(LABEL_update1_1x1_2).'" href="admin.php?doAdmin=updte1_1x1_2">'.ucfirst(LABEL_update1_1x1_2).'</a></li>';
	$row.='<li><a title="'.ucfirst(LABEL_update1x1_2).'" href="admin.php?doAdmin=updte1x1_2">'.ucfirst(LABEL_update1x1_2).'</a></li>';

	$row.='</ul></li>';

	}   

return $row;


};




#
# term menu options
#
function HTMLtermMenu($array_tema,$relacionesTermino){

$row.='<a tabindex="0" class="button" href="#menu-manager" id="hierarchybreadcrumbTermMenu">'.LABEL_Opciones.'</a>';
$row.='<div id="term-menu" class="hidden" style="display: none">';

$row.='<ul class="menumanager">';

$sqlcheckIsValidTerm=SQLcheckIsValidTerm($array_tema[tema_id]);


if(($relacionesTermino[cantNT]+$relacionesTermino[cantUF])==0)


//no have relations
if(($relacionesTermino[cantTotal])=='0')
{

	/*
	Change status term
	*/
	$link_estado.='<li><a href="#">'.ucfirst(LABEL_CambiarEstado).'</a><ul id="menu_estado">';
	switch($array_tema[estado_id])
		{
		case '12':
			//Candidato / candidate => aceptado
			$link_estado.='<li><a title="'.LABEL_AceptarTermino.'" href="index.php?tema='.$array_tema[idTema].'&amp;estado_id=13">'.ucfirst(LABEL_AceptarTermino).'</a></li>';
			//Candidato / candidate => rechazado
			$link_estado.='<li><a title="'.LABEL_RechazarTermino.'" href="index.php?tema='.$array_tema[idTema].'&amp;estado_id=14">'.ucfirst(LABEL_RechazarTermino).'</a></li>';
			break;

			case '13':
			//Aceptado / Acepted=> Rechazado
			$link_estado.='<li><a title="'.LABEL_RechazarTermino.'" href="index.php?tema='.$array_tema[idTema].'&amp;estado_id=14">'.ucfirst(LABEL_RechazarTermino).'</a></li>';
			break;

			case '14':
			//Rechazado / Rejected=> Candidato
			$link_estado.='<li><a title="'.LABEL_CandidatearTermino.'" href="index.php?tema='.$array_tema[idTema].'&amp;estado_id=12">'.ucfirst(LABEL_CandidatearTermino).'</a></li>';
			break;
			}
	$link_estado.='</ul></li>';
};


// PERMITIR O NO POLIJERARQUIAS//
if( ( ($relacionesTermino[cantTG]==0) || ($_SESSION[CFGPolijerarquia]=='1') ) && ($array_tema[estado_id]=='13')){
	   $link_subordinar='<li><a title="'.MENU_AgregarTG.'" href="index.php?taskterm=addBT&amp;tema='.$array_tema[idTema].'">'.ucfirst(MENU_AgregarTG).'</a></li>';
	};
	
	



		$row.='      <li><a title="'.MENU_EditT.'" href="index.php?taskterm=editTerm&amp;tema='.$array_tema[idTema].'">'.ucfirst(MENU_EditT).'</a></li>';	

		//permitir notas en todos los tipos de términos
		$row.='     <li><a title="'.LABEL_EditorNota.'" href="index.php?taskterm=editNote&amp;note_id=?&amp;editNota=?&amp;tema='.$array_tema[idTema].'">'.ucfirst(LABEL_EditorNota).'</a></li>';
	
		
		//solo acepta relaciones si el término esta aceptado
		if(($array_tema[estado_id]=='13') && (SQLcount($sqlcheckIsValidTerm)=='0')){
			$row.='<li><a href="#">'.ucfirst(LABEL_Agregar).'</a><ul id="menu_agregar">';
			
			//link agregar un TE
			$row.='     <li><a title="'.MENU_AgregarTE.'" href="#">'.ucfirst(MENU_AgregarTE).'</a><ul>';
			
			//elegir vía de agregacion
			$row.='     <li><a title="'.MENU_AgregarTE.'" href="index.php?taskterm=addNT&amp;tema='.$array_tema[idTema].'">'.ucfirst(MENU_AgregarT).'</a></li>';
			$row.='     <li><a title="'.MENU_AgregarTEexist.'" href="index.php?taskterm=addFreeNT&amp;tema='.$array_tema[idTema].'">'.ucfirst(MENU_AgregarTEexist).'</a></li>'; //SafetyLit					
			$row.='</ul></li>';
	
			//link agregar un UP
			$row.='     <li><a title="'.MENU_AgregarUP.'" href="#">'.ucfirst(MENU_AgregarUP).'</a><ul>';
			//elegir vía de agregacion
			$row.='     <li><a href="index.php?taskterm=addUF&amp;tema='.$array_tema[idTema].'">'.ucfirst(MENU_AgregarT).'</a></li>';
			$row.='     <li><a href="index.php?taskterm=addFreeUF&amp;tema='.$array_tema[idTema].'">'.ucfirst(MENU_AgregarUPexist).'</a></li>'; //SafetyLit	
			$row.='</ul></li>';


			//link agregar un TR
			$row.='     <li><a title="'.MENU_AgregarTR.'" href="#">'.ucfirst(MENU_AgregarTR).'</a><ul>';
			$row.='     <li><a title="'.MENU_AgregarT.'" href="index.php?taskterm=addRTnw&amp;tema='.$array_tema[idTema].'">'.ucfirst(MENU_AgregarT).'</a></li>';
			$row.='     <li><a title="'.MENU_selectExistTerm.'" href="index.php?taskterm=addRT&amp;tema='.$array_tema[idTema].'">'.ucfirst(MENU_selectExistTerm).'</a></li>';
			$row.='    </ul></li>';


			$row.='     <li><a title="'.LABEL__getForRecomendation.'" href="index.php?taskterm=findSuggestionTargetTerm&amp;tema='.$array_tema[idTema].'">'.ucfirst(LABEL__getForRecomendation).'</a></li>';


			$row.='    '.$link_subordinar;

			
			$row.='     <li><a title="'.LABEL_URI2term.'" href="index.php?taskterm=addURI&amp;tema='.$array_tema[idTema].'">'.ucfirst(LABEL_URI2term).'</a></li>';

			
			$row.='<li><a href="#">'.ucfirst(LABEL_relbetweenVocabularies).'</a><ul id="menu_agregar_relaciones">';
			//link agregar un EQ
			$row.='     <li><a title="'.LABEL_vocabulario_referencia.'" href="index.php?taskterm=addEQ&amp;tema='.$array_tema[idTema].'">'.ucfirst(LABEL_vocabulario_referencia).'</a></li>';
			//link agregar un término externo vía web services
			$row.='     <li><a title="'.LABEL_relacion_vocabularioWebService.'" href="index.php?taskterm=findTargetTerm&amp;tema='.$array_tema[idTema].'">'.ucfirst(LABEL_vocabulario_referenciaWS).'</a></li>';
			$row.='    </ul>';
			$row.='</li>';
			$row.='</ul>';
			}


		$row.=$link_estado;
		
		if($array_tema["isMetaTerm"]==1)
		{
			$label_task_meta_term=LABEL_turnOffMetaTerm;
			$task_meta_term=0;
		}
		else
		{
			$label_task_meta_term=LABEL_turnOnMetaTerm;
			$task_meta_term=1;
		}

		$row.='     <li><a title="'.$label_task_meta_term.'" href="index.php?taskterm=metaTerm&amp;mt_status='.$task_meta_term.'&amp;tema='.$array_tema[idTema].'">'.ucfirst($label_task_meta_term).'</a></li>';



		$row.='<li><a title="'.LABEL_EliminarTE.'" href="javascript:expandLink(\'borrart\')">'.ucfirst(LABEL_EliminarTE).'</a></li>';

$row.='   </ul>';		
$row.='</div>';

return $row;
};




#
# Ficha del término
#
function HTMLNotasTermino($array){
  if(count($array[notas])){
        for($iNota=0; $iNota<(count($array[notas])); ++$iNota){
                if($array[notas][$iNota][id]){
                        $body.='<div class="NA" id="'.$array[notas][$iNota][tipoNota].$array[notas][$iNota][id].'">';
                        $body.='<dl id="notas">';
                                switch($array[notas][$iNota][tipoNota]){
                                        case 'NA';
                                        $tipoNota=LABEL_NA;
                                        break;

                                        case 'NH';
                                        $tipoNota=LABEL_NH;
                                        break;

                                        case 'NC';
                                        $tipoNota=LABEL_NC;
                                        break;

                                        case 'NB';
                                        $tipoNota=LABEL_NB;
                                        break;

                                        case 'NP';
                                        $tipoNota=LABEL_NP;
                                        break;
                                        
                                }
		
		$tipoNota=(in_array($array[notas][$iNota][tipoNota_id],array(8,9,10,11,15))) ? arrayReplace(array(8,9,10,11,15),array(LABEL_NA,LABEL_NH,LABEL_NB,LABEL_NP,LABEL_NC),$array[notas][$iNota][tipoNota_id]) : $array[notas][$iNota][tipoNotaLabel];                                
		//idioma de la nota
		//Rellenar si esta vacion
		$array[notas][$iNota][lang_nota]=(!$array[notas][$iNota][lang_nota]) ? $_SESSION["CFGIdioma"] : $array[notas][$iNota][lang_nota];

		//no mostrar si es igual al idioma del vocabulario
		$label_lang_nota=($array[notas][$iNota][lang_nota]==$_SESSION["CFGIdioma"]) ? '' : ' ('.$array[notas][$iNota][lang_nota].')';

                        if($_SESSION[$_SESSION["CFGURL"]][ssuser_id]){
                        $body.='<dt> <a title="'.LABEL_EditarNota.'" href="'.$PHP_SELF.'?editNota='.$array[notas][$iNota][id].'&amp;taskterm=editNote&amp;tema='.$array[idTema].'"><img alt="'.LABEL_EditarNota.'"  src="'.T3_WEBPATH.'/images/icons/page_edit.png"/></a> <a title="'.LABEL_EditarNota.'" href="'.$PHP_SELF.'?editNota='.$array[notas][$iNota][id].'&amp;taskterm=editNote&amp;tema='.$array[idTema].'">'.$tipoNota.'</a>'.$label_lang_nota.'</dt>';
                        $body.='<dd> '.wiki2html($array[notas][$iNota][nota]);
                        $body.='<div class="footnote">'.$array["notas"][$iNota]["cuando_nota"].'</div>';
                        $body.='</dd>';
                        }else{
                        $body.='<dt>'.$tipoNota.$label_lang_nota.'</dt><dd> '.wiki2html($array[notas][$iNota][nota]).'</dd>';
                        }

                        $body.='</dl>';;
                        $body.='</div>';;
                  };//fin de if id nota
        };// fin del for

  };
  return $body;
};

#
# //// BUCLE HACIA ARRIBA
#
function sql_rel($idTema,$i){
if($i<10){// Para evitar bucle infinito en caso de error por relaciones recursivas. max= 10
      $sql=SQLbucleArriba($idTema);
                    if(SQLcount($sql)=='0'){
                        $datos=ARRAYverTerminoBasico($idTema);
                                if($i==0){
                                $enlace.='#'.$datos[titTema].' ';
                                }else{
                                $enlace.='#<a title="'.LABEL_verDetalle.$datos[titTema].'" href="'.$PHP_SELF.'?tema='.$datos[idTema].'">'.$datos[titTema].'</a>';
                                };

                    return $enlace;
                    }else{
                    return ver_txt($sql,$i);
                    };
        };
};



#
# //// PROCESAMIENTO HTML DEL BUCLE HACIA ARRIBA
#

function ver_txt($datos,$i){
                    while($lista=$datos->FetchRow()){
                          if($i==0){
                             $rows.='#'.$lista[2].'';
                             }else{
                             $rows.='#<a title="'.LABEL_verDetalle.$lista[2].'" href="index.php?tema='.$lista[1].'">'.$lista[2].'</a>';
                             }

                          if($lista[0]>0){
                             $rows.=sql_rel($lista[0],$i+1);
                             }
                          };

 return $rows;
};



#
# //// armado de un tema_id acumula los tema_ids hacia arriba
#
function bucle_arriba($indice_temas,$idTema){
      $sql=SQLbucleArriba($idTema);
      $indice_temas.=otro_bucle_arriba($sql);
   return $indice_temas;
};

function otro_bucle_arriba($sql){
         while($lista=$sql->FetchRow()){
		if($lista[0]>0){
                $indice_temas.=bucle_arriba($lista[0].'|',$lista[0]);
		}
	}

	return $indice_temas;
};
#
# //// BUCLE HACIA ABAJO
#
function evalRelacionSuperior($idTema,$i,$idTemaEvaluado){

      $sql=SQLbucleArriba($idTema);

                    if(SQLcount($sql)==0)
                    {
                    return "TRUE";
                    }
                    else
                    {
                    return evalSubordina($sql,$i,$idTemaEvaluado);
                    };
};


#
# //// PROCESAMIENTO ARRAY DEL BUCLE HACIA abajo
#
function evalSubordina($datos,$i,$idTemaEvaluado){

         while($lista=$datos->FetchRow()){
                    if(($idTemaEvaluado==$lista[1])||($idTemaEvaluado==$lista[0])){
                    return FALSE;
                    }elseif($lista[0]>1){
                    return evalRelacionSuperior($lista[0],$i+1,$idTemaEvaluado);
                    }else{
                    return TRUE;
                    }
         };
};



#
# Armado del menú de cambio de idioma
#
function doMenuLang($tema_id="0"){

   GLOBAL $idiomas_disponibles;

    $selectLang.='<form id="select-lang" method="get" action="index.php">';
	$selectLang.='<select name="setLang" id="setLang" onchange="this.form.submit();">';
   foreach ($idiomas_disponibles AS $key => $value) {
        if($value[2]==$_SESSION[$_SESSION["CFGURL"]][lang][2]){
	$selectLang.='<option value="'.$value[2].'" selected="selected">'.$value[0].'</option>';
        }else{
	$selectLang.='<option value="'.$value[2].'">'.$value[0].'</option>';
        };
    };

    $selectLang.='</select>';
    
    if((is_numeric($tema_id)) && ($tema_id>0))
    {    
		$selectLang.='<input type="hidden" name="tema" value="'.$tema_id.'" />';
	}
    $selectLang.='</form>';
    $menuLang=substr("$menuLang",1);
    return $selectLang;
};




#
# Armado de tabla de términos según meses
#
function doBrowseTermsFromDate($month,$year,$ord=""){

        GLOBAL $MONTHS;
        $sql=SQLlistTermsfromDate($month,$year,$ord);
        $rows.='<table cellpadding="0" cellspacing="0" summary="'.LABEL_auditoria.'" >';


        $rows.='<tbody>';
        while($array=$sql->FetchRow()){
                $fecha_termino=do_fecha($array[cuando]);

                $rows.='<tr>';
                $rows.='<td class="izq"><a href="index.php?tema='.$array[id_tema].'" title="'.LABEL_verDetalle.LABEL_Termino.'">'.$array[tema].'</a></td>';
                $rows.='<td>'.$fecha_termino[dia].' / '.$fecha_termino[descMes].' / '.$fecha_termino[ano].'</td>';
                if($_SESSION[$_SESSION["CFGURL"]][ssuser_nivel]=='1'){
                $rows.='<td><a href="admin.php?user_id='.$array[id_usuario].'" title="'.LABEL_DatosUser.'">'.$array[apellido].', '.$array[nombres].'</a></td>';
                }else{
                $rows.='<td>'.$array[apellido].', '.$array[nombres].'</td>';
                }
                $rows.='</tr>';
                };
                $rows.='</tbody>';

        $rows.='<thead>';
        $rows.='<tr>';
        $rows.='<th class="izq" colspan="3"><a href="sobre.php" title="'.ucfirst(LABEL_auditoria).'">'.ucfirst(LABEL_auditoria).'.</a> &middot; '.$fecha_termino[descMes].' / '.$fecha_termino[ano].': '.SQLcount($sql).' '.LABEL_Terminos.'</th>';
        $rows.='</tr>';

        $rows.='<tr>';
        $rows.='<th><a href="sobre.php?m='.$month.'&y='.$year.'&ord=T" title="'.LABEL_ordenar.' '.LABEL_Termino.'">'.ucfirst(LABEL_Termino).'</a></th>';
        $rows.='<th><a href="sobre.php?m='.$month.'&y='.$year.'&ord=F" title="'.LABEL_ordenar.' '.LABEL_Fecha.'">'.ucfirst(LABEL_Fecha).'</a></th>';
        $rows.='<th><a href="sobre.php?m='.$month.'&y='.$year.'&ord=U" title="'.LABEL_ordenar.' '.MENU_Usuarios.'">'.ucfirst(MENU_Usuarios).'</a></th>';
        $rows.='</tr>';
        $rows.='</thead>';


        $rows.='<tfoot>';
        $rows.='<tr>';
        $rows.='<td class="izq">'.ucfirst(LABEL_TotalTerminos).'</td>';
        $rows.='<td colspan="2">'.SQLcount($sql).'</td>';
        $rows.='</tr>';
        $rows.='</tfoot>';

        $rows.='</table>        ';
        return $rows;
};


#
# Armado de browse de términos
#
function doBrowseTermsByDate(){
        GLOBAL $MONTHS;
        $sql=SQLtermsByDate();
        $rows.='<table class="contenido" cellpadding="0" cellspacing="0" summary="'.ucfirst(LABEL_auditoria).'">';

        $rows.='<thead>';

        $rows.='<tr>';
        $rows.='<th colspan="3" class="titulo_tabla">'.ucfirst(LABEL_auditoria).'</th>';
        $rows.='</tr>';

        $rows.='<tr>';
        $rows.='<th>'.ucfirst(LABEL_ano).'</th>';
        $rows.='<th>'.ucfirst(LABEL_mes).'</th>';
        $rows.='<th>'.ucfirst(LABEL_Terminos).'</th>';
        $rows.='</tr>';
        $rows.='</thead>';


        $rows.='<tbody>';
        while($array=$sql->FetchRow()){

                $fecha_termino=do_fecha($array[cuando]);
                $rows.='<tr>';
                $rows.='<td class="centrado">'.$array[years].'</td>';
                $rows.='<td class="centrado"><a href="sobre.php?m='.$array[months].'&y='.$array[years].'" title="'.LABEL_verDetalle.$fecha_termino[descMes].'">'.$fecha_termino[descMes].'</a></td>';
                $rows.='<td class="centrado">'.$array["cant"].'</td>';
                $rows.='</tr>';
                $TotalTerminos+=$array["cant"];
                };
                $rows.='</tbody>';
        $rows.='<tfoot>';
        $rows.='<tr>';
        $rows.='<td colspan="2">'.ucfirst(LABEL_TotalTerminos).'</td>';
        $rows.='<td>'.$TotalTerminos.'</td>';
        $rows.='</tr>';
        $rows.='</tfoot>';

        $rows.='</table>        ';
        return $rows;
};



function HTML_URLsearch($display=Array(),$arrayTema=Array()) {

	GLOBAL $CFG; 
	$ARRAY_busquedas=$CFG[SEARCH_URL_SITES_SINTAX];

	$string_busqueda = $arrayTema[titTema];
 	$html = '<ul id="enlaces_web">' . "\n";
	foreach($display as $sitename) {
		if (in_array($sitename, $ARRAY_busquedas))
			continue;
		$site = $ARRAY_busquedas[$sitename];
		$html .= "\t<li>";
		$url = $site['url'];
		if($site['encode']=='utf8'){
			$url = str_replace('STRING_BUSQUEDA', urlencode(utf8_encode($string_busqueda)), $url);
			}else{
			$url = str_replace('STRING_BUSQUEDA', $string_busqueda, $url);
			}

		$html .= '<a href="'.$url.'" title="'.LABEL_Buscar.' '.$arrayTema[titTema].'  ('.$site[leyenda].')">';
		$html .= '<img src="'.T3_WEBPATH.'/images/'.$site[favicon].'" alt="'.LABEL_Buscar.' '.$arrayTema[titTema].'  ('.$site[leyenda].')"/>';
		$html .= '</a>';
		$html .= "</li>\n";
		}
	$html .= "</ul>\n";

return $html;
};


#
# Expande una busqueda hacia arriba == busca los términos más generales de los términos especificos devueltos en una busqueda
#
function HTMLbusquedaExpandidaTG($acumula_indice,$acumula_temas){
global $DBCFG;

$array_indice = explode("|", $acumula_indice);
$array_temas = explode("|", $acumula_temas);

$cantValores=array_count_values($array_indice);
$array_indice=array_unique($array_indice);

while (list($key, $val) = each($array_indice)) 
{
	if(!in_array($val,$array_temas))
	{
		$temas_ids.=$val.',';
	}
};


//Si no hay términos más genericos que los resultados
if(@$temas_ids)
{
	$sql=SQLlistaTema_id(substr($temas_ids,0,-1));

	//Si hay resultados
	if(SQLcount($sql)>0)
	{
		$row_result.='<dl class="narrowList navmenu"><dt>'.ucfirst(LABEL_resultados_suplementarios).' ('.SQLcount($sql).')</dt>';
   
		while($resulta_busca=$sql->FetchRow())
		{
			$ibusca=++$ibusca;
			
			if($ibusca=='15')
			{
				$row_result.='<dd id="moreTGexpandidos">[<a href="#" onclick="moreFacets(\'TGexpandidos\'); return false;">'.ucfirst(LABEL_more).'...</a>]</dd>';
				$row_result.='</dl>';
				$row_result.='<dl class="narrowList navmenu offscreen" id="narrowGroupHidden_TGexpandidos">';
				}
			if($ibusca>'15')
			{
				$row_result.='<dd><a title="'.LABEL_verDetalle.$resulta_busca[tema].'" href="index.php?tema='.$resulta_busca[tema_id].'&amp;/'.string2url($resulta_busca[tema]).'">'.$resulta_busca[tema].'</a></dd>'."\r\n" ;
			}
			else
			{
				$row_result.='<dd><a title="'.LABEL_verDetalle.$resulta_busca[tema].'" href="index.php?tema='.$resulta_busca[tema_id].'&amp;/'.string2url($resulta_busca[tema]).'">'.$resulta_busca[tema].'</a></dd>'."\r\n" ;				
			}	
			
		}
		
		$row_result.= (SQLcount($sql)>15) ? '<dd>[<a href="#bodyText" onclick="lessFacets(\'TGexpandidos\'); return true;"> '.ucfirst(LABEL_less).'...</a>]</dd>' : '';
		$row_result.='</dl>';
	};
}

return $row_result;

};



#
# Expande una busqueda hacia terminos relacionados == busca los términos relacionados de los términos especificos devueltos en una busqueda
#
function HTMLbusquedaExpandidaTR($acumula_temas){

$temas_ids=str_replace("|",",", $acumula_temas);
//Si no hay términos más genericos que los resultados
if(@$temas_ids){
$sql=SQLexpansionTR(substr($temas_ids,0,-1));

//Si hay resultados
if(SQLcount($sql)>0){
		$row_result.='<dl class="narrowList navmenu"><dt>'.ucfirst(LABEL_resultados_relacionados).' ('.SQLcount($sql).')</dt>';
   
		while($resulta_busca=$sql->FetchRow())
		{
			$ibusca=++$ibusca;
			
			if($ibusca=='15')
			{
				$row_result.='<dd id="moreTRexpandidos">[<a href="#" onclick="moreFacets(\'TRexpandidos\'); return false;">'.ucfirst(LABEL_more).'...</a>]</dd>';
				$row_result.='</dl>';
				$row_result.='<dl class="narrowList navmenu offscreen" id="narrowGroupHidden_TRexpandidos">';
				}
			if($ibusca>'15')
			{
				$row_result.='<dd><a title="'.LABEL_verDetalle.$resulta_busca[tema].'" href="index.php?tema='.$resulta_busca[tema_id].'&amp;/'.string2url($resulta_busca[tema]).'">'.$resulta_busca[tema].'</a></dd>'."\r\n" ;
			}
			else
			{
				$row_result.='<dd><a title="'.LABEL_verDetalle.$resulta_busca[tema].'" href="index.php?tema='.$resulta_busca[tema_id].'&amp;/'.string2url($resulta_busca[tema]).'">'.$resulta_busca[tema].'</a></dd>'."\r\n" ;				
			}	
			
		}
		
		$row_result.= (SQLcount($sql)>10) ? '<dd>[<a href="#bodyText" onclick="lessFacets(\'TRexpandidos\'); return true;">'.ucfirst(LABEL_less).'...</a>]</dd>' : '';
		$row_result.='</dl>';
	}
}
return $row_result;

};


function HTMLverTE($tema_id,$i_profundidad,$i=""){

GLOBAL $CFG;

$sql=SQLverTerminosE($tema_id);

$rows='<ul id="masTE'.$tema_id.'" style="display: none">';
//Contador de profundidad de TE desde la raíz
$i_profundidad=($i_profundidad==0) ? 1 : $i_profundidad;

$i_profundidad=++$i_profundidad;

//Contador de profundidad de TE desde el TE base
$i=++$i;
while($array=$sql->FetchRow()){
   if($array[id_te]){
	if($i<CFG_MAX_TREE_DEEP){
		$link_next='  <a href="javascript:expand(\''.$array[id_tema].'\')" title="'.LABEL_verDetalle.' '.$array[tema].' ('.TE_termino.')" ><span id ="expandTE'.$array[id_tema].'">&#x25ba;</span><span id ="contraeTE'.$array[id_tema].'" style="display: none">&#x25bc;</span></a> ';
		$link_next.=HTMLverTE($array[id_tema],$i_profundidad,$i);
		}else{
		$link_next='&nbsp; <a title="'.LABEL_verDetalle.TE_termino.' '.$array[tema].'" href="index.php?tema='.$array[id_tema].'">&#x25ba;</a>';
		}
     }else{
     $link_next='';
     };
	
	$css_class_MT=($array["isMetaTerm"]==1) ? ' class="metaTerm" ' : '';
	$label_MT=($array["isMetaTerm"]==1) ? NOTE_isMetaTerm : '';

	$rows.='<li><acronym class="thesacronym" title="'.TE_termino.'" lang="'.LANG.'">'.TE_acronimo.$i_profundidad.'</acronym> ' ;
	$rows.=' <a '.$css_class_MT.' title="'.LABEL_verDetalle.' '.$array[tema].' ('.TE_termino.') '.$label_MT.'"  href="index.php?tema='.$array[id_tema].'&amp;/'.string2url($array[tema]).'">'.$array[tema].'</a>'.$link_next.'</li>';
	};
$rows.='</ul>';

return $rows;
}


function HTMLlistaTerminosEstado($estado_id,$limite="")
{

//Estados posibles y aceptados
$arrayEstados_id=array(12,14);

//Descripcion de estados
$arrayEstados=array("12"=>LABEL_Candidatos,"13"=>LABEL_Aceptados,"14"=>LABEL_Rechazados);

if(in_array($estado_id,$arrayEstados_id)){

	$sql=SQLterminosEstado($estado_id,$limite);

	$rows.='<div><h1>'.ucfirst($arrayEstados[$estado_id]).' ('.SQLcount($sql).') </h1>';
	$rows.='<ul>';
	while($array = $sql->FetchRow()){
			$rows.='<li> <a class="estado_termino'.$array[estado_id].'" title="'.$array[tema].'" href="index.php?tema='.$array[tema_id].'&tipo=E">'.$array[tema].'</a><li/>';
			}
	$rows.='</ul>';
	$rows.='</div>';
	}

return $rows;
};



function HTMLsugerirTermino($texto,$acumula_temas="0"){

$sqlSimilar=SQLsimiliar($texto,$acumula_temas);

if(SQLcount($sqlSimilar)>0)
{
	while($arraySimilar=$sqlSimilar->FetchRow()){
		$listaCandidatos.= $arraySimilar[tema].'|';
		}

		$listaCandidatos=explode("|",$listaCandidatos);
		$similar = new Qi_Util_Similar($listaCandidatos, $texto);
		$sugerencia= $similar->sugestao();

		$evalSimilar=evalSimiliarResults($texto, $sugerencia);

		if ($sugerencia && ($evalSimilar)) 
		{
		$rows.='<h4>'.ucfirst(LABEL_TERMINO_SUGERIDO).' <em><strong><a href="index.php?'.FORM_LABEL_buscar.'='.$sugerencia.'&amp;sgs=off" title="'.LABEL_verDetalle.$sugerencia.'">'.$sugerencia.'</a></strong></em> </h4>';			
		}
}
return $rows;
}


/*
Display top terms
*/
function HTMLtopTerms($letra=""){

GLOBAL $CFG;

$_TOP_TERMS_BROWSER=(in_array($CFG["_TOP_TERMS_BROWSER"], array(1,0))) ? $CFG["_TOP_TERMS_BROWSER"] : 0;

$rows.='<div id="bodyText">';
$rows.='<div class="row">';
$rows.='<div class="col-md-8">';

if($_TOP_TERMS_BROWSER==1)
{

	//Top terms
	$sql=SQLverTopTerm();

	while ($array = $sql->FetchRow()){

		$rows.='<h2 class="TThtml">';
		//Editor de código
		if(($_SESSION[$_SESSION["CFGURL"]][ssuser_id]) && ($CFG["_USE_CODE"]=='1'))
		{
			$rows.='<div title="term code, click to edit" class="editable_textarea" id="code_tema'.$array[tema_id].'">'.$array[code].'</div>';
		}
		elseif($CFG["_SHOW_CODE"]=='1') 
		{
			$rows.=' '.$array[code].' ';
		}

		$css_class_MT=($array["isMetaTerm"]==1) ? ' class="metaTerm" ' : '';

		$rows.='<a '.$css_class_MT.' title="'.LABEL_verDetalle.$array[tema].'" href="index.php?tema='.$array[tema_id].'">'.$array[tema].'</a>';
		$rows.='</h2>' ;
		
		};


}
else{

	$rows.='<div id="treeTerm" data-url="suggest.php?node=TT"></div>';


}
$rows.='</div>';
$rows.='<div class="col-md-4">';
$rows.='<div class="panel panel-default">';
$rows.='<div class="panel-heading">Navegar por ordem alfabética</div>';
$rows.='<div class="panel-body">';
$rows.=HTMLlistaAlfabeticaUnica($letra);
$rows.='</div></div></div>';


$rows.='</div></div>';

return $rows;
}


function HTMLlistaAlfabeticaUnica($letra=""){


$sqlMenuAlfabetico=SQLlistaABC($letra);


if(SQLcount($sqlMenuAlfabetico)>0)
{

	while ($datosAlfabetico = $sqlMenuAlfabetico->FetchRow()) 
	{
		$datosAlfabetico[0]=isValidLetter($datosAlfabetico[0]);

		//is a valid letter
		if(strlen($datosAlfabetico[0])>0)
		{

			if(!ctype_digit($datosAlfabetico[0]))
			{
			$classMenu = ($datosAlfabetico[1]==0)  ? '' : ' class="selected" ';
			$menuAlfabetico.='<span class="badge"><a '.$classMenu.' title="'.LABEL_verTerminosLetra.' '.$datosAlfabetico[0].'" href="'.$PHP_SELF.'?letra='.$datosAlfabetico[0].'">'.$datosAlfabetico[0].'</a></span>';        
			}
			else
			{
			$menuNoAlfabetico='<span class="badge"><a title="'.LABEL_verTerminosLetra.' '.$datosAlfabetico[0].'" href="'.$PHP_SELF.'?letra='.$datosAlfabetico[0].'">0-9</a></span>';        
			}

		}
		
	};//fin del while
}

$menuAlfabetico='<div>'.$menuNoAlfabetico.$menuAlfabetico.'</div>';

return $menuAlfabetico;
};



/*
All terms form one char
*/
function HTMLterminosLetra($letra) 
{


$cantLetra=numTerms2Letter($letra);

$letra_label= (!ctype_digit($letra)) ?  $letra : '0-9';

//If there are no result => may be there are ilegal input
if($cantLetra>0) 
{
	$terminosLetra.='<div id="breadScrumb" class="letters"><ol><li><a title="'.MENU_Inicio.'" href="index.php">'.ucfirst(MENU_Inicio).'</a></li><li><em>'.$letra_label.'</em>: <strong>'.$cantLetra.' </strong>'.LABEL_Terminos.'</li></ol></div>';	
} 
else
{
	$terminosLetra.='<div id="breadScrumb" class="letters"><ol><li><a title="'.MENU_Inicio.'" href="index.php">'.ucfirst(MENU_Inicio).'</a></li><li> <strong>'.$cantLetra.' </strong>'.LABEL_Terminos.'</li></ol></div>';
}


$paginado_letras='';

$pag= secure_data($_GET["p"]);

if($cantLetra>0)
{

	if($cantLetra>CFG_NUM_SHOW_TERMSxSTATUS)
	{
		
		
		$paginado_letras=paginate_links( array(
		'type' => 'list',
		'show_all' => (($cantLetra/CFG_NUM_SHOW_TERMSxSTATUS)<15) ? true : false,
		'base' => 'index.php?letra='.$letra.'%_%',
		'format' => '&amp;p=%#%',
		'current' => max( 1, $pag),
		'total' => $cantLetra/CFG_NUM_SHOW_TERMSxSTATUS
			) 
		);
	};

$limit=CFG_NUM_SHOW_TERMSxSTATUS;


$min= ($pag-1)*$limit;

$sqlDatosLetra=SQLmenuABCpages($letra,array("min"=>$min,"limit"=>$limit));


//~ Pagination before terms
//~ $terminosLetra.=$paginado_letras;

$start_ol=($min>0) ? $min+1 :1;

$terminosLetra.='<div id="listaLetras"><ol start="'.$start_ol.'">';
while ($datosLetra= $sqlDatosLetra->FetchRow()){

//Si no es un término preferido
	if($datosLetra[termino_preferido]){
		switch($datosLetra[t_relacion]){
			//UF
			case '4':
			$leyendaConector=USE_termino;
			break;
			//Tipo relacion término equivalente parcialmente
			case '5':
			$leyendaConector='<acronym title="'.LABEL_termino_parcial_equivalente.'" lang="'.LANG.'">'.EQP_acronimo.'</acronym>';
			break;
			//Tipo relacion término equivalente
			case '6':
			$leyendaConector='<acronym title="'.LABEL_termino_equivalente.'" lang="'.LANG.'">'.EQ_acronimo.'</acronym>';
			break;
			//Tipo relacion término no equivalente
			case '7':
			$leyendaConector='<acronym title="'.LABEL_termino_no_equivalente.'" lang="'.LANG.'">'.NEQ_acronimo.'</acronym>';
			break;
			//Tipo relacion término equivalente inexacta
			case '8':
			$leyendaConector='<acronym title="'.LABEL_termino_parcial_equivalente.'" lang="'.LANG.'">'.EQP_acronimo.'</acronym>';
			break;
			}

		$terminosLetra.='<li><em><a title="'.LABEL_verDetalle.xmlentities($datosLetra[tema]).'" href="index.php?tema='.$datosLetra[tema_id].'&amp;/'.string2url($datosLetra[tema]).'">'.$datosLetra[tema].'</a></em> '.$leyendaConector.' <a title="'.LABEL_verDetalle.$datosLetra[tema].'" href="index.php?tema='.$datosLetra[id_definitivo].'&amp;/'.($datosLetra[termino_preferido]).'">'.$datosLetra[termino_preferido].'</a></li>'."\r\n" ;
	}
	else
	{
		$styleClassLink= ($datosLetra[estado_id]!=='13') ? 'class="estado_termino'.$datosLetra[estado_id].'"' : '';

		$terminosLetra.='<li><a '.$styleClassLink.' title="'.LABEL_verDetalle.xmlentities($datosLetra[tema]).'" href="index.php?tema='.$datosLetra[id_definitivo].'&amp;/'.string2url($datosLetra[tema]).'">'.xmlentities($datosLetra[tema]).'</a></li>'."\r\n" ;
	}
};
		$terminosLetra.='   </ol>';
		$terminosLetra.='</div>';
};


	$terminosLetra.='<div class="clear">'.$paginado_letras.'</div>';

	return $terminosLetra;
}



#
# Armado de resultados de búsqueda avanzada
#
function HTMLadvancedSearchResult($array){

//Ctrol lenght string
$array[xstring]=trim($array[xstring]);

if(strlen(trim($array[xstring]))>=CFG_MIN_SEARCH_SIZE)
{
	$sql= SQLadvancedSearch($array);
	
	
	$sql_cant=SQLcount($sql);
	
	$classMensaje= ($sql_cant>0) ? 'information' : 'warning';
	
	$resumeResult = '<p id="adsearch" class="'.$classMensaje.'"><strong>'.$sql_cant.'</strong> '.MSG_ResultBusca.' <strong> "<em>'.stripslashes($array[xstring]).'</em>"</strong></p>';
} 
else
{
	$sql_cant='0';	
	$resumeResult = '<p id="adsearch" class="error">'.sprintf(MSG_minCharSerarch,stripslashes($array[xstring]),strlen($array[xstring]),CFG_MIN_SEARCH_SIZE-1).'</p>';
}

 $body.=$resumeResult;

if($sql_cant>0)
 {
	 $row_result.='<div id="listaBusca"><ul>';

	 while($resulta_busca=$sql->FetchRow()){

		$ibusca=++$ibusca;
		$css_class_MT=($resulta_busca["isMetaTerm"]==1) ? ' class="metaTerm" ' : '';

		//Si no es un término preferido
		if($resulta_busca[uf_tema_id])
		{
				switch($resulta_busca[t_relacion])
				{
				case '4':					//UF
				$leyendaConector=USE_termino;
				break;
	
				case '5'://Tipo relacion término equivalente parcialmente
				$leyendaConector='<acronym title="'.LABEL_termino_parcial_equivalente.'" lang="'.LANG.'">'.EQP_acronimo.'</acronym>';
				break;
	
				case '6'://Tipo relacion término equivalente
				$leyendaConector='<acronym title="'.LABEL_termino_equivalente.'" lang="'.LANG.'">'.EQ_acronimo.'</acronym>';
				break;
	
				case '7'://Tipo relacion término no equivalente
				$leyendaConector='<acronym title="'.LABEL_termino_no_equivalente.'" lang="'.LANG.'">'.NEQ_acronimo.'</acronym>';
				break;
	
				case '8'://Tipo relacion término equivalente inexacta
				$leyendaConector='<acronym title="'.LABEL_termino_parcial_equivalente.'" lang="'.LANG.'">'.EQP_acronimo.'</acronym>';
				break;
				}

			$row_result.='<li><em><a title="'.LABEL_verDetalle.$resulta_busca[tema].'" href="index.php?tema='.$resulta_busca[uf_tema_id].'&amp;/'.string2url($resulta_busca[uf_tema]).'">'.$resulta_busca[uf_tema].'</a></em> '.$leyendaConector.' <a title="'.LABEL_verDetalle.$resulta_busca[tema].'" href="index.php?tema='.$resulta_busca[tema_id].'">'.$resulta_busca[tema].'</a> </li>'."\r\n" ;
		}
		else // es un término preferido
		{
			$row_result.='<li><a '.$css_class_MT.' title="'.LABEL_verDetalle.$resulta_busca[tema].'" href="index.php?tema='.$resulta_busca[tema_id].'&amp;/'.string2url($resulta_busca[tema]).'">'.$resulta_busca[tema].'</a></li>'."\r\n" ;
		}

	};//fin del while
	$row_result.='</ul>';
	$row_result.='</div>';


};// fin de if result


return $body.$row_result;
};


/*
Show terms from target vocabularies
*/
function HTMLtargetTerms($tema_id)
{
	$sql=SQLtargetTerms($tema_id);
	
	if (SQLcount($sql)>0) 
	{
		$rows='<ul>';
		
	 while ($array=$sql->FetchRow())
		{
			if ($_SESSION[$_SESSION["CFGURL"]][ssuser_id]) 
			{
				$delLink= '<a id="elimina_'.$array[tterm_id].'" title="'.LABEL_borraRelacion.'"  class="eliminar" href="'.$PHP_SELF.'?tterm_id='.$array[tterm_id].'&amp;tema='.$tema_id.'&amp;tvocab_id='.$array[tvocab_id].'&amp;taskrelations=delTgetTerm" onclick="return askData();"><acronym title="'.LABEL_borraRelacion.'" lang="'.LANG.'">x</acronym></a>'; 
				$checkLink= '<a id="actua_'.$array[tterm_id].'" title="'.LABEL_ShowTargetTermforUpdate.'"  class="button" href="'.$PHP_SELF.'?tterm_id='.$array[tterm_id].'&amp;tema='.$tema_id.'&amp;tvocab_id='.$array[tvocab_id].'&amp;tterm_id='.$array[tterm_id].'&amp;taskEdit=checkDateTermsTargetVocabulary">'.LABEL_ShowTargetTermforUpdate.'</a>'; 
				
				$ttermManageLink=' '.$delLink.' '.$checkLink.'  ';
			
			}
			
			

			$rows.='<li>'.$ttermManageLink.' '.FixEncoding(ucfirst($array[tvocab_label])).' <a href="'.$array[tterm_url].'" title="'.FixEncoding($array[tterm_string]).'">'.FixEncoding($array[tterm_string]).'</a>';
			$rows.=(($_GET[taskEdit]=='checkDateTermsTargetVocabulary') && ($_GET[tterm_id]==$array[tterm_id]) && ($_SESSION[$_SESSION["CFGURL"]][ssuser_nivel])) ? HTMLcheckTargetTerm($array) : '';
			$rows.='</li>';

		}
		$rows.='</ul>';
	}

return $rows;	
}

/*
Show URIs associated to term
*/
function HTMLURI4term($tema_id)
{
	$sql=SQLURIxterm($tema_id);
	
	if (SQLcount($sql)>0) 
	{
		$rows='<ul>';
		
	 while ($array=$sql->FetchRow())
		{
			if ($_SESSION[$_SESSION["CFGURL"]][ssuser_id]) 
			{
				$delLink= '<a id="elimina_'.$array[uri_id].'" title="'.LABEL_borraRelacion.'"  class="eliminar" href="'.$PHP_SELF.'?uri_id='.$array[uri_id].'&amp;tema='.$tema_id.'&amp;taskrelations=delURIterm" onclick="return askData();"><acronym title="'.LABEL_borraRelacion.'" lang="'.LANG.'">x</acronym></a>'; 							
			}						
			
			$rows.='<li>'.$delLink.' '.ucfirst($array[uri_value]).' <a href="'.$array[uri].'" title="'.ucfirst($array[uri_value]).'">'.$array[uri].'</a>';
			$rows.='</li>';
		}
		$rows.='</ul>';
	}

return $rows;	
}


/*
check changes in one foreing term
*/
function HTMLcheckTargetTerm($array) 
{

$dataSimpleChkUpdateTterm=dataSimpleChkUpdateTterm("tematres",$array["tterm_uri"]);

if($dataSimpleChkUpdateTterm->result->term->term_id)
{
	$tterm_string=(string) $dataSimpleChkUpdateTterm->result->term->string;
	$tterm_id= (int) $dataSimpleChkUpdateTterm->result->term->term_id;
	$tterm_date=$dataSimpleChkUpdateTterm->result->term->date_mod;
}

$last_term_update=($array["cuando_last"]) ? $array["cuando_last"] : $array["cuando"];
	
/*
	El término no existe más en el vocabulario de destino
*/
	if($tterm_id<1)
	{
		$rows.= '<ul class="errorNoImage">';
		$rows.= '<li><strong>'.ucfirst(LABEL_notFound).'</strong></li>';
		$rows.= '<li><a href="index.php?tvocab_id='.$array["tvocab_id"].'&amp;tterm_id='.$array["tterm_id"].'&amp;tema='.$array["tema_id"].'&amp;taskrelations=delTgetTerm" class="eliminar" title="'.ucfirst(LABEL_borraRelacion).'">'.ucfirst(LABEL_borraRelacion).'</a></li>';
		$rows.= '</ul>';
	}
/*
	hay actualizacion del término
*/
	elseif ($tterm_date > $last_term_update) 
	{
		$ARRAYupdateTterm["$array[tterm_uri]"]["string"]=FixEncoding($tterm_string);	
		$ARRAYupdateTterm["$array[tterm_uri]"]["date_mod"]=$tterm_date;
					
		$rows.= '<ul class="warningNoImage">';
		$rows.= '<li><strong>'.$tterm_string.'</strong></li>';
		$rows.= '<li>'.$tterm_date.'</li>';
		$rows.= '<li><a href="index.php?tvocab_id='.$array["tvocab_id"].'&amp;tterm_id='.$array["tterm_id"].'&amp;tgetTerm_id='.$array["tterm_id"].'&amp;tema='.$array["tema_id"].'&amp;taskrelations=updTgetTerm" class="button" title="'.ucfirst(LABEL_actualizar).'">'.ucfirst(LABEL_actualizar).'</a></li>';
		$rows.= '<li><a href="index.php?tvocab_id='.$array["tvocab_id"].'&amp;tterm_id='.$array["tterm_id"].'&amp;tema='.$array["tema_id"].'&amp;taskrelations=delTgetTerm" title="'.ucfirst(LABEL_borraRelacion).'" class="eliminar">'.ucfirst(LABEL_borraRelacion).'</a></li>';
		$rows.= '</ul>';
	}
	else
	{
		//$array["tema_id"]["status_tterm"]= true;
		$rows='<span class="successNoImage">'.LABEL_termUpdated.'</span>';
	}	

return $rows;
}



/**
 * Retrieve paginated link for archive post pages.
 *
 * Technically, the function can be used to create paginated link list for any
 * area. The 'base' argument is used to reference the url, which will be used to
 * create the paginated links. The 'format' argument is then used for replacing
 * the page number. It is however, most likely and by default, to be used on the
 * archive post pages.
 *
 * The 'type' argument controls format of the returned value. The default is
 * 'plain', which is just a string with the links separated by a newline
 * character. The other possible values are either 'array' or 'list'. The
 * 'array' value will return an array of the paginated link list to offer full
 * control of display. The 'list' value will place all of the paginated links in
 * an unordered HTML list.
 *
 * The 'total' argument is the total amount of pages and is an integer. The
 * 'current' argument is the current page number and is also an integer.
 *
 * An example of the 'base' argument is "http://example.com/all_posts.php%_%"
 * and the '%_%' is required. The '%_%' will be replaced by the contents of in
 * the 'format' argument. An example for the 'format' argument is "?page=%#%"
 * and the '%#%' is also required. The '%#%' will be replaced with the page
 * number.
 *
 * You can include the previous and next links in the list by setting the
 * 'prev_next' argument to true, which it is by default. You can set the
 * previous text, by using the 'prev_text' argument. You can set the next text
 * by setting the 'next_text' argument.
 *
 * If the 'show_all' argument is set to true, then it will show all of the pages
 * instead of a short list of the pages near the current page. By default, the
 * 'show_all' is set to false and controlled by the 'end_size' and 'mid_size'
 * arguments. The 'end_size' argument is how many numbers on either the start
 * and the end list edges, by default is 1. The 'mid_size' argument is how many
 * numbers to either side of current page, but not including current page.
 *
 * It is possible to add query vars to the link by using the 'add_args' argument
 * and see {@link add_query_arg()} for more information.
 *
 * @since 2.1.0
 *
 * @param string|array $args Optional. Override defaults.
 * @return array|string String of page links or array of page links.
 * http://codex.wordpress.org/Function_Reference/paginate_links
 */
function paginate_links( $args = '' ) {
	$defaults = array(
		'base' => '%_%', // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
		'format' => '?letra=%#%', // ?page=%#% : %#% is replaced by the page number
		'total' => 1,
		'current' => 0,
		'show_all' => false,
		'prev_next' => true,
		'prev_text' => ('&laquo; '.ucfirst(LABEL_Prev)),
		'next_text' => (ucfirst(LABEL_Next).' &raquo;'),
		'end_size' => 1,
		'mid_size' => 2,
		'type' => 'plain',
		'add_args' => false, // array of query args to add
		'add_fragment' => ''
	);

	$args = t3_parse_args( $args, $defaults );
	extract($args, EXTR_SKIP);

	// Who knows what else people pass in $args	
	$total = (int) $total;
	if ( $total < 1 )
		return ;
	$current  = (int) $current;
	$end_size = 0  < (int) $end_size ? (int) $end_size : 1; // Out of bounds?  Make it the default.
	$mid_size = 0 <= (int) $mid_size ? (int) $mid_size : 2;
	$r = '';
	$page_links = array();
	$n = 0;
	$dots = false;

	if ( $prev_next && $current && 1 < $current ) :
		$link = str_replace('%_%', 2 == $current ? '' : $format, $base);
		$link = str_replace('%#%', $current - 1, $link);
		$link .= $add_fragment;
		$page_links[] = '<a class="previous-off" href="'.$link.'" title="' . $prev_text . '">' . $prev_text . '</a>';
	endif;
	for ( $n = 1; $n <= $total+1; $n++ ) :
		$n_display = $n;
		if ( $n == $current ) :
			$page_links[] = "<span class='page-numbers active'>$n_display</span>";
			$dots = true;
		else :
			if ( $show_all || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
				$link = str_replace('%_%', 1 == $n ? '' : $format, $base);
				$link = str_replace('%#%', $n, $link);
				$link .= $add_fragment;
				$page_links[] = '<a class="page-numbers" href="'.$link.'" title="'.LABEL_PageNum.' '.$n_display.'">'.$n_display.'</a>';
				$dots = true;
			elseif ( $dots && !$show_all ) :
				$page_links[] = '<span class="page-numbers dots">&hellip;</span>';
				$dots = false;
			endif;
		endif;
	endfor;
	
	if ( $prev_next && $current && ( $current <= $total || -1 == $total ) ) :
		$link = str_replace('%_%', $format, $base);
		$link = str_replace('%#%', $current + 1, $link);
		$link .= $add_fragment;
		$page_links[] = '<a class="next-off" href="'.$link.'" title="' . $next_text . '">' . $next_text . '</a>';
	endif;
	switch ( $type ) :
		case 'array' :
			return $page_links;
			break;
		case 'list' :
			$r .= "<ul class='pagination-clean'>\n\t<li>";
			$r .= join("</li>\n\t<li>", $page_links);
			$r .= "</li>\n</ul>\n";
			break;
		default :
			$r = join("\n", $page_links);
			break;
	endswitch;
	
	return $r;
}

/**
 * Retorna los datos, acorde al formato de autocompleter
 */
function getData4Autocompleter($searchq,$type=1)
{
	$sql=($type==1) ? SQLstartWith($searchq) : SQLbuscaTerminosSimple($searchq,"15");		

	$arrayResponse=array("query"=>$searchq,
						 "suggestions"=>array(),
						 "data"=>array());
	
	while($array=$sql->FetchRow())
	{
		array_push($arrayResponse["suggestions"], $array["tema"]);
		array_push($arrayResponse["data"], $array["tema_id"]);
	}				

    return json_encode($arrayResponse);
};

/*
Retorna los datos, acorde al formato de jtree
 */
function getData4jtree($term_id=0)
{
	
	GLOBAL $CFG;
	if(is_numeric($term_id))
		{	
			# display narrower terms
			$sql=SQLverTerminosE($term_id);
		}
		elseif ($term_id=='TT') {
			# display top terms
			$sql=SQLverTopTerm();		
		}
		else
		{
			return;
		}

	$arrayResponse=array();

	while($array=$sql->FetchRow())
	{			
		//there are NT?
		$load_on_demand=($array["id_te"]==0) ? false : true;

		//is top terms
		$load_on_demand=($term_id==0) ? true : $load_on_demand;

	if(($_SESSION[$_SESSION["CFGURL"]][ssuser_id]) && ($CFG["_USE_CODE"]=='1'))
	{
		$pre_link=' '.$array["code"].' ';
	}
	elseif($CFG["_SHOW_CODE"]=='1') 
	{
		$pre_link=' '.$array["code"].' ';
	}
	else
	{
		$pre_link='	';
	}

	$css_class_MT=($array["isMetaTerm"]==1) ? ' class="metaTerm" ' : '';

	$link='<h2 class="TT">'.$pre_link.'<a '.$css_class_MT.' title="'.LABEL_verDetalle.$array[tema].'" href="index.php?tema='.$array[tema_id].'">'.$array[tema].'</a></h2>';

		array_push($arrayResponse, array("label"=>"$link",
                  "id"=>"$array[tema_id]",
                  "load_on_demand"=>$load_on_demand));
	}
    return json_encode($arrayResponse);
};

#######################################################################
?>
