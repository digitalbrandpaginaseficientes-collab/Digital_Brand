DROP DATABASE IF EXISTS digital_brand;

CREATE DATABASE digital_brand
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE digital_brand;

-- =====================================================
-- TABLA DE USUARIOS
-- =====================================================
CREATE TABLE usuarios (
    id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NULL,
    empresa VARCHAR(120) NULL,
    rol ENUM('cliente', 'administrador') NOT NULL DEFAULT 'cliente',
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_usuarios_nombre (nombre),
    INDEX idx_usuarios_rol (rol),
    INDEX idx_usuarios_estado (estado)
) ENGINE=InnoDB;

-- =====================================================
-- TABLA DE SERVICIOS
-- =====================================================
CREATE TABLE servicios (
    id_servicio INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NOT NULL,
    precio DECIMAL(12,2) NOT NULL DEFAULT 0,
    caracteristicas TEXT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_servicios_estado (estado)
) ENGINE=InnoDB;

-- =====================================================
-- SERVICIOS CONTRATADOS POR LOS USUARIOS
-- =====================================================
CREATE TABLE usuario_servicios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    id_servicio INT UNSIGNED NOT NULL,
    estado ENUM(
        'pendiente',
        'en desarrollo',
        'activo',
        'finalizado',
        'cancelado'
    ) NOT NULL DEFAULT 'pendiente',
    fecha_compra TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_inicio DATE NULL,
    fecha_vencimiento DATE NULL,
    observaciones TEXT NULL,

    CONSTRAINT fk_usuario_servicios_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_usuario_servicios_servicio
        FOREIGN KEY (id_servicio)
        REFERENCES servicios(id_servicio)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT uq_usuario_servicio
        UNIQUE (id_usuario, id_servicio),

    INDEX idx_usuario_servicios_estado (estado)
) ENGINE=InnoDB;

-- =====================================================
-- SOLICITUDES DE PLANES
-- =====================================================
CREATE TABLE solicitudes_planes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    id_servicio INT UNSIGNED NOT NULL,
    mensaje TEXT NULL,
    estado ENUM(
        'pendiente',
        'en revisión',
        'aprobada',
        'rechazada',
        'atendida'
    ) NOT NULL DEFAULT 'pendiente',
    fecha_solicitud TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_atencion DATETIME NULL,

    CONSTRAINT fk_solicitudes_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_solicitudes_servicio
        FOREIGN KEY (id_servicio)
        REFERENCES servicios(id_servicio)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_solicitudes_estado (estado),
    INDEX idx_solicitudes_fecha (fecha_solicitud)
) ENGINE=InnoDB;

-- =====================================================
-- MENSAJES DEL FORMULARIO DE CONTACTO
-- =====================================================
CREATE TABLE contacto (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NULL,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    telefono VARCHAR(20) NULL,
    asunto VARCHAR(150) NOT NULL,
    mensaje TEXT NOT NULL,
    estado ENUM('nuevo', 'leído', 'respondido') NOT NULL DEFAULT 'nuevo',
    fecha_envio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_contacto_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_contacto_estado (estado),
    INDEX idx_contacto_fecha (fecha_envio)
) ENGINE=InnoDB;

-- =====================================================
-- MENSAJES DEL CHATBOT
-- =====================================================
CREATE TABLE chatbot_mensajes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NULL,
    mensaje TEXT NOT NULL,
    respuesta TEXT NOT NULL,
    fecha_envio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_chatbot_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_chatbot_fecha (fecha_envio)
) ENGINE=InnoDB;

-- =====================================================
-- RECUPERACIÓN DE CONTRASEÑA
-- =====================================================
CREATE TABLE recuperacion_contrasenas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    token_hash VARCHAR(255) NOT NULL UNIQUE,
    fecha_expiracion DATETIME NOT NULL,
    utilizado TINYINT(1) NOT NULL DEFAULT 0,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_recuperacion_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_recuperacion_expiracion (fecha_expiracion)
) ENGINE=InnoDB;

-- =====================================================
-- DATOS INICIALES DE SERVICIOS
-- =====================================================
INSERT INTO servicios (
    nombre,
    descripcion,
    precio,
    caracteristicas
) VALUES
(
    'Landing Básica',
    'Página web de una sola sección, diseñada para presentar un negocio, producto o servicio.',
    300000,
    'Diseño adaptable|Formulario de contacto|Integración con WhatsApp|Redes sociales|Chatbot'
),
(
    'Sitio Corporativo',
    'Sitio web empresarial con varias secciones para presentar servicios, información y canales de contacto.',
    700000,
    'Hasta 5 páginas|Diseño adaptable|Formulario de contacto|Panel de cliente|Redes sociales|Chatbot'
),
(
    'Tienda Online',
    'Plataforma para publicar productos, gestionar pedidos y facilitar las ventas por internet.',
    1000000,
    'Catálogo de productos|Carrito de compras|Gestión de pedidos|Panel administrativo|Diseño adaptable|Chatbot'
);
