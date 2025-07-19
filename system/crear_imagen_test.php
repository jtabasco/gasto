<?php
// Script simple para crear imagen de prueba

echo "Creando imagen de prueba...\n";

// Crear una imagen simple
$width = 300;
$height = 200;

// Crear imagen
$image = imagecreate($width, $height);

// Definir colores
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
$blue = imagecolorallocate($image, 0, 0, 255);

// Rellenar fondo
imagefill($image, 0, 0, $white);

// Dibujar texto
$text = "TEST MMS";
imagestring($image, 5, 100, 80, $text, $blue);

// Asegurar que el directorio existe
$dir = 'system/uploads/receipts/';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
    echo "Directorio creado: $dir\n";
}

// Guardar imagen
$filename = $dir . 'test_image.jpg';
if (imagejpeg($image, $filename, 90)) {
    echo "✅ Imagen creada exitosamente: $filename\n";
    echo "Tamaño: " . filesize($filename) . " bytes\n";
} else {
    echo "❌ Error al crear la imagen\n";
}

// Liberar memoria
imagedestroy($image);

echo "\nAhora puedes probar MMS con imagen:\n";
echo "http://tudominio.com/gasto/system/test_mms_simple.php\n";
?> 