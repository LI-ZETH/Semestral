ETAPA 7G — BAJAS, DESCARTES Y DONACIONES
=========================================

INSTALACIÓN
-----------
1. Haz un respaldo/commit de tu proyecto actual.
2. Extrae el contenido de este ZIP directamente dentro de:
   C:\xampp\htdocs\Desarrollo_7\Semestral
3. Selecciona "Reemplazar los archivos en el destino".
4. No ejecutes migraciones. Esta fase utiliza las tablas existentes:
   - BajaActivo
   - TipoBaja
   - Activo
   - EstadoActivo
   - MovimientoActivo
5. Reinicia Apache y recarga con Ctrl + F5.

ACCESO
------
Administrador:
Panel -> Bajas de activos

También:
http://localhost/Desarrollo_7/Semestral/public/bajas

FLUJO
-----
1. El activo debe estar "En inventario" o "Revisión técnica".
2. No puede tener asignación activa.
3. No puede tener reparación o solicitud de reparación abierta.
4. Si es una licencia, no puede tener puestos activos.
5. El administrador registra Descarte o Donación.
6. El estado cambia automáticamente a DESCARTE o DONADO.
7. Se crea el movimiento correspondiente y se conserva todo el historial.

REGLAS IMPORTANTES
------------------
- Una baja es definitiva y no se elimina.
- Los estados DESCARTE y DONADO ya no se pueden elegir manualmente al editar una copia.
- Una copia con baja ya no se puede editar, activar ni desactivar.
- Para una donación son obligatorios la entidad beneficiaria y el responsable de recepción.
- Para un descarte es obligatoria la opinión técnica.

COMPROBACIÓN DE SINTAXIS
------------------------
php -l app\Controllers\BajaActivoController.php
php -l app\Repositories\BajaActivoRepository.php
php -l app\Services\BajaActivoService.php

Get-ChildItem -Recurse -Filter *.php |
ForEach-Object {
    php -l $_.FullName
}

PRUEBA RÁPIDA
-------------
1. Usa una copia en estado "En inventario".
2. En Administrar copias, pulsa "Registrar baja".
3. Prueba un descarte y completa la opinión técnica.
4. Confirma que el activo cambie a "Descarte".
5. Abre Reportes -> Movimientos y verifica el movimiento DESCARTE.
6. Repite con otra copia y el tipo Donación.
7. Comprueba que no puedas asignar ni editar una copia ya dada de baja.

BASE DE DATOS
-------------
Antes de entregar el proyecto, vuelve a exportar la base actualizada para
que la profesora reciba los registros y todas las tablas/migraciones ya
integradas en un solo archivo SQL.
