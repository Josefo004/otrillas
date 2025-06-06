<?php

function getSEA($codigo){
    $re = [];
    $codigosSEA = [
        ["CodigoSEA" => "A", "PruebasParciales" => 25, "PruebasLaboratorio" => 25, "PracticasRepasos" => 10, "PruebaFinal" => 40],
        ["CodigoSEA" => "B", "PruebasParciales" => 35, "PruebasLaboratorio" => 0, "PracticasRepasos" => 25, "PruebaFinal" => 40],
        ["CodigoSEA" => "C", "PruebasParciales" => 35, "PruebasLaboratorio" => 0, "PracticasRepasos" => 15, "PruebaFinal" => 50],
        ["CodigoSEA" => "D", "PruebasParciales" => 50, "PruebasLaboratorio" => 0, "PracticasRepasos" => 20, "PruebaFinal" => 30],
        ["CodigoSEA" => "E", "PruebasParciales" => 35, "PruebasLaboratorio" => 0, "PracticasRepasos" => 35, "PruebaFinal" => 30],
        ["CodigoSEA" => "F", "PruebasParciales" => 40, "PruebasLaboratorio" => 0, "PracticasRepasos" => 40, "PruebaFinal" => 20],
        ["CodigoSEA" => "G", "PruebasParciales" => 40, "PruebasLaboratorio" => 0, "PracticasRepasos" => 20, "PruebaFinal" => 40],
        ["CodigoSEA" => "H", "PruebasParciales" => 30, "PruebasLaboratorio" => 0, "PracticasRepasos" => 40, "PruebaFinal" => 30],
        ["CodigoSEA" => "I", "PruebasParciales" => 30, "PruebasLaboratorio" => 25, "PracticasRepasos" => 10, "PruebaFinal" => 35],
        ["CodigoSEA" => "J", "PruebasParciales" => 33, "PruebasLaboratorio" => 0, "PracticasRepasos" => 33, "PruebaFinal" => 34],
        ["CodigoSEA" => "K", "PruebasParciales" => 35, "PruebasLaboratorio" => 25, "PracticasRepasos" => 0, "PruebaFinal" => 40],
        ["CodigoSEA" => "V", "PruebasParciales" => 50, "PruebasLaboratorio" => 0, "PracticasRepasos" => 0, "PruebaFinal" => 50],
        ["CodigoSEA" => "W", "PruebasParciales" => 50, "PruebasLaboratorio" => 0, "PracticasRepasos" => 0, "PruebaFinal" => 50]
    ];

    foreach ($codigosSEA as $sea) {
        if ($sea['CodigoSEA'] === $codigo) {
            $re = [
                'parciales' => $sea['PruebasParciales'], 'laboratorio' => $sea['PruebasLaboratorio'], 
                'practicas' => $sea['PracticasRepasos'], 'final' => $sea['PruebaFinal']
            ];
            break;
        }
    }
    
    return $re;
}

// leer.php
function newArray($cabecera, $datos) {
    $narray = [];
    foreach ($cabecera as $key => $value) {
        $narray[trim($value)] = (trim($datos[$key]) === '') ? '0' : trim(str_replace(',', '.', $datos[$key]));
    }
    $narray['errores'] = [];
    return $narray;
}

