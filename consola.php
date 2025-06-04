<?php

class Leer {
    public function saludar(): void
    {
        echo "Hola desde consola\n";
    }
}

// Verificamos que haya un argumento
if ($argc < 2) {
    echo "Uso: php consola.php Clase/metodo\n";
    exit(1);
}

// Obtenemos clase y método del argumento
[$claseMetodo] = array_slice($argv, 1);
[$clase, $metodo] = explode('/', $claseMetodo);

// Convertimos el nombre de la clase a formato de PHP (opcional, si usas namespaces o camelCase)
$clase = ucfirst(strtolower($clase));

// Verificamos si la clase existe
if (!class_exists($clase)) {
    echo "Error: Clase '$clase' no encontrada.\n";
    exit(1);
}

// Instanciamos la clase
$instancia = new $clase();

// Verificamos si el método existe
if (!method_exists($instancia, $metodo)) {
    echo "Error: Método '$metodo' no encontrado en la clase '$clase'.\n";
    exit(1);
}

// Llamamos al método
$instancia->$metodo();
