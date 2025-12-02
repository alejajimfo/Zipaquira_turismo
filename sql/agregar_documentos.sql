-- Agregar campos para documentos en la tabla usuarios
ALTER TABLE usuarios 
ADD COLUMN documento_identidad VARCHAR(500) AFTER pais,
ADD COLUMN rut VARCHAR(500) AFTER documento_identidad,
ADD COLUMN camara_comercio VARCHAR(500) AFTER rut,
ADD COLUMN certificado_rnt VARCHAR(500) AFTER camara_comercio,
ADD COLUMN documentos_verificados BOOLEAN DEFAULT FALSE AFTER certificado_rnt;

-- Agregar campos adicionales para perfil_turista
ALTER TABLE perfil_turista
ADD COLUMN documento_identidad VARCHAR(500) AFTER idiomas;