function Revtareas($datos, $sea) {
    $sumaTareas = 0;
    $cantidadTareas = 0;
    $practicas = $sea['practicas'];

    foreach ($datos as $clave => $valor) {
        // Detectamos tareas válidas (empiezan con "Tarea" y NO contienen ":")
        if (str_starts_with($clave, 'Tarea') && !str_contains($clave, ':')) {
            $tareaX = floatval($valor);
            if ($tareaX > $practicas) {
                $datos['errores'][] = ['Tipo' => 'Tarea', 'campo'=>$clave, 'valorEXCEL'=>$valor, 'valorSEA'=> $practicas, 'msg'=>'la nota del campo '.$clave.' es mayor al SEA'];
            }
            $sumaTareas += $tareaX;
            $cantidadTareas++;

        }
    }

    // Calcular el promedio si hay tareas
    $promedioTareas = $cantidadTareas > 0 ? round($sumaTareas / $cantidadTareas, 2) : 0;

    if ($promedioTareas > $practicas) {
        $datos['errores'][] = ['Tipo'=>'Tarea Promedio', 'campo' =>'Tarea:Notas de Práctica (Real)', 'valorEXCEL'=>$datos['Tarea:Notas de Práctica (Real)'], 'ValorCalculado' =>$promedioTareas, 'valorSEA'=> $practicas, 'msg'=>'el promedio de tareas es mayor al SEA'];
    }

    if ($promedioTareas - floatval($datos['Tarea:Notas de Práctica (Real)']) != 0) {
        $datos['errores'][] = ['Tipo'=>'Tarea Promedio', 'campo' =>'Tarea:Notas de Práctica (Real)', 'valorEXCEL'=>$datos['Tarea:Notas de Práctica (Real)'], 'ValorCalculado' =>$promedioTareas, 'msg'=>'Hay diferencia entre Tarea:Notas de Práctica (Real) y su valor calculado'];
    }
    
    return $datos;
}

function promedioParciales($datos, $sea) {
    $sumaParciales = 0;
    $cantidadParciales = 0;


    foreach ($datos as $clave => $valor) {
        // Detectamos parciales válidos (empiezan con "Parcial" y NO contienen ":")
        $nClave = strtoupper($clave);
        if (strpos($nClave, 'PARCIAL (REAL)') !== false){
            $parcialX = floatval($valor);
            if ($parcialX > $sea['parciales']) {
                $datos['errores'][] = ['Tipo' => 'Parcial', 'campo'=>$clave, 'valorEXCEL'=>$valor, 'valorSEA'=> $sea['parciales'], 'msg'=>'la nota del campo '.$clave.' es mayor al SEA'];
            }
            $sumaParciales += $parcialX;
            $cantidadParciales++;
        }
    }
    // Calcular el promedio si hay parciales
    $promedioParciales = $cantidadParciales > 0 ? round($sumaParciales / $cantidadParciales, 2) : 0;
    if ($promedioParciales > $sea['parciales']) {
        $datos['errores'][] = ['Tipo'=>'Parcial Promedio', 'campo' =>'Total Parcial', 'valorEXCEL'=>$datos['Total Parcial'], 'ValorCalculado' =>$promedioParciales, 'valorSEA'=> $sea['parciales'], 'msg'=>'el promedio de parciales es mayor al SEA'];
    }
    if ($promedioParciales - floatval($datos['Total Parcial']) != 0) {
        $datos['errores'][] = ['Tipo'=>'Parcial Promedio', 'campo' =>'Total Parcial', 'valorEXCEL'=>$datos['Total Parcial'], 'ValorCalculado' =>$promedioParciales, 'msg'=>'Hay diferencia entre Total Parcial y su valor calculado'];
    }
    
    return $datos;
}

