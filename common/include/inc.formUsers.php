<?php
if ((stristr( $_SERVER['REQUEST_URI'], "session.php") ) || ( !defined('T3_ABSPATH') )) die("no access");
#   TemaTres : aplicación para la gestión de lenguajes documentales #       #
#                                                                        #
#   Copyright (C) 2004-2008 Diego Ferreyra tematres@r020.com.ar
#   Distribuido bajo Licencia GNU Public License, versión 2 (de junio de 1.991) Free Software Foundation
#  
###############################################################################################################
# Include para alta y modificacion de usuarios genericos.

if($_GET[user_id]=='new'){
	$nombre_boton=LABEL_Enviar;
	}else{
	$dato_user=ARRAYdatosUser($_GET[user_id]);

	$resumen=ARRAYresumen($_SESSION[id_tesa],"U","$dato_user[id]");

	$row_resumen.='<div id="cajaAncha">'."\n\r";
	$row_resumen.='  <div><strong>'.LABEL_Acciones.'</strong></div><dl class="dosCol">'."\n\r";

	if($resumen[cant_total]>0){
		$row_resumen.='<dt><a href="sobre.php?user_id='.$dato_user[id].'" title="'.LABEL_Terminos.'">'.ucfirst(LABEL_Terminos).'</dt><dd>'.$resumen[cant_total].'</a>&nbsp;</dd>'."\n\r";
		}else{
		$row_resumen.='<dt>'.ucfirst(LABEL_Terminos).'</dt><dd>'.$resumen[cant_total]."&nbsp;</dd>\n\r";
		};
	$row_resumen.='<dt>'.ucfirst(LABEL_RelTerminos).'</dt><dd>'.$resumen[cant_rel]."&nbsp;</dd>\n\r";
	$row_resumen.='<dt>'.ucfirst(LABEL_TerminosUP).'</dt><dd>'.$resumen[cant_up]."&nbsp;</dd>\n\r";
	$row_resumen.='</dl></div>';

    $nombre_boton=LABEL_Cambiar;
};
?>
<script id="demo" type="text/javascript">
$("#form-users").validate({
});
</script>
<h1><a href="admin.php?user_id=list" title="<?php echo LABEL_AdminUser;?>"><?php echo LABEL_AdminUser;?></a></h1>
        <?php echo $row_resumen;?>
                  <form name="login" id="form-users" class="myform" action="admin.php" method="post">
				<fieldset>
					<legend> <?php echo LABEL_DatosUser;?> </legend>

                        <div>
            <label for="<?php echo LABEL_nombre;?>" accesskey="n"><?php echo ucfirst(LABEL_nombre);?></label>
                <input name="<?php echo FORM_LABEL_nombre;?>"
                id="nombre"
                size="20" maxlength="150"
                value="<?php echo $dato_user[nombres];?>"
                />
                          </div>
                           <div>
            <label for="apellido" accesskey="a"><?php echo ucfirst(LABEL_apellido);?></label>
                <input  name="<?php echo FORM_LABEL_apellido;?>"
                id="apellido"
                size="20" maxlength="150"
                value="<?php echo $dato_user[apellido];?>"
                />
                        </div>
                        <div>
            <label for="mail" accesskey="l"><?php echo ucfirst(LABEL_mail);?></label>
                <input name="<?php echo FORM_LABEL_mail;?>"
                id="mail"
                size="20" maxlength="256"
                value="<?php echo $dato_user[mail];?>"
                />
                        </div>
                        <div >
            <label for="orga" accesskey="o"><?php echo ucfirst(LABEL_orga);?></label>
                <input name="<?php echo FORM_LABEL_orga;?>"
                id="orga"
                size="20" maxlength="256"
                value="<?php echo $dato_user[orga];?>"
                />
                        </div>
                        <div>
            <label for="clave" accesskey="c"><?php echo ucfirst(LABEL_pass);?></label>
                <input name="<?php echo FORM_LABEL_pass;?>" id="<?php echo FORM_LABEL_pass;?>"
                type="password"
                size="10" maxlength="10"
                value=""
                />
                        </div>
                        <div>
            <label for="reclave" accesskey="r"><?php echo ucfirst(LABEL_repass);?></label>
                <input name="<?php echo FORM_LABEL_repass;?>" id="<?php echo FORM_LABEL_repass;?>"
                type="password"
                value=""
                size="10" maxlength="10"
                />
                        </div>
                        
            <label for="isAdmin" accesskey="i"><?php echo ucfirst(LABEL_esSuperUsuario);?></label>
                <input type="checkbox"
                name="isAdmin"                
                id="isAdmin"
                value="1"                
                <?php echo arrayReplace(array("1","2"),array("checked",""),$dato_user[nivel])?>
                />
            <?php
            if(isset($dato_user[id]))
       
            {?>
               
            <label for="isAlive" accesskey="i"><?php echo ucfirst(LABEL_User_Habilitado);?></label>
                <input type="checkbox"
                name="isAlive"                
                id="isAlive"
                value="ACTIVO"      
                <?php
                echo ($dato_user[estado]=='ACTIVO') ? 'checked': '';
                ?>
                />
            <?php
			};    
			?>
                
          		<div class="submit_form" align="center">
				
				<input type="button"  
				name="cancelar" 
				id="boton_cancelar" 
				type="button" 
				class="submit ui-corner-all"  
				onClick="location.href='admin.php?user_id=list'" 
				value="<?php echo ucfirst(LABEL_Cancelar);?>"
				/>		
				
				<INPUT name="useactua"
                TYPE="HIDDEN"
                value="<?php echo $dato_user[id];?>"
                />
                <input name="boton"
                type="submit"
                class="submit ui-corner-all"  
                value="<?php echo $nombre_boton;?>"
                />
		</div>
          </fieldset>
		</form>
		
<script type="text/javascript">
	$(document).ready(function() {
	var validator = $("#form-users").validate({
		rules: {'<?php echo FORM_LABEL_nombre;?>':  {required: true},
		'<?php echo FORM_LABEL_apellido;?>':  {required: true},
		'<?php echo FORM_LABEL_mail;?>':  { required:true, email: true},
		'<?php echo FORM_LABEL_repass;?>': {equalTo: "#<?php echo FORM_LABEL_pass;?>",minlength: 4}
				},
				debug: true,
				errorElement: "label",
				 submitHandler: function(form) {
				   form.submit();
				 }
				
	});	
});
</script>

