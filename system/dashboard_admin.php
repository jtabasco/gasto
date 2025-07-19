<?php
session_start();

if(empty($_SESSION['active'])){
    header('location: ../');
}

include "../conexion.php";

// Obtener datos del dashboard
function getDashboardData() {
    global $conn;
    
    $data = [];
    
    // 1. Resumen general del mes actual
    $mes_actual = date('Y-m');
    $sql_resumen = "SELECT 
                        COUNT(DISTINCT u.id) as total_usuarios,
                        COUNT(DISTINCT f.id) as total_familias,
                        SUM(CASE WHEN dg.pagado = 'No' THEN dg.monto ELSE 0 END) as total_deudas_pendientes,
                        COUNT(CASE WHEN dg.pagado = 'No' THEN 1 END) as cantidad_deudas_pendientes
                    FROM usuarios u
                    LEFT JOIN familia f ON u.familia_id = f.id
                    LEFT JOIN detalle_gasto dg ON u.id = dg.deudor
                    LEFT JOIN Gastos g ON dg.idgasto = g.id
                    WHERE u.activo = 'Si'";
    
    $result_resumen = $conn->query($sql_resumen);
    $data['resumen'] = $result_resumen->fetch_assoc();
    
    // 2. Deudas por familia
    $sql_familias = "SELECT 
                        f.nombre as familia,
                        COUNT(DISTINCT u.id) as usuarios_activos,
                        SUM(CASE WHEN dg.pagado = 'No' THEN dg.monto ELSE 0 END) as deudas_pendientes,
                        COUNT(CASE WHEN dg.pagado = 'No' THEN 1 END) as cantidad_deudas
                    FROM familia f
                    LEFT JOIN usuarios u ON f.id = u.familia_id AND u.activo = 'Si'
                    LEFT JOIN detalle_gasto dg ON u.id = dg.deudor
                    LEFT JOIN Gastos g ON dg.idgasto = g.id AND (g.fecha LIKE '$mes_actual%' OR g.fecha IS NULL)
                    GROUP BY f.id, f.nombre
                    ORDER BY deudas_pendientes DESC";
    
    $result_familias = $conn->query($sql_familias);
    $data['familias'] = [];
    while($row = $result_familias->fetch_assoc()) {
        $data['familias'][] = $row;
    }
    
    // 3. Usuarios con más deudas pendientes
    $sql_usuarios_deudas = "SELECT 
                                u.nombre,
                                f.nombre as familia,
                                SUM(CASE WHEN dg.pagado = 'No' THEN dg.monto ELSE 0 END) as deuda_total,
                                COUNT(CASE WHEN dg.pagado = 'No' THEN 1 END) as cantidad_deudas,
                                MAX(CASE WHEN dg.pagado = 'No' THEN DATEDIFF(CURDATE(), g.fecha) ELSE 0 END) as dias_mora_max,
                                AVG(CASE WHEN dg.pagado = 'No' THEN DATEDIFF(CURDATE(), g.fecha) ELSE 0 END) as dias_mora_promedio
                            FROM usuarios u
                            LEFT JOIN familia f ON u.familia_id = f.id
                            LEFT JOIN detalle_gasto dg ON u.id = dg.deudor
                            LEFT JOIN Gastos g ON dg.idgasto = g.id
                            WHERE u.activo = 'Si'
                            GROUP BY u.id, u.nombre, f.nombre
                            HAVING deuda_total > 0
                            ORDER BY deuda_total DESC
                            LIMIT 10";
    
    $result_usuarios = $conn->query($sql_usuarios_deudas);
    $data['usuarios_deudas'] = [];
    while($row = $result_usuarios->fetch_assoc()) {
        $data['usuarios_deudas'][] = $row;
    }
    
    // 4. Deudas por días de mora
    $sql_mora = "SELECT 
                    CASE 
                        WHEN DATEDIFF(CURDATE(), g.fecha) <= 7 THEN '1-7 días'
                        WHEN DATEDIFF(CURDATE(), g.fecha) <= 15 THEN '8-15 días'
                        WHEN DATEDIFF(CURDATE(), g.fecha) <= 30 THEN '16-30 días'
                        ELSE 'Más de 30 días'
                    END as rango_dias,
                    COUNT(*) as cantidad,
                    SUM(dg.monto) as monto_total
                FROM detalle_gasto dg
                INNER JOIN Gastos g ON dg.idgasto = g.id
                WHERE dg.pagado = 'No'
                GROUP BY rango_dias
                ORDER BY FIELD(rango_dias, '1-7 días', '8-15 días', '16-30 días', 'Más de 30 días')";
    
    $result_mora = $conn->query($sql_mora);
    $data['mora'] = [];
    while($row = $result_mora->fetch_assoc()) {
        $data['mora'][] = $row;
    }
    
    // 5. Alertas críticas
    $sql_alertas = "SELECT 
                        u.nombre,
                        f.nombre as familia,
                        SUM(dg.monto) as deuda_total,
                        MAX(DATEDIFF(CURDATE(), g.fecha)) as dias_mora,
                        COUNT(*) as cantidad_deudas
                    FROM usuarios u
                    LEFT JOIN familia f ON u.familia_id = f.id
                    INNER JOIN detalle_gasto dg ON u.id = dg.deudor
                    INNER JOIN Gastos g ON dg.idgasto = g.id
                    WHERE dg.pagado = 'No'
                    GROUP BY u.id, u.nombre, f.nombre
                    HAVING deuda_total > 1000 OR dias_mora > 30
                    ORDER BY deuda_total DESC, dias_mora DESC";
    
    $result_alertas = $conn->query($sql_alertas);
    $data['alertas'] = [];
    while($row = $result_alertas->fetch_assoc()) {
        $data['alertas'][] = $row;
    }
    
    return $data;
}

