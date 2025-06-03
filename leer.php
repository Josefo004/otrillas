<?php
// leer.php
function newArray($cabecera, $datos) {
    $narray = [];
    foreach ($cabecera as $key => $value) {
        $narray[trim($value)] = (trim($datos[$key]) === '') ? '0' : trim($datos[$key]);
    }
    return $narray;
}

function promediotareas($datos) {
    $sumaTareas = 0;
    $cantidadTareas = 0;

    foreach ($datos as $clave => $valor) {
        // Detectamos tareas válidas (empiezan con "Tarea" y NO contienen ":")
        if (str_starts_with($clave, 'Tarea') && !str_contains($clave, ':')) {
            $sumaTareas += floatval(str_replace(',', '.', $valor));
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




$archivo = fopen("CSV/dato01.csv", "r");

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