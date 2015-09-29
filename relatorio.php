<!DOCTYPE HTML>
<?php
require_once('lib/l_nocahe.php');
if (isset($_GET['funcao'])){
	$funcao = $_GET['funcao'];
}

$de_aviso="";
$cd_tipo="";

require_once('lib/l_acesso.php');
//SE NÃO TIVER A SESSÃO DE ADMINISTRADOR LOGADO E NÃO PERTENCER AO NÍVEL DE ADMINISTRADOR ELE VOLTA PRO INDEX
if(!isset($_SESSION['logado_admin']) || $_SESSION['cd_nivel'] != 1)
{
	session_destroy();
	header("Location: index.php");
}
$cd_usuario_logado = $_SESSION['cd_usuario_logado'] ;
//SE CLICADO NO BOTÃO SAIR
if(isset($_POST['logout']))
{
	$_SESSION['cd_usuario']="";
	$_SESSION['de_senha']="";
	$_SESSION['de_usuario']="";
	$_SESSION['de_logotipo']="";
	$_SESSION['cd_nivel']="";

	session_destroy();
	setcookie("","",-1800);

	header("Location: index.php");
}
require_once('lib/l_conexao.php');
require_once('lib/l_utils.php');
require_once('lib/l_mostraDados.php');
require_once('lib/l_gravaDados.php');
require_once('lib/l_formCampoTexto.php');


if(cdbl("".$_GET['cd_enquete']) == 0){
	$cd_enquete = $_POST['cd_enquete'];
}else{
	$cd_enquete = $_GET['cd_enquete'];
}

if(cdbl("".$_GET['cd_pergunta']) == 0){
	$cd_pergunta = $_POST['cd_pergunta'];
}else{
	$cd_pergunta = $_GET['cd_pergunta'];
}

if(cdbl("".$_GET['cd_usuario_alterou']) == 0){
	$cd_usuario_alterou = $_POST['cd_usuario_alterou'];
}else{
	$cd_usuario_alterou = $_GET['cd_usuario_alterou'];
}

if (isset($_POST['dt_inicio'])){
	$dt_inicio = $_POST['dt_inicio'];
}
if (isset($_POST['dt_final'])){
	$dt_final = $_POST['dt_final'];
}


/*
 * Cria conexão com GTQuest
 */
$conn = db_conPDO();

