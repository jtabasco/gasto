<?php
session_start();
if (!isset($_SESSION['familia'])) {
    echo json_encode(['success' => false, 'msg' => 'No hay familia en sesión']);
    exit;
}
$cacheKey = 'roles_familias_' . $_SESSION['familia'];
$cacheFile = realpath(__DIR__ . '/../cache/' . $cacheKey . '.cache');
if ($cacheFile && file_exists($cacheFile)) {
    unlink($cacheFile);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'msg' => 'Archivo de caché no encontrado']);
} 