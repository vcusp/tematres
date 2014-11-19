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
<!DOCTYPE html>
<html lang="<?php echo LANG;?>">

<head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php echo $metadata["metadata"]; ?>
	<link type="image/x-icon" href="http://www.producao.usp.br/themes/BDPI/images/faviconUSP.ico" rel="icon" />
	<link type="image/x-icon" href="http://www.producao.usp.br/themes/BDPI/images/faviconUSP.ico" rel="shortcut icon" />
	<link rel="stylesheet" href="<?php echo T3_WEBPATH;?>css/style.css" type="text/css" media="screen" />
	<!-- <link rel="stylesheet" href="<?php echo T3_WEBPATH;?>css/print.css" type="text/css" media="print" /> -->

	<script type="text/javascript" src="<?php echo T3_WEBPATH;?>jq/lib/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="<?php echo T3_WEBPATH;?>jq/jquery.autocomplete.js"></script>   
	<script type="text/javascript" src="<?php echo T3_WEBPATH;?>jq/jquery.mockjax.js"></script>
	<script type="text/javascript" src="<?php echo T3_WEBPATH;?>jq/tree.jquery.js"></script>
	
	<link rel="stylesheet" type="text/css" href="<?php echo T3_WEBPATH;?>css/jquery.autocomplete.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo T3_WEBPATH;?>css/jqtree.css" />
        
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="<?php echo T3_WEBPATH;?>bootstrap/css/vcusp-theme.css">

	<!-- Bootstrap JS -->
	<script type='text/javascript' src='http://www.producao.usp.br/jspui/static/js/bdpi/bdpi.min.js'></script>
	
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
<!--uspbarra - ínicio -->
	<div id="uspbarra" style="background-color:transparent;border-style:none">
		<div class="uspLogo"  style="background-color:transparent;border-style:none">
			<img class="img-responsive" onclick="javascript:window.open('http://www.usp.br');" alt="USP" style="cursor:pointer;position: absolute;bottom: 0px;" src="../common/images/Logo_usp_composto.jpg" />
		</div>
		<div class="panel-group" id="accordion"  style="background-color:transparent;border-style:none">
			<div class="panel" style="background-color:transparent;border-style:none">
                                     <div id="collapseThree" class="panel-collapse collapse" style="background-color:transparent">
                                                        <div class="panel-body usppanel" style="background-color:#b3b3bc">
                                                            <div class="row" style="background-color:transparent">
                                                                <div class="col-md-3 text-center">
                                                                    <a href=http://www.usp.br/sibi/><img src="http://www.producao.usp.br/a/barrausp/images/sibi.png" title="SIBi - Sistema Integrado de Bibliotecas da USP" width=150 height=69 border=0 /></a>
                                                                    <div class="uspmenu_top_usp">
                                                                        <ul>
                                                                            <li><a href="http://www.producao.usp.br/a/barrausp/barra/creditos.html" target=_blank>Créditos</a></li>
                                                                            <li><a href="http://www.producao.usp.br/a/barrausp/barra/contato.html" target=_blank>Fale com o SIBi</a></li>
                                                                            <div><img src="http://www.producao.usp.br/a/barrausp/images/spacer.gif" width=10 height=10 /></div>
                                                                            <div class="panel-heading">PORTAL DE BUSCA INTEGRADA</div>
                                                                            <div class="panel-body">Um único ponto de acesso a todos os conteúdos informacionais disponíveis para a comunidade USP.</div>
                                                                            <br />
                                                                            <form class="form-inline" role="form" method="get" name="busca" action="http://www.buscaintegrada.usp.br/primo_library/libweb/action/search.do" onsubmit="if (document.getElementById(\'mySearch\').value==\'Busca geral...\'||document.getElementById(\'mySearch\').value==\'\'){alert(\'Preencha o campo de busca!\ return false;} else {return true;}" >
                                                                                <input type hidden name="dscnt" value="0">
                                                                                <input type hidden name="frbg" value="">
                                                                                <input type hidden name="scp.scps" value=\'scope:("USP"),primo_central_multiple_fe\'>
                                                                                <input type hidden name="tab" value="default_tab" >
                                                                                <input type hidden name="dstmp" value="1330609813304" >
                                                                                <input type hidden name="srt" value="rank" >
                                                                                <input type hidden name="ct" value="search" >
                                                                                <input type hidden name="mode" value="Basic" >
                                                                                <input type hidden name="dum" value="true" >
                                                                                <input type hidden name="indx" value="1" >
                                                                                <input type hidden name="tb" value="t" >
                        <input type hidden name="fn" value="search" >
                            <input type hidden name="vid" value="USP" >
                                <div class="form-group">
                                    <input type="text" name="vl(freeText0)" id="mySearch" size=22  value="Busca geral..."  onfocus="this.value = ''" tabindex=1 />
                                </div>
                                <div class="form-group">    
                                    <input type="submit" value="Buscar" tabindex=2 >
                                </div>            

                                </form>                                
                                </ul>
                                </div>
                                </div>
                                <div class="col-md-3">
                                    <ul class="uspmenu_top_usp">
                                        <div class="panel-heading">BIBLIOTECAS USP</div>
                                        <li><a href="http://www.bibliotecas.usp.br/lista.htm" target="_blank">Lista alfabética</a></li>
                                        <li><a href="http://www.sibi.usp.br/30anos" target="_blank">SIBiUSP 30 Anos</a></li>
                                    </ul>
                                    <ul class="uspmenu_top_usp">
                                        <ul class="uspmenu_top_usp">
                                            <div class="panel-heading">PRODUTOS E SERVIÇOS</div>
                                            <li><a href="http://www.acessoaberto.usp.br/" target="_blank">Acesso Aberto</a></li>
                                            <li><a href="http://dedalus.usp.br/" target="_blank">Acesso ao catálogo Dedalus</a></li>
                                            <li><a href="http://www.buscaintegrada.usp.br" target="_blank">Portal de Busca Integrada</a></li>
                                            <li><a href="http://www.sibi.usp.br/Vocab/" target="_blank">Vocabulário Controlado</a></li>
                                            <li><a href="http://workshop.sibi.usp.br/index.php"  target="_blank">Writing Center - WorkShops</a></li>
                                        </ul>
                                    </ul>
                                </div>
                                <div class="col-md-3">
                                    <ul class="uspmenu_top_usp">	
                                        <div class="panel-heading">BIBLIOTECAS DIGITAIS</div>
                                        <li><a href="http://bore.usp.br" target="_blank">Obras Raras e Especiais</a></li>
                                        <li><a href="http://revistas.usp.br" target="_blank">Portal de Revistas</a></li>
                                        <li><a href="http://www.producao.usp.br" target="_blank">Biblioteca Digital da Produção Intelectual (BDPI)</a></li>
                                    </ul>
                                    <ul class="uspmenu_top_usp">
                                        <div class="panel-heading">PARCERIAS INTERNAS</div>
                                        <li><a href="http://repositorio.iau.usp.br" target="_blank">Repositório Digital IAU</a></li>
                                        <li><a href="http://www.brasiliana.usp.br" target="_blank">Biblioteca Digital Brasiliana</a></li>
                                        <li><a href="http://www.ieb.usp.br/catalogo_eletronico" target="_blank">Biblioteca Digital do IEB</a></li>
                                        <li><a href="http://www.mapashistoricos.usp.br" target="_blank">Cartografia Histórica</a></li>
                                        <li><a href="http://www.teses.usp.br" target="_blank">Teses/Dissertações</a></li>

                                    </ul>                        
                                </div>
                                <div class="col-md-3">
                                    <ul class="uspmenu_top_usp">
                                        <div class="panel-heading">PARCERIAS EXTERNAS</div>
                                        <li><a href="http://regional.bvsalud.org/php/index.php" target="_blank">BVS em Saúde</a></li>
                                        <li><a href="http://enfermagem.bvs.br/php/index.php" target="_blank">BVS em Enfermagem</a></li>
                                        <li><a href="http://odontologia.bvs.br" target="_blank">BVS em Odontologia</a></li>
                                        <li><a href="http://www.bvs-psi.org.br"  target="_blank">BVS Psicologia Brasil</a></li>
                                        <li><a href="http://www.saudepublica.bvs.br/php/index.php" target="_blank">BVS em Saúde Pública</a></li>
                                        <li><a href="http://www.bvmemorial.fapesp.br" target="_blank">Biblioteca Virtual da América Latina</a></li>
                                        <li><a href="http://www.bv.fapesp.br" target="_blank">Biblioteca Virtual da FAPESP</a></li>
                                        <li><a href="http://ppegeo.igc.usp.br/scielo.php" target="_blank">PaGEO (Geociências)</a></li>
                                        <li><a href="http://www.periodicos.capes.gov.br" target="_blank">Portal CAPES</a></li>
                                        <li><a href="http://www.scielo.org/php/index.php?lang=pt" target="_blank">SciELO</a></li>
                                    </ul>                        
                                </div>                    
                                </div>
                                </div>
                                </div>
                <div class="usptab" style="border-style:none;background-color:transparent;" style="position:relative;">
                    <ul class="usplogin" style="border-style:none;" >
                        <li class="uspleft" style="position:relative; z-index:0"></li>
                        <li id="usptoggle" style="position:relative; z-index:0">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" id="uspopen" class="uspopen" border="0" style="display: block;">
                                <img src="http://www.producao.usp.br/a/barrausp/images/seta_down.jpg" border="0">
                                    <img src="http://www.producao.usp.br/a/barrausp/images/barrinha.png" alt="SIBi - Abrir o painel" width="35" height="16" border="0" title="SIBi - Abrir o painel">
                                        </a>
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" id="uspclose" style="display: none;" class="uspclose" border="0">
                                            <img src="http://www.producao.usp.br/a/barrausp/images/seta_up.jpg" border="0">
                                                <img src="http://www.producao.usp.br/a/barrausp/images/barrinha.png" width="35" height="16" border="0" title="SIBi - Fechar painel" alt="SIBi - Fechar painel">
                                                    </a>
                        </li>
                        <li class="uspright" style="background-color:transparent" style="position:relative; z-index:0; display:visible"></li>
                                                    </ul>
                                                    </div> </div>              
				</div>
	</div>