function generarSQL($datos) {
    $CodigoCarrera = '127';
    $NumeroPlanEstudios = '1';
    $GestionAcademica = '2025';
    $CodigoModalidadCurso = 'NA';
    $SiglaMateria = 'CJS102';
    $CodigoSEA = $datos['SEA'];
    $Grupo = $datos['Grupo'];
    
    $Ci = $datos['Nombre de usuario'];
    $NotasPracticas = $datos['Tarea:Notas de Práctica (Real)'];
    $PromedioParciales = $datos['Total Parcial'];
    $NotaSemifinal = $datos['Nota Entrada'];
    $ExamenFinal = $datos['Cuestionario:EXAMEN FINAL (Real)'];
    $NotaFinal = $datos['Nota Final (Real)'];
    $ExamenSegundaInstancia = $datos['Segunda Instancia (Real)'];    
    $NotaSegundaInstancia = $datos['Notas Finales (Segunda Instancia)'];
    
    
    $tareas = [];
    $campos_tarea = ['Tarea1', 'Tarea2', 'Tarea3', 'Tarea4', 'Tarea5', 'Tarea6', 'Tarea7', 'Tarea8', 'Tarea9', 'Tarea10'];
    $k=0;
    foreach ($datos as $clave => $valor) {
        if (str_starts_with($clave, 'Tarea') && !str_contains($clave, ':')) {
            $tareas[$campos_tarea[$k]] = $valor;
            $k++;
        }
    }

    $parciales = [];
    $campos_parcial = ['Parcial1', 'Parcial2', 'Parcial3'];
    $k=0;
    foreach ($datos as $clave => $valor) {
        // Detectamos parciales válidos (empiezan con "Parcial" y NO contienen ":")
        $nClave = strtoupper($clave);
        if (strpos($nClave, 'PARCIAL (REAL)') !== false){
            $parciales[$campos_parcial[$k]] = $valor;
            $k++;
        }
    }

    // echo '<pre>'.var_export($datos, true).'</pre>';
    // echo '<pre>'.var_export($tareas, true).'</pre>';
    // echo '<pre>'.var_export($parciales, true).'</pre>';

    $q = "INSERT INTO ImportarMoodleCalificaciones (Ci, CodigoCarrera, NumeroPlanEstudios, GestionAcademica, CodigoModalidadCurso, SiglaMateria, CodigoSEA, Grupo, ";
    foreach($tareas as $clave => $valor) {$q .= "$clave, ";}

    $q .= "NotasPracticas, ";
    foreach($parciales as $clave => $valor) {$q .= "$clave, ";}

    
    $q .= "PromedioParciales, NotaSemifinal, ExamenFinal, NotaFinal, ExamenSegundaInstancia, NotaSegundaInstancia, FechaHoraImportar) ";
    $q .= "VALUES ('$Ci', '$CodigoCarrera', '$NumeroPlanEstudios', '$GestionAcademica', '$CodigoModalidadCurso', '$SiglaMateria', '$CodigoSEA', '$Grupo', ";
    foreach($tareas as $clave => $valor) {
        $q .= "'" . $valor . "', ";
    }

    $q .= "'$NotasPracticas', ";
    
    foreach($parciales as $clave => $valor) {
        $q .= "'" . $valor . "', ";
    }
    $q .= "'$PromedioParciales', '$NotaSemifinal', '$ExamenFinal', '$NotaFinal', '$ExamenSegundaInstancia', '$NotaSegundaInstancia', NOW());";

    echo $q . "\n</br></br>";
}

function depurar($data) {
    $mismaSigla = $data[0]['Sigla'];
    $mismoSEA = $data[0]['SEA'];
    $mismoGrupo = $data[0]['Grupo'];
    $mismoCodigoCarrera = $data[0]['CodigoCarrera'];
    $reSigla = false;
    $reSEA = false;
    $reGrupo = false;
    $reCodigoCarrera = false;
    foreach ($data as $item) {
        if ($item['Sigla'] !== $mismaSigla) { $reSigla = true; }
        if ($item['SEA'] !== $mismoSEA) { $reSEA = true; }
        if ($item['Grupo'] !== $mismoGrupo) { $reGrupo = true; }
        if ($item['CodigoCarrera'] !== $mismoCodigoCarrera) { $reCodigoCarrera = true; }
    }
    if ($reSigla || $reSEA || $reGrupo || $reCodigoCarrera) {
        echo "<h1>Advertencia: Datos inconsistentes</h1>\n";
        if ($reSigla) { echo "<p>Las siglas de las materias no coinciden.</p>\n"; }
        if ($reSEA) { echo "<p>Los SEA no coinciden.</p>\n"; }
        if ($reGrupo) { echo "<p>Los grupos no coinciden.</p>\n"; }
        if ($reCodigoCarrera) { echo "<p>Los códigos de carrera no coinciden.</p>\n"; }
        exit();
    } else {
        echo "<h1>Datos consistentes</h1>\n";
    }

}

