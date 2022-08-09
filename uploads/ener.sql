-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 08-07-2019 a las 14:38:29
-- Versión del servidor: 5.7.26-0ubuntu0.18.04.1
-- Versión de PHP: 7.2.19-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ener`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `en_card`
--

CREATE TABLE `en_card` (
  `ide` bigint(20) NOT NULL,
  `idcard` varchar(16) DEFAULT NULL,
  `company` int(11) DEFAULT NULL,
  `city` varchar(200) DEFAULT NULL,
  `picture` varchar(30) DEFAULT NULL,
  `up_date` date DEFAULT NULL,
  `active` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `en_card`
--

INSERT INTO `en_card` (`ide`, `idcard`, `company`, `city`, `picture`, `up_date`, `active`) VALUES
(1, '4242424242424242', 1, 'Nuevo León', 'c190319112221.jpg', '2019-03-19', 1),
(2, '4242424242424140', 1, 'Nuevo León', 'c190319112221.jpg', '2019-03-19', 1),
(3, '4242424242414039', 1, 'Nuevo León', 'c190319112221.jpg', '2019-03-19', 1),
(4, '4242424242414044', 1, 'Nuevo León', 'c190319112221.jpg', '2019-04-19', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `en_card_funds`
--

CREATE TABLE `en_card_funds` (
  `ide` bigint(20) NOT NULL,
  `idecompany` varchar(250) DEFAULT NULL,
  `idcard` varchar(16) DEFAULT NULL,
  `fund` decimal(20,2) DEFAULT NULL,
  `up_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `en_company`
--

CREATE TABLE `en_company` (
  `ide` bigint(20) NOT NULL,
  `company` varchar(250) DEFAULT NULL,
  `social_reason` varchar(250) DEFAULT NULL,
  `fullname` varchar(250) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `telephone` varchar(250) DEFAULT NULL,
  `address` text,
  `city` varchar(200) DEFAULT NULL,
  `zip` int(6) DEFAULT NULL,
  `aboutme` text,
  `picture` varchar(30) DEFAULT NULL,
  `perfil` varchar(50) DEFAULT NULL,
  `fund` decimal(20,2) DEFAULT NULL,
  `up_date` date DEFAULT NULL,
  `active` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `en_company`
--

INSERT INTO `en_company` (`ide`, `company`, `social_reason`, `fullname`, `email`, `telephone`, `address`, `city`, `zip`, `aboutme`, `picture`, `perfil`, `fund`, `up_date`, `active`) VALUES
(1, 'Energex', 'Energex Demo SA de CV', 'Sergio Marquez', 'sergio.marquez@redpagos.net', '818333555', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'Monterrey', 64000, 'Empresa Demo', 'logo-small.png', 'ADMINISTRADORA', '100000.00', '2019-03-19', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `en_funds`
--

CREATE TABLE `en_funds` (
  `ide` bigint(20) NOT NULL,
  `company` int(11) DEFAULT NULL,
  `fund` decimal(20,2) DEFAULT NULL,
  `up_date` date DEFAULT NULL,
  `active` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `en_layout_anchor`
--

CREATE TABLE `en_layout_anchor` (
  `ide` bigint(20) NOT NULL,
  `idecompany` int(11) DEFAULT NULL,
  `idcard` int(11) DEFAULT NULL,
  `fund` decimal(20,2) DEFAULT NULL,
  `idtype` varchar(50) DEFAULT NULL,
  `up_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `en_log`
--

CREATE TABLE `en_log` (
  `ide` bigint(20) NOT NULL,
  `idecompany` int(11) DEFAULT NULL,
  `fund` decimal(20,2) DEFAULT NULL,
  `idtype` varchar(50) DEFAULT NULL,
  `type_description` text,
  `up_date` date DEFAULT NULL,
  `active` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `en_users`
--

CREATE TABLE `en_users` (
  `ide` bigint(20) NOT NULL,
  `email` varchar(250) DEFAULT NULL,
  `company` int(11) DEFAULT NULL,
  `fullname` varchar(250) DEFAULT NULL,
  `address` text,
  `city` varchar(200) DEFAULT NULL,
  `zip` int(6) DEFAULT NULL,
  `aboutme` text,
  `picture` varchar(30) DEFAULT NULL,
  `idcard` int(11) DEFAULT NULL,
  `perfil` varchar(50) DEFAULT NULL,
  `up_date` date DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `idkey` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `en_users`
--

INSERT INTO `en_users` (`ide`, `email`, `company`, `fullname`, `address`, `city`, `zip`, `aboutme`, `picture`, `idcard`, `perfil`, `up_date`, `active`, `idkey`) VALUES
(1, 'sergio.marquez@redpagos.net', 1, 'Sergio Marquez', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'Nuevo Leon', 6400, 'Usario Administrador de la plataforma.', 'assets/img/faces/face-8.jpg', 1, 'DEVELOP', '2019-03-19', 1, '123456'),
(2, 'luis.salvador@redpagos.net', 1, 'Luis Salvador', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'Nuevo Leon', 6400, 'Usario Administrador de la plataforma.', 'assets/img/faces/face-4.jpg', 2, 'DEVELOP', '2019-03-19', 1, '123456'),
(3, 'alejandro.martinez@redpagos.net', 1, 'Alejandro Martinez', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'Nuevo Leon', 6400, 'Usario Administrador de la plataforma.', 'assets/img/faces/face-2.jpg', 3, 'DEVELOP', '2019-03-19', 1, '123456');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `en_card`
--
ALTER TABLE `en_card`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `ide` (`ide`);

--
-- Indices de la tabla `en_card_funds`
--
ALTER TABLE `en_card_funds`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `ide` (`ide`);

--
-- Indices de la tabla `en_company`
--
ALTER TABLE `en_company`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `ide` (`ide`);

--
-- Indices de la tabla `en_funds`
--
ALTER TABLE `en_funds`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `ide` (`ide`);

--
-- Indices de la tabla `en_layout_anchor`
--
ALTER TABLE `en_layout_anchor`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `ide` (`ide`);

--
-- Indices de la tabla `en_log`
--
ALTER TABLE `en_log`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `ide` (`ide`);

--
-- Indices de la tabla `en_users`
--
ALTER TABLE `en_users`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `ide` (`ide`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `en_card`
--
ALTER TABLE `en_card`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `en_card_funds`
--
ALTER TABLE `en_card_funds`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `en_company`
--
ALTER TABLE `en_company`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `en_funds`
--
ALTER TABLE `en_funds`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `en_layout_anchor`
--
ALTER TABLE `en_layout_anchor`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `en_log`
--
ALTER TABLE `en_log`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `en_users`
--
ALTER TABLE `en_users`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