<!-- uspbarra - fim -->  

<div class="container">
	<div class="row">
	<div class="col-md-8">
		<div class="logo">
			<h1><a href="index.php" title="<?php echo $_SESSION[CFGTitulo].': '.MENU_ListaSis;?> "><?php echo $_SESSION[CFGTitulo];?></a></h1>
		</div>
	</div>
	<div class="col-md-4">
		<address>
		<strong>Departamento Técnico do Sistema Integrado de Bibliotecas da USP</strong><br>
			Rua da Biblioteca, S/N - Complexo Brasiliana<br>
			05508-050 - Cidade Universitária, São Paulo, SP - Brasil<br>
			<abbr title="Phone">Tel:</abbr> (0xx11) 3091-4439<br>
			<strong>E-mail:</strong> <a href="mailto:#">atendimento@sibi.usp.br</a>
		</address>
	</div>
</div>
	
	<div id="arriba"></div>
			<header class="navbar navbar-inverse" role="navigation">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
					<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation" >
						<ul class="nav navbar-nav" >
							<li class="active"><a title="<?php echo MENU_Inicio;?>" href="index.php"><span class="glyphicon glyphicon-home"></span> <?php echo MENU_Inicio;?></a></li>
							<?php
								//hay sesion de usuario
								if($_SESSION[$_SESSION["CFGURL"]][ssuser_nivel]){
								echo HTMLmainMenu();
				//no hay session de usuario
				}else{
				?>
					 <li>
                                            <!-- Button trigger modal -->
                                            <a type="button" data-toggle="modal" data-target="#login"><?php echo MENU_MiCuenta;?></a>
                                            <!-- Modal -->
                                            <div class="modal" id="login" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                              <div class="modal-dialog">
                                                <div class="modal-content">
                                                  <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Login</h4>
                                                  </div>
                                                  <div class="modal-body">
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
                                                  </div>
                                                  <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>                                                    
                                                  </div>
                                                </div>
                                              </div>
                                            </div> 
				<?php
				};
				?>
						</ul>
						<ul class="nav navbar-nav navbar-right">
                                                    <li><a title="<?php echo LABEL_busqueda;?>" href="index.php?xsearch=1"><?php echo ucfirst(LABEL_BusquedaAvanzada);?></a></li>
                                                    <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Idioma <span class="caret"></span></a>
                                                        <ul class="dropdown-menu" role="menu">
                                                          <li><a href="?setLang=pt">Português</a></li>
                                                          <li><a href="?setLang=en">English</a></li>
                                                          <li><a href="?setLang=es">Español</a></li>
                                                        </ul>
                                                    </li>
                                                    <li><a title="<?php echo MENU_Sobre;?>" href="sobre.php"><?php echo MENU_Sobre;?></a></li>                                                 
						</ul>
						<!-- Search Box -->
							<form method="get" id="simple-search" name="simple-search" action="index.php" class="navbar-form navbar-right" onsubmit="return checkrequired(this)">
							<div class="form-group">
								<input type="text" id="query" class="form-control" name="<?php echo FORM_LABEL_buscar;?>" size="25" value=""/>
							</div>
							<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button>
							</form>
					</nav>
			</header>
			
			
