<?php
// Archivo de prueba para dividirImporte

function dividirImporte(float $importe, int $partes): array {
    if ($partes <= 0) {
        return [];
    }
    $total_centimos = (int) round($importe * 100);
    $base_centimos = (int) ($total_centimos / $partes);
    $resto = $total_centimos % $partes;
    $resultado = [];
    for ($i = 0; $i < $partes; $i++) {
        $centimos_actuales = $base_centimos;
        if ($i < $resto) {
            $centimos_actuales += 1;
        }
        $resultado[] = number_format($centimos_actuales / 100, 2, '.', '');
    }
    return $resultado;
}

// Casos de prueba
$casos = [
    [10, 3],
    [100, 3],
    [99.99, 3],
    [50, 7],
    [1, 2],
    [0.99, 3],
    [100, 1],
    [0, 3],
    [67.47, 4],
];

foreach ($casos as $caso) {
    $importe = $caso[0];
    $partes = $caso[1];
    $resultado = dividirImporte($importe, $partes);
    echo "<script>console.log('Importe: $importe | Partes: $partes | Resultado:', " . json_encode($resultado) . ");</script>\n";
}

// También mostrar el resultado en HTML para referencia
foreach ($casos as $caso) {
    $importe = $caso[0];
    $partes = $caso[1];
    $resultado = dividirImporte($importe, $partes);
    echo "<div>Importe: $importe | Partes: $partes | Resultado: [" . implode(', ', $resultado) . "]</div>\n";
} 