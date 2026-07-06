<?php

session_start();

// Elimina todas las variables de sesión
$_SESSION = [];

// Destruye la sesión
session_destroy();

// Evita que el navegador guarde la página en caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirige al login
header("Location: Login.php");
exit();