<!-- body, or middle section of our header -->   

<!-- ###### Body Text ###### -->

<div class="row">
<div class="col-md-12">
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Apresentação</h3>
  </div>
  <div class="panel-body">
    <p>O Vocabulário Controlado USP, é uma lista de assuntos utilizada para a indexação de recursos de informação no Banco de Dados Bibliográficos da USP  DEDALUS. O Vocabulário abrange as áreas do conhecimento inerentes às atividades de ensino, pesquisa e extensão da Universidade de São Paulo, e é constituído de termos de entrada, entre os autorizados para indexação, os termos "não-autorizados", que operam como remissivas, e os elos "falsos", os quais apenas agrupam termos mais específicos.</p>
    <p>Devido à diversificação e abrangência dos termos incluídos, referentes às várias áreas do conhecimento, o Vocabulário poderá ser utilizado para a representação do conteúdo de documentos de diferentes sistemas de informação, mas, sua utilidade deverá ser maior em bases de dados bibliográficos de instituições de ensino superior.</p>
    <p>O Vocabulário pode ser consultado pela sua Macroestrutura, que contém as relações lógico-semânticas explícitas entre as áreas, subáreas e a terminologia propriamente dita. Está disponível também a Lista Alfabética de Assuntos e a Lista Sistemática (Hierárquica), ambas complementadas por: Tabela de Qualificadores (termos utilizados em combinação com a Lista) , Tabela de Locais Geográficos e Históricos, de Gênero e Forma, Profissões e Ocupações, que propiciam as condições de complementação dos assuntos.</p>
    <p>O Vocabulário Controlado USP poderá ser utilizado pelas unidades de informação para indexar suas coleções por meio de consulta nessa interface (Website); no entanto, não estará disponível para exportação para sistemas externos à USP. Aproveitamos para informar que o SIBi/USP não estará mais editando, a partir de 2003, o vocabulário em suporte CD-ROM.</p>
  </div>
  <div class="panel-heading">
    <h3 class="panel-title">Histórico e Metodologia do Projeto</h3>
  </div>
  <div class="panel-body">
