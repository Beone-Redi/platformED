-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 27, 2019 at 05:18 PM
-- Server version: 5.7.27-0ubuntu0.18.04.1
-- PHP Version: 7.2.19-0ubuntu0.18.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ener`
--

-- --------------------------------------------------------

--
-- Table structure for table `estados`
--

CREATE TABLE `estados` (
  `id` int(11) NOT NULL,
  `clave` varchar(2) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `abrev` varchar(16) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabla de Estados de la República Mexicana';

--
-- Dumping data for table `estados`
--

INSERT INTO `estados` (`id`, `clave`, `nombre`, `abrev`, `activo`) VALUES
(1, '01', 'AGUASCALIENTES', 'Ags.', 1),
(2, '02', 'BAJA CALIFORNIA', 'BC.', 1),
(3, '03', 'BAJA CALIFORNIA SUR', 'BCS', 1),
(4, '04', 'CAMPECHE', 'Camp.', 1),
(5, '05', 'COAHUILA DE ZARAGOZA', 'Coah.', 1),
(6, '06', 'COLIMA', 'Col.', 1),
(7, '07', 'CHIAPAS', 'Chis.', 1),
(8, '08', 'CHIHUAHUA', 'Chih.', 1),
(9, '09', 'DISTRITO FEDERAL', 'DF', 1),
(10, '10', 'DURANGO', 'Dgo.', 1),
(11, '11', 'GUANAJUATO', 'Gto.', 1),
(12, '12', 'GUERRERO', 'Gro.', 1),
(13, '13', 'HIDALGO', 'Hgo.', 1),
(14, '14', 'JALISCO', 'Jal.', 1),
(15, '15', 'MÉXICO', 'Mex.', 1),
(16, '16', 'MICHOACÁN DE OCAMPO', 'Mich.', 1),
(17, '17', 'MORELOS', 'Mor.', 1),
(18, '18', 'NAYARIT', 'Nay.', 1),
(19, '19', 'NUEVO LEÓN', 'NL.', 1),
(20, '20', 'OAXACA', 'Oax.', 1),
(21, '21', 'PUEBLA', 'Pue.', 1),
(22, '22', 'QUERÉTARO', 'Qro.', 1),
(23, '23', 'QUINTANA ROO', 'Q. Roo.', 1),
(24, '24', 'SAN LUIS POTOSÍ', 'SLP', 1),
(25, '25', 'SINALOA', 'Sin.', 1),
(26, '26', 'SONORA', 'Son.', 1),
(27, '27', 'TABASCO', 'Tab.', 1),
(28, '28', 'TAMAULIPAS', 'Tamps.', 1),
(29, '29', 'TLAXCALA', 'Tlax.', 1),
(30, '30', 'VERACRUZ DE IGNACIO DE LA LLAVE', 'Ver.', 1),
(31, '31', 'YUCATÁN', 'Yuc.', 1),
(32, '32', 'ZACATECAS', 'Zac.', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `estados`
--
ALTER TABLE `estados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
