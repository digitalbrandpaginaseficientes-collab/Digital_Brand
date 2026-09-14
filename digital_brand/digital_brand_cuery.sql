-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-08-2026 a las 03:40:57
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `digital_brand`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chatbot_mensajes`
--

CREATE TABLE `chatbot_mensajes` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED DEFAULT NULL,
  `mensaje` text NOT NULL,
  `respuesta` text NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `chatbot_mensajes`
--

INSERT INTO `chatbot_mensajes` (`id`, `id_usuario`, `mensaje`, `respuesta`, `fecha_envio`) VALUES
(1, NULL, '¿Qué planes ofrecen?', '¡Hola! En Digital Brand, ofrecemos tres planes diferentes para satisfacer las necesidades de tus empresas:\n\n1. **Landing B├ísica**: Precio: $300.000. Descripción: P├ígina web de una sola secci├│n, dise├▒ada para presentar un negocio, producto o servicio. Incluye: Dise├▒o adaptable, Formulario de contacto, Integraci├│n con WhatsApp, Redes sociales, Chatbot.\n2. **Redes Sociales**: Precio: $450.000. Descripción: Esta es una prueba de las pruebas. Incluye: pruebas, pruebas, pruebas.\n3. **Sitio Corporativo**: Precio: $700.000. Descripción: Sitio web empresarial con varias secciones para presentar servicios, informaci├│n y canales de contacto. Incluye: Hasta 5 p├íginas, Dise├▒o adaptable, Formulario de contacto, Panel de cliente, Redes sociales, Chatbot.\n4. **Tienda Online**: Precio: $1.000.000. Descripción: Plataforma para publicar productos, gestionar pedidos y facilitar las ventas por internet. Incluye: Cat├ílogo de productos, Carrito de compras, Gesti├│', '2026-08-04 01:08:31'),
(2, NULL, '¿Cuáles son los precios?', 'Los precios de nuestros planes son:\n\n* **Landing Bísica**: $300.000\n* **Redes Sociales**: $450.000 (Nota: Esta es una prueba de las pruebas, no tiene detalles específicos)\n* **Sitio Corporativo**: $700.000\n* **Tienda Online**: $1.000.000\n\nEspero que esta información sea útil. ¿Necesitas más ayuda?', '2026-08-04 01:09:02'),
(3, NULL, '¿Cómo puedo solicitar un servicio?', 'Para solicitar un plan, debes seguir estos pasos:\n\n1. Inicia sesión en nuestro sitio web.\n2. Ingresa a la sección \"Servicios\" y selecciona \"Solicitar plan\".\n3. Rellena el formulario con tus datos y necesidades específicas.\n\n¡Eso es todo! Nuestro equipo estará encantado de ayudarte a encontrar el plan perfecto para tus necesidades.', '2026-08-04 01:09:28'),
(4, NULL, '¿Cómo puedo contactar a Digital Brand?', 'Para contactarnos, puedes utilizar las siguientes opciones:\n\n* Llamar a nuestro número de WhatsApp: +57 333 284 3241\n* Escribe un correo electrónico a nuestra dirección de contacto: DigitalBrandpaginaseficientes@gmail.com\n* Visita nuestra página de contacto en el sitio web y rellena el formulario con tus datos y pregunta.\n\nEstamos aquí para ayudarte. ¡No dudes en contactarnos!', '2026-08-04 01:09:54'),
(5, NULL, 'cual es el principal servicio ?', 'El principal servicio que ofrecemos en Digital Brand es la creación y diseño de páginas web eficientes para empresas que rinden. Nuestro objetivo es brindar soluciones personalizadas y efectivas para ayudar a nuestros clientes a establecer una presencia en línea sólida y atraer a más clientes.', '2026-08-04 01:10:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto`
--

CREATE TABLE `contacto` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `asunto` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `estado` enum('nuevo','leído','respondido') NOT NULL DEFAULT 'nuevo',
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacion_contrasenas`
--

CREATE TABLE `recuperacion_contrasenas` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `utilizado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recuperacion_contrasenas`
--

INSERT INTO `recuperacion_contrasenas` (`id`, `id_usuario`, `token_hash`, `fecha_expiracion`, `utilizado`, `fecha_creacion`) VALUES
(1, 1, 'c7172b5500eefad19d416d0d2233a8e27a239b4b42eb49b3a6222353d4da406f', '2026-08-03 17:48:36', 0, '2026-08-03 22:18:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `precio` decimal(12,2) NOT NULL DEFAULT 0.00,
  `caracteristicas` text DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre`, `descripcion`, `precio`, `caracteristicas`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Landing B├ísica', 'P├ígina web de una sola secci├│n, dise├▒ada para presentar un negocio, producto o servicio.', 300000.00, 'Dise├▒o adaptable|Formulario de contacto|Integraci├│n con WhatsApp|Redes sociales|Chatbot', 'activo', '2026-08-03 13:37:09', '2026-08-03 13:37:09'),
