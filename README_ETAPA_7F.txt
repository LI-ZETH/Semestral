ETAPA 7F — LICENCIAS DE SOFTWARE

Incluye:
- CRUD de los datos específicos de LicenciaSoftware.
- Proveedor, tipo, URL, puestos, fechas y renovación automática.
- Clave de licencia cifrada con RSA-OAEP.
- Confirmación de contraseña antes de descifrar la clave.
- Asignación y revocación de puestos a colaboradores.
- Validación de puestos disponibles.
- Alertas visuales de expiración.
- Página Mis licencias para colaboradores.

INSTALACIÓN
1. Copiar las carpetas app, database y PATCHES a la raíz del proyecto.
2. Ejecutar en phpMyAdmin:
   database/migrations/2026_07_15_asignaciones_licencias.sql
3. Aplicar los archivos de PATCHES en orden.
4. Copiar PATCHES/05_css.txt al final de public/assets/css/app.css.
5. Ejecutar php -l sobre los archivos nuevos.

FLUJO PARA REGISTRAR UNA LICENCIA
1. Crear una categoría/subcategoría de Software si no existe.
2. Crear un Producto con tipo LICENCIA.
3. Crear una copia individual para ese producto.
4. Abrir Licencias de software.
5. Registrar los datos de la licencia seleccionando esa copia.
6. Asignar puestos a colaboradores.

IMPORTANTE
- La migración se ejecuta una sola vez.
- Después de terminar el proyecto, exportar nuevamente la base completa.
- Nunca subir config/crypto.php ni storage/keys/private.pem.
