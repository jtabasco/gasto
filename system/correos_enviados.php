<?php
session_start();
include "../conexion.php";
if (!isset($_SESSION['idUser'])) { header("Location: ../index.html"); exit; }
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) { echo "<script>alert('Acceso denegado. Solo administradores pueden ver los correos enviados.'); window.history.back();</script>"; exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correos Enviados - Sistema de Gastos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            margin: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-top: 5rem;
            margin-bottom: 5rem;
        }
        .section-title {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .section-title h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.4rem;
        }
        .stats-cards { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { 
            flex: 1; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border-radius: 15px; 
            padding: 20px; 
            text-align: center; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            position: relative;
        }
        .stat-card:nth-child(2) { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card:nth-child(3) { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .stat-card:nth-child(4) { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        .stat-number { font-size: 2.2rem; font-weight: bold; }
        .stat-label { font-size: 1rem; margin-top: 5px; }
        .table-container { 
            background: #fff; 
            border-radius: 20px; 
            padding: 25px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.1); 
            margin: 20px 0;
            overflow-x: auto;
        }
        .table-custom {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            border: none;
        }
        .table-custom th {
            background: #0d6efd;
            color: #fff;
            font-weight: 600;
            vertical-align: middle;
            border: none;
            padding: 8px 6px;
            font-size: 0.85rem;
            white-space: nowrap;
        }
        .table-custom td {
            vertical-align: middle;
            padding: 6px 8px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        .table-custom tbody tr:hover {
            background: linear-gradient(45deg, #f8f9ff, #f0f2ff);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        .icon-cell {
            font-size: 1.1rem;
            margin-right: 0.5rem;
            color: #764ba2;
            opacity: 0.8;
        }
        .email-status { 
            padding: 2px 8px; 
            border-radius: 8px; 
            font-size: 0.75rem; 
            font-weight: 500; 
        }
        .status-sent { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .btn-preview { 
            padding: 2px 6px; 
            font-size: 0.8rem; 
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        .modal-header {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px 20px 0 0;
            border: none;
        }
        .modal-title {
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                padding: 20px;
                margin-top: 2rem;
            }
            .section-title {
                flex-direction: column;
                gap: 15px;
                text-align: center;
                padding: 10px 15px;
            }
            .section-title h3 {
                font-size: 1.2rem;
            }
            .stats-cards {
                flex-direction: column;
                gap: 15px;
            }
            .stat-card {
                padding: 15px;
            }
            .stat-number {
                font-size: 1.8rem;
            }
            .table-container {
                padding: 15px;
                margin: 15px 0;
            }
            .table-custom th,
            .table-custom td {
                padding: 8px 4px;
                font-size: 0.8rem;
            }
            .table-custom th {
                font-size: 0.75rem;
                padding: 6px 2px;
            }
            .icon-cell {
                display: none;
            }
            .btn-sm {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
            }
            .email-status {
                font-size: 0.7rem;
                padding: 1px 6px;
            }
            .action-buttons {
                gap: 3px;
            }
            .action-buttons .btn {
                min-width: 30px;
            }
        }
        
        @media (max-width: 576px) {
            .main-container {
                margin: 5px;
                padding: 15px;
                margin-top: 1rem;
            }
            .section-title {
                padding: 8px 12px;
            }
            .section-title h3 {
                font-size: 1.1rem;
            }
            .stats-cards {
                gap: 10px;
            }
            .stat-card {
                padding: 12px;
            }
            .stat-number {
                font-size: 1.5rem;
            }
            .stat-label {
                font-size: 0.9rem;
            }
            .table-container {
                padding: 10px;
            }
            .table-custom th,
            .table-custom td {
                padding: 6px 2px;
                font-size: 0.75rem;
            }
            .table-custom th {
                font-size: 0.7rem;
                padding: 4px 1px;
            }
            .btn-sm {
                padding: 0.15rem 0.3rem;
                font-size: 0.65rem;
            }
            .email-status {
                font-size: 0.65rem;
                padding: 1px 4px;
            }
            .action-buttons {
                gap: 2px;
            }
            .action-buttons .btn {
                min-width: 25px;
                font-size: 0.6rem;
            }
            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .dataTables_wrapper .dataTables_scroll {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .dataTables_wrapper .dataTables_scrollBody {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
</head>
<body>
<?php include "../include/nav.php";?>

<div class="main-container">
    <div class="section-title">
        <h3><i class="bi bi-envelope-fill me-2"></i>Correos Enviados</h3>
    </div>
    
    <div class="stats-cards" id="statsCards">
        <div class="stat-card">
            <i class="bi bi-envelope-fill stat-icon"></i>
            <div class="stat-number" id="statTotal">0</div>
            <div class="stat-label">Total de Correos</div>
        </div>
        <div class="stat-card">
            <i class="bi bi-check-circle-fill stat-icon"></i>
            <div class="stat-number" id="statSent">0</div>
            <div class="stat-label">Enviados</div>
        </div>
        <div class="stat-card">
            <i class="bi bi-clock-fill stat-icon"></i>
            <div class="stat-number" id="statPending">0</div>
            <div class="stat-label">Pendientes</div>
        </div>
        <div class="stat-card">
            <i class="bi bi-calendar-day-fill stat-icon"></i>
            <div class="stat-number" id="statToday">0</div>
            <div class="stat-label">Hoy</div>
        </div>
    </div>
    
    <div class="table-container">
        <table id="emailsTable" class="table table-custom table-hover" style="width:100%">
            <thead>
                <tr>
                    <th class="text-center"><i class="bi bi-calendar-event icon-cell"></i>Fecha Creación</th>
                    <th class="text-center"><i class="bi bi-send-fill icon-cell"></i>Fecha Envío</th>
                    <th class="text-center"><i class="bi bi-person-fill icon-cell"></i>Destinatario</th>
                    <th class="text-center"><i class="bi bi-chat-text-fill icon-cell"></i>Asunto</th>
                    <th class="text-center"><i class="bi bi-info-circle-fill icon-cell"></i>Estado</th>
                    <th class="text-center"><i class="bi bi-gear-fill icon-cell"></i>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal para vista previa -->
<div class="modal fade" id="emailPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista Previa del Correo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalContenido"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
<script>
// Cargar estadísticas
function loadStats() {
    $.get('ajax/get_email_stats.php', function(data) {
        if (typeof data === 'string') data = JSON.parse(data);
        $('#statTotal').text(data.total || 0);
        $('#statSent').text(data.sent || 0);
        $('#statPending').text(data.pending || 0);
        $('#statToday').text(data.today || 0);
    });
}

// Inicializar DataTable
let emailsTable;
$(document).ready(function() {
    loadStats();
    emailsTable = $('#emailsTable').DataTable({
        order: [[0, 'desc']],
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        },
        searching: true,
        info: true,
        columnDefs: [
            {
                targets: [0,1,2,3,4,5],
                className: 'dt-body-center',
            },
            {
                targets: [0,1], // Fechas
                responsivePriority: 1
            },
            {
                targets: [2], // Destinatario
                responsivePriority: 2
            },
            {
                targets: [3], // Asunto
                responsivePriority: 3
            },
            {
                targets: [4], // Estado
                responsivePriority: 4
            },
            {
                targets: [5], // Acciones
                responsivePriority: 5
            }
        ],
        columns: [
            { 
                data: 0,
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data ? new Date(data).toLocaleDateString('es-ES', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '-';
                    }
                    if (type === 'filter') {
                        // Para búsqueda, incluir tanto formato original como mostrado
                        const displayDate = data ? new Date(data).toLocaleDateString('es-ES', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '-';
                        return data + ' ' + displayDate;
                    }
                    return data;
                }
            },
            { 
                data: 1,
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data ? new Date(data).toLocaleDateString('es-ES', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '-';
                    }
                    if (type === 'filter') {
                        // Para búsqueda, incluir tanto formato original como mostrado
                        const displayDate = data ? new Date(data).toLocaleDateString('es-ES', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '-';
                        return data + ' ' + displayDate;
                    }
                    return data;
                }
            },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5, orderable: false },
            { data: 6, visible: false }  // ID oculto pero disponible
        ],
        "paging": true,
        "language":{
            "url":"json/spanish.json"
        },
        stateSave: false,
        ajax: {
            "url":"ajax/get_emails.php",
            "type": 'GET',
            "dataSrc": 'data'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        scrollX: true,
        scrollCollapse: true,
        autoWidth: false
    });
});

// Vista previa de correo
function previewEmail(id) {
    $('#modalContenido').html('<div class="text-center">Cargando...</div>');
    var modal = new bootstrap.Modal(document.getElementById('emailPreviewModal'));
    modal.show();
    $.get('ajax/get_email_details.php', {id: id}, function(data) {
        if (typeof data === 'string') data = JSON.parse(data);
        if (data.success) {
            const email = data.email;
            $('#modalContenido').html(`
                <div><b>Destinatario:</b> ${email.destinatario}</div>
                <div><b>Fecha Creación:</b> ${email.fecha_creacion}</div>
                <div><b>Fecha Envío:</b> ${email.enviado == 1 ? email.fecha_envio : 'No enviado'}</div>
                <div><b>Asunto:</b> ${email.asunto}</div>
                <hr/>
                <div>${email.cuerpo_formateado || email.cuerpo}</div>
            `);
        } else {
            $('#modalContenido').html('<div class="text-danger">No se pudo cargar el correo.</div>');
        }
    });
}

// Eliminar correo con iziToast
function deleteEmail(id) {
    // Obtener datos de la fila usando el contexto del botón
    let button = event.target.closest('button');
    let row = $(button).closest('tr');
    let rowData = emailsTable.row(row).data();
    
    if (!rowData) { 
        iziToast.error({ title: 'Error', message: 'No se pudo obtener la información del correo.' }); 
        return; 
    }
    
    // Usar el ID del array de datos (posición 6)
    let emailId = rowData[6];
    
    // Formatear fecha para mostrar
    let fechaMostrar = rowData[0] ? new Date(rowData[0]).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }) : '-';
    
    iziToast.question({
        timeout: 20000,
        close: false,
        overlay: true,
        displayMode: 'once',
        id: 'question',
        zindex: 999,
        title: '',
        message: `<div><b>Fecha:</b> ${fechaMostrar}</div><div><b>Destinatario:</b> ${rowData[2]}</div><div><b>Asunto:</b> ${rowData[3]}</div>`,
        icon: 'bi bi-exclamation-triangle-fill',
        iconColor: '#dc3545',
        backgroundColor: '#fff3f3',
        titleColor: '#dc3545',
        messageColor: '#333',
        position: 'center',
        buttons: [
            [
                '<button style="color:white;background:#dc3545;border:none;padding:6px 18px;border-radius:4px;"><b>Eliminar</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    $.ajax({
                        url: 'ajax/delete_email.php',
                        type: 'POST',
                        data: {id: emailId},
                        success: function(data) {
                            if (typeof data === 'string') data = JSON.parse(data);
                            if (data.success) {
                                iziToast.success({ title: 'Eliminado', message: 'Correo eliminado correctamente', position: 'topRight' });
                                emailsTable.ajax.reload();
                                loadStats();
                            } else {
                                iziToast.error({ title: 'Error', message: 'Error al eliminar el correo: ' + (data.message || 'Error desconocido'), position: 'topRight' });
                            }
                        },
                        error: function() {
                            iziToast.error({ title: 'Error', message: 'Error de conexión al eliminar el correo', position: 'topRight' });
                        }
                    });
                }, true],
            [
                '<button style="color:#333;background:#eee;border:none;padding:6px 18px;border-radius:4px;">Cancelar</button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                }]
        ]
    });
}
</script>
</body>
</html> 