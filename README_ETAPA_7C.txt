ETAPA 7C — PERFIL PERSONAL Y UBICACIÓN DEL COLABORADOR

Esta etapa implementa:
- Perfil funcional para Administrador, Técnico y Colaborador.
- Edición de nombre, apellido, identificación y correo.
- Datos laborales para colaboradores: teléfono, cargo y departamento.
- Cambio de ubicación con historial en ColaboradorUbicacion.
- Cambio seguro de contraseña verificando la contraseña actual.
- Enlace Mi perfil en el encabezado.

NO REQUIERE MIGRACIÓN SQL.
Utiliza las tablas existentes:
- Usuario
- Rol
- Colaborador
- Ubicacion
- ColaboradorUbicacion

Archivos nuevos:
- app/Controllers/PerfilController.php
- app/Interfaces/PerfilRepositoryInterface.php
- app/Repositories/PerfilRepository.php
- app/Services/PerfilService.php
- app/Views/perfil/show.php
- app/Views/perfil/edit.php
- app/Views/perfil/password.php

Cambios manuales:
1. Aplicar PATCHES/01_routes_web.txt
2. Aplicar PATCHES/02_header_perfil.txt
3. Copiar PATCHES/04_css.txt al final de public/assets/css/app.css
4. PATCHES/03_dashboard_tecnico.txt es opcional.
