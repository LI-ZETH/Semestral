ETAPA 7B - SOLICITUDES Y REPARACIONES

1. Ejecuta la migración:
   database/migrations/2026_07_15_solicitudes_reparacion.sql

2. Copia las carpetas app y database dentro del proyecto.

3. Aplica manualmente los cambios de PATCHES:
   01_routes_web.txt
   02_dashboard_administrador.txt
   03_boton_reparacion_mis_equipos.txt
   04_css.txt

4. Elimina las rutas provisionales de ModuloController para:
   /solicitudes
   /reparaciones

5. Prueba el flujo:
   Colaborador crea solicitud o reporte.
   Administrador revisa/asigna.
   Técnico gestiona reparación.
   Colaborador consulta el seguimiento.

6. Después de verificar la etapa, exporta nuevamente DB_CMDB.sql
   para que la tabla SolicitudReparacion quede incluida en la entrega.
