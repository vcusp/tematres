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
 /*
term reporter
*/
if(($_GET[mod]=='csv') && (substr($_GET[task],0,3)=='csv') && ($_SESSION[$_SESSION["CFGURL"]][ssuser_id]))  
{
	return wichReport($_GET[task]);
}

$search_string ='';
$search_string = (doValue($_GET,FORM_LABEL_buscar)) ? XSSprevent(doValue($_GET,FORM_LABEL_buscar)) : '';
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
<script type="application/javascript" src="js.php" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo T3_WEBPATH;?>forms/jquery.validate.min.js"></script>
<?php
 if($_SESSION[$_SESSION["CFGURL"]]["lang"][2]!=='en')
		echo '<script src="'.T3_WEBPATH.'forms/localization/messages_'.$_SESSION[$_SESSION["CFGURL"]]["lang"][2].'.js" type="text/javascript"></script>';
?>
</head>
  <body>
   <div id="arriba"></div>
			<div id="hd">
			  <div id="search-container" class="floatRight">
			<form method="get" id="simple-search" name="simple-search" action="index.php" onsubmit="return checkrequired(this)">		  		  
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

<!-- ###### Body Text ###### -->
<?php

	require_once(T3_ABSPATH . 'common/include/inc.inicio.php');

?>
<p></p>
<!-- ###### Footer ###### -->

    <div id="footer">   
		<div id="subsidiary">  <!-- NB: outer <div> required for correct rendering in IE -->
			<div id="first">
				<?php    
				 if(!$_GET[letra]) 
				 {
					 echo HTMLlistaAlfabeticaUnica();
				 }
				 ?>				
 		    </div>  		  
   		    
   		    <div id="second">
				<div>				
					<div class="clearer"></div>
					<strong><?php echo LABEL_URI ?>: </strong><span class="footerCol2"><a href="<?php echo $_SESSION["CFGURL"];?>"><?php echo $_SESSION["CFGURL"];?></a></span>
					<div class="clearer"></div>
					<?php
					//are enable SPARQL
					if(CFG_ENABLE_SPARQL==1)
					{
						echo '<strong><a href="'.$_SESSION["CFGURL"].'sparql.php" title="'.LABEL_SPARQLEndpoint.'">'.LABEL_SPARQLEndpoint.'</a></strong>';
						echo '<div class="clearer"></div>';

					}
				
					if(CFG_SIMPLE_WEB_SERVICE==1)
					{
						echo '<a href="'.$_SESSION["CFGURL"].'services.php" title="API">API</a>';
						echo '<div class="clearer"></div>';

					}
					?>
					<strong><?php echo LABEL_Autor ?>: </strong><span class="footerCol2"><?php echo $_SESSION["CFGAutor"];?></span>
					<div class="clearer"></div>
					<?php echo doMenuLang($metadata["arraydata"]["tema_id"]); ?>
				</div>			
					
		    </div>
    </div>
  </div>  
 </body>
</html>