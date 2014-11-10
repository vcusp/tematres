<?php
if ((stristr( $_SERVER['REQUEST_URI'], "session.php") ) || ( !defined('T3_ABSPATH') )) die("no access");
#   TemaTres : aplicación para la gestión de lenguajes documentales #       #
#                                                                        #
#   Copyright (C) 2004-2008 Diego Ferreyra tematres@r020.com.ar
#   Distribuido bajo Licencia GNU Public License, versión 2 (de junio de 1.991) Free Software Foundation
#  
###############################################################################################################
# Formulario de alta de notas #
#

 //SEND_KEY to prevent duplicated
  session_start();
  $_SESSION['SEND_KEY']=md5(uniqid(rand(), true));
	
  $ARRAYTermino=ARRAYverTerminoBasico($_GET[tema]);
  $hidden='<input type="hidden"  name="idTema" value="'.$ARRAYTermino[idTema].'" />';
  $hidden.='<input type="hidden"  name="ks" id="ks" value="'.$_SESSION["SEND_KEY"].'"/>';

  if($editNota){
    $arrayNota=ARRAYdatosNota($editNota);
    
    if($arrayNota[idNota]){//Edicion
		$hidden.='<input type="hidden" name="idNota" value="'.$arrayNota[idNota].'" />';
		$hidden.='<input type="hidden" name="modNota" value="1" />';

		$hidden.='  <input type="button"  class="submit ui-corner-all"  name="cancelar" type="button" onClick="location.href=\'index.php?tema='.$ARRAYTermino[idTema].'\'" value="'.ucfirst(LABEL_Cancelar).'"/>';
		$hidden.=' <input type="submit"  class="submit ui-corner-all"  name="eliminarNota" value="'.LABEL_EliminarNota.'"/>';
		$hidden.='<input type="submit"  class="submit ui-corner-all"  name="guardarCambioNota" value="'.LABEL_Cambiar.'"/>';

    }else{
		$hidden.='<input type="hidden" name="altaNota" value="1" />';
		$hidden.='  <input type="button"  name="cancelar" type="button" onClick="location.href=\'index.php?tema='.$ARRAYTermino[idTema].'\'" value="'.ucfirst(LABEL_Cancelar).'"/>';
		$hidden.='<input type="submit"  name="boton" value="'.LABEL_Enviar.'"/>';

    }
  };

  $LabelNB='NB#'.LABEL_NB;
  $LabelNH='NH#'.LABEL_NH;
  $LabelNA='NA#'.LABEL_NA;
  $LabelNP='NP#'.LABEL_NP;
  $LabelNC='NC#'.LABEL_NC;

  $sqlNoteType=SQLcantNotas();
  $arrayNoteType=array();
  
  while ($array=$sqlNoteType->FetchRow()){ 
  		 $varNoteType=(in_array($array["value_id"],array(8,9,10,11,15))) ? arrayReplace(array(8,9,10,11,15),array($LabelNA,$LabelNH,$LabelNB,$LabelNP,$LabelNC),$array["value_id"]) : $array["value_code"].'#'.$array["value"];
    	 array_push($arrayNoteType, $varNoteType);	 
  };

// Preparado de datos para el formulario ///
$arrayLang=array();
foreach ($CFG["ISO639-1"] as $langs) {
	array_push($arrayLang,"$langs[0]#$langs[1]");
	};
//idioma de la nota
$arrayNota[lang_nota] = (!$arrayNota[lang_nota]) ? $_SESSION["CFGIdioma"] : $arrayNota[lang_nota];

  ?>
<div id="bodyText">
<a class="topOfPage" href="index.php?tema=<?php echo $ARRAYTermino[idTema];?>" title="<?php echo LABEL_Anterior;?>"><?php echo LABEL_Anterior;?></a>
<h1><?php echo LABEL_EditorNota ;?></h1>
          <form class="myform" name="altaNota" id="altaNota" action="index.php" method="post">
		  <fieldset>
			<legend>  <?php echo LABEL_EditorNotaTermino.' <a href="index.php?tema='.$ARRAYTermino[idTema].'">'.$ARRAYTermino[titTema].'</a>';?>  </legend>

                       <div>
                        <label for="<?php echo LABEL_tipoNota;?>" accesskey="t">
                        <?php echo ucfirst(LABEL_tipoNota);?></label>
                          <select id="tipoNota" name="<?php echo FORM_LABEL_tipoNota;?>">
                          <optgroup label="<?php echo LABEL_tipoNota;?>">
                           <?php
                           echo doSelectForm($arrayNoteType,$arrayNota[tipo_nota]);
                           ?>
                           </optgroup>
                           </select>
                          </div>
			<div>
			<label for="<?php echo FORM_LABEL_Idioma;?>" accesskey="l"><?php echo ucfirst(LABEL_Idioma);?></label>
				<select id="<?php echo FORM_LABEL_Idioma;?>" name="<?php echo FORM_LABEL_Idioma;?>">
				<optgroup label="<?php echo LABEL_Idioma;?>">
			<?php
			echo doSelectForm($arrayLang,$arrayNota[lang_nota]);
				?>
				</optgroup>
				</select>
			</div>
           <div>
           <label for="<?php echo LABEL_nota;?>" accesskey="n"><?php echo ucfirst(LABEL_nota);?></label> 
			<textarea style="width: 80%" cols="60" name="<?php echo FORM_LABEL_nota;?>" rows="15" id="<?php echo LABEL_nota;?>"><?php echo $arrayNota[nota];?></textarea>
			</div>
			<div class="submit_form" align="center">
			<br/>
			<?php echo $hidden;?>
			</div>
  </fieldset>
</form>
</div>