$archivo = fopen("CSV/dato02.csv", "r");

if ($archivo) {
    $k = 0; 
    $cabecera=[]; $resultado = [];
    while (($datos = fgetcsv($archivo, 1000, ";")) !== FALSE) {
        $x = implode("; ", $datos);
        $narray = explode(";", $x);
        if ($k == 0) {
            $cabecera = $narray; // Guardar la cabecera
        } else {
            $nuevoArray = newArray($cabecera, $narray);
            //echo '<pre>'.var_export($nuevoArray, true).'</pre>';
            $resultado[] = $nuevoArray; // Agregar el nuevo array al resultado
        }
        $k++;
    }
    fclose($archivo);

    if (count($resultado) > 0) {
        depurar($resultado); // Verificar consistencia de datos
        // Verificar si todos los datos tienen la misma sigla, SEA, grupo y código de carrera
        
        $sea = getSEA($resultado[0]['SEA']);
        foreach ($resultado as $key => &$value) {
            $value = Revtareas($value, $sea); // Calcular el promedio de tareas
            $value = promedioParciales($value, $sea); // Calcular el promedio de parciales
        }

    }

    $estudiantes_d = array_values(array_filter($resultado, function($item) {
        return (is_array($item['errores']) && count($item['errores']) > 0); // Filtrar solo los que tienen errores
    }));

    $estudiantes_d = array_map(function($item) {
        return [
            'Nombre de usuario' => $item['Nombre de usuario'],
            'Sigla' => $item['Sigla'],
            'CodigoCarrera' => $item['CodigoCarrera'],
            'SEA' => $item['SEA'],
            'Grupo' => $item['Grupo'],
            'Tarea:Notas de Práctica (Real)' => $item['Tarea:Notas de Práctica (Real)'],
            'Total Parcial' => $item['Total Parcial'],
            'Cuestionario:EXAMEN FINAL (Real)' => $item['Cuestionario:EXAMEN FINAL (Real)'],
            'errores' => is_array($item['errores']) ? implode(' | ', array_map(fn($e) => $e['msg'], $item['errores'])) : '', // Concatenamos los mensajes de error
            'erroresJson' => json_encode($item['errores'], JSON_UNESCAPED_UNICODE) // Exportamos como JSON
        ];
    }, $estudiantes_d);

    if (!empty($estudiantes_d)) {
        echo '<pre>'.var_export($estudiantes_d, true).'</pre>';
        // $nombreArchivo = "web/$SiglaMateria-$CodigoCarrera-errores_calificaciones-" . date('Ymd_His') . ".csv";

        // echo "Generando archivo: $nombreArchivo\n";
        // $fp = fopen($nombreArchivo, 'w');
        // if ($fp === false) {
        //     echo "Error al crear el archivo.\n";
        //     return;
        // }
        // // Escribir encabezados
        // fputcsv($fp, ['Cu', 'NombreUniversitario', 'CodigoCarrera', 'GestionAcademica', 'CodigoModalidadCurso', 'SiglaMateria', 'Errores', 'ErroresJson']);
        // // Escribir datos
        // foreach ($estudiantes_d as $fila) {
        //     fputcsv($fp, $fila);
        // }
        // fclose($fp);
        // echo "Archivo generado exitosamente: $nombreArchivo\n";
    }
    else {
        echo "<h1>No se encontraron errores en las calificaciones</h1>\n <br>";
        //generar sql
        // echo '<pre>'.var_export($resultado, true).'</pre>';
        foreach ($resultado as $datos) {
            generarSQL($datos);
        }
    }

} else {
    echo "Error al abrir el archivo.";
}

?>