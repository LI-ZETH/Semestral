CREATE TABLE IF NOT EXISTS AsignacionLicencia (
    idAsignacionLicencia BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idLicencia BIGINT UNSIGNED NOT NULL,
    idColaborador INT UNSIGNED NOT NULL,
    idUsuarioAsigna INT UNSIGNED NOT NULL,
    correoAsignado VARCHAR(120) NULL,
    fechaAsignacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaRevocacion DATETIME NULL,
    estadoAsignacion ENUM('ACTIVA', 'REVOCADA')
        NOT NULL DEFAULT 'ACTIVA',
    observaciones TEXT NULL,

    INDEX idx_asignacion_licencia_estado (
        idLicencia,
        estadoAsignacion,
        fechaRevocacion
    ),
    INDEX idx_asignacion_licencia_colaborador (
        idColaborador,
        estadoAsignacion
    ),

    CONSTRAINT fk_asignacion_licencia_licencia
        FOREIGN KEY (idLicencia)
        REFERENCES LicenciaSoftware (idLicencia)
        ON UPDATE CASCADE,

    CONSTRAINT fk_asignacion_licencia_colaborador
        FOREIGN KEY (idColaborador)
        REFERENCES Colaborador (idColaborador)
        ON UPDATE CASCADE,

    CONSTRAINT fk_asignacion_licencia_usuario
        FOREIGN KEY (idUsuarioAsigna)
        REFERENCES Usuario (idUsuario)
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
