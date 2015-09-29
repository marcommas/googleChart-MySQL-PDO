<?php
header('Content-Type: application/json; charset=UTF-8');

//Bibliotecas
require_once('../lib/l_conexao.php');

/*
Caso tenha passa algum dados por parâmetro pega eles por GET
if (isset($_GET['dados'])){
	$dados = $_GET['dados'];
}
*/

//Cria a conexão com o banco
$conn = db_conPDO();

	$arr_de_resposta_usuario= array();
	$arr_de_resposta = array(); 
    $table = array();
	$rows = array();
    
    
    try{
		$sql = " SELECT id, dados";
		$sql .= " FROM tabela ";	

		//PREPARA A SQL
		$stmt = $conn->prepare($sql);
		
		$stmt->execute();

		if($stmt->rowCount() > 0){

			while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
				$id = $row->id;
			 	$dados = $row->dados;
			}
		}
	}catch(PDOException $e) {
		$de_aviso='ERROR: ' . $e->getMessage();
		$cd_tipo="am";
	}



	$table = array();
	//Crio uma tabela, e defino as colunas aqui em baixo

	$table['cols'] = array(

		array("id"=>"",'label' => 'Genero', 'pattern' => "",'type' => 'string'),
		array("id"=>"",'label' => 'Quantidade', 'pattern' => "",'type' => 'number'),
		
		//Para definir a cor da barra
		array('type' => 'string', 'p' => array('role' => 'style')),
		array('type' => 'string', 'p' => array('role' => 'annotation'))
		
	);
	
	$rows = array();

	foreach ($dados as $chave => $valor ) {

		$temp=array();
		$temp[]=array('v'=>(string) $dados[$chave]);
		$temp[]=array('v'=>(int) $valor);

		switch ($dados[$chave]) {
			case "Satisfeito":
				$temp[] = array('v' =>(string) 'color: #00CC00; opacity: 0.5;stroke-width:2;');
				$temp[] = array('v' =>(string) $valor);
				break;

			case "Neutro":
				$temp[] = array('v' =>(string) 'color: #FFCC33; opacity: 0.5;stroke-width:2;');
				$temp[] = array('v' =>(string) $valor);
				break;

			case "Insatisfeito":
				$temp[] = array('v' =>(string) 'color: #FF0000; opacity: 0.5;stroke-width:2;');
				$temp[] = array('v' =>(string) $valor);
				break;

			case "Não se aplica":
				$temp[] = array('v' =>(string) 'color: #C0C0C0; opacity: 0.5;stroke-width:2;');
				$temp[] = array('v' =>(string) $valor);
				break;

			default:
				$temp[] = array('v' =>(string) 'color: #0033CC; opacity: 0.5;stroke-width:2;');
				$temp[] = array('v' =>(string) $valor);

		}	
		$rows[] = array('c'=>$temp);
		
	}


	//integra a variavel $table que continha só colunas com a variável $rows
	//assim cria uma uma tabela com linhas e colunas
	$table['rows']=$rows;

	//tranforma a tabela em formato Json
	echo json_encode($table);


?>