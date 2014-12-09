<?php
#   TemaTres : aplicación para la gestión de lenguajes documentales #       #
#                                                                        #
#   Copyright (C) 2004-2008 Diego Ferreyra tematres@r020.com.ar
#   Distribuido bajo Licencia GNU Public License, versión 2 (de junio de 1.991) Free Software Foundation
#  
###############################################################################################################
#
include("config.tematres.php");
$metadata=do_meta_tag();
?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo LANG;?>">
  <head>
  <?php echo $metadata["metadata"]; ?>
  <link type="image/x-icon" href="<?php echo T3_WEBPATH;?>images/tematres.ico" rel="icon" />
  <link type="image/x-icon" href="<?php echo T3_WEBPATH;?>images/tematres.ico" rel="shortcut icon" />
  <link rel="stylesheet" href="<?php echo T3_WEBPATH;?>css/style.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="<?php echo T3_WEBPATH;?>css/print.css" type="text/css" media="print" />

<script type="text/javascript" src="<?php echo T3_WEBPATH;?>jq/lib/jquery-1.8.0.min.js"></script>

<script type="text/javascript" src="<?php echo T3_WEBPATH;?>jq/jquery.autocomplete.js"></script>    
<script type="text/javascript" src="<?php echo T3_WEBPATH;?>jq/jquery.mockjax.js"></script>   
<script type="text/javascript" src="<?php echo T3_WEBPATH;?>jq/tree.jquery.js"></script>   

<link rel="stylesheet" type="text/css" href="<?php echo T3_WEBPATH;?>css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="<?php echo T3_WEBPATH;?>css/jqtree.css" />

<?php
if ($_SESSION[$_SESSION["CFGURL"]][ssuser_nivel]>0) 
{
	?>
<!-- Load TinyMCE -->
<script type="text/javascript" src="<?php echo T3_WEBPATH;?>tiny_mce/jquery.tinymce.js"></script>
<!-- /TinyMCE -->

	<script type="text/javascript" src="<?php echo T3_WEBPATH;?>jq/fg.menu.js"></script>    
	<link type="text/css" href="<?php echo T3_WEBPATH;?>jq/fg.menu.css" media="screen" rel="stylesheet" />
	<link type="text/css" href="<?php echo T3_WEBPATH;?>jq/theme/ui.all.css" media="screen" rel="stylesheet" />				
	<script type="text/javascript" src="<?php echo T3_WEBPATH;?>jq/jquery.jeditable.mini.js" charset="utf-8"></script>
<?php
}
?>
<script type="text/javascript" src="js.php" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo T3_WEBPATH;?>forms/jquery.validate.min.js"></script>
<?php
 if($_SESSION[$_SESSION["CFGURL"]]["lang"][2]!=='en')
		echo '<script src="'.T3_WEBPATH.'forms/localization/messages_'.$_SESSION[$_SESSION["CFGURL"]]["lang"][2].'.js" type="text/javascript"></script>';
?>
<script type="text/javascript" src="js.php" ></script>
</head>
  <body>
   <div id="arriba"></div>
			<div id="hd">
			  <div id="search-container" class="floatRight">
			<form method="get" id="simple-search" action="index.php" onsubmit="return checkrequired(this)">		  		  
			<input type="text" id="query" name="<?php echo FORM_LABEL_buscar;?>" size="20" value=""/>
			<input class="enlace" type="submit" value="<?php echo LABEL_Buscar ?>" />
			</form>
		  </div>

				<!-- id portal_navigation_bar used to denote the top links -->
				<div class="hd" id="portal_navigation_bar">
			
				
				<!-- browse_navigation used to denote the top portal navigation links -->
					 <ul id="browse_navigation" class="inline_controls">
					 <li class="first"><a title="<?php echo MENU_Inicio;?>" href="index.php"><?php echo MENU_Inicio;?></a></li>
				<?php
				//hay sesion de usuario
				if($_SESSION[$_SESSION["CFGURL"]][ssuser_nivel]){
					echo HTMLmainMenu();
				//no hay session de usuario
				}else{
				?>
					 <li><a href="login.php" title="<?php echo MENU_MiCuenta;?>"><?php echo MENU_MiCuenta;?></a></li>

				<?php
				};
				?>				
	
				<li><a title="<?php echo MENU_Sobre;?>" href="sobre.php"><?php echo MENU_Sobre;?></a></li>
				<li><a title="<?php echo LABEL_busqueda;?>" href="index.php?xsearch=1"><?php echo ucfirst(LABEL_BusquedaAvanzada);?></a></li>				
					</ul>
				</div>
			</div>	