?>
<html lang="pt-br">
<head>
	<meta charset="UTF-8" />
		<meta name="description" content="GTQuest" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="X-UA-Compatible" content="IE=11" />
		<title>GTQuest</title>
		<link rel="stylesheet" type="text/css" href="css/estilo.css" media="all">
		<link rel="shortcut icon" href="img/quest.png" type="image/png" />
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/bootstrap-responsive.css">

		<script type="text/javascript" charset="iso-8859-1" src="js/f_utils.js?t=1"></script>
		<script type="text/javascript" charset="iso-8859-1" src="js/f_ajaxload.js?t=1"></script>
		<script type="text/javascript" charset="iso-8859-1" src="js/calendar.js"></script>

		<script type="text/javascript" charset="iso-8859-1" src="js/jsapi.js"></script>
		<script type="text/javascript" charset="iso-8859-1" src="js/jquery-1.7.1.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/jquery-1.7.1.min.js"><\/script>')</script>
		<script type="text/javascript" src="js/bootstrap.js"></script>

		<script type="text/javascript">

		</script>
	</head>
	<?php


	if(isset($_POST['geraExcel']))
	{
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');

		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');

		/** Include PHPExcel */
		require_once dirname(__FILE__) . '/lib/PHPExcel18/Classes/PHPExcel.php';


		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		//Propriedades do documento
		$objPHPExcel->getProperties()->setCreator("Genialtec Soluções em Tecnologia")
									 ->setLastModifiedBy("Genialtec Soluções em Tecnologia")
									 ->setTitle("Relatório GTQuest")
									 ->setSubject("Relatório GTQuest")
									 ->setDescription("Relatório das enquetes respondidas pelos usuários no software GTQuest.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Relatório");

		try{
			$sql = " SELECT E.cd_enquete, E.de_enquete"; 
			$sql .= " FROM gt_enquete E";	
			$sql .= " WHERE E.fl_ocultar=0";
			if(cdbl($cd_enquete > 0))
			{
				$sql .= " AND E.cd_enquete= :cd_enquete";
			}

			//PREPARA A SQL
			$stmt = $conn->prepare($sql);
			if(cdbl($cd_enquete > 0))
			{
				$stmt->bindParam(':cd_enquete', $cd_enquete, PDO::PARAM_INT);
			}
			$stmt->execute();

			if($stmt->rowCount() > 0)
			{
				
				$i=0;
				while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
					$cont=1;
					$cd_enquete_aux = $row->cd_enquete;		
					$de_enquete_aux = $row->de_enquete;				
			    	// Criando uma nova planilha dentro do arquivo
					$objPHPExcel->createSheet();

					// Agora, vamos adicionar os dados na planinha da posição $i
					$objPHPExcel->setActiveSheetIndex($i);

					//TIRA A LINHA DE GRADE
					$objPHPExcel->getActiveSheet()->setShowGridlines(false);

					//DEFINE AS LARGURAS DAS COLUNAS
					$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
					$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

					//UNE AS COLUNAS
					$objPHPExcel->getActiveSheet()->mergeCells("A1:K1");

					// Define o título da planilha 
					$objPHPExcel->getActiveSheet()->setTitle($row->de_enquete);

					$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()
													                    	->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)
										                               		->setSize(16);


									                               	
					//MOSTRA O NOME DA ENQUETE NO INÍCIO DO EXCEL
					$objPHPExcel->getActiveSheet()->SetCellValue('A1', $row->de_enquete);

					//SE FOI SELECIONA O PERÍDO DA ENQUETE, MOSTRA ELE NO RELATÓRIO
					if($dt_inicio != "//" || $dt_final != "//" ){
						$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)
										                               		     ->setSize(14);
						$objPHPExcel->getActiveSheet()->mergeCells("A2:K2");
						$objPHPExcel->getActiveSheet()->SetCellValue('A2', "Período do Relatório: ".$dt_inicio." até ".$dt_final);
						$cont++;
					}

					$i++;
					$cont++;

					try{
						//PROCEDURE QUE TRAS O NOME DAS PERGUNTAS, E O TIPO DA PERGUNTA
						$sqll = " CALL pcPergunta( :cd_enquete, @cd_pergunta, @de_pergunta, @fl_tipo_pergunta);"; 
						
						$stmtt = $conn->prepare($sqll);

						$stmtt->bindParam(':cd_enquete', $cd_enquete_aux, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT,100);

						$stmtt->execute();
						if($stmtt->rowCount() > 0)
						{
							while($roww = $stmtt->fetch(PDO::FETCH_OBJ)) {	
								$cd_pergunta= $roww->cd_pergunta;	
								$fl_tipo_pergunta= $roww->fl_tipo_pergunta;		

								$cont = $cont+2;	

								$objPHPExcel->getActiveSheet()->getStyle('A'.$cont.':K'.$cont)->getFont()
																			 ->setSize(16)
																			 ->setBold(true)
																		     ->getColor()->setRGB('000000');
								//COR DE FUNDO VERDE
								$objPHPExcel->getActiveSheet()->getStyle('A'.$cont.':K'.$cont)->getFill()
																	->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
																	->getStartColor()->setARGB('92D050');
								//UNE AS CELULAS
								$objPHPExcel->getActiveSheet()->mergeCells('A'.$cont.':K'.$cont);

								$objPHPExcel->getActiveSheet()->getStyle('A'.$cont.':K'.$cont)->getAlignment()
													                    	->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

								$objPHPExcel->getActiveSheet()->SetCellValue('A'.$cont)	;

								$objPHPExcel->getActiveSheet()->SetCellValue('A'.$cont, $roww->de_pergunta);
								
								$cont = $cont+2;

																	
								try{
									//PROCEDURE PARA TRAZER AS RESPOSTAS
									$sqlll = " CALL pcResposta( :cd_pergunta, @cd_resposta, @de_resposta);"; 

									//PREPARA A SQL
									$sstmttt = $conn->prepare($sqlll);
									$sstmttt->closeCursor();

									$sstmttt->bindParam(':cd_pergunta', $cd_pergunta, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 100);

									$sstmttt->execute();
									$qt_resposta_aux="";
									

									if($sstmttt->rowCount() > 0)
									{
										//COL=1 SERIA A COLUNA LETRA A
										$col=1;
										//LINHARESPOSTA PARA MANTER NA MESMA LINHA AS RESPOSTAS E A QTDE DE RESPOSTAS
										$linhaResposta = $cont;
										//ESSE P SERVE PARA SABER A LINHA QUE VAI PULARNA HORA DAS RESPOSTA, PARA A NOVA PERGUNTA
										$p=0;
										while($rrowww = $sstmttt->fetch(PDO::FETCH_OBJ)) {
											
											$cd_resposta = $rrowww->cd_resposta;
											$objPHPExcel->getActiveSheet()->getStyle(get_col_letter($col).$linhaResposta.':'.get_col_letter($col).$linhaResposta)->getFont()->setSize(14)
																										->setBold(true)
																		                                ->getColor()->setRGB('000000');

											$objPHPExcel->getActiveSheet()->getStyle(get_col_letter($col).$linhaResposta.':'.get_col_letter($col).$linhaResposta)->getFill()
																										->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
																										->getStartColor()->setARGB('8DB4E2');
										
											$objPHPExcel->getActiveSheet()->SetCellValue(get_col_letter($col).$linhaResposta, $rrowww->de_resposta);

											//CONFIRO SE FOI SELECIONADO A DATA, PARA PASSAR PARA A PROCEDURE
											//COM A DATA OU NULL
											if($dt_inicio != "//" || $dt_final != "//" ){
												$dt_inicio_aux =  grdt($dt_inicio);
												$dt_final_aux = grdt($dt_final);
											}else{
												$dt_inicio_aux = null;
												$dt_final_aux = null;
											}
											

											//SE A PERGUNTA FOR RESPOSTA ÚNICA
											if($fl_tipo_pergunta == 0){
												try{
													//PRECEDURE QUE ANALISA AS RESPOSTAS DOS USUARIOS
													$ssqlll = " CALL pcRespUsuario( :cd_pergunta, :cd_resposta, :dt_inicio, :dt_final, @de_nome_completo, @dt_cadastro, @qt_resposta);"; 

													//PREPARA A SQL
													$ssstmttt = $conn->prepare($ssqlll);
													$ssstmttt->closeCursor();

													$ssstmttt->bindParam(':cd_pergunta', $cd_pergunta, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 100);
													$ssstmttt->bindParam(':cd_resposta', $cd_resposta, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 100);
													$ssstmttt->bindParam(':dt_inicio', $dt_inicio_aux, PDO::PARAM_INPUT_OUTPUT, 100);
													$ssstmttt->bindParam(':dt_final', $dt_final_aux, PDO::PARAM_INPUT_OUTPUT, 100);

													$ssstmttt->execute();

													if($sstmttt->rowCount() > 0)
													{	
														$linhaResp="";
														$coluser="";
														$coldt="";

														$linhaResp = $linhaResposta;
														$coluser = $col;
														$coldt = $col;
														$coldt++;
														$c=0;
													
														while($rrrowww = $ssstmttt->fetch(PDO::FETCH_OBJ)) {
															$linhaResp++;
															//COR DA RESPOSTA AZUL
															$objPHPExcel->getActiveSheet()->getStyle(get_col_letter($coluser).$linhaResp.':'.get_col_letter($coldt).$linhaResp)->getFont()->setSize(12)
																		                                ->getColor()->setRGB('0070C0');
															//COR DO FUNDO DA RESPOSTA AZUL CLARO
															$objPHPExcel->getActiveSheet()->getStyle(get_col_letter($coluser).$linhaResp.':'.get_col_letter($coldt).$linhaResp)->getFill()
																										->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
																										->getStartColor()->setARGB('C5D9F1');

															$qt_resposta = $rrrowww->qt_resposta;
															$dt_cadastro = $rrrowww->dt_cadastro;

															$objPHPExcel->getActiveSheet()->SetCellValue(get_col_letter($coluser).$linhaResp, $rrrowww->de_nome_completo);
															$objPHPExcel->getActiveSheet()->SetCellValue(get_col_letter($coldt).$linhaResp, mydth($dt_cadastro) );
															
															//CONTAGEM DE LINHA PARA A PRÓXIMA PERGUNTA
															if($qt_resposta > 0 && $qt_resposta > $qt_resposta_aux){
																//SE AINDA NÃO HOUVE A CONTAGEM, ENTRA AQUI
																if($p==0){
																	$qt_resposta_aux = $qt_resposta;
																	$cont = $cont+$qt_resposta;

																	$p=1;
																//SE JA TEVE A CONTAGEM, ENTRA AQUI E TIRA AS LINHAS QUE JÁ ESTAVAM SETADAS
																}else {
																	$cont = $cont-$qt_resposta_aux;

																	$qt_resposta_aux = $qt_resposta;
																	$cont = $cont+$qt_resposta;
																}
															}
														}
														$col++;

														//DEFINO A COR DO FUNDO PARA O AZUL DO NOME DAS RESPOSTA E PARA PRETO
														$objPHPExcel->getActiveSheet()->getStyle(get_col_letter($col).$linhaResposta.':'.get_col_letter($col).$linhaResposta)->getFont()->setSize(14)
																										->setBold(true)
																		                                ->getColor()->setRGB('000000');

														$objPHPExcel->getActiveSheet()->getStyle(get_col_letter($col).$linhaResposta.':'.get_col_letter($col).$linhaResposta)->getFill()
																										->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
																										->getStartColor()->setARGB('8DB4E2');
														$objPHPExcel->getActiveSheet()->SetCellValue(get_col_letter($col).$linhaResposta, cdbl($qt_resposta) );
														
														$col = $col+2;

														$qt_resposta="";
														
													}

												}catch(PDOException $e) {
													$de_aviso='ERROR: ' . $e->getMessage();
													$cd_tipo="am";
												}
												

											//SE A PERGUNTA FOR DESCRITIVA
											}else if($fl_tipo_pergunta == 2 ){
												try{
													$ssqlll = " CALL pcRespDescritiva(:cd_pergunta,:dt_inicio, :dt_final, @cd_usuario_respondeu, @dt_cadastro, @de_nome_completo, @de_resposta_usuario);"; 

													//PREPARA A SQL
													$ssstmttt = $conn->prepare($ssqlll);
													$ssstmttt->closeCursor();

													$ssstmttt->bindParam(':cd_pergunta', $cd_pergunta, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 100);
													$ssstmttt->bindParam(':dt_inicio', $dt_inicio_aux, PDO::PARAM_INPUT_OUTPUT, 100);
													$ssstmttt->bindParam(':dt_final', $dt_final_aux, PDO::PARAM_INPUT_OUTPUT, 100);
													$ssstmttt->execute();

													

													if($sstmttt->rowCount() > 0)
													{
														$cd_pergunta_anterior = $cd_pergunta-1;

														$objPHPExcel->getActiveSheet()->getStyle('A'.$cont)->getFont()->setSize(12)
																		                                   ->getColor()->setRGB('1F497D');
														$objPHPExcel->getActiveSheet()->getStyle('A'.$cont)->getFill()
																									->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
																									->getStartColor()->setARGB('FFFFFF');

														$objPHPExcel->getActiveSheet()->getStyle('A'.$cont.':'.'E'.$cont)->getFont()->setSize(12)
																										->setBold(true)
																		                                ->getColor()->setRGB('1F497D');


														$objPHPExcel->getActiveSheet()->SetCellValue('A'.$cont, "Usuário");
														$objPHPExcel->getActiveSheet()->SetCellValue('B'.$cont, "Data e Hora");
														$objPHPExcel->getActiveSheet()->SetCellValue('D'.$cont, "Feedback");
														$objPHPExcel->getActiveSheet()->SetCellValue('E'.$cont, "Comentário");
														$cont++;


														while($rrrowww = $ssstmttt->fetch(PDO::FETCH_OBJ)) {
															
															$cd_usuario_respondeu = $rrrowww->cd_usuario_respondeu;
															$de_nome_completo = $rrrowww->de_nome_completo;
															$de_resposta_usuario = $rrrowww->de_resposta_usuario;
															$dt_cadastro = $rrrowww->dt_cadastro;

															
															$objPHPExcel->getActiveSheet()->getStyle('A'.$cont.':K'.$cont)->getFont()->setSize(12)
																		                                ->getColor()->setRGB('0070C0');

															$objPHPExcel->getActiveSheet()->getStyle('A'.$cont.':K'.$cont)->getFill()
																										->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
																										->getStartColor()->setARGB('C5D9F1');


															try{
																$ssqqlll = " CALL pcRespFeedback(:cd_pergunta,:cd_usuario_respondeu, @de_resposta);"; 

																//PREPARA A SQL
																$sssttmttt = $conn->prepare($ssqqlll);
																$sssttmttt->closeCursor();

																$sssttmttt->bindParam(':cd_pergunta', $cd_pergunta_anterior, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 100);
																$sssttmttt->bindParam(':cd_usuario_respondeu', $cd_usuario_respondeu, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, 100);
																$sssttmttt->execute();

																if($sssttmttt->rowCount() > 0)
																{
																	if($rrroowww = $sssttmttt->fetch(PDO::FETCH_OBJ)) {
																		
																		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$cont, $rrroowww->de_resposta);

																	}
																}

															}catch(PDOException $e) {
																$de_aviso='ERROR: ' . $e->getMessage();
																$cd_tipo="am";
															}
															$objPHPExcel->getActiveSheet()->mergeCells("E".$cont.":K".$cont);

															$objPHPExcel->getActiveSheet()->SetCellValue('A'.$cont, $de_nome_completo);
															$objPHPExcel->getActiveSheet()->SetCellValue('B'.$cont, mydth($dt_cadastro) );
															$objPHPExcel->getActiveSheet()->SetCellValue('E'.$cont, $rrrowww->de_resposta_usuario);

															$cont++;
														}
													}

												}catch(PDOException $e) {
													$de_aviso='ERROR: ' . $e->getMessage();
													$cd_tipo="am";
												}
											}
										}
									}

								}catch(PDOException $e) {
									$de_aviso='ERROR: ' . $e->getMessage();
									$cd_tipo="am";
								}

							}
						}
					}catch(PDOException $e) {
						$de_aviso='ERROR: ' . $e->getMessage();
						$cd_tipo="am";
					}	
						
					
				}
			}
		}catch(PDOException $e) {
			$de_aviso='ERROR: ' . $e->getMessage();
			$cd_tipo="am";
		}

		
		// Define a planilha como ativa sendo a primeira, assim quando abrir o arquivo será a que virá aberta como padrão
		$objPHPExcel->setActiveSheetIndex(0);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		if(cdbl($cd_enquete > 0))
		{
			header('Content-Disposition: attachment;filename="Relatorio-'.$de_enquete_aux."-".date("d-m-Y").'.xlsx"');
		}
		else{
			header('Content-Disposition: attachment;filename="Relatorio-'.date("d-m-Y").'.xlsx"');	
		}
		//header('Content-Type: application/vnd.ms-excel');
		//header('Content-Disposition: attachment;filename="01simple.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		
		ob_end_clean();
		$objWriter->save('php://output');
		exit;

	}	


	?>

	<body id='body' onload="loadAnima();">
		<div id="wrap">
			<div class="container-fluid">
				<div class="row-fluid">
					<div class="span1">
						<h2 id="logotipoGtquest">
							GT<span>QUEST</span>
						</h2>
					</div>
					<div class="span9">
						<br>
						<form id="sair" name="sair" action="cad_usuario.php" method="post" enctype="multipart/form-data" >
						<?php

						echo "<span class='textoApresentacao'>Olá ".msMs($_SESSION['de_usuario'])." </span>";

						?>
							<input type="submit" name="logout" id="logout" value="Sair" class="btn"  />
						</form>
				
					</div>
					<div class="span2 ">
						<br>
						<?php
							$de_logotipo="";
							if(trim($_SESSION['de_logotipo'])=="")
							{
								$de_logotipo="img/genialteclogo.jpg";
							}else
							{
								if($de_logotipo==""){
									$de_logotipo=trim($_SESSION["de_logotipo"]);
								}		
							}

							echo "<img id='logotipo' class='img-rounded'  src='".$de_logotipo."'>";
						?>

					</div>
				</div>
						
				<br><br>	
				<div class="row-fluid">
					
					<!--MENUA LATERAL-->
					<div class="span2">					
						<div class="sidebar-nav bs-docs-sidenav">
				            <ul class="nav nav-list ">

				            	<li class="nav-header" >Relatório de Respostas</li>
				            	<li class="divider"></li>
				              
				            	<li class="menu-sidebar"><a href="home.php" >Home<i class="icon-chevron-right"></i></a></li>
								<li class="menu-sidebar"><a href="cad_usuario.php" >Usuário <i class="icon-chevron-right"></i></a></li>
								<li class="menu-sidebar"><a href="cad_enquete.php">Enquete <i class="icon-chevron-right"></i></a></li>
								<li class="menu-sidebar"><a href="cad_pergunta.php" >Pergunta <i class="icon-chevron-right"></i></a></li>
								<li class="menu-sidebar"><a href="cad_resposta.php" >Resposta <i class="icon-chevron-right"></i></a></li>
								<li class="menu-sidebar active"><a href="relatorio.php" >Relatório <i class="icon-chevron-right"></i></a></li>
								<li class="menu-sidebar"><a href="configuracoes.php" >Configurações <i class="icon-chevron-right"></i></a></li>
				         
				            </ul>
				        </div>
					</div>

					<!--CONTEÚDO-->
					<div class="span10">
						<div class="row-fluid">
							<div class="span11 offset1">
								<form id="formulario" name="formulario" action="relatorio.php" method="post" enctype="multipart/form-data" >
			
									<br><br>
									<?php

										/*
										 * ENQUETE
										 */

										try 
										{
											$sql = " SELECT cd_enquete, de_enquete FROM gt_enquete order by de_enquete";
										
											//PREPARA A SQL
											$stmt = $conn->prepare($sql);
											$stmt->execute();

											$cont=0;
											echo "<select class='span12'  id='cd_enquete' name='cd_enquete' value='".$cd_enquete."' onchange=f_montar_pergunta(this.value)>";
											echo "<option value=''  disabled selected>Nome da Enquete</option>";
											while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
												
												$cd_enquete_aux[$cont] = $row->cd_enquete;
										    	$de_enquete_aux[$cont] = $row->de_enquete;

												echo "<option value='".$cd_enquete_aux[$cont]."'  >".$de_enquete_aux[$cont]."</option>";
										    	$cont++;
										    }
										    echo "</select>";
										} catch(PDOException $e) {
											$de_aviso='ERROR: ' . $e->getMessage();
											$cd_tipo="vm";
										}


										/*
										 * PERGUNTA
										 */
										echo "<select class='span12 '  id='cd_pergunta' name='cd_pergunta' value='".$cd_pergunta."'  >";
											echo "<option value=''  disabled selected>Nome da Pergunta</option>";
										echo "</select>";

										/*
										 * USUÁRIO
										 */
										try 
										{
											$sql = " SELECT cd_usuario, de_nome FROM gt_usuario WHERE cd_nivel = 2";
										
											//PREPARA A SQL
											$stmt = $conn->prepare($sql);
											$stmt->execute();

											$cont=0;
											echo "<select class='span12'  id='cd_usuario_alterou' name='cd_usuario_alterou' value='".$cd_usuario_alterou."' >";
											echo "<option value=''  disabled selected>Nome da Usuário</option>";
											while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
												
												$cd_usuario_aux[$cont] = $row->cd_usuario;
										    	$de_usuario_aux[$cont] = $row->de_nome;

												echo "<option value='".$cd_usuario_aux[$cont]."'  >".$de_usuario_aux[$cont]."</option>";
										    	$cont++;
										    }
										    echo "</select>";
										} catch(PDOException $e) {
											$de_aviso='ERROR: ' . $e->getMessage();
											$cd_tipo="vm";
										}

										echo "<div class='row-fluid'>";
											echo "<div class='span12'>";
												echo "<legend >Período da Enquete</legend>";
												echo funCalendarioDataMini("Data de Início", "dt_inicio", "dt_inicio", false, false,"", $dt_inicio,"","");
												
												echo funCalendarioDataMini("Data Final", "dt_final", "dt_final", false, false,"", $dt_final,"","");
											echo "</div>";
										echo "</div>";

					

										/*
										 * BOTÕES
										 */

										echo "<div class='span4 offset7'>";
											echo '<br><br>';
											
											echo "<div class='btn-group'>";
												echo "<input type='submit' name='consultar' id='consultar' value='Consultar' class='btn btn-success'  onclick='return camposObrigatorios()' />";
											echo "</div>";

											echo "<div class='btn-group'>";
												echo "<input type='submit' name='geraExcel' id='geraExcel' value='Gerar Excel' class='btn btn-warning' onclick='return camposObrigatoriosExcel()'   />";
											echo "</div>";
											
											echo "<div class='btn-group'>";
												echo "<input type='button' id='bt_limpar' name='bt_limpar' class='btn' value='Limpar' onclick=f_limpar('relatorio.php')>";
											echo "</div>";

										echo "</div>";

										?>
								</form>
							</div>

						</div>
					</div>

				</div>
				<br>

				<!-- TABELA-->
				<div class="row-fluid">
					<div class="span12">

						<?php

						if(isset($_POST['consultar']))
						{
							try 
							{
								$sql = " SELECT RU.cd_enquete, RU.cd_pergunta, RU.cd_resposta, RU.de_resposta_usuario, RU.cd_usuario_respondeu,"; 
								$sql .= " RU.cd_usuario_alterou, RU.fl_tipo_pergunta, RU.dt_alteracao,"; 
								$sql .= " E.de_enquete, P.de_pergunta, R.de_resposta, U.de_nome";
								$sql .=  " ,(";
								$sql .=  "   SELECT count(cd_resposta)"; 
								$sql .=  "   FROM gt_resposta_usuario RU  ";
								$sql .=  "   WHERE RU.cd_resposta = R.cd_resposta ";
									if( $dt_inicio !="//" && $dt_final != "//")
									{
										$sql .= " AND date(RU.dt_alteracao) BETWEEN '".grdt($dt_inicio)."'  AND '".grdt($dt_final)."' ";
									}	
									if(cdbl($cd_usuario_alterou) > 0)
									{
										$sql .= " AND RU.cd_usuario_alterou =".cdbl($cd_usuario_alterou);	
									}
								$sql .=  "   ) qt_respostas ";
								
								$sql .= " FROM gt_resposta_usuario RU ";	
								$sql .= " LEFT OUTER JOIN gt_enquete E ";
								$sql .= " ON E.cd_enquete = RU.cd_enquete";
								$sql .= " LEFT OUTER JOIN gt_pergunta P ";
								$sql .= " ON P.cd_pergunta = RU.cd_pergunta";
								$sql .= " LEFT OUTER JOIN gt_resposta R ";
								$sql .= " ON R.cd_pergunta=P.cd_pergunta";
								$sql .= " LEFT OUTER JOIN gt_usuario U ";
								$sql .= " ON RU.cd_usuario_alterou=U.cd_usuario";
								$sql .= " WHERE RU.fl_enviado=1";
								$sql .= " AND RU.cd_enquete=".cdbl($cd_enquete);
								//$sql .= " AND RU.cd_enquete = :cd_enquete";
								if(cdbl($cd_pergunta > 0))
								{
									//$sql .= " AND RU.cd_pergunta = :cd_pergunta";
									$sql .= " AND RU.cd_pergunta = ".cdbl($cd_pergunta);;
								}
								if(cdbl($cd_usuario_alterou) > 0)
								{
									$sql .= " AND RU.cd_usuario_alterou =".cdbl($cd_usuario_alterou);	
								}
								
								if( $dt_inicio !="//" && $dt_final != "//")
								{
									$sql .= " AND date(RU.dt_alteracao) BETWEEN '".grdt($dt_inicio)."'  AND '".grdt($dt_final)."' ";
								}	 
								$sql .= " ORDER BY RU.cd_enquete,RU.cd_Pergunta,RU.cd_resposta"; 


								//PREPARA A SQL
								$stmt = $conn->prepare($sql);

								$stmt->execute();


								if($stmt->rowCount() > 0)
								{
									
									echo "<div class='accordion' id='accordion2'>";
										while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
											$i++;
											$cd_enquete_aux = $row->cd_enquete;
											$de_enquete_aux = $row->de_enquete;
											$cd_pergunta_aux = $row->cd_pergunta;
											$de_pergunta_aux = $row->de_pergunta;
								    		$cd_resposta_aux = $row->cd_resposta;
								    		$de_resposta_aux = $row->de_resposta;
								    		$de_resposta_usuario_aux = $row->de_resposta_usuario;
								    		$fl_tipo_pergunta_aux = $row->fl_tipo_pergunta;
								    		$de_usuario_aux = $row->de_nome;
								    		$dt_cadastro_aux = mydth($row->dt_cadastro);
								    		$dt_alteracao_aux = mydth($row->dt_alteracao);
								    		$qt_respostas = $row->qt_respostas;
								    		$cd_usuario_respondeu_aux = $row->cd_usuario_respondeu;
							    			

											if($cd_enquete_aux!=$cd_enquete_anterior)
											{
												echo "<div class='row-fluid'>";
														echo "<h4 >".$de_enquete_aux."</h4>";
														echo "<br>";
														/*if(fl_master == 1)
														{
															echo "<div  class='span1 offset8' style='margin-bottom: 50px;margin-top: -20px;' >";
																echo "<input type='button' id='zerar_relatorio_todos' name='zerar_relatorio_todos' class='btn btn-danger' value='Zerar Relatório de Todas as Perguntas' onclick=f_zerar_relatorio_todos(".cdbl($cd_enquete_aux).",".cdbl($cd_usuario_alterou).") >";
															echo "</div>";	
														}*/
												echo "</div>";							
													
											}		
											$cd_enquete_anterior=$cd_enquete_aux;

											if($cd_pergunta_aux!=$cd_pergunta_anterior)
											{
												echo "<div class='accordion-group ' style='margin-bottom: 30px; margin-top: -10px;' >";
													echo "<div class='accordion-heading'>";

														if($fl_tipo_pergunta_aux==0) 
														{
															echo "<a class='accordion-toggle' style='text-decoration: none;' data-toggle='collapse' href='#".cdbl($cd_pergunta_aux)."' onclick='f_expandir_pergunta_radio(".cdbl($cd_enquete_aux).",".cdbl($cd_pergunta_aux).",".cdbl($cd_usuario_alterou).");' >";
														        echo "<div style='text-align:left'>";
				
														        	echo "<table style='width:100%'>";
																		echo "<tr >";
																			echo "<td style='width:80%; text-align:left;' >&nbsp".$de_pergunta_aux."</td>";
																			echo "<td style='width:20%; text-align:center'>Resposta Única</td>";
																		echo "</tr>";
																	echo "</table>";


																echo "</div>";
															
													   	 	echo "</a>";
														}

														if($fl_tipo_pergunta_aux==1)
														{
															echo "<a class='accordion-toggle' style='text-decoration: none;' data-toggle='collapse' href='#".cdbl($cd_pergunta_aux)."' onclick='f_expandir_pergunta_check(".cdbl($cd_enquete_aux).",".cdbl($cd_pergunta_aux).",".cdbl($cd_usuario_alterou).");' >";
														        echo "<div style='text-align:left'>";

																	echo "<table style='width:100%'>";
																		echo "<tr >";
																			echo "<td style='width:80%; text-align:left;' >&nbsp".$de_pergunta_aux."</td>";
																			echo "<td style='width:20%; text-align:center'>Multipla Escolha</td>";
																		echo "</tr>";
																	echo "</table>";

																echo "</div>";
															
													   	 	echo "</a>";

														}

														if($fl_tipo_pergunta_aux==2)
														{
															echo "<a class='accordion-toggle' style='text-decoration: none;' data-toggle='collapse' href='#".cdbl($cd_pergunta_aux)."'onclick='f_expandir_pergunta_descritiva(".cdbl($cd_pergunta_aux).");'>";
														        echo "<div style='text-align:left'>";
															      
														        	echo "<table style='width:100%'>";
																		echo "<tr >";
																			echo "<td style='width:80%; text-align:left;' >&nbsp".$de_pergunta_aux."</td>";
																			echo "<td style='width:20%; text-align:center'>Resposta Descritiva</td>";
																		echo "</tr>";
																	echo "</table>";

																echo "</div>";
															
													   	 	echo "</a>";

														}

													echo "</div>";

													echo "<div id='".cdbl($cd_pergunta_aux)."' class='accordion-body collapse ' style='height:0px;'>";
														echo "<div class='accordion-inner '>";

															//Descricao de Pergunta
															if(cdbl($fl_tipo_pergunta_aux)==0)
															{
																try{
																	$sqli = " SELECT COUNT(DISTINCT RU.cd_resposta_usuario) as qt_resposta_usuario ";
																	$sqli .= " FROM gt_resposta_usuario RU";
																	$sqli .= " LEFT OUTER JOIN gt_pergunta P";
																	$sqli .= " ON RU.cd_pergunta = P.cd_pergunta ";
																	$sqli .= " WHERE P.cd_enquete= :cd_enquete";
																	$sqli .= " AND P.cd_pergunta= :cd_pergunta";
																	if(cdbl($cd_usuario_alterou) > 0)
																	{
																		$sqli .= " AND RU.cd_usuario_alterou = :cd_usuario_alterou";	
																	}
																	if( $dt_inicio !="//" && $dt_final != "//")
																	{
																		$sqli .= " AND date(RU.dt_alteracao) BETWEEN '".grdt($dt_inicio)."'  AND '".grdt($dt_final)."' ";
																	}	

																	//PREPARA A SQL
																	$stmtt = $conn->prepare($sqli);
																	$stmtt->bindParam(':cd_enquete', $cd_enquete_aux, PDO::PARAM_INT);
																	$stmtt->bindParam(':cd_pergunta', $cd_pergunta_aux, PDO::PARAM_INT);
																	if(cdbl($cd_usuario_alterou) > 0){
																		$stmtt->bindParam(':cd_usuario_alterou', $cd_usuario_alterou, PDO::PARAM_INT);
																	}
																	$stmtt->execute();

																	if($stmtt->rowCount() > 0){

																		if($roww = $stmtt->fetch(PDO::FETCH_OBJ)) {
																		 	$qt_resposta_usuario = $roww->qt_resposta_usuario;
																		
																		}
																	}
																}catch(PDOException $e) {
																	$de_aviso='ERROR: ' . $e->getMessage();
																	$cd_tipo="am";
																}
																//echo "<div class='grafico' >";
																echo "<div id='area_grafico_radio".$cd_pergunta_aux."' ></div>";
																//echo "</div>";

																echo "<h3>Total de respostas para esta pergunta: ".$qt_resposta_usuario."</h3>";	
																/*if(fl_master == 1){
																	echo "<div class='span1 offset9' style='margin-bottom:20px;'>";
																		echo "<input type='button' id='bt_zerar_relatorio' name='bt_zerar_relatorio' class='btn btn-danger' value='Zerar Relatório' onclick='f_zerar_relatorio(".cdbl($cd_pergunta_aux).",".cdbl($cd_usuario_alterou).")'' >";
																	echo "</div>";
																}*/


					
															}

															if(cdbl($fl_tipo_pergunta_aux) == 1)
															{
																try{
																	$sqlii = " SELECT COUNT(DISTINCT RU.cd_resposta_usuario) as qt_resposta_usuario ";
																	$sqlii .= " FROM gt_resposta_usuario RU";
																	$sqlii .= " LEFT OUTER JOIN gt_pergunta P";
																	$sqlii .= " ON RU.cd_pergunta = P.cd_pergunta ";
																	$sqlii .= " WHERE P.cd_enquete= :cd_enquete";
																	$sqlii .= " AND P.cd_pergunta= :cd_pergunta";
																	if(cdbl($cd_usuario_alterou) > 0)
																	{
																		$sqlii .= " AND RU.cd_usuario_alterou = :cd_usuario_alterou";		
																	}
																	if( $dt_inicio !="//" && $dt_final != "//")
																	{
																		$sqlii .= " AND date(RU.dt_alteracao) BETWEEN '".grdt($dt_inicio)."'  AND '".grdt($dt_final)."' ";
																	}		

																	//PREPARA A SQL
																	$stmttt = $conn->prepare($sqlii);
																	$stmttt->bindParam(':cd_enquete', $cd_enquete_aux, PDO::PARAM_INT);
																	$stmttt->bindParam(':cd_pergunta', $cd_pergunta_aux, PDO::PARAM_INT);
																	if(cdbl($cd_usuario_alterou) > 0){
																		$stmttt->bindParam(':cd_usuario_alterou', $cd_usuario_alterou, PDO::PARAM_INT);
																	}
																	$stmttt->execute();

																	if($stmttt->rowCount() > 0){

																		if($rowww = $stmttt->fetch(PDO::FETCH_OBJ)) {
																		 	$qt_resposta_usuario = $rowww->qt_resposta_usuario;
																		
																		}
																	}
																}catch(PDOException $e) {
																	$de_aviso='ERROR: ' . $e->getMessage();
																	$cd_tipo="am";
																}

																echo "<div id='area_grafico_check".$cd_pergunta_aux."' ></div>";
																echo "<h3>Total de respostas para esta pergunta: ".$qt_resposta_usuario."</h3>";
																/*if(fl_master == 1){
																	echo "<div class='span1 offset9' style='margin-bottom:20px;'>";
																		echo "<input type='button' id='bt_zerar_relatorio' name='bt_zerar_relatorio' class='btn btn-danger' value='Zerar Relatório' onclick='f_zerar_relatorio(".cdbl($cd_pergunta_aux).",".cdbl($cd_usuario_alterou).")'>";
																	echo "</div>";
																}*/


															}
																
											
															if(cdbl($fl_tipo_pergunta_aux) == 2)
															{
																try{
																	$sqliii = " SELECT COUNT(DISTINCT RU.cd_resposta_usuario) as qt_resposta_usuario ";
																	$sqliii .= " FROM gt_resposta_usuario RU";
																	$sqliii .= " LEFT OUTER JOIN gt_pergunta P";
																	$sqliii .= " ON RU.cd_pergunta = P.cd_pergunta ";
																	$sqliii .= " WHERE P.cd_enquete= :cd_enquete";
																	$sqliii .= " AND P.cd_pergunta= :cd_pergunta";
																	if(cdbl($cd_usuario_alterou) > 0)
																	{
																		$sqliii .= " AND RU.cd_usuario_alterou = :cd_usuario_alterou";		
																	}
																	if( $dt_inicio !="//" && $dt_final != "//")
																	{
																		$sqliii .= " AND date(RU.dt_alteracao) BETWEEN '".grdt($dt_inicio)."'  AND '".grdt($dt_final)."' ";
																	}		

																	//PREPARA A SQL
																	$stmtttt = $conn->prepare($sqliii);
																	$stmtttt->bindParam(':cd_enquete', $cd_enquete_aux, PDO::PARAM_INT);
																	$stmtttt->bindParam(':cd_pergunta', $cd_pergunta_aux, PDO::PARAM_INT);
																	if(cdbl($cd_usuario_alterou) > 0){
																		$stmtttt->bindParam(':cd_usuario_alterou', $cd_usuario_alterou, PDO::PARAM_INT);
																	}
																	$stmtttt->execute();

																	if($stmtttt->rowCount() > 0){

																		if($rowwww = $stmtttt->fetch(PDO::FETCH_OBJ)) {
																		 	$qt_resposta_usuario = $rowwww->qt_resposta_usuario;
																		
																		}
																	}
																}catch(PDOException $e) {
																	$de_aviso='ERROR: ' . $e->getMessage();
																	$cd_tipo="am";
																}


																echo "<h3>Total de respostas para esta pergunta: ".$qt_resposta_usuario."</h3>";
																echo "<table class=' table-bordered table-hover table-condensed  table-striped'   style='width:100%'>";
																echo "<thead>";
																	echo "<tr>";
																		echo "<th style='width:200px;' >Nome</th>";
																		echo "<th style='width:100px;' >Feedback </th>";
																		echo "<th >".$de_pergunta_aux."</th>";
																		echo "<th style='text-align:center; width:200px;'>Data de Cadastro</th>";
																	echo "</tr>";
																echo "</thead>";
															}

															
												if($fl_tipo_pergunta_aux !=2){
														//fecha accordion-inner
														echo "</div>";
													//fecha cd_pergunta_aux
													echo "</div>";
												//fecha o group
												echo "</div>";
												}
											}
											$cd_pergunta_anterior=$cd_pergunta_aux;

											//TRATAMENTO PARA NA PERGUNRA 'DEIXE AQUI SEU FEEDBACK' 
											//APAREÇA O FEEDBACK QUE O USUARIO SELECIONOU
											//ELOGIO, CRITICA, SEM RESPOSTA
											if(cdbl($fl_tipo_pergunta_aux) == 2)
											{

												try{
													$sqly = " SELECT RU.de_resposta_usuario ";
													$sqly .= " FROM gt_resposta_usuario RU";
													$sqly .= " LEFT OUTER JOIN gt_pergunta P";
													$sqly .= " ON RU.cd_pergunta = P.cd_pergunta ";
													$sqly .= " LEFT OUTER JOIN gt_usuario U";
													$sqly .= " ON RU.cd_usuario_alterou = U.cd_usuario ";
													$sqly .= " WHERE P.cd_enquete= :cd_enquete";
													$sqly .= " AND P.cd_pergunta= :cd_pergunta";
													$sqly .= " AND RU.cd_usuario_respondeu= :cd_usuario_respondeu";

													if(cdbl($cd_usuario_alterou) > 0)
													{
														$sqly .= " AND RU.cd_usuario_alterou = :cd_usuario_alterou";	
													}
													if( $dt_inicio !="//" && $dt_final != "//")
													{
														$sqly .= " AND date(RU.dt_alteracao) BETWEEN '".grdt($dt_inicio)."'  AND '".grdt($dt_final)."' ";
													}	

													$cd_pergunta_teste =$cd_pergunta_aux-1;

													//PREPARA A SQL
													$stmtty = $conn->prepare($sqly);
													
													$stmtty->bindParam(':cd_enquete', $cd_enquete_aux, PDO::PARAM_INT);
													$stmtty->bindParam(':cd_pergunta', $cd_pergunta_teste, PDO::PARAM_INT);
													$stmtty->bindParam(':cd_usuario_respondeu', $cd_usuario_respondeu_aux, PDO::PARAM_INT);
													if(cdbl($cd_usuario_alterou) > 0){
														$stmtty->bindParam(':cd_usuario_alterou', $cd_usuario_alterou, PDO::PARAM_INT);
													}

													$stmtty->execute();

													$contador++;
													if($stmtty->rowCount() > 0){

														if($rowwy = $stmtty->fetch(PDO::FETCH_OBJ)) {
														 	
															$cd_resposta_user =  $rowwy->de_resposta_usuario;
															
															try{
																$sqlyy = " SELECT de_resposta ";
																$sqlyy .= " FROM gt_resposta R";
																$sqlyy .= " WHERE R.cd_resposta= :cd_resposta";

																//PREPARA A SQL
																$stmttyy = $conn->prepare($sqlyy);
																
																$stmttyy->bindParam(':cd_resposta', $cd_resposta_user, PDO::PARAM_INT);

																$stmttyy->execute();
																
																if($stmttyy->rowCount() > 0){

																	if($linha = $stmttyy->fetch(PDO::FETCH_OBJ)) {
																	 	//Guarda o que o usuario selecionou, elegio, critou ou sem sugestões
																		$de_feedback[$contador] =  $linha->de_resposta;
																	
																	}
																}
															}catch(PDOException $e) {
																$de_aviso='ERROR: ' . $e->getMessage();
																$cd_tipo="am";
															}
														}	
													}else{ 
														//Se o  usuário não respondeu
														$de_feedback[$contador] = " ";
													}
												}catch(PDOException $e) {
													$de_aviso='ERROR: ' . $e->getMessage();
													$cd_tipo="am";
												}
											}

											if($qt_respostas != 0)
											{
												$con++;
												
												//Cor das respostas
												echo "<tr class='linhaTabela'>";
													echo "<td style='text-align:left'>".$de_usuario_aux."</td>";

													echo "<td style='text-align:left'>".$de_feedback[$con]."</td>";

													echo "<td style='text-align:left'>".$de_resposta_usuario_aux."</td>";

													echo "<td style='text-align:center'>".$dt_alteracao_aux."</td>";	
														
												echo "</tr>";

												if ($qt_respostas == $con) 
												{
														$con = 0;
														echo "</table>";
														echo "<br>";
														/*if(fl_master == 1){
															echo "<div class='span1 offset9' style='margin-bottom:20px;'>";
																echo "<input type='button' id='bt_zerar_relatorio' name='bt_zerar_relatorio' class='btn btn-danger' value='Zerar Relatório' onclick='f_zerar_relatorio(".cdbl($cd_pergunta_aux).",".cdbl($cd_usuario_alterou).")'>";
															echo "</div>";
														}*/
														//fecha accordion-inner
														echo "</div>";
													//fecha cd_pergunta_aux	
													echo "</div>";
												//fecha o group
												echo "</div>";
												}
											}
										}
									echo "</div>";
								}
								else
								{
									$de_aviso="Enquete não respondida!";
									$cd_tipo="am";
								}

							}catch(PDOException $e) {
								$de_aviso='ERROR: ' . $e->getMessage();
								$cd_tipo="vm";
							}

						}

									
						?>
				
						
					</div>
				</div>

				<br><br>
			</div>
		</div>

		<div id="footer">
	  		<div class="container">
	   			<p>Development by <a href="http://www.genialtec.com.br" target="_blank" >Genialtec</a></p>
	  		</div>
		</div>

		<script>loadEsconde();</script>


		<script type="text/javascript">


		//Carregar a API de visualizacao e os pacotes necessarios.
		google.load('visualization', '1.0', {'packages':['corechart']});
		
		function f_expandir_pergunta_radio(cd_enquete, cd_pergunta, cd_usuario_alterou)
		{
			var dt_inicio = document.getElementById('dt_inicio').value;
			var dt_final = document.getElementById('dt_final').value;

			//Desenha o Gráfico
			var jsonData = $.ajax({
				url: "json/gera_grafico_radio.php?cd_enquete="+cd_enquete+"&cd_pergunta="+cd_pergunta+"&cd_usuario_alterou="+cd_usuario_alterou+"&dt_inicio="+dt_inicio+"&dt_final="+dt_final,
				dataType: "json",
				async:false

				}).responseText;


			var options = {
				width:'100%',
				height:'100px',
				//tras valores nulos
				sliceVisibilityThreshold:0,
				hAxis: {format: 'decimal'},
				bar: {groupWidth: "80%"},
        		legend: { position: "none" },
        		isStacked: 'absolute',
        		
        		annotations: {
					textStyle: {
						//fontName: 'Times-Roman',
						fontSize: 18,
						color: 'black',
						bold: false,
					}  
				}
			};

			var data = new google.visualization.DataTable(jsonData);

			var chart = new google.visualization.BarChart(document.getElementById('area_grafico_radio'+cd_pergunta));

			chart.draw(data, options);
			
			$(window).resize(function(){
	            chart.draw(data, options);
	        });
			
		}
   

		function f_expandir_pergunta_check(cd_enquete, cd_pergunta, cd_usuario_alterou)
		{

			var dt_inicio = document.getElementById('dt_inicio').value;
			var dt_final = document.getElementById('dt_final').value;

			//Desenha o Gráfico
			var jsonData = $.ajax({
				url: "json/gera_grafico_check.php?cd_enquete="+cd_enquete+"&cd_pergunta="+cd_pergunta+"&cd_usuario_alterou="+cd_usuario_alterou+"&dt_inicio="+dt_inicio+"&dt_final="+dt_final,
				dataType: "json",
				async:false
				}).responseText;

			var options = {
				width:'100%',
				height:'100%',
	
				
				//Em forma de donut
				//pieHole:0.3,
				chartArea: {
					left:0,
					top:20,
					width:"100%",
					height:"100%"
		        },
				//tras valores nulos
				sliceVisibilityThreshold:0,
				
				//Em forma de donut
				//pieHole:0.3,
				
				//mostra o nome da resposta no grágico
				//pieSliceText: 'label',

				//Em 3d
				is3D: true,
				legend:{ alignment: 'center', position: 'right', textStyle: { fontSize:17, bold:true }  }
				
				};

			var data = new google.visualization.DataTable(jsonData);
			var chart = new google.visualization.PieChart(document.getElementById('area_grafico_check'+cd_pergunta));
			
			chart.draw(data, options);
			
			$(window).resize(function(){
	            chart.draw(data, options);
	        });
			
		}


		/*
		 * Função para selecionar a pergunta
		 */
		function f_montar_pergunta(cd_enquete)
		{
			var url = "ajx/ajx_enquete_pergunta.php?cd_enquete=" + cd_enquete;
			document.getElementById("cd_pergunta").innerHTML="";
			f_ajaxget("cd_pergunta", url);	
		}
		
		
		function f_zerar_relatorio_todos(cd_enquete,cd_usuario_alterou) 
		{
			var sim = window.confirm('Serão excluídas todas as respostas referente a esta enquete.\nDeseja realmente zerar o relatório?');
			if (sim)
			{
				document.formulario.action="relatorio.php?funcao=ET&cd_enquete="+cd_enquete+"&cd_usuario_alterou="+cd_usuario_alterou;	
				document.formulario.submit();
			}
		}

		function f_zerar_relatorio(cd_pergunta,cd_usuario_alterou) 
		{
			var sim = window.confirm('Serão excluídas as respostas referente a esta enquete.\nDeseja realmente zerar o relatório?');
			if (sim)
			{
				document.formulario.action="relatorio.php?funcao=E&cd_pergunta="+cd_pergunta+"&cd_usuario_alterou="+cd_usuario_alterou;	
				document.formulario.submit();
			}
		}

		function camposObrigatorios()
		{	
			f_setHiddenData('dt_inicio');
			f_setHiddenData('dt_final');
			var cd_enquete = document.getElementById('cd_enquete').value;
			var dt_inicio = document.getElementById('dt_inicio').value;
			var dt_final = document.getElementById('dt_final').value;
			
			if (cd_enquete == "")
			{
				f_avisos("Não é possível realizar um relatório com o campo Nome da Enquete vazio!","am");
				document.getElementById("cd_enquete").focus();
				return false;
			} 
			if (dt_inicio != "//" && dt_final == "//")
			{
				f_avisos("É necessário a Data de Final preenchida!","am");
				document.getElementById("dt_final").focus();
				return false;
			} 
			if (dt_inicio == "//" && dt_final != "//")
			{
				f_avisos("É necessário a Data de Início preenchida!","am");
				document.getElementById("dt_inicio").focus();
				return false;
			} 
			return true;
		}

		function camposObrigatoriosExcel()
		{	
			f_setHiddenData('dt_inicio');
			f_setHiddenData('dt_final');
			var dt_inicio = document.getElementById('dt_inicio').value;
			var dt_final = document.getElementById('dt_final').value;
			
			if (dt_inicio != "//" && dt_final == "//")
			{
				f_avisos("É necessário a Data de Final preenchida!","am");
				document.getElementById("dt_final").focus();
				return false;
			} 
			if (dt_inicio == "//" && dt_final != "//")
			{
				f_avisos("É necessário a Data de Início preenchida!","am");
				document.getElementById("dt_inicio").focus();
				return false;
			} 
			return true;
		}


		</script>


	</body>
</html>
<?php

/*
 * Barra de status
 */
echo funStatusBar();
echo funAviso($de_aviso,$cd_tipo);

?>