<p>A informatização dos recursos bibliográficos da USP teve início em 1985, com a criação de um catálogo global do acervo de todas as bibliotecas da instituição, destinado a acesso on-line, mais tarde denominado Banco de Dados Bibliográficos da USP  DEDALUS. Para a representação temática desse acervo, embora cada biblioteca já utilizasse terminologias consagradas de cada especialidade em seus catálogos tradicionais, foi necessário desenvolver uma linguagem de tratamento comum para o Banco, denominada Lista de Assuntos USP, que contava inicialmente com cerca de 8.000 entradas. Entre 1988 e 1989 foram incluídos novos termos à lista, totalizando 8.300 cabeçalhos autorizados.</p>
<p>Em dezembro de 1992, o Departamento Técnico do SIBi realizou um Workshop interno, com o objetivo de iniciar a modernização do Sistema de Bibliotecas da USP. Nesse evento, foram definidas as atividades prioritárias para o Sistema, figurando dentre elas o aprimoramento do Banco DEDALUS. Em março de 1993 foi constituída equipe específica para essa atividade, composta inicialmente por 8 bibliotecários de Unidades da USP, com a atribuição de expandir e atualizar a Lista de Assuntos então existente.</p>
<p>A primeira tarefa da equipe consistiu em planejar e organizar os diversos aspectos do trabalho. Nesse contexto, foi proposta pelo Departamento Técnico do SIBi uma parceria com o Departamento de Biblioteconomia e Documentação da Escola de Comunicações e Artes da USP(CBD/ECA/USP), para a orientação metodológica da estruturação do Vocabulário. A seguir, foi efetuado um primeiro estudo da Lista de Assuntos, com a colaboração de bibliotecários de várias Unidades da USP vinculados às atividades de Processamento Técnico e de Cadastramento de Monografias no Banco DEDALUS.</p>
<p>Com a finalidade de capacitar o grupo para a tarefa, foi realizado, simultaneamente, o curso "Princípios de Compatibilização de Linguagens Documentárias", ministrado no DT/SIBi, pelas Docentes Anna Maria Marques Cintra, Maria de Fátima Gonçalves Moreira Tálamo, Marilda Lopes Ginez de Lara e Nair Yumiko Kobashi, do CBD/ECA/USP, e Mariângela Lopes Fujita, da UNESP, com a participação de cerca de 50 bibliotecários. Estabeleceu-se, com esse curso, um patamar comum de conhecimentos e de procedimentos metodológicos compartilhado pelo grupo. Ao término do referido curso, foi elaborado o "Projeto para Aprimoramento da Lista de Assuntos USP" .</p>
<p>Para o desenvolvimento do referido projeto, houve a participação efetiva de cerca de 40 bibliotecários do Sistema, bem como a valiosa colaboração de docentes das várias Unidades da USP, na estruturação dos sistemas conceituais e adequação terminológica das áreas contempladas pelo Vocabulário. Por sua vez, os docentes integrantes da linha de pesquisa em Análise Documentária e Terminologia do CBD/ECA/USP ofereceram o aporte metodológico ao projeto.</p>
<p>Além do acompanhamento do Departamento Técnico do SIBi/USP, destacam-se a atividade de coordenação técnica, feita por uma bibliotecária do Sistema, e a coordenação metodológica de uma docente do CBD/ECA/USP. Para a organização final dos originais e a criação de base de dados, com o objetivo de tornar disponível o Vocabulário no Banco DEDALUS, houve a participação da equipe de bibliotecários, analistas de sistemas e técnicos do Departamento Técnico do SIBi/USP.</p>
<h3>Metodologia</h3>
<p>As Bibliotecas da USP caracterizam-se pela descentralização e especialização de seus acervos. O acesso global a esse fundo informacional requer, desse modo, uma linguagem comum de representação temática, que contemple a convivência, em cada acervo, de itens bibliográficos gerais (presentes nas várias bibliotecas do Sistema), voltados para o ensino de graduação, e de itens especializados (igualmente presentes nas várias bibliotecas), que respondem às demandas do ensino de pós-graduação e da produção de conhecimento pelas diferentes linhas de pesquisa.</p>
<p>Para enfrentar esse desafio, optou-se pela criação de um vocabulário unificado, tendo como ponto de partida as linguagens efetivamente utilizadas pelas bibliotecas do sistema. A elaboração de um Vocabulário controlado fundamentou-se no princípio de que um instrumento dinâmico, capaz de ser atualizado de forma criteriosa, requer uma estrutura de relações lógico-semânticas explícitas entre as áreas, subáreas e a terminologia propriamente dita, em seus diferentes níveis e a apresentação de regras de utilização igualmente explícitas e compartilhadas.</p>
<p>Para assegurar a realização do projeto, foram estabelecidas metas claras, procedimentos sistemáticos, controlados e submetidos a ajustes periódicos e, principalmente, contou-se com equipe sintonizada com os objetivos globais do trabalho. Nesse sentido, foram adotados os seguintes procedimentos organizacionais e metodológicos:</p>
<ol type="a">
    <li>Organização das bibliotecas da USP em nove sub-grupos (Quadro I)</li>
    <li>Elaboração da estrutura temática de cada área e compatibilização das estruturas por sub-grupos</li>
    <li>Inclusão dos blocos de assuntos, gerados em ordem hierárquica, na estrutura temática unificada</li>
    <li>Estabelecimento de relações lógico-semânticas entre os termos</li>
    <li>Definição dos termos ambíguos (em ficha terminológica) e compatibilização das estruturas temáticas dos sub-grupos com as áreas complementares</li>
