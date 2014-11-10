<?php
if ((stristr( $_SERVER['REQUEST_URI'], "session.php") ) || ( !defined('T3_ABSPATH') )) die("no access");
#   TemaTres : aplicación para la gestión de lenguajes documentales #       #
#                                                                        #
#   Copyright (C) 2004-2008 Diego Ferreyra tematres@r020.com.ar
#   Distribuido bajo Licencia GNU Public License, versión 2 (de junio de 1.991) Free Software Foundation
#  
###############################################################################################################
#

// Preparado de datos para el formulario ///
$arrayLang=array();
foreach ($CFG["ISO639-1"] as $langs) {
	array_push($arrayLang,"$langs[0]#$langs[1]");
	};
$si=LABEL_SI;
$no=LABEL_NO;

if($_GET[vocabulario_id]>0){
	$sql_vocabulario=SQLdatosVocabulario($_GET[vocabulario_id]);
	$array_vocabulario=$sql_vocabulario->FetchRow();
	$fecha_crea=do_fecha($array_vocabulario[cuando]);
	}else{
	$array_vocabulario[vocabulario_id]='NEW';
	}

if($array_vocabulario[vocabulario_id]==1){
	$titulo_formulario=LABEL_vocabulario_principal;
	
	$ARRAYfetchValues=ARRAYfetchValues('METADATA');
	}else{
	$titulo_formulario=LABEL_vocabulario_referencia;
	}

$array_ano=do_intervalDate("1998",date(Y),FORM_LABEL_FechaAno);
$array_dia=do_intervalDate("1","31",FORM_LABEL_FechaDia);
$array_mes=do_intervalDate("1","12",FORM_LABEL_FechaMes);

?>
	

<script id="demo" type="text/javascript">
$("#config-vocab").validate({
});

</script>	

<h1><?php echo ucfirst(LABEL_lcConfig).' '.$titulo_formulario;?></h1>

    <form id="config-vocab" name="abm_config" action="<?php echo $PHP_SELF;?>"  method="post">
<fieldset class="myform">
  <legend><?php echo ucfirst(LABEL_lcConfig).' '.$titulo_formulario;?></legend>

	<div>
        <label for="titulo" accesskey="t"><?php echo ucfirst(LABEL_Titulo);?></label>
        <input id="titulo"
    name="<?php echo FORM_LABEL_Titulo;?>"   size="40"
    maxlength="150"
    value="<?php echo $array_vocabulario[titulo];?>"
    
    />
    </div>
	<div>
       <label for="autor" accesskey="a"><?php echo ucfirst(LABEL_Autor);?></label>
        <input id="autor"
    name="<?php echo FORM_LABEL_Autor;?>"
    size="50"
    maxlength="250"
    value="<?php echo $array_vocabulario[autor];?>"
    />
</div>
<div>
       <label for="contributor" accesskey="c"><?php echo ucfirst(LABEL_Contributor);?></label>
        <input id="contributor"
    name="dccontributor"
    size="50"
    maxlength="250"
    
    value="<?php echo $ARRAYfetchValues["dc:contributor"]["value"];?>"
    />
</div>

<div>
       <label for="rights" accesskey="c"><?php echo ucfirst(LABEL_Rights);?></label>
        <input id="rights"
    name="dcrights"
    size="50"
    maxlength="250"
    value="<?php echo $ARRAYfetchValues["dc:rights"]["value"];?>"
    />
</div>

<div>
       <label for="publisher" accesskey="c"><?php echo ucfirst(LABEL_Publisher);?></label>
        <input id="publisher"
    name="dcpublisher"
    size="50"
    maxlength="250"
    value="<?php echo $ARRAYfetchValues["dc:publisher"]["value"];?>"
    />
</div>

<div>
     <label for="<?php echo FORM_LABEL_Idioma;?>" accesskey="l"><?php echo ucfirst(LABEL_Idioma);?></label>
        <select id="<?php echo FORM_LABEL_Idioma;?>" name="<?php echo FORM_LABEL_Idioma;?>">
         <optgroup label="<?php echo LABEL_Idioma;?>">
    <?php
    echo doSelectForm($arrayLang,$array_vocabulario[idioma]);
        ?>
        </optgroup>
        </select>
