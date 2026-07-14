-- =====================================================================
-- SISTEMA CMDB / INVENTARIO
-- Base de datos inicial corregida
-- Compatible con MySQL 8.x y MariaDB 10.4+ (XAMPP)
-- =====================================================================

DROP DATABASE IF EXISTS Inventario;
CREATE DATABASE Inventario
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE Inventario;

SET NAMES utf8mb4;

-- =====================================================================
-- 1. SEGURIDAD, USUARIOS Y AUTENTICACIÓN
-- =====================================================================

CREATE TABLE Rol (
    idRol INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombreRol VARCHAR(40) NOT NULL,
    descripcion VARCHAR(150) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT uq_rol_nombre UNIQUE (nombreRol)
) ENGINE=InnoDB;

INSERT INTO Rol (nombreRol, descripcion) VALUES
('Administrador', 'Administra usuarios, configuración y todos los módulos.'),
('Colaborador', 'Consulta sus activos asignados y registra solicitudes.'),
('Técnico', 'Gestiona revisiones técnicas, reparaciones y bajas.'),
('Operador', 'Registra y actualiza información operativa del inventario.');

CREATE TABLE Usuario (
    idUsuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(25) NULL,
    nombre VARCHAR(60) NOT NULL,
    apellido VARCHAR(60) NOT NULL,
    usuario VARCHAR(40) NOT NULL,
    correo VARCHAR(120) NOT NULL,
    passwordHash VARCHAR(255) NOT NULL,
    idRol INT UNSIGNED NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    intentosFallidos TINYINT UNSIGNED NOT NULL DEFAULT 0,
    bloqueado TINYINT(1) NOT NULL DEFAULT 0,
    fechaBloqueo DATETIME NULL,
    ultimoAcceso DATETIME NULL,
    fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_usuario_cedula UNIQUE (cedula),
    CONSTRAINT uq_usuario_usuario UNIQUE (usuario),
    CONSTRAINT uq_usuario_correo UNIQUE (correo),
    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (idRol) REFERENCES Rol(idRol)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_usuario_rol_activo
    ON Usuario (idRol, activo, bloqueado);

-- La llave privada NO se almacena aquí.
-- Solo se registra la llave pública para verificar firmas digitales.
CREATE TABLE LlavePublicaUsuario (
    idLlavePublica INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT UNSIGNED NOT NULL,
    llavePublica LONGTEXT NOT NULL,
    huellaDigital CHAR(64) NOT NULL,
    algoritmo VARCHAR(30) NOT NULL DEFAULT 'RSA-2048-SHA256',
    versionLlave SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    fechaCreacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaRevocacion DATETIME NULL,
    motivoRevocacion VARCHAR(255) NULL,

    CONSTRAINT uq_llave_huella UNIQUE (huellaDigital),
    CONSTRAINT uq_llave_usuario_version UNIQUE (idUsuario, versionLlave),
    CONSTRAINT fk_llave_usuario
        FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE Historial_Login (
    idHistorialLogin BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT UNSIGNED NULL,
    usuarioIngresado VARCHAR(80) NOT NULL,
    direccionIP VARCHAR(45) NOT NULL,
    userAgent VARCHAR(500) NULL,
    exito TINYINT(1) NOT NULL DEFAULT 0,
    descripcion VARCHAR(255) NULL,
    fechaIntento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_historial_login_usuario
        FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX idx_historial_login_usuario_fecha
    ON Historial_Login (idUsuario, fechaIntento);

CREATE INDEX idx_historial_login_ip_fecha
    ON Historial_Login (direccionIP, fechaIntento);

-- =====================================================================
-- 2. UBICACIONES Y COLABORADORES
-- =====================================================================

CREATE TABLE Ubicacion (
    idUbicacion INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombreUbicacion VARCHAR(100) NOT NULL,
    tipoUbicacion ENUM('EDIFICIO', 'OFICINA', 'CASA', 'BODEGA', 'OTRA')
        NOT NULL DEFAULT 'OFICINA',
    edificio VARCHAR(80) NULL,
    piso VARCHAR(30) NULL,
    oficina VARCHAR(50) NULL,
    direccion VARCHAR(255) NULL,
    descripcion VARCHAR(255) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_ubicacion_nombre UNIQUE (nombreUbicacion)
) ENGINE=InnoDB;

CREATE TABLE Colaborador (
    idColaborador INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT UNSIGNED NULL,
    identificacion VARCHAR(25) NOT NULL,
    nombre VARCHAR(60) NOT NULL,
    apellido VARCHAR(60) NOT NULL,
    correo VARCHAR(120) NOT NULL,
    telefono VARCHAR(25) NULL,
    foto VARCHAR(255) NULL,
    cargo VARCHAR(100) NULL,
    departamento VARCHAR(100) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fechaIngreso DATE NULL,
    fechaSalida DATE NULL,
    fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_colaborador_usuario UNIQUE (idUsuario),
    CONSTRAINT uq_colaborador_identificacion UNIQUE (identificacion),
    CONSTRAINT uq_colaborador_correo UNIQUE (correo),
    CONSTRAINT fk_colaborador_usuario
        FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX idx_colaborador_nombre
    ON Colaborador (apellido, nombre);

CREATE INDEX idx_colaborador_activo
    ON Colaborador (activo);

-- Permite conservar el historial cuando un colaborador cambia de oficina o residencia.
CREATE TABLE ColaboradorUbicacion (
    idColaboradorUbicacion BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idColaborador INT UNSIGNED NOT NULL,
    idUbicacion INT UNSIGNED NOT NULL,
    fechaInicio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaFin DATETIME NULL,
    esActual TINYINT(1) NOT NULL DEFAULT 1,
    observaciones VARCHAR(255) NULL,

    CONSTRAINT fk_colab_ubicacion_colaborador
        FOREIGN KEY (idColaborador) REFERENCES Colaborador(idColaborador)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_colab_ubicacion_ubicacion
        FOREIGN KEY (idUbicacion) REFERENCES Ubicacion(idUbicacion)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_colab_ubicacion_actual
    ON ColaboradorUbicacion (idColaborador, esActual, fechaFin);

-- =====================================================================
-- 3. CATÁLOGO: CATEGORÍAS, SUBCATEGORÍAS Y PRODUCTOS GENERALES
-- =====================================================================

CREATE TABLE Categoria (
    idCategoria INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombreCategoria VARCHAR(80) NOT NULL,
    descripcion VARCHAR(255) NULL,
    imagen VARCHAR(255) NULL,
    imagenAjuste ENUM('cover', 'contain') NOT NULL DEFAULT 'cover',
    imagenTamano ENUM('compacta', 'mediana', 'amplia') NOT NULL DEFAULT 'mediana',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_categoria_nombre UNIQUE (nombreCategoria)
) ENGINE=InnoDB;

INSERT INTO Categoria (nombreCategoria, descripcion) VALUES
('Hardware', 'Componentes, periféricos y otros bienes físicos.'),
('Software', 'Aplicaciones, sistemas y licencias informáticas.'),
('Equipo de Red', 'Routers, switches, access points, firewalls y similares.'),
('Equipo de Cómputo', 'Laptops, desktops, servidores y estaciones de trabajo.'),
('Equipo de Telefonía', 'Teléfonos IP, celulares y equipos de comunicación.');

CREATE TABLE Subcategoria (
    idSubcategoria INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idCategoria INT UNSIGNED NOT NULL,
    nombreSubcategoria VARCHAR(80) NOT NULL,
    descripcion VARCHAR(255) NULL,
    imagen VARCHAR(255) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_subcategoria_categoria_nombre
        UNIQUE (idCategoria, nombreSubcategoria),
    CONSTRAINT fk_subcategoria_categoria
        FOREIGN KEY (idCategoria) REFERENCES Categoria(idCategoria)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO Subcategoria (idCategoria, nombreSubcategoria, descripcion) VALUES
(1, 'Monitor', 'Monitores y pantallas.'),
(1, 'Impresora', 'Impresoras y equipos multifuncionales.'),
(1, 'Periférico', 'Teclados, mouse, cámaras y accesorios.'),
(2, 'Licencia', 'Licencias de uso de software.'),
(2, 'Aplicación', 'Aplicaciones y plataformas informáticas.'),
(2, 'Sistema Operativo', 'Licencias y medios de sistemas operativos.'),
(3, 'Router', 'Equipos de enrutamiento.'),
(3, 'Switch', 'Conmutadores de red.'),
(3, 'Access Point', 'Puntos de acceso inalámbricos.'),
(3, 'Firewall', 'Dispositivos de seguridad de red.'),
(4, 'Laptop', 'Computadoras portátiles.'),
(4, 'Desktop', 'Computadoras de escritorio.'),
(4, 'Servidor', 'Servidores físicos.'),
(5, 'Teléfono IP', 'Teléfonos de voz sobre IP.'),
(5, 'Teléfono móvil', 'Teléfonos inteligentes institucionales.');

CREATE TABLE Producto (
    idProducto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idSubcategoria INT UNSIGNED NOT NULL,
    nombreProducto VARCHAR(120) NOT NULL,
    marca VARCHAR(80) NULL,
    modelo VARCHAR(100) NULL,
    descripcion TEXT NULL,
    tipoProducto ENUM('HARDWARE', 'SOFTWARE', 'LICENCIA') NOT NULL,
    vidaUtilMeses SMALLINT UNSIGNED NULL,
    imagen VARCHAR(255) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_producto_modelo
        UNIQUE (idSubcategoria, nombreProducto, marca, modelo),
    CONSTRAINT fk_producto_subcategoria
        FOREIGN KEY (idSubcategoria) REFERENCES Subcategoria(idSubcategoria)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_producto_subcategoria_activo
    ON Producto (idSubcategoria, activo);

-- =====================================================================
-- 4. INVENTARIO: COPIAS O ACTIVOS INDIVIDUALES
-- =====================================================================

CREATE TABLE EstadoActivo (
    idEstadoActivo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigoEstado VARCHAR(30) NOT NULL,
    nombreEstado VARCHAR(60) NOT NULL,
    permiteAsignacion TINYINT(1) NOT NULL DEFAULT 0,
    cuentaComoInventario TINYINT(1) NOT NULL DEFAULT 1,
    activo TINYINT(1) NOT NULL DEFAULT 1,

    CONSTRAINT uq_estado_activo_codigo UNIQUE (codigoEstado),
    CONSTRAINT uq_estado_activo_nombre UNIQUE (nombreEstado)
) ENGINE=InnoDB;

INSERT INTO EstadoActivo
(codigoEstado, nombreEstado, permiteAsignacion, cuentaComoInventario) VALUES
('EN_INVENTARIO', 'En inventario', 1, 1),
('ASIGNADO', 'Asignado', 0, 1),
('REVISION_TECNICA', 'Revisión técnica', 0, 1),
('EN_REPARACION', 'En reparación', 0, 1),
('DESCARTE', 'Descarte', 0, 1),
('DONADO', 'Donado', 0, 0);

CREATE TABLE Activo (
    idActivo BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idProducto INT UNSIGNED NOT NULL,
    codigoActivo VARCHAR(40) NOT NULL,
    numeroSerie VARCHAR(120) NULL,
    direccionIP VARCHAR(45) NULL,
    costo DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    fechaAdquisicion DATE NOT NULL,
    fechaIngreso DATE NOT NULL,
    vidaUtilMeses SMALLINT UNSIGNED NULL,
    valorResidual DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    idEstadoActivo INT UNSIGNED NOT NULL,
    idUbicacion INT UNSIGNED NULL,
    qrToken CHAR(64) NOT NULL,
    observaciones TEXT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaActualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_activo_codigo UNIQUE (codigoActivo),
    CONSTRAINT uq_activo_serie UNIQUE (numeroSerie),
    CONSTRAINT uq_activo_qr_token UNIQUE (qrToken),
    CONSTRAINT fk_activo_producto
        FOREIGN KEY (idProducto) REFERENCES Producto(idProducto)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_activo_estado
        FOREIGN KEY (idEstadoActivo) REFERENCES EstadoActivo(idEstadoActivo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_activo_ubicacion
        FOREIGN KEY (idUbicacion) REFERENCES Ubicacion(idUbicacion)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX idx_activo_producto_estado
    ON Activo (idProducto, idEstadoActivo, activo);

CREATE INDEX idx_activo_depreciacion
    ON Activo (fechaAdquisicion, vidaUtilMeses);

CREATE INDEX idx_activo_ip
    ON Activo (direccionIP);

-- Cada activo debe tener como mínimo dos imágenes.
-- Los archivos se guardan en el servidor; la base de datos conserva sus rutas.
CREATE TABLE ImagenActivo (
    idImagenActivo BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idActivo BIGINT UNSIGNED NOT NULL,
    rutaImagen VARCHAR(255) NOT NULL,
    nombreOriginal VARCHAR(255) NULL,
    mimeType VARCHAR(100) NULL,
    tamanoBytes INT UNSIGNED NULL,
    esPrincipal TINYINT(1) NOT NULL DEFAULT 0,
    ordenVisual SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fechaRegistro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_imagen_activo_ruta UNIQUE (rutaImagen),
    CONSTRAINT uq_imagen_activo_orden UNIQUE (idActivo, ordenVisual),
    CONSTRAINT fk_imagen_activo_activo
        FOREIGN KEY (idActivo) REFERENCES Activo(idActivo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_imagen_activo_principal
    ON ImagenActivo (idActivo, esPrincipal, activo);

-- Información adicional solamente para activos de software o licencias.
CREATE TABLE LicenciaSoftware (
    idLicencia BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idActivo BIGINT UNSIGNED NOT NULL,
    proveedor VARCHAR(120) NULL,
    tipoLicencia VARCHAR(80) NULL,
    urlAcceso VARCHAR(500) NULL,
    claveCifrada LONGTEXT NULL,
    cantidadPuestos INT UNSIGNED NOT NULL DEFAULT 1,
    fechaInicio DATE NULL,
    fechaExpiracion DATE NULL,
    renovacionAutomatica TINYINT(1) NOT NULL DEFAULT 0,
    observaciones TEXT NULL,

    CONSTRAINT uq_licencia_activo UNIQUE (idActivo),
    CONSTRAINT fk_licencia_activo
        FOREIGN KEY (idActivo) REFERENCES Activo(idActivo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_licencia_expiracion
    ON LicenciaSoftware (fechaExpiracion);

-- =====================================================================
-- 5. ASIGNACIONES Y DEVOLUCIONES
-- =====================================================================

CREATE TABLE AsignacionActivo (
    idAsignacion BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idActivo BIGINT UNSIGNED NOT NULL,
    idColaborador INT UNSIGNED NOT NULL,
    usuarioEntrega INT UNSIGNED NOT NULL,
    fechaEntrega DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaDevolucion DATETIME NULL,
    estadoAsignacion ENUM('ACTIVA', 'DEVUELTA', 'CANCELADA')
        NOT NULL DEFAULT 'ACTIVA',
    observacionesEntrega TEXT NULL,

    CONSTRAINT fk_asignacion_activo
        FOREIGN KEY (idActivo) REFERENCES Activo(idActivo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_asignacion_colaborador
        FOREIGN KEY (idColaborador) REFERENCES Colaborador(idColaborador)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_asignacion_usuario_entrega
        FOREIGN KEY (usuarioEntrega) REFERENCES Usuario(idUsuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_asignacion_activo_estado
    ON AsignacionActivo (idActivo, estadoAsignacion, fechaDevolucion);

CREATE INDEX idx_asignacion_colaborador_estado
    ON AsignacionActivo (idColaborador, estadoAsignacion);

CREATE TABLE MotivoDevolucion (
    idMotivoDevolucion INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombreMotivo VARCHAR(80) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,

    CONSTRAINT uq_motivo_devolucion UNIQUE (nombreMotivo)
) ENGINE=InnoDB;

INSERT INTO MotivoDevolucion (nombreMotivo) VALUES
('Renuncia'),
('Traslado'),
('Cambio de equipo'),
('Equipo dañado'),
('Fin de licencia'),
('Otro');

CREATE TABLE DevolucionActivo (
    idDevolucion BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idAsignacion BIGINT UNSIGNED NOT NULL,
    usuarioRecibe INT UNSIGNED NOT NULL,
    idMotivoDevolucion INT UNSIGNED NOT NULL,
    condicionRecepcion ENUM('BUENO', 'DANADO', 'INCOMPLETO', 'NO_VERIFICADO')
        NOT NULL DEFAULT 'NO_VERIFICADO',
    observaciones TEXT NULL,
    fechaRecepcion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_devolucion_asignacion UNIQUE (idAsignacion),
    CONSTRAINT fk_devolucion_asignacion
        FOREIGN KEY (idAsignacion) REFERENCES AsignacionActivo(idAsignacion)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_devolucion_usuario_recibe
        FOREIGN KEY (usuarioRecibe) REFERENCES Usuario(idUsuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_devolucion_motivo
        FOREIGN KEY (idMotivoDevolucion)
        REFERENCES MotivoDevolucion(idMotivoDevolucion)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =====================================================================
-- 6. REVISIONES, REPARACIONES, DESCARTES Y DONACIONES
-- =====================================================================

CREATE TABLE EstadoReparacion (
    idEstadoReparacion INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombreEstado VARCHAR(50) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,

    CONSTRAINT uq_estado_reparacion UNIQUE (nombreEstado)
) ENGINE=InnoDB;

INSERT INTO EstadoReparacion (nombreEstado) VALUES
('Pendiente'),
('En proceso'),
('Finalizada'),
('No reparable'),
('Cancelada');

CREATE TABLE Reparacion (
    idReparacion BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idActivo BIGINT UNSIGNED NOT NULL,
    idTecnico INT UNSIGNED NOT NULL,
    idEstadoReparacion INT UNSIGNED NOT NULL,
    descripcionFalla TEXT NOT NULL,
    diagnostico TEXT NULL,
    trabajoRealizado TEXT NULL,
    costoReparacion DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    fechaInicio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaFin DATETIME NULL,
    observaciones TEXT NULL,

    CONSTRAINT fk_reparacion_activo
        FOREIGN KEY (idActivo) REFERENCES Activo(idActivo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_reparacion_tecnico
        FOREIGN KEY (idTecnico) REFERENCES Usuario(idUsuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_reparacion_estado
        FOREIGN KEY (idEstadoReparacion)
        REFERENCES EstadoReparacion(idEstadoReparacion)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_reparacion_activo_estado
    ON Reparacion (idActivo, idEstadoReparacion);

CREATE TABLE TipoBaja (
    idTipoBaja INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigoTipo VARCHAR(20) NOT NULL,
    nombreTipo VARCHAR(40) NOT NULL,

    CONSTRAINT uq_tipo_baja_codigo UNIQUE (codigoTipo),
    CONSTRAINT uq_tipo_baja_nombre UNIQUE (nombreTipo)
) ENGINE=InnoDB;

INSERT INTO TipoBaja (codigoTipo, nombreTipo) VALUES
('DESCARTE', 'Descarte'),
('DONACION', 'Donación');

CREATE TABLE BajaActivo (
    idBaja BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idActivo BIGINT UNSIGNED NOT NULL,
    idTipoBaja INT UNSIGNED NOT NULL,
    idUsuario INT UNSIGNED NOT NULL,
    motivo TEXT NOT NULL,
    opinionTecnica TEXT NULL,
    responsableDonacion VARCHAR(150) NULL,
    entidadBeneficiaria VARCHAR(180) NULL,
    documentoReferencia VARCHAR(100) NULL,
    fechaBaja DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_baja_activo UNIQUE (idActivo),
    CONSTRAINT fk_baja_activo
        FOREIGN KEY (idActivo) REFERENCES Activo(idActivo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_baja_tipo
        FOREIGN KEY (idTipoBaja) REFERENCES TipoBaja(idTipoBaja)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_baja_usuario
        FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =====================================================================
-- 7. NECESIDADES Y SOLICITUDES DE COLABORADORES
-- =====================================================================

CREATE TABLE EstadoSolicitud (
    idEstadoSolicitud INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombreEstado VARCHAR(50) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,

    CONSTRAINT uq_estado_solicitud UNIQUE (nombreEstado)
) ENGINE=InnoDB;

INSERT INTO EstadoSolicitud (nombreEstado) VALUES
('En espera'),
('En trámite'),
('Aprobada'),
('Rechazada'),
('Atendida'),
('Cancelada');

CREATE TABLE SolicitudNecesidad (
    idSolicitud BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idColaborador INT UNSIGNED NOT NULL,
    idSubcategoria INT UNSIGNED NULL,
    idProducto INT UNSIGNED NULL,
    idEstadoSolicitud INT UNSIGNED NOT NULL,
    tipoSolicitud ENUM('EQUIPO', 'SOFTWARE', 'LICENCIA', 'OTRA') NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcionNecesidad TEXT NOT NULL,
    justificacion TEXT NOT NULL,
    cantidad SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    prioridad ENUM('BAJA', 'MEDIA', 'ALTA', 'URGENTE') NOT NULL DEFAULT 'MEDIA',
    periodoNecesidad ENUM('INMEDIATA', 'ANUAL', 'QUINQUENAL')
        NOT NULL DEFAULT 'INMEDIATA',
    anioPresupuestado YEAR NULL,
    costoEstimado DECIMAL(12,2) NULL,
    usuarioRevisa INT UNSIGNED NULL,
    observacionRevision TEXT NULL,
    fechaSolicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaRevision DATETIME NULL,

    CONSTRAINT fk_solicitud_colaborador
        FOREIGN KEY (idColaborador) REFERENCES Colaborador(idColaborador)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_solicitud_subcategoria
        FOREIGN KEY (idSubcategoria) REFERENCES Subcategoria(idSubcategoria)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT fk_solicitud_producto
        FOREIGN KEY (idProducto) REFERENCES Producto(idProducto)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT fk_solicitud_estado
        FOREIGN KEY (idEstadoSolicitud)
        REFERENCES EstadoSolicitud(idEstadoSolicitud)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_solicitud_usuario_revisa
        FOREIGN KEY (usuarioRevisa) REFERENCES Usuario(idUsuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX idx_solicitud_estado_fecha
    ON SolicitudNecesidad (idEstadoSolicitud, fechaSolicitud);

CREATE INDEX idx_solicitud_presupuesto
    ON SolicitudNecesidad (anioPresupuestado, periodoNecesidad);

-- =====================================================================
-- 8. TRAZABILIDAD DEL ACTIVO Y AUDITORÍA CRIPTOGRÁFICA
-- =====================================================================

CREATE TABLE MovimientoActivo (
    idMovimiento BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idActivo BIGINT UNSIGNED NOT NULL,
    idUsuario INT UNSIGNED NOT NULL,
    tipoMovimiento ENUM(
        'REGISTRO',
        'ACTUALIZACION',
        'ASIGNACION',
        'DEVOLUCION',
        'CAMBIO_ESTADO',
        'CAMBIO_UBICACION',
        'REPARACION',
        'DESCARTE',
        'DONACION'
    ) NOT NULL,
    idEstadoAnterior INT UNSIGNED NULL,
    idEstadoNuevo INT UNSIGNED NULL,
    idUbicacionAnterior INT UNSIGNED NULL,
    idUbicacionNueva INT UNSIGNED NULL,
    descripcion TEXT NULL,
    fechaMovimiento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_movimiento_activo
        FOREIGN KEY (idActivo) REFERENCES Activo(idActivo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_movimiento_usuario
        FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_movimiento_estado_anterior
        FOREIGN KEY (idEstadoAnterior) REFERENCES EstadoActivo(idEstadoActivo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_movimiento_estado_nuevo
        FOREIGN KEY (idEstadoNuevo) REFERENCES EstadoActivo(idEstadoActivo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_movimiento_ubicacion_anterior
        FOREIGN KEY (idUbicacionAnterior) REFERENCES Ubicacion(idUbicacion)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT fk_movimiento_ubicacion_nueva
        FOREIGN KEY (idUbicacionNueva) REFERENCES Ubicacion(idUbicacion)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX idx_movimiento_activo_fecha
    ON MovimientoActivo (idActivo, fechaMovimiento);

CREATE TABLE Auditoria (
    idAuditoria BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT UNSIGNED NULL,
    idLlavePublica INT UNSIGNED NULL,
    modulo VARCHAR(80) NOT NULL,
    accion VARCHAR(100) NOT NULL,
    tablaAfectada VARCHAR(80) NULL,
    idRegistro VARCHAR(80) NULL,
    descripcion TEXT NULL,
    datosAnteriores LONGTEXT NULL,
    datosNuevos LONGTEXT NULL,
    direccionIP VARCHAR(45) NULL,
    userAgent VARCHAR(500) NULL,
    hashAnterior CHAR(64) NULL,
    hashRegistro CHAR(64) NULL,
    firmaDigital LONGTEXT NULL,
    algoritmoFirma VARCHAR(30) NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_auditoria_usuario
        FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT fk_auditoria_llave
        FOREIGN KEY (idLlavePublica)
        REFERENCES LlavePublicaUsuario(idLlavePublica)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX idx_auditoria_usuario_fecha
    ON Auditoria (idUsuario, fecha);

CREATE INDEX idx_auditoria_tabla_registro
    ON Auditoria (tablaAfectada, idRegistro);

-- =====================================================================
-- 9. VISTAS PARA REPORTES Y PANTALLAS
-- =====================================================================

CREATE OR REPLACE VIEW VistaAsignacionesActivas AS
SELECT
    aa.idAsignacion,
    aa.fechaEntrega,
    a.idActivo,
    a.codigoActivo,
    a.numeroSerie,
    p.idProducto,
    p.nombreProducto,
    p.marca,
    p.modelo,
    c.idColaborador,
    c.identificacion,
    CONCAT(c.nombre, ' ', c.apellido) AS nombreColaborador
FROM AsignacionActivo aa
INNER JOIN Activo a
    ON a.idActivo = aa.idActivo
INNER JOIN Producto p
    ON p.idProducto = a.idProducto
INNER JOIN Colaborador c
    ON c.idColaborador = aa.idColaborador
WHERE aa.estadoAsignacion = 'ACTIVA'
  AND aa.fechaDevolucion IS NULL;

CREATE OR REPLACE VIEW VistaInventarioDetalle AS
SELECT
    a.idActivo,
    a.codigoActivo,
    a.numeroSerie,
    a.direccionIP,
    a.costo,
    a.fechaAdquisicion,
    a.fechaIngreso,
    COALESCE(a.vidaUtilMeses, p.vidaUtilMeses) AS vidaUtilMesesAplicada,
    CASE
        WHEN COALESCE(a.vidaUtilMeses, p.vidaUtilMeses) IS NULL THEN NULL
        ELSE DATE_ADD(
            a.fechaAdquisicion,
            INTERVAL COALESCE(a.vidaUtilMeses, p.vidaUtilMeses) MONTH
        )
    END AS fechaFinVidaUtil,
    (
        SELECT ia.rutaImagen
        FROM ImagenActivo ia
        WHERE ia.idActivo = a.idActivo
          AND ia.activo = 1
        ORDER BY ia.esPrincipal DESC, ia.ordenVisual ASC, ia.idImagenActivo ASC
        LIMIT 1
    ) AS imagenPrincipal,
    (
        SELECT COUNT(*)
        FROM ImagenActivo ia2
        WHERE ia2.idActivo = a.idActivo
          AND ia2.activo = 1
    ) AS cantidadImagenes,
    a.qrToken,
    ea.codigoEstado,
    ea.nombreEstado,
    p.idProducto,
    p.nombreProducto,
    p.marca,
    p.modelo,
    p.tipoProducto,
    s.idSubcategoria,
    s.nombreSubcategoria,
    cat.idCategoria,
    cat.nombreCategoria,
    u.nombreUbicacion,
    va.idColaborador,
    va.nombreColaborador
FROM Activo a
INNER JOIN Producto p
    ON p.idProducto = a.idProducto
INNER JOIN Subcategoria s
    ON s.idSubcategoria = p.idSubcategoria
INNER JOIN Categoria cat
    ON cat.idCategoria = s.idCategoria
INNER JOIN EstadoActivo ea
    ON ea.idEstadoActivo = a.idEstadoActivo
LEFT JOIN Ubicacion u
    ON u.idUbicacion = a.idUbicacion
LEFT JOIN VistaAsignacionesActivas va
    ON va.idActivo = a.idActivo
WHERE a.activo = 1;

CREATE OR REPLACE VIEW VistaResumenCategoria AS
SELECT
    cat.idCategoria,
    cat.nombreCategoria,
    COUNT(a.idActivo) AS totalActivos,
    SUM(CASE WHEN ea.codigoEstado = 'EN_INVENTARIO' THEN 1 ELSE 0 END)
        AS enInventario,
    SUM(CASE WHEN ea.codigoEstado = 'ASIGNADO' THEN 1 ELSE 0 END)
        AS asignados,
    SUM(CASE WHEN ea.codigoEstado = 'REVISION_TECNICA' THEN 1 ELSE 0 END)
        AS enRevision,
    SUM(CASE WHEN ea.codigoEstado = 'EN_REPARACION' THEN 1 ELSE 0 END)
        AS enReparacion,
    SUM(CASE WHEN ea.codigoEstado = 'DESCARTE' THEN 1 ELSE 0 END)
        AS enDescarte,
    SUM(CASE WHEN ea.codigoEstado = 'DONADO' THEN 1 ELSE 0 END)
        AS donados
FROM Categoria cat
LEFT JOIN Subcategoria s
    ON s.idCategoria = cat.idCategoria
LEFT JOIN Producto p
    ON p.idSubcategoria = s.idSubcategoria
LEFT JOIN Activo a
    ON a.idProducto = p.idProducto
   AND a.activo = 1
LEFT JOIN EstadoActivo ea
    ON ea.idEstadoActivo = a.idEstadoActivo
GROUP BY cat.idCategoria, cat.nombreCategoria;

CREATE OR REPLACE VIEW VistaActivosProximosDepreciacion AS
SELECT
    a.idActivo,
    a.codigoActivo,
    p.nombreProducto,
    p.marca,
    p.modelo,
    a.costo,
    a.fechaAdquisicion,
    COALESCE(a.vidaUtilMeses, p.vidaUtilMeses) AS vidaUtilMesesAplicada,
    DATE_ADD(
        a.fechaAdquisicion,
        INTERVAL COALESCE(a.vidaUtilMeses, p.vidaUtilMeses) MONTH
    ) AS fechaFinVidaUtil,
    DATEDIFF(
        DATE_ADD(
            a.fechaAdquisicion,
            INTERVAL COALESCE(a.vidaUtilMeses, p.vidaUtilMeses) MONTH
        ),
        CURRENT_DATE
    ) AS diasRestantes,
    ea.nombreEstado
FROM Activo a
INNER JOIN Producto p
    ON p.idProducto = a.idProducto
INNER JOIN EstadoActivo ea
    ON ea.idEstadoActivo = a.idEstadoActivo
WHERE a.activo = 1
  AND ea.codigoEstado <> 'DONADO'
  AND COALESCE(a.vidaUtilMeses, p.vidaUtilMeses) IS NOT NULL;

CREATE OR REPLACE VIEW VistaActivosPorColaborador AS
SELECT
    c.idColaborador,
    c.identificacion,
    CONCAT(c.nombre, ' ', c.apellido) AS nombreColaborador,
    c.correo,
    aa.idAsignacion,
    aa.fechaEntrega,
    a.idActivo,
    a.codigoActivo,
    a.numeroSerie,
    a.direccionIP,
    p.nombreProducto,
    p.marca,
    p.modelo,
    cat.nombreCategoria
FROM Colaborador c
INNER JOIN AsignacionActivo aa
    ON aa.idColaborador = c.idColaborador
   AND aa.estadoAsignacion = 'ACTIVA'
   AND aa.fechaDevolucion IS NULL
INNER JOIN Activo a
    ON a.idActivo = aa.idActivo
INNER JOIN Producto p
    ON p.idProducto = a.idProducto
INNER JOIN Subcategoria s
    ON s.idSubcategoria = p.idSubcategoria
INNER JOIN Categoria cat
    ON cat.idCategoria = s.idCategoria;

CREATE OR REPLACE VIEW VistaActivosConImagenesIncompletas AS
SELECT
    a.idActivo,
    a.codigoActivo,
    p.nombreProducto,
    COUNT(ia.idImagenActivo) AS cantidadImagenes
FROM Activo a
INNER JOIN Producto p
    ON p.idProducto = a.idProducto
LEFT JOIN ImagenActivo ia
    ON ia.idActivo = a.idActivo
   AND ia.activo = 1
WHERE a.activo = 1
GROUP BY a.idActivo, a.codigoActivo, p.nombreProducto
HAVING COUNT(ia.idImagenActivo) < 2;

-- =====================================================================
-- 10. CONSULTAS DE COMPROBACIÓN
-- =====================================================================

-- Resumen por categoría:
-- SELECT * FROM VistaResumenCategoria;

-- Activos que llegarán al fin de su vida útil en los próximos 90 días:
-- SELECT *
-- FROM VistaActivosProximosDepreciacion
-- WHERE diasRestantes BETWEEN 0 AND 90
-- ORDER BY diasRestantes ASC;

-- Activos actualmente asignados:
-- SELECT * FROM VistaAsignacionesActivas;

-- Activos que todavía no cumplen con el mínimo de dos imágenes:
-- SELECT * FROM VistaActivosConImagenesIncompletas;

-- Activos de un colaborador:
-- SELECT *
-- FROM VistaActivosPorColaborador
-- WHERE idColaborador = 1;

-- IMPORTANTE:
-- La asignación, devolución, reparación y baja deben ejecutarse desde PHP
-- mediante transacciones PDO. Por ejemplo, al devolver:
-- 1) cerrar AsignacionActivo;
-- 2) insertar DevolucionActivo;
-- 3) cambiar Activo a REVISION_TECNICA;
-- 4) insertar MovimientoActivo;
-- 5) insertar Auditoria;
-- todo dentro de una sola transacción.
--
-- Antes de considerar completo el registro de un activo, PHP debe validar:
-- SELECT COUNT(*) FROM ImagenActivo WHERE idActivo = ? AND activo = 1;
-- El resultado debe ser mayor o igual a 2.
