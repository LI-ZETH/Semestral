-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-07-2026 a las 01:58:07
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
CREATE DATABASE IF NOT EXISTS `inventario` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `inventario`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activo`
--

DROP TABLE IF EXISTS `activo`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacionactivo`
--

DROP TABLE IF EXISTS `asignacionactivo`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bajaactivo`
--

DROP TABLE IF EXISTS `bajaactivo`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

DROP TABLE IF EXISTS `categoria`;
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
(4, 'Equipo de Cómputo', 'Laptops, desktops, servidores y estaciones de trabajo.', 'uploads/categorias/80c6d0f6e23953e37239793bfca707c7.jpg', 'contain', 'mediana', 1, '2026-07-14 12:10:17', '2026-07-14 17:42:13'),
(5, 'Equipo de Telefonía', 'Teléfonos IP, celulares y equipos de comunicación.', NULL, 'cover', 'mediana', 1, '2026-07-14 12:10:17', '2026-07-14 12:10:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colaborador`
--

DROP TABLE IF EXISTS `colaborador`;
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
(1, 2, '8-1046-6767', 'Winston', 'Franco', 'winston@gmail.com', '6779-0893', NULL, 'Desarrollador de Software', 'Tecnologia', 1, '2026-07-14', NULL, '2026-07-14 16:00:18', '2026-07-14 16:00:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colaboradorubicacion`
--

DROP TABLE IF EXISTS `colaboradorubicacion`;
CREATE TABLE `colaboradorubicacion` (
  `idColaboradorUbicacion` bigint(20) UNSIGNED NOT NULL,
  `idColaborador` int(10) UNSIGNED NOT NULL,
  `idUbicacion` int(10) UNSIGNED NOT NULL,
  `fechaInicio` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaFin` datetime DEFAULT NULL,
  `esActual` tinyint(1) NOT NULL DEFAULT 1,
  `observaciones` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucionactivo`
--

DROP TABLE IF EXISTS `devolucionactivo`;
CREATE TABLE `devolucionactivo` (
  `idDevolucion` bigint(20) UNSIGNED NOT NULL,
  `idAsignacion` bigint(20) UNSIGNED NOT NULL,
  `usuarioRecibe` int(10) UNSIGNED NOT NULL,
  `idMotivoDevolucion` int(10) UNSIGNED NOT NULL,
  `condicionRecepcion` enum('BUENO','DANADO','INCOMPLETO','NO_VERIFICADO') NOT NULL DEFAULT 'NO_VERIFICADO',
  `observaciones` text DEFAULT NULL,
  `fechaRecepcion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadoactivo`
--

DROP TABLE IF EXISTS `estadoactivo`;
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

DROP TABLE IF EXISTS `estadoreparacion`;
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

DROP TABLE IF EXISTS `estadosolicitud`;
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

DROP TABLE IF EXISTS `historial_login`;
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
(42, 3, 'Guillem_Tec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 'Inicio de sesión correcto.', '2026-07-14 18:17:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenactivo`
--

DROP TABLE IF EXISTS `imagenactivo`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `licenciasoftware`
--

DROP TABLE IF EXISTS `licenciasoftware`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llavepublicausuario`
--

DROP TABLE IF EXISTS `llavepublicausuario`;
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

DROP TABLE IF EXISTS `motivodevolucion`;
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

DROP TABLE IF EXISTS `movimientoactivo`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

DROP TABLE IF EXISTS `producto`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reparacion`
--

DROP TABLE IF EXISTS `reparacion`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

DROP TABLE IF EXISTS `rol`;
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

DROP TABLE IF EXISTS `solicitudnecesidad`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategoria`
--

DROP TABLE IF EXISTS `subcategoria`;
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
(11, 4, 'Laptop', 'Computadoras portátiles.', NULL, 1, '2026-07-14 12:10:17'),
(12, 4, 'Desktop', 'Computadoras de escritorio.', NULL, 1, '2026-07-14 12:10:17'),
(13, 4, 'Servidor', 'Servidores físicos.', NULL, 1, '2026-07-14 12:10:17'),
(14, 5, 'Teléfono IP', 'Teléfonos de voz sobre IP.', NULL, 1, '2026-07-14 12:10:17'),
(15, 5, 'Teléfono móvil', 'Teléfonos inteligentes institucionales.', NULL, 1, '2026-07-14 12:10:17'),
(16, 4, 'Workstations', 'Equipos optimizados para tareas pesadas como diseño 3D, arquitectura o edición de video.', 'uploads/subcategorias/0de5053ae1e2359a8eddefb6f4cb9a64.jpg', 1, '2026-07-14 18:16:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipobaja`
--

DROP TABLE IF EXISTS `tipobaja`;
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

DROP TABLE IF EXISTS `ubicacion`;
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
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
(1, '8-1025-2381', 'Joseph', 'Cordoba', 'joseph_admin', 'josephcordoba2318@gmail.com', '$2y$10$ttkY6gg4tySDuShSViS/8e2q5Po3MfeQWAMGe.QfZsLV1aP2wxumS', 1, 1, 0, 0, NULL, '2026-07-14 18:17:18', '2026-07-14 14:20:35', '2026-07-14 18:17:18'),
(2, '8-1046-6767', 'Winston', 'Franco', 'wins_09', 'winston@gmail.com', '$2y$10$lYXrmPK2DWTeHLPFL8u/n.ewfm9GGVTk01uWfSyS1cvroIfFfSfui', 2, 1, 0, 0, NULL, '2026-07-14 17:49:17', '2026-07-14 16:00:18', '2026-07-14 17:49:17'),
(3, '8-1030-0070', 'Guillermo', 'Mas', 'guillem_tec', 'guille@gmail.com', '$2y$10$TZZTS9yld0mZ50mbAWr51.JqV1ouxdzT5VIlUbzkt726WidVuvRFi', 3, 1, 0, 0, NULL, '2026-07-14 18:17:28', '2026-07-14 16:15:51', '2026-07-14 18:17:28');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vistaactivosconimagenesincompletas`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `vistaactivosconimagenesincompletas`;
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
DROP VIEW IF EXISTS `vistaactivosporcolaborador`;
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
DROP VIEW IF EXISTS `vistaactivosproximosdepreciacion`;
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
DROP VIEW IF EXISTS `vistaasignacionesactivas`;
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
DROP VIEW IF EXISTS `vistainventariodetalle`;
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
DROP VIEW IF EXISTS `vistaresumencategoria`;
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

DROP VIEW IF EXISTS `vistaactivosconimagenesincompletas`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vistaactivosconimagenesincompletas`  AS SELECT `a`.`idActivo` AS `idActivo`, `a`.`codigoActivo` AS `codigoActivo`, `p`.`nombreProducto` AS `nombreProducto`, count(`ia`.`idImagenActivo`) AS `cantidadImagenes` FROM ((`activo` `a` join `producto` `p` on(`p`.`idProducto` = `a`.`idProducto`)) left join `imagenactivo` `ia` on(`ia`.`idActivo` = `a`.`idActivo` and `ia`.`activo` = 1)) WHERE `a`.`activo` = 1 GROUP BY `a`.`idActivo`, `a`.`codigoActivo`, `p`.`nombreProducto` HAVING count(`ia`.`idImagenActivo`) < 2 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vistaactivosporcolaborador`
--
DROP TABLE IF EXISTS `vistaactivosporcolaborador`;

DROP VIEW IF EXISTS `vistaactivosporcolaborador`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vistaactivosporcolaborador`  AS SELECT `c`.`idColaborador` AS `idColaborador`, `c`.`identificacion` AS `identificacion`, concat(`c`.`nombre`,' ',`c`.`apellido`) AS `nombreColaborador`, `c`.`correo` AS `correo`, `aa`.`idAsignacion` AS `idAsignacion`, `aa`.`fechaEntrega` AS `fechaEntrega`, `a`.`idActivo` AS `idActivo`, `a`.`codigoActivo` AS `codigoActivo`, `a`.`numeroSerie` AS `numeroSerie`, `a`.`direccionIP` AS `direccionIP`, `p`.`nombreProducto` AS `nombreProducto`, `p`.`marca` AS `marca`, `p`.`modelo` AS `modelo`, `cat`.`nombreCategoria` AS `nombreCategoria` FROM (((((`colaborador` `c` join `asignacionactivo` `aa` on(`aa`.`idColaborador` = `c`.`idColaborador` and `aa`.`estadoAsignacion` = 'ACTIVA' and `aa`.`fechaDevolucion` is null)) join `activo` `a` on(`a`.`idActivo` = `aa`.`idActivo`)) join `producto` `p` on(`p`.`idProducto` = `a`.`idProducto`)) join `subcategoria` `s` on(`s`.`idSubcategoria` = `p`.`idSubcategoria`)) join `categoria` `cat` on(`cat`.`idCategoria` = `s`.`idCategoria`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vistaactivosproximosdepreciacion`
--
DROP TABLE IF EXISTS `vistaactivosproximosdepreciacion`;

DROP VIEW IF EXISTS `vistaactivosproximosdepreciacion`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vistaactivosproximosdepreciacion`  AS SELECT `a`.`idActivo` AS `idActivo`, `a`.`codigoActivo` AS `codigoActivo`, `p`.`nombreProducto` AS `nombreProducto`, `p`.`marca` AS `marca`, `p`.`modelo` AS `modelo`, `a`.`costo` AS `costo`, `a`.`fechaAdquisicion` AS `fechaAdquisicion`, coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) AS `vidaUtilMesesAplicada`, `a`.`fechaAdquisicion`+ interval coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) month AS `fechaFinVidaUtil`, to_days(`a`.`fechaAdquisicion` + interval coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) month) - to_days(curdate()) AS `diasRestantes`, `ea`.`nombreEstado` AS `nombreEstado` FROM ((`activo` `a` join `producto` `p` on(`p`.`idProducto` = `a`.`idProducto`)) join `estadoactivo` `ea` on(`ea`.`idEstadoActivo` = `a`.`idEstadoActivo`)) WHERE `a`.`activo` = 1 AND `ea`.`codigoEstado` <> 'DONADO' AND coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) is not null ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vistaasignacionesactivas`
--
DROP TABLE IF EXISTS `vistaasignacionesactivas`;

DROP VIEW IF EXISTS `vistaasignacionesactivas`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vistaasignacionesactivas`  AS SELECT `aa`.`idAsignacion` AS `idAsignacion`, `aa`.`fechaEntrega` AS `fechaEntrega`, `a`.`idActivo` AS `idActivo`, `a`.`codigoActivo` AS `codigoActivo`, `a`.`numeroSerie` AS `numeroSerie`, `p`.`idProducto` AS `idProducto`, `p`.`nombreProducto` AS `nombreProducto`, `p`.`marca` AS `marca`, `p`.`modelo` AS `modelo`, `c`.`idColaborador` AS `idColaborador`, `c`.`identificacion` AS `identificacion`, concat(`c`.`nombre`,' ',`c`.`apellido`) AS `nombreColaborador` FROM (((`asignacionactivo` `aa` join `activo` `a` on(`a`.`idActivo` = `aa`.`idActivo`)) join `producto` `p` on(`p`.`idProducto` = `a`.`idProducto`)) join `colaborador` `c` on(`c`.`idColaborador` = `aa`.`idColaborador`)) WHERE `aa`.`estadoAsignacion` = 'ACTIVA' AND `aa`.`fechaDevolucion` is null ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vistainventariodetalle`
--
DROP TABLE IF EXISTS `vistainventariodetalle`;

DROP VIEW IF EXISTS `vistainventariodetalle`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vistainventariodetalle`  AS SELECT `a`.`idActivo` AS `idActivo`, `a`.`codigoActivo` AS `codigoActivo`, `a`.`numeroSerie` AS `numeroSerie`, `a`.`direccionIP` AS `direccionIP`, `a`.`costo` AS `costo`, `a`.`fechaAdquisicion` AS `fechaAdquisicion`, `a`.`fechaIngreso` AS `fechaIngreso`, coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) AS `vidaUtilMesesAplicada`, CASE WHEN coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) is null THEN NULL ELSE `a`.`fechaAdquisicion`+ interval coalesce(`a`.`vidaUtilMeses`,`p`.`vidaUtilMeses`) month END AS `fechaFinVidaUtil`, (select `ia`.`rutaImagen` from `imagenactivo` `ia` where `ia`.`idActivo` = `a`.`idActivo` and `ia`.`activo` = 1 order by `ia`.`esPrincipal` desc,`ia`.`ordenVisual`,`ia`.`idImagenActivo` limit 1) AS `imagenPrincipal`, (select count(0) from `imagenactivo` `ia2` where `ia2`.`idActivo` = `a`.`idActivo` and `ia2`.`activo` = 1) AS `cantidadImagenes`, `a`.`qrToken` AS `qrToken`, `ea`.`codigoEstado` AS `codigoEstado`, `ea`.`nombreEstado` AS `nombreEstado`, `p`.`idProducto` AS `idProducto`, `p`.`nombreProducto` AS `nombreProducto`, `p`.`marca` AS `marca`, `p`.`modelo` AS `modelo`, `p`.`tipoProducto` AS `tipoProducto`, `s`.`idSubcategoria` AS `idSubcategoria`, `s`.`nombreSubcategoria` AS `nombreSubcategoria`, `cat`.`idCategoria` AS `idCategoria`, `cat`.`nombreCategoria` AS `nombreCategoria`, `u`.`nombreUbicacion` AS `nombreUbicacion`, `va`.`idColaborador` AS `idColaborador`, `va`.`nombreColaborador` AS `nombreColaborador` FROM ((((((`activo` `a` join `producto` `p` on(`p`.`idProducto` = `a`.`idProducto`)) join `subcategoria` `s` on(`s`.`idSubcategoria` = `p`.`idSubcategoria`)) join `categoria` `cat` on(`cat`.`idCategoria` = `s`.`idCategoria`)) join `estadoactivo` `ea` on(`ea`.`idEstadoActivo` = `a`.`idEstadoActivo`)) left join `ubicacion` `u` on(`u`.`idUbicacion` = `a`.`idUbicacion`)) left join `vistaasignacionesactivas` `va` on(`va`.`idActivo` = `a`.`idActivo`)) WHERE `a`.`activo` = 1 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vistaresumencategoria`
--
DROP TABLE IF EXISTS `vistaresumencategoria`;

DROP VIEW IF EXISTS `vistaresumencategoria`;
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
  MODIFY `idActivo` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asignacionactivo`
--
ALTER TABLE `asignacionactivo`
  MODIFY `idAsignacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `idAuditoria` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bajaactivo`
--
ALTER TABLE `bajaactivo`
  MODIFY `idBaja` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `idCategoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `colaborador`
--
ALTER TABLE `colaborador`
  MODIFY `idColaborador` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `colaboradorubicacion`
--
ALTER TABLE `colaboradorubicacion`
  MODIFY `idColaboradorUbicacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `devolucionactivo`
--
ALTER TABLE `devolucionactivo`
  MODIFY `idDevolucion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `idHistorialLogin` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `imagenactivo`
--
ALTER TABLE `imagenactivo`
  MODIFY `idImagenActivo` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `licenciasoftware`
--
ALTER TABLE `licenciasoftware`
  MODIFY `idLicencia` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `idMovimiento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `idProducto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reparacion`
--
ALTER TABLE `reparacion`
  MODIFY `idReparacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idRol` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudnecesidad`
--
ALTER TABLE `solicitudnecesidad`
  MODIFY `idSolicitud` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `subcategoria`
--
ALTER TABLE `subcategoria`
  MODIFY `idSubcategoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `tipobaja`
--
ALTER TABLE `tipobaja`
  MODIFY `idTipoBaja` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  MODIFY `idUbicacion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