<!-- body, or middle section of our header -->   

    <!-- ###### Header ###### -->
    <div id="header">
      <h1><a href="index.php" title="<?php echo $_SESSION[CFGTitulo].': '.MENU_ListaSis;?> "><?php echo $_SESSION[CFGTitulo];?></a></h1>
	</div>	

    <?php   
    $resumen=ARRAYresumen($_SESSION[id_tesa],"G","");
    $fecha_crea=do_fecha($_SESSION[CFGCreacion]);
    $fecha_mod=do_fecha($_SESSION["CFGlastMod"]);
    $ARRAYmailContact=ARRAYfetchValue('CONTACT_MAIL');
    ?>
<div id="bodyText">
     <h1><?php echo $_SESSION[CFGTitulo];?> / <?php echo $_SESSION[CFGAutor];?></h1>
        <dl id="sumario">
        <dt><?php echo ucfirst(LABEL_URI);?></dt><dd><?php echo $_SESSION[CFGURL];?> </dd>
        <dt><?php echo ucfirst(LABEL_Idioma);?></dt><dd><?php echo $_SESSION[CFGIdioma];?></dd>
        <dt><?php echo ucfirst(FORM_LABEL__contactMail);?></dt><dd><?php echo $ARRAYmailContact["value"];?></dd>
        <dt><?php echo ucfirst(LABEL_Fecha);?></dt><dd><?php echo $fecha_crea[dia].'/'.$fecha_crea[mes].'/'.$fecha_crea[ano];?></dd>
		<dt><?php echo ucfirst(LABEL_lastChangeDate);?></dt><dd><?php echo $fecha_mod[dia].'/'.$fecha_mod[mes].'/'.$fecha_mod[ano];;?>
        <dt><?php echo ucfirst(LABEL_Keywords);?></dt><dd><?php echo $_SESSION[CFGKeywords];?></dd>
        <dt><?php echo ucfirst(LABEL_TipoLenguaje);?></dt><dd><?php echo $_SESSION[CFGTipo];?></dd>
        <dt><?php echo ucfirst(LABEL_Cobertura);?></dt><dd><?php echo $_SESSION[CFGCobertura];?></dd>
        <dt><?php echo ucfirst(LABEL_Terminos);?></dt><dd><?php echo $resumen[cant_total];?> <ul>
        <ul>
	<?php

	if($_SESSION[$_SESSION["CFGURL"]]["CFG_VIEW_STATUS"]==1)
	{
		if($resumen[cant_candidato]>0){
			echo '<li><a href="index.php?estado_id=12">'.ucfirst(LABEL_Candidato).': '.$resumen[cant_candidato].'</a></li>';
			}

		if($resumen[cant_rechazado]>0){
			echo '<li><a href="index.php?estado_id=14">'.ucfirst(LABEL_Rechazado).': '.$resumen[cant_rechazado].'</a></li>';
			}
	}	
	?>
	</ul></dd>
        <dt><?php echo ucfirst(LABEL_RelTerminos);?></dt><dd><?php echo $resumen[cant_rel];?></dd>
        <dt><?php echo ucfirst(LABEL_TerminosUP);?></dt><dd><?php echo $resumen[cant_up];?></dd>
		
		<?php
		//Evaluar si hay notas
		if (is_array($resumen["cant_notas"])) 
		{
			
			  $sqlNoteType=SQLcantNotas();
			  $arrayNoteType=array();
			  
			  while ($array=$sqlNoteType->FetchRow()){
			  		 if($array[cant]>0)
			  		 {
			  		 	 echo '<dt>';
				  		 echo  (in_array($array["value_id"],array(8,9,10,11,15))) ? arrayReplace(array(8,9,10,11,15),array(LABEL_NA,LABEL_NH,LABEL_NB,LABEL_NP,LABEL_NC),$array["value_id"]) : $array["value"];
				    	 echo '</dt><dd> '.$array[cant].'</dd>';    	 
			  		 }	 
			  };			
		}

		//are enable SPARQL
		if(CFG_ENABLE_SPARQL==1)
		{
			echo '<dt>'.LABEL_SPARQLEndpoint.'</dt> <dd><a href="'.$_SESSION["CFGURL"].'sparql.php" title="'.LABEL_SPARQLEndpoint.'">'.$_SESSION["CFGURL"].'sparql.php</a></dd>';
		}			
		//are enable SPARQL
		if(CFG_SIMPLE_WEB_SERVICE ==1)
		{
			echo '<dt>API </dt> <dd><a href="'.$_SESSION["CFGURL"].'services.php" title="API">'.$_SESSION["CFGURL"].'services.php</a></dd>';
		}			
		?>	
	</dl>
	<?php


	if($_SESSION[$_SESSION["CFGURL"]][ssuser_id]){
		//es admin y quiere ver un usuario
  		if(($_GET[user_id])	&&	($_SESSION[$_SESSION["CFGURL"]][ssuser_nivel]==1))
  		{
		echo doBrowseTermsFromUser(secure_data($_GET[user_id],$_GET[ord]));
		//no es admin y quiere verse a si mismo
  		}
  		elseif($_GET[user_id])
  		{
		echo doBrowseTermsFromUser(secure_data($_SESSION[$_SESSION["CFGURL"]][ssuser_id],"sql"),secure_data($_GET[ord],"sql"));
		//quiere ver un año
		}
		elseif($_GET[y])
		{
		echo doBrowseTermsFromDate(secure_data($_GET[m],"sql"),secure_data($_GET[y],"sql"),secure_data($_GET[ord],"sql"));
		}
		else
		{
		//ver lista agregada
		echo doBrowseTermsByDate();
		}
	};
	?>
    <!-- ###### Footer ###### -->
    </div>

    <div>
    <div id="footer">  <!-- NB: outer <div> required for correct rendering in IE -->
    <div  style="float: left;
    width: 70px;">
      <a href="http://r020.com.ar/tematres/" title="TemaTres: vocabulary server"><img src="../common/images/tematresfoot.jpg"  alt="TemaTres"/></a>
    </div>
      <div>
        <strong><?php echo LABEL_Autor ?>: </strong>
        <span class="footerCol2"><?php echo $_SESSION["CFGAutor"];?></span>
      </div>
      <div>
        <strong><?php echo LABEL_URI ?>: </strong>
        <span class="footerCol2"> <?php echo $_SESSION["CFGURL"];?></span>
      </div>
			<?php
				//are enable SPARQL
				if(CFG_ENABLE_SPARQL==1)
				{
					echo '<strong><a href="'.$_SESSION["CFGURL"].'sparql.php" title="'.LABEL_SPARQLEndpoint.'">'.LABEL_SPARQLEndpoint.'</a></strong>';
					echo '<div class="clearer"></div>';
				}
			?>
      <div>
        <strong><?php echo LABEL_Version ?>: </strong>
        <span class="footerCol2"><?php echo $_SESSION["CFGVersion"];?></span>
      </div>
      
           
    </div></div>
  </body>
</html>
