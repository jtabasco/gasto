<?php
// Script para generar una imagen de prueba real para MMS

// Crear una imagen de prueba
$width = 400;
$height = 300;

// Crear imagen
$image = imagecreate($width, $height);

// Definir colores
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
$blue = imagecolorallocate($image, 0, 0, 255);
$red = imagecolorallocate($image, 255, 0, 0);

// Rellenar fondo
imagefill($image, 0, 0, $white);

// Dibujar un rectángulo
imagerectangle($image, 50, 50, 350, 250, $black);

// Dibujar texto
$text = "TEST MMS";
$font_size = 5;
$x = ($width - strlen($text) * imagefontwidth($font_size)) / 2;
$y = ($height - imagefontheight($font_size)) / 2;
imagestring($image, $font_size, $x, $y, $text, $blue);

// Agregar más texto
imagestring($image, 3, 100, 100, "Sistema de Gastos", $red);
imagestring($image, 3, 100, 120, "Prueba MMS", $red);
imagestring($image, 3, 100, 140, date('Y-m-d H:i:s'), $black);

// Asegurar que el directorio existe
$dir = 'system/uploads/receipts/';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Guardar imagen
$filename = $dir . 'test_image.jpg';
if (imagejpeg($image, $filename, 90)) {
    echo "✅ Imagen de prueba creada exitosamente: $filename\n";
    echo "Tamaño: " . filesize($filename) . " bytes\n";
    echo "Dimensiones: {$width}x{$height} píxeles\n";
} else {
    echo "❌ Error al crear la imagen\n";
}

// Liberar memoria
imagedestroy($image);

echo "\nAhora puedes usar esta imagen en test_mms_simple.php\n";
echo "Actualiza la variable \$imagen_path a: '$filename'\n";
?> 