</div>
<div>

     <label for="dia" accesskey="f"><?php echo ucfirst(LABEL_Fecha);?></label>
    <select name="<?php echo FORM_LABEL_FechaDia;?>"
     id="dia">
	<optgroup label="<?php echo LABEL_dia;?>">
         <?php
          echo doSelectForm($array_dia,"$fecha_crea[dia]");
          ?>
        </optgroup>
     </select>
         <select name="<?php echo FORM_LABEL_FechaMes;?>"
     id="mes">
	<optgroup label="<?php echo LABEL_mes;?>">
         <?php
          echo doSelectForm($array_mes,"$fecha_crea[mes]");
          ?>
        </optgroup>
     </select>
    <select name="<?php echo FORM_LABEL_FechaAno;?>"
     id="ano">
	<optgroup label="<?php echo LABEL_ano;?>">
         <?php
          echo doSelectForm($array_ano,"$fecha_crea[ano]");
          ?>
        </optgroup>
     </select>
   </div>
   <?php
    if ($array_vocabulario[vocabulario_id]==1){
    $ARRAYcontactMail=ARRAYfetchValue('CONTACT_MAIL');
    ?>
<div>
     <label for="contact_mail"><?php echo ucfirst(FORM_LABEL__contactMail);?></label>
        <input
    name="contact_mail"
    id="contact_mail"
    size="60"
    maxlength="256"
    value="<?php echo  $ARRAYcontactMail["value"];?>"
    />
</div>
    <?php
  }
  ?>
 <div>
     <label for="keywords" accesskey="k"><?php echo ucfirst(LABEL_Keywords);?></label>
        <input id="keywords"
    name="<?php echo FORM_LABEL_Keywords;?>"
    size="40"
    maxlength="256"
    value="<?php echo $array_vocabulario[keywords];?>"
    />
</div>

 <div>
     <label for="tipo_lang" accesskey="l"><?php echo ucfirst(LABEL_TipoLenguaje);?></label>
        <input id="tipo_lang"
    name="<?php echo FORM_LABEL_TipoLenguaje;?>"
    size="40"
    maxlength="150"
    value="<?php echo $array_vocabulario[tipo];?>"
    />
</div>
<div>
        <label for="URIt" accesskey="u"><?php echo ucfirst(LABEL_URI);?></label>
        <input id="URIt"
    name="<?php echo FORM_LABEL_URI;?>"
    size="40" maxlength="256"
    value="<?php echo $array_vocabulario[url_base];?>"
    />
</div>
 <div>
        <label for="cobertura" accesskey="d"><?php echo ucfirst(LABEL_Cobertura);?></label>
        <textarea id="cobertura"
    name="<?php echo FORM_LABEL_Cobertura;?>" rows="5"
    cols="38"><?php echo $array_vocabulario[cobertura];?></textarea>
</div>

		<?php
		if ($array_vocabulario[vocabulario_id]==1){
			echo HTMLformConfigValues($array_vocabulario);	
		}
		
		?>

 <div>
        <input type="hidden" 
        name="vocabulario_id" 
        id="vocabulario_id"
		value="<?php echo $array_vocabulario[vocabulario_id];?>"
		/>
		
		<div  align="center">

		<input type="button"  
		name="cancelar" 
		id="boton_cancelar" 
		type="button" 
		class="submit ui-corner-all"  
		onClick="location.href='admin.php?vocabulario_id=list'" 
		value="<?php echo ucfirst(LABEL_Cancelar);?>"
		/>		

        <input id="boton_enviar"
    	name="boton_config"
    	class="submit ui-corner-all"  
		type="submit"
		value="<?php echo FORM_LABEL_Guardar;?>"
		/>
		
        </div>
       
</div>
</fieldset>
</form>
<script id="demo" type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	var validator = $("#config-vocab").validate({
		rules: {'<?php echo FORM_LABEL_Titulo;?>':  {required: true, minlength: 3 },
            'contact_mail':  { required:false, email: true},
				},
				debug: true,
				errorElement: "label",
				 submitHandler: function(form) {
				   form.submit();
				 }
				
	});	
});
</script>	
