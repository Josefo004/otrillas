<?php
require_once 'clase.Conex.php';

class General
{
    public static function getImportados($conexion, $CodigoCarrera = '', $NumeroPlanEstudios = '', $GestionAcademica = '', $CodigoModalidadCurso = '', $SiglaMateria = '', $CodigoSEA = '', $Grupo = '' )
    {
        $sql = "SELECT * FROM ImportarMoodleCalificaciones
                WHERE CodigoCarrera = '$CodigoCarrera' 
                AND NumeroPlanEstudios = '$NumeroPlanEstudios' 
                AND GestionAcademica = '$GestionAcademica' 
                AND CodigoModalidadCurso = '$CodigoModalidadCurso' 
                AND SiglaMateria = '$SiglaMateria' 
                AND CodigoSEA = '$CodigoSEA' 
                AND Grupo = '$Grupo'
                ORDER BY CodigoCarrera, NumeroPlanEstudios, GestionAcademica, CodigoModalidadCurso, SiglaMateria, CodigoSEA, Grupo";
        
        $consulta = $conexion->prepare($sql);
        $consulta->execute();
        $arr = $consulta->errorInfo();

        if($arr[0]!='00000'){echo "\nPDOStatement::errorInfo():\n"; print_r($arr);}

        $registros = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($registros) {
		    return $registros;
		} else {
			return false;
		};
    }
}

?>