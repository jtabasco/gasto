-- Script para actualizar la tabla correos_pendientes

-- Verificar si la columna fecha_creacion ya existe
SELECT COUNT(*) INTO @col_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'correos_pendientes'
AND COLUMN_NAME = 'fecha_creacion';

-- Agregar columna fecha_creacion si no existe
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE correos_pendientes ADD COLUMN fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'SELECT "Columna fecha_creacion ya existe" as mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Actualizar registros existentes que no tengan fecha_creacion
UPDATE correos_pendientes
SET fecha_creacion = NOW()
WHERE fecha_creacion IS NULL;

-- Mostrar estructura actual de la tabla
DESCRIBE correos_pendientes;

-- Verificar si la columna fecha_envio ya existe
SELECT COUNT(*) INTO @col_envio_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'correos_pendientes'
AND COLUMN_NAME = 'fecha_envio';

-- Agregar columna fecha_envio si no existe
SET @sql_envio = IF(@col_envio_exists = 0, 
    'ALTER TABLE correos_pendientes ADD COLUMN fecha_envio TIMESTAMP NULL',
    'SELECT "Columna fecha_envio ya existe" as mensaje'
);
PREPARE stmt_envio FROM @sql_envio;
EXECUTE stmt_envio;
DEALLOCATE PREPARE stmt_envio;

-- Mostrar estructura final de la tabla
DESCRIBE correos_pendientes; 