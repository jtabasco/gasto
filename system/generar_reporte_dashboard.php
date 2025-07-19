<?php
session_start();
date_default_timezone_set('America/Denver');
include "../conexion.php";

if(empty($_SESSION['active'])){
    header('location: ../');
    exit;
}

// Función para obtener datos del dashboard
function getDashboardData() {
    global $conn;
    
    $data = [];
    $mes_actual = date('Y-m');
    
    // Resumen general
    $sql_resumen = "SELECT 
                        COUNT(DISTINCT u.id) as total_usuarios,
                        COUNT(DISTINCT f.id) as total_familias,
                        SUM(CASE WHEN dg.pagado = 'No' THEN dg.monto ELSE 0 END) as total_deudas_pendientes
                    FROM usuarios u
                    LEFT JOIN familia f ON u.familia_id = f.id
                    LEFT JOIN detalle_gasto dg ON u.id = dg.deudor
                    LEFT JOIN Gastos g ON dg.idgasto = g.id
                    WHERE u.activo = 'Si'";
    
    $result_resumen = $conn->query($sql_resumen);
    $data['resumen'] = $result_resumen->fetch_assoc();
    
    // Deudas por familia
    $sql_familias = "SELECT 
                        f.nombre as familia,
                        COUNT(DISTINCT u.id) as usuarios_activos,
                        SUM(CASE WHEN dg.pagado = 'No' THEN dg.monto ELSE 0 END) as deudas_pendientes
                    FROM familia f
                    LEFT JOIN usuarios u ON f.id = u.familia_id AND u.activo = 'Si'
                    LEFT JOIN detalle_gasto dg ON u.id = dg.deudor
                    LEFT JOIN Gastos g ON dg.idgasto = g.id
                    GROUP BY f.id, f.nombre
                    ORDER BY deudas_pendientes DESC";
    
    $result_familias = $conn->query($sql_familias);
    $data['familias'] = [];
    while($row = $result_familias->fetch_assoc()) {
        $data['familias'][] = $row;
    }
    
    // Usuarios con más deudas
    $sql_usuarios = "SELECT 
                        u.nombre,
                        f.nombre as familia,
                        SUM(CASE WHEN dg.pagado = 'No' THEN dg.monto ELSE 0 END) as deuda_total,
                        MAX(CASE WHEN dg.pagado = 'No' THEN DATEDIFF(CURDATE(), g.fecha) ELSE 0 END) as dias_mora_max
                    FROM usuarios u
                    LEFT JOIN familia f ON u.familia_id = f.id
                    LEFT JOIN detalle_gasto dg ON u.id = dg.deudor
                    LEFT JOIN Gastos g ON dg.idgasto = g.id
                    WHERE u.activo = 'Si'
                    GROUP BY u.id, u.nombre, f.nombre
                    HAVING deuda_total > 0
                    ORDER BY deuda_total DESC
                    LIMIT 10";
    
    $result_usuarios = $conn->query($sql_usuarios);
    $data['usuarios'] = [];
    while($row = $result_usuarios->fetch_assoc()) {
        $data['usuarios'][] = $row;
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
    <title>Reporte Dashboard - <?php echo date('F Y'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-break {
                page-break-before: always;
            }
        }
        
        .report-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .metric-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .table-report {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .table-report thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
        }
        
        .badge-danger {
            background-color: #dc3545;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        
        .badge-success {
            background-color: #198754;
        }
        
        .badge-info {
            background-color: #0dcaf0;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <!-- Botones de acción -->
        <div class="row mb-4 no-print">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="dashboard_admin.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Volver al Dashboard
                    </a>
                    <button onclick="window.print()" class="btn btn-success">
                        <i class="bi bi-printer"></i> Imprimir Reporte
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Encabezado del reporte -->
        <div class="report-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0"><i class="bi bi-graph-up"></i> Reporte Dashboard Administrativo</h1>
                    <p class="mb-0">Período: <?php echo date('F Y'); ?></p>
                    <p class="mb-0">Generado: <?php echo date('d/m/Y H:i:s'); ?></p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="bi bi-file-earmark-text" style="font-size: 4rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
        
        <!-- Resumen General -->
        <div class="row mb-4">
            <div class="col-12">
                <h3><i class="bi bi-info-circle"></i> Resumen General</h3>
                <div class="row">
                  <div class="col-md-4">
                    <div class="metric-box text-start d-flex align-items-center" style="background: #e3eafe;">
                      <i class="bi bi-people-fill me-3" style="font-size:2.5rem;color:#667eea;"></i>
                      <div>
                        <h4 class="mb-0"><?php echo number_format($dashboard_data['resumen']['total_usuarios']); ?></h4>
                        <p class="mb-0">Usuarios Activos</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="metric-box text-start d-flex align-items-center" style="background: #ede3fa;">
                      <i class="bi bi-house-heart-fill me-3" style="font-size:2.5rem;color:#764ba2;"></i>
                      <div>
                        <h4 class="mb-0"><?php echo number_format($dashboard_data['resumen']['total_familias']); ?></h4>
                        <p class="mb-0">Familias</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="metric-box text-start d-flex align-items-center" style="background: #ffe3e3;">
                      <i class="bi bi-exclamation-triangle-fill me-3" style="font-size:2.5rem;color:#ff6b6b;"></i>
                      <div>
                        <h4 class="mb-0 text-danger">$<?php echo number_format($dashboard_data['resumen']['total_deudas_pendientes'], 2); ?></h4>
                        <p class="mb-0">Deudas Pendientes</p>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
        
        <!-- Resumen por Familia -->
        <div class="row mb-4 print-break">
            <div class="col-12">
                <h3><i class="bi bi-house-heart"></i> Resumen por Familia</h3>
                <div class="table-responsive">
                    <table class="table table-hover table-report">
                        <thead>
                            <tr>
                                <th>Familia</th>
                                <th>Usuarios Activos</th>
                                <th>Deudas Pendientes</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dashboard_data['familias'] as $familia): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($familia['familia']); ?></strong></td>
                                <td><?php echo $familia['usuarios_activos']; ?></td>
                                <td class="text-danger">$<?php echo number_format($familia['deudas_pendientes'], 2); ?></td>
                                <td>
                                    <?php if ($familia['deudas_pendientes'] == 0): ?>
                                        <span class="badge badge-success">Al día</span>
                                    <?php elseif ($familia['deudas_pendientes'] > 500): ?>
                                        <span class="badge badge-danger">Deuda Alta</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Deuda Moderada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Top 10 Usuarios con Más Deudas -->
        <div class="row mb-4 print-break">
            <div class="col-12">
                <h3><i class="bi bi-person-exclamation"></i> Top 10 Usuarios con Más Deudas</h3>
                <div class="table-responsive">
                    <table class="table table-hover table-report">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Familia</th>
                                <th>Deuda Total</th>
                                <th>Días de Mora</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dashboard_data['usuarios'] as $usuario): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($usuario['familia']); ?></td>
                                <td class="text-danger">$<?php echo number_format($usuario['deuda_total'], 2); ?></td>
                                <td>
                                    <?php if ($usuario['dias_mora_max'] > 30): ?>
                                        <span class="badge badge-danger"><?php echo $usuario['dias_mora_max']; ?> días</span>
                                    <?php elseif ($usuario['dias_mora_max'] > 15): ?>
                                        <span class="badge badge-warning"><?php echo $usuario['dias_mora_max']; ?> días</span>
                                    <?php else: ?>
                                        <span class="badge badge-info"><?php echo $usuario['dias_mora_max']; ?> días</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($usuario['deuda_total'] > 1000): ?>
                                        <span class="badge badge-danger">Crítico</span>
                                    <?php elseif ($usuario['dias_mora_max'] > 30): ?>
                                        <span class="badge badge-warning">Mora Alta</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">Normal</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Recomendaciones -->
        <div class="row mb-4 print-break">
            <div class="col-12">
                <h3><i class="bi bi-lightbulb"></i> Recomendaciones</h3>
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-exclamation-triangle text-warning"></i>
                                Revisar usuarios con deudas superiores a $1,000
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-clock text-danger"></i>
                                Contactar usuarios con más de 30 días de mora
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-bell text-info"></i>
                                Implementar recordatorios automáticos para deudas pendientes
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-graph-up text-success"></i>
                                Analizar patrones de pago por familia
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-calendar-check text-primary"></i>
                                Establecer fechas límite de pago por familia
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pie de página -->
        <div class="row mt-5 no-print">
            <div class="col-12 text-center">
                <hr>
                <p class="text-muted">
                    <small>
                        Reporte generado automáticamente por el Sistema de Gestión de Gastos Familiares<br>
                        © <?php echo date('Y'); ?> - Todos los derechos reservados
                    </small>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 