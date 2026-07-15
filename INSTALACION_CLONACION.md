# Instalación rápida después de clonar TrackiT

1. Coloca el proyecto dentro de `htdocs`.
2. Inicia Apache y MySQL desde XAMPP.
3. Importa `database/inventario.sql` en phpMyAdmin.
4. Ejecuta desde la raíz del proyecto:

```powershell
php scripts/setup_project.php
```

5. Abre la carpeta `public` desde el navegador.

En un XAMPP estándar, TrackiT crea automáticamente la configuración local
para MySQL (`root`, contraseña vacía) y genera sus llaves RSA. Los archivos
locales se mantienen fuera de Git mediante `.gitignore`.

Cuando MySQL use una contraseña personalizada, copia
`config/database.example.php` como `config/database.php` y ajusta únicamente
las credenciales locales.
