-- =====================================================================
-- ETAPA 7B: SOLICITUDES DE REPARACIÓN
-- Ejecutar una sola vez sobre una base de datos existente.
-- No ejecutar después de importar un DB_CMDB.sql que ya incluya esta tabla.
-- =====================================================================

CREATE TABLE SolicitudReparacion (
    idSolicitudReparacion BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idActivo BIGINT UNSIGNED NOT NULL,
    idColaborador INT UNSIGNED NOT NULL,
    idUbicacionSolicitud INT UNSIGNED NULL,
    idTecnico INT UNSIGNED NULL,
    idReparacion BIGINT UNSIGNED NULL,
    usuarioRevisa INT UNSIGNED NULL,
    estadoSolicitud ENUM(
        'EN_ESPERA',
        'ASIGNADA',
        'EN_PROCESO',
        'FINALIZADA',
        'RECHAZADA',
        'CANCELADA'
    ) NOT NULL DEFAULT 'EN_ESPERA',
    titulo VARCHAR(150) NOT NULL,
    descripcionFalla TEXT NOT NULL,
    prioridad ENUM('BAJA', 'MEDIA', 'ALTA', 'URGENTE')
        NOT NULL DEFAULT 'MEDIA',
    observacionRevision TEXT NULL,
    fechaSolicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaAsignacion DATETIME NULL,
    fechaCierre DATETIME NULL,

    CONSTRAINT uq_solicitud_reparacion_reparacion
        UNIQUE (idReparacion),

    CONSTRAINT fk_solicitud_reparacion_activo
        FOREIGN KEY (idActivo) REFERENCES Activo(idActivo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_solicitud_reparacion_colaborador
        FOREIGN KEY (idColaborador) REFERENCES Colaborador(idColaborador)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_solicitud_reparacion_ubicacion
        FOREIGN KEY (idUbicacionSolicitud)
        REFERENCES Ubicacion(idUbicacion)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_solicitud_reparacion_tecnico
        FOREIGN KEY (idTecnico) REFERENCES Usuario(idUsuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_solicitud_reparacion_reparacion
        FOREIGN KEY (idReparacion) REFERENCES Reparacion(idReparacion)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_solicitud_reparacion_usuario_revisa
        FOREIGN KEY (usuarioRevisa) REFERENCES Usuario(idUsuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX idx_solicitud_reparacion_estado_fecha
    ON SolicitudReparacion (
        estadoSolicitud,
        prioridad,
        fechaSolicitud
    );

CREATE INDEX idx_solicitud_reparacion_activo_estado
    ON SolicitudReparacion (
        idActivo,
        estadoSolicitud
    );

CREATE INDEX idx_solicitud_reparacion_tecnico_estado
    ON SolicitudReparacion (
        idTecnico,
        estadoSolicitud
    );
