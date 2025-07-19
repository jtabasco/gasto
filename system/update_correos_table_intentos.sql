-- Agregar columna intentos a la tabla restablecer
ALTER TABLE `restablecer` 
ADD COLUMN `intentos` INT DEFAULT 0 AFTER `codigo`; 