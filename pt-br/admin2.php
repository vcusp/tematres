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

if($_SESSION[$_SESSION["CFGURL"]]["ssuser_nivel"]!=='1'){
	header("Location:login.php");
	};

//Acciones de gestion de usuarios
if($_POST["boton"]==LABEL_Enviar){
	$user_id=admin_users("alta");
	header("Location:admin.php?user_id=list");
	}

if($_POST["useactua"]){
	$user_id=admin_users("actua",$_POST["useactua"]);
	header("Location:admin.php?user_id=list");
	}

if($_GET["usestado"]){
	$user_id=admin_users("estado",$_GET["usestado"]);
	header("Location:admin.php?user_id=list");
	}

if(($_POST["boton_config"])&&(is_numeric($_POST["vocabulario_id"]))){
	abm_vocabulario("M",$_POST["vocabulario_id"]);
	header("Location:admin.php?vocabulario_id=list");
	};

if(($_POST["boton_config"])&&($_POST["vocabulario_id"]=='NEW')){
	abm_vocabulario("A");
	header("Location:admin.php?vocabulario_id=list");
	};


if(($_POST["doAdmin"]=='addTargetVocabulary')){
	abm_targetVocabulary("A");
	};

if(($_POST["doAdmin"]=='saveTargetVocabulary')){
	abm_targetVocabulary("M",$_POST["tvocab_id"]);
	};

if(($_POST["doAdmin"]=='saveUserTypeNote')){
	abm_UserTypeNote("M",$_POST["tvocab_id"]);
	};

if(($_POST["doAdmin"]=='massrem')){
	REMmassiveData($_POST);
	header("Location:index.php");
	};

if(($_POST["doAdmin"]=='updateEndpointNow')){
	doSparqlEndpoint($_POST);
	header("Location:sparql.php");
	};
	
//Acciones de gestion de vocabularios
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
				if($_SESSION[$_SESSION["CFGURL"]]["ssuser_nivel"]){
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
      <h1><a href="index.php" title="<?php echo $_SESSION["CFGTitulo"].': '.MENU_ListaSis;?> "><?php echo $_SESSION["CFGTitulo"];?></a></h1>
	</div>	

    <!-- ###### Body Text ###### -->
     <div id="bodyText">
<?php
if($_SESSION[$_SESSION["CFGURL"]]["ssuser_id"]){


	if($_GET["opTbl"]=='TRUE'){
		echo optimizarTablas();
		};

	if($_GET["user_id"]=='list'){
		echo HTMLListaUsers();
		};

	if(is_numeric($_GET["user_id"])){
		require_once(T3_ABSPATH . 'common/include/inc.formUsers.php');
		}

	if($_GET["user_id"]=='new'){
		require_once(T3_ABSPATH . 'common/include/inc.formUsers.php');
	};


	if(is_numeric($_GET["vocabulario_id"])){
		require_once(T3_ABSPATH . 'common/include/inc.abmConfig.php');
		}		


	if(($_GET["vocabulario_id"]=='list')||(count($_GET)<1)){
		echo HTMLlistaVocabularios();
		echo HTMLlistaTargetVocabularios();
		echo HTMLformUserNotes();
		echo HTMLformUserRelations();
		echo HTMLformURIdefinition();
		};

	//Formulario de exportación
	if($_GET["doAdmin"]=='export'){
		echo HTMLformExport();
		}

	//Regenerate indice table
	if($_GET["doAdmin"]=='reindex'){
		$sql=SQLreCreateTermIndex();
		echo $sql[cant_terms_index].' '.LABEL_Terminos;
		}

	if($_GET["doAdmin"]=='import'){
		echo HTMLformImport();
	}		
		

	if($_GET["doAdmin"]=='massiverem'){
		echo HTMLformMasiveDelete();
	}		
		

	if($_GET["doAdmin"]=='updateEndpoint'){
		echo HTMLformUpdateEndpoit();
	}		
		
	//Formulario de import 
	if($_POST["taskAdmin"]=='importTab'){
			require_once(T3_ABSPATH . 'common/include/inc.import.php');
		};
	//Formulario de import 
	if($_POST["taskAdmin"]=='importTag'){
			require_once(T3_ABSPATH . 'common/include/inc.importTXT.php');
		};		
	//Formulario de import line 127 after Formulario de exportación
	if($_POST["taskAdmin"]=='importSkos'){
			require_once(T3_ABSPATH . 'common/include/inc.importSkos.php');
		};

	//Form to add / edit foreing target vocabulary 
	if($_GET["doAdmin"]=='seeformTargetVocabulary'){		
			echo HTMLformTargetVocabulary($_GET[tvocab_id]);
			}		

	//Form to add / edit foreing target vocabulary 
	if($_GET["doAdmin"]=='seeTermsTargetVocabulary'){
			echo HTMLlistaTermsTargetVocabularios($_GET[tvocab_id],$_GET[f]);
			}		
			
	//update from tematres 1.1 -> tematres 1.2 
	if($_GET["doAdmin"]=='updte1_1x1_2'){
				echo updateTemaTres('1_1x1_2');
				}								

	//update from tematres 1.1 -> tematres 1.2 
	if($_GET["doAdmin"]=='updte1x1_2'){
				echo updateTemaTres('1x1_2');
				}								

	//update from tematres 1.3 -> tematres 1.4 
	if($_GET["doAdmin"]=='updte1_3x1_4'){
				echo updateTemaTres('1_3x1_4');
				}								
	//update from tematres 1.4 -> tematres 1.5 
	if($_GET["doAdmin"]=='updte1_4x1_5'){
				echo updateTemaTres('1_4x1_5');
				}								
	//update from tematres 1.5 -> tematres 1.6 
	if($_GET["doAdmin"]=='updte1_5x1_6'){
				echo updateTemaTres('1_5x1_6');
				}								
	//update from tematres 1.6 -> tematres 1.7
	if($_GET["doAdmin"]=='updte1_6x1_7'){
				echo updateTemaTres('1_6x1_7');
				}								
	}
?>
</div>
    <!-- ###### Footer ###### -->
    
    <div><div id="footer">  <!-- NB: outer <div> required for correct rendering in IE -->
      <div>
        <strong>Autor: </strong>
        <span class="footerCol2"><?php echo $_SESSION["CFGAutor"];?></span>
      </div>

      <div>
        <strong>URI: </strong>
        <span class="footerCol2"><?php echo $_SESSION["CFGURL"];?></span>
      </div>

      <div>
        <strong>Version: </strong>
        <span class="footerCol2"><?php echo $_SESSION["CFGVersion"];?></span>
      </div>
    </div></div>
  </body>
</html>
