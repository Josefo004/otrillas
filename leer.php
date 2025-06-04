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

function verificarSEA($codSEA, $datos) {
    $sea = getSEA($codSEA);

    if(floatval($datos['Tarea:Notas de Práctica (Real)']) > $sea['practicas']) {
        $datos['Practicas Rev. SEA'] = "practicas excel = ".$datos['Tarea:Notas de Práctica (Real)']." - practicasSEA = ".$sea['practicas'];
    }

    if(floatval($datos['Total Parcial']) > $sea['parciales']) {
        $datos['Parciales Rev. SEA'] = "parciales excel = ".$datos['Total Parcial']." - parcialesSEA = ".$sea['parciales'];
    }

    if(floatval($datos['Cuestionario:EXAMEN FINAL (Real)']) > $sea['final']){
        $datos['Final Rev. SEA'] = "final excel = ".$datos['Cuestionario:EXAMEN FINAL (Real)']." - finalSEA = ".$sea['final'];
    }

    if(floatval($datos['Cuestionario:EXAMEN FINAL (Real)']) + floatval($datos['Total Parcial']) + floatval($datos['Tarea:Notas de Práctica (Real)']) > 100){
        $datos['Total Rev. SEA'] = "total excel = ".(floatval($datos['Cuestionario:EXAMEN FINAL (Real)']) + floatval($datos['Total Parcial']) + floatval($datos['Tarea:Notas de Práctica (Real)']))." - totalSEA = 100";
    }

    if (floatval($datos['Segunda Instancia (Real)']) > $sea['final']) {
        $datos['Segunda Instancia Rev. SEA'] = "segunda instancia excel = ".$datos['Segunda Instancia (Real)']." - finalSEA = ".$sea['final'];
    }
    
    return $datos;

    
}


// leer.php
function newArray($cabecera, $datos) {
    $narray = [];
    foreach ($cabecera as $key => $value) {
        $narray[trim($value)] = (trim($datos[$key]) === '') ? '0' : trim(str_replace(',', '.', $datos[$key]));
    }
    return $narray;
}

function promediotareas($datos) {
    $sumaTareas = 0;
    $cantidadTareas = 0;

    foreach ($datos as $clave => $valor) {
        // Detectamos tareas válidas (empiezan con "Tarea" y NO contienen ":")
        if (str_starts_with($clave, 'Tarea') && !str_contains($clave, ':')) {
            $sumaTareas += floatval($valor);
            $cantidadTareas++;
        }
    }
    // Calcular el promedio si hay tareas
    $promedioTareas = $cantidadTareas > 0 ? round($sumaTareas / $cantidadTareas, 2) : 0;

    // Agregamos el nuevo cálculo al array
    $datos['Tarea:Notas de Práctica (Real) Calculado'] = $promedioTareas;
    $datos['Dif. Tarea:Notas de Práctica (Real)'] = floatval($datos['Tarea:Notas de Práctica (Real)']) - $promedioTareas;
    return $datos;
}

function promedioParciales($datos) {
    $sumaParciales = 0;
    $cantidadParciales = 0;

    foreach ($datos as $clave => $valor) {
        // Detectamos parciales válidos (empiezan con "Parcial" y NO contienen ":")
        $nClave = strtoupper($clave);
        if (strpos($nClave, 'PARCIAL (REAL)') !== false){
            $sumaParciales += floatval($valor);
            $cantidadParciales++;
        }
    }
    // Calcular el promedio si hay parciales
    $promedioParciales = $cantidadParciales > 0 ? round($sumaParciales / $cantidadParciales, 2) : 0;

    // Agregamos el nuevo cálculo al array
    $datos['Total Parcial Calculado'] = $promedioParciales;
    $datos['Dif. Total Parcial Calculado'] = floatval($datos['Total Parcial']) - $promedioParciales;
    return $datos;
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
            $nuevoArray = promediotareas($nuevoArray); // Calcular el promedio de tareas
            $nuevoArray = promedioParciales($nuevoArray); // Calcular el promedio de parciales
            $nuevoArray = verificarSEA($nuevoArray['SEA'], $nuevoArray); // Verificar SEA
            $resultado[] = $nuevoArray; // Agregar el nuevo array al resultado
        }
        $k++;
    }
    fclose($archivo);
    echo '<pre>'.var_export($resultado, true).'</pre>';
} else {
    echo "Error al abrir el archivo.";
}

?>