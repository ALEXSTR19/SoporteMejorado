<?php
/** Crea las tablas del modulo sin modificar las tablas existentes del sistema. */
function prepararInventario($conexion)
{
    $consultas = array(
        "CREATE TABLE IF NOT EXISTS inventario_articulos (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(40) NOT NULL UNIQUE,
            nombre VARCHAR(150) NOT NULL,
            categoria ENUM('Herramienta','Material','Refaccion','Equipo','Otro') NOT NULL DEFAULT 'Material',
            unidad VARCHAR(30) NOT NULL DEFAULT 'pieza',
            existencia DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0,
            minimo DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0,
            ubicacion VARCHAR(120) NOT NULL DEFAULT '',
            descripcion VARCHAR(255) NOT NULL DEFAULT '',
            reutilizable TINYINT(1) NOT NULL DEFAULT 0,
            activo TINYINT(1) NOT NULL DEFAULT 1,
            creado_por VARCHAR(100) NOT NULL,
            creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_inventario_nombre (nombre), INDEX idx_inventario_stock (activo, existencia, minimo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
        "CREATE TABLE IF NOT EXISTS inventario_salidas (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            folio VARCHAR(30) NOT NULL UNIQUE,
            fecha DATE NOT NULL,
            hora TIME NOT NULL,
            destino VARCHAR(180) NOT NULL,
            motivo VARCHAR(255) NOT NULL,
            responsable VARCHAR(150) NOT NULL,
            observaciones TEXT NOT NULL,
            estado ENUM('En curso','Cerrada','Cancelada') NOT NULL DEFAULT 'En curso',
            creado_por VARCHAR(100) NOT NULL,
            creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_salidas_fecha (fecha), INDEX idx_salidas_estado (estado)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
        "CREATE TABLE IF NOT EXISTS inventario_salida_detalle (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            salida_id INT UNSIGNED NOT NULL,
            articulo_id INT UNSIGNED NOT NULL,
            cantidad DECIMAL(10,2) UNSIGNED NOT NULL,
            devuelto DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0,
            INDEX idx_detalle_salida (salida_id),
            CONSTRAINT fk_detalle_salida FOREIGN KEY (salida_id) REFERENCES inventario_salidas(id),
            CONSTRAINT fk_detalle_articulo FOREIGN KEY (articulo_id) REFERENCES inventario_articulos(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
        "CREATE TABLE IF NOT EXISTS inventario_movimientos (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            articulo_id INT UNSIGNED NOT NULL,
            tipo ENUM('Alta','Ajuste','Salida','Devolucion','Cancelacion') NOT NULL,
            cantidad DECIMAL(10,2) NOT NULL,
            referencia VARCHAR(40) NOT NULL DEFAULT '',
            usuario VARCHAR(100) NOT NULL,
            creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_mov_articulo (articulo_id), INDEX idx_mov_fecha (creado_en),
            CONSTRAINT fk_mov_articulo FOREIGN KEY (articulo_id) REFERENCES inventario_articulos(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
    foreach ($consultas as $consulta) {
        if (!mysqli_query($conexion, $consulta)) {
            return 'No fue posible preparar el modulo de inventario: ' . mysqli_error($conexion);
        }
    }
    return '';
}
