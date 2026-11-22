ADMIN METASOFT - FINAL CORREGIDO
================================

URL local usada por el usuario:
http://localhost/metasoaft_admin/

CAMBIOS PRINCIPALES
-------------------
1. Corregido el renderer de arrays:
   un objeto asociativo de textos ya NO se interpreta como lista.

2. Preview:
   - NO usa iframe.
   - Extrae únicamente la sección desde el sitio público.
   - usa el HTML/CSS real de METAROAD.
   - permanece sticky mientras se hace scroll.
   - muestra errores de carga en el propio panel.
   - intenta localhost y 127.0.0.1 en XAMPP.

3. Campos:
   textos, imágenes, iconos, estilos, colores, animaciones,
   items y configuración se editan visualmente.

4. Uploads:
   se envían al admin-upload.php del sitio.
   Si la carpeta no existe, el sitio la crea.

5. Base:
   importar METASOFT_CRM_METAROAD_INICIO_FINAL.sql

IMPORTANTE
----------
El preview lee:
http://localhost/metaroad-new/

Por eso METAROAD debe estar accesible antes de abrir una sección.
