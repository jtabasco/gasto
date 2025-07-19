<?php
// Si se solicita solo la fecha más reciente, devolverla en JSON
if (isset($_POST['solo_fecha']) && $_POST['solo_fecha'] == '1') {
    $readme = file_get_contents('../README.md');
    $lineas = explode("\n", $readme);
    $enSeccion = false;
    foreach ($lineas as $linea) {
        if (strpos($linea, '## 📝 Cambios Recientes') !== false) {
            $enSeccion = true;
            continue;
        }
        if ($enSeccion && strpos($linea, '### ') === 0) {
            $fecha = trim(str_replace('### ', '', $linea));
            echo json_encode(['fecha' => $fecha]);
            exit;
        }
    }
    echo json_encode(['fecha' => null]);
    exit;
}

// Leer el archivo README.md
$readme = file_get_contents('../README.md');

// Convertir el contenido a HTML
$html = '<div class="list-group">';

// Dividir el contenido en líneas
$lineas = explode("\n", $readme);

$enSeccion = false;
$bloqueAbierto = false;
$primeraFecha = true; // Para no poner <hr> antes del primer bloque

foreach ($lineas as $linea) {
    // Buscar la sección de cambios recientes
    if (strpos($linea, '## 📝 Cambios Recientes') !== false) {
        $enSeccion = true;
        continue;
    }
    
    // Si estamos en la sección de cambios y encontramos otro encabezado, terminamos
    if ($enSeccion && strpos($linea, '## ') === 0) {
        break;
    }
    
    // Si estamos en la sección de cambios, procesamos la línea
    if ($enSeccion && trim($linea) !== '') {
        if (strpos($linea, '### ') === 0) {
            // Cerrar bloque anterior si está abierto
            if ($bloqueAbierto) {
                $html .= '</div>';
                $html .= '<hr class="my-2">'; // Línea separadora
            }
            // Es una fecha de actualización
            $fecha = trim(str_replace('### ', '', $linea));
            $html .= '<div class="list-group-item list-group-item-primary">';
            $html .= '<h6 class="mb-1">' . $fecha . '</h6>';
            $bloqueAbierto = true;
        } elseif (strpos($linea, '- ') === 0) {
            // Es un cambio
            $cambio = trim(str_replace('- ', '', $linea));
            $html .= '<div class="list-group-item">';
            $html .= '<p class="mb-1">' . $cambio . '</p>';
            $html .= '</div>';
        }
    }
}

// Cerrar el último bloque si está abierto (sin <hr> final)
if ($bloqueAbierto) {
    $html .= '</div>';
}

$html .= '</div>';

// Si no hay cambios recientes, mostrar un mensaje
if (!$enSeccion) {
    $html = '<div class="alert alert-info">No hay cambios recientes registrados.</div>';
}

echo $html;
?> 