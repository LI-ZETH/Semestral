-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-07-2026 a las 08:05:55
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inventario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activo`
--

CREATE TABLE `activo` (
  `idActivo` bigint(20) UNSIGNED NOT NULL,
  `idProducto` int(10) UNSIGNED NOT NULL,
  `codigoActivo` varchar(40) NOT NULL,
  `numeroSerie` varchar(120) DEFAULT NULL,
  `direccionIP` varchar(45) DEFAULT NULL,
  `costo` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fechaAdquisicion` date NOT NULL,
  `fechaIngreso` date NOT NULL,
  `vidaUtilMeses` smallint(5) UNSIGNED DEFAULT NULL,
  `valorResidual` decimal(12,2) NOT NULL DEFAULT 0.00,
  `idEstadoActivo` int(10) UNSIGNED NOT NULL,
  `idUbicacion` int(10) UNSIGNED DEFAULT NULL,
  `qrToken` char(64) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaActualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `activo`
--

INSERT INTO `activo` (`idActivo`, `idProducto`, `codigoActivo`, `numeroSerie`, `direccionIP`, `costo`, `fechaAdquisicion`, `fechaIngreso`, `vidaUtilMeses`, `valorResidual`, `idEstadoActivo`, `idUbicacion`, `qrToken`, `observaciones`, `activo`, `fechaRegistro`, `fechaActualizacion`) VALUES
(1, 2, 'TRK-LAP-000001', 'HP-450-G10-0001', '192.168.1.25', 850.00, '2026-07-14', '2026-07-14', 60, 85.00, 2, 1, 'd0eae5497418e2708cebf4d9ce45f01065cc331a579adfdfd0162e647428ce70', 'Se incluyó lápiz óptico, cargador y estuche.', 1, '2026-07-14 20:37:22', '2026-07-14 21:40:56'),
(2, 3, 'TRK-LAP-000002', 'DL-G33-590-0001', '198.149.1.89', 800.00, '2026-07-14', '2026-07-14', 90, 75.00, 6, 1, '07fc3a87424c46afb1fd3db3cf06629ddc02c6691ffa8d5fd84cea63770c4d12', NULL, 1, '2026-07-14 21:31:21', '2026-07-15 00:34:52'),
(3, 5, 'LIC-M365-001', NULL, NULL, 40.00, '2026-07-14', '2026-07-14', 12, 20.00, 1, NULL, '0823f44d7b5b9a8adb9a60656f369b555968a8d14edbe163c861ebfa4ea8be66', NULL, 1, '2026-07-14 23:59:47', '2026-07-14 23:59:47'),
(23, 35, 'TRK-LAP-001', 'HP450G10-0001', NULL, 999.00, '2025-01-15', '2025-01-16', NULL, 100.00, 2, 9, '87b976ec229da756b55e5cc5a31ed9cc1ec31d299b7aae56f2b82a6aaa4275e6', 'Laptop asignada', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(24, 35, 'TRK-LAP-002', 'HP450G10-0002', NULL, 999.00, '2025-01-15', '2025-01-16', NULL, 100.00, 1, 8, '47f1542c8f67047d5fdc85b1168890f97d1345a3a4f47731804116b75ee00e44', 'Laptop disponible', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(25, 35, 'TRK-LAP-003', 'HP450G10-0003', NULL, 999.00, '2025-01-15', '2025-01-16', NULL, 100.00, 3, 11, '7e1199aabd5013f2554607ba9a30e5f81aada359db32f7fc3668e6bb21ce1e19', 'Laptop en revisión', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(26, 36, 'TRK-LAP-004', 'DL5440-0001', NULL, 1150.00, '2025-03-10', '2025-03-11', NULL, 115.00, 1, 8, '9fff5d3c34a9b67c744a1006c9b0289f4b487bdf91a504ff67baea546cdd4f29', 'Laptop disponible', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(27, 36, 'TRK-LAP-005', 'DL5440-0002', NULL, 1150.00, '2025-03-10', '2025-03-11', NULL, 115.00, 1, 8, '176b1a656a944ffd78c1a7742f9aba9444497eda4f364166c1de0c58b4bbd2b1', 'Laptop disponible', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(28, 37, 'TRK-DESK-001', 'LNV-M70S-0001', NULL, 875.00, '2024-08-20', '2024-08-21', NULL, 87.50, 1, 10, '4991dbe7c349ff32b52058bb24feb96db69d3190bb47805a6f89e8b0d6b62baa', 'Desktop RRHH', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(29, 37, 'TRK-DESK-002', 'LNV-M70S-0002', NULL, 875.00, '2024-08-20', '2024-08-21', NULL, 87.50, 1, 9, '8c1df6ad9b40469d47571b5bb6bd6cffaadc30eb53d38e79c077cd21c4b8eef8', 'Desktop administración', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(30, 24, 'TRK-MON-001', 'DELL-P2422H-001', NULL, 245.00, '2024-08-20', '2024-08-21', NULL, 20.00, 1, 10, '9e48c0f4f9f5df61a02eee71d2113613d912497c665faac12b70741a8a71043e', 'Monitor RRHH', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(31, 24, 'TRK-MON-002', 'DELL-P2422H-002', NULL, 245.00, '2024-08-20', '2024-08-21', NULL, 20.00, 1, 9, '549b7a6076cf107c2a6b0cfd92ba8d213e2786e8a37ecb4d8159424ff8429572', 'Monitor administración', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(32, 25, 'TRK-PRN-001', 'HPM404-001', '192.168.10.45', 410.00, '2023-06-12', '2023-06-13', NULL, 40.00, 4, 11, '2f62fa11e5d28e8690ef66382d53f8ef2265995dc324544a19e16a2e88369ee7', 'Impresora en reparación', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(33, 31, 'TRK-RTR-001', 'C1111-8P-001', '192.168.1.1', 1350.00, '2023-02-10', '2023-02-11', NULL, 135.00, 1, 12, '2336e5259c0066cc7c561000e43c0eac202a86f6d11c79a047855d152230fdce', 'Router principal', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(34, 32, 'TRK-SW-001', 'CBS350-001', '192.168.1.2', 620.00, '2023-02-10', '2023-02-11', NULL, 62.00, 1, 12, 'c6654f74f4c2d2e7cf19b79badc2f26466c560b862170685b42159d3bedeca90', 'Switch principal', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(35, 33, 'TRK-AP-001', 'U6LITE-001', '192.168.1.20', 145.00, '2024-01-18', '2024-01-19', NULL, 15.00, 1, 9, 'b4bcc77236d1af046171ce5dc453b3a22c18aff397d458e0f57d4f7a0243e27f', 'Punto de acceso', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(36, 34, 'TRK-FW-001', 'FG40F-001', '192.168.1.254', 785.00, '2023-02-10', '2023-02-11', NULL, 78.50, 1, 12, 'b81624b4dea70c95a20c80771fc0e05012e9d16858b405592aec496ac0b06de5', 'Firewall principal', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(37, 38, 'TRK-SRV-001', 'PET350-001', '192.168.1.10', 3650.00, '2023-04-05', '2023-04-06', NULL, 365.00, 1, 12, '07fcb943613191b54e06cd6d6380f4c0338f7e714784256ad3d32c1d1cff75a2', 'Servidor institucional', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(38, 39, 'TRK-WS-001', 'HPZ2G9-001', NULL, 2150.00, '2024-05-20', '2024-05-21', NULL, 215.00, 1, 9, 'da4c69cf70c7cfbc219fb19d84f113a5d1fc2bbfdfac87b05670ed6d072f3854', 'Workstation de diseño', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(39, 40, 'TRK-IP-001', 'YT54W-001', '192.168.20.31', 185.00, '2024-02-14', '2024-02-15', NULL, 18.50, 1, 9, '59263961eeaed985cfd12e414b46e1d43503c5f8ef776dbf05c0739715714f0e', 'Teléfono IP', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(40, 40, 'TRK-IP-002', 'YT54W-002', '192.168.20.32', 185.00, '2024-02-14', '2024-02-15', NULL, 18.50, 1, 10, '13bd25a6b56baaa00ebe70c1ce901fe2e6d937559f5a033f6acdecf3240095f8', 'Teléfono IP', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(41, 41, 'TRK-MOB-001', 'SMA55-001', NULL, 395.00, '2024-03-01', '2024-03-02', NULL, 39.50, 6, 8, 'cba8524b3e8d85e3a5113fd6f13584c8484563e507c4d365355f71bd70d31632', 'Equipo donado', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(42, 28, 'LIC-ADOBE-001', 'ADOBE-CC-001', NULL, 2100.00, '2026-02-01', '2026-02-01', NULL, 0.00, 1, 8, '674c5d22a1820443772058536e3cb1d78f9ac5896a9cfc7439c399e8afcd488c', 'Licencia de 5 puestos', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(43, 30, 'LIC-WIN11-001', 'WIN11-PRO-001', NULL, 799.00, '2025-09-01', '2025-09-01', NULL, 0.00, 1, 8, '88cbca0aa1c1a6284e7a115119826c5db0e525c41d26f094b2fed64864c22399', 'Paquete de licencias', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(44, 24, 'TRK-MON-OLD-001', 'DELL-OLD-001', NULL, 190.00, '2019-01-10', '2019-01-11', NULL, 10.00, 5, 8, '8715946c86070ab3072862405b66ca311e6fd75ea7089b9989842848a064e725', 'Monitor dado de baja', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacionactivo`
--

CREATE TABLE `asignacionactivo` (
  `idAsignacion` bigint(20) UNSIGNED NOT NULL,
  `idActivo` bigint(20) UNSIGNED NOT NULL,
  `idColaborador` int(10) UNSIGNED NOT NULL,
  `usuarioEntrega` int(10) UNSIGNED NOT NULL,
  `fechaEntrega` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaDevolucion` datetime DEFAULT NULL,
  `estadoAsignacion` enum('ACTIVA','DEVUELTA','CANCELADA') NOT NULL DEFAULT 'ACTIVA',
  `observacionesEntrega` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asignacionactivo`
--

INSERT INTO `asignacionactivo` (`idAsignacion`, `idActivo`, `idColaborador`, `usuarioEntrega`, `fechaEntrega`, `fechaDevolucion`, `estadoAsignacion`, `observacionesEntrega`) VALUES
(1, 1, 1, 1, '2026-07-14 20:59:16', '2026-07-14 21:01:45', 'DEVUELTA', 'Se le entrega con Lápiz Óptico, Cargador y Estuche.'),
(2, 1, 1, 1, '2026-07-14 21:02:19', NULL, 'ACTIVA', 'Se entrega con Cargador y Lápiz óptico'),
(3, 2, 1, 1, '2026-07-14 21:35:46', '2026-07-15 00:33:17', 'DEVUELTA', 'Se entrega con Cargador'),
(4, 23, 3, 7, '2026-07-01 09:00:00', NULL, 'ACTIVA', 'Laptop entregada para labores administrativas.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacionlicencia`
--

CREATE TABLE `asignacionlicencia` (
  `idAsignacionLicencia` bigint(20) UNSIGNED NOT NULL,
  `idLicencia` bigint(20) UNSIGNED NOT NULL,
  `idColaborador` int(10) UNSIGNED NOT NULL,
  `idUsuarioAsigna` int(10) UNSIGNED NOT NULL,
  `correoAsignado` varchar(120) DEFAULT NULL,
  `fechaAsignacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaRevocacion` datetime DEFAULT NULL,
  `estadoAsignacion` enum('ACTIVA','REVOCADA') NOT NULL DEFAULT 'ACTIVA',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asignacionlicencia`
--

INSERT INTO `asignacionlicencia` (`idAsignacionLicencia`, `idLicencia`, `idColaborador`, `idUsuarioAsigna`, `correoAsignado`, `fechaAsignacion`, `fechaRevocacion`, `estadoAsignacion`, `observaciones`) VALUES
(1, 1, 1, 1, 'winston@gmail.com', '2026-07-15 00:01:46', '2026-07-15 00:03:48', 'REVOCADA', NULL),
(2, 1, 1, 1, 'winston@gmail.com', '2026-07-15 00:03:56', NULL, 'ACTIVA', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `idAuditoria` bigint(20) UNSIGNED NOT NULL,
  `idUsuario` int(10) UNSIGNED DEFAULT NULL,
  `idLlavePublica` int(10) UNSIGNED DEFAULT NULL,
  `modulo` varchar(80) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `tablaAfectada` varchar(80) DEFAULT NULL,
  `idRegistro` varchar(80) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `datosAnteriores` longtext DEFAULT NULL,
  `datosNuevos` longtext DEFAULT NULL,
  `direccionIP` varchar(45) DEFAULT NULL,
  `userAgent` varchar(500) DEFAULT NULL,
  `hashAnterior` char(64) DEFAULT NULL,
  `hashRegistro` char(64) DEFAULT NULL,
  `firmaDigital` longtext DEFAULT NULL,
  `algoritmoFirma` varchar(30) DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`idAuditoria`, `idUsuario`, `idLlavePublica`, `modulo`, `accion`, `tablaAfectada`, `idRegistro`, `descripcion`, `datosAnteriores`, `datosNuevos`, `direccionIP`, `userAgent`, `hashAnterior`, `hashRegistro`, `firmaDigital`, `algoritmoFirma`, `fecha`) VALUES
(1, 1, 1, 'REPORTES', 'EXPORTAR_INVENTARIO', NULL, NULL, 'Se exportó un reporte en formato compatible con Excel.', NULL, '{\"filtros\":{\"buscar\":\"\",\"categoria\":\"0\",\"estado\":\"0\",\"tipo\":\"inventario\"},\"tipo\":\"inventario\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, '18c99f9b193ce4a755598b39dc1e4f0af34eb9eb4ef1e9b72a9c454027723dcf', 'frhIpchCpDwsBNc/TIfvll0J96+KGM2DZQu8U7FGpFCVctGiYhTxDmWz4WzB93sq3afYjojr4kI/iEBK/irr1WedWr2CDF71uAa/UvzvR8H0+oaISfFORb+muR3i3IRBDdPTrYd3TRZ3j7MCpT2v/DKhwDaeaFAQm1+811IgtfrvrkDs9iABm+1LNb6P6lX/q0TzqrgG3MwlvL30OgjHb+wPrEThBwrqxSX4YoIkQFggxB5kB7UD73NLPLLMP79WRZwn0+n4I3CdVZ/lN8Fs7iaQF8zu6UdRysUvWTnlso4ihnDFQBK6u6myCzCwdOtxgjqZp/B1OCh58iU8PmbKHA==', 'RSA-2048-SHA256', '2026-07-14 22:33:54'),
(2, 1, 1, 'REPORTES', 'EXPORTAR_ACCESOS', NULL, NULL, 'Se exportó un reporte en formato compatible con Excel.', NULL, '{\"filtros\":{\"buscar\":\"\",\"resultado\":\"\",\"tipo\":\"accesos\"},\"tipo\":\"accesos\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '18c99f9b193ce4a755598b39dc1e4f0af34eb9eb4ef1e9b72a9c454027723dcf', '1b34f37aeb551614111249bb8b461d668dcf11ad076b2256b9ba16212d542f2b', 'ZBubpLESHmGXTPmRFbx61pjgTE75z6o9/7yXe27a2SUl1sZvcX66dQBrNs24yCElQbZavb7T5WdKlV6aV/PWePOw39zniKAY4VQZuFgLvOrcVtI4zk6YJhnyMMBHr9XBnpeC6xNqMh96TFZEAiBWZhQFBWQ+sgF2W23WEdNfdWinl5PiSlT0o3JTNBwqA+G7jj5G03ksOV40+AYHeaGhwx49B3fV9r4XJ+0s+PVU1YWj15032dPfc90cpglbDa2ySe9TqQnt/ZXb81OkbuhO+sbdp7+W9x8trqrHGnadFlKf0bgSviKG8BGsPx0/zCiQzhphqAPq0wDLdjTHmC6UKw==', 'RSA-2048-SHA256', '2026-07-14 22:34:57'),
(3, 1, 1, 'INVENTARIO', 'ACTUALIZAR', 'Subcategoria', '4', 'Solicitud confirmada mediante la ruta /inventario/subcategorias/actualizar.', NULL, '{\"descripcion\":\"Computadoras de escritorio.\",\"idCategoria\":\"4\",\"idSubcategoria\":\"12\",\"nombreSubcategoria\":\"Desktop\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '1b34f37aeb551614111249bb8b461d668dcf11ad076b2256b9ba16212d542f2b', '73efe5e55f26940e2d8baaf8c94506e526aba62cae7e2d168309c4bac010e135', 'QT7R7f07yeMYBm4aMqF3HIylnx2PMAFmku3ZSKdP7EUWB4PXdvVEMpFGdZP7ibCWinJo4p4v/nOPOG4PFNb8A122agu7bh/1eoC6Ir00c31xzQcSpHzy1daq4fqwXSjsMdMl+jmcD0P9ikHOFSa569362iKt0vERUIeWrsIg4jxdNERDdIkNP6GU6/qj7gNMhNXisso0/C1chG38EAD4+2oK1arQlR4yiPjrUnK9esu1KRqvKDXExhzjPJFeifLj61kVrUwvGUS2r10siED9j4qpyRtHdM41KrDOv5prRVd9AEeTexp45H6dmL/QIaKOLXCPFZoK+t1Sy2SzBXnmIw==', 'RSA-2048-SHA256', '2026-07-14 22:39:08'),
(4, 1, 1, 'REPORTES', 'EXPORTAR_INVENTARIO', NULL, NULL, 'Se exportó un reporte en formato compatible con Excel.', NULL, '{\"filtros\":{\"buscar\":\"\",\"categoria\":\"0\",\"estado\":\"0\",\"tipo\":\"inventario\"},\"tipo\":\"inventario\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '73efe5e55f26940e2d8baaf8c94506e526aba62cae7e2d168309c4bac010e135', 'a960ce6ec2f1c79a12b5a63cbd5d3c6c495716b505dec88f6770ad6797c7d6d3', 'gnMvFkM9+qmx0ly/RCRm1WAV1sGBf+6LozqXAlV+Lgajj2W7Q7RdrggXE1vVkp2qpmrHgEDpBmhP4L6rwGKVz92sw0cWJjDouF9FDPlf4L3NJ8IVsLCGV1QXnv0hYx1SmGzMf0zaWYACh6013wcljHpEF5S7Zg+4xjM3y6Z7o8ZEwJ9Yrb1t3mg8PJN7KbZqZ9bZ+iHK7Y7BY5rU+U96S+ksDlmMOrib0JVFnHHosZ0yBkfPlHVcD2moSq5ifnwHo+yuh8qQNHSaQTjTgBMItDsF+Fax15D5O8AqQ0eSAeLvqYfmRIojXNgecCZuZTQIUWpB4NTh5KYTefpaOPStuQ==', 'RSA-2048-SHA256', '2026-07-14 23:00:54'),
(5, 1, 1, 'INVENTARIO', 'CREAR', 'Subcategoria', '2', 'Solicitud confirmada mediante la ruta /inventario/subcategorias/guardar.', NULL, '{\"descripcion\":\"Conjunto de programas informáticos integrados diseñados para facilitar, organizar y automatizar las tareas habituales de una oficina, estudio o negocio\",\"idCategoria\":\"2\",\"nombreSubcategoria\":\"Suite de oficina\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'a960ce6ec2f1c79a12b5a63cbd5d3c6c495716b505dec88f6770ad6797c7d6d3', 'ea467fc81704fff0d0a7a84669cdf68cf988e6e861dfc59cdc85a70e78f645ef', 'fFq3GYpuK6j41L/hgy8xM1LLKuX8nNH+K+5HcGujev8PZYPYHPlHE9yby0mKcDZf8w0CHbKzLENRZtw6yzYCV79u06K3hxWA1WwD3jKQavFY8Ll4TxhNiivetR7pbLgFFBTz7AcFCRy0A0pD8oJCATEz0T/oSLCZn9wNXgwEmvI4FQoX8tzGxxMlkHRDL7d/ItRRt7s6WKgOL8H//eufyH2ZB2mDTk01OK2fKppX9P300ggG2X+bHIBk070fFuKm6Bh0pVvRkDQx7Fs5o1sBtpk75JT2Mi1quNR/mnODICLFnkgZ4zF6GdHa9Yn3Jsz4XwwTC3YYLOSyWMm1xVvx+g==', 'RSA-2048-SHA256', '2026-07-14 23:51:37'),
(6, 1, 1, 'INVENTARIO', 'CREAR', 'Producto', '17', 'Solicitud confirmada mediante la ruta /inventario/productos/guardar.', NULL, '{\"descripcion\":\"Suite de productividad empresarial con aplicaciones\\r\\nde oficina y servicios en la nube.\",\"idSubcategoria\":\"17\",\"marca\":\"Microsoft\",\"modelo\":\"Microsoft 365 Business Standard\",\"nombreProducto\":\"Microsoft 365\",\"tipoProducto\":\"LICENCIA\",\"vidaUtilMeses\":\"12\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'ea467fc81704fff0d0a7a84669cdf68cf988e6e861dfc59cdc85a70e78f645ef', '25f2dd418e13831483193164a48d07e446638d2b09f683a32830f3d4fc144542', 'Zc72K1qljnQSa9puqSKmE7OX0qamFXQf3fOOZPY4KWQClRnsl0RztI4kXNwEUkMygdrLU0NWVMinN23oMYeEBPO9BXQpbBsJPno05PvmQYv2XAt18kVrpFlhPgioCVzkLAteOIve/KyKcuNwM8BDYG6FSFrGgriXl4Gp19+IqO5QalN3yQXgUYbtvIbrcf6hWvwrD5dG6Y03J4pytTi1xRvXP+eXMGVfSL+cPHEG7hHH9bAWr8F22rugxDDfnRTDkrI01CtoguPVVOATI4zQs5+GGHrRCiIBCzyenpjf1IFpzJgnjhiqmjoiPcy4Jf8PdX2IlaJq2d2NrZPIhZkKig==', 'RSA-2048-SHA256', '2026-07-14 23:56:55'),
(7, 1, 1, 'INVENTARIO', 'CREAR', 'Activo', '5', 'Solicitud confirmada mediante la ruta /inventario/activos/guardar.', NULL, '{\"codigoActivo\":\"LIC-M365-001\",\"costo\":\"40\",\"direccionIP\":\"\",\"fechaAdquisicion\":\"2026-07-14\",\"fechaIngreso\":\"2026-07-14\",\"idEstadoActivo\":\"1\",\"idProducto\":\"5\",\"idUbicacion\":\"\",\"numeroSerie\":\"\",\"observaciones\":\"\",\"valorResidual\":\"20\",\"vidaUtilMeses\":\"12\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '25f2dd418e13831483193164a48d07e446638d2b09f683a32830f3d4fc144542', '08be588eb7d4a70126cdbf7fb2607518c1966f945d66ee00045d1953e8264327', 'S6z6b5COq/UcMLe+ekLYl7GOxZVNlCvO+V0+YkGICh/e9MC2oWLIugeHJ8qqHCrKAUizueekjQWbI7lAY+wnzc+btoWG0AnAxeUd73wodgN3kwuXzTWqtKrweDW6ZVrjxDcXYnqxbdJxDV48AP4hp2En045K6jLESta1ODk/WU262kRDRcz9hYw9YXhQkJiK2KG/5feY9u2lk9bMEybsy0YRGSPdYU9sx/qi+juncWMkGpGsmG771Invxv+3oWThlBwhfxsSIpTlaabKHZOybIlDcU9wblpf9RMNU05n7rSkO3432/t3iW/GYHYsBBjxxbvOdWLW+2xPbsLEKdfLUw==', 'RSA-2048-SHA256', '2026-07-14 23:59:47'),
(8, 1, 1, 'LICENCIAS', 'CREAR', NULL, '3', 'Solicitud confirmada mediante la ruta /licencias/guardar.', NULL, '{\"cantidadPuestos\":\"1\",\"claveLicencia\":\"[PROTEGIDO]\",\"fechaExpiracion\":\"2027-07-15\",\"fechaInicio\":\"2026-07-15\",\"idActivo\":\"3\",\"observaciones\":\"\",\"proveedor\":\"Microsoft\",\"renovacionAutomatica\":\"1\",\"tipoLicencia\":\"Suscripción empresarial\",\"urlAcceso\":\"https://www.office.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '08be588eb7d4a70126cdbf7fb2607518c1966f945d66ee00045d1953e8264327', '558a3b67708f7f4f4ed87547cc6dd2ac430ef73c8fe5f8899865eff60e46512e', 'GPc7lHmdAth3LBMt9/GISpnfP1kuSu6Z27Dk0V9xOFhpJe+muswF5DIRHogt6IDyNRyCsRiXnjkWWvswmTGOayyrSfKxYJtbjdonQeCKo6JKEDdPQy3lNbcJgwhUJGFWHieAUVttUjcrLl61VyO7GkuIXzW3/Xb+UyPXNN1gZ01nb96MhbjClDpX3TbOuggr8uSWlR/UI6IcVGgWrQAoSfXmAwhM8PxLeJ5GWdyK/ofrcxLrJTWnddJWBDAfFEJsoeTXIgn64kSa4twFJvtSVa2TSqLTMJVSglHS+rpu+3FDA8NZwD00bGPPIzxsYXBXIGaTv23ua93WtTQ7M383Dg==', 'RSA-2048-SHA256', '2026-07-15 00:01:11'),
(9, 1, 1, 'LICENCIAS', 'CLAVE', NULL, NULL, 'Solicitud confirmada mediante la ruta /licencias/clave.', NULL, '{\"contrasenaActual\":\"[PROTEGIDO]\",\"idLicencia\":\"1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '558a3b67708f7f4f4ed87547cc6dd2ac430ef73c8fe5f8899865eff60e46512e', '8e34c78bacbc80c69070f2408f1332cf9454f89edc2b6fa140d5d20401947a9f', 'b0pvWNVK1LO88EV19sJdD215cPad0bVVThfWlFA/yIRV1moanuwv7qTuVg5Ju+HzI38qg3/oIqlQ0nprAUlq//KtsVt3paOuLejSXAjPfyFOlEeLfoh6hfwWudUDyunb7++9XmOdJEECxm5BxHNr4VvU2/eXRNSyu1QZhHjlRADoRmTwDFGXNqx4jF3Aex1VAG4baa6jf7iCujvoC9KnRmTqEGHWemKElHpokjofeo+gScZsipQ+mJ3gyEAFBGraywIu5o8/GGUkajhOB7SB+GdILFj+GPpQS3XtLyt9CPbgmb4oH0uDgwWsVRNy0/LaVMs09Y+PdAUixUoAwjDojg==', 'RSA-2048-SHA256', '2026-07-15 00:01:21'),
(10, 1, 1, 'LICENCIAS', 'ASIGNAR', NULL, NULL, 'Solicitud confirmada mediante la ruta /licencias/asignar.', NULL, '{\"correoAsignado\":\"winston@gmail.com\",\"idColaborador\":\"1\",\"idLicencia\":\"1\",\"observaciones\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '8e34c78bacbc80c69070f2408f1332cf9454f89edc2b6fa140d5d20401947a9f', '6a91450369a0ab5aeb4bfa36ef05f5f145a2f7e1048cbbff831b6cf7ba309b94', 'XBIxuDUgP9cOfo0dRgxqACBg05CKfaHf91bCtBhNyh/ZHloxohHHKZxqXYr0mpkSIcutzN3RUWpWEmKgDh5VMbR0oC2ZzkLtKIjDoQLcFpzKKE8OpjpmSBoFJ0XNgrhpuwfeQuZ2L/QSeeQhjvEK4OyXBPQ7yr+v/Je83lhJQ4s6eXIU6jPhZacNo89arAfyaKUjuxPHQNlFE0zTx2dRi58+85ytWmkOcZbZhY77KiLLYhKzwZqowAlNQoy0UhZ27KNQ+3+F02qCJ49vb19nYZNNgsSaAfr1xrpsymvGo5w8xXwPRD86GYNvF9io/Z9C+a6TF6ZSeX5Nd37vSDbXTQ==', 'RSA-2048-SHA256', '2026-07-15 00:01:46'),
(11, 1, 1, 'LICENCIAS', 'ACTUALIZAR', NULL, NULL, 'Solicitud confirmada mediante la ruta /licencias/actualizar.', NULL, '{\"cantidadPuestos\":\"10\",\"claveLicencia\":\"[PROTEGIDO]\",\"fechaExpiracion\":\"2027-07-15\",\"fechaInicio\":\"2026-07-15\",\"idLicencia\":\"1\",\"observaciones\":\"\",\"proveedor\":\"Microsoft\",\"renovacionAutomatica\":\"1\",\"tipoLicencia\":\"Suscripción empresarial\",\"urlAcceso\":\"https://www.office.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '6a91450369a0ab5aeb4bfa36ef05f5f145a2f7e1048cbbff831b6cf7ba309b94', '1bf34468a181505a50bf47dcae38472ecb0d3926d68089f1c7d6161bf8c990b1', 'hQmWWMqoZ9GgeQi0zdtf/QmykmdvGE05f5xNhAP7DRtXu8x3FN3rD+/EnyGivudRy8BlcQz1ZXiCKEmSc2/h2mxbBi8dysMXMiwVisrU1IOLJbAH3gb98HItjZcsxxCR1WHR9OYA8LhffZ61vXToRY0UFXOGcLhCDtmBn9mT2NQHEsQRj0Rzr4HAQX85rQASztMunlAWV+4tVGrsgFCrDdON/UxAeMopi45M5x7qul35uBL5/t6Qk6gMNGpi1x3BKy/kLKMxWDC/Cn1VF+KH+xn7OveuhYlVh1Z1iQQIeEiL5hvIMHjksjLz7yLpTB8tduVM00NIlo2mDYtJJ6CeqA==', 'RSA-2048-SHA256', '2026-07-15 00:03:18'),
(12, 1, 1, 'LICENCIAS', 'REVOCAR', NULL, NULL, 'Solicitud confirmada mediante la ruta /licencias/asignacion/revocar.', NULL, '{\"idAsignacionLicencia\":\"1\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '1bf34468a181505a50bf47dcae38472ecb0d3926d68089f1c7d6161bf8c990b1', '6bc1e6ab8ce8b2e41f21cada96ae2f8b14017567827cd1bb633f466ed3f4de87', 'eURHPHB/16l5JrEbn3KCsCoCgRuPqoJlWR5WUm9t0nYCbHaPD1lNLF9P/ch7SSs078YiUztLLlsI2k0xf5BTezrZeMwputZSj1TRC4/FPSlaTipsh1XPa7DtzVuvi9dvWoa33/G7FaT5tCLlMluDk70xSOsqC1RJ9b9GqLRZ8fXrzqwelHs68KSq+qTuhpizl+BBFLN9icGZUZKPH0LD3bl8nTTI/3/EkPtoEH1NnEhLRmsrZaMAkxAmqcD7zVnw4enQvP7pDdgJRiETbX4ugNDyXvlGwQ03IVtSLXQlZqsLPFRxIOUC53ri6OSI5b8F9eNbieCgN0MDpsqhBucrXQ==', 'RSA-2048-SHA256', '2026-07-15 00:03:48'),
(13, 1, 1, 'LICENCIAS', 'ASIGNAR', NULL, NULL, 'Solicitud confirmada mediante la ruta /licencias/asignar.', NULL, '{\"correoAsignado\":\"winston@gmail.com\",\"idColaborador\":\"1\",\"idLicencia\":\"1\",\"observaciones\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '6bc1e6ab8ce8b2e41f21cada96ae2f8b14017567827cd1bb633f466ed3f4de87', 'c8482766e7adb4a57b3cc27c5738223a72689adf935e2330558934433b27b894', 'Ke2ezzvWaFFKreBGJL8WIAUt+sdAnQa+liS/5PK8DjhRFwirYMciCQd48JOm6vYWnDArJju6SH1ra/yq//Udhmrb1mAe4W1lAotrF48amYIEIMKY/3QY9ToGV3QSg5zNycC/7oHe8XWYevf55BZTbmVFLWXQCZEpr8C8DhJ9MgrFu4l74dk9IsOoOCy1/wUN9o1ORGgzX5bFOsQ2H1D4eTAn9drDFWb2H9mvIugewv4y+6fBfhFVG0n4ytrvUJ5++sHkiS96WHrrL9Nf+XZFvw84LPdyOjqrjk586kRIWDeQQwkTjx+Fb/SIjVztYB8n0FQjBOHGjgltb02pKjlONA==', 'RSA-2048-SHA256', '2026-07-15 00:03:56'),
(14, 1, 1, 'ASIGNACIONES', 'DEVOLVER', 'DevolucionActivo', '3', 'Solicitud confirmada mediante la ruta /asignaciones/devolver.', NULL, '{\"condicionRecepcion\":\"BUENO\",\"idAsignacion\":\"3\",\"idEstadoActivo\":\"1\",\"idMotivoDevolucion\":\"6\",\"idUbicacion\":\"1\",\"observaciones\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'c8482766e7adb4a57b3cc27c5738223a72689adf935e2330558934433b27b894', '72bdd7c1a6da8f0252f58f982414897e1608ae83cd1cb66a76797a3989596786', 'BimVRUs5Y/SmogZLMIBElVTmwlgZaPbscDQkKZnCA0l6aJ8ymFrYuAFU9Hydb5DCdmu9mJggf4we6gpPvFcSYbDWESk6eSPTcMqB81h5M0HDgzJIRWsS4zfJVc96Ln6rWPrrlv5QKdPgXSKNZwJuSrpV1tDnCKzgfOAcxywVF+3Hq/nFlXuvTRhRIfgzntn++naQKnmu7TtGB4Y46FUVqqmsn6oPdEnrCt9YSRgRrmEhmig7/AOQdXRV2o66PeBuiE2KsFjAAGNX2OhVE3uV5cm6otMfvwKLEtl59Q8+X7MY/lHkp9bmdrRXG+l3gyWs98xlIS8lVJe5gf2+o4OVIQ==', 'RSA-2048-SHA256', '2026-07-15 00:33:18'),
(15, 1, 1, 'BAJAS', 'CREAR', NULL, '2', 'Solicitud confirmada mediante la ruta /bajas/guardar.', NULL, '{\"confirmarBaja\":\"[PROTEGIDO]\",\"documentoReferencia\":\"\",\"entidadBeneficiaria\":\"Fanlyc\",\"fechaBaja\":\"2026-07-15\",\"idActivo\":\"2\",\"idTipoBaja\":\"2\",\"motivo\":\"Pues porque estaban nesecitando una compu potente\",\"opinionTecnica\":\"Esta todo correcto con la laptop\",\"responsableDonacion\":\"Roberto Moreno\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '72bdd7c1a6da8f0252f58f982414897e1608ae83cd1cb66a76797a3989596786', '25edebed266c2f1631edcef5a5763a5e3c2ec760fcacca0cc1667f1aae29e1d1', 'Nnw/XwKw5gbfuyiJq4sUgGLoj9agCeKiZ6ymqdGlOA1k4CYSj6dEfbNOFxBnMkidCYN+JnyvwhA6IX4u7+aFrwfJDryHG7s8EfP2sdNiwR/Tn9Yza7PdHVUl28s6uCz7N8kLC4bkqGD/lm6hKN99hnDENa9RyZyy5Nhn2fMWtQhsElNlPW6cizbTX6LFT2SqgrnPYa6REsCvolpOsU0HRU8qnDM7osXlL1d1gZ6zwPn1jDpLdex4VDwKpLjXXMDXEZSYzrQLlv7zEHM+Mu2DzeHO84rGBshwpCjZD+ZY4S6yksrxZMGRRWHUpBL4lE4bi+x2FY9ciU8fWkHkplO4Vw==', 'RSA-2048-SHA256', '2026-07-15 00:34:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bajaactivo`
--

CREATE TABLE `bajaactivo` (
  `idBaja` bigint(20) UNSIGNED NOT NULL,
  `idActivo` bigint(20) UNSIGNED NOT NULL,
  `idTipoBaja` int(10) UNSIGNED NOT NULL,
  `idUsuario` int(10) UNSIGNED NOT NULL,
  `motivo` text NOT NULL,
  `opinionTecnica` text DEFAULT NULL,
  `responsableDonacion` varchar(150) DEFAULT NULL,
  `entidadBeneficiaria` varchar(180) DEFAULT NULL,
  `documentoReferencia` varchar(100) DEFAULT NULL,
  `fechaBaja` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bajaactivo`
--

INSERT INTO `bajaactivo` (`idBaja`, `idActivo`, `idTipoBaja`, `idUsuario`, `motivo`, `opinionTecnica`, `responsableDonacion`, `entidadBeneficiaria`, `documentoReferencia`, `fechaBaja`) VALUES
(1, 2, 2, 1, 'Pues porque estaban nesecitando una compu potente', 'Esta todo correcto con la laptop', 'Roberto Moreno', 'Fanlyc', NULL, '2026-07-15 00:34:52'),
(2, 44, 1, 7, 'Pantalla con daño irreversible y costo de reparación no justificable.', 'El panel presenta líneas permanentes y la reparación supera el valor recuperable.', NULL, NULL, 'BAJA-DEMO-001', '2026-06-15 10:00:00'),
(3, 41, 2, 7, 'Equipo reemplazado por renovación tecnológica.', 'El teléfono funciona y es apto para tareas básicas.', 'María González', 'Fundación Tecnología para Todos', 'DON-DEMO-001', '2026-06-30 14:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `idCategoria` int(10) UNSIGNED NOT NULL,
  `nombreCategoria` varchar(80) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `imagenAjuste` enum('cover','contain') NOT NULL DEFAULT 'cover',
  `imagenTamano` enum('compacta','mediana','amplia') NOT NULL DEFAULT 'mediana',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaActualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`idCategoria`, `nombreCategoria`, `descripcion`, `imagen`, `imagenAjuste`, `imagenTamano`, `activo`, `fechaRegistro`, `fechaActualizacion`) VALUES
(1, 'Hardware', 'Componentes, periféricos y otros bienes físicos.', NULL, 'cover', 'mediana', 1, '2026-07-14 12:10:17', '2026-07-14 12:10:17'),
(2, 'Software', 'Aplicaciones, sistemas y licencias informáticas.', NULL, 'cover', 'mediana', 1, '2026-07-14 12:10:17', '2026-07-14 12:10:17'),
(3, 'Equipo de Red', 'Routers, switches, access points, firewalls y similares.', NULL, 'cover', 'mediana', 1, '2026-07-14 12:10:17', '2026-07-14 12:10:17'),
(4, 'Equipo de Cómputo', 'Laptops, desktops, servidores y estaciones de trabajo.', 'uploads/categorias/21d953feadfecd67fc8a55bb1d3422b2.jpg', 'contain', 'mediana', 1, '2026-07-14 12:10:17', '2026-07-14 19:44:20'),
(5, 'Equipo de Telefonía', 'Teléfonos IP, celulares y equipos de comunicación.', NULL, 'cover', 'mediana', 1, '2026-07-14 12:10:17', '2026-07-14 12:10:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colaborador`
--

CREATE TABLE `colaborador` (
  `idColaborador` int(10) UNSIGNED NOT NULL,
  `idUsuario` int(10) UNSIGNED DEFAULT NULL,
  `identificacion` varchar(25) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `apellido` varchar(60) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fechaIngreso` date DEFAULT NULL,
  `fechaSalida` date DEFAULT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaActualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `colaborador`
--

INSERT INTO `colaborador` (`idColaborador`, `idUsuario`, `identificacion`, `nombre`, `apellido`, `correo`, `telefono`, `foto`, `cargo`, `departamento`, `activo`, `fechaIngreso`, `fechaSalida`, `fechaRegistro`, `fechaActualizacion`) VALUES
(1, 2, '8-1046-6767', 'Winston', 'Franco', 'winston@gmail.com', '6779-0893', NULL, 'Desarrollador de Software', 'Tecnologia', 1, '2026-07-14', NULL, '2026-07-14 16:00:18', '2026-07-14 16:00:18'),
(3, 9, '8-900-0003', 'Colaborador', 'Demo', 'demo.colaborador@trackit.local', '6000-0003', NULL, 'Analista administrativo', 'Administración', 1, '2025-01-06', NULL, '2026-07-15 01:01:16', '2026-07-15 01:01:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colaboradorubicacion`
--

CREATE TABLE `colaboradorubicacion` (
  `idColaboradorUbicacion` bigint(20) UNSIGNED NOT NULL,
  `idColaborador` int(10) UNSIGNED NOT NULL,
  `idUbicacion` int(10) UNSIGNED NOT NULL,
  `fechaInicio` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaFin` datetime DEFAULT NULL,
  `esActual` tinyint(1) NOT NULL DEFAULT 1,
  `observaciones` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `colaboradorubicacion`
--

INSERT INTO `colaboradorubicacion` (`idColaboradorUbicacion`, `idColaborador`, `idUbicacion`, `fechaInicio`, `fechaFin`, `esActual`, `observaciones`) VALUES
(1, 1, 1, '2026-07-14 20:59:16', '2026-07-14 21:02:19', 0, 'Ubicación actualizada durante la asignación del activo TRK-LAP-000001.'),
(2, 1, 1, '2026-07-14 21:02:19', '2026-07-14 21:35:46', 0, 'Ubicación actualizada durante la asignación del activo TRK-LAP-000001.'),
(3, 1, 1, '2026-07-14 21:35:46', NULL, 1, 'Ubicación actualizada durante la asignación del activo TRK-LAP-000002.'),
(5, 3, 9, '2026-07-15 01:01:16', NULL, 1, 'Ubicación inicial de los datos demostrativos.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucionactivo`
--

CREATE TABLE `devolucionactivo` (
  `idDevolucion` bigint(20) UNSIGNED NOT NULL,
  `idAsignacion` bigint(20) UNSIGNED NOT NULL,
  `usuarioRecibe` int(10) UNSIGNED NOT NULL,
  `idMotivoDevolucion` int(10) UNSIGNED NOT NULL,
  `condicionRecepcion` enum('BUENO','DANADO','INCOMPLETO','NO_VERIFICADO') NOT NULL DEFAULT 'NO_VERIFICADO',
  `observaciones` text DEFAULT NULL,
  `fechaRecepcion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `devolucionactivo`
--

INSERT INTO `devolucionactivo` (`idDevolucion`, `idAsignacion`, `usuarioRecibe`, `idMotivoDevolucion`, `condicionRecepcion`, `observaciones`, `fechaRecepcion`) VALUES
(1, 1, 1, 6, 'BUENO', NULL, '2026-07-14 21:01:45'),
(2, 3, 1, 6, 'BUENO', NULL, '2026-07-15 00:33:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadoactivo`
--

CREATE TABLE `estadoactivo` (
  `idEstadoActivo` int(10) UNSIGNED NOT NULL,
  `codigoEstado` varchar(30) NOT NULL,
  `nombreEstado` varchar(60) NOT NULL,
  `permiteAsignacion` tinyint(1) NOT NULL DEFAULT 0,
  `cuentaComoInventario` tinyint(1) NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estadoactivo`
--

INSERT INTO `estadoactivo` (`idEstadoActivo`, `codigoEstado`, `nombreEstado`, `permiteAsignacion`, `cuentaComoInventario`, `activo`) VALUES
(1, 'EN_INVENTARIO', 'En inventario', 1, 1, 1),
(2, 'ASIGNADO', 'Asignado', 0, 1, 1),
(3, 'REVISION_TECNICA', 'Revisión técnica', 0, 1, 1),
(4, 'EN_REPARACION', 'En reparación', 0, 1, 1),
(5, 'DESCARTE', 'Descarte', 0, 1, 1),
(6, 'DONADO', 'Donado', 0, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadoreparacion`
--

CREATE TABLE `estadoreparacion` (
  `idEstadoReparacion` int(10) UNSIGNED NOT NULL,
  `nombreEstado` varchar(50) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estadoreparacion`
--

INSERT INTO `estadoreparacion` (`idEstadoReparacion`, `nombreEstado`, `activo`) VALUES
(1, 'Pendiente', 1),
(2, 'En proceso', 1),
(3, 'Finalizada', 1),
(4, 'No reparable', 1),
(5, 'Cancelada', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadosolicitud`
--

CREATE TABLE `estadosolicitud` (
  `idEstadoSolicitud` int(10) UNSIGNED NOT NULL,
  `nombreEstado` varchar(50) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estadosolicitud`
--

INSERT INTO `estadosolicitud` (`idEstadoSolicitud`, `nombreEstado`, `activo`) VALUES
(1, 'En espera', 1),
(2, 'En trámite', 1),
(3, 'Aprobada', 1),
(4, 'Rechazada', 1),
(5, 'Atendida', 1),
(6, 'Cancelada', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_login`
--

CREATE TABLE `historial_login` (
  `idHistorialLogin` bigint(20) UNSIGNED NOT NULL,
  `idUsuario` int(10) UNSIGNED DEFAULT NULL,
  `usuarioIngresado` varchar(80) NOT NULL,
  `direccionIP` varchar(45) NOT NULL,
  `userAgent` varchar(500) DEFAULT NULL,
  `exito` tinyint(1) NOT NULL DEFAULT 0,
  `descripcion` varchar(255) DEFAULT NULL,
  `fechaIntento` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `historial_login`
--

INSERT INTO `historial_login` (`idHistorialLogin`, `idUsuario`, `usuarioIngresado`, `direccionIP`, `userAgent`, `exito`, `descripcion`, `fechaIntento`) VALUES
(1, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 14:41:05'),
(2, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'Contraseña incorrecta.', '2026-07-14 14:41:44'),
(3, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'Contraseña incorrecta.', '2026-07-14 14:41:46'),
(4, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'Contraseña incorrecta.', '2026-07-14 14:41:51'),
(5, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 14:46:47'),
(6, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'Contraseña incorrecta.', '2026-07-14 14:49:51'),
(7, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 14:49:55'),
(8, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'Contraseña incorrecta.', '2026-07-14 14:50:34'),
(9, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 14:50:38'),
(10, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 14:53:46'),
(11, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 15:06:00'),
(12, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 15:38:41'),
(13, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 15:53:16'),
(14, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 15:57:23'),
(15, NULL, 'guiller_tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'El usuario o correo no existe.', '2026-07-14 16:25:27'),
(16, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 16:25:36'),
(17, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 16:53:44'),
(18, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 17:02:46'),
(19, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 17:04:19'),
(20, NULL, 'guiller_tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'El usuario o correo no existe.', '2026-07-14 17:06:20'),
(21, NULL, 'guiller_tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'El usuario o correo no existe.', '2026-07-14 17:06:26'),
(22, NULL, 'guiller_tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'El usuario o correo no existe.', '2026-07-14 17:06:55'),
(23, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 17:07:31'),
(24, 1, 'joseph_admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 17:21:13'),
(25, NULL, 'Guiller_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'El usuario, correo o cédula no existe.', '2026-07-14 17:40:00'),
(26, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 17:40:10'),
(27, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 17:41:08'),
(28, NULL, 'Guiller_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'El usuario, correo o cédula no existe.', '2026-07-14 17:42:40'),
(29, NULL, 'Guiller_Tec', '::1', 'Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', 0, 'El usuario, correo o cédula no existe.', '2026-07-14 17:43:41'),
(30, 3, 'guillem_tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 17:46:30'),
(31, NULL, 'Guiller_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'El usuario, correo o cédula no existe.', '2026-07-14 17:47:23'),
(32, NULL, 'Guiller_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'El usuario, correo o cédula no existe.', '2026-07-14 17:47:36'),
(33, NULL, 'guiller_tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'El usuario, correo o cédula no existe.', '2026-07-14 17:47:44'),
(34, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 17:48:32'),
(35, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 17:49:17'),
(36, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 17:49:45'),
(37, NULL, 'Guiller_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'No existe una cuenta asociada al identificador ingresado.', '2026-07-14 17:54:44'),
(38, NULL, 'Guiller_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 0, 'No existe una cuenta asociada al identificador ingresado.', '2026-07-14 17:55:27'),
(39, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 18:12:58'),
(40, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 18:17:03'),
(41, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 18:17:18'),
(42, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 18:17:28'),
(43, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 19:42:03'),
(44, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 19:42:58'),
(45, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 19:45:01'),
(46, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 19:45:37'),
(47, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 19:45:53'),
(48, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 19:54:44'),
(49, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:01:35'),
(50, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:01:55'),
(51, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:02:07'),
(52, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:02:22'),
(53, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:06:09'),
(54, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:06:21'),
(55, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:06:38'),
(56, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:06:50'),
(57, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:13:44'),
(58, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:14:08'),
(59, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:14:55'),
(60, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:15:15'),
(61, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:15:32'),
(62, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:15:57'),
(63, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:38:45'),
(64, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:40:13'),
(65, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:55:31'),
(66, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:56:03'),
(67, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:59:31'),
(68, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 20:59:55'),
(69, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:00:29'),
(70, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:01:06'),
(71, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:26:36'),
(72, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:28:45'),
(73, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:31:29'),
(74, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:33:34'),
(75, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:35:11'),
(76, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:35:23'),
(77, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:35:57'),
(78, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:37:18'),
(79, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:38:12'),
(80, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:38:37'),
(81, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:39:34'),
(82, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:41:43'),
(83, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:41:51'),
(84, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:42:25'),
(85, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:42:29'),
(86, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:56:02'),
(87, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:56:12'),
(88, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:57:25'),
(89, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:58:14'),
(90, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 21:58:19'),
(91, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 22:26:19'),
(92, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 22:54:34'),
(93, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 23:02:33'),
(94, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 23:02:36'),
(95, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 23:03:48'),
(96, 1, 'Joseph_Admin', '192.168.101.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 23:08:35'),
(97, 1, 'Joseph_Admin', '192.168.101.3', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 23:10:43'),
(98, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 23:46:32'),
(99, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-15 00:04:07'),
(100, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-15 00:31:31'),
(101, 1, 'Joseph_Admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-15 01:01:48'),
(102, 2, 'wins_09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-15 01:04:07'),
(103, 9, 'demo_colaborador', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-15 01:05:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenactivo`
--

CREATE TABLE `imagenactivo` (
  `idImagenActivo` bigint(20) UNSIGNED NOT NULL,
  `idActivo` bigint(20) UNSIGNED NOT NULL,
  `rutaImagen` varchar(255) NOT NULL,
  `nombreOriginal` varchar(255) DEFAULT NULL,
  `mimeType` varchar(100) DEFAULT NULL,
  `tamanoBytes` int(10) UNSIGNED DEFAULT NULL,
  `esPrincipal` tinyint(1) NOT NULL DEFAULT 0,
  `ordenVisual` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `imagenactivo`
--

INSERT INTO `imagenactivo` (`idImagenActivo`, `idActivo`, `rutaImagen`, `nombreOriginal`, `mimeType`, `tamanoBytes`, `esPrincipal`, `ordenVisual`, `activo`, `fechaRegistro`) VALUES
(1, 1, 'uploads/activos/fbda946716d2da0fb2621bab82aaf25f.jpg', 'pavilon 2.jpg', 'image/jpeg', 470854, 0, 1, 1, '2026-07-14 20:37:22'),
(2, 1, 'uploads/activos/470891e67d829c32b87878e8c750c2c7.webp', 'pavilon 1.webp', 'image/webp', 175096, 1, 2, 1, '2026-07-14 20:37:22'),
(3, 2, 'uploads/activos/d8585135354c4d8a29f7483527b90289.webp', 'dell2.webp', 'image/webp', 13074, 1, 1, 1, '2026-07-14 21:31:21'),
(4, 2, 'uploads/activos/ac82c4fb07a2586476b5bc1e87183218.jpg', 'Dell1.jpg', 'image/jpeg', 64667, 0, 2, 1, '2026-07-14 21:31:21'),
(5, 3, 'uploads/activos/b123e5650241dba99bfcf06cf5e04ecf.jpg', 'imagesoffice.jpg', 'image/jpeg', 35187, 1, 1, 1, '2026-07-14 23:59:47'),
(6, 3, 'uploads/activos/a5fbcae2159dd284dc5e7c31e77a01a9.png', 'images.png', 'image/png', 19074, 0, 2, 1, '2026-07-14 23:59:47'),
(47, 23, 'assets/img/demo/activos/trk-lap-001-1.png', 'trk-lap-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(48, 23, 'assets/img/demo/activos/trk-lap-001-2.png', 'trk-lap-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(49, 24, 'assets/img/demo/activos/trk-lap-002-1.png', 'trk-lap-002-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(50, 24, 'assets/img/demo/activos/trk-lap-002-2.png', 'trk-lap-002-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(51, 25, 'assets/img/demo/activos/trk-lap-003-1.png', 'trk-lap-003-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(52, 25, 'assets/img/demo/activos/trk-lap-003-2.png', 'trk-lap-003-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(53, 26, 'assets/img/demo/activos/trk-lap-004-1.png', 'trk-lap-004-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(54, 26, 'assets/img/demo/activos/trk-lap-004-2.png', 'trk-lap-004-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(55, 27, 'assets/img/demo/activos/trk-lap-005-1.png', 'trk-lap-005-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(56, 27, 'assets/img/demo/activos/trk-lap-005-2.png', 'trk-lap-005-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(57, 28, 'assets/img/demo/activos/trk-desk-001-1.png', 'trk-desk-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(58, 28, 'assets/img/demo/activos/trk-desk-001-2.png', 'trk-desk-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(59, 29, 'assets/img/demo/activos/trk-desk-002-1.png', 'trk-desk-002-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(60, 29, 'assets/img/demo/activos/trk-desk-002-2.png', 'trk-desk-002-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(61, 30, 'assets/img/demo/activos/trk-mon-001-1.png', 'trk-mon-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(62, 30, 'assets/img/demo/activos/trk-mon-001-2.png', 'trk-mon-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(63, 31, 'assets/img/demo/activos/trk-mon-002-1.png', 'trk-mon-002-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(64, 31, 'assets/img/demo/activos/trk-mon-002-2.png', 'trk-mon-002-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(65, 32, 'assets/img/demo/activos/trk-prn-001-1.png', 'trk-prn-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(66, 32, 'assets/img/demo/activos/trk-prn-001-2.png', 'trk-prn-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(67, 33, 'assets/img/demo/activos/trk-rtr-001-1.png', 'trk-rtr-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(68, 33, 'assets/img/demo/activos/trk-rtr-001-2.png', 'trk-rtr-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(69, 34, 'assets/img/demo/activos/trk-sw-001-1.png', 'trk-sw-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(70, 34, 'assets/img/demo/activos/trk-sw-001-2.png', 'trk-sw-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(71, 35, 'assets/img/demo/activos/trk-ap-001-1.png', 'trk-ap-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(72, 35, 'assets/img/demo/activos/trk-ap-001-2.png', 'trk-ap-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(73, 36, 'assets/img/demo/activos/trk-fw-001-1.png', 'trk-fw-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(74, 36, 'assets/img/demo/activos/trk-fw-001-2.png', 'trk-fw-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(75, 37, 'assets/img/demo/activos/trk-srv-001-1.png', 'trk-srv-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(76, 37, 'assets/img/demo/activos/trk-srv-001-2.png', 'trk-srv-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(77, 38, 'assets/img/demo/activos/trk-ws-001-1.png', 'trk-ws-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(78, 38, 'assets/img/demo/activos/trk-ws-001-2.png', 'trk-ws-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(79, 39, 'assets/img/demo/activos/trk-ip-001-1.png', 'trk-ip-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(80, 39, 'assets/img/demo/activos/trk-ip-001-2.png', 'trk-ip-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(81, 40, 'assets/img/demo/activos/trk-ip-002-1.png', 'trk-ip-002-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(82, 40, 'assets/img/demo/activos/trk-ip-002-2.png', 'trk-ip-002-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(83, 41, 'assets/img/demo/activos/trk-mob-001-1.png', 'trk-mob-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(84, 41, 'assets/img/demo/activos/trk-mob-001-2.png', 'trk-mob-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(85, 42, 'assets/img/demo/activos/lic-adobe-001-1.png', 'lic-adobe-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(86, 42, 'assets/img/demo/activos/lic-adobe-001-2.png', 'lic-adobe-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(87, 43, 'assets/img/demo/activos/lic-win11-001-1.png', 'lic-win11-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(88, 43, 'assets/img/demo/activos/lic-win11-001-2.png', 'lic-win11-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16'),
(89, 44, 'assets/img/demo/activos/trk-mon-old-001-1.png', 'trk-mon-old-001-1.png', 'image/png', NULL, 1, 1, 1, '2026-07-15 01:01:16'),
(90, 44, 'assets/img/demo/activos/trk-mon-old-001-2.png', 'trk-mon-old-001-2.png', 'image/png', NULL, 0, 2, 1, '2026-07-15 01:01:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `licenciasoftware`
--

CREATE TABLE `licenciasoftware` (
  `idLicencia` bigint(20) UNSIGNED NOT NULL,
  `idActivo` bigint(20) UNSIGNED NOT NULL,
  `proveedor` varchar(120) DEFAULT NULL,
  `tipoLicencia` varchar(80) DEFAULT NULL,
  `urlAcceso` varchar(500) DEFAULT NULL,
  `claveCifrada` longtext DEFAULT NULL,
  `cantidadPuestos` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `fechaInicio` date DEFAULT NULL,
  `fechaExpiracion` date DEFAULT NULL,
  `renovacionAutomatica` tinyint(1) NOT NULL DEFAULT 0,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `licenciasoftware`
--

INSERT INTO `licenciasoftware` (`idLicencia`, `idActivo`, `proveedor`, `tipoLicencia`, `urlAcceso`, `claveCifrada`, `cantidadPuestos`, `fechaInicio`, `fechaExpiracion`, `renovacionAutomatica`, `observaciones`) VALUES
(1, 3, 'Microsoft', 'Suscripción empresarial', 'https://www.office.com', 'NdN1E7QK98vF1CeZnqD8lv8Qrs1SvGttkUtOdV1JtfycCsFM5gpGGhVsiAhNTP3Z1lGPMaJw6exci3yDeJEPVfYFgsvbuBjYJBRz0mVmrhgH2h7Vu6e68TpbGRwdJkHPj20r8032/sKXvKqFiqiGSwlCjAmTYSNJ1EB8orcCStO+q6ZmmD8fqgplO2Osrl0FItKxtcdD81QpNqLYnRw95SXuoZTxskvbzxBztSEwOevssIxbD20/rNA8G0nt4t3fWTKiw0d7FKfxFW4QxWpj+5gTYz3ZAg9tRx++AD8I9X5+fa0I/HbiC2QWBQcoEWppFc3jeXJEXGOJbUOPdi7qWQ==', 10, '2026-07-15', '2027-07-15', 1, NULL),
(2, 42, 'Adobe', 'Suscripción para equipos', 'https://creativecloud.adobe.com', NULL, 5, '2026-02-01', '2027-02-01', 1, 'Licencia demostrativa para el equipo creativo.'),
(3, 43, 'Microsoft', 'Licencia perpetua', 'https://www.microsoft.com/windows', NULL, 5, '2025-09-01', NULL, 0, 'Paquete demostrativo de licencias Windows 11 Pro.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llavepublicausuario`
--

CREATE TABLE `llavepublicausuario` (
  `idLlavePublica` int(10) UNSIGNED NOT NULL,
  `idUsuario` int(10) UNSIGNED NOT NULL,
  `llavePublica` longtext NOT NULL,
  `huellaDigital` char(64) NOT NULL,
  `algoritmo` varchar(30) NOT NULL DEFAULT 'RSA-2048-SHA256',
  `versionLlave` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaRevocacion` datetime DEFAULT NULL,
  `motivoRevocacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `llavepublicausuario`
--

INSERT INTO `llavepublicausuario` (`idLlavePublica`, `idUsuario`, `llavePublica`, `huellaDigital`, `algoritmo`, `versionLlave`, `activa`, `fechaCreacion`, `fechaRevocacion`, `motivoRevocacion`) VALUES
(1, 1, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmMJl4HzK2NDQ2Gb36+Vh\nkvnDUnctuk8J7djsCBc2RvGLHpPV1mdNbc3FikWi/A/lQZxJyRaVtHWeLJ89GWC0\nFnEGy2ZUaTHYauo5AfsP0hOoizL5VBK8vPn6s7vM67DSAYidOhNvunagVfmL/u8Q\n62qKa7iVMn+oyqEeGNqH2/F5RpVkvpysXjS0r2EvXqpDChrr9vDQj7QdjMahZH7R\nLLlheSOsL3e66C/hZg63dR4MI3IF1mlhFq0kj7n0ngOhhwzYhk2Al4VE+F/bSt2M\nRJodChchH4l7dvJboI5aTpq1YmSECCt/B762v4gTJcErBjgfmbAdIcAwjTlaJAiT\nuQIDAQAB\n-----END PUBLIC KEY-----\n', '2654148ea7985b976d76551d6114ec9935925a88d353dafc302d7ef447ef630f', 'RSA-2048-SHA256', 1, 1, '2026-07-14 14:20:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `motivodevolucion`
--

CREATE TABLE `motivodevolucion` (
  `idMotivoDevolucion` int(10) UNSIGNED NOT NULL,
  `nombreMotivo` varchar(80) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `motivodevolucion`
--

INSERT INTO `motivodevolucion` (`idMotivoDevolucion`, `nombreMotivo`, `activo`) VALUES
(1, 'Renuncia', 1),
(2, 'Traslado', 1),
(3, 'Cambio de equipo', 1),
(4, 'Equipo dañado', 1),
(5, 'Fin de licencia', 1),
(6, 'Otro', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientoactivo`
--

CREATE TABLE `movimientoactivo` (
  `idMovimiento` bigint(20) UNSIGNED NOT NULL,
  `idActivo` bigint(20) UNSIGNED NOT NULL,
  `idUsuario` int(10) UNSIGNED NOT NULL,
  `tipoMovimiento` enum('REGISTRO','ACTUALIZACION','ASIGNACION','DEVOLUCION','CAMBIO_ESTADO','CAMBIO_UBICACION','REPARACION','DESCARTE','DONACION') NOT NULL,
  `idEstadoAnterior` int(10) UNSIGNED DEFAULT NULL,
  `idEstadoNuevo` int(10) UNSIGNED DEFAULT NULL,
  `idUbicacionAnterior` int(10) UNSIGNED DEFAULT NULL,
  `idUbicacionNueva` int(10) UNSIGNED DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fechaMovimiento` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimientoactivo`
--

INSERT INTO `movimientoactivo` (`idMovimiento`, `idActivo`, `idUsuario`, `tipoMovimiento`, `idEstadoAnterior`, `idEstadoNuevo`, `idUbicacionAnterior`, `idUbicacionNueva`, `descripcion`, `fechaMovimiento`) VALUES
(1, 1, 1, 'REGISTRO', NULL, 1, NULL, NULL, 'Registro inicial del activo TRK-LAP-000001.', '2026-07-14 20:37:22'),
(2, 1, 1, 'ACTUALIZACION', 1, 1, NULL, NULL, 'Actualización administrativa del activo TRK-LAP-000001.', '2026-07-14 20:37:39'),
(3, 1, 1, 'ACTUALIZACION', 1, 1, NULL, NULL, 'Actualización administrativa del activo TRK-LAP-000001.', '2026-07-14 20:38:35'),
(4, 1, 1, 'ASIGNACION', 1, 2, NULL, 1, 'Asignación del activo TRK-LAP-000001 al colaborador Winston Franco.', '2026-07-14 20:59:16'),
(5, 1, 1, 'DEVOLUCION', 2, 1, 1, 1, 'Devolución del activo TRK-LAP-000001 por Winston Franco. Motivo: Otro.', '2026-07-14 21:01:45'),
(6, 1, 1, 'ASIGNACION', 1, 2, 1, 1, 'Asignación del activo TRK-LAP-000001 al colaborador Winston Franco.', '2026-07-14 21:02:19'),
(7, 2, 1, 'REGISTRO', NULL, 1, NULL, NULL, 'Registro inicial del activo TRK-LAP-000002.', '2026-07-14 21:31:21'),
(8, 2, 1, 'ASIGNACION', 1, 2, NULL, 1, 'Asignación del activo TRK-LAP-000002 al colaborador Winston Franco.', '2026-07-14 21:35:46'),
(9, 1, 1, 'REPARACION', 2, 3, 1, 1, 'Solicitud de reparación asignada al técnico Guillermo Mas.', '2026-07-14 21:39:12'),
(10, 1, 3, 'REPARACION', 3, 2, 1, 1, 'Reparación actualizada a estado Finalizada. ', '2026-07-14 21:40:56'),
(11, 1, 3, 'REPARACION', 2, 2, 1, 1, 'Reparación actualizada a estado Finalizada. ', '2026-07-14 21:41:15'),
(12, 3, 1, 'REGISTRO', NULL, 1, NULL, NULL, 'Registro inicial del activo LIC-M365-001.', '2026-07-14 23:59:47'),
(13, 2, 1, 'DEVOLUCION', 2, 1, 1, 1, 'Devolución del activo TRK-LAP-000002 por Winston Franco. Motivo: Otro.', '2026-07-15 00:33:17'),
(14, 2, 1, 'DONACION', 1, 6, 1, 1, 'Donación del activo TRK-LAP-000002 a Fanlyc.', '2026-07-15 00:34:52'),
(15, 23, 7, 'REGISTRO', NULL, 2, NULL, 9, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(16, 24, 7, 'REGISTRO', NULL, 1, NULL, 8, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(17, 25, 7, 'REGISTRO', NULL, 3, NULL, 11, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(18, 26, 7, 'REGISTRO', NULL, 1, NULL, 8, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(19, 27, 7, 'REGISTRO', NULL, 1, NULL, 8, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(20, 28, 7, 'REGISTRO', NULL, 1, NULL, 10, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(21, 29, 7, 'REGISTRO', NULL, 1, NULL, 9, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(22, 30, 7, 'REGISTRO', NULL, 1, NULL, 10, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(23, 31, 7, 'REGISTRO', NULL, 1, NULL, 9, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(24, 32, 7, 'REGISTRO', NULL, 4, NULL, 11, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(25, 33, 7, 'REGISTRO', NULL, 1, NULL, 12, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(26, 34, 7, 'REGISTRO', NULL, 1, NULL, 12, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(27, 35, 7, 'REGISTRO', NULL, 1, NULL, 9, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(28, 36, 7, 'REGISTRO', NULL, 1, NULL, 12, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(29, 37, 7, 'REGISTRO', NULL, 1, NULL, 12, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(30, 38, 7, 'REGISTRO', NULL, 1, NULL, 9, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(31, 39, 7, 'REGISTRO', NULL, 1, NULL, 9, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(32, 40, 7, 'REGISTRO', NULL, 1, NULL, 10, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(33, 41, 7, 'REGISTRO', NULL, 6, NULL, 8, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(34, 42, 7, 'REGISTRO', NULL, 1, NULL, 8, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(35, 43, 7, 'REGISTRO', NULL, 1, NULL, 8, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(36, 44, 7, 'REGISTRO', NULL, 5, NULL, 8, 'Registro incorporado por el conjunto de datos demostrativos.', '2026-07-15 01:01:16'),
(46, 23, 7, 'ASIGNACION', 1, 2, 8, 9, 'Asignación demostrativa al colaborador demo.', '2026-07-01 09:00:00'),
(47, 32, 8, 'REPARACION', 3, 4, 11, 11, 'Reparación demostrativa de impresora.', '2026-07-10 10:30:00'),
(48, 44, 7, 'DESCARTE', 3, 5, 11, 8, 'Baja demostrativa por descarte.', '2026-06-15 10:00:00'),
(49, 41, 7, 'DONACION', 1, 6, 8, 8, 'Baja demostrativa por donación.', '2026-06-30 14:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `idProducto` int(10) UNSIGNED NOT NULL,
  `idSubcategoria` int(10) UNSIGNED NOT NULL,
  `nombreProducto` varchar(120) NOT NULL,
  `marca` varchar(80) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `tipoProducto` enum('HARDWARE','SOFTWARE','LICENCIA') NOT NULL,
  `vidaUtilMeses` smallint(5) UNSIGNED DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaActualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`idProducto`, `idSubcategoria`, `nombreProducto`, `marca`, `modelo`, `descripcion`, `tipoProducto`, `vidaUtilMeses`, `imagen`, `activo`, `fechaRegistro`, `fechaActualizacion`) VALUES
(1, 11, 'Laptop HP ProBook 450', 'HP', 'ProBook 450 G10', 'Laptop institucional para actividades administrativas.', 'HARDWARE', 60, 'uploads/productos/04612ca419760e6817da45b55ae06147.jpg', 1, '2026-07-14 19:57:00', '2026-07-14 19:57:00'),
(2, 11, 'Laptop HP Pavilion x360', 'HP', 'Pavilion x360', 'Laptop táctil y plegable para uso cotidiano.', 'HARDWARE', 90, 'uploads/productos/59ee14e5c49bcfaa7ecc8445d8b4dac6.jpg', 1, '2026-07-14 19:59:21', '2026-07-14 20:02:03'),
(3, 11, 'Laptop Dell G3 3590', 'Dell', 'G3 3590', 'Laptop de alto rendimiento para trabajos pesados.', 'HARDWARE', 67, 'uploads/productos/7de6d6ad6f00c280bd0b9d16cc2bb5b4.jpg', 1, '2026-07-14 20:05:56', '2026-07-14 20:06:58'),
(4, 11, 'Lenovo LoQ 9079', 'Lenovo', 'LoQ 9079', 'Laptop de alto rendimiento para procesos 3D.', 'HARDWARE', 100, 'uploads/productos/344fff2d7b6b14618af4ce7b684badc0.webp', 1, '2026-07-14 20:13:15', '2026-07-14 20:13:15'),
(5, 17, 'Microsoft 365', 'Microsoft', 'Microsoft 365 Business Standard', 'Suite de productividad empresarial con aplicaciones\r\nde oficina y servicios en la nube.', 'LICENCIA', 12, 'uploads/productos/ac28f161e460d86500ebb3ddff8aafe6.png', 1, '2026-07-14 23:56:55', '2026-07-14 23:56:55'),
(24, 1, 'Monitor Dell P2422H', 'Dell', 'P2422H', 'Monitor profesional de 24 pulgadas para estaciones administrativas.', 'HARDWARE', 60, 'assets/img/demo/productos/monitor-dell-p2422h.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(25, 2, 'Impresora HP LaserJet Pro M404dn', 'HP', 'M404dn', 'Impresora láser monocromática de red para oficina.', 'HARDWARE', 60, 'assets/img/demo/productos/impresora-hp-m404dn.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(26, 3, 'Combo Logitech MK270', 'Logitech', 'MK270', 'Teclado y mouse inalámbricos para puestos de trabajo.', 'HARDWARE', 36, 'assets/img/demo/productos/combo-logitech-mk270.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(27, 4, 'Microsoft 365 Business Standard', 'Microsoft', 'Business Standard', 'Suite de productividad empresarial por suscripción.', 'LICENCIA', 12, 'assets/img/demo/productos/microsoft-365.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(28, 4, 'Adobe Creative Cloud Teams', 'Adobe', 'Creative Cloud Teams', 'Licencia de aplicaciones creativas para equipos de diseño.', 'LICENCIA', 12, 'assets/img/demo/productos/adobe-creative-cloud.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(29, 5, 'Microsoft Teams Desktop', 'Microsoft', 'Teams', 'Aplicación institucional de colaboración y videoconferencias.', 'SOFTWARE', 36, 'assets/img/demo/productos/microsoft-teams.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(30, 6, 'Windows 11 Pro', 'Microsoft', '11 Pro', 'Sistema operativo profesional para estaciones de trabajo.', 'LICENCIA', 60, 'assets/img/demo/productos/windows-11-pro.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(31, 7, 'Router Cisco ISR 1100', 'Cisco', 'C1111-8P', 'Router empresarial para conectividad WAN y servicios de red.', 'HARDWARE', 84, 'assets/img/demo/productos/router-cisco-isr1100.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(32, 8, 'Switch Cisco CBS350 24 puertos', 'Cisco', 'CBS350-24T-4G', 'Switch administrable de 24 puertos Gigabit.', 'HARDWARE', 84, 'assets/img/demo/productos/switch-cisco-cbs350.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(33, 9, 'Access Point Ubiquiti UniFi U6 Lite', 'Ubiquiti', 'U6 Lite', 'Punto de acceso Wi-Fi 6 para oficinas.', 'HARDWARE', 60, 'assets/img/demo/productos/ap-ubiquiti-u6-lite.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(34, 10, 'Firewall Fortinet FortiGate 40F', 'Fortinet', 'FortiGate 40F', 'Equipo de seguridad perimetral y control de tráfico.', 'HARDWARE', 60, 'assets/img/demo/productos/firewall-fortigate-40f.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(35, 11, 'Laptop HP ProBook 450 G10', 'HP', 'ProBook 450 G10', 'Laptop empresarial para personal administrativo.', 'HARDWARE', 60, 'assets/img/demo/productos/laptop-hp-probook-450.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(36, 11, 'Laptop Dell Latitude 5440', 'Dell', 'Latitude 5440', 'Laptop empresarial para trabajo híbrido.', 'HARDWARE', 60, 'assets/img/demo/productos/laptop-dell-latitude-5440.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(37, 12, 'Desktop Lenovo ThinkCentre M70s Gen 4', 'Lenovo', 'ThinkCentre M70s Gen 4', 'Computadora de escritorio empresarial compacta.', 'HARDWARE', 60, 'assets/img/demo/productos/desktop-lenovo-m70s.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(38, 13, 'Servidor Dell PowerEdge T350', 'Dell', 'PowerEdge T350', 'Servidor torre para servicios internos y respaldos.', 'HARDWARE', 84, 'assets/img/demo/productos/servidor-dell-t350.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(39, 16, 'Workstation HP Z2 Tower G9', 'HP', 'Z2 Tower G9', 'Estación de trabajo para diseño, modelado y edición.', 'HARDWARE', 72, 'assets/img/demo/productos/workstation-hp-z2.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(40, 14, 'Teléfono IP Yealink SIP-T54W', 'Yealink', 'SIP-T54W', 'Teléfono IP empresarial con conectividad de red.', 'HARDWARE', 60, 'assets/img/demo/productos/telefono-yealink-t54w.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(41, 15, 'Samsung Galaxy A55 5G', 'Samsung', 'Galaxy A55 5G', 'Teléfono móvil institucional para personal de campo.', 'HARDWARE', 36, 'assets/img/demo/productos/samsung-galaxy-a55.png', 1, '2026-07-15 01:01:16', '2026-07-15 01:01:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reparacion`
--

CREATE TABLE `reparacion` (
  `idReparacion` bigint(20) UNSIGNED NOT NULL,
  `idActivo` bigint(20) UNSIGNED NOT NULL,
  `idTecnico` int(10) UNSIGNED NOT NULL,
  `idEstadoReparacion` int(10) UNSIGNED NOT NULL,
  `descripcionFalla` text NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `trabajoRealizado` text DEFAULT NULL,
  `costoReparacion` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fechaInicio` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaFin` datetime DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reparacion`
--

INSERT INTO `reparacion` (`idReparacion`, `idActivo`, `idTecnico`, `idEstadoReparacion`, `descripcionFalla`, `diagnostico`, `trabajoRealizado`, `costoReparacion`, `fechaInicio`, `fechaFin`, `observaciones`) VALUES
(1, 1, 3, 3, 'Esta mañana la computadora dejo de encender y solo la habia usado anoche para hacer unos trabajos y ya.', 'Se le fundió un led de la pantalla que hizo corto con los demas leds y por eso murio la pantalla.', 'Se abrió la computadora para revisión, y se cambio la pantalla.', 250.00, '2026-07-14 21:39:12', '2026-07-14 21:41:15', 'Repara eso ya, que el man esta esperando.'),
(2, 32, 8, 2, 'Atascos frecuentes y manchas en las impresiones.', 'Rodillos de arrastre desgastados.', 'Limpieza interna y solicitud de repuesto.', 45.00, '2026-07-10 10:30:00', NULL, 'Caso demostrativo pendiente de finalizar.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idRol` int(10) UNSIGNED NOT NULL,
  `nombreRol` varchar(40) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idRol`, `nombreRol`, `descripcion`, `activo`) VALUES
(1, 'Administrador', 'Administra usuarios, configuración y todos los módulos.', 1),
(2, 'Colaborador', 'Consulta sus activos asignados y registra solicitudes.', 1),
(3, 'Técnico', 'Gestiona revisiones técnicas, reparaciones y bajas.', 1),
(4, 'Operador', 'Registra y actualiza información operativa del inventario.', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudnecesidad`
--

CREATE TABLE `solicitudnecesidad` (
  `idSolicitud` bigint(20) UNSIGNED NOT NULL,
  `idColaborador` int(10) UNSIGNED NOT NULL,
  `idSubcategoria` int(10) UNSIGNED DEFAULT NULL,
  `idProducto` int(10) UNSIGNED DEFAULT NULL,
  `idEstadoSolicitud` int(10) UNSIGNED NOT NULL,
  `tipoSolicitud` enum('EQUIPO','SOFTWARE','LICENCIA','OTRA') NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcionNecesidad` text NOT NULL,
  `justificacion` text NOT NULL,
  `cantidad` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `prioridad` enum('BAJA','MEDIA','ALTA','URGENTE') NOT NULL DEFAULT 'MEDIA',
  `periodoNecesidad` enum('INMEDIATA','ANUAL','QUINQUENAL') NOT NULL DEFAULT 'INMEDIATA',
  `anioPresupuestado` year(4) DEFAULT NULL,
  `costoEstimado` decimal(12,2) DEFAULT NULL,
  `usuarioRevisa` int(10) UNSIGNED DEFAULT NULL,
  `observacionRevision` text DEFAULT NULL,
  `fechaSolicitud` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaRevision` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudnecesidad`
--

INSERT INTO `solicitudnecesidad` (`idSolicitud`, `idColaborador`, `idSubcategoria`, `idProducto`, `idEstadoSolicitud`, `tipoSolicitud`, `titulo`, `descripcionNecesidad`, `justificacion`, `cantidad`, `prioridad`, `periodoNecesidad`, `anioPresupuestado`, `costoEstimado`, `usuarioRevisa`, `observacionRevision`, `fechaSolicitud`, `fechaRevision`) VALUES
(1, 1, 11, 3, 3, 'EQUIPO', 'Laptop para proyecto Universitario', 'Necesito un laptop potente para unas animaciones.', 'Levo tiempo sin solicitar una, necesito una porfavor.', 1, 'MEDIA', 'INMEDIATA', '2026', 90.00, 1, NULL, '2026-07-14 21:32:42', '2026-07-14 21:34:51'),
(2, 3, 11, 36, 2, 'EQUIPO', 'Renovación de laptops administrativas', 'Se requieren dos laptops para reemplazar equipos con vida útil próxima a vencer.', 'Mejorar el rendimiento y la continuidad operativa del departamento.', 2, 'ALTA', 'ANUAL', '2026', 2300.00, 7, 'Solicitud incluida en el presupuesto anual.', '2026-07-02 08:30:00', '2026-07-03 11:00:00'),
(3, 3, 4, 27, 1, 'LICENCIA', 'Puestos adicionales de Microsoft 365', 'Se necesitan cinco puestos adicionales para nuevos colaboradores.', 'Mantener herramientas de correo, ofimática y colaboración.', 5, 'MEDIA', 'INMEDIATA', '2026', 750.00, NULL, NULL, '2026-07-08 14:15:00', NULL),
(4, 3, 9, 33, 3, 'EQUIPO', 'Ampliación de cobertura inalámbrica', 'Instalar dos puntos de acceso adicionales en la sucursal.', 'Mejorar la cobertura y estabilidad de la red inalámbrica.', 2, 'MEDIA', 'QUINQUENAL', '2027', 290.00, 7, 'Aprobada para la planificación de infraestructura.', '2026-06-20 09:40:00', '2026-06-23 15:20:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudreparacion`
--

CREATE TABLE `solicitudreparacion` (
  `idSolicitudReparacion` bigint(20) UNSIGNED NOT NULL,
  `idActivo` bigint(20) UNSIGNED NOT NULL,
  `idColaborador` int(10) UNSIGNED NOT NULL,
  `idUbicacionSolicitud` int(10) UNSIGNED DEFAULT NULL,
  `idTecnico` int(10) UNSIGNED DEFAULT NULL,
  `idReparacion` bigint(20) UNSIGNED DEFAULT NULL,
  `usuarioRevisa` int(10) UNSIGNED DEFAULT NULL,
  `estadoSolicitud` enum('EN_ESPERA','ASIGNADA','EN_PROCESO','FINALIZADA','RECHAZADA','CANCELADA') NOT NULL DEFAULT 'EN_ESPERA',
  `titulo` varchar(150) NOT NULL,
  `descripcionFalla` text NOT NULL,
  `prioridad` enum('BAJA','MEDIA','ALTA','URGENTE') NOT NULL DEFAULT 'MEDIA',
  `observacionRevision` text DEFAULT NULL,
  `fechaSolicitud` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaAsignacion` datetime DEFAULT NULL,
  `fechaCierre` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudreparacion`
--

INSERT INTO `solicitudreparacion` (`idSolicitudReparacion`, `idActivo`, `idColaborador`, `idUbicacionSolicitud`, `idTecnico`, `idReparacion`, `usuarioRevisa`, `estadoSolicitud`, `titulo`, `descripcionFalla`, `prioridad`, `observacionRevision`, `fechaSolicitud`, `fechaAsignacion`, `fechaCierre`) VALUES
(1, 1, 1, 1, 3, 1, 1, 'FINALIZADA', 'La compu dejo de encender de la nada', 'Esta mañana la computadora dejo de encender y solo la habia usado anoche para hacer unos trabajos y ya.', 'MEDIA', 'Repara eso ya, que el man esta esperando.', '2026-07-14 21:38:02', '2026-07-14 21:39:12', '2026-07-14 21:41:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategoria`
--

CREATE TABLE `subcategoria` (
  `idSubcategoria` int(10) UNSIGNED NOT NULL,
  `idCategoria` int(10) UNSIGNED NOT NULL,
  `nombreSubcategoria` varchar(80) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `subcategoria`
--

INSERT INTO `subcategoria` (`idSubcategoria`, `idCategoria`, `nombreSubcategoria`, `descripcion`, `imagen`, `activo`, `fechaRegistro`) VALUES
(1, 1, 'Monitor', 'Monitores y pantallas.', NULL, 1, '2026-07-14 12:10:17'),
(2, 1, 'Impresora', 'Impresoras y equipos multifuncionales.', NULL, 1, '2026-07-14 12:10:17'),
(3, 1, 'Periférico', 'Teclados, mouse, cámaras y accesorios.', NULL, 1, '2026-07-14 12:10:17'),
(4, 2, 'Licencia', 'Licencias de uso de software.', NULL, 1, '2026-07-14 12:10:17'),
(5, 2, 'Aplicación', 'Aplicaciones y plataformas informáticas.', NULL, 1, '2026-07-14 12:10:17'),
(6, 2, 'Sistema Operativo', 'Licencias y medios de sistemas operativos.', NULL, 1, '2026-07-14 12:10:17'),
(7, 3, 'Router', 'Equipos de enrutamiento.', NULL, 1, '2026-07-14 12:10:17'),
(8, 3, 'Switch', 'Conmutadores de red.', NULL, 1, '2026-07-14 12:10:17'),
(9, 3, 'Access Point', 'Puntos de acceso inalámbricos.', NULL, 1, '2026-07-14 12:10:17'),
(10, 3, 'Firewall', 'Dispositivos de seguridad de red.', NULL, 1, '2026-07-14 12:10:17'),
(11, 4, 'Laptop', 'Computadoras portátiles.', 'uploads/subcategorias/8a05d4030f4c306839924130266f0537.jpg', 1, '2026-07-14 12:10:17'),
(12, 4, 'Desktop', 'Computadoras de escritorio.', 'uploads/subcategorias/c82aa6b5d297623abbe9ef812998175d.jpg', 1, '2026-07-14 12:10:17'),
(13, 4, 'Servidor', 'Servidores físicos.', NULL, 1, '2026-07-14 12:10:17'),
(14, 5, 'Teléfono IP', 'Teléfonos de voz sobre IP.', NULL, 1, '2026-07-14 12:10:17'),
(15, 5, 'Teléfono móvil', 'Teléfonos inteligentes institucionales.', NULL, 1, '2026-07-14 12:10:17'),
(16, 4, 'Workstations', 'Equipos optimizados para tareas pesadas como diseño 3D, arquitectura o edición de video.', 'uploads/subcategorias/0de5053ae1e2359a8eddefb6f4cb9a64.jpg', 1, '2026-07-14 18:16:14'),
(17, 2, 'Suite de oficina', 'Conjunto de programas informáticos integrados diseñados para facilitar, organizar y automatizar las tareas habituales de una oficina, estudio o negocio', 'uploads/subcategorias/f1c7eed047d05ca9f167ee726dd58583.webp', 1, '2026-07-14 23:51:37'),
(19, 2, 'Diseño creativo', 'Aplicaciones y licencias para diseño y producción multimedia.', NULL, 1, '2026-07-15 01:01:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipobaja`
--

CREATE TABLE `tipobaja` (
  `idTipoBaja` int(10) UNSIGNED NOT NULL,
  `codigoTipo` varchar(20) NOT NULL,
  `nombreTipo` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipobaja`
--

INSERT INTO `tipobaja` (`idTipoBaja`, `codigoTipo`, `nombreTipo`) VALUES
(1, 'DESCARTE', 'Descarte'),
(2, 'DONACION', 'Donación');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion`
--

CREATE TABLE `ubicacion` (
  `idUbicacion` int(10) UNSIGNED NOT NULL,
  `nombreUbicacion` varchar(100) NOT NULL,
  `tipoUbicacion` enum('EDIFICIO','OFICINA','CASA','BODEGA','OTRA') NOT NULL DEFAULT 'OFICINA',
  `edificio` varchar(80) DEFAULT NULL,
  `piso` varchar(30) DEFAULT NULL,
  `oficina` varchar(50) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ubicacion`
--

INSERT INTO `ubicacion` (`idUbicacion`, `nombreUbicacion`, `tipoUbicacion`, `edificio`, `piso`, `oficina`, `direccion`, `descripcion`, `activo`, `fechaRegistro`) VALUES
(1, 'Oficina principal - Piso 3', 'OFICINA', 'Edificio Central', '3', 'Recursos Humanos', 'Av. Ricardo J. Alfaro, Golden Point', NULL, 1, '2026-07-14 20:58:16'),
(8, 'Bodega TI Principal', 'BODEGA', 'Edificio Central', 'Planta baja', 'Bodega TI', 'Vía principal, Ciudad de Panamá', 'Almacén principal de equipos disponibles.', 1, '2026-07-15 01:01:16'),
(9, 'Oficina Administración', 'OFICINA', 'Edificio Central', 'Piso 2', 'Administración', 'Vía principal, Ciudad de Panamá', 'Área administrativa.', 1, '2026-07-15 01:01:16'),
(10, 'Oficina Recursos Humanos', 'OFICINA', 'Edificio Central', 'Piso 3', 'Recursos Humanos', 'Vía principal, Ciudad de Panamá', 'Área de Recursos Humanos.', 1, '2026-07-15 01:01:16'),
(11, 'Taller Técnico', 'OFICINA', 'Edificio Central', 'Planta baja', 'Taller TI', 'Vía principal, Ciudad de Panamá', 'Diagnóstico y reparación de equipos.', 1, '2026-07-15 01:01:16'),
(12, 'Sala de Servidores', 'OFICINA', 'Edificio Central', 'Piso 1', 'Centro de datos', 'Vía principal, Ciudad de Panamá', 'Infraestructura crítica y equipos de red.', 1, '2026-07-15 01:01:16'),
(13, 'Sucursal Costa del Este', 'EDIFICIO', 'Sucursal Costa del Este', 'Piso 4', 'Operaciones', 'Costa del Este, Panamá', 'Sucursal operativa de demostración.', 1, '2026-07-15 01:01:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(10) UNSIGNED NOT NULL,
  `cedula` varchar(25) DEFAULT NULL,
  `nombre` varchar(60) NOT NULL,
  `apellido` varchar(60) NOT NULL,
  `usuario` varchar(40) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `idRol` int(10) UNSIGNED NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `intentosFallidos` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `bloqueado` tinyint(1) NOT NULL DEFAULT 0,
  `fechaBloqueo` datetime DEFAULT NULL,
  `ultimoAcceso` datetime DEFAULT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaActualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `cedula`, `nombre`, `apellido`, `usuario`, `correo`, `passwordHash`, `idRol`, `activo`, `intentosFallidos`, `bloqueado`, `fechaBloqueo`, `ultimoAcceso`, `fechaRegistro`, `fechaActualizacion`) VALUES
(1, '8-1025-2381', 'Joseph', 'Cordoba', 'joseph_admin', 'josephcordoba2318@gmail.com', '$2y$10$ttkY6gg4tySDuShSViS/8e2q5Po3MfeQWAMGe.QfZsLV1aP2wxumS', 1, 1, 0, 0, NULL, '2026-07-15 01:01:48', '2026-07-14 14:20:35', '2026-07-15 01:01:48'),
(2, '8-1046-6767', 'Winston', 'Franco', 'wins_09', 'winston@gmail.com', '$2y$10$lYXrmPK2DWTeHLPFL8u/n.ewfm9GGVTk01uWfSyS1cvroIfFfSfui', 2, 1, 0, 0, NULL, '2026-07-15 01:04:07', '2026-07-14 16:00:18', '2026-07-15 01:04:07'),
(3, '8-1030-0070', 'Guillermo', 'Mas', 'guillem_tec', 'guille@gmail.com', '$2y$10$TZZTS9yld0mZ50mbAWr51.JqV1ouxdzT5VIlUbzkt726WidVuvRFi', 3, 1, 0, 0, NULL, '2026-07-14 23:03:48', '2026-07-14 16:15:51', '2026-07-14 23:03:48'),
(7, '8-900-0001', 'Administrador', 'Demo', 'demo_admin', 'demo.admin@trackit.local', '$2y$12$5RcZHQvhMuY47UadRNNsLOCbqgsWgLfZydlxeyuD93Ys.KYDj6CKG', 1, 1, 0, 0, NULL, NULL, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(8, '8-900-0002', 'Técnico', 'Demo', 'demo_tecnico', 'demo.tecnico@trackit.local', '$2y$12$5RcZHQvhMuY47UadRNNsLOCbqgsWgLfZydlxeyuD93Ys.KYDj6CKG', 3, 1, 0, 0, NULL, NULL, '2026-07-15 01:01:16', '2026-07-15 01:01:16'),
(9, '8-900-0003', 'Colaborador', 'Demo', 'demo_colaborador', 'demo.colaborador@trackit.local', '$2y$10$.agtX7LBTkj2TuACjb/ZQ.BA4hGGD9x1RHldxZgJ2DBRJ6j03wtCi', 2, 1, 0, 0, NULL, '2026-07-15 01:05:03', '2026-07-15 01:01:16', '2026-07-15 01:05:03');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vistaactivosconimagenesincompletas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vistaactivosconimagenesincompletas` (
`idActivo` bigint(20) unsigned
,`codigoActivo` varchar(40)
,`nombreProducto` varchar(120)
,`cantidadImagenes` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vistaactivosporcolaborador`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vistaactivosporcolaborador` (
`idColaborador` int(10) unsigned
,`identificacion` varchar(25)
,`nombreColaborador` varchar(121)
,`correo` varchar(120)
,`idAsignacion` bigint(20) unsigned
,`fechaEntrega` datetime
,`idActivo` bigint(20) unsigned
,`codigoActivo` varchar(40)
,`numeroSerie` varchar(120)
,`direccionIP` varchar(45)
,`nombreProducto` varchar(120)
,`marca` varchar(80)
,`modelo` varchar(100)
,`nombreCategoria` varchar(80)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vistaactivosproximosdepreciacion`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vistaactivosproximosdepreciacion` (
`idActivo` bigint(20) unsigned
,`codigoActivo` varchar(40)
,`nombreProducto` varchar(120)
,`marca` varchar(80)
,`modelo` varchar(100)
,`costo` decimal(12,2)
,`fechaAdquisicion` date
,`vidaUtilMesesAplicada` smallint(5) unsigned
,`fechaFinVidaUtil` date
,`diasRestantes` int(7)
,`nombreEstado` varchar(60)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vistaasignacionesactivas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vistaasignacionesactivas` (
`idAsignacion` bigint(20) unsigned
,`fechaEntrega` datetime
,`idActivo` bigint(20) unsigned
,`codigoActivo` varchar(40)
,`numeroSerie` varchar(120)
,`idProducto` int(10) unsigned
,`nombreProducto` varchar(120)
,`marca` varchar(80)
,`modelo` varchar(100)
,`idColaborador` int(10) unsigned
,`identificacion` varchar(25)
,`nombreColaborador` varchar(121)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vistainventariodetalle`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vistainventariodetalle` (
`idActivo` bigint(20) unsigned
,`codigoActivo` varchar(40)
,`numeroSerie` varchar(120)
,`direccionIP` varchar(45)
,`costo` decimal(12,2)
,`fechaAdquisicion` date
,`fechaIngreso` date
,`vidaUtilMesesAplicada` smallint(5) unsigned
,`fechaFinVidaUtil` date
,`imagenPrincipal` varchar(255)
,`cantidadImagenes` bigint(21)
,`qrToken` char(64)
,`codigoEstado` varchar(30)
,`nombreEstado` varchar(60)
,`idProducto` int(10) unsigned
,`nombreProducto` varchar(120)
,`marca` varchar(80)
,`modelo` varchar(100)
,`tipoProducto` enum('HARDWARE','SOFTWARE','LICENCIA')
,`idSubcategoria` int(10) unsigned
,`nombreSubcategoria` varchar(80)
,`idCategoria` int(10) unsigned
,`nombreCategoria` varchar(80)
,`nombreUbicacion` varchar(100)
,`idColaborador` int(10) unsigned
,`nombreColaborador` varchar(121)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vistaresumencategoria`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vistaresumencategoria` (
`idCategoria` int(10) unsigned
,`nombreCategoria` varchar(80)
,`totalActivos` bigint(21)
,`enInventario` decimal(22,0)
,`asignados` decimal(22,0)
,`enRevision` decimal(22,0)
,`enReparacion` decimal(22,0)
,`enDescarte` decimal(22,0)
,`donados` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vistaactivosconimagenesincompletas`
--
DROP TABLE IF EXISTS `vistaactivosconimagenesincompletas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vistaactivosconimagenesincompletas`  AS SELECT `a`.`idActivo` AS `idActivo`, `a`.`codigoActivo` AS `codigoActivo`, `p`.`nombreProducto` AS `nombreProducto`, count(`ia`.`idImagenActivo`) AS `cantidadImagenes` FROM ((`activo` `a` join `producto` `p` on(`p`.`idProducto` = `a`.`idProducto`)) left join `imagenactivo` `ia` on(`ia`.`idActivo` = `a`.`idActivo` and `ia`.`activo` = 1)) WHERE `a`.`activo` = 1 GROUP BY `a`.`idActivo`, `a`.`codigoActivo`, `p`.`nombreProducto` HAVING count(`ia`.`idImagenActivo`) < 2 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vistaactivosporcolaborador`
--
DROP TABLE IF EXISTS `vistaactivosporcolaborador`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vistaactivosporcolaborador`  AS SELECT `c`.`idColaborador` AS `idColaborador`, `c`.`identificacion` AS `identificacion`, concat(`c`.`nombre`,' ',`c`.`apellido`) AS `nombreColaborador`, `c`.`correo` AS `correo`, `aa`.`idAsignacion` AS `idAsignacion`, `aa`.`fechaEntrega` AS `fechaEntrega`, `a`.`idActivo` AS `idActivo`, `a`.`codigoActivo` AS `codigoActivo`, `a`.`numeroSerie` AS `numeroSerie`, `a`.`direccionIP` AS `direccionIP`, `p`.`nombreProducto` AS `nombreProducto`, `p`.`marca` AS `marca`, `p`.`modelo` AS `modelo`, `cat`.`nombreCategoria` AS `nombreCategoria` FROM (((((`colaborador` `c` join `asignacionactivo` `aa` on(`aa`.`idColaborador` = `c`.`idColaborador` and `aa`.`estadoAsignacion` = 'ACTIVA' and `aa`.`fechaDevolucion` is null)) join `activo` `a` on(`a`.`idActivo` = `aa`.`idActivo`)) join `producto` `p` on(`p`.`idProducto` = `a`.`idProducto`)) join `subcategoria` `s` on(`s`.`idSubcategoria` = `p`.`idSubcategoria`)) join `categoria` `cat` on(`cat`.`idCategoria` = `s`.`idCategoria`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vistaactivosproximosdepreciacion`
--
DROP TABLE IF EXISTS `vistaactivosproximosdepreciacion`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vistaactivosproximosdepreciacion`  AS SELECT `a`.`idActivo` AS `idActivo`, `a`.`codigoActivo` AS `codigoActivo`, `p`.`nombreProducto` AS `nombreProducto`, `p`.`marca` AS `marca`, `p`.`modelo` AS `modelo`, `a`.`costo` AS `costo`, `a`.`fechaAdquisicion` AS `fechaAdquisicion`, coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) AS `vidaUtilMesesAplicada`, `a`.`fechaAdquisicion`+ interval coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) month AS `fechaFinVidaUtil`, to_days(`a`.`fechaAdquisicion` + interval coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) month) - to_days(curdate()) AS `diasRestantes`, `ea`.`nombreEstado` AS `nombreEstado` FROM ((`activo` `a` join `producto` `p` on(`p`.`idProducto` = `a`.`idProducto`)) join `estadoactivo` `ea` on(`ea`.`idEstadoActivo` = `a`.`idEstadoActivo`)) WHERE `a`.`activo` = 1 AND `ea`.`codigoEstado` <> 'DONADO' AND coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) is not null ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vistaasignacionesactivas`
--
DROP TABLE IF EXISTS `vistaasignacionesactivas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vistaasignacionesactivas`  AS SELECT `aa`.`idAsignacion` AS `idAsignacion`, `aa`.`fechaEntrega` AS `fechaEntrega`, `a`.`idActivo` AS `idActivo`, `a`.`codigoActivo` AS `codigoActivo`, `a`.`numeroSerie` AS `numeroSerie`, `p`.`idProducto` AS `idProducto`, `p`.`nombreProducto` AS `nombreProducto`, `p`.`marca` AS `marca`, `p`.`modelo` AS `modelo`, `c`.`idColaborador` AS `idColaborador`, `c`.`identificacion` AS `identificacion`, concat(`c`.`nombre`,' ',`c`.`apellido`) AS `nombreColaborador` FROM (((`asignacionactivo` `aa` join `activo` `a` on(`a`.`idActivo` = `aa`.`idActivo`)) join `producto` `p` on(`p`.`idProducto` = `a`.`idProducto`)) join `colaborador` `c` on(`c`.`idColaborador` = `aa`.`idColaborador`)) WHERE `aa`.`estadoAsignacion` = 'ACTIVA' AND `aa`.`fechaDevolucion` is null ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vistainventariodetalle`
--
DROP TABLE IF EXISTS `vistainventariodetalle`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vistainventariodetalle`  AS SELECT `a`.`idActivo` AS `idActivo`, `a`.`codigoActivo` AS `codigoActivo`, `a`.`numeroSerie` AS `numeroSerie`, `a`.`direccionIP` AS `direccionIP`, `a`.`costo` AS `costo`, `a`.`fechaAdquisicion` AS `fechaAdquisicion`, `a`.`fechaIngreso` AS `fechaIngreso`, coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) AS `vidaUtilMesesAplicada`, CASE WHEN coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) is null THEN NULL ELSE `a`.`fechaAdquisicion`+ interval coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) month END AS `fechaFinVidaUtil`, (select `ia`.`rutaImagen` from `imagenactivo` `ia` where `ia`.`idActivo` = `a`.`idActivo` and `ia`.`activo` = 1 order by `ia`.`esPrincipal` desc,`ia`.`ordenVisual`,`ia`.`idImagenActivo` limit 1) AS `imagenPrincipal`, (select count(0) from `imagenactivo` `ia2` where `ia2`.`idActivo` = `a`.`idActivo` and `ia2`.`activo` = 1) AS `cantidadImagenes`, `a`.`qrToken` AS `qrToken`, `ea`.`codigoEstado` AS `codigoEstado`, `ea`.`nombreEstado` AS `nombreEstado`, `p`.`idProducto` AS `idProducto`, `p`.`nombreProducto` AS `nombreProducto`, `p`.`marca` AS `marca`, `p`.`modelo` AS `modelo`, `p`.`tipoProducto` AS `tipoProducto`, `s`.`idSubcategoria` AS `idSubcategoria`, `s`.`nombreSubcategoria` AS `nombreSubcategoria`, `cat`.`idCategoria` AS `idCategoria`, `cat`.`nombreCategoria` AS `nombreCategoria`, `u`.`nombreUbicacion` AS `nombreUbicacion`, `va`.`idColaborador` AS `idColaborador`, `va`.`nombreColaborador` AS `nombreColaborador` FROM ((((((`activo` `a` join `producto` `p` on(`p`.`idProducto` = `a`.`idProducto`)) join `subcategoria` `s` on(`s`.`idSubcategoria` = `p`.`idSubcategoria`)) join `categoria` `cat` on(`cat`.`idCategoria` = `s`.`idCategoria`)) join `estadoactivo` `ea` on(`ea`.`idEstadoActivo` = `a`.`idEstadoActivo`)) left join `ubicacion` `u` on(`u`.`idUbicacion` = `a`.`idUbicacion`)) left join `vistaasignacionesactivas` `va` on(`va`.`idActivo` = `a`.`idActivo`)) WHERE `a`.`activo` = 1 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vistaresumencategoria`
--
DROP TABLE IF EXISTS `vistaresumencategoria`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vistaresumencategoria`  AS SELECT `cat`.`idCategoria` AS `idCategoria`, `cat`.`nombreCategoria` AS `nombreCategoria`, count(`a`.`idActivo`) AS `totalActivos`, sum(case when `ea`.`codigoEstado` = 'EN_INVENTARIO' then 1 else 0 end) AS `enInventario`, sum(case when `ea`.`codigoEstado` = 'ASIGNADO' then 1 else 0 end) AS `asignados`, sum(case when `ea`.`codigoEstado` = 'REVISION_TECNICA' then 1 else 0 end) AS `enRevision`, sum(case when `ea`.`codigoEstado` = 'EN_REPARACION' then 1 else 0 end) AS `enReparacion`, sum(case when `ea`.`codigoEstado` = 'DESCARTE' then 1 else 0 end) AS `enDescarte`, sum(case when `ea`.`codigoEstado` = 'DONADO' then 1 else 0 end) AS `donados` FROM ((((`categoria` `cat` left join `subcategoria` `s` on(`s`.`idCategoria` = `cat`.`idCategoria`)) left join `producto` `p` on(`p`.`idSubcategoria` = `s`.`idSubcategoria`)) left join `activo` `a` on(`a`.`idProducto` = `p`.`idProducto` and `a`.`activo` = 1)) left join `estadoactivo` `ea` on(`ea`.`idEstadoActivo` = `a`.`idEstadoActivo`)) GROUP BY `cat`.`idCategoria`, `cat`.`nombreCategoria` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activo`
--
ALTER TABLE `activo`
  ADD PRIMARY KEY (`idActivo`),
  ADD UNIQUE KEY `uq_activo_codigo` (`codigoActivo`),
  ADD UNIQUE KEY `uq_activo_qr_token` (`qrToken`),
  ADD UNIQUE KEY `uq_activo_serie` (`numeroSerie`),
  ADD KEY `fk_activo_estado` (`idEstadoActivo`),
  ADD KEY `fk_activo_ubicacion` (`idUbicacion`),
  ADD KEY `idx_activo_producto_estado` (`idProducto`,`idEstadoActivo`,`activo`),
  ADD KEY `idx_activo_depreciacion` (`fechaAdquisicion`,`vidaUtilMeses`),
  ADD KEY `idx_activo_ip` (`direccionIP`);

--
-- Indices de la tabla `asignacionactivo`
--
ALTER TABLE `asignacionactivo`
  ADD PRIMARY KEY (`idAsignacion`),
  ADD KEY `fk_asignacion_usuario_entrega` (`usuarioEntrega`),
  ADD KEY `idx_asignacion_activo_estado` (`idActivo`,`estadoAsignacion`,`fechaDevolucion`),
  ADD KEY `idx_asignacion_colaborador_estado` (`idColaborador`,`estadoAsignacion`);

--
-- Indices de la tabla `asignacionlicencia`
--
ALTER TABLE `asignacionlicencia`
  ADD PRIMARY KEY (`idAsignacionLicencia`),
  ADD KEY `idx_asignacion_licencia_estado` (`idLicencia`,`estadoAsignacion`,`fechaRevocacion`),
  ADD KEY `idx_asignacion_licencia_colaborador` (`idColaborador`,`estadoAsignacion`),
  ADD KEY `fk_asignacion_licencia_usuario` (`idUsuarioAsigna`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`idAuditoria`),
  ADD KEY `fk_auditoria_llave` (`idLlavePublica`),
  ADD KEY `idx_auditoria_usuario_fecha` (`idUsuario`,`fecha`),
  ADD KEY `idx_auditoria_tabla_registro` (`tablaAfectada`,`idRegistro`);

--
-- Indices de la tabla `bajaactivo`
--
ALTER TABLE `bajaactivo`
  ADD PRIMARY KEY (`idBaja`),
  ADD UNIQUE KEY `uq_baja_activo` (`idActivo`),
  ADD KEY `fk_baja_tipo` (`idTipoBaja`),
  ADD KEY `fk_baja_usuario` (`idUsuario`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`idCategoria`),
  ADD UNIQUE KEY `uq_categoria_nombre` (`nombreCategoria`);

--
-- Indices de la tabla `colaborador`
--
ALTER TABLE `colaborador`
  ADD PRIMARY KEY (`idColaborador`),
  ADD UNIQUE KEY `uq_colaborador_identificacion` (`identificacion`),
  ADD UNIQUE KEY `uq_colaborador_correo` (`correo`),
  ADD UNIQUE KEY `uq_colaborador_usuario` (`idUsuario`),
  ADD KEY `idx_colaborador_nombre` (`apellido`,`nombre`),
  ADD KEY `idx_colaborador_activo` (`activo`);

--
-- Indices de la tabla `colaboradorubicacion`
--
ALTER TABLE `colaboradorubicacion`
  ADD PRIMARY KEY (`idColaboradorUbicacion`),
  ADD KEY `fk_colab_ubicacion_ubicacion` (`idUbicacion`),
  ADD KEY `idx_colab_ubicacion_actual` (`idColaborador`,`esActual`,`fechaFin`);

--
-- Indices de la tabla `devolucionactivo`
--
ALTER TABLE `devolucionactivo`
  ADD PRIMARY KEY (`idDevolucion`),
  ADD UNIQUE KEY `uq_devolucion_asignacion` (`idAsignacion`),
  ADD KEY `fk_devolucion_usuario_recibe` (`usuarioRecibe`),
  ADD KEY `fk_devolucion_motivo` (`idMotivoDevolucion`);

--
-- Indices de la tabla `estadoactivo`
--
ALTER TABLE `estadoactivo`
  ADD PRIMARY KEY (`idEstadoActivo`),
  ADD UNIQUE KEY `uq_estado_activo_codigo` (`codigoEstado`),
  ADD UNIQUE KEY `uq_estado_activo_nombre` (`nombreEstado`);

--
-- Indices de la tabla `estadoreparacion`
--
ALTER TABLE `estadoreparacion`
  ADD PRIMARY KEY (`idEstadoReparacion`),
  ADD UNIQUE KEY `uq_estado_reparacion` (`nombreEstado`);

--
-- Indices de la tabla `estadosolicitud`
--
ALTER TABLE `estadosolicitud`
  ADD PRIMARY KEY (`idEstadoSolicitud`),
  ADD UNIQUE KEY `uq_estado_solicitud` (`nombreEstado`);

--
-- Indices de la tabla `historial_login`
--
ALTER TABLE `historial_login`
  ADD PRIMARY KEY (`idHistorialLogin`),
  ADD KEY `idx_historial_login_usuario_fecha` (`idUsuario`,`fechaIntento`),
  ADD KEY `idx_historial_login_ip_fecha` (`direccionIP`,`fechaIntento`);

--
-- Indices de la tabla `imagenactivo`
--
ALTER TABLE `imagenactivo`
  ADD PRIMARY KEY (`idImagenActivo`),
  ADD UNIQUE KEY `uq_imagen_activo_ruta` (`rutaImagen`),
  ADD UNIQUE KEY `uq_imagen_activo_orden` (`idActivo`,`ordenVisual`),
  ADD KEY `idx_imagen_activo_principal` (`idActivo`,`esPrincipal`,`activo`);

--
-- Indices de la tabla `licenciasoftware`
--
ALTER TABLE `licenciasoftware`
  ADD PRIMARY KEY (`idLicencia`),
  ADD UNIQUE KEY `uq_licencia_activo` (`idActivo`),
  ADD KEY `idx_licencia_expiracion` (`fechaExpiracion`);

--
-- Indices de la tabla `llavepublicausuario`
--
ALTER TABLE `llavepublicausuario`
  ADD PRIMARY KEY (`idLlavePublica`),
  ADD UNIQUE KEY `uq_llave_huella` (`huellaDigital`),
  ADD UNIQUE KEY `uq_llave_usuario_version` (`idUsuario`,`versionLlave`);

--
-- Indices de la tabla `motivodevolucion`
--
ALTER TABLE `motivodevolucion`
  ADD PRIMARY KEY (`idMotivoDevolucion`),
  ADD UNIQUE KEY `uq_motivo_devolucion` (`nombreMotivo`);

--
-- Indices de la tabla `movimientoactivo`
--
ALTER TABLE `movimientoactivo`
  ADD PRIMARY KEY (`idMovimiento`),
  ADD KEY `fk_movimiento_usuario` (`idUsuario`),
  ADD KEY `fk_movimiento_estado_anterior` (`idEstadoAnterior`),
  ADD KEY `fk_movimiento_estado_nuevo` (`idEstadoNuevo`),
  ADD KEY `fk_movimiento_ubicacion_anterior` (`idUbicacionAnterior`),
  ADD KEY `fk_movimiento_ubicacion_nueva` (`idUbicacionNueva`),
  ADD KEY `idx_movimiento_activo_fecha` (`idActivo`,`fechaMovimiento`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`idProducto`),
  ADD UNIQUE KEY `uq_producto_modelo` (`idSubcategoria`,`nombreProducto`,`marca`,`modelo`),
  ADD KEY `idx_producto_subcategoria_activo` (`idSubcategoria`,`activo`);

--
-- Indices de la tabla `reparacion`
--
ALTER TABLE `reparacion`
  ADD PRIMARY KEY (`idReparacion`),
  ADD KEY `fk_reparacion_tecnico` (`idTecnico`),
  ADD KEY `fk_reparacion_estado` (`idEstadoReparacion`),
  ADD KEY `idx_reparacion_activo_estado` (`idActivo`,`idEstadoReparacion`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idRol`),
  ADD UNIQUE KEY `uq_rol_nombre` (`nombreRol`);

--
-- Indices de la tabla `solicitudnecesidad`
--
ALTER TABLE `solicitudnecesidad`
  ADD PRIMARY KEY (`idSolicitud`),
  ADD KEY `fk_solicitud_colaborador` (`idColaborador`),
  ADD KEY `fk_solicitud_subcategoria` (`idSubcategoria`),
  ADD KEY `fk_solicitud_producto` (`idProducto`),
  ADD KEY `fk_solicitud_usuario_revisa` (`usuarioRevisa`),
  ADD KEY `idx_solicitud_estado_fecha` (`idEstadoSolicitud`,`fechaSolicitud`),
  ADD KEY `idx_solicitud_presupuesto` (`anioPresupuestado`,`periodoNecesidad`);

--
-- Indices de la tabla `solicitudreparacion`
--
ALTER TABLE `solicitudreparacion`
  ADD PRIMARY KEY (`idSolicitudReparacion`),
  ADD UNIQUE KEY `uq_solicitud_reparacion_reparacion` (`idReparacion`),
  ADD KEY `fk_solicitud_reparacion_colaborador` (`idColaborador`),
  ADD KEY `fk_solicitud_reparacion_ubicacion` (`idUbicacionSolicitud`),
  ADD KEY `fk_solicitud_reparacion_usuario_revisa` (`usuarioRevisa`),
  ADD KEY `idx_solicitud_reparacion_estado_fecha` (`estadoSolicitud`,`prioridad`,`fechaSolicitud`),
  ADD KEY `idx_solicitud_reparacion_activo_estado` (`idActivo`,`estadoSolicitud`),
  ADD KEY `idx_solicitud_reparacion_tecnico_estado` (`idTecnico`,`estadoSolicitud`);

--
-- Indices de la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  ADD PRIMARY KEY (`idSubcategoria`),
  ADD UNIQUE KEY `uq_subcategoria_categoria_nombre` (`idCategoria`,`nombreSubcategoria`);

--
-- Indices de la tabla `tipobaja`
--
ALTER TABLE `tipobaja`
  ADD PRIMARY KEY (`idTipoBaja`),
  ADD UNIQUE KEY `uq_tipo_baja_codigo` (`codigoTipo`),
  ADD UNIQUE KEY `uq_tipo_baja_nombre` (`nombreTipo`);

--
-- Indices de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD PRIMARY KEY (`idUbicacion`),
  ADD UNIQUE KEY `uq_ubicacion_nombre` (`nombreUbicacion`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`),
  ADD UNIQUE KEY `uq_usuario_usuario` (`usuario`),
  ADD UNIQUE KEY `uq_usuario_correo` (`correo`),
  ADD UNIQUE KEY `uq_usuario_cedula` (`cedula`),
  ADD KEY `idx_usuario_rol_activo` (`idRol`,`activo`,`bloqueado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activo`
--
ALTER TABLE `activo`
  MODIFY `idActivo` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `asignacionactivo`
--
ALTER TABLE `asignacionactivo`
  MODIFY `idAsignacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `asignacionlicencia`
--
ALTER TABLE `asignacionlicencia`
  MODIFY `idAsignacionLicencia` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `idAuditoria` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `bajaactivo`
--
ALTER TABLE `bajaactivo`
  MODIFY `idBaja` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `idCategoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `colaborador`
--
ALTER TABLE `colaborador`
  MODIFY `idColaborador` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `colaboradorubicacion`
--
ALTER TABLE `colaboradorubicacion`
  MODIFY `idColaboradorUbicacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `devolucionactivo`
--
ALTER TABLE `devolucionactivo`
  MODIFY `idDevolucion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `estadoactivo`
--
ALTER TABLE `estadoactivo`
  MODIFY `idEstadoActivo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `estadoreparacion`
--
ALTER TABLE `estadoreparacion`
  MODIFY `idEstadoReparacion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `estadosolicitud`
--
ALTER TABLE `estadosolicitud`
  MODIFY `idEstadoSolicitud` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `historial_login`
--
ALTER TABLE `historial_login`
  MODIFY `idHistorialLogin` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT de la tabla `imagenactivo`
--
ALTER TABLE `imagenactivo`
  MODIFY `idImagenActivo` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `licenciasoftware`
--
ALTER TABLE `licenciasoftware`
  MODIFY `idLicencia` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `llavepublicausuario`
--
ALTER TABLE `llavepublicausuario`
  MODIFY `idLlavePublica` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `motivodevolucion`
--
ALTER TABLE `motivodevolucion`
  MODIFY `idMotivoDevolucion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `movimientoactivo`
--
ALTER TABLE `movimientoactivo`
  MODIFY `idMovimiento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `reparacion`
--
ALTER TABLE `reparacion`
  MODIFY `idReparacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idRol` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudnecesidad`
--
ALTER TABLE `solicitudnecesidad`
  MODIFY `idSolicitud` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudreparacion`
--
ALTER TABLE `solicitudreparacion`
  MODIFY `idSolicitudReparacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  MODIFY `idSubcategoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `tipobaja`
--
ALTER TABLE `tipobaja`
  MODIFY `idTipoBaja` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  MODIFY `idUbicacion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `activo`
--
ALTER TABLE `activo`
  ADD CONSTRAINT `fk_activo_estado` FOREIGN KEY (`idEstadoActivo`) REFERENCES `estadoactivo` (`idEstadoActivo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_activo_producto` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_activo_ubicacion` FOREIGN KEY (`idUbicacion`) REFERENCES `ubicacion` (`idUbicacion`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `asignacionactivo`
--
ALTER TABLE `asignacionactivo`
  ADD CONSTRAINT `fk_asignacion_activo` FOREIGN KEY (`idActivo`) REFERENCES `activo` (`idActivo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_asignacion_colaborador` FOREIGN KEY (`idColaborador`) REFERENCES `colaborador` (`idColaborador`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_asignacion_usuario_entrega` FOREIGN KEY (`usuarioEntrega`) REFERENCES `usuario` (`idUsuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `asignacionlicencia`
--
ALTER TABLE `asignacionlicencia`
  ADD CONSTRAINT `fk_asignacion_licencia_colaborador` FOREIGN KEY (`idColaborador`) REFERENCES `colaborador` (`idColaborador`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_asignacion_licencia_licencia` FOREIGN KEY (`idLicencia`) REFERENCES `licenciasoftware` (`idLicencia`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_asignacion_licencia_usuario` FOREIGN KEY (`idUsuarioAsigna`) REFERENCES `usuario` (`idUsuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `fk_auditoria_llave` FOREIGN KEY (`idLlavePublica`) REFERENCES `llavepublicausuario` (`idLlavePublica`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `bajaactivo`
--
ALTER TABLE `bajaactivo`
  ADD CONSTRAINT `fk_baja_activo` FOREIGN KEY (`idActivo`) REFERENCES `activo` (`idActivo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_baja_tipo` FOREIGN KEY (`idTipoBaja`) REFERENCES `tipobaja` (`idTipoBaja`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_baja_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `colaborador`
--
ALTER TABLE `colaborador`
  ADD CONSTRAINT `fk_colaborador_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `colaboradorubicacion`
--
ALTER TABLE `colaboradorubicacion`
  ADD CONSTRAINT `fk_colab_ubicacion_colaborador` FOREIGN KEY (`idColaborador`) REFERENCES `colaborador` (`idColaborador`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_colab_ubicacion_ubicacion` FOREIGN KEY (`idUbicacion`) REFERENCES `ubicacion` (`idUbicacion`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `devolucionactivo`
--
ALTER TABLE `devolucionactivo`
  ADD CONSTRAINT `fk_devolucion_asignacion` FOREIGN KEY (`idAsignacion`) REFERENCES `asignacionactivo` (`idAsignacion`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_devolucion_motivo` FOREIGN KEY (`idMotivoDevolucion`) REFERENCES `motivodevolucion` (`idMotivoDevolucion`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_devolucion_usuario_recibe` FOREIGN KEY (`usuarioRecibe`) REFERENCES `usuario` (`idUsuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `historial_login`
--
ALTER TABLE `historial_login`
  ADD CONSTRAINT `fk_historial_login_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `imagenactivo`
--
ALTER TABLE `imagenactivo`
  ADD CONSTRAINT `fk_imagen_activo_activo` FOREIGN KEY (`idActivo`) REFERENCES `activo` (`idActivo`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `licenciasoftware`
--
ALTER TABLE `licenciasoftware`
  ADD CONSTRAINT `fk_licencia_activo` FOREIGN KEY (`idActivo`) REFERENCES `activo` (`idActivo`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `llavepublicausuario`
--
ALTER TABLE `llavepublicausuario`
  ADD CONSTRAINT `fk_llave_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientoactivo`
--
ALTER TABLE `movimientoactivo`
  ADD CONSTRAINT `fk_movimiento_activo` FOREIGN KEY (`idActivo`) REFERENCES `activo` (`idActivo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_movimiento_estado_anterior` FOREIGN KEY (`idEstadoAnterior`) REFERENCES `estadoactivo` (`idEstadoActivo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_movimiento_estado_nuevo` FOREIGN KEY (`idEstadoNuevo`) REFERENCES `estadoactivo` (`idEstadoActivo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_movimiento_ubicacion_anterior` FOREIGN KEY (`idUbicacionAnterior`) REFERENCES `ubicacion` (`idUbicacion`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_movimiento_ubicacion_nueva` FOREIGN KEY (`idUbicacionNueva`) REFERENCES `ubicacion` (`idUbicacion`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_movimiento_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `fk_producto_subcategoria` FOREIGN KEY (`idSubcategoria`) REFERENCES `subcategoria` (`idSubcategoria`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `reparacion`
--
ALTER TABLE `reparacion`
  ADD CONSTRAINT `fk_reparacion_activo` FOREIGN KEY (`idActivo`) REFERENCES `activo` (`idActivo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reparacion_estado` FOREIGN KEY (`idEstadoReparacion`) REFERENCES `estadoreparacion` (`idEstadoReparacion`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reparacion_tecnico` FOREIGN KEY (`idTecnico`) REFERENCES `usuario` (`idUsuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudnecesidad`
--
ALTER TABLE `solicitudnecesidad`
  ADD CONSTRAINT `fk_solicitud_colaborador` FOREIGN KEY (`idColaborador`) REFERENCES `colaborador` (`idColaborador`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_estado` FOREIGN KEY (`idEstadoSolicitud`) REFERENCES `estadosolicitud` (`idEstadoSolicitud`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_producto` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_subcategoria` FOREIGN KEY (`idSubcategoria`) REFERENCES `subcategoria` (`idSubcategoria`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_usuario_revisa` FOREIGN KEY (`usuarioRevisa`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudreparacion`
--
ALTER TABLE `solicitudreparacion`
  ADD CONSTRAINT `fk_solicitud_reparacion_activo` FOREIGN KEY (`idActivo`) REFERENCES `activo` (`idActivo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_reparacion_colaborador` FOREIGN KEY (`idColaborador`) REFERENCES `colaborador` (`idColaborador`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_reparacion_reparacion` FOREIGN KEY (`idReparacion`) REFERENCES `reparacion` (`idReparacion`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_reparacion_tecnico` FOREIGN KEY (`idTecnico`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_reparacion_ubicacion` FOREIGN KEY (`idUbicacionSolicitud`) REFERENCES `ubicacion` (`idUbicacion`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitud_reparacion_usuario_revisa` FOREIGN KEY (`usuarioRevisa`) REFERENCES `usuario` (`idUsuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  ADD CONSTRAINT `fk_subcategoria_categoria` FOREIGN KEY (`idCategoria`) REFERENCES `categoria` (`idCategoria`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`idRol`) REFERENCES `rol` (`idRol`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