$dashboard_data = getDashboardData();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo - Sistema de Gastos</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .alert-card {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .success-card {
            background: linear-gradient(135deg, #2ed573 0%, #1e90ff 100%);
            color: white;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .info-card {
            background: linear-gradient(135deg, #3742fa 0%, #2f3542 100%);
            color: white;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .table-custom {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .table-custom thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
        }
        
        .badge-mora {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
        }
        
        .badge-success {
            background: linear-gradient(135deg, #2ed573 0%, #1e90ff 100%);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .dashboard-card {
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }
        .dashboard-row {
            gap: 24px;
        }
        .chart-container-fixed {
            max-width: 480px;
            max-height: 320px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .metric-card-vibrant {
            min-height: 120px;
            display: flex;
            flex-direction: row;
            align-items: center;
            border-radius: 24px;
            box-shadow: 0 6px 32px 0 rgba(80,80,160,0.10);
            padding: 24px 32px;
            margin-bottom: 20px;
            color: #fff;
            font-weight: 500;
        }
        .metric-card-vibrant .icon {
            font-size: 3rem;
            margin-right: 24px;
            flex-shrink: 0;
        }
        .metric-card-users {
            background: linear-gradient(90deg, #4f8cff 0%, #005bea 100%);
        }
        .metric-card-families {
            background: linear-gradient(90deg, #a259ff 0%, #6d28d9 100%);
        }
        .metric-card-debt {
            background: linear-gradient(90deg, #ff6b6b 0%, #ff3c3c 100%);
        }
        .metric-card-vibrant h3 {
            margin-bottom: 0;
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
        }
        .metric-card-vibrant p {
            margin-bottom: 0;
            font-size: 1.2rem;
            color: #fff;
            opacity: 0.95;
        }
        
        @media (max-width: 768px) {
            .metric-card, .alert-card, .success-card, .info-card {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .chart-container {
                padding: 15px;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body style="background-color: #f8f9fa;">
    <?php include "../include/nav.php"; ?>
    
    <div class="container-fluid mt-5 pt-4">
        <!-- Header del Dashboard -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="metric-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-0"><i class="bi bi-graph-up"></i> Dashboard Administrativo</h2>
                            <p class="mb-0 opacity-75">Resumen del mes: <?php echo date('F Y'); ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="generar_reporte_dashboard.php" class="btn btn-light me-2" target="_blank">
                                <i class="bi bi-file-earmark-text"></i> Generar Reporte
                            </a>
                            <button class="btn btn-light" onclick="window.print()">
                                <i class="bi bi-printer"></i> Imprimir Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Métricas Principales -->
        <div class="row mb-4 justify-content-center dashboard-row">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="metric-card-vibrant metric-card-users">
                    <i class="bi bi-people-fill icon"></i>
                    <div>
                        <h3><?php echo number_format($dashboard_data['resumen']['total_usuarios']); ?></h3>
                        <p>Usuarios Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="metric-card-vibrant metric-card-families">
                    <i class="bi bi-house-heart-fill icon"></i>
                    <div>
                        <h3><?php echo number_format($dashboard_data['resumen']['total_familias']); ?></h3>
                        <p>Familias</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="metric-card-vibrant metric-card-debt">
                    <i class="bi bi-exclamation-triangle-fill icon"></i>
                    <div>
                        <h3>$<?php echo number_format($dashboard_data['resumen']['total_deudas_pendientes'], 2); ?></h3>
                        <p>Deudas Pendientes</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gráficos -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="chart-container-fixed">
                    <h5><i class="bi bi-pie-chart"></i> Deudas por Días de Mora</h5>
                    <canvas id="moraChart" width="420" height="260"></canvas>
                </div>
            </div>
            
            <div class="col-lg-6 mb-3">
                <div class="chart-container-fixed">
                    <h5><i class="bi bi-bar-chart"></i> Deudas por Familia</h5>
                    <canvas id="familiasChart" width="420" height="260"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Alertas Críticas -->
        <?php if (!empty($dashboard_data['alertas'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert-card">
                    <h5><i class="bi bi-exclamation-triangle-fill"></i> Alertas Críticas</h5>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Familia</th>
                                    <th>Deuda Total</th>
                                    <th>Días de Mora</th>
                                    <th>Cantidad Deudas</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dashboard_data['alertas'] as $alerta): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($alerta['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($alerta['familia']); ?></td>
                                    <td><span class="badge-mora">$<?php echo number_format($alerta['deuda_total'], 2); ?></span></td>
                                    <td>
                                        <?php if ($alerta['dias_mora'] > 30): ?>
                                            <span class="badge bg-danger"><?php echo $alerta['dias_mora']; ?> días</span>
                                        <?php elseif ($alerta['dias_mora'] > 15): ?>
                                            <span class="badge bg-warning"><?php echo $alerta['dias_mora']; ?> días</span>
                                        <?php else: ?>
                                            <span class="badge bg-info"><?php echo $alerta['dias_mora']; ?> días</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $alerta['cantidad_deudas']; ?></td>
                                    <td>
                                        <?php if ($alerta['deuda_total'] > 1000): ?>
                                            <span class="badge bg-danger">Deuda Alta</span>
                                        <?php elseif ($alerta['dias_mora'] > 30): ?>
                                            <span class="badge bg-warning">Mora Crítica</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Tabla de Familias -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="info-card">
                    <h5><i class="bi bi-house-heart"></i> Resumen por Familia</h5>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Familia</th>
                                    <th>Usuarios Activos</th>
                                    <th>Deudas Pendientes</th>
                                    <th>Cantidad Deudas</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dashboard_data['familias'] as $familia): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($familia['familia']); ?></strong></td>
                                    <td><?php echo $familia['usuarios_activos']; ?></td>
                                    <td>
                                        <?php if ($familia['deudas_pendientes'] > 0): ?>
                                            <span class="badge-mora">$<?php echo number_format($familia['deudas_pendientes'], 2); ?></span>
                                        <?php else: ?>
                                            <span class="badge-success">$0.00</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $familia['cantidad_deudas']; ?></td>
                                    <td>
                                        <?php if ($familia['deudas_pendientes'] == 0): ?>
                                            <span class="badge bg-success">Al día</span>
                                        <?php elseif ($familia['deudas_pendientes'] > 500): ?>
                                            <span class="badge bg-danger">Deuda Alta</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Deuda Moderada</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top 10 Usuarios con Más Deudas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="info-card">
                    <h5><i class="bi bi-person-exclamation"></i> Top 10 Usuarios con Más Deudas</h5>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Familia</th>
                                    <th>Deuda Total</th>
                                    <th>Cantidad Deudas</th>
                                    <th>Días Mora Máx</th>
                                    <th>Días Mora Prom</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dashboard_data['usuarios_deudas'] as $usuario): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($usuario['familia']); ?></td>
                                    <td><span class="badge-mora">$<?php echo number_format($usuario['deuda_total'], 2); ?></span></td>
                                    <td><?php echo $usuario['cantidad_deudas']; ?></td>
                                    <td>
                                        <?php if ($usuario['dias_mora_max'] > 30): ?>
                                            <span class="badge bg-danger"><?php echo $usuario['dias_mora_max']; ?> días</span>
                                        <?php elseif ($usuario['dias_mora_max'] > 15): ?>
                                            <span class="badge bg-warning"><?php echo $usuario['dias_mora_max']; ?> días</span>
                                        <?php else: ?>
                                            <span class="badge bg-info"><?php echo $usuario['dias_mora_max']; ?> días</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo round($usuario['dias_mora_promedio'], 1); ?> días</td>
                                    <td>
                                        <?php if ($usuario['deuda_total'] > 1000): ?>
                                            <span class="badge bg-danger">Crítico</span>
                                        <?php elseif ($usuario['dias_mora_max'] > 30): ?>
                                            <span class="badge bg-warning">Mora Alta</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Gráfico de Mora
        const moraCtx = document.getElementById('moraChart').getContext('2d');
        const moraChart = new Chart(moraCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($dashboard_data['mora'], 'rango_dias')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($dashboard_data['mora'], 'monto_total')); ?>,
                    backgroundColor: [
                        '#2ed573',
                        '#ffa502',
                        '#ff6348',
                        '#ff4757'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Gráfico de Familias
        const familiasCtx = document.getElementById('familiasChart').getContext('2d');
        const familiasChart = new Chart(familiasCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($dashboard_data['familias'], 'familia')); ?>,
                datasets: [{
                    label: 'Deudas Pendientes',
                    data: <?php echo json_encode(array_column($dashboard_data['familias'], 'deudas_pendientes')); ?>,
                    backgroundColor: 'rgba(255, 107, 107, 0.8)',
                    borderColor: 'rgba(255, 107, 107, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
        
        // Auto-refresh cada 5 minutos
        setInterval(function() {
            location.reload();
        }, 300000);
    </script>
</body>
</html> 
