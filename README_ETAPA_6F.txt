ETAPA 6F - COPIAS O ACTIVOS INDIVIDUALES

Incluye:
- CRUD lógico de copias por producto.
- Código único y número de serie único.
- Estado, ubicación, costo, fechas, vida útil y valor residual.
- Dirección IPv4 o IPv6 opcional.
- Entre 2 y 8 imágenes por activo.
- Selección de imagen principal.
- Reemplazo y eliminación controlada de imágenes.
- Activación y desactivación sin eliminación física.
- Registro automático en MovimientoActivo.
- Token QR criptográficamente aleatorio de 64 caracteres.
- Filtros por código, serie, IP, ubicación, estado y registro activo.

No requiere migración: utiliza las tablas Activo, ImagenActivo,
EstadoActivo, Ubicacion y MovimientoActivo que ya están en DB_CMDB.sql.

PASOS:
1. Copiar app/ dentro de la raíz del proyecto.
2. Aplicar los archivos de PATCHES.
3. Crear public/uploads/activos/.gitkeep.
4. Ejecutar php -l sobre todos los archivos.
5. Probar con un producto activo.