</ol>
<h4>Organização das bibliotecas da USP em nove sub-grupos (Quadro I)</h4>
<p>Ver Quadro I</p>
<h4>Elaboração da estrutura temática de cada área e compatibilização das estruturas por sub-grupos</h4>
<p>As estruturas temáticas de cada área foram elaboradas a partir dos descritores existentes nos catálogos locais das bibliotecas e da consulta a diversos tipos de fontes de referência: tesauros já existentes, sistemas de classificação, dicionários especializados, coleções básicas de cada área, estruturas curriculares, linhas de pesquisa das Unidades e especialistas da Universidade nas áreas do conhecimento consideradas. A partir da estrutura preparada pelas bibliotecas, procurou-se elaborar em cada sub-grupo uma estrutura unificada. Foram construídas, inicialmente, em torno de 50 estruturas temáticas relacionadas às disciplinas científicas. Dentre elas, a Biblioteca do Instituto de Química elaborou as estruturas temáticas de Química e de Farmácia; a Faculdade de Filosofia elaborou várias estruturas temáticas, referentes à Antropologia, Sociologia, História, Letras, como segue:</p>
<div class="table-responsive">
  <table class="table table-bordered">
      <thead>
        <tr>
          <th>Sub-Grupo</th>
          <th>Áreas Temáticas</th>
          <th>Bibliotecas Participantes</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>Filosofia, Ciência Política, Antropologia, Sociologia, História, História do Brasil, Museologia, Geografia, Letras e Lingüística.</td>
          <td>
              <ul>
                  <li>Fac. Filosofia, Letras e Ciências Humanas</li>
                  <li>Instituto de Estudos Brasileiros</li>
                  <li>Museu Paulista</li>
              </ul>
          </td>
        </tr>
        <tr>
          <td>2</td>
          <td>Economia, Direito, Administração, Contabilidade</td>
          <td>
              <ul>
                  <li>Faculdade de Direito</li>
                  <li>Faculdade de Economia e Administração</li>
              </ul>              
          </td>
        </tr>
        <tr>
          <td>3</td>
          <td>Educação, Psicologia</td>
          <td>
              <ul>
                  <li>Faculdade de Educação</li>
                  <li>Instituto de Psicologia</li>
              </ul>
          </td>
        </tr>
        <tr>
          <td>4</td>
          <td>Artes, Arquitetura e Urbanismo, Comunicações</td>
          <td>
              <ul>
                  <li>Escola de Comunicações e Artes</li>
                  <li>Faculdade de Arquitetura e Urbanismo</li>
                  <li>Museu de Arte Contemporânea</li>
              </ul>
          </td>
        </tr>
        <tr>
          <td>5</td>
          <td>Geologia, Química, Bioquímica, Farmácia, Matemática, Estatística, Astronomia, Geofísica e Física</td>
          <td>
              <ul>
                  <li>Instituto Astronômico e Geofísico</li>
                  <li>Instituto de Física</li>
                  <li>Instituto de Física de São Carlos</li>
                  <li>Instituto de Geociências</li>
                  <li>Instituto de Matemática e Estatística</li>
                  <li>Instituto de Química e Faculdade de Ciências Farmacêuticas (Conjunto das Químicas)</li>       
                  <li>Instituto de Química de São Carlos</li>                     
              </ul>
          </td>
        </tr>
        <tr>
          <td>6</td>
          <td>Medicina, Saúde Pública, Nutrição, Enfermagem, Educação Física e Esportes</td>
          <td>
              <ul>
                  <li>Escola de Educação Fìsica e Esportes</li>
                  <li>Escola de Enfermagem</li>
                  <li>Faculdade de Medicina</li>
                  <li>Faculdade de Saúde Pública</li>
                  <li>Unidades do Campus de Ribeirão Preto (Biblioteca Central)</li>           
              </ul>
          </td>
        </tr>
        <tr>
          <td>7</td>
          <td>Biologia, Botânica, Oceanografia, Zoologia, Medicina Veterinária, Zootecnia e Ciências Agrárias</td>
          <td>
              <ul>
                  <li>Escola Superior de Agricultura "Luiz de Queiroz"</li>
                  <li>Faculdade de Medicina Veterinária e Zootecnia</li>
                  <li>Instituto de Biociências</li>
                  <li>Instituto de Ciências Biomédicas</li>
                  <li>Instituto Oceanográfico</li>
                  <li>Museu de Zoologia</li>         
              </ul>
          </td>
        </tr>
        <tr>
          <td>8</td>
          <td>Engenharia</td>
          <td>
              <ul>
                  <li>Escola de Engenharia de São Carlos</li>
                  <li>Escola Politécnica</li>
              </ul>
          </td>
        </tr>
        <tr>
          <td>9</td>
          <td>Odontologia</td>
          <td>
              <ul>
                  <li>Faculdade de Odontologia</li>
                  <li>Faculdade de Odontologia de Bauru</li>
              </ul>
          </td>
        </tr>        
      </tbody>
  </table>
 </div>   