(2, 'Sitio Corporativo', 'Sitio web empresarial con varias secciones para presentar servicios, informaci├│n y canales de contacto.', 700000.00, 'Hasta 5 p├íginas|Dise├▒o adaptable|Formulario de contacto|Panel de cliente|Redes sociales|Chatbot', 'activo', '2026-08-03 13:37:09', '2026-08-03 13:37:09'),
(3, 'Tienda Online', 'Plataforma para publicar productos, gestionar pedidos y facilitar las ventas por internet.', 1000000.00, 'Cat├ílogo de productos|Carrito de compras|Gesti├│n de pedidos|Panel administrativo|Dise├▒o adaptable|Chatbot', 'activo', '2026-08-03 13:37:09', '2026-08-03 13:37:09'),
(4, 'Redes Sociales qwe', 'esto es una prueba de las pruebas', 450000.00, 'pruebas | pruebas | pruebas', 'inactivo', '2026-08-03 17:10:55', '2026-08-04 01:16:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_planes`
--

CREATE TABLE `solicitudes_planes` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_servicio` int(10) UNSIGNED NOT NULL,
  `mensaje` text DEFAULT NULL,
  `estado` enum('pendiente','en revisión','aprobada','rechazada','atendida') NOT NULL DEFAULT 'pendiente',
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_atencion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudes_planes`
--

INSERT INTO `solicitudes_planes` (`id`, `id_usuario`, `id_servicio`, `mensaje`, `estado`, `fecha_solicitud`, `fecha_atencion`) VALUES
(8, 1, 1, 'quiero mas informacion sobre los productos', 'aprobada', '2026-08-03 17:46:37', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `empresa` varchar(120) DEFAULT NULL,
  `rol` enum('cliente','administrador') NOT NULL DEFAULT 'cliente',
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `contrasena`, `telefono`, `empresa`, `rol`, `estado`, `fecha_registro`, `fecha_actualizacion`) VALUES
(1, 'Cesar Alexander Zapata Jimene', 'plusstcwp@gmail.com', '$2y$10$VwCgB8ruwYT6ZTv1LiHp6eVxO7ejYJ2.L75JWM/BmnRQsuoi1sWUK', '3166985459', 'Plusst', 'cliente', 'activo', '2026-08-03 14:48:23', '2026-08-04 01:35:49'),
(2, 'Santiago Diaz', 'santidiazz2010@gmail.com', '$2y$10$2YyhUIuQRDIKqR3nVfp4ietTjAJLnZEjjn8schk6db4NzvaEWxCz2', '+57 321 5462028', 'Digital Brand', 'administrador', 'activo', '2026-08-03 16:17:12', '2026-08-03 16:52:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_servicios`
--

CREATE TABLE `usuario_servicios` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_servicio` int(10) UNSIGNED NOT NULL,
  `estado` enum('pendiente','en desarrollo','en revisión','activo','finalizado','cancelado') NOT NULL DEFAULT 'pendiente',
  `progreso` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `fecha_compra` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_inicio` date DEFAULT NULL,
  `fecha_entrega_estimada` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_servicios`
--

INSERT INTO `usuario_servicios` (`id`, `id_usuario`, `id_servicio`, `estado`, `progreso`, `fecha_compra`, `fecha_inicio`, `fecha_entrega_estimada`, `fecha_finalizacion`, `fecha_vencimiento`, `observaciones`) VALUES
(1, 1, 1, 'en revisión', 75, '2026-08-03 17:16:16', '2026-08-03', '2026-08-29', NULL, NULL, 'Se inició el desarrollo del proyecto. Actualmente se está trabajando en la estructura, los contenidos y el diseño inicial.');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `chatbot_mensajes`
--
ALTER TABLE `chatbot_mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_chatbot_usuario` (`id_usuario`),
  ADD KEY `idx_chatbot_fecha` (`fecha_envio`);

--
-- Indices de la tabla `contacto`
--
ALTER TABLE `contacto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contacto_usuario` (`id_usuario`),
  ADD KEY `idx_contacto_estado` (`estado`),
  ADD KEY `idx_contacto_fecha` (`fecha_envio`);

--
-- Indices de la tabla `recuperacion_contrasenas`
--
ALTER TABLE `recuperacion_contrasenas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_hash` (`token_hash`),
  ADD KEY `fk_recuperacion_usuario` (`id_usuario`),
  ADD KEY `idx_recuperacion_expiracion` (`fecha_expiracion`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `idx_servicios_estado` (`estado`);

--
-- Indices de la tabla `solicitudes_planes`
--
ALTER TABLE `solicitudes_planes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_solicitudes_usuario` (`id_usuario`),
  ADD KEY `fk_solicitudes_servicio` (`id_servicio`),
  ADD KEY `idx_solicitudes_estado` (`estado`),
  ADD KEY `idx_solicitudes_fecha` (`fecha_solicitud`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `idx_usuarios_nombre` (`nombre`),
  ADD KEY `idx_usuarios_rol` (`rol`),
  ADD KEY `idx_usuarios_estado` (`estado`);

--
-- Indices de la tabla `usuario_servicios`
--
ALTER TABLE `usuario_servicios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuario_servicio` (`id_usuario`,`id_servicio`),
  ADD KEY `fk_usuario_servicios_servicio` (`id_servicio`),
  ADD KEY `idx_usuario_servicios_estado` (`estado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `chatbot_mensajes`
--
ALTER TABLE `chatbot_mensajes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `contacto`
--
ALTER TABLE `contacto`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `recuperacion_contrasenas`
--
ALTER TABLE `recuperacion_contrasenas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudes_planes`
--
ALTER TABLE `solicitudes_planes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario_servicios`
--
ALTER TABLE `usuario_servicios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `chatbot_mensajes`
--
ALTER TABLE `chatbot_mensajes`
  ADD CONSTRAINT `fk_chatbot_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `contacto`
--
ALTER TABLE `contacto`
  ADD CONSTRAINT `fk_contacto_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `recuperacion_contrasenas`
--
ALTER TABLE `recuperacion_contrasenas`
  ADD CONSTRAINT `fk_recuperacion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudes_planes`
--
ALTER TABLE `solicitudes_planes`
  ADD CONSTRAINT `fk_solicitudes_servicio` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_solicitudes_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario_servicios`
--
ALTER TABLE `usuario_servicios`
  ADD CONSTRAINT `fk_usuario_servicios_servicio` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuario_servicios_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
