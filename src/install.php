<?php
#   TemaTres : aplicación para la gestión de lenguajes documentales #       #
#                                                                        #
#   Copyright (C) 2004-2008 Diego Ferreyra tematres@r020.com.ar
#   Distribuido bajo Licencia GNU Public License, versión 2 (de junio de 1.991) Free Software Foundation
#  
###############################################################################################################
#
// Config char encode (only can be: utf-8 or iso-8859-1)
$CFG["_CHAR_ENCODE"] ='utf-8';

$page_encode = (in_array($CFG["_CHAR_ENCODE"],array('utf-8','iso-8859-1'))) ? $CFG["_CHAR_ENCODE"] : 'utf-8';

header ('Content-type: text/html; charset='.$page_encode.'');

//Config lang
$lang='';
$tematres_lang='';
$lang_install=(isset($_GET["lang_install"])) ? $_GET["lang_install"] : 'es';

$lang = $tematres_lang=(in_array($lang_install,array('ca','de','en','es','fr','it','nl','pt'))) ? $lang_install : 'es'; 

	//1. check if config file exist
	if ( !file_exists('db.tematres.php'))  
	{
		return message('<div class="error">Configuration file <code>db.tematres.php</code> not found!</div><br/>') ;
	}
	else
	{
		include('db.tematres.php');
	}
	

require_once(T3_ABSPATH . 'common/lang/'.$lang.'-utf-8.inc.php') ;

function message($mess) {
	echo "" ;
	echo $mess ;
	echo "<br/>" ;
}


