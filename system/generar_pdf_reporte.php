<?php
session_start();
include "../conexion.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['user'])) {
    die('Usuario no autenticado');
}

// Obtener parámetros
$ano = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
$mes = isset($_GET['mes']) ? $_GET['mes'] : date('n');

// Nombres de los meses
$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

// Obtener totales
$sql_total = "SELECT sum(Monto) as importe 
              FROM `Gastos` 
              inner join usuarios on usuarios.id=Gastos.comprador 
              where usuarios.familia_id = '" . $_SESSION['familia'] . "' 
              and month(fecha)='$mes' 
              and year(fecha)='$ano'";
$result_total = $conn->query($sql_total);
$total = $result_total->fetch_object()->importe ?? 0;

$sql_pagado = "SELECT sum(importe) as importe 
               FROM unionXvat 
               where deudor='" . $_SESSION['idUser'] . "' 
               and month(fecha)='$mes' 
               and year(fecha)='$ano' 
               GROUP by year(fecha),month(fecha)";
$result_pagado = $conn->query($sql_pagado);
$pagado = $result_pagado->fetch_object()->importe ?? 0;

// Obtener datos por comprador
$sql_compradores = "SELECT usuarios.nombre, sum(Monto) as importe 
                    FROM `Gastos` 
                    inner join usuarios on usuarios.id=Gastos.comprador 
                    where usuarios.familia_id = '" . $_SESSION['familia'] . "' 
                    and year(fecha)='$ano' 
                    and month(fecha)='$mes' 
                    GROUP by year(fecha),month(fecha),comprador";
$result_compradores = $conn->query($sql_compradores);
$compradores = [];
while ($row = $result_compradores->fetch_object()) {
    $compradores[] = $row;
}

// Obtener datos por categoría
$sql_categorias = "SELECT categoria.categoria, sum(Monto) as importe 
                   FROM `Gastos` 
                   inner join categoria on categoria.id=Gastos.idcat 
                   inner join usuarios on usuarios.id=Gastos.comprador 
                   where usuarios.familia_id = '" . $_SESSION['familia'] . "' 
                   and year(fecha)='$ano' 
                   and month(fecha)='$mes' 
                   GROUP by year(fecha),month(fecha),categoria";
$result_categorias = $conn->query($sql_categorias);
$categorias = [];
while ($row = $result_categorias->fetch_object()) {
    $porcentaje = $total > 0 ? ($row->importe / $total) * 100 : 0;
    $categorias[] = [
        'categoria' => $row->categoria,
        'importe' => $row->importe,
        'porcentaje' => number_format($porcentaje, 1),
        'porcentaje_num' => $porcentaje // Para ordenar
    ];
}

// Ordenar por porcentaje descendente
usort($categorias, function($a, $b) {
    return $b['porcentaje_num'] <=> $a['porcentaje_num'];
});

// Obtener datos pagados por categoría
$sql_pagado_cat = "SELECT categoria, sum(importe) as importe 
                   FROM unionXvat 
                   where deudor='" . $_SESSION['idUser'] . "' 
                   and year(fecha)='$ano' 
                   and month(fecha)='$mes' 
                   GROUP by year(fecha),month(fecha),categoria";
$result_pagado_cat = $conn->query($sql_pagado_cat);
$pagado_categorias = [];
while ($row = $result_pagado_cat->fetch_object()) {
    $porcentaje = $pagado > 0 ? ($row->importe / $pagado) * 100 : 0;
    $pagado_categorias[] = [
        'categoria' => $row->categoria,
        'importe' => $row->importe,
        'porcentaje' => number_format($porcentaje, 1),
        'porcentaje_num' => $porcentaje // Para ordenar
    ];
}

// Ordenar por porcentaje descendente
usort($pagado_categorias, function($a, $b) {
    return $b['porcentaje_num'] <=> $a['porcentaje_num'];
});

// Cerrar conexiones
if (isset($result_total)) $result_total->close();
if (isset($result_pagado)) $result_pagado->close();
if (isset($result_compradores)) $result_compradores->close();
if (isset($result_categorias)) $result_categorias->close();
if (isset($result_pagado_cat)) $result_pagado_cat->close();
if (isset($conn)) $conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Gastos - <?php echo $meses[$mes] . ' ' . $ano; ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #007bff;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        .header h2 {
            color: #666;
            margin: 0 0 5px 0;
            font-size: 18px;
        }
        
        .header p {
            margin: 0;
            color: #888;
            font-size: 14px;
        }
        
        .summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #007bff;
        }
        
        .summary h3 {
            margin: 0 0 15px 0;
            color: #007bff;
            font-size: 16px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }
        
        .summary-row.total {
            font-weight: bold;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section h3 {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
            white-space: nowrap;
        }
        
        .percentage {
            text-align: center;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
        
        @media print {
            .print-button {
                display: none;
            }
        }
        
        /* Mejoras para móvil */
        @media (max-width: 768px) {
            body {
                margin: 10px;
                font-size: 14px;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .header h2 {
                font-size: 16px;
            }
            
            .summary {
                padding: 15px;
            }
            
            .summary-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .summary-row .amount {
                align-self: flex-end;
                margin-top: -20px;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 8px 6px;
            }
            
            .amount {
                font-size: 12px;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Imprimir PDF
    </button>
    
    <div class="header">
        <h1>REPORTE DE GASTOS</h1>
        <h2><?php echo $meses[$mes] . ' ' . $ano; ?></h2>
        <p>Familia: <?php echo $_SESSION['familia_nombre']; ?></p>
    </div>
    
    <div class="summary">
        <h3>RESUMEN DE TOTALES</h3>
        <div class="summary-row">
            <span>Total Gastos:</span>
            <span class="amount">$ <?php echo number_format($total, 2); ?></span>
        </div>
        <div class="summary-row">
            <span>Total Pagado:</span>
            <span class="amount">$ <?php echo number_format($pagado, 2); ?></span>
        </div>
    </div>
    
    <?php if (!empty($compradores)): ?>
    <div class="section">
        <h3>GASTOS POR COMPRADOR</h3>
        <table>
            <thead>
                <tr>
                    <th>Comprador</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($compradores as $comprador): ?>
                <tr>
                    <td><?php echo htmlspecialchars($comprador->nombre); ?></td>
                    <td class="amount">$ <?php echo number_format($comprador->importe, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($categorias)): ?>
    <div class="section">
        <h3>GASTOS POR CATEGORÍA</h3>
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Importe</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                <tr>
                    <td><?php echo htmlspecialchars($categoria['categoria']); ?></td>
                    <td class="amount">$ <?php echo number_format($categoria['importe'], 2); ?></td>
                    <td class="percentage"><?php echo $categoria['porcentaje']; ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($pagado_categorias)): ?>
    <div class="section">
        <h3>PAGADO POR CATEGORÍA</h3>
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Importe</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagado_categorias as $categoria): ?>
                <tr>
                    <td><?php echo htmlspecialchars($categoria['categoria']); ?></td>
                    <td class="amount">$ <?php echo number_format($categoria['importe'], 2); ?></td>
                    <td class="percentage"><?php echo $categoria['porcentaje']; ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <div class="footer">
        <p>Generado el: <?php echo date('d/m/Y H:i:s'); ?> por <?php echo htmlspecialchars($_SESSION['user']); ?></p>
        <p>Sistema de Gestión de Gastos Familiares</p>
    </div>
</body>
</html> 