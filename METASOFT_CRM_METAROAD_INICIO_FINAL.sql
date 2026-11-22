-- ============================================================
-- INSTALACIÓN / ACTUALIZACIÓN FINAL
-- Base: crm_metasoft
-- Sitio público: http://localhost/metaroad-new/
-- Admin local esperado: http://localhost/metasoaft_admin/
--
-- Puede ejecutarse sobre la base existente:
-- CREATE TABLE IF NOT EXISTS + INSERT ... ON DUPLICATE KEY UPDATE.
-- Los contenidos de las secciones de Inicio se actualizan a esta versión.
-- ============================================================

-- ============================================================
-- METASOFT CRM - BASE COMPLETA FINAL + METAROAD / INICIO
-- Basada en METAROAD_INICIO_ADMIN_READY_FINAL/index.html
-- URL por sitio; uploads por sección; contenido completo por sección.
-- ============================================================

CREATE DATABASE IF NOT EXISTS crm_metasoft
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE crm_metasoft;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS crm_sitios (
 cod_sitio INT NOT NULL AUTO_INCREMENT,
 nom_sitio VARCHAR(150) NOT NULL,
 slug_sitio VARCHAR(100) NOT NULL,
 url_sitio VARCHAR(500) NOT NULL,
 token_sitio VARCHAR(255) DEFAULT NULL,
 estado_sitio TINYINT(1) NOT NULL DEFAULT 1,
 fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(cod_sitio),
 UNIQUE KEY uk_crm_sitios_slug(slug_sitio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS crm_usuarios (
 cod_usuario INT NOT NULL AUTO_INCREMENT,
 nom_usuario VARCHAR(150) NOT NULL,
 ape_usuario VARCHAR(150) DEFAULT NULL,
 email_usuario VARCHAR(180) NOT NULL,
 user_usuario VARCHAR(100) NOT NULL,
 password_usuario CHAR(32) NOT NULL,
 foto_usuario VARCHAR(255) DEFAULT NULL,
 telefono_usuario VARCHAR(50) DEFAULT NULL,
 tipo_usuario VARCHAR(50) NOT NULL DEFAULT 'Administrador',
 estado_usuario TINYINT(1) NOT NULL DEFAULT 1,
 ultimo_acceso DATETIME DEFAULT NULL,
 fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(cod_usuario),
 UNIQUE KEY uk_crm_usuario_email(email_usuario),
 UNIQUE KEY uk_crm_usuario_user(user_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS web_paginas (
 cod_pagina INT NOT NULL AUTO_INCREMENT,
 cod_sitio INT NOT NULL,
 nom_pagina VARCHAR(150) NOT NULL,
 slug_pagina VARCHAR(150) NOT NULL,
 titulo_pagina VARCHAR(255) DEFAULT NULL,
 descripcion_pagina TEXT DEFAULT NULL,
 ruta_pagina VARCHAR(255) DEFAULT NULL,
 estado_pagina TINYINT(1) NOT NULL DEFAULT 1,
 eliminado_pagina TINYINT(1) NOT NULL DEFAULT 0,
 fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 fecha_actualizacion DATETIME DEFAULT NULL,
 PRIMARY KEY(cod_pagina),
 UNIQUE KEY uk_web_pagina_slug(cod_sitio,slug_pagina),
 KEY idx_web_paginas_sitio(cod_sitio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS web_secciones (
 cod_seccion INT NOT NULL AUTO_INCREMENT,
 cod_sitio INT NOT NULL,
 nom_seccion VARCHAR(180) NOT NULL,
 slug_seccion VARCHAR(180) NOT NULL,
 carpeta_uploads VARCHAR(255) NOT NULL,
 contenido_seccion LONGTEXT DEFAULT NULL,
 global_seccion TINYINT(1) NOT NULL DEFAULT 0,
 orden_global INT NOT NULL DEFAULT 0,
 estado_seccion TINYINT(1) NOT NULL DEFAULT 1,
 eliminado_seccion TINYINT(1) NOT NULL DEFAULT 0,
 fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 fecha_actualizacion DATETIME DEFAULT NULL,
 PRIMARY KEY(cod_seccion),
 UNIQUE KEY uk_web_seccion_slug(cod_sitio,slug_seccion),
 KEY idx_web_secciones_sitio(cod_sitio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS web_paginas_secciones (
 cod_pagina_seccion INT NOT NULL AUTO_INCREMENT,
 cod_pagina INT NOT NULL,
 cod_seccion INT NOT NULL,
 orden_seccion INT NOT NULL DEFAULT 0,
 estado_pagina_seccion TINYINT(1) NOT NULL DEFAULT 1,
 PRIMARY KEY(cod_pagina_seccion),
 UNIQUE KEY uk_pagina_seccion(cod_pagina,cod_seccion),
 KEY idx_paginas_secciones_pagina(cod_pagina,estado_pagina_seccion,orden_seccion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS web_formularios_mensajes (
 cod_mensaje INT NOT NULL AUTO_INCREMENT,
 cod_sitio INT NOT NULL,
 cod_pagina INT DEFAULT NULL,
 cod_seccion INT DEFAULT NULL,
 tipo_formulario VARCHAR(100) NOT NULL DEFAULT 'contacto',
 nombre VARCHAR(180) DEFAULT NULL,
 empresa VARCHAR(180) DEFAULT NULL,
 telefono VARCHAR(100) DEFAULT NULL,
 correo VARCHAR(180) DEFAULT NULL,
 mensaje LONGTEXT DEFAULT NULL,
 datos_adicionales LONGTEXT DEFAULT NULL,
 estado_mensaje VARCHAR(50) NOT NULL DEFAULT 'Nuevo',
 fecha_mensaje DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(cod_mensaje),
 KEY idx_mensajes_sitio(cod_sitio,fecha_mensaje),
 KEY idx_mensajes_estado(estado_mensaje)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin inicial: admin / admin123 (MD5 solicitado)
INSERT INTO crm_usuarios
(nom_usuario,ape_usuario,email_usuario,user_usuario,password_usuario,tipo_usuario,estado_usuario)
VALUES
('Administrador','','admin@metasoft.local','admin','0192023a7bbd73250516f069df18b500','Administrador',1)
ON DUPLICATE KEY UPDATE estado_usuario=1;

-- Sitio: solo URL, sin rutas físicas Windows/Mac.
INSERT INTO crm_sitios
(nom_sitio,slug_sitio,url_sitio,token_sitio,estado_sitio)
VALUES
('METAROAD','metaroad','http://localhost/metaroad-new/','METAROAD_LOCAL_2026',1)
ON DUPLICATE KEY UPDATE
 nom_sitio=VALUES(nom_sitio),
 url_sitio=VALUES(url_sitio),
 token_sitio=VALUES(token_sitio),
 estado_sitio=1;

SET @cod_sitio_metaroad=(SELECT cod_sitio FROM crm_sitios WHERE slug_sitio='metaroad' LIMIT 1);

INSERT INTO web_paginas
(cod_sitio,nom_pagina,slug_pagina,titulo_pagina,descripcion_pagina,ruta_pagina,estado_pagina,eliminado_pagina)
VALUES
(@cod_sitio_metaroad,'Inicio','inicio','METAROAD | Software para empresas de transporte','Venta de pasajes, encomiendas, caja, usuarios, unidades y reportes en un solo sistema.','/',1,0)
ON DUPLICATE KEY UPDATE
 nom_pagina=VALUES(nom_pagina),
 titulo_pagina=VALUES(titulo_pagina),
 descripcion_pagina=VALUES(descripcion_pagina),
 ruta_pagina=VALUES(ruta_pagina),
 estado_pagina=1,
 eliminado_pagina=0;

SET @cod_pagina_inicio=(SELECT cod_pagina FROM web_paginas WHERE cod_sitio=@cod_sitio_metaroad AND slug_pagina='inicio' LIMIT 1);

-- Header principal
INSERT INTO web_secciones
(cod_sitio,nom_seccion,slug_seccion,carpeta_uploads,contenido_seccion,global_seccion,orden_global,estado_seccion,eliminado_seccion)
VALUES
(@cod_sitio_metaroad,'Header principal','header-principal','uploads/secciones/header-principal/','{"textos":{"boton_demo":"Solicitar demo"},"imagenes":{"logo":"assets/hero/metaroad-logo.png"},"items":[{"slug":"inicio","titulo":"Inicio","enlace":"#inicio","desplegable":0,"orden":1,"estado":1},{"slug":"software","titulo":"Software","enlace":"#software","desplegable":1,"orden":2,"estado":1},{"slug":"modulos","titulo":"M&oacute;dulos","enlace":"#demo","desplegable":0,"orden":3,"estado":1},{"slug":"beneficios","titulo":"Beneficios","enlace":"#beneficios","desplegable":0,"orden":4,"estado":1},{"slug":"clientes","titulo":"Clientes","enlace":"#clientes","desplegable":0,"orden":5,"estado":1},{"slug":"contacto","titulo":"Contacto","enlace":"#contacto","desplegable":0,"orden":6,"estado":1}],"estilos":{"menu":{"color":"#ffffff","color_activo":"#111111","fondo_activo":"#ffd21f","font_size":"11px","font_weight":"700"},"boton_demo":{"color":"#ffd21f","background":"rgba(7,19,29,0.55)","border_color":"#ffd21f","border_radius":"999px","font_size":"10px","font_weight":"800"}},"animaciones":{"entrada":{"activo":1,"tipo":"fadeDown","duracion":0.55,"delay":0,"intensidad":18}},"configuracion":{"enlace_demo":"#contacto"}}',1,1,1,0)
ON DUPLICATE KEY UPDATE
 nom_seccion=VALUES(nom_seccion),
 carpeta_uploads=VALUES(carpeta_uploads),
 contenido_seccion=VALUES(contenido_seccion),
 global_seccion=VALUES(global_seccion),
 orden_global=VALUES(orden_global),
 estado_seccion=1,
 eliminado_seccion=0;
SET @sec_header_principal=(SELECT cod_seccion FROM web_secciones WHERE cod_sitio=@cod_sitio_metaroad AND slug_seccion='header-principal' LIMIT 1);

-- Hero Inicio
INSERT INTO web_secciones
(cod_sitio,nom_seccion,slug_seccion,carpeta_uploads,contenido_seccion,global_seccion,orden_global,estado_seccion,eliminado_seccion)
VALUES
(@cod_sitio_metaroad,'Hero Inicio','hero-inicio','uploads/secciones/hero-inicio/','{"textos":{"etiqueta":"SOFTWARE DE TRANSPORTE","titulo":"Tu empresa de<br>transporte.","titulo_destacado":"Una operaci&oacute;n<br>conectada.","descripcion":"Venta de pasajes, encomiendas, caja, usuarios, unidades y reportes en un solo sistema.","texto_scroll":"DESPL&Aacute;ZATE Y EXPLORA"},"botones":{"whatsapp":{"texto":"Solicitar demo por WhatsApp","enlace":"https://wa.me/59167550917"},"demo":{"texto":"Ver c&oacute;mo funciona","enlace":"#demo"}},"imagenes":{"fondo":"assets/hero/hero-background.webp","overlay":"assets/hero/hero-overlay.svg","road_glow":"assets/hero/hero-road-glow.svg","anillos":"assets/hero/hero-light-rings.svg","particulas":"assets/hero/hero-particles.svg","plataforma":"assets/hero/hero-platform.png","bus":"assets/hero/bus-hero.png"},"items":[{"slug":"pasajes","titulo":"Venta de pasajes","icono":"assets/icons/pasajes.svg","orden":1,"estado":1,"estilo":{"color":"#ffffff","background":"rgba(9,19,31,0.86)","border_color":"#84dfff","glow_color":"#59ccff"},"animacion":{"activo":1,"tipo":"scaleIn","duracion":0.42,"delay":0,"intensidad":18}},{"slug":"encomiendas","titulo":"Encomiendas","icono":"assets/icons/encomiendas.svg","orden":2,"estado":1,"estilo":{"color":"#ffffff","background":"rgba(9,19,31,0.86)","border_color":"#84dfff","glow_color":"#59ccff"},"animacion":{"activo":1,"tipo":"scaleIn","duracion":0.42,"delay":0.07,"intensidad":18}},{"slug":"caja","titulo":"Caja y egresos","icono":"assets/icons/caja.svg","orden":3,"estado":1,"estilo":{"color":"#ffffff","background":"rgba(9,19,31,0.86)","border_color":"#ffd21f","glow_color":"#ffd21f"},"animacion":{"activo":1,"tipo":"scaleIn","duracion":0.42,"delay":0.14,"intensidad":18}},{"slug":"usuarios","titulo":"Usuarios y permisos","icono":"assets/icons/usuarios.svg","orden":4,"estado":1,"estilo":{"color":"#ffffff","background":"rgba(9,19,31,0.86)","border_color":"#84dfff","glow_color":"#59ccff"},"animacion":{"activo":1,"tipo":"scaleIn","duracion":0.42,"delay":0.21,"intensidad":18}},{"slug":"unidades","titulo":"Unidades y flota","icono":"assets/icons/unidades.svg","orden":5,"estado":1,"estilo":{"color":"#ffffff","background":"rgba(9,19,31,0.86)","border_color":"#84dfff","glow_color":"#59ccff"},"animacion":{"activo":1,"tipo":"scaleIn","duracion":0.42,"delay":0.28,"intensidad":18}},{"slug":"reportes","titulo":"Reportes","icono":"assets/icons/reportes.svg","orden":6,"estado":1,"estilo":{"color":"#ffffff","background":"rgba(9,19,31,0.86)","border_color":"#84dfff","glow_color":"#59ccff"},"animacion":{"activo":1,"tipo":"scaleIn","duracion":0.42,"delay":0.35,"intensidad":18}}],"estilos":{"textos":{"etiqueta":{"color":"#ffffff","font_size":"10px","font_weight":"900","text_align":"left","line_height":"1.2","letter_spacing":"0.6px","text_transform":"uppercase"},"titulo":{"color":"#ffffff","font_size":"46px","font_weight":"900","text_align":"left","line_height":"0.98","letter_spacing":"-1.7px","text_transform":"none"},"titulo_destacado":{"color":"#ffd21f","font_size":"46px","font_weight":"900","text_align":"left","line_height":"0.98","letter_spacing":"-1.7px","text_transform":"none"},"descripcion":{"color":"#f1f5f8","font_size":"11px","font_weight":"400","text_align":"left","line_height":"1.5","letter_spacing":"0px","text_transform":"none"},"texto_scroll":{"color":"#ffffff","font_size":"8px","font_weight":"500","text_align":"center","line_height":"1.2","letter_spacing":"1.1px","text_transform":"uppercase"}},"botones":{"whatsapp":{"color":"#111111","background":"#ffd21f","border_color":"#ffd21f","border_radius":"999px","font_size":"10px","font_weight":"800"},"demo":{"color":"#ffffff","background":"rgba(7,19,29,0.73)","border_color":"#dce8ef","border_radius":"999px","font_size":"10px","font_weight":"800"}},"seccion":{"background_color":"#071019"}},"animaciones":{"seccion":{"activo":1,"tipo":"fadeIn","duracion":0.7,"delay":0,"intensidad":20},"titulo":{"activo":1,"tipo":"fadeUp","duracion":0.65,"delay":0.12,"intensidad":28},"bus":{"activo":1,"tipo":"scaleIn","duracion":0.8,"delay":0.35,"intensidad":20},"cards":{"activo":1,"tipo":"staggerUp","duracion":0.42,"delay":0.45,"intensidad":20},"particulas":{"activo":1,"tipo":"float","duracion":6,"delay":0,"intensidad":10}},"configuracion":{"distribucion":"texto_izquierda_imagen_derecha"}}',0,0,1,0)
ON DUPLICATE KEY UPDATE
 nom_seccion=VALUES(nom_seccion),
 carpeta_uploads=VALUES(carpeta_uploads),
 contenido_seccion=VALUES(contenido_seccion),
 global_seccion=VALUES(global_seccion),
 orden_global=VALUES(orden_global),
 estado_seccion=1,
 eliminado_seccion=0;
SET @sec_hero_inicio=(SELECT cod_seccion FROM web_secciones WHERE cod_sitio=@cod_sitio_metaroad AND slug_seccion='hero-inicio' LIMIT 1);
INSERT INTO web_paginas_secciones(cod_pagina,cod_seccion,orden_seccion,estado_pagina_seccion) VALUES(@cod_pagina_inicio,@sec_hero_inicio,1,1) ON DUPLICATE KEY UPDATE orden_seccion=VALUES(orden_seccion),estado_pagina_seccion=1;

-- Demo interactiva
INSERT INTO web_secciones
(cod_sitio,nom_seccion,slug_seccion,carpeta_uploads,contenido_seccion,global_seccion,orden_global,estado_seccion,eliminado_seccion)
VALUES
(@cod_sitio_metaroad,'Demo interactiva','demo-interactiva','uploads/secciones/demo-interactiva/','{"textos":{"etiqueta":"DEMO INTERACTIVA","titulo":"Prueba c&oacute;mo<br>funciona METAROAD","descripcion":"Interact&uacute;a con los m&oacute;dulos y descubre el flujo completo de tu operaci&oacute;n."},"pasos":[{"numero":1,"texto":"Selecciona un m&oacute;dulo"},{"numero":2,"texto":"Interact&uacute;a"},{"numero":3,"texto":"Conoce el resultado"}],"imagenes":{"bus_principal":"assets/hero/bus-hero.png"},"items":[{"slug":"pasajes","titulo":"Venta de pasajes","icono":"assets/icons/pasajes.svg","orden":1,"estado":1,"descripcion":"Registro de ventas, selecci&oacute;n de asientos y emisi&oacute;n de boletos.","informacion":["Selecci&oacute;n de asientos","Registro de ventas","Emisi&oacute;n de boletos","Control de rutas y horarios"],"escena":{"asiento":"14","ruta":"La Paz &rarr; Cochabamba","fecha_hora":"20 Nov 2026 &middot; 19:30","boton":"Emitir boleto","boleto_titulo":"METAROAD","boleto_subtitulo":"Boleto de Viaje","boleto_ruta":"La Paz &rarr; Cochabamba","boleto_asiento":"Asiento 14","codigo":"MR-PAS-014"},"estilo":{"accent":"#ffd21f","panel_background":"rgba(13,69,100,0.95)","panel_border":"#4bd9ff"},"animacion":{"activo":1,"tipo":"fadeIn","duracion":0.45,"delay":0,"intensidad":18}},{"slug":"encomiendas","titulo":"Encomiendas","icono":"assets/icons/encomiendas.svg","orden":2,"estado":1,"descripcion":"Registro, control y seguimiento de env&iacute;os.","informacion":["Registro del env&iacute;o","Seguimiento del estado","Control del recorrido"],"escena":{"titulo_panel":"Gesti&oacute;n de encomiendas","codigo":"MR-458932","ruta":"La Paz &rarr; Cochabamba","estado":"En tr&aacute;nsito","estados":["Registro del env&iacute;o","En tr&aacute;nsito","En ruta","En entrega","Entregado"]},"estilo":{"accent":"#ffd21f","panel_background":"rgba(13,69,100,0.95)","panel_border":"#4bd9ff"},"animacion":{"activo":1,"tipo":"slideRight","duracion":0.45,"delay":0,"intensidad":24}},{"slug":"caja","titulo":"Caja y egresos","icono":"assets/icons/caja.svg","orden":3,"estado":1,"descripcion":"Control de ingresos, egresos y cierre de caja.","informacion":["Ingresos","Egresos","Cierre de caja"],"escena":{"titulo_panel":"Caja y egresos","ingresos":"Bs 42,850.00","egresos":"Bs 12,430.00","resultado":"Bs 30,420.00","movimientos":[{"concepto":"Venta de pasajes","monto":"Bs 1,250.00","tipo":"ingreso"},{"concepto":"Encomienda","monto":"Bs 320.00","tipo":"ingreso"},{"concepto":"Combustible","monto":"-Bs 2,000.00","tipo":"egreso"},{"concepto":"Mantenimiento","monto":"-Bs 600.00","tipo":"egreso"}]},"estilo":{"accent":"#ffd21f","panel_background":"rgba(13,69,100,0.95)","panel_border":"#4bd9ff"},"animacion":{"activo":1,"tipo":"fadeUp","duracion":0.45,"delay":0,"intensidad":20}},{"slug":"usuarios","titulo":"Usuarios y permisos","icono":"assets/icons/usuarios.svg","orden":4,"estado":1,"descripcion":"Gesti&oacute;n de usuarios, roles y niveles de acceso.","informacion":["Usuarios","Roles","Niveles de acceso"],"escena":{"titulo_panel":"Usuarios y permisos","usuarios":[{"nombre":"Carlos Mendoza","rol":"Administrador"},{"nombre":"Ana Torres","rol":"Ventas"},{"nombre":"Luis Paredes","rol":"Operaciones"},{"nombre":"Mar&iacute;a Rojas","rol":"Caja"}],"permisos":["Gesti&oacute;n de ventas","Encomiendas","Caja y egresos","Reportes","Gesti&oacute;n de flota"]},"estilo":{"accent":"#ffd21f","panel_background":"rgba(13,69,100,0.95)","panel_border":"#4bd9ff"},"animacion":{"activo":1,"tipo":"scaleIn","duracion":0.45,"delay":0,"intensidad":16}},{"slug":"unidades","titulo":"Unidades y flota","icono":"assets/icons/unidades.svg","orden":5,"estado":1,"descripcion":"Administraci&oacute;n de unidades, disponibilidad y mantenimiento.","informacion":["Unidades","Disponibilidad","Mantenimiento"],"escena":{"titulo_panel":"Unidades y flota","unidad_principal":"Unidad MR-101","estado_principal":"Operativa","flota":[{"unidad":"MR-101","estado":"Operativa"},{"unidad":"MR-102","estado":"En ruta"},{"unidad":"MR-103","estado":"Mantenimiento"}],"resumen":{"unidades":"3","operativas":"2","en_ruta":"1"}},"estilo":{"accent":"#ffd21f","panel_background":"rgba(13,69,100,0.95)","panel_border":"#4bd9ff"},"animacion":{"activo":1,"tipo":"slideLeft","duracion":0.45,"delay":0,"intensidad":24}},{"slug":"reportes","titulo":"Reportes","icono":"assets/icons/reportes.svg","orden":6,"estado":1,"descripcion":"Informes y estad&iacute;sticas de ventas, encomiendas y operaci&oacute;n.","informacion":["Ventas","Encomiendas","Ingresos y egresos","Operaci&oacute;n"],"escena":{"titulo_panel":"Reportes y an&aacute;lisis","total_ventas":"Bs 128,750.00","variacion_ventas":"&#9650; 12.4%","pasajes_vendidos":"3,842","variacion_pasajes":"&#9650; 9.8%","rutas":["La Paz &rarr; Cochabamba","Cochabamba &rarr; La Paz","La Paz &rarr; Santa Cruz"],"acciones":["Exportar reporte","PDF","Excel"]},"estilo":{"accent":"#ffd21f","panel_background":"rgba(13,69,100,0.95)","panel_border":"#4bd9ff"},"animacion":{"activo":1,"tipo":"fadeIn","duracion":0.45,"delay":0,"intensidad":20}}],"estilos":{"textos":{"etiqueta":{"color":"#ffffff","font_size":"11px","font_weight":"900","text_align":"left","line_height":"1.2","letter_spacing":"0.04em","text_transform":"uppercase"},"titulo":{"color":"#ffffff","font_size":"36px","font_weight":"900","text_align":"left","line_height":"1.0","letter_spacing":"-1px","text_transform":"none"},"descripcion":{"color":"#ffffff","font_size":"11px","font_weight":"400","text_align":"left","line_height":"1.45","letter_spacing":"0px","text_transform":"none"}},"tabs":{"color":"#ffffff","background":"rgba(255,255,255,0.025)","border_color":"rgba(255,255,255,0.34)","active_color":"#111111","active_background":"#ffd21f","active_border":"#ffd21f"},"pasos":{"color":"#ffffff","active_color":"#111111","active_background":"#ffd21f"},"seccion":{"background_color":"#071019"}},"animaciones":{"seccion":{"activo":1,"tipo":"fadeIn","duracion":0.7,"delay":0,"intensidad":18},"cambio_modulo":{"activo":1,"tipo":"fadeIn","duracion":0.35,"delay":0,"intensidad":16},"pasos":{"activo":1,"tipo":"fadeDown","duracion":0.45,"delay":0.1,"intensidad":14}},"configuracion":{"modulo_inicial":"pasajes"}}',0,0,1,0)
ON DUPLICATE KEY UPDATE
 nom_seccion=VALUES(nom_seccion),
 carpeta_uploads=VALUES(carpeta_uploads),
 contenido_seccion=VALUES(contenido_seccion),
 global_seccion=VALUES(global_seccion),
 orden_global=VALUES(orden_global),
 estado_seccion=1,
 eliminado_seccion=0;
SET @sec_demo_interactiva=(SELECT cod_seccion FROM web_secciones WHERE cod_sitio=@cod_sitio_metaroad AND slug_seccion='demo-interactiva' LIMIT 1);
INSERT INTO web_paginas_secciones(cod_pagina,cod_seccion,orden_seccion,estado_pagina_seccion) VALUES(@cod_pagina_inicio,@sec_demo_interactiva,2,1) ON DUPLICATE KEY UPDATE orden_seccion=VALUES(orden_seccion),estado_pagina_seccion=1;

-- Recorrido de encomienda
INSERT INTO web_secciones
(cod_sitio,nom_seccion,slug_seccion,carpeta_uploads,contenido_seccion,global_seccion,orden_global,estado_seccion,eliminado_seccion)
VALUES
(@cod_sitio_metaroad,'Recorrido de encomienda','recorrido-encomienda','uploads/secciones/recorrido-encomienda/','{"textos":{"titulo":"Recorrido de<br>una encomienda","descripcion":"Registra, sigue y entrega tus env&iacute;os con trazabilidad en tiempo real.","titulo_buscador":"Seguimiento de encomienda","placeholder_buscador":"MR-458932"},"imagenes":{"paquete":"assets/sections/package-standalone.svg"},"items":[{"slug":"registrada","titulo":"Registrada","icono":"","orden":1,"estado":1},{"slug":"en-transito","titulo":"En tr&aacute;nsito","icono":"assets/icons/unidades.svg","orden":2,"estado":1},{"slug":"en-ruta","titulo":"En ruta","icono":"assets/icons/ubicacion.svg","orden":3,"estado":1},{"slug":"entregada","titulo":"Entregada","icono":"","orden":4,"estado":1}],"estilos":{"textos":{"titulo":{"color":"#111111","font_size":"25px","font_weight":"900","text_align":"left","line_height":"0.95","letter_spacing":"0px","text_transform":"none"},"descripcion":{"color":"#343a40","font_size":"10px","font_weight":"400","text_align":"left","line_height":"1.4","letter_spacing":"0px","text_transform":"none"},"titulo_buscador":{"color":"#ffffff","font_size":"10px","font_weight":"800","text_align":"left","line_height":"1.2","letter_spacing":"0px","text_transform":"none"}},"linea":{"color":"#ffd21f","completado":"#28c65b"},"seccion":{"background_color":"#f4f6f8"}},"animaciones":{"seccion":{"activo":1,"tipo":"fadeUp","duracion":0.6,"delay":0,"intensidad":20},"estados":{"activo":1,"tipo":"staggerUp","duracion":0.42,"delay":0.12,"intensidad":18}},"configuracion":{}}',0,0,1,0)
ON DUPLICATE KEY UPDATE
 nom_seccion=VALUES(nom_seccion),
 carpeta_uploads=VALUES(carpeta_uploads),
 contenido_seccion=VALUES(contenido_seccion),
 global_seccion=VALUES(global_seccion),
 orden_global=VALUES(orden_global),
 estado_seccion=1,
 eliminado_seccion=0;
SET @sec_recorrido_encomienda=(SELECT cod_seccion FROM web_secciones WHERE cod_sitio=@cod_sitio_metaroad AND slug_seccion='recorrido-encomienda' LIMIT 1);
INSERT INTO web_paginas_secciones(cod_pagina,cod_seccion,orden_seccion,estado_pagina_seccion) VALUES(@cod_pagina_inicio,@sec_recorrido_encomienda,3,1) ON DUPLICATE KEY UPDATE orden_seccion=VALUES(orden_seccion),estado_pagina_seccion=1;

-- Beneficios Inicio
INSERT INTO web_secciones
(cod_sitio,nom_seccion,slug_seccion,carpeta_uploads,contenido_seccion,global_seccion,orden_global,estado_seccion,eliminado_seccion)
VALUES
(@cod_sitio_metaroad,'Beneficios Inicio','beneficios-inicio','uploads/secciones/beneficios-inicio/','{"textos":{"titulo":"&iquest;Por qu&eacute; elegir<br><em>METAROAD?</em>","descripcion":"Potencia tu empresa con una soluci&oacute;n moderna, segura y escalable."},"imagenes":{"dashboard":"assets/sections/dashboard-laptop-standalone.svg"},"items":[{"slug":"mas-ventas","titulo":"M&aacute;s ventas","icono":"assets/icons/licencias.svg","orden":1,"estado":1,"estilo":{"color":"#111111","background":"#ffffff","icon_color":"#f8bd00"},"animacion":{"activo":1,"tipo":"fadeUp","duracion":0.42,"delay":0,"intensidad":18}},{"slug":"seguridad","titulo":"Seguridad","icono":"assets/icons/escudo.svg","orden":2,"estado":1,"estilo":{"color":"#111111","background":"#ffffff","icon_color":"#f8bd00"},"animacion":{"activo":1,"tipo":"fadeUp","duracion":0.42,"delay":0.06,"intensidad":18}},{"slug":"puntualidad","titulo":"Puntualidad","icono":"assets/icons/reloj.svg","orden":3,"estado":1,"estilo":{"color":"#111111","background":"#ffffff","icon_color":"#f8bd00"},"animacion":{"activo":1,"tipo":"fadeUp","duracion":0.42,"delay":0.12,"intensidad":18}},{"slug":"operacion-integral","titulo":"Operaci&oacute;n integral","icono":"assets/icons/caja.svg","orden":4,"estado":1,"estilo":{"color":"#111111","background":"#ffffff","icon_color":"#f8bd00"},"animacion":{"activo":1,"tipo":"fadeUp","duracion":0.42,"delay":0.18,"intensidad":18}},{"slug":"reportes-inteligentes","titulo":"Reportes inteligentes","icono":"assets/icons/reportes.svg","orden":5,"estado":1,"estilo":{"color":"#111111","background":"#ffffff","icon_color":"#f8bd00"},"animacion":{"activo":1,"tipo":"fadeUp","duracion":0.42,"delay":0.24,"intensidad":18}},{"slug":"soporte","titulo":"Soporte","icono":"assets/icons/soporte.svg","orden":6,"estado":1,"estilo":{"color":"#111111","background":"#ffffff","icon_color":"#f8bd00"},"animacion":{"activo":1,"tipo":"fadeUp","duracion":0.42,"delay":0.3,"intensidad":18}}],"estilos":{"textos":{"titulo":{"color":"#111111","font_size":"25px","font_weight":"900","text_align":"left","line_height":"0.95","letter_spacing":"0px","text_transform":"none"},"descripcion":{"color":"#444b52","font_size":"10px","font_weight":"400","text_align":"left","line_height":"1.4","letter_spacing":"0px","text_transform":"none"}},"titulo_destacado":{"color":"#f8bd00"},"seccion":{"background_color":"#f4f6f8"}},"animaciones":{"seccion":{"activo":1,"tipo":"fadeUp","duracion":0.6,"delay":0,"intensidad":18},"cards":{"activo":1,"tipo":"staggerUp","duracion":0.42,"delay":0.1,"intensidad":16},"dashboard":{"activo":1,"tipo":"slideLeft","duracion":0.6,"delay":0.18,"intensidad":22}},"configuracion":{}}',0,0,1,0)
ON DUPLICATE KEY UPDATE
 nom_seccion=VALUES(nom_seccion),
 carpeta_uploads=VALUES(carpeta_uploads),
 contenido_seccion=VALUES(contenido_seccion),
 global_seccion=VALUES(global_seccion),
 orden_global=VALUES(orden_global),
 estado_seccion=1,
 eliminado_seccion=0;
SET @sec_beneficios_inicio=(SELECT cod_seccion FROM web_secciones WHERE cod_sitio=@cod_sitio_metaroad AND slug_seccion='beneficios-inicio' LIMIT 1);
INSERT INTO web_paginas_secciones(cod_pagina,cod_seccion,orden_seccion,estado_pagina_seccion) VALUES(@cod_pagina_inicio,@sec_beneficios_inicio,4,1) ON DUPLICATE KEY UPDATE orden_seccion=VALUES(orden_seccion),estado_pagina_seccion=1;

-- Contacto Inicio
INSERT INTO web_secciones
(cod_sitio,nom_seccion,slug_seccion,carpeta_uploads,contenido_seccion,global_seccion,orden_global,estado_seccion,eliminado_seccion)
VALUES
(@cod_sitio_metaroad,'Contacto Inicio','contacto-inicio','uploads/secciones/contacto-inicio/','{"textos":{"titulo":"Conversemos sobre<br>lo que necesitas.","descripcion":"D&eacute;janos tus datos y cu&eacute;ntanos c&oacute;mo trabaja tu empresa. Te ayudaremos a conocer METAROAD.","texto_boton":"Enviar solicitud","texto_whatsapp":"Cont&aacute;ctanos<br>por WhatsApp"},"formulario":{"nombre":{"label":"Nombre completo","placeholder":"Nombre completo","requerido":1},"empresa":{"label":"Empresa","placeholder":"Empresa","requerido":0},"telefono":{"label":"Tel&eacute;fono o WhatsApp","placeholder":"Tel&eacute;fono o WhatsApp","requerido":1},"correo":{"label":"Correo electr&oacute;nico","placeholder":"Correo electr&oacute;nico","requerido":1},"mensaje":{"label":"Mensaje","placeholder":"Cu&eacute;ntanos sobre tu empresa o lo que necesitas.","requerido":1}},"datos":{"direccion":"Pacieri 717 casi Antezana Cochabamba","telefono_1":"74276774","whatsapp":"67550917","correo":""},"imagenes":{"ondas":"assets/decor/contact-waves.svg"},"estilos":{"textos":{"titulo":{"color":"#ffffff","font_size":"27px","font_weight":"900","text_align":"left","line_height":"0.98","letter_spacing":"0px","text_transform":"none"},"descripcion":{"color":"#d7dfe5","font_size":"10px","font_weight":"400","text_align":"left","line_height":"1.45","letter_spacing":"0px","text_transform":"none"},"texto_boton":{"color":"#111111","font_size":"10px","font_weight":"800","text_align":"center","line_height":"1.2","letter_spacing":"0px","text_transform":"none"},"texto_whatsapp":{"color":"#ffffff","font_size":"18px","font_weight":"800","text_align":"center","line_height":"1.05","letter_spacing":"0px","text_transform":"none"}},"formulario":{"label_color":"#ffffff","input_background":"#ffffff","input_color":"#111111","border_color":"#d9dde2","required_color":"#ff5454","button_background":"#ffd21f","button_color":"#111111"},"whatsapp":{"background":"#45c82b","color":"#ffffff"},"seccion":{"background_color":"#061009"}},"animaciones":{"seccion":{"activo":1,"tipo":"fadeUp","duracion":0.6,"delay":0,"intensidad":18},"formulario":{"activo":1,"tipo":"slideRight","duracion":0.55,"delay":0.12,"intensidad":22},"mapa":{"activo":1,"tipo":"fadeIn","duracion":0.55,"delay":0.18,"intensidad":18},"whatsapp":{"activo":1,"tipo":"scaleIn","duracion":0.55,"delay":0.22,"intensidad":16}},"configuracion":{"mostrar_mapa":1,"mostrar_whatsapp":1,"mapa_query":"Pacieri 717 casi Antezana Cochabamba Bolivia"}}',0,0,1,0)
ON DUPLICATE KEY UPDATE
 nom_seccion=VALUES(nom_seccion),
 carpeta_uploads=VALUES(carpeta_uploads),
 contenido_seccion=VALUES(contenido_seccion),
 global_seccion=VALUES(global_seccion),
 orden_global=VALUES(orden_global),
 estado_seccion=1,
 eliminado_seccion=0;
SET @sec_contacto_inicio=(SELECT cod_seccion FROM web_secciones WHERE cod_sitio=@cod_sitio_metaroad AND slug_seccion='contacto-inicio' LIMIT 1);
INSERT INTO web_paginas_secciones(cod_pagina,cod_seccion,orden_seccion,estado_pagina_seccion) VALUES(@cod_pagina_inicio,@sec_contacto_inicio,5,1) ON DUPLICATE KEY UPDATE orden_seccion=VALUES(orden_seccion),estado_pagina_seccion=1;

-- Limpia asignaciones inactivas de Inicio sin tocar secciones reutilizables.
UPDATE web_paginas_secciones SET estado_pagina_seccion=1 WHERE cod_pagina=@cod_pagina_inicio;

-- Verificación rápida.
SELECT cod_sitio,nom_sitio,url_sitio FROM crm_sitios WHERE cod_sitio=@cod_sitio_metaroad;
SELECT cod_pagina,nom_pagina,ruta_pagina FROM web_paginas WHERE cod_pagina=@cod_pagina_inicio;
SELECT cod_seccion,nom_seccion,slug_seccion,carpeta_uploads,global_seccion FROM web_secciones WHERE cod_sitio=@cod_sitio_metaroad ORDER BY global_seccion DESC,cod_seccion ASC;

-- FIN
