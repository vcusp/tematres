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

	if(($_GET["action"]=='rp') && ($_GET["key"]))
	{
			$chek_key=check_password_reset_key($_GET["key"], urldecode($_GET["login"]));

			if($chek_key["user_id"]>0)
			{									
				$task_result=reset_password($chek_key);
			}			
	}
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
if ($_SESSION[$_SESSION["CFGURL"]]["ssuser_nivel"]>0) 
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
          <?php
            if($_SESSION[$_SESSION["CFGURL"]]["ssuser_id"]){
            require_once(T3_ABSPATH . 'common/include/inc.misTerminos.php');
            }else{
	?>
     <div id="bodyText">

	<?php
	if($_POST["task"]=='user_recovery')
	{
		$task_result=recovery($_POST["id_correo_electronico_recovery"]);		
	}
	
	
	if ($_GET["task"]=='recovery') 
	{
		echo HTMLformRecoveryPassword();	
	}
	else 
	{
		
		if(($_POST["task"]=='login') && (!$_SESSION[$_SESSION["CFGURL"]]["ssuser_id"])) 
		{
			$task_result=array("msg"=>t3_messages('no_user'));			
		}					
		echo HTMLformLogin($task_result);		
	};
	
	?>

<?php
            }
           ?>
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
<?php
/**
 * * From WordPress !!!
 * 
 * Retrieves a user row based on password reset key and login
 *
 * @uses $wpdb WordPress Database object
 *
 * @param string $key Hash to validate sending user's password
 * @param string $login The user login
 * @return object|WP_Error User's database row on success, error object for invalid keys
 */
function check_password_reset_key($key, $login) {

	$key = preg_replace('/[^a-z0-9]/i', '', $key);

	if ( empty( $key ) || !is_string( $key ) )
		return t3_messages('invalid_key');

	if ( empty($login) || !is_string($login) )
		return t3_messages('invalid_key');

	$ARRAYuser = ARRAYdatosUserXkey($login,$key);

	if ( empty( $ARRAYuser ) )
		return t3_messages('invalid_key');

	return $ARRAYuser;
}


function recovery($user_login)
{	
	
	GLOBAL $DBCFG;

	$ARRAYuser=array();
	$ARRAYuser=ARRAYdatosUserXmail($user_login);
	
	
	//El usuario no existe
	if(!$ARRAYuser["user_id"]) return array("result"=>false, "msg"=>t3_messages("no_user")); 

	if ( empty($ARRAYuser["user_activation_key"]) ) {
		// Generate something random for a key...
		$ARRAYuser["user_activation_key"] = wp_generate_password(20, false);

		// Now insert the new md5 key into the db	
		$sql_update_key=SQL("update","$DBCFG[DBprefix]usuario set user_activation_key='$ARRAYuser[user_activation_key]' where id='$ARRAYuser[user_id]'");	
	}


	$message = LABEL_mail_recovery_pass1. "\r\n\r\n";
	$message .= $_SESSION["CFGURL"]. "\r\n\r\n";
	$message .= sprintf(LABEL_mail_recovery_pass2, $ARRAYuser[mail]) . "\r\n\r\n";
	$message .= LABEL_mail_recovery_pass3. "\r\n\r\n";
	$message .= LABEL_mail_recovery_pass4. "\r\n\r\n";
	$message .= currentBasePage($_SESSION["CFGURL"]).'login.php?action=rp&key='.$ARRAYuser["user_activation_key"].'&login='.rawurlencode($ARRAYuser[mail])."\r\n";
	
	$title = sprintf('[%s] '.LABEL_mail_recoveryTitle, $_SESSION[CFGTitulo] );
	

	$sendMail=sendMail($ARRAYuser[mail], $title, $message);

	if ($sendMail)
	{
		return array("result"=>true, "msg"=>t3_messages("mailOK")); 
	}
	else 
	{
		array("result"=>false, "msg"=>t3_messages("mailFail")); 
	};
	
	return;
	
};


function reset_password($ARRAYuser){

	$string_pass = wp_generate_password( 12, false);
	
	//set password
	setPassword($ARRAYuser["user_id"],$string_pass,CFG_HASH_PASS);
		
	$message = LABEL_mail_pass1.' '.$ARRAYuser["mail"] . "\r\n\r\n";
	$message .= LABEL_mail_pass2.' '.$string_pass. "\r\n\r\n";
	$message .= LABEL_mail_pass3."\r\n\r\n";
	$message .= currentBasePage($_SESSION["CFGURL"]).'login.php'."\r\n";

	$title = sprintf('[%s] '.LABEL_mail_passTitle, $_SESSION["CFGTitulo"] );

	$sendMail=sendMail($ARRAYuser["mail"], $title, $message);
	
	if ($sendMail)
	{
		return array("result"=>true, "msg"=>t3_messages("mailOK")); 
	}
	else 
	{
		return array("result"=>false, "msg"=>t3_messages("mailFail")); 
	};

};


/**
 * * From WordPress !!!
 * 
 * Generates a random password drawn from the defined set of characters.
 *
 * @since 2.5
 *
 * @param int $length The length of password to generate
 * @param bool $special_chars Whether to include standard special characters. Default true.
 * @param bool $extra_special_chars Whether to include other special characters. Used when
 *   generating secret keys and salts. Default false.
 * @return string The random password
 **/
function wp_generate_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	if ( $special_chars )
		$chars .= '!@#$%^&*()';
	if ( $extra_special_chars )
		$chars .= '-_ []{}<>~`+=,.;:/?|';

	$password = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$password .= substr($chars, wp_rand(0, strlen($chars) - 1), 1);
	}

	return $password;
}


 /**
  * From WordPress !!!
 * Generates a random number
 *
 * @since 2.6.2
 *
 * @param int $min Lower limit for the generated number (optional, default is 0)
 * @param int $max Upper limit for the generated number (optional, default is 4294967295)
 * @return int A random number between min and max
 */
function wp_rand( $min = 0, $max = 0 ) {

	//sustitución de un valor global
	$rnd_value==3;

	// Reset $rnd_value after 14 uses
	// 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd_value
	if ( strlen($rnd_value) < 8 ) {
		$seed = srand();
		$rnd_value = md5( uniqid(microtime() . mt_rand(), true ) . $seed );
		$rnd_value .= sha1($rnd_value);
		$rnd_value .= sha1($rnd_value . $seed);
		$seed = md5($seed . $rnd_value);
	}

	// Take the first 8 digits for our value
	$value = substr($rnd_value, 0, 8);

	// Strip the first eight, leaving the remainder for the next call to wp_rand().
	$rnd_value = substr($rnd_value, 8);

	$value = abs(hexdec($value));

	// Reduce the value to be within the min - max range
	// 4294967295 = 0xffffffff = max random number
	if ( $max != 0 )
		$value = $min + (($max - $min + 1) * ($value / (4294967295 + 1)));

	return abs(intval($value));
}
?>
