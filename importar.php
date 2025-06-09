<?php
require_once 'clases/clase.General.php';

function listaInicial(){
	$CodigoCarrera = '127';
	$NumeroPlanEstudios = '1'; 
	$GestionAcademica = '2025'; 
	$CodigoModalidadCurso = 'NA'; 
	$SiglaMateria = 'CJS102'; 
	$CodigoSEA = 'H'; 
	$Grupo = 'B';
	$datosImportar = General::getImportados(Conexion::getInstancia(),
		$CodigoCarrera,
		$NumeroPlanEstudios,
		$GestionAcademica,
		$CodigoModalidadCurso,
		$SiglaMateria,
		$CodigoSEA,
		$Grupo
	);
	return $datosImportar;
}

function totalTareas($data){
	$maximo = 0;
	foreach ($data as $value) {
		$j = 0;
		foreach	($value as $key => $val) {
			if ((strpos($key, 'Tarea') !== false) && (!is_null($val))) {
				$j++;
				if ($j > $maximo) { $maximo = $j; }
			}
		}
	}
	return $maximo;
}

function armarJsonPracticas(&$data, $maxTareas){
	foreach ($data as &$value) {
		$j = 1; $re2='{';
		foreach ($value as $key => $val) {
			$tar = "Tarea$j";
			if($key == $tar && !is_null($val)){
				$re2.= '"'.$j.'":"'.$val.'",';
				$j++;
			}
		}
		$re2 = substr($re2, 0, -1);
		$re2 .= '}';
		$value['practicas'] = $re2;
	}

	foreach ($data as &$value) {
		$j = 1; $re2=''; $re3='{';
		foreach ($value as $key => $val) {
			$tar = "Parcial$j";
			if($key == $tar && !is_null($val)){
				$re2.= $tar." = "."'$val', ";
				$re3.= '"'.$j.'":"'.$val.'",';
				$j++;
			}
		}
		$re3 = substr($re3, 0, -1);
		$re3 .= '}';
		$value['parciales'] = $re2;
		$value['parcialesPonderado'] = $re3;
	}
	
}

function armarSQL($data){
	foreach ($data as $value) {
		$Cu = $value['Cu'];
		$CodigoCarrera = $value['CodigoCarrera'];
		$NumeroPlanEstudios = $value['NumeroPlanEstudios'];
		$GestionAcademica = $value['GestionAcademica'];
		$CodigoModalidadCurso = $value['CodigoModalidadCurso'];
		$SiglaMateria = $value['SiglaMateria'];
		$Grupo = $value['Grupo'];
		$parciales = $value['parciales'];
		$parcialesPonderado = $value['parcialesPonderado'];
		$practicas = $value['practicas'];
		$NotaPracticas = $value['NotasPracticas'];
		$NotaSemifinal = $value['NotaSemifinal'];
		$ExamenFinalPonderado = $value['ExamenFinal'];
		$NotaFinal = $value['NotaFinal'];
		$ExamenSegundaInstanciaPonderado = $value['ExamenSegundaInstancia'];
		$NotaSegundaInstancia = $value['NotaSegundaInstancia'];
		$PromedioPonderadoParciales = $value['PromedioParciales'];

		$sql = "UPDATE Edoc_Calificaciones SET $parciales JsonTeoriasPonderado = '$parcialesPonderado', JsonPracticasPonderado = '$practicas', NotaPracticas = '$NotaPracticas', NotaSemifinal = '$NotaSemifinal', ExamenFinalPonderado = '$ExamenFinalPonderado', NotaFinal = '$NotaFinal', ExamenSegundaInstanciaPonderado = '$ExamenSegundaInstanciaPonderado', NotaSegundaInstancia = '$NotaSegundaInstancia', PromedioPonderadoParciales = '$PromedioPonderadoParciales' WHERE Cu='$Cu' AND CodigoCarrera = '$CodigoCarrera' AND NumeroPlanEstudios = '$NumeroPlanEstudios' AND GestionAcademica = '$GestionAcademica' AND CodigoModalidadCurso = '$CodigoModalidadCurso' AND SiglaMateria = '$SiglaMateria' AND T = '$Grupo';";
		echo $sql . "</br></br>\n";
	}
}

// Main execution
$lista = listaInicial();
$maxTareas = totalTareas($lista);
armarJsonPracticas($lista, $maxTareas);

if ($lista) {
	// echo '<pre>'.print_r($lista,true).'</pre>';
	armarSQL($lista);
} else {
	echo "No se encontraron registros para los criterios especificados.";
}

?>