<h4>Inclusão dos blocos de assuntos, gerados em ordem hierárquica, na estrutura temática unificada</h4>
<p>Foi elaborado pela equipe do DT/SIBi um programa de computador (com o respectivo manual de procedimentos) para o desenvolvimento das atividades de compatibilização. Este programa permitiu registrar a coleta dos assuntos usados em cada biblioteca e sua correspondência com a Lista de Assuntos USP então vigente. Após a compatibilização das estruturas temáticas por sub-grupo, os dados foram integrados à estrutura temática unificada, a fim de serem estabelecidas as relações lógico-semânticas entre os termos.</p>
<p>A partir desse processo de inclusão, foi gerada uma listagem em ordem alfabética com a finalidade de consolidar os termos (descritores) a serem efetivamente utilizados. Cada termo foi identificado por uma sigla indicadora da Unidade USP quanto à sua proveniência.</p>
<h4>Estabelecimento de relações lógico-semânticas entre os termos</h4>
<p>A lista global de termos obtida foi inicialmente organizada em ordem alfabética e analisada para proceder à eliminação de redundâncias e aos ajustes necessários. Foram determinados, em seguida, os termos preferenciais, sendo os sinônimos ou quase-sinônimos mantidos como remissivas. Para a normalização dos termos (homogeneidade formal e univocidade da relação termo-conceito), introduziram-se qualificadores, notas de escopo, operadores de equivalência (VER), com base nas normas e diretrizes de construção de vocabulários documentários</p>
<h4>Definição dos termos ambíguos (em ficha terminológica) e compatibilização das estruturas temáticas dos sub-grupos com as áreas complementares</h4>
<p>No processo de construção do Vocabulário USP, verificou-se a necessidade de refinar as relações lógico-semânticas entre os termos e, ao mesmo tempo, acrescentar modificadores para eliminar as ambigüidades.</p>
<p>A definição dos termos conferiu rigor ao processo de compatibilização das estruturas temáticas dos sub-grupos com as áreas complementares. Evitou-se, desse modo, manter redundâncias indesejáveis que comprometessem a economia do sistema.</p>
<p>A lista alfabética obtida foi editorada e encaminhada a cada integrante do grupo para proceder à reestruturação hierárquica da sua área, com o uso dos seus termos específicos.</p>
<p>As listagens hierárquicas foram então analisadas em conjunto pelos coordenadores do trabalho. A seguir, foram submetidas à apreciação das bibliotecas, para que se procedesse à codificação alfa-numérica dos termos, de acordo com a Macroestutura estabelecida para o Vocabulário.</p>
<h3>Organização</h3>
<p>As hierarquias de termos foram definidas por áreas do conhecimento e agrupadas de acordo com a Macroestrutura (Quadro II).</p>
<div class="table-responsive">
  <table class="table table-bordered">
      <thead>
        <tr>
          <th>Grandes Áreas</th>
          <th>Áreas</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>CA100 CIÊNCIAS AGRÁRIAS</td>
          <td>
              <ul>
                  <li>CA110 AGRONOMIA</li>
                  <li>CA120 ENGENHARIA DE PESCA</li>
              </ul>
          </td>
        </tr>
        <tr>
          <td>CB200 BIOCIÊNCIAS</td>
          <td>
              <ul>
                  <li>CB210 BIOLOGIA</li>
                  <li>CB220 BOTÂNICA</li>
                  <li>CB230 IMUNOLOGIA</li>
                  <li>CB240 MICROBIOLOGIA</li>  
                  <li>CB250 ZOOLOGIA</li>              
              </ul>
          </td>
        </tr>
        <tr>
          <td>CB300 CIÊNCIAS DA SAÚDE</td>
          <td>
              <ul>
                  <li>CB310 EDUCAÇÃO FÍSICA E ESPORTE</li>
                  <li>CB320 ENFERMAGEM</li>
                  <li>CB330 MEDICINA</li>
                  <li>CB340 NUTRIÇÃO</li>  
                  <li>CB350 ODONTOLOGIA</li>
                  <li>CB360 PSICOLOGIA</li>
                  <li>CB370 SAÚDE PÚBLICA</li>
                  <li>CB380 FARMÁCIA E COSMETOLOGIA</li>  
                  <li>CB390 FONOAUDIOLOGIA</li>                     
              </ul>
          </td>
        </tr>
        <tr>
          <td>CB400 MEDICINA VETERINÁRIA E ZOOTECNIA</td>
          <td>
              <ul>
                  <li>CB410 MEDICINA VETERINÁRIA</li>
                  <li>CB420 ZOOTECNIA</li>
              </ul>
          </td>
        </tr>
        <tr>
          <td>CE500 CIÊNCIAS EXATAS</td>
          <td>
              <ul>
                  <li>CE510 ASTRONOMIA</li>
                  <li>CE520 FÍSICA</li>
                  <li>CE530 GEOCIÊNCIAS</li>
                  <li>CE540 GEOFÍSICA</li>  
                  <li>CE550 MATEMÁTICA</li>
                  <li>CE560 QUÍMICA</li>                
              </ul>
          </td>
        </tr>
        <tr>
          <td>CE600 CIÊNCIAS EXATAS APLICADAS</td>
          <td>
              <ul>
                  <li>CE610 CIÊNCIA DA COMPUTAÇÃO</li>
                  <li>CE620 ENGENHARIA</li>
                  <li>CE630 ESTATÍSTICA E PROBABILIDADE</li>
                  <li>CE640 METEOROLOGIA</li>               
              </ul>
          </td>
        </tr>
        <tr>
          <td>CH700 CIÊNCIAS HUMANAS</td>
          <td>
              <ul>
                  <li>CH710 ADMINISTRAÇÃO, ECONOMIA, ECONOMIA DOMÉSTICA E CONTABILIDADE</li>
                  <li>CH720 ARQUEOLOGIA, MITOLOGIA E PRÉ-HISTÓRIA</li>
                  <li>CH730 ARQUITETURA, PLANEJAMENTO TERRITORIAL URBANO E HABITAÇÃO</li>
                  <li>CH740 ARTES E COMUNICAÇÕES</li>  
                  <li>CH750 CIÊNCIA DA INFORMAÇÃO E MUSEOLOGIA</li>
                  <li>CH760 DIREITO, FILOSOFIA, RELIGIÃO, CIÊNCIAS SOCIAIS E CIÊNCIA MILITAR</li>
                  <li>CH770 EDUCAÇÃO, LAZER E RECREAÇÃO</li>
                  <li>CH780 HISTÓRIA, HISTÓRIA DO BRASIL E GEOGRAFIA</li>  
                  <li>CH790 LINGUAGEM, LÍNGUAS, LINGUÍSTICA, TEORIA LITERÁRIA E LITERATURA</li>                     
              </ul>
          </td>
        </tr>          
      </tbody>
  </table>
 </div>   
</div>
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
<!-- ###### Footer ###### -->

    <div class="footer">
        <div class="well well-lg">
            <p>Desenvolvido com Tematres 1.81</p>                
            <p><?php echo LABEL_URI ?>: <span class="footerCol2"><a href="<?php echo $_SESSION["CFGURL"];?>"><?php echo $_SESSION["CFGURL"];?></a></span></p>
				<?php
				//are enable SPARQL
				if(CFG_ENABLE_SPARQL==1)
				{
					echo '<p><strong><a href="'.$_SESSION["CFGURL"].'sparql.php" title="'.LABEL_SPARQLEndpoint.'">'.LABEL_SPARQLEndpoint.'</a></strong></p>';
				}
                                
				if(CFG_SIMPLE_WEB_SERVICE==1)
				{
					echo '<p><a href="'.$_SESSION["CFGURL"].'services.php" title="API">API do WebService</a></p>';	
				}
				?>
			
    
  </div>
        </div>
 </body>
</html>
