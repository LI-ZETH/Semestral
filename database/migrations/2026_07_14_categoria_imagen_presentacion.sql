-- Ejecutar una sola vez sobre la base Inventario existente.
USE Inventario;

ALTER TABLE Categoria
    ADD COLUMN imagenAjuste ENUM('cover', 'contain')
        NOT NULL DEFAULT 'cover'
        AFTER imagen,
    ADD COLUMN imagenTamano ENUM('compacta', 'mediana', 'amplia')
        NOT NULL DEFAULT 'mediana'
        AFTER imagenAjuste;