function checkInstall($lang)
{
	GLOBAL $install_message;
	
	$conf_file_path =  str_replace("install.php","db.tematres.php","http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']) ;			
	
	//1. check if config file exist
	if ( !file_exists('db.tematres.php') )  
	{
		return message('<div class="error">'.sprintf($install_message[201],$conf_file_path).'</div><br/>') ;
	}
	else
	{
		message("<div><span class=\"success\">$install_message[202]</span></div><br/>") ;
		include('db.tematres.php');
	}
	
	
	if($DBCFG["debugMode"]==0) 
	{
		//silent warnings
		error_reporting(0);
		$label_login='';
		$label_dbname='';
		$label_server='';
	}	
	else 
	{
		$label_login=$DBCFG["DBLogin"];
		$label_dbname=$DBCFG["DBName"];
		$label_server=$DBCFG["Server"];			
	};
	//2. check connection to server
		
	require_once(T3_ABSPATH . 'common/include/adodb5/adodb.inc.php');
	
	//default driver
	$DBCFG["DBdriver"]=($DBCFG["DBdriver"]) ? $DBCFG["DBdriver"] : "mysqli";
	
	$DB = NewADOConnection($DBCFG["DBdriver"]);

	if(!$DB->Connect($DBCFG["Server"], $DBCFG["DBLogin"], $DBCFG["DBPass"]))
	{
		return message('<div class="error">'.sprintf($install_message[203],$label_server,$label_login,$conf_file_path).'</div><br/>') ;
	}
	else
	{
		message('<div><span class="success">'.sprintf($install_message[204],$label_server).'</span></div><br/>') ;
	}
//~ 
	//3. check connection to database
	if(!$DB->Connect($DBCFG["Server"], $DBCFG["DBLogin"], $DBCFG["DBPass"],$DBCFG["DBName"]))
	{

		return message('<div class="error">'.sprintf($install_message[205],$label_dbname,$label_server,$conf_file_path).'</div><br/>');
	}
	else
	{
		 message('<div><span class="success">'.sprintf($install_message[206],$label_dbname,$label_server).'</span></div>');
	
	}
	
	
	//4. check tables

	$sql=$DB->Execute('SHOW TABLES from `'.$DBCFG["DBName"].'` where `tables_in_'.$DBCFG["DBName"].'` in (\''.$DBCFG["DBprefix"].'config\',\''.$DBCFG["DBprefix"].'indice\',\''.$DBCFG["DBprefix"].'notas\',\''.$DBCFG["DBprefix"].'tabla_rel\',\''.$DBCFG["DBprefix"].'tema\',\''.$DBCFG["DBprefix"].'usuario\',\''.$DBCFG["DBprefix"].'values\')');

	if ($DB->Affected_Rows()=='7')
	{		
	return message("<div class=\"error\">$install_message[301]</div>") ;		
	}
	else
	{
	//Final step: dump or form	
	if ( isset($_POST['send']) ) 
		{
		$sqlInstall=SQLtematres($DBCFG,$DB);
		}
		else 
		{
		echo HTMLformInstall($lang);		
		}
	}
}


function SQLtematres($DBCFG,$DB)
{
	
// Si se establecio un charset para la conexion
if(@$DBCFG["DBcharset"]){
	$DB->Execute("SET NAMES $DBCFG[DBcharset]");
	}
	
		$prefix=$DBCFG["DBprefix"] ;


		$result1 = $DB->Execute("CREATE TABLE `".$prefix."config` (
		  `id` int(5) unsigned NOT NULL auto_increment,
		  `titulo` varchar(255) NOT NULL default '',
		  `autor` varchar(255) NOT NULL default '',
		  `idioma` char(5) NOT NULL default 'es',
		  `cobertura` text,
		  `keywords` varchar(255) default NULL,
		  `tipo` varchar(100) default NULL,
		  `polijerarquia` tinyint(1) NOT NULL default '1',
		  `cuando` date NOT NULL default '0000-00-00',
		  `observa` text,
		  `url_base` varchar(255) default NULL,
		  PRIMARY KEY  (`id`)
		) DEFAULT CHARSET=utf8 ENGINE=MyISAM ;") ;
		
		//If create table --> insert data
		if($result1)
		{
			$today = date("Y-m-d") ;
			$url =  str_replace("install.php","","http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']) ;
			$title = ($_POST['title']) ? $DB->qstr(trim($_POST["title"]),get_magic_quotes_gpc()) : "'TemaTres'";
			$author = ($_POST['author']) ? $DB->qstr(trim($_POST["author"]),get_magic_quotes_gpc()) : "'TemaTres'";
			$tematres_lang=$DB->qstr(trim($_POST["lang"]),get_magic_quotes_gpc());
			$tematres_lang=($tematres_lang) ?  $tematres_lang : 'es';

			$comment = '';			

			
			$result2 = $DB->Execute("INSERT INTO `".$prefix."config` 
			(`id`, `titulo`, `autor`, `idioma`,  `tipo`, `polijerarquia`, `cuando`, `observa`, `url_base`) 
			VALUES (1, $title, $author, $tematres_lang, 'Controlled vocabulary', 2, '$today', NULL, '$url');") ;
			

			
			}
		$result3 = $DB->Execute("CREATE TABLE `".$prefix."indice` (
				  `tema_id` int(11) NOT NULL default '0',
				  `indice` varchar(250) NOT NULL default '',
				  PRIMARY KEY  (`tema_id`),
				  KEY `indice` (`indice`)
				) DEFAULT CHARSET=utf8 ENGINE=MyISAM COMMENT='indice de temas';") ;

		$result4 = $DB->Execute("CREATE TABLE `".$prefix."notas` (
				  `id` int(11) NOT NULL auto_increment,
				  `id_tema` int(11) NOT NULL default '0',
				  `tipo_nota` char(3) NOT NULL default 'NA',
				  `lang_nota` varchar(7) default NULL,
				  `nota` mediumtext NOT NULL,
				  `cuando` datetime NOT NULL default '0000-00-00 00:00:00',
				  `uid` int(5) NOT NULL default '0',
				  PRIMARY KEY  (`id`),
				  KEY `id_tema` (`id_tema`),
				  KEY `orden_notas` (`tipo_nota`,`lang_nota`),
				  FULLTEXT `notas` (`nota`)		
				) DEFAULT CHARSET=utf8 ENGINE=MyISAM ;") ;

		$result5 = $DB->Execute("CREATE TABLE `".$prefix."tabla_rel` (
				  `id_mayor` int(5) NOT NULL default '0',
				  `id_menor` int(5) NOT NULL default '0',
				  `t_relacion` tinyint(1) unsigned NOT NULL default '0',
				  `rel_rel_id` int(22) NULL,
				  `id` int(9) unsigned NOT NULL auto_increment,
				  `uid` int(10) unsigned NOT NULL default '0',
				  `cuando` datetime NOT NULL default '0000-00-00 00:00:00',
				  PRIMARY KEY  (`id`),
				  UNIQUE KEY `NewIndex` (`id_mayor`,`id_menor`),
				  KEY `unico` (`t_relacion`),
				  KEY `id_menor` (`id_menor`),
				  KEY `id_mayor` (`id_mayor`),
				  KEY `rel_rel_id` (`rel_rel_id`)
				) DEFAULT CHARSET=utf8 ENGINE=MyISAM ;") ;

		$result6 = $DB->Execute("CREATE TABLE `".$prefix."tema` (
				  `tema_id` int(10) NOT NULL auto_increment,
				  `code` VARCHAR( 150 ) NULL COMMENT 'code_term' ,
				  `tema` varchar(250) default NULL,
				  `tesauro_id` int(5) NOT NULL default '0',
				  `uid` tinyint(3) unsigned NOT NULL default '0',
				  `cuando` datetime NOT NULL default '0000-00-00 00:00:00',
				  `uid_final` int(5) unsigned default NULL,
				  `cuando_final` datetime default NULL,
				  `estado_id` int(5) NOT NULL default '13',
				  `cuando_estado` datetime NOT NULL default '0000-00-00 00:00:00',
			      `isMetaTerm` BOOLEAN NOT NULL DEFAULT FALSE, 
				  PRIMARY KEY  (`tema_id`),
				  KEY ( `code` ),
				  KEY `tema` (`tema`),
				  KEY `cuando` (`cuando`,`cuando_final`),
				  KEY `uid` (`uid`,`uid_final`),
				  KEY `tesauro_id` (`tesauro_id`),
				  KEY `estado_id` (`estado_id`),
			      KEY `isMetaTerm` (`isMetaTerm`)
				) DEFAULT CHARSET=utf8 ENGINE=MyISAM ;") ;

		$result61 = $DB->Execute("CREATE TABLE IF NOT EXISTS `".$prefix."term2tterm` (
				 `tterm_id` int(22) NOT NULL AUTO_INCREMENT,
				  `tvocab_id` int(22) NOT NULL,
				  `tterm_url` varchar(200) NOT NULL,
				  `tterm_uri` varchar(200) NOT NULL,
				  `tterm_string` varchar(250) NOT NULL,
				  `cuando` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  `cuando_last` timestamp NULL DEFAULT NULL,
				  `uid` int(22) NOT NULL,
				  `tema_id` int(22) NOT NULL,
				  PRIMARY KEY (`tterm_id`),
				  KEY `tvocab_id` (`tvocab_id`,`cuando`,`cuando_last`,`uid`),
				  KEY `tema_id` (`tema_id`),
  				  KEY `target_terms` (`tterm_string`)
				) DEFAULT CHARSET=utf8 ENGINE=MyISAM;") ;
				
		$result62 = $DB->Execute("CREATE TABLE IF NOT EXISTS `".$prefix."tvocab` (
				 `tvocab_id` int(22) NOT NULL AUTO_INCREMENT,
				  `tvocab_label` varchar(150) NOT NULL,
				  `tvocab_tag` varchar(20) NOT NULL,
				  `tvocab_lang` VARCHAR( 5 ),
				  `tvocab_title` varchar(200) NOT NULL,
				  `tvocab_url` varchar(250) NOT NULL,
				  `tvocab_uri_service` varchar(250) NOT NULL,
				  `tvocab_status` tinyint(1) NOT NULL,
				  `cuando` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  `uid` int(22) NOT NULL,
				  PRIMARY KEY (`tvocab_id`),
				  KEY `uid` (`uid`),
				  KEY `status` (`tvocab_status`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;") ;

		$result7 = $DB->Execute("CREATE TABLE `".$prefix."usuario` (
				  `APELLIDO` varchar(150) default NULL,
				  `NOMBRES` varchar(150) default NULL,
				  `uid` int(9) unsigned default NULL,
				  `cuando` date default NULL,
				  `id` int(11) unsigned NOT NULL auto_increment,
				  `mail` varchar(255) default NULL,
				  `pass` varchar(60) NOT NULL default '',
				  `orga` varchar(255) default NULL,
				  `nivel` tinyint(1) unsigned NOT NULL default '2',
				  `estado` set('ACTIVO','BAJA') NOT NULL default 'BAJA',
				  `hasta` datetime NOT NULL default '0000-00-00 00:00:00',
				  `user_activation_key` varchar(60) DEFAULT NULL,
				  PRIMARY KEY  (`id`),
				  UNIQUE KEY `mail` (`mail`),
				  KEY `pass` (`pass`),
				  KEY `user_activation_key` (`user_activation_key`)
				) DEFAULT CHARSET=utf8 ENGINE=MyISAM ;") ;

		$result9 = $DB->Execute("CREATE TABLE `".$prefix."values` (
				  `value_id` int(11) NOT NULL auto_increment,
				  `value_type` varchar(64) NOT NULL default '0',
				  `value` longtext NOT NULL ,
				  `value_order` tinyint(4) default NULL,
				  `value_code` varchar(20) default NULL,
				  PRIMARY KEY  (`value_id`),
				  KEY `value_type` (`value_type`)
				) DEFAULT CHARSET=utf8 ENGINE=MyISAM COMMENT='general values table';") ;
		$result10 = $DB->Execute("CREATE TABLE `".$prefix."uri` (
			  `uri_id` int(22) NOT NULL AUTO_INCREMENT,
			  `tema_id` int(22) NOT NULL,
			  `uri_type_id` int(22) NOT NULL,
			  `uri` tinytext NOT NULL,
			  `uid` int(22) NOT NULL,
			  `cuando` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`uri_id`),
			  KEY `tema_id` (`tema_id`)
			) CHARSET=utf8 ENGINE=MyISAM  COMMENT='external URIs associated to terms';");


		$result10 = $DB->Execute("INSERT INTO `".$prefix."values` (`value_id`, `value_type`, `value`, `value_order`, `value_code`) VALUES (2, 't_relacion', 'Termino relacionado', NULL, 'TR'),
				(3, 't_relacion', 'Termino superior', NULL, 'TG'),
				(4, 't_relacion', 'Usado por', NULL, 'UP'),
				(5, 't_relacion', 'Equivalencia parcial', NULL, 'EQ_P'),
				(6, 't_relacion', 'Equivalencia total', NULL, 'EQ'),
				(7, 't_relacion', 'No equivalencia', NULL, 'EQ_NO'),
				(8, 't_nota', 'Nota de alcance', 1, 'NA'),
				(9, 't_nota', 'Nota histórica', 2, 'NH'),
				(10, 't_nota', 'Nota bibliografica', 3, 'NB'),
				(11, 't_nota', 'Nota privada', 4, 'NP'),
				(1, 't_usuario', 'Admin', NULL, 'admin'),
				(12, 't_estado', 'termino candidato', 1, 'C'),
				(13, 't_estado', 'termino activo', 2, 'A'),
				(14, 't_estado', 'termino rechazado', 3, 'R'),
				(15, 't_nota', 'Nota catalográfica', 2, 'NC'),
				(16, 'config', '_USE_CODE', 1, '0'),
				(17, 'config', '_SHOW_CODE', 1, '0'),
				(18, 'config', 'CFG_MAX_TREE_DEEP', NULL, '3'),
				(19, 'config', 'CFG_VIEW_STATUS', NULL, '0'),
				(20, 'config', 'CFG_SIMPLE_WEB_SERVICE', NULL, '1'),
				(21, 'config', 'CFG_NUM_SHOW_TERMSxSTATUS', NULL, '200'),
				(22, 'config', 'CFG_MIN_SEARCH_SIZE', NULL, '2'),
				(23, 'config', '_SHOW_TREE', '1', '1'),
				(24, 'config', '_PUBLISH_SKOS', '1', '0'),
				(25,'4', 'Spelling variant', NULL, 'SP'),
				(26,'4', 'MisSpelling', NULL, 'MS'),
				(27,'3', 'Partitive', NULL, 'P'),
				(28,'3', 'Instance', NULL, 'I'),
				(30,'4', 'Abbreviation', NULL, 'AB'),
				(31,'4', 'Full form of the term', NULL, 'FT'),
				(32,'URI_TYPE', 'broadMatch', NULL, 'broadMatch'),
				(33,'URI_TYPE', 'closeMatch', NULL, 'closeMatch'),
				(34,'URI_TYPE', 'exactMatch', NULL, 'exactMatch'),
				(35,'URI_TYPE', 'relatedMatch', NULL, 'relatedMatch'),
				(36,'URI_TYPE', 'narrowMatch', NULL, 'narrowMatch'),
				(37,'DATESTAMP',now(), NULL,'NOTE_CHANGE'),
				(38,'DATESTAMP',now(), NULL,'TERM_CHANGE'),
				(39,'DATESTAMP',now(), NULL,'TTERM_CHANGE'),
				(40,'DATESTAMP',now(), NULL,'THES_CHANGE'),
				(41,'METADATA',NULL, 2,'dc:contributor'),
				(42,'METADATA',NULL, 5,'dc:publisher'),
				(43,'METADATA',NULL, 9,'dc:rights'),
				(44,'4', 'Hidden', NULL, 'H'),
				(45, 'config', 'CFG_SEARCH_METATERM', NULL, '0'),
				(46, 'config', 'CFG_ENABLE_SPARQL', NULL, '0'),
				(47, 'config', 'CFG_SUGGESTxWORD', NULL, '1')
				");

		//If create table --> insert data
		if(is_object($result7))
		{

		 $admin_mail=($_POST['mail']) ? $DB->qstr(trim($_POST['mail']),get_magic_quotes_gpc()) : "'admin@r020.com.ar'";
		 $admin_name=($_POST['name']) ? $DB->qstr(trim($_POST['name']),get_magic_quotes_gpc()) : "'admin name'";
		 $admin_surname=($_POST['s_name']) ? $DB->qstr(trim($_POST['s_name']),get_magic_quotes_gpc()) : "'admin sur name'";

		 
		 $admin_pass=($_POST['mdp']) ? trim($_POST['mdp']) : "'admin'";		 
		 
	     require_once(T3_ABSPATH . 'common/include/fun.gral.php');
	     
		 $admin_pass_hash=(CFG_HASH_PASS==1) ? t3_hash_password($admin_pass) : $admin_pass; 
		 
		 $admin_pass_hash=($admin_pass_hash) ? $DB->qstr(trim($admin_pass_hash),get_magic_quotes_gpc()) : "'admin'";		 
	 
 
		 $result8=$DB->Execute("INSERT INTO
			`".$prefix."usuario` (`APELLIDO`, `NOMBRES`, `uid`, `cuando`, `id`, `mail`, `pass`, `orga`, `nivel`, `estado`, `hasta`)
			VALUES
			($admin_name,$admin_surname, 1, now(), 1, $admin_mail,$admin_pass_hash, 'TemaTres', 1, 'ACTIVO', now())") ;
		
		//echo $DB->ErrorMsg();
		
		//Tematres installed
		if(is_object($result8))
		{			
			GLOBAL $install_message;
			
			$return_true = '<div class="information">';
			$return_true .= '<h3>'.ucfirst(LABEL_bienvenida).'</h3>' ;				
			$return_true .= '<span style="text-decoration: underline">'.$install_message[306].'</span>' ;				
			//~ Not echo for security 
			//~ $return_true .='<li>'.ucfirst(LABEL_mail).': '.$admin_mail.'</li>' ;
			//~ $return_true .= '<li>'.ucfirst(LABEL_pass).' : '.$admin_pass.'</li>' ;
			$return_true .='</div>';
		
			message($return_true);
		}
			
		}
		
	
}


function HTMLformInstall($lang_install)
{
	GLOBAL $install_message;

    require_once(T3_ABSPATH . 'common/include/config.tematres.php');
    require_once(T3_ABSPATH . 'common/include/fun.gral.php');

	$arrayLang=array();
	foreach ($CFG["ISO639-1"] as $langs) {
		array_push($arrayLang,"$langs[0]#$langs[1]");
	};

	
	$rows='<div><form class="myform" id="formulaire" name="formulaire" method="post" action="">
		<fieldset>
		<legend style="margin-bottom: 5px;">'.ucfirst(LABEL_lcDatos).'</legend>';
				
	$rows.='<div><label for="title">'.ucfirst(LABEL_Titulo).'</label>
		<input type="text" class="campo" id="title" name="title"/></div>
		<div><label for="author">'.ucfirst(LABEL_Autor).'</label>
		<input type="text" name="author" id="author" class="campo"/></div>';
		
	$rows.='<div class="formdiv">
     <label for="'.FORM_LABEL_Idioma.'" accesskey="l">'.LABEL_Idioma.'</label>
        <select id="lang" name="lang">'.doSelectForm($arrayLang,$lang_install).'</select>
	</div>';
		
	$rows.='</fieldset>';

		
	$rows.='<fieldset>
	
	<legend style="margin-bottom: 5px;">'.ucfirst(MENU_NuevoUsuario).'</legend>

		<div><label for="name">'.ucfirst(LABEL_nombre).'</label>
		<input type="text" name="name"/></div>

		<div><label for="s_name">'.ucfirst(LABEL_apellido).'</label>
		<input type="text" name="s_name"/></div>

		<div><label for="mail">'.ucfirst(LABEL_mail).'</label>
		<input type="text" name="mail"/>
		</div>
		<div><label for="mdp">'.ucfirst(LABEL_pass).'</label>
		<input type="password" name="mdp" id="mdp" onkeyup="test_password();" onclick="if (document.formulaire.saisie.value == \'password\') this.value = \'\'; test_password();"/>
		<img src="images/sec0.png" name="img_secu" height="20" width="0"/>
		</div>		
		<div><label for="mdp">'.ucfirst(LABEL_repass).'</label>
		<input type="password" name="repass"  />
		</div>		
		<input name="resultat" type="hidden" value="bon" />
		<input name="securite" type="hidden" value="" />
		';
		
		$rows.='<div style="margin-left:15em"><input class="submit ui-corner-all" type="submit" name="send" value="'.ucfirst(LABEL_Enviar).'" /></div>
		</fieldset>
	</form></div>';
	
	return $rows;
};
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php echo $install_message[101]; ?></title>

  <link rel="stylesheet" href="../common/css/install.css" type="text/css" media="all" />
  <link type="image/x-icon" href="../common/images/tematres.ico" rel="icon" />
  <link type="image/x-icon" href="../common/images/tematres.ico" rel="shortcut icon" />
		
		<script type="text/javascript">
			function test_password() {
			var securite = 0 ;
			document.formulaire.mdp.value = document.formulaire.mdp.value.replace(/[^A-Za-z0-9_-]/g,"_") ;
			document.formulaire.resultat.value = "rien" ;
			if ( document.formulaire.mdp.value.match(/[A-Z]/) ) securite++ ;
			if ( document.formulaire.mdp.value.match(/[a-z]/) ) securite++ ;
			if ( document.formulaire.mdp.value.match(/[0-9]/) ) securite++ ;
			//document.formulaire.resultat.value = document.formulaire.saisie.value.length ;
			var longueur = document.formulaire.mdp.value.length ;
			if ( longueur >= 4 ) securite++ ;
			if ( longueur >= 6 ) securite++ ;
			if ( longueur >= 8 ) securite++ ;

			//document.formulaire.securite.value = securite + " (de 1 Ã  6)";
			document.img_secu.width = securite * 9 *securite + 10 ;
			switch ( securite ) {
			case 0 :
				document.img_secu.src = "../common/images/sec0.png" ;
				break ;
			case 1 :
				document.img_secu.src = "../common/images/sec1.png" ;
				break ;
			case 2 :
				document.img_secu.src = "../common/images/sec1.png" ;
				break ;
			case 3 :
				document.img_secu.src = "../common/images/sec2.png" ;
				break ;
			case 4 :
				document.img_secu.src = "../common/images/sec3.png" ;
				break ;
			case 5 :
				document.img_secu.src = "../common/images/sec3.png" ;
				break ;
			case 6 :
				document.img_secu.src = "../common/images/sec4.png" ;
				break ;
			default :
				document.img_secu.src = "../common/images/sec1.png" ;
			}
		}
		
		</script>
	<script type="text/javascript" src="../common/jq/lib/jquery-1.8.0.min.js"></script>
	
	<script type="text/javascript" src="../common/forms/jquery.validate.min.js"></script>

	<script type="text/javascript">$("#formulaire").validate({});</script>
	
	<?php
	if($lang!=='en')
		echo '<script src="../common/forms/localization/messages_'.$lang.'.js" type="text/javascript"></script>';
	?>
	
	<script id="install_script" type="text/javascript">
		$(document).ready(function() {
		var validator = $("#formulaire").validate({
			rules: {'title':  {required: true},
					'author':  {required: true},
					'mail':  {required: true,email: true},
					'mdp': { required:true, minlength: 5},
					'repass': {equalTo: "#mdp"}
					},
					debug: true,
					errorElement: "label",
					 submitHandler: function(form) {
					   form.submit();
					 }
					
			});	
		});
	</script>		
	</head>
<body onload="test_password();">

	
    <div id="header">
	<h1><?php echo $install_message[101];?></h1>
	</div>	
<div id="bodyText">

		<div id="select_lang">
		<form class="myform" action="install.php" method="get" name="lang" id="lang">
		<label for="lang_install"><?php echo LABEL_Idioma;?></label>
		<select id="lang_install" name="lang_install" onChange="document.lang.submit()">
		<option><?php echo LABEL_Idioma;?></option>
		<option value="ca">català</option>
		<option value="en">english</option>
		<option value="es">español</option>
		<option value="fr">fran&ccedil;ais</option>
		<option value="pt">portug&uuml;&eacute;s</option>
		</select>
		</form>
		</div>

	<div class="clear">
	<?php	
	echo checkInstall($lang);
	?>
	</div>
    <!-- ###### Footer ###### -->
    </div>

    <div>
    <div id="footer" style="height: 50px;">  <!-- NB: outer <div> required for correct rendering in IE -->
    <div  style="float: left;
    width: 70px;">
      <a href="http://r020.com.ar/tematres/" title="TemaTres: vocabulary server"><img src="common/images/tematresfoot.jpg"  alt="TemaTres" /></a>
    </div>
      <div>
        <strong>TemaTres</strong>
        <span class="footerCol2"> Vocabulary Server</span>
      </div>

      <div>
        <strong><?php echo LABEL_URI ?>: </strong>
        <span class="footerCol2"> <?php echo str_replace("install.php","","http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']);?></span>
      </div>
      
           
    </div></div>
  </body>
</html>
