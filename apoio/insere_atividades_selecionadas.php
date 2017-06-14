<?php

require '../config.php';

//INSERE ATIVIDADES SELECIONADAS NO BANCO
if (isset($_POST['cadastrar'])) {
	
	$atividades = "SELECT `ATIVIDADES_ATIEMPPLA`, `STATUS_ATIEMPPLA`, `ID_EMP` from ATIVIDADES_EMPRESA_PLAT WHERE ID_EMP = 1"; //ALTERAR PARA COLOCAR ID DA EMPRESA TRAZIDO NA SESSION

	$conexao = @mysql_connect($host, $usuario, $senha) or exit(mysql_error());

	mysql_select_db($banco);
	
	if(isset($_POST['atividades'])){
	 
	 	$listaAtividades = $_POST['atividades'];
	 	$atividadesSelecionadas = implode(",",$listaAtividades);
	    

		$sqlAti = "UPDATE `atividades_empresa_plat` SET `ATIVIDADES_ATIEMPPLA`='1' WHERE `ID_EMP` = 1";//"INSERT INTO ATIVIDADES_EMPRESA_PLAT (ATIVIDADES_ATIEMPPLA, STATUS_ATIEMPPLA, ID_EMP) VALUES ('". $atividadesSelecionadas . "','0',1)"; //ALTERAR PARA COLOCAR ID DA EMPRESA TRAZIDO NA SESSION
		$queryAti = mysql_query($sqlAti, $conexao);

		header("Location: ../selecao_atividades.php"); /* REDIRECIONA USUARIO PARA MESMA PAGINA DE SELECAO */
			
	} else {
		echo 'Voce nao selecionou nenhuma atividade ';
	}

}
?>