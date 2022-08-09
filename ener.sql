-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 09, 2022 at 09:45 PM
-- Server version: 5.7.39-0ubuntu0.18.04.2
-- PHP Version: 7.2.24-0ubuntu0.18.04.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ener`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`admin`@`localhost` PROCEDURE `applyPayCard` (IN `VCard` VARCHAR(8), IN `VAmount` DECIMAL(15,2), IN `VCompany` VARCHAR(4), IN `VDate` VARCHAR(30), IN `VUser` VARCHAR(4), IN `VComment` VARCHAR(250))  BEGIN
	DECLARE AmountCompany FLOAT DEFAULT 0.00;
	DECLARE NewAmountCompany FLOAT DEFAULT 0.00;
	DECLARE Respuesta BOOLEAN DEFAULT FALSE;
    DECLARE Bandera_Upd BOOLEAN DEFAULT FALSE;

 
    SET AmountCompany = (SELECT fund FROM companys WHERE ide = VCompany); 
	SET NewAmountCompany=AmountCompany-VAmount;    
    UPDATE companys SET fund=(AmountCompany-VAmount) WHERE ide=VCompany;
	SET Bandera_Upd=ROW_COUNT();
    
    
    
    IF (Bandera_Upd) THEN 
		
		INSERT INTO funds(`ide`,`company`,`BalanceCompany`,`fund`,`up_date`,`description`,`active`,`idcard`,`User`,`Comment`,`newBalanceCompany`)
		VALUES (NULL,`VCompany`,`AmountCompany`,`VAmount`,`VDate`,'FONDEO A TARJETA','1',`VCard`,`VUser`,`VComment`,`NewAmountCompany`);
      
        	INSERT INTO layout_anchor(`ide`,`idecompany`,`BalanceCompany`,`idcard`,`fund`,`idtype`,`up_date`,`User`,`Comment`,`newBalanceCompany`)
		VALUES (NULL,`VCompany`,`AmountCompany`,`VCard`,`VAmount`,'fund',`VDate`,`VUser`,`VComment`,`NewAmountCompany`);
        set Respuesta=1;
   
    END IF;
    
    SELECT Respuesta;
    
    
END$$

CREATE DEFINER=`admin`@`localhost` PROCEDURE `applyPayCardAPI` (IN `VCard` VARCHAR(8), IN `VAmount` DECIMAL(15,2), IN `VCompany` VARCHAR(4), IN `VDate` VARCHAR(30), IN `VUser` VARCHAR(4), IN `VComment` VARCHAR(250), IN `FoundBy` VARCHAR(10))  BEGIN
	DECLARE AmountCompany FLOAT DEFAULT 0.00;
	DECLARE NewAmountCompany FLOAT DEFAULT 0.00;
	DECLARE Respuesta BOOLEAN DEFAULT FALSE;
    DECLARE Bandera_Upd BOOLEAN DEFAULT FALSE;
	DECLARE Bandera_fund BOOLEAN DEFAULT FALSE;
	DECLARE Bandera_anchor BOOLEAN DEFAULT FALSE;

 
    SET AmountCompany = (SELECT fund FROM companys WHERE ide = VCompany); 
	SET NewAmountCompany=AmountCompany-VAmount;    
    UPDATE companys SET fund=(AmountCompany-VAmount) WHERE ide=VCompany;
	SET Bandera_Upd=ROW_COUNT();
    
    
    
    IF (Bandera_Upd) THEN 
		
		INSERT INTO funds(`ide`,`company`,`BalanceCompany`,`fund`,`up_date`,`description`,`active`,`idcard`,`User`,`Comment`,`newBalanceCompany`,`Founded_by`)
		VALUES (NULL,`VCompany`,`AmountCompany`,`VAmount`,`VDate`,'FONDEO A TARJETA','1',`VCard`,`VUser`,`VComment`,`NewAmountCompany`,`FoundBy`);
		SET Bandera_fund=ROW_COUNT();
		INSERT INTO layout_anchor(`ide`,`idecompany`,`BalanceCompany`,`idcard`,`fund`,`idtype`,`up_date`,`User`,`Comment`,`newBalanceCompany`,`Founded_by`)
		VALUES (NULL,`VCompany`,`AmountCompany`,`VCard`,`VAmount`,'fund',`VDate`,`VUser`,`VComment`,`NewAmountCompany`,`FoundBy`);
        SET Bandera_anchor=ROW_COUNT();
        
        IF(Bandera_fund && Bandera_anchor)
        THEN
			SET Respuesta=TRUE;
        ELSE
			UPDATE companys SET fund=AmountCompany WHERE ide=VCompany;
        END IF;
   
    END IF;
    
    SELECT Respuesta;
    
    
END$$

CREATE DEFINER=`admin`@`localhost` PROCEDURE `applyPayCompany` (IN `VCompanyIn` VARCHAR(4), IN `VCompanyOut` VARCHAR(4), IN `VDate` VARCHAR(30), IN `VUser` VARCHAR(4), IN `VComment` VARCHAR(1000), IN `VTransfer` DECIMAL(15,2), IN `VComision` DECIMAL(15,2), IN `VMontoComision` DECIMAL(15,2), IN `VIVAComision` DECIMAL(15,2), IN `VFund` DECIMAL(15,2))  BEGIN
	DECLARE AmountCompanyIN FLOAT DEFAULT 0.00;
	DECLARE AmountCompanyOUT FLOAT DEFAULT 0.00;

	DECLARE NewAmountCompanyIN FLOAT DEFAULT 0.00;
	DECLARE NewAmountCompanyOUT FLOAT DEFAULT 0.00;

	DECLARE Respuesta BOOLEAN DEFAULT FALSE;
    DECLARE Bandera_IN BOOLEAN DEFAULT FALSE;
	DECLARE Bandera_OUT BOOLEAN DEFAULT FALSE;
	DECLARE Bandera_fund BOOLEAN DEFAULT FALSE;

 
    SET AmountCompanyIN = (SELECT fund FROM companys WHERE ide = VCompanyIn); 
	SET AmountCompanyOUT = (SELECT fund FROM companys WHERE ide = VCompanyOut); 
	
    SET NewAmountCompanyIN=AmountCompanyIN-VFund;    
    SET NewAmountCompanyOUT=AmountCompanyOUT+VFund;    
    
    UPDATE companys SET fund=NewAmountCompanyIN WHERE ide=VCompanyIn;
	SET Bandera_IN=ROW_COUNT();
    UPDATE companys SET fund=NewAmountCompanyOUT WHERE ide=VCompanyOut;
	SET Bandera_OUT=ROW_COUNT();

    
    
    IF (Bandera_IN and Bandera_OUT) THEN 
		
		
		INSERT INTO funds(`ide`, `company`,`BalanceCompany`, `fund`, `up_date`, `description`, `active`,`idcard`,`User`,
        `Comment`,`Transfer_company`,`Monto_comision`,`Iva_Comision`,`Comision`,`newBalanceCompany`,`BalanceAdmin`,`newBalanceAdmin`) 
		VALUES (NULL,`VCompanyOut`,`AmountCompanyOUT`,`VFund`,`VDate`,'FONDEO EMPRESA','1','',`VUser`,
        `VComment`,`VTransfer`,`VMontoComision`,`VIVAComision`,`VComision`,`NewAmountCompanyOUT`,`AmountCompanyIN`,`NewAmountCompanyIN`);
        SET Bandera_fund=ROW_COUNT();
		IF(Bandera_fund)
			THEN
				SET Respuesta=TRUE;
		ELSE	
			UPDATE companys SET fund=AmountCompanyIN WHERE ide=VCompanyIn;
			UPDATE companys SET fund=AmountCompanyOUT WHERE ide=VCompanyOut;   
        END IF;
	
   ELSE	
		UPDATE companys SET fund=AmountCompanyIN WHERE ide=VCompanyIn;
		UPDATE companys SET fund=AmountCompanyOUT WHERE ide=VCompanyOut;        
    END IF;
    
    SELECT Respuesta;
    
    
END$$

CREATE DEFINER=`admin`@`localhost` PROCEDURE `ChangeUser` (IN `CompanyPrevious` VARCHAR(20), IN `UserPrevious` VARCHAR(200), IN `CompanyUser` VARCHAR(10), IN `VUser` VARCHAR(200), IN `VCard` VARCHAR(20), IN `VDate` VARCHAR(20))  BEGIN
DECLARE Bandera_Upd BOOLEAN DEFAULT FALSE;
DECLARE Respuesta BOOLEAN DEFAULT FALSE;
	
	
    UPDATE `cards` SET `user`=VUser,`company`=CompanyUser WHERE substr(`idcard`,-8)=VCard;
	SET Bandera_Upd=ROW_COUNT();
    IF (Bandera_Upd) THEN 
		INSERT INTO `ChangeUser`(`Id`,`Card`,`User`,`Company`,`update_at`) VALUES (NULL,`VCard`,`UserPrevious`,`CompanyPrevious`,`VDate`);
		INSERT INTO `ChangeUser`(`Id`,`Card`,`User`,`Company`,`update_at`) VALUES (NULL,`VCard`,`VUser`,`CompanyUser`,`VDate`);
		SET Respuesta=TRUE;
    END IF;
    SELECT Respuesta;
END$$

CREATE DEFINER=`admin`@`localhost` PROCEDURE `GetBalanceCompany` (IN `VCompany` VARCHAR(4), IN `VStatus` VARCHAR(4), IN `VType` VARCHAR(4))  BEGIN
	DECLARE AmountCompany FLOAT DEFAULT 0.00;
	DECLARE GetBalanceTemp FLOAT DEFAULT 0.00;
	DECLARE GetBalanceTempAplied FLOAT DEFAULT 0.00;
    
    SET AmountCompany = (SELECT fund FROM companys WHERE ide = VCompany); 
	SET GetBalanceTemp= (SELECT IFNULL(Sum(Amount),0) FROM `Funds_Temp` WHERE Status=VStatus AND IdType=VType AND Company=VCompany);
	SET GetBalanceTempAplied= (SELECT IFNULL(Sum(A.Amount),0) FROM `Funds_Temp` AS A INNER JOIN analisis_cards AS B ON A.AuthCode=B.Respuesta WHERE A.Status=VStatus AND A.IdType=VType AND A.Company=VCompany);
	
    SELECT (AmountCompany-(GetBalanceTemp-GetBalanceTempAplied)) AS 	'Balance';
    
    
END$$

CREATE DEFINER=`admin`@`localhost` PROCEDURE `LogUser` (IN `inEmail` VARCHAR(150), IN `inPass` VARCHAR(128))  BEGIN
	SELECT 
		A.ide AS 'ID', 
		A.company AS 'IDCOMPANY',
		UPPER(A.perfil) AS 'NAMEPERFIL',
		UPPER(A.fullname) AS 'FULLNAMEUSER',
		B.Options AS 'PERFILOPTIONS',
        UPPER(C.company) AS 'NAMECOMPANY'
	FROM users AS A
	INNER JOIN profiles AS B ON B.Name = A.perfil
	INNER JOIN companys AS C ON C.ide = A.company
	WHERE A.email = `inEmail` 	AND A.idkey = `inPass` 	AND A.active = 1
	AND B.status = 'ACTIVE';
END$$

CREATE DEFINER=`admin`@`localhost` PROCEDURE `reversePayCard` (IN `VCard` VARCHAR(8), IN `VAmount` DECIMAL(15,2), IN `VCompany` VARCHAR(4), IN `VDate` VARCHAR(30), IN `VUser` VARCHAR(4), IN `VComment` VARCHAR(250))  BEGIN
	DECLARE AmountCompany FLOAT DEFAULT 0.00;
	DECLARE NewAmountCompany FLOAT DEFAULT 0.00;
	DECLARE Respuesta BOOLEAN DEFAULT FALSE;
    DECLARE Bandera_Upd BOOLEAN DEFAULT FALSE;

 
    SET AmountCompany = (SELECT fund FROM companys WHERE ide = VCompany); 
	SET NewAmountCompany=AmountCompany-VAmount;    
    UPDATE companys SET fund=(AmountCompany-VAmount) WHERE ide=VCompany;
	SET Bandera_Upd=ROW_COUNT();
    
    
    
    IF (Bandera_Upd) THEN 
		
		INSERT INTO funds(`ide`,`company`,`BalanceCompany`,`fund`,`up_date`,`description`,`active`,`idcard`,`User`,`Comment`,`newBalanceCompany`)
		VALUES (NULL,`VCompany`,`AmountCompany`,`VAmount`,`VDate`,'REVERSO A TARJETA','1',`VCard`,`VUser`,`VComment`,`NewAmountCompany`);
      
        	INSERT INTO layout_anchor(`ide`,`idecompany`,`BalanceCompany`,`idcard`,`fund`,`idtype`,`up_date`,`User`,`Comment`,`newBalanceCompany`)
		VALUES (NULL,`VCompany`,`AmountCompany`,`VCard`,`VAmount`,'reverse',`VDate`,`VUser`,`VComment`,`NewAmountCompany`);
        set Respuesta=1;
   
    END IF;
    
    SELECT Respuesta;
    
    
END$$

CREATE DEFINER=`admin`@`localhost` PROCEDURE `reversePayCardAPI` (IN `VCard` VARCHAR(8), IN `VAmount` DECIMAL(15,2), IN `VCompany` VARCHAR(4), IN `VDate` VARCHAR(30), IN `VUser` VARCHAR(4), IN `VComment` VARCHAR(250), IN `FoundBy` VARCHAR(10))  BEGIN
	DECLARE AmountCompany FLOAT DEFAULT 0.00;
	DECLARE NewAmountCompany FLOAT DEFAULT 0.00;
	DECLARE Respuesta BOOLEAN DEFAULT FALSE;
    DECLARE Bandera_Upd BOOLEAN DEFAULT FALSE;
	DECLARE Bandera_fund BOOLEAN DEFAULT FALSE;
	DECLARE Bandera_anchor BOOLEAN DEFAULT FALSE;

 
    SET AmountCompany = (SELECT fund FROM companys WHERE ide = VCompany); 
	SET NewAmountCompany=AmountCompany-VAmount;    
    UPDATE companys SET fund=(AmountCompany-VAmount) WHERE ide=VCompany;
	SET Bandera_Upd=ROW_COUNT();
    
    
    
    IF (Bandera_Upd) THEN 
		
		INSERT INTO funds(`ide`,`company`,`BalanceCompany`,`fund`,`up_date`,`description`,`active`,`idcard`,`User`,`Comment`,`newBalanceCompany`,`Founded_by`)
		VALUES (NULL,`VCompany`,`AmountCompany`,`VAmount`,`VDate`,'REVERSO A TARJETA','1',`VCard`,`VUser`,`VComment`,`NewAmountCompany`,`FoundBy`);
		SET Bandera_fund=ROW_COUNT();
		INSERT INTO layout_anchor(`ide`,`idecompany`,`BalanceCompany`,`idcard`,`fund`,`idtype`,`up_date`,`User`,`Comment`,`newBalanceCompany`,`Founded_by`)
		VALUES (NULL,`VCompany`,`AmountCompany`,`VCard`,`VAmount`,'reverse',`VDate`,`VUser`,`VComment`,`NewAmountCompany`,`FoundBy`);
        SET Bandera_anchor=ROW_COUNT();
        
        IF(Bandera_fund && Bandera_anchor)
        	THEN
				SET Respuesta=TRUE;
        ELSE
			UPDATE companys SET fund=AmountCompany WHERE ide=VCompany;
        END IF;
    END IF;
    
    SELECT Respuesta;
    
    
END$$

CREATE DEFINER=`admin`@`localhost` PROCEDURE `reversePayCompany` (IN `VCompanyIn` VARCHAR(4), IN `VCompanyOut` VARCHAR(4), IN `VDate` VARCHAR(30), IN `VUser` VARCHAR(4), IN `VComment` VARCHAR(1000), IN `VFund` DECIMAL(15,2))  BEGIN
	DECLARE AmountCompanyIN FLOAT DEFAULT 0.00;
	DECLARE AmountCompanyOUT FLOAT DEFAULT 0.00;

	DECLARE NewAmountCompanyIN FLOAT DEFAULT 0.00;
	DECLARE NewAmountCompanyOUT FLOAT DEFAULT 0.00;

	DECLARE Respuesta BOOLEAN DEFAULT FALSE;
    DECLARE Bandera_IN BOOLEAN DEFAULT FALSE;
	DECLARE Bandera_OUT BOOLEAN DEFAULT FALSE;
	DECLARE Bandera_fund BOOLEAN DEFAULT FALSE;

 
    SET AmountCompanyIN = (SELECT fund FROM companys WHERE ide = VCompanyIn); 
	SET AmountCompanyOUT = (SELECT fund FROM companys WHERE ide = VCompanyOut); 
	
    SET NewAmountCompanyIN=AmountCompanyIN-VFund;    
    SET NewAmountCompanyOUT=AmountCompanyOUT+VFund;    
    
    UPDATE companys SET fund=NewAmountCompanyIN WHERE ide=VCompanyIn;
	SET Bandera_IN=ROW_COUNT();
    UPDATE companys SET fund=NewAmountCompanyOUT WHERE ide=VCompanyOut;
	SET Bandera_OUT=ROW_COUNT();

    
    
    IF (Bandera_IN and Bandera_OUT) THEN 
		
		
		INSERT INTO funds(`ide`, `company`,`BalanceCompany`, `fund`, `up_date`, `description`, `active`,`idcard`,`User`,
        `Comment`,`newBalanceCompany`,`BalanceAdmin`,`newBalanceAdmin`) 
		VALUES (NULL,`VCompanyOut`,`AmountCompanyOUT`,`VFund`,`VDate`,'REVERSO EMPRESA','1','',`VUser`,
        `VComment`,`NewAmountCompanyOUT`,`AmountCompanyIN`,`NewAmountCompanyIN`);
        SET Bandera_fund=ROW_COUNT();
		IF(Bandera_fund)
			THEN
				SET Respuesta=TRUE;
		ELSE	
			UPDATE companys SET fund=AmountCompanyIN WHERE ide=VCompanyIn;
			UPDATE companys SET fund=AmountCompanyOUT WHERE ide=VCompanyOut;   
        END IF;
	
   ELSE	
		UPDATE companys SET fund=AmountCompanyIN WHERE ide=VCompanyIn;
		UPDATE companys SET fund=AmountCompanyOUT WHERE ide=VCompanyOut;        
    END IF;
    
    SELECT Respuesta;
    
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `access_services`
--

CREATE TABLE `access_services` (
  `Id` int(11) NOT NULL,
  `Key_Empresa` varchar(100) NOT NULL DEFAULT '',
  `Empresa` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `access_services`
--

INSERT INTO `access_services` (`Id`, `Key_Empresa`, `Empresa`) VALUES
(1, '42JY9X0COG1AD61SIE', 'BIONE');

-- --------------------------------------------------------

--
-- Table structure for table `analisis_cards`
--

CREATE TABLE `analisis_cards` (
  `ide` bigint(20) NOT NULL,
  `idecompany` varchar(200) NOT NULL,
  `idcard` varchar(20) NOT NULL,
  `fund` varchar(30) NOT NULL,
  `up_date` varchar(20) NOT NULL,
  `Usuario` varchar(20) NOT NULL,
  `Respuesta` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `analisis_cards`
--

INSERT INTO `analisis_cards` (`ide`, `idecompany`, `idcard`, `fund`, `up_date`, `Usuario`, `Respuesta`) VALUES
(1, '2', '09546008', '-2', '2019-12-05 16:05:37', '2', ''),
(2, '2', '09546008', '-2', '2019-12-05 16:05:44', '2', '00277487'),
(3, '2', '09546008', '1', '2019-12-05 16:06:10', '2', ''),
(4, '2', '09546008', '1', '2019-12-05 16:06:13', '2', '00285255'),
(5, '2', '09546008', '0.3', '2019-12-05 16:06:45', '2', ''),
(6, '2', '09546008', '0.3', '2019-12-05 16:06:47', '2', '00175457'),
(7, '2', '09546008', '0.2', '2019-12-05 16:06:51', '2', ''),
(8, '2', '09546008', '0.2', '2019-12-05 16:06:52', '2', '00064217'),
(9, '2', '09546008', '0.5', '2019-12-05 16:06:59', '2', ''),
(10, '2', '09546008', '0.5', '2019-12-05 16:07:01', '2', '00836960'),
(11, '2', '09546008', '1', '2019-12-10 16:02:19', '2', ''),
(12, '2', '09546008', '1', '2019-12-10 16:02:27', '2', '00316419'),
(13, '2', '09546008', '-1', '2019-12-10 16:03:09', '2', ''),
(14, '2', '09546008', '-1', '2019-12-10 16:03:18', '2', '00307027'),
(15, '2', '09546008', '0.3', '2019-12-10 16:05:01', '2', ''),
(16, '2', '09546008', '0.3', '2019-12-10 16:05:03', '2', '00023146'),
(17, '2', '09546008', '-3', '2019-12-10 16:26:40', '2', ''),
(18, '2', '09546008', '-3', '2019-12-10 16:26:44', '2', '00591008'),
(19, '2', '09546008', '1', '2019-12-16 12:15:15', '2', ''),
(20, '2', '09546008', '1', '2019-12-16 12:15:16', '2', '00245839'),
(21, '2', '09546008', '2', '2019-12-16 12:18:42', '2', ''),
(22, '2', '09546008', '2', '2019-12-16 12:18:43', '2', '00728807'),
(23, '3', '11887002', '150', '2019-12-22 17:29:01', '3', ''),
(24, '3', '11887002', '150', '2019-12-22 17:29:04', '3', '00277502'),
(25, '3', '11554008', '1', '2019-12-23 12:11:12', '3', ''),
(26, '3', '11554008', '1', '2019-12-23 12:11:31', '3', '00010135'),
(27, '3', '11554008', '-1', '2019-12-23 12:12:10', '3', ''),
(28, '3', '11554008', '-1', '2019-12-23 12:12:11', '3', '00082745'),
(29, '3', '11887002', '-10', '2020-01-08 11:15:50', '3', ''),
(30, '3', '11887002', '-10', '2020-01-08 11:15:52', '3', '00790239'),
(31, '3', '11887002', '5', '2020-01-08 11:16:53', '3', ''),
(32, '3', '11887002', '5', '2020-01-08 11:16:54', '3', '00363360'),
(33, '3', '11887002', '5', '2020-01-08 11:35:09', '3', ''),
(34, '3', '11887002', '5', '2020-01-08 11:35:26', '3', '00626729'),
(35, '3', '11887002', '1', '2020-01-23 15:07:01', '3', ''),
(36, '3', '11887002', '1', '2020-01-23 15:07:03', '3', '00795210'),
(37, '3', '11887002', '-1', '2020-01-23 15:07:42', '3', ''),
(38, '3', '11887002', '-1', '2020-01-23 15:07:44', '3', '00265253'),
(39, '3', '09416004', '-65', '2020-03-11 14:40:01', '3', ''),
(40, '3', '09416004', '-65', '2020-03-11 14:40:23', '3', '00087749'),
(41, '3', '03310005', '65', '2020-03-17 09:47:35', '3', ''),
(42, '3', '03310005', '65', '2020-03-17 09:47:48', '3', '00934720'),
(43, '2', '09529004', '-2', '2020-05-13 15:47:23', '2', ''),
(44, '2', '09529004', '-2', '2020-05-13 15:47:26', '2', '00660338'),
(45, '2', '09546008', '1', '2020-08-12 18:11:56', '2', ''),
(46, '2', '09546008', '1', '2020-08-12 18:12:13', '2', '00166428'),
(47, '2', '09546008', '-1', '2020-08-12 18:12:28', '2', ''),
(48, '2', '09546008', '-1', '2020-08-12 18:12:30', '2', '00915373'),
(49, '2', '09546008', '1', '2020-08-21 11:40:52', '2', ''),
(50, '2', '09546008', '1', '2020-08-21 11:40:53', '2', '00619958'),
(51, '2', '09546008', '-1', '2020-08-21 11:41:11', '2', ''),
(52, '2', '09546008', '-1', '2020-08-21 11:41:27', '2', '00505421'),
(53, '2', '09546008', '2', '2020-08-24 13:03:16', '2', ''),
(54, '2', '09546008', '2', '2020-08-24 13:03:16', '2', '00132611'),
(55, '2', '09546008', '-2', '2020-08-24 13:03:47', '2', ''),
(56, '2', '09546008', '-2', '2020-08-24 13:03:49', '2', '00519754'),
(57, '2', '09546008', '3', '2020-08-24 13:08:02', '2', ''),
(58, '2', '09546008', '3', '2020-08-24 13:08:03', '2', '00040316'),
(59, '2', '09546008', '-3', '2020-08-24 13:08:33', '2', ''),
(60, '2', '09546008', '-3', '2020-08-24 13:08:44', '2', '00677138'),
(61, '2', '09546008', '1', '2020-08-24 16:23:05', '2', ''),
(62, '2', '09546008', '1', '2020-08-24 16:23:27', '2', '00998574'),
(63, '2', '09546008', '-1', '2020-08-24 16:23:42', '2', ''),
(64, '2', '09546008', '-1', '2020-08-24 16:23:45', '2', '00829982'),
(65, '2', '09546008', '1', '2020-08-25 13:22:52', '2', ''),
(66, '2', '09546008', '1', '2020-08-25 13:22:54', '2', '00129237'),
(67, '2', '09546008', '2', '2020-08-26 16:33:45', '2', ''),
(68, '2', '09546008', '2', '2020-08-26 16:33:52', '2', '00039578'),
(69, '2', '09546008', '-2', '2020-08-26 16:34:14', '2', ''),
(70, '2', '09546008', '-2', '2020-08-26 16:34:39', '2', '00880262'),
(71, '2', '09546008', '3', '2020-08-26 16:37:26', '2', ''),
(72, '2', '09546008', '3', '2020-08-26 16:37:31', '2', '00112554'),
(73, '2', '09546008', '-4', '2020-08-26 16:39:22', '2', ''),
(74, '2', '09546008', '-4', '2020-08-26 16:39:44', '2', '00709069'),
(75, '2', '09546008', '2', '2020-10-30 17:54:33', '2', ''),
(76, '2', '09546008', '2', '2020-10-30 17:54:34', '2', '00451755'),
(77, '2', '09546008', '0.1', '2020-10-30 17:56:32', '2', ''),
(78, '2', '09546008', '0.1', '2020-10-30 17:56:33', '2', '00235611'),
(79, '2', '09546008', '-2', '2020-10-30 17:57:25', '2', ''),
(80, '2', '09546008', '-2', '2020-10-30 17:57:26', '2', '00045074'),
(81, '2', '09546008', '-1', '2020-11-24 13:09:32', '2', ''),
(82, '2', '09546008', '-1', '2020-11-24 13:09:34', '2', '00519755'),
(83, '2', '09546008', '-1', '2020-11-24 13:14:39', '2', ''),
(84, '2', '09546008', '-1', '2020-11-24 13:14:41', '2', '00932796'),
(85, '2', '09546008', '1', '2021-03-11 13:41:51', '2', ''),
(86, '2', '09546008', '1', '2021-03-11 13:41:52', '2', '635387'),
(87, '2', '09546008', '-1', '2021-03-11 13:43:17', '2', ''),
(88, '2', '09546008', '-1', '2021-03-11 13:43:20', '2', '665376'),
(89, '2', '10152002', '1', '2021-03-11 16:21:15', '2', ''),
(90, '2', '10152002', '1', '2021-03-11 16:21:16', '2', '227521'),
(91, '2', '09546008', '-1', '2021-03-11 16:26:02', '2', ''),
(92, '2', '09546008', '-1', '2021-03-11 16:26:05', '2', '831953'),
(93, '2', '09546008', '1', '2021-03-11 16:27:41', '2', ''),
(94, '2', '09546008', '1', '2021-03-11 16:27:42', '2', '277769'),
(95, '2', '10152002', '-1', '2021-03-11 16:27:59', '2', ''),
(96, '2', '10152002', '-1', '2021-03-11 16:28:00', '2', '011746'),
(97, '2', '09546008', '1', '2021-03-11 17:42:34', '2', ''),
(98, '2', '09546008', '1', '2021-03-11 17:42:35', '2', '192244'),
(99, '2', '09546008', '-1', '2021-03-11 17:43:39', '2', ''),
(100, '2', '09546008', '-1', '2021-03-11 17:43:41', '2', '742174'),
(101, '2', '10152002', '1', '2021-03-17 13:23:59', '2', ''),
(102, '2', '10152002', '1', '2021-03-17 13:24:00', '2', '014052'),
(103, '2', '10152002', '-2', '2021-03-17 13:24:16', '2', ''),
(104, '2', '10152002', '-2', '2021-03-17 13:24:17', '2', '601301'),
(105, '2', '09546008', '2', '2021-05-03 17:24:33', '2', ''),
(106, '2', '09546008', '2', '2021-05-03 17:24:35', '2', '096108'),
(107, '2', '09546008', '-3', '2021-05-03 18:07:59', '2', ''),
(108, '2', '09546008', '-3', '2021-05-03 18:08:01', '2', '086134'),
(109, '2', '3.00', '09546008', '2021-05-10 17:40:28', 'FUND', ''),
(110, '2', '3.00', '09546008', '2021-05-10 17:40:29', 'FUND', 'DEBE INTRODUCIR UNA TARJETA VALIDA EN CANTIDAD DE DIGITOS'),
(111, '2', '3.00', '10152002', '2021-05-10 17:42:53', 'FUND', ''),
(112, '2', '3.00', '10152002', '2021-05-10 17:42:53', 'FUND', 'DEBE INTRODUCIR UNA TARJETA VALIDA EN CANTIDAD DE DIGITOS'),
(113, '2', '3.00', '09546008', '2021-05-10 17:43:37', 'a', ''),
(114, '2', '3.00', '09546008', '2021-05-10 17:43:37', 'a', 'DEBE INTRODUCIR UNA TARJETA VALIDA EN CANTIDAD DE DIGITOS'),
(115, '2', '09546008', '-2', '2021-05-10 17:45:01', '2', ''),
(116, '2', '09546008', '-2', '2021-05-10 17:45:03', '2', '001464'),
(117, '2', '3.00', '10152002', '2021-05-10 17:47:11', 'a', ''),
(118, '2', '3.00', '10152002', '2021-05-10 17:47:11', 'a', 'DEBE INTRODUCIR UNA TARJETA VALIDA EN CANTIDAD DE DIGITOS'),
(119, '2', '5.00', '09546008', '2021-05-10 17:47:31', 'Fund', ''),
(120, '2', '5.00', '09546008', '2021-05-10 17:47:31', 'Fund', 'DEBE INTRODUCIR UNA TARJETA VALIDA EN CANTIDAD DE DIGITOS'),
(121, '2', '5.00', '09546008', '2021-05-10 17:53:01', 'Fund', ''),
(122, '2', '5.00', '09546008', '2021-05-10 17:53:01', 'Fund', 'DEBE INTRODUCIR UNA TARJETA VALIDA EN CANTIDAD DE DIGITOS'),
(123, '2', '09546008', '2', '2021-05-10 17:57:44', '2', ''),
(124, '2', '09546008', '2', '2021-05-10 17:57:45', '2', '548111'),
(125, '2', '09546008', '3', '2021-05-10 18:03:05', '2', ''),
(126, '2', '09546008', '3', '2021-05-10 18:03:07', '2', '762815'),
(127, '2', '09546008', '-3', '2021-05-10 18:03:29', '2', ''),
(128, '2', '09546008', '-3', '2021-05-10 18:03:30', '2', '978127'),
(129, '2', '09546008', '-2', '2021-05-10 18:05:02', '2', ''),
(130, '2', '09546008', '-2', '2021-05-10 18:05:04', '2', '845163'),
(131, '2', '09546008', '4', '2021-05-10 18:05:26', '2', ''),
(132, '2', '09546008', '4', '2021-05-10 18:05:27', '2', '033727'),
(133, '2', '09546008', '-1', '2021-11-04 13:54:08', '2', ''),
(134, '2', '09546008', '-1', '2021-11-04 13:54:10', '2', '252738'),
(135, '2', '09546008', '1', '2021-11-04 13:56:25', '2', ''),
(136, '2', '09546008', '1', '2021-11-04 13:56:28', '2', '074167');

-- --------------------------------------------------------

--
-- Table structure for table `anualidad_tarjetas`
--

CREATE TABLE `anualidad_tarjetas` (
  `Id` int(11) NOT NULL,
  `Card` varchar(16) NOT NULL,
  `Amount` double(15,2) NOT NULL,
  `Date` datetime NOT NULL,
  `Concept` varchar(100) NOT NULL,
  `Restante` double(15,2) NOT NULL,
  `anio_cobrado` varchar(5) NOT NULL,
  `company` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `ide` bigint(20) NOT NULL,
  `idcard` varchar(16) DEFAULT NULL,
  `company` int(11) DEFAULT NULL,
  `city` varchar(200) DEFAULT NULL,
  `picture` varchar(30) DEFAULT NULL,
  `up_date` date DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `user` varchar(1000) NOT NULL DEFAULT '',
  `created_by` varchar(10) NOT NULL DEFAULT '1',
  `Id_Product` varchar(10) NOT NULL DEFAULT '1',
  `Paynet_Comision` varchar(30) NOT NULL DEFAULT '2.5',
  `create_at` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`ide`, `idcard`, `company`, `city`, `picture`, `up_date`, `active`, `user`, `created_by`, `Id_Product`, `Paynet_Comision`, `create_at`) VALUES
(2, '5513530011554008', 3, 'MONTERREY', 'c190319112221.jpg', '2019-09-26', 1, '554008@recargasenergex.mx', '1', '1', '2.5', ''),
(3, '5513530009546008', 2, 'MONTERREY', 'c190319112221.jpg', '2019-10-08', 1, '10152002@energex.mx', '1', '1', '2.5', ''),
(4, '5513530010152002', 2, 'MONTERREY', 'c190319112221.jpg', '2019-10-08', 1, '09546008@energex.mx', '1', '1', '2.5', ''),
(5, '5513530011887002', 3, 'MONTERREY', 'c190319112221.jpg', '2019-11-27', 1, 'jramos@recargasenergex.com', '1', '1', '2.5', ''),
(7, '5513530003310005', 3, 'DOCTOR COSS', 'c190319112221.jpg', '2019-12-04', 1, '03310005@energex.mx', '1', '1', '2.5', ''),
(8, '5513530009416004', 3, 'MONTERREY', 'c190319112221.jpg', '2020-02-04', 1, '03310005@energex.mx', '1', '1', '2.5', ''),
(9, '5513530009529004', 2, 'MONTERREY', 'c190319112221.jpg', '2020-05-13', 1, '11419004@energex.mx', '1', '1', '2.5', ''),
(10, '5513530011419004', 5, 'MONTERREY', 'c190319112221.jpg', '2020-05-20', 1, 'dolares@clusterenergetico.org', '1', '1', '2.5', ''),
(11, '81164001', 3, 'MONTERREY', 'c190319112221.jpg', '2020-09-14', 1, 'valeriacadena@energex.mx', '1', '1', '2.5', ''),
(12, '42424242', 2, 'MONTERREY', 'c190319112221.jpg', '2021-03-11', 1, 'juan@demo.mx', '1', '', '2.5', ''),
(13, '42424242', 6, 'MONTERREY', 'c190319112221.jpg', '2021-06-07', 1, 'dmener', '1', '', '2.5', '');

-- --------------------------------------------------------

--
-- Table structure for table `cards_changes`
--

CREATE TABLE `cards_changes` (
  `Id` int(11) NOT NULL,
  `Company` varchar(10) NOT NULL,
  `Card` varchar(16) NOT NULL DEFAULT '',
  `Reason` varchar(1000) NOT NULL DEFAULT '',
  `CompanyNew` varchar(10) NOT NULL DEFAULT '',
  `DateR` datetime NOT NULL,
  `UserR` varchar(10) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cards_changes`
--

INSERT INTO `cards_changes` (`Id`, `Company`, `Card`, `Reason`, `CompanyNew`, `DateR`, `UserR`) VALUES
(1, '2', '09546008', 'DEMO', '6', '2021-05-04 11:45:48', '7'),
(2, '6', '09546008', 'REASIGNACION', '2', '2021-05-04 11:46:07', '7'),
(3, '2', '09546008', 'PRUEBA', '6', '2021-05-06 11:09:23', '7'),
(4, '6', '09546008', 'DEMO', '2', '2021-05-06 11:09:38', '7');

-- --------------------------------------------------------

--
-- Table structure for table `card_temp`
--

CREATE TABLE `card_temp` (
  `Id` bigint(20) NOT NULL,
  `idcard` varchar(20) NOT NULL,
  `company` varchar(10) NOT NULL,
  `city` varchar(200) NOT NULL,
  `picture` varchar(100) NOT NULL,
  `up_date` varchar(100) NOT NULL,
  `active` varchar(20) NOT NULL,
  `user` varchar(250) NOT NULL,
  `validator` varchar(100) NOT NULL,
  `Id_Product` varchar(10) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ciudades`
--

CREATE TABLE `ciudades` (
  `id` int(11) NOT NULL,
  `estado_id` int(11) NOT NULL COMMENT 'Relación con estados',
  `clave` varchar(3) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabla de Municipios de la Republica Mexicana';

--
-- Dumping data for table `ciudades`
--

INSERT INTO `ciudades` (`id`, `estado_id`, `clave`, `nombre`, `activo`) VALUES
(1, 1, '001', 'Aguascalientes', 1),
(2, 1, '002', 'Asientos', 1),
(3, 1, '003', 'Calvillo', 1),
(4, 1, '004', 'Cosío', 1),
(5, 1, '005', 'Jesús María', 1),
(6, 1, '006', 'Pabellón de Arteaga', 1),
(7, 1, '007', 'Rincón de Romos', 1),
(8, 1, '008', 'San José de Gracia', 1),
(9, 1, '009', 'Tepezalá', 1),
(10, 1, '010', 'El Llano', 1),
(11, 1, '011', 'San Francisco de los Romo', 1),
(12, 2, '001', 'Ensenada', 1),
(13, 2, '002', 'Mexicali', 1),
(14, 2, '003', 'Tecate', 1),
(15, 2, '004', 'Tijuana', 1),
(16, 2, '005', 'Playas de Rosarito', 1),
(17, 3, '001', 'Comondú', 1),
(18, 3, '002', 'Mulegé', 1),
(19, 3, '003', 'La Paz', 1),
(20, 3, '008', 'Los Cabos', 1),
(21, 3, '009', 'Loreto', 1),
(22, 4, '001', 'Calkiní', 1),
(23, 4, '002', 'Campeche', 1),
(24, 4, '003', 'Carmen', 1),
(25, 4, '004', 'Champotón', 1),
(26, 4, '005', 'Hecelchakán', 1),
(27, 4, '006', 'Hopelchén', 1),
(28, 4, '007', 'Palizada', 1),
(29, 4, '008', 'Tenabo', 1),
(30, 4, '009', 'Escárcega', 1),
(31, 4, '010', 'Calakmul', 1),
(32, 4, '011', 'Candelaria', 1),
(33, 5, '001', 'Abasolo', 1),
(34, 5, '002', 'Acuña', 1),
(35, 5, '003', 'Allende', 1),
(36, 5, '004', 'Arteaga', 1),
(37, 5, '005', 'Candela', 1),
(38, 5, '006', 'Castaños', 1),
(39, 5, '007', 'Cuatro Ciénegas', 1),
(40, 5, '008', 'Escobedo', 1),
(41, 5, '009', 'Francisco I. Madero', 1),
(42, 5, '010', 'Frontera', 1),
(43, 5, '011', 'General Cepeda', 1),
(44, 5, '012', 'Guerrero', 1),
(45, 5, '013', 'Hidalgo', 1),
(46, 5, '014', 'Jiménez', 1),
(47, 5, '015', 'Juárez', 1),
(48, 5, '016', 'Lamadrid', 1),
(49, 5, '017', 'Matamoros', 1),
(50, 5, '018', 'Monclova', 1),
(51, 5, '019', 'Morelos', 1),
(52, 5, '020', 'Múzquiz', 1),
(53, 5, '021', 'Nadadores', 1),
(54, 5, '022', 'Nava', 1),
(55, 5, '023', 'Ocampo', 1),
(56, 5, '024', 'Parras', 1),
(57, 5, '025', 'Piedras Negras', 1),
(58, 5, '026', 'Progreso', 1),
(59, 5, '027', 'Ramos Arizpe', 1),
(60, 5, '028', 'Sabinas', 1),
(61, 5, '029', 'Sacramento', 1),
(62, 5, '030', 'Saltillo', 1),
(63, 5, '031', 'San Buenaventura', 1),
(64, 5, '032', 'San Juan de Sabinas', 1),
(65, 5, '033', 'San Pedro', 1),
(66, 5, '034', 'Sierra Mojada', 1),
(67, 5, '035', 'Torreón', 1),
(68, 5, '036', 'Viesca', 1),
(69, 5, '037', 'Villa Unión', 1),
(70, 5, '038', 'Zaragoza', 1),
(71, 6, '001', 'Armería', 1),
(72, 6, '002', 'Colima', 1),
(73, 6, '003', 'Comala', 1),
(74, 6, '004', 'Coquimatlán', 1),
(75, 6, '005', 'Cuauhtémoc', 1),
(76, 6, '006', 'Ixtlahuacán', 1),
(77, 6, '007', 'Manzanillo', 1),
(78, 6, '008', 'Minatitlán', 1),
(79, 6, '009', 'Tecomán', 1),
(80, 6, '010', 'Villa de Álvarez', 1),
(81, 7, '001', 'Acacoyagua', 1),
(82, 7, '002', 'Acala', 1),
(83, 7, '003', 'Acapetahua', 1),
(84, 7, '004', 'Altamirano', 1),
(85, 7, '005', 'Amatán', 1),
(86, 7, '006', 'Amatenango de la Frontera', 1),
(87, 7, '007', 'Amatenango del Valle', 1),
(88, 7, '008', 'Angel Albino Corzo', 1),
(89, 7, '009', 'Arriaga', 1),
(90, 7, '010', 'Bejucal de Ocampo', 1),
(91, 7, '011', 'Bella Vista', 1),
(92, 7, '012', 'Berriozábal', 1),
(93, 7, '013', 'Bochil', 1),
(94, 7, '014', 'El Bosque', 1),
(95, 7, '015', 'Cacahoatán', 1),
(96, 7, '016', 'Catazajá', 1),
(97, 7, '017', 'Cintalapa', 1),
(98, 7, '018', 'Coapilla', 1),
(99, 7, '019', 'Comitán de Domínguez', 1),
(100, 7, '020', 'La Concordia', 1),
(101, 7, '021', 'Copainalá', 1),
(102, 7, '022', 'Chalchihuitán', 1),
(103, 7, '023', 'Chamula', 1),
(104, 7, '024', 'Chanal', 1),
(105, 7, '025', 'Chapultenango', 1),
(106, 7, '026', 'Chenalhó', 1),
(107, 7, '027', 'Chiapa de Corzo', 1),
(108, 7, '028', 'Chiapilla', 1),
(109, 7, '029', 'Chicoasén', 1),
(110, 7, '030', 'Chicomuselo', 1),
(111, 7, '031', 'Chilón', 1),
(112, 7, '032', 'Escuintla', 1),
(113, 7, '033', 'Francisco León', 1),
(114, 7, '034', 'Frontera Comalapa', 1),
(115, 7, '035', 'Frontera Hidalgo', 1),
(116, 7, '036', 'La Grandeza', 1),
(117, 7, '037', 'Huehuetán', 1),
(118, 7, '038', 'Huixtán', 1),
(119, 7, '039', 'Huitiupán', 1),
(120, 7, '040', 'Huixtla', 1),
(121, 7, '041', 'La Independencia', 1),
(122, 7, '042', 'Ixhuatán', 1),
(123, 7, '043', 'Ixtacomitán', 1),
(124, 7, '044', 'Ixtapa', 1),
(125, 7, '045', 'Ixtapangajoya', 1),
(126, 7, '046', 'Jiquipilas', 1),
(127, 7, '047', 'Jitotol', 1),
(128, 7, '048', 'Juárez', 1),
(129, 7, '049', 'Larráinzar', 1),
(130, 7, '050', 'La Libertad', 1),
(131, 7, '051', 'Mapastepec', 1),
(132, 7, '052', 'Las Margaritas', 1),
(133, 7, '053', 'Mazapa de Madero', 1),
(134, 7, '054', 'Mazatán', 1),
(135, 7, '055', 'Metapa', 1),
(136, 7, '056', 'Mitontic', 1),
(137, 7, '057', 'Motozintla', 1),
(138, 7, '058', 'Nicolás Ruíz', 1),
(139, 7, '059', 'Ocosingo', 1),
(140, 7, '060', 'Ocotepec', 1),
(141, 7, '061', 'Ocozocoautla de Espinosa', 1),
(142, 7, '062', 'Ostuacán', 1),
(143, 7, '063', 'Osumacinta', 1),
(144, 7, '064', 'Oxchuc', 1),
(145, 7, '065', 'Palenque', 1),
(146, 7, '066', 'Pantelhó', 1),
(147, 7, '067', 'Pantepec', 1),
(148, 7, '068', 'Pichucalco', 1),
(149, 7, '069', 'Pijijiapan', 1),
(150, 7, '070', 'El Porvenir', 1),
(151, 7, '071', 'Villa Comaltitlán', 1),
(152, 7, '072', 'Pueblo Nuevo Solistahuacán', 1),
(153, 7, '073', 'Rayón', 1),
(154, 7, '074', 'Reforma', 1),
(155, 7, '075', 'Las Rosas', 1),
(156, 7, '076', 'Sabanilla', 1),
(157, 7, '077', 'Salto de Agua', 1),
(158, 7, '078', 'San Cristóbal de las Casas', 1),
(159, 7, '079', 'San Fernando', 1),
(160, 7, '080', 'Siltepec', 1),
(161, 7, '081', 'Simojovel', 1),
(162, 7, '082', 'Sitalá', 1),
(163, 7, '083', 'Socoltenango', 1),
(164, 7, '084', 'Solosuchiapa', 1),
(165, 7, '085', 'Soyaló', 1),
(166, 7, '086', 'Suchiapa', 1),
(167, 7, '087', 'Suchiate', 1),
(168, 7, '088', 'Sunuapa', 1),
(169, 7, '089', 'Tapachula', 1),
(170, 7, '090', 'Tapalapa', 1),
(171, 7, '091', 'Tapilula', 1),
(172, 7, '092', 'Tecpatán', 1),
(173, 7, '093', 'Tenejapa', 1),
(174, 7, '094', 'Teopisca', 1),
(175, 7, '096', 'Tila', 1),
(176, 7, '097', 'Tonalá', 1),
(177, 7, '098', 'Totolapa', 1),
(178, 7, '099', 'La Trinitaria', 1),
(179, 7, '100', 'Tumbalá', 1),
(180, 7, '101', 'Tuxtla Gutiérrez', 1),
(181, 7, '102', 'Tuxtla Chico', 1),
(182, 7, '103', 'Tuzantán', 1),
(183, 7, '104', 'Tzimol', 1),
(184, 7, '105', 'Unión Juárez', 1),
(185, 7, '106', 'Venustiano Carranza', 1),
(186, 7, '107', 'Villa Corzo', 1),
(187, 7, '108', 'Villaflores', 1),
(188, 7, '109', 'Yajalón', 1),
(189, 7, '110', 'San Lucas', 1),
(190, 7, '111', 'Zinacantán', 1),
(191, 7, '112', 'San Juan Cancuc', 1),
(192, 7, '113', 'Aldama', 1),
(193, 7, '114', 'Benemérito de las Américas', 1),
(194, 7, '115', 'Maravilla Tenejapa', 1),
(195, 7, '116', 'Marqués de Comillas', 1),
(196, 7, '117', 'Montecristo de Guerrero', 1),
(197, 7, '118', 'San Andrés Duraznal', 1),
(198, 7, '119', 'Santiago el Pinar', 1),
(199, 8, '001', 'Ahumada', 1),
(200, 8, '002', 'Aldama', 1),
(201, 8, '003', 'Allende', 1),
(202, 8, '004', 'Aquiles Serdán', 1),
(203, 8, '005', 'Ascensión', 1),
(204, 8, '006', 'Bachíniva', 1),
(205, 8, '007', 'Balleza', 1),
(206, 8, '008', 'Batopilas', 1),
(207, 8, '009', 'Bocoyna', 1),
(208, 8, '010', 'Buenaventura', 1),
(209, 8, '011', 'Camargo', 1),
(210, 8, '012', 'Carichí', 1),
(211, 8, '013', 'Casas Grandes', 1),
(212, 8, '014', 'Coronado', 1),
(213, 8, '015', 'Coyame del Sotol', 1),
(214, 8, '016', 'La Cruz', 1),
(215, 8, '017', 'Cuauhtémoc', 1),
(216, 8, '018', 'Cusihuiriachi', 1),
(217, 8, '019', 'Chihuahua', 1),
(218, 8, '020', 'Chínipas', 1),
(219, 8, '021', 'Delicias', 1),
(220, 8, '022', 'Dr. Belisario Domínguez', 1),
(221, 8, '023', 'Galeana', 1),
(222, 8, '024', 'Santa Isabel', 1),
(223, 8, '025', 'Gómez Farías', 1),
(224, 8, '026', 'Gran Morelos', 1),
(225, 8, '027', 'Guachochi', 1),
(226, 8, '028', 'Guadalupe', 1),
(227, 8, '029', 'Guadalupe y Calvo', 1),
(228, 8, '030', 'Guazapares', 1),
(229, 8, '031', 'Guerrero', 1),
(230, 8, '032', 'Hidalgo del Parral', 1),
(231, 8, '033', 'Huejotitán', 1),
(232, 8, '034', 'Ignacio Zaragoza', 1),
(233, 8, '035', 'Janos', 1),
(234, 8, '036', 'Jiménez', 1),
(235, 8, '037', 'Juárez', 1),
(236, 8, '038', 'Julimes', 1),
(237, 8, '039', 'López', 1),
(238, 8, '040', 'Madera', 1),
(239, 8, '041', 'Maguarichi', 1),
(240, 8, '042', 'Manuel Benavides', 1),
(241, 8, '043', 'Matachí', 1),
(242, 8, '044', 'Matamoros', 1),
(243, 8, '045', 'Meoqui', 1),
(244, 8, '046', 'Morelos', 1),
(245, 8, '047', 'Moris', 1),
(246, 8, '048', 'Namiquipa', 1),
(247, 8, '049', 'Nonoava', 1),
(248, 8, '050', 'Nuevo Casas Grandes', 1),
(249, 8, '051', 'Ocampo', 1),
(250, 8, '052', 'Ojinaga', 1),
(251, 8, '053', 'Praxedis G. Guerrero', 1),
(252, 8, '054', 'Riva Palacio', 1),
(253, 8, '055', 'Rosales', 1),
(254, 8, '056', 'Rosario', 1),
(255, 8, '057', 'San Francisco de Borja', 1),
(256, 8, '058', 'San Francisco de Conchos', 1),
(257, 8, '059', 'San Francisco del Oro', 1),
(258, 8, '060', 'Santa Bárbara', 1),
(259, 8, '061', 'Satevó', 1),
(260, 8, '062', 'Saucillo', 1),
(261, 8, '063', 'Temósachic', 1),
(262, 8, '064', 'El Tule', 1),
(263, 8, '065', 'Urique', 1),
(264, 8, '066', 'Uruachi', 1),
(265, 8, '067', 'Valle de Zaragoza', 1),
(266, 9, '002', 'Azcapotzalco', 1),
(267, 9, '003', 'Coyoacán', 1),
(268, 9, '004', 'Cuajimalpa de Morelos', 1),
(269, 9, '005', 'Gustavo A. Madero', 1),
(270, 9, '006', 'Iztacalco', 1),
(271, 9, '007', 'Iztapalapa', 1),
(272, 9, '008', 'La Magdalena Contreras', 1),
(273, 9, '009', 'Milpa Alta', 1),
(274, 9, '010', 'Álvaro Obregón', 1),
(275, 9, '011', 'Tláhuac', 1),
(276, 9, '012', 'Tlalpan', 1),
(277, 9, '013', 'Xochimilco', 1),
(278, 9, '014', 'Benito Juárez', 1),
(279, 9, '015', 'Cuauhtémoc', 1),
(280, 9, '016', 'Miguel Hidalgo', 1),
(281, 9, '017', 'Venustiano Carranza', 1),
(282, 10, '001', 'Canatlán', 1),
(283, 10, '002', 'Canelas', 1),
(284, 10, '003', 'Coneto de Comonfort', 1),
(285, 10, '004', 'Cuencamé', 1),
(286, 10, '005', 'Durango', 1),
(287, 10, '006', 'General Simón Bolívar', 1),
(288, 10, '007', 'Gómez Palacio', 1),
(289, 10, '008', 'Guadalupe Victoria', 1),
(290, 10, '009', 'Guanaceví', 1),
(291, 10, '010', 'Hidalgo', 1),
(292, 10, '011', 'Indé', 1),
(293, 10, '012', 'Lerdo', 1),
(294, 10, '013', 'Mapimí', 1),
(295, 10, '014', 'Mezquital', 1),
(296, 10, '015', 'Nazas', 1),
(297, 10, '016', 'Nombre de Dios', 1),
(298, 10, '017', 'Ocampo', 1),
(299, 10, '018', 'El Oro', 1),
(300, 10, '019', 'Otáez', 1),
(301, 10, '020', 'Pánuco de Coronado', 1),
(302, 10, '021', 'Peñón Blanco', 1),
(303, 10, '022', 'Poanas', 1),
(304, 10, '023', 'Pueblo Nuevo', 1),
(305, 10, '024', 'Rodeo', 1),
(306, 10, '025', 'San Bernardo', 1),
(307, 10, '026', 'San Dimas', 1),
(308, 10, '027', 'San Juan de Guadalupe', 1),
(309, 10, '028', 'San Juan del Río', 1),
(310, 10, '029', 'San Luis del Cordero', 1),
(311, 10, '030', 'San Pedro del Gallo', 1),
(312, 10, '031', 'Santa Clara', 1),
(313, 10, '032', 'Santiago Papasquiaro', 1),
(314, 10, '033', 'Súchil', 1),
(315, 10, '034', 'Tamazula', 1),
(316, 10, '035', 'Tepehuanes', 1),
(317, 10, '036', 'Tlahualilo', 1),
(318, 10, '037', 'Topia', 1),
(319, 10, '038', 'Vicente Guerrero', 1),
(320, 10, '039', 'Nuevo Ideal', 1),
(321, 11, '001', 'Abasolo', 1),
(322, 11, '002', 'Acámbaro', 1),
(323, 11, '003', 'San Miguel de Allende', 1),
(324, 11, '004', 'Apaseo el Alto', 1),
(325, 11, '005', 'Apaseo el Grande', 1),
(326, 11, '006', 'Atarjea', 1),
(327, 11, '007', 'Celaya', 1),
(328, 11, '008', 'Manuel Doblado', 1),
(329, 11, '009', 'Comonfort', 1),
(330, 11, '010', 'Coroneo', 1),
(331, 11, '011', 'Cortazar', 1),
(332, 11, '012', 'Cuerámaro', 1),
(333, 11, '013', 'Doctor Mora', 1),
(334, 11, '014', 'Dolores Hidalgo Cuna de la Independencia Nacional', 1),
(335, 11, '015', 'Guanajuato', 1),
(336, 11, '016', 'Huanímaro', 1),
(337, 11, '017', 'Irapuato', 1),
(338, 11, '018', 'Jaral del Progreso', 1),
(339, 11, '019', 'Jerécuaro', 1),
(340, 11, '020', 'León', 1),
(341, 11, '021', 'Moroleón', 1),
(342, 11, '022', 'Ocampo', 1),
(343, 11, '023', 'Pénjamo', 1),
(344, 11, '024', 'Pueblo Nuevo', 1),
(345, 11, '025', 'Purísima del Rincón', 1),
(346, 11, '026', 'Romita', 1),
(347, 11, '027', 'Salamanca', 1),
(348, 11, '028', 'Salvatierra', 1),
(349, 11, '029', 'San Diego de la Unión', 1),
(350, 11, '030', 'San Felipe', 1),
(351, 11, '031', 'San Francisco del Rincón', 1),
(352, 11, '032', 'San José Iturbide', 1),
(353, 11, '033', 'San Luis de la Paz', 1),
(354, 11, '034', 'Santa Catarina', 1),
(355, 11, '035', 'Santa Cruz de Juventino Rosas', 1),
(356, 11, '036', 'Santiago Maravatío', 1),
(357, 11, '037', 'Silao de la Victoria', 1),
(358, 11, '038', 'Tarandacuao', 1),
(359, 11, '039', 'Tarimoro', 1),
(360, 11, '040', 'Tierra Blanca', 1),
(361, 11, '041', 'Uriangato', 1),
(362, 11, '042', 'Valle de Santiago', 1),
(363, 11, '043', 'Victoria', 1),
(364, 11, '044', 'Villagrán', 1),
(365, 11, '045', 'Xichú', 1),
(366, 11, '046', 'Yuriria', 1),
(367, 12, '001', 'Acapulco de Juárez', 1),
(368, 12, '002', 'Ahuacuotzingo', 1),
(369, 12, '003', 'Ajuchitlán del Progreso', 1),
(370, 12, '004', 'Alcozauca de Guerrero', 1),
(371, 12, '005', 'Alpoyeca', 1),
(372, 12, '006', 'Apaxtla', 1),
(373, 12, '007', 'Arcelia', 1),
(374, 12, '008', 'Atenango del Río', 1),
(375, 12, '009', 'Atlamajalcingo del Monte', 1),
(376, 12, '010', 'Atlixtac', 1),
(377, 12, '011', 'Atoyac de Álvarez', 1),
(378, 12, '012', 'Ayutla de los Libres', 1),
(379, 12, '013', 'Azoyú', 1),
(380, 12, '014', 'Benito Juárez', 1),
(381, 12, '015', 'Buenavista de Cuéllar', 1),
(382, 12, '016', 'Coahuayutla de José María Izazaga', 1),
(383, 12, '017', 'Cocula', 1),
(384, 12, '018', 'Copala', 1),
(385, 12, '019', 'Copalillo', 1),
(386, 12, '020', 'Copanatoyac', 1),
(387, 12, '021', 'Coyuca de Benítez', 1),
(388, 12, '022', 'Coyuca de Catalán', 1),
(389, 12, '023', 'Cuajinicuilapa', 1),
(390, 12, '024', 'Cualác', 1),
(391, 12, '025', 'Cuautepec', 1),
(392, 12, '026', 'Cuetzala del Progreso', 1),
(393, 12, '027', 'Cutzamala de Pinzón', 1),
(394, 12, '028', 'Chilapa de Álvarez', 1),
(395, 12, '029', 'Chilpancingo de los Bravo', 1),
(396, 12, '030', 'Florencio Villarreal', 1),
(397, 12, '031', 'General Canuto A. Neri', 1),
(398, 12, '032', 'General Heliodoro Castillo', 1),
(399, 12, '033', 'Huamuxtitlán', 1),
(400, 12, '034', 'Huitzuco de los Figueroa', 1),
(401, 12, '035', 'Iguala de la Independencia', 1),
(402, 12, '036', 'Igualapa', 1),
(403, 12, '037', 'Ixcateopan de Cuauhtémoc', 1),
(404, 12, '038', 'Zihuatanejo de Azueta', 1),
(405, 12, '039', 'Juan R. Escudero', 1),
(406, 12, '040', 'Leonardo Bravo', 1),
(407, 12, '041', 'Malinaltepec', 1),
(408, 12, '042', 'Mártir de Cuilapan', 1),
(409, 12, '043', 'Metlatónoc', 1),
(410, 12, '044', 'Mochitlán', 1),
(411, 12, '045', 'Olinalá', 1),
(412, 12, '046', 'Ometepec', 1),
(413, 12, '047', 'Pedro Ascencio Alquisiras', 1),
(414, 12, '048', 'Petatlán', 1),
(415, 12, '049', 'Pilcaya', 1),
(416, 12, '050', 'Pungarabato', 1),
(417, 12, '051', 'Quechultenango', 1),
(418, 12, '052', 'San Luis Acatlán', 1),
(419, 12, '053', 'San Marcos', 1),
(420, 12, '054', 'San Miguel Totolapan', 1),
(421, 12, '055', 'Taxco de Alarcón', 1),
(422, 12, '056', 'Tecoanapa', 1),
(423, 12, '057', 'Técpan de Galeana', 1),
(424, 12, '058', 'Teloloapan', 1),
(425, 12, '059', 'Tepecoacuilco de Trujano', 1),
(426, 12, '060', 'Tetipac', 1),
(427, 12, '061', 'Tixtla de Guerrero', 1),
(428, 12, '062', 'Tlacoachistlahuaca', 1),
(429, 12, '063', 'Tlacoapa', 1),
(430, 12, '064', 'Tlalchapa', 1),
(431, 12, '065', 'Tlalixtaquilla de Maldonado', 1),
(432, 12, '066', 'Tlapa de Comonfort', 1),
(433, 12, '067', 'Tlapehuala', 1),
(434, 12, '068', 'La Unión de Isidoro Montes de Oca', 1),
(435, 12, '069', 'Xalpatláhuac', 1),
(436, 12, '070', 'Xochihuehuetlán', 1),
(437, 12, '071', 'Xochistlahuaca', 1),
(438, 12, '072', 'Zapotitlán Tablas', 1),
(439, 12, '073', 'Zirándaro', 1),
(440, 12, '074', 'Zitlala', 1),
(441, 12, '075', 'Eduardo Neri', 1),
(442, 12, '076', 'Acatepec', 1),
(443, 12, '077', 'Marquelia', 1),
(444, 12, '078', 'Cochoapa el Grande', 1),
(445, 12, '079', 'José Joaquín de Herrera', 1),
(446, 12, '080', 'Juchitán', 1),
(447, 12, '081', 'Iliatenco', 1),
(448, 13, '001', 'Acatlán', 1),
(449, 13, '002', 'Acaxochitlán', 1),
(450, 13, '003', 'Actopan', 1),
(451, 13, '004', 'Agua Blanca de Iturbide', 1),
(452, 13, '005', 'Ajacuba', 1),
(453, 13, '006', 'Alfajayucan', 1),
(454, 13, '007', 'Almoloya', 1),
(455, 13, '008', 'Apan', 1),
(456, 13, '009', 'El Arenal', 1),
(457, 13, '010', 'Atitalaquia', 1),
(458, 13, '011', 'Atlapexco', 1),
(459, 13, '012', 'Atotonilco el Grande', 1),
(460, 13, '013', 'Atotonilco de Tula', 1),
(461, 13, '014', 'Calnali', 1),
(462, 13, '015', 'Cardonal', 1),
(463, 13, '016', 'Cuautepec de Hinojosa', 1),
(464, 13, '017', 'Chapantongo', 1),
(465, 13, '018', 'Chapulhuacán', 1),
(466, 13, '019', 'Chilcuautla', 1),
(467, 13, '020', 'Eloxochitlán', 1),
(468, 13, '021', 'Emiliano Zapata', 1),
(469, 13, '022', 'Epazoyucan', 1),
(470, 13, '023', 'Francisco I. Madero', 1),
(471, 13, '024', 'Huasca de Ocampo', 1),
(472, 13, '025', 'Huautla', 1),
(473, 13, '026', 'Huazalingo', 1),
(474, 13, '027', 'Huehuetla', 1),
(475, 13, '028', 'Huejutla de Reyes', 1),
(476, 13, '029', 'Huichapan', 1),
(477, 13, '030', 'Ixmiquilpan', 1),
(478, 13, '031', 'Jacala de Ledezma', 1),
(479, 13, '032', 'Jaltocán', 1),
(480, 13, '033', 'Juárez Hidalgo', 1),
(481, 13, '034', 'Lolotla', 1),
(482, 13, '035', 'Metepec', 1),
(483, 13, '036', 'San Agustín Metzquititlán', 1),
(484, 13, '037', 'Metztitlán', 1),
(485, 13, '038', 'Mineral del Chico', 1),
(486, 13, '039', 'Mineral del Monte', 1),
(487, 13, '040', 'La Misión', 1),
(488, 13, '041', 'Mixquiahuala de Juárez', 1),
(489, 13, '042', 'Molango de Escamilla', 1),
(490, 13, '043', 'Nicolás Flores', 1),
(491, 13, '044', 'Nopala de Villagrán', 1),
(492, 13, '045', 'Omitlán de Juárez', 1),
(493, 13, '046', 'San Felipe Orizatlán', 1),
(494, 13, '047', 'Pacula', 1),
(495, 13, '048', 'Pachuca de Soto', 1),
(496, 13, '049', 'Pisaflores', 1),
(497, 13, '050', 'Progreso de Obregón', 1),
(498, 13, '051', 'Mineral de la Reforma', 1),
(499, 13, '052', 'San Agustín Tlaxiaca', 1),
(500, 13, '053', 'San Bartolo Tutotepec', 1),
(501, 13, '054', 'San Salvador', 1),
(502, 13, '055', 'Santiago de Anaya', 1),
(503, 13, '056', 'Santiago Tulantepec de Lugo Guerrero', 1),
(504, 13, '057', 'Singuilucan', 1),
(505, 13, '058', 'Tasquillo', 1),
(506, 13, '059', 'Tecozautla', 1),
(507, 13, '060', 'Tenango de Doria', 1),
(508, 13, '061', 'Tepeapulco', 1),
(509, 13, '062', 'Tepehuacán de Guerrero', 1),
(510, 13, '063', 'Tepeji del Río de Ocampo', 1),
(511, 13, '064', 'Tepetitlán', 1),
(512, 13, '065', 'Tetepango', 1),
(513, 13, '066', 'Villa de Tezontepec', 1),
(514, 13, '067', 'Tezontepec de Aldama', 1),
(515, 13, '068', 'Tianguistengo', 1),
(516, 13, '069', 'Tizayuca', 1),
(517, 13, '070', 'Tlahuelilpan', 1),
(518, 13, '071', 'Tlahuiltepa', 1),
(519, 13, '072', 'Tlanalapa', 1),
(520, 13, '073', 'Tlanchinol', 1),
(521, 13, '074', 'Tlaxcoapan', 1),
(522, 13, '075', 'Tolcayuca', 1),
(523, 13, '076', 'Tula de Allende', 1),
(524, 13, '077', 'Tulancingo de Bravo', 1),
(525, 13, '078', 'Xochiatipan', 1),
(526, 13, '079', 'Xochicoatlán', 1),
(527, 13, '080', 'Yahualica', 1),
(528, 13, '081', 'Zacualtipán de Ángeles', 1),
(529, 13, '082', 'Zapotlán de Juárez', 1),
(530, 13, '083', 'Zempoala', 1),
(531, 13, '084', 'Zimapán', 1),
(532, 14, '001', 'Acatic', 1),
(533, 14, '002', 'Acatlán de Juárez', 1),
(534, 14, '003', 'Ahualulco de Mercado', 1),
(535, 14, '004', 'Amacueca', 1),
(536, 14, '005', 'Amatitán', 1),
(537, 14, '006', 'Ameca', 1),
(538, 14, '007', 'San Juanito de Escobedo', 1),
(539, 14, '008', 'Arandas', 1),
(540, 14, '009', 'El Arenal', 1),
(541, 14, '010', 'Atemajac de Brizuela', 1),
(542, 14, '011', 'Atengo', 1),
(543, 14, '012', 'Atenguillo', 1),
(544, 14, '013', 'Atotonilco el Alto', 1),
(545, 14, '014', 'Atoyac', 1),
(546, 14, '015', 'Autlán de Navarro', 1),
(547, 14, '016', 'Ayotlán', 1),
(548, 14, '017', 'Ayutla', 1),
(549, 14, '018', 'La Barca', 1),
(550, 14, '019', 'Bolaños', 1),
(551, 14, '020', 'Cabo Corrientes', 1),
(552, 14, '021', 'Casimiro Castillo', 1),
(553, 14, '022', 'Cihuatlán', 1),
(554, 14, '023', 'Zapotlán el Grande', 1),
(555, 14, '024', 'Cocula', 1),
(556, 14, '025', 'Colotlán', 1),
(557, 14, '026', 'Concepción de Buenos Aires', 1),
(558, 14, '027', 'Cuautitlán de García Barragán', 1),
(559, 14, '028', 'Cuautla', 1),
(560, 14, '029', 'Cuquío', 1),
(561, 14, '030', 'Chapala', 1),
(562, 14, '031', 'Chimaltitán', 1),
(563, 14, '032', 'Chiquilistlán', 1),
(564, 14, '033', 'Degollado', 1),
(565, 14, '034', 'Ejutla', 1),
(566, 14, '035', 'Encarnación de Díaz', 1),
(567, 14, '036', 'Etzatlán', 1),
(568, 14, '037', 'El Grullo', 1),
(569, 14, '038', 'Guachinango', 1),
(570, 14, '039', 'Guadalajara', 1),
(571, 14, '040', 'Hostotipaquillo', 1),
(572, 14, '041', 'Huejúcar', 1),
(573, 14, '042', 'Huejuquilla el Alto', 1),
(574, 14, '043', 'La Huerta', 1),
(575, 14, '044', 'Ixtlahuacán de los Membrillos', 1),
(576, 14, '045', 'Ixtlahuacán del Río', 1),
(577, 14, '046', 'Jalostotitlán', 1),
(578, 14, '047', 'Jamay', 1),
(579, 14, '048', 'Jesús María', 1),
(580, 14, '049', 'Jilotlán de los Dolores', 1),
(581, 14, '050', 'Jocotepec', 1),
(582, 14, '051', 'Juanacatlán', 1),
(583, 14, '052', 'Juchitlán', 1),
(584, 14, '053', 'Lagos de Moreno', 1),
(585, 14, '054', 'El Limón', 1),
(586, 14, '055', 'Magdalena', 1),
(587, 14, '056', 'Santa María del Oro', 1),
(588, 14, '057', 'La Manzanilla de la Paz', 1),
(589, 14, '058', 'Mascota', 1),
(590, 14, '059', 'Mazamitla', 1),
(591, 14, '060', 'Mexticacán', 1),
(592, 14, '061', 'Mezquitic', 1),
(593, 14, '062', 'Mixtlán', 1),
(594, 14, '063', 'Ocotlán', 1),
(595, 14, '064', 'Ojuelos de Jalisco', 1),
(596, 14, '065', 'Pihuamo', 1),
(597, 14, '066', 'Poncitlán', 1),
(598, 14, '067', 'Puerto Vallarta', 1),
(599, 14, '068', 'Villa Purificación', 1),
(600, 14, '069', 'Quitupan', 1),
(601, 14, '070', 'El Salto', 1),
(602, 14, '071', 'San Cristóbal de la Barranca', 1),
(603, 14, '072', 'San Diego de Alejandría', 1),
(604, 14, '073', 'San Juan de los Lagos', 1),
(605, 14, '074', 'San Julián', 1),
(606, 14, '075', 'San Marcos', 1),
(607, 14, '076', 'San Martín de Bolaños', 1),
(608, 14, '077', 'San Martín Hidalgo', 1),
(609, 14, '078', 'San Miguel el Alto', 1),
(610, 14, '079', 'Gómez Farías', 1),
(611, 14, '080', 'San Sebastián del Oeste', 1),
(612, 14, '081', 'Santa María de los Ángeles', 1),
(613, 14, '082', 'Sayula', 1),
(614, 14, '083', 'Tala', 1),
(615, 14, '084', 'Talpa de Allende', 1),
(616, 14, '085', 'Tamazula de Gordiano', 1),
(617, 14, '086', 'Tapalpa', 1),
(618, 14, '087', 'Tecalitlán', 1),
(619, 14, '088', 'Tecolotlán', 1),
(620, 14, '089', 'Techaluta de Montenegro', 1),
(621, 14, '090', 'Tenamaxtlán', 1),
(622, 14, '091', 'Teocaltiche', 1),
(623, 14, '092', 'Teocuitatlán de Corona', 1),
(624, 14, '093', 'Tepatitlán de Morelos', 1),
(625, 14, '094', 'Tequila', 1),
(626, 14, '095', 'Teuchitlán', 1),
(627, 14, '096', 'Tizapán el Alto', 1),
(628, 14, '097', 'Tlajomulco de Zúñiga', 1),
(629, 14, '098', 'San Pedro Tlaquepaque', 1),
(630, 14, '099', 'Tolimán', 1),
(631, 14, '100', 'Tomatlán', 1),
(632, 14, '101', 'Tonalá', 1),
(633, 14, '102', 'Tonaya', 1),
(634, 14, '103', 'Tonila', 1),
(635, 14, '104', 'Totatiche', 1),
(636, 14, '105', 'Tototlán', 1),
(637, 14, '106', 'Tuxcacuesco', 1),
(638, 14, '107', 'Tuxcueca', 1),
(639, 14, '108', 'Tuxpan', 1),
(640, 14, '109', 'Unión de San Antonio', 1),
(641, 14, '110', 'Unión de Tula', 1),
(642, 14, '111', 'Valle de Guadalupe', 1),
(643, 14, '112', 'Valle de Juárez', 1),
(644, 14, '113', 'San Gabriel', 1),
(645, 14, '114', 'Villa Corona', 1),
(646, 14, '115', 'Villa Guerrero', 1),
(647, 14, '116', 'Villa Hidalgo', 1),
(648, 14, '117', 'Cañadas de Obregón', 1),
(649, 14, '118', 'Yahualica de González Gallo', 1),
(650, 14, '119', 'Zacoalco de Torres', 1),
(651, 14, '120', 'Zapopan', 1),
(652, 14, '121', 'Zapotiltic', 1),
(653, 14, '122', 'Zapotitlán de Vadillo', 1),
(654, 14, '123', 'Zapotlán del Rey', 1),
(655, 14, '124', 'Zapotlanejo', 1),
(656, 14, '125', 'San Ignacio Cerro Gordo', 1),
(657, 15, '001', 'Acambay de Ruíz Castañeda', 1),
(658, 15, '002', 'Acolman', 1),
(659, 15, '003', 'Aculco', 1),
(660, 15, '004', 'Almoloya de Alquisiras', 1),
(661, 15, '005', 'Almoloya de Juárez', 1),
(662, 15, '006', 'Almoloya del Río', 1),
(663, 15, '007', 'Amanalco', 1),
(664, 15, '008', 'Amatepec', 1),
(665, 15, '009', 'Amecameca', 1),
(666, 15, '010', 'Apaxco', 1),
(667, 15, '011', 'Atenco', 1),
(668, 15, '012', 'Atizapán', 1),
(669, 15, '013', 'Atizapán de Zaragoza', 1),
(670, 15, '014', 'Atlacomulco', 1),
(671, 15, '015', 'Atlautla', 1),
(672, 15, '016', 'Axapusco', 1),
(673, 15, '017', 'Ayapango', 1),
(674, 15, '018', 'Calimaya', 1),
(675, 15, '019', 'Capulhuac', 1),
(676, 15, '020', 'Coacalco de Berriozábal', 1),
(677, 15, '021', 'Coatepec Harinas', 1),
(678, 15, '022', 'Cocotitlán', 1),
(679, 15, '023', 'Coyotepec', 1),
(680, 15, '024', 'Cuautitlán', 1),
(681, 15, '025', 'Chalco', 1),
(682, 15, '026', 'Chapa de Mota', 1),
(683, 15, '027', 'Chapultepec', 1),
(684, 15, '028', 'Chiautla', 1),
(685, 15, '029', 'Chicoloapan', 1),
(686, 15, '030', 'Chiconcuac', 1),
(687, 15, '031', 'Chimalhuacán', 1),
(688, 15, '032', 'Donato Guerra', 1),
(689, 15, '033', 'Ecatepec de Morelos', 1),
(690, 15, '034', 'Ecatzingo', 1),
(691, 15, '035', 'Huehuetoca', 1),
(692, 15, '036', 'Hueypoxtla', 1),
(693, 15, '037', 'Huixquilucan', 1),
(694, 15, '038', 'Isidro Fabela', 1),
(695, 15, '039', 'Ixtapaluca', 1),
(696, 15, '040', 'Ixtapan de la Sal', 1),
(697, 15, '041', 'Ixtapan del Oro', 1),
(698, 15, '042', 'Ixtlahuaca', 1),
(699, 15, '043', 'Xalatlaco', 1),
(700, 15, '044', 'Jaltenco', 1),
(701, 15, '045', 'Jilotepec', 1),
(702, 15, '046', 'Jilotzingo', 1),
(703, 15, '047', 'Jiquipilco', 1),
(704, 15, '048', 'Jocotitlán', 1),
(705, 15, '049', 'Joquicingo', 1),
(706, 15, '050', 'Juchitepec', 1),
(707, 15, '051', 'Lerma', 1),
(708, 15, '052', 'Malinalco', 1),
(709, 15, '053', 'Melchor Ocampo', 1),
(710, 15, '054', 'Metepec', 1),
(711, 15, '055', 'Mexicaltzingo', 1),
(712, 15, '056', 'Morelos', 1),
(713, 15, '057', 'Naucalpan de Juárez', 1),
(714, 15, '058', 'Nezahualcóyotl', 1),
(715, 15, '059', 'Nextlalpan', 1),
(716, 15, '060', 'Nicolás Romero', 1),
(717, 15, '061', 'Nopaltepec', 1),
(718, 15, '062', 'Ocoyoacac', 1),
(719, 15, '063', 'Ocuilan', 1),
(720, 15, '064', 'El Oro', 1),
(721, 15, '065', 'Otumba', 1),
(722, 15, '066', 'Otzoloapan', 1),
(723, 15, '067', 'Otzolotepec', 1),
(724, 15, '068', 'Ozumba', 1),
(725, 15, '069', 'Papalotla', 1),
(726, 15, '070', 'La Paz', 1),
(727, 15, '071', 'Polotitlán', 1),
(728, 15, '072', 'Rayón', 1),
(729, 15, '073', 'San Antonio la Isla', 1),
(730, 15, '074', 'San Felipe del Progreso', 1),
(731, 15, '075', 'San Martín de las Pirámides', 1),
(732, 15, '076', 'San Mateo Atenco', 1),
(733, 15, '077', 'San Simón de Guerrero', 1),
(734, 15, '078', 'Santo Tomás', 1),
(735, 15, '079', 'Soyaniquilpan de Juárez', 1),
(736, 15, '080', 'Sultepec', 1),
(737, 15, '081', 'Tecámac', 1),
(738, 15, '082', 'Tejupilco', 1),
(739, 15, '083', 'Temamatla', 1),
(740, 15, '084', 'Temascalapa', 1),
(741, 15, '085', 'Temascalcingo', 1),
(742, 15, '086', 'Temascaltepec', 1),
(743, 15, '087', 'Temoaya', 1),
(744, 15, '088', 'Tenancingo', 1),
(745, 15, '089', 'Tenango del Aire', 1),
(746, 15, '090', 'Tenango del Valle', 1),
(747, 15, '091', 'Teoloyucan', 1),
(748, 15, '092', 'Teotihuacán', 1),
(749, 15, '093', 'Tepetlaoxtoc', 1),
(750, 15, '094', 'Tepetlixpa', 1),
(751, 15, '095', 'Tepotzotlán', 1),
(752, 15, '096', 'Tequixquiac', 1),
(753, 15, '097', 'Texcaltitlán', 1),
(754, 15, '098', 'Texcalyacac', 1),
(755, 15, '099', 'Texcoco', 1),
(756, 15, '100', 'Tezoyuca', 1),
(757, 15, '101', 'Tianguistenco', 1),
(758, 15, '102', 'Timilpan', 1),
(759, 15, '103', 'Tlalmanalco', 1),
(760, 15, '104', 'Tlalnepantla de Baz', 1),
(761, 15, '105', 'Tlatlaya', 1),
(762, 15, '106', 'Toluca', 1),
(763, 15, '107', 'Tonatico', 1),
(764, 15, '108', 'Tultepec', 1),
(765, 15, '109', 'Tultitlán', 1),
(766, 15, '110', 'Valle de Bravo', 1),
(767, 15, '111', 'Villa de Allende', 1),
(768, 15, '112', 'Villa del Carbón', 1),
(769, 15, '113', 'Villa Guerrero', 1),
(770, 15, '114', 'Villa Victoria', 1),
(771, 15, '115', 'Xonacatlán', 1),
(772, 15, '116', 'Zacazonapan', 1),
(773, 15, '117', 'Zacualpan', 1),
(774, 15, '118', 'Zinacantepec', 1),
(775, 15, '119', 'Zumpahuacán', 1),
(776, 15, '120', 'Zumpango', 1),
(777, 15, '121', 'Cuautitlán Izcalli', 1),
(778, 15, '122', 'Valle de Chalco Solidaridad', 1),
(779, 15, '123', 'Luvianos', 1),
(780, 15, '124', 'San José del Rincón', 1),
(781, 15, '125', 'Tonanitla', 1),
(782, 16, '001', 'Acuitzio', 1),
(783, 16, '002', 'Aguililla', 1),
(784, 16, '003', 'Álvaro Obregón', 1),
(785, 16, '004', 'Angamacutiro', 1),
(786, 16, '005', 'Angangueo', 1),
(787, 16, '006', 'Apatzingán', 1),
(788, 16, '007', 'Aporo', 1),
(789, 16, '008', 'Aquila', 1),
(790, 16, '009', 'Ario', 1),
(791, 16, '010', 'Arteaga', 1),
(792, 16, '011', 'Briseñas', 1),
(793, 16, '012', 'Buenavista', 1),
(794, 16, '013', 'Carácuaro', 1),
(795, 16, '014', 'Coahuayana', 1),
(796, 16, '015', 'Coalcomán de Vázquez Pallares', 1),
(797, 16, '016', 'Coeneo', 1),
(798, 16, '017', 'Contepec', 1),
(799, 16, '018', 'Copándaro', 1),
(800, 16, '019', 'Cotija', 1),
(801, 16, '020', 'Cuitzeo', 1),
(802, 16, '021', 'Charapan', 1),
(803, 16, '022', 'Charo', 1),
(804, 16, '023', 'Chavinda', 1),
(805, 16, '024', 'Cherán', 1),
(806, 16, '025', 'Chilchota', 1),
(807, 16, '026', 'Chinicuila', 1),
(808, 16, '027', 'Chucándiro', 1),
(809, 16, '028', 'Churintzio', 1),
(810, 16, '029', 'Churumuco', 1),
(811, 16, '030', 'Ecuandureo', 1),
(812, 16, '031', 'Epitacio Huerta', 1),
(813, 16, '032', 'Erongarícuaro', 1),
(814, 16, '033', 'Gabriel Zamora', 1),
(815, 16, '034', 'Hidalgo', 1),
(816, 16, '035', 'La Huacana', 1),
(817, 16, '036', 'Huandacareo', 1),
(818, 16, '037', 'Huaniqueo', 1),
(819, 16, '038', 'Huetamo', 1),
(820, 16, '039', 'Huiramba', 1),
(821, 16, '040', 'Indaparapeo', 1),
(822, 16, '041', 'Irimbo', 1),
(823, 16, '042', 'Ixtlán', 1),
(824, 16, '043', 'Jacona', 1),
(825, 16, '044', 'Jiménez', 1),
(826, 16, '045', 'Jiquilpan', 1),
(827, 16, '046', 'Juárez', 1),
(828, 16, '047', 'Jungapeo', 1),
(829, 16, '048', 'Lagunillas', 1),
(830, 16, '049', 'Madero', 1),
(831, 16, '050', 'Maravatío', 1),
(832, 16, '051', 'Marcos Castellanos', 1),
(833, 16, '052', 'Lázaro Cárdenas', 1),
(834, 16, '053', 'Morelia', 1),
(835, 16, '054', 'Morelos', 1),
(836, 16, '055', 'Múgica', 1),
(837, 16, '056', 'Nahuatzen', 1),
(838, 16, '057', 'Nocupétaro', 1),
(839, 16, '058', 'Nuevo Parangaricutiro', 1),
(840, 16, '059', 'Nuevo Urecho', 1),
(841, 16, '060', 'Numarán', 1),
(842, 16, '061', 'Ocampo', 1),
(843, 16, '062', 'Pajacuarán', 1),
(844, 16, '063', 'Panindícuaro', 1),
(845, 16, '064', 'Parácuaro', 1),
(846, 16, '065', 'Paracho', 1),
(847, 16, '066', 'Pátzcuaro', 1),
(848, 16, '067', 'Penjamillo', 1),
(849, 16, '068', 'Peribán', 1),
(850, 16, '069', 'La Piedad', 1),
(851, 16, '070', 'Purépero', 1),
(852, 16, '071', 'Puruándiro', 1),
(853, 16, '072', 'Queréndaro', 1),
(854, 16, '073', 'Quiroga', 1),
(855, 16, '074', 'Cojumatlán de Régules', 1),
(856, 16, '075', 'Los Reyes', 1),
(857, 16, '076', 'Sahuayo', 1),
(858, 16, '077', 'San Lucas', 1),
(859, 16, '078', 'Santa Ana Maya', 1),
(860, 16, '079', 'Salvador Escalante', 1),
(861, 16, '080', 'Senguio', 1),
(862, 16, '081', 'Susupuato', 1),
(863, 16, '082', 'Tacámbaro', 1),
(864, 16, '083', 'Tancítaro', 1),
(865, 16, '084', 'Tangamandapio', 1),
(866, 16, '085', 'Tangancícuaro', 1),
(867, 16, '086', 'Tanhuato', 1),
(868, 16, '087', 'Taretan', 1),
(869, 16, '088', 'Tarímbaro', 1),
(870, 16, '089', 'Tepalcatepec', 1),
(871, 16, '090', 'Tingambato', 1),
(872, 16, '091', 'Tingüindín', 1),
(873, 16, '092', 'Tiquicheo de Nicolás Romero', 1),
(874, 16, '093', 'Tlalpujahua', 1),
(875, 16, '094', 'Tlazazalca', 1),
(876, 16, '095', 'Tocumbo', 1),
(877, 16, '096', 'Tumbiscatío', 1),
(878, 16, '097', 'Turicato', 1),
(879, 16, '098', 'Tuxpan', 1),
(880, 16, '099', 'Tuzantla', 1),
(881, 16, '100', 'Tzintzuntzan', 1),
(882, 16, '101', 'Tzitzio', 1),
(883, 16, '102', 'Uruapan', 1),
(884, 16, '103', 'Venustiano Carranza', 1),
(885, 16, '104', 'Villamar', 1),
(886, 16, '105', 'Vista Hermosa', 1),
(887, 16, '106', 'Yurécuaro', 1),
(888, 16, '107', 'Zacapu', 1),
(889, 16, '108', 'Zamora', 1),
(890, 16, '109', 'Zináparo', 1),
(891, 16, '110', 'Zinapécuaro', 1),
(892, 16, '111', 'Ziracuaretiro', 1),
(893, 16, '112', 'Zitácuaro', 1),
(894, 16, '113', 'José Sixto Verduzco', 1),
(895, 17, '001', 'Amacuzac', 1),
(896, 17, '002', 'Atlatlahucan', 1),
(897, 17, '003', 'Axochiapan', 1),
(898, 17, '004', 'Ayala', 1),
(899, 17, '005', 'Coatlán del Río', 1),
(900, 17, '006', 'Cuautla', 1),
(901, 17, '007', 'Cuernavaca', 1),
(902, 17, '008', 'Emiliano Zapata', 1),
(903, 17, '009', 'Huitzilac', 1),
(904, 17, '010', 'Jantetelco', 1),
(905, 17, '011', 'Jiutepec', 1),
(906, 17, '012', 'Jojutla', 1),
(907, 17, '013', 'Jonacatepec', 1),
(908, 17, '014', 'Mazatepec', 1),
(909, 17, '015', 'Miacatlán', 1),
(910, 17, '016', 'Ocuituco', 1),
(911, 17, '017', 'Puente de Ixtla', 1),
(912, 17, '018', 'Temixco', 1),
(913, 17, '019', 'Tepalcingo', 1),
(914, 17, '020', 'Tepoztlán', 1),
(915, 17, '021', 'Tetecala', 1),
(916, 17, '022', 'Tetela del Volcán', 1),
(917, 17, '023', 'Tlalnepantla', 1),
(918, 17, '024', 'Tlaltizapán de Zapata', 1),
(919, 17, '025', 'Tlaquiltenango', 1),
(920, 17, '026', 'Tlayacapan', 1),
(921, 17, '027', 'Totolapan', 1),
(922, 17, '028', 'Xochitepec', 1),
(923, 17, '029', 'Yautepec', 1),
(924, 17, '030', 'Yecapixtla', 1),
(925, 17, '031', 'Zacatepec', 1),
(926, 17, '032', 'Zacualpan de Amilpas', 1),
(927, 17, '033', 'Temoac', 1),
(928, 18, '001', 'Acaponeta', 1),
(929, 18, '002', 'Ahuacatlán', 1),
(930, 18, '003', 'Amatlán de Cañas', 1),
(931, 18, '004', 'Compostela', 1),
(932, 18, '005', 'Huajicori', 1),
(933, 18, '006', 'Ixtlán del Río', 1),
(934, 18, '007', 'Jala', 1),
(935, 18, '008', 'Xalisco', 1),
(936, 18, '009', 'Del Nayar', 1),
(937, 18, '010', 'Rosamorada', 1),
(938, 18, '011', 'Ruíz', 1),
(939, 18, '012', 'San Blas', 1),
(940, 18, '013', 'San Pedro Lagunillas', 1),
(941, 18, '014', 'Santa María del Oro', 1),
(942, 18, '015', 'Santiago Ixcuintla', 1),
(943, 18, '016', 'Tecuala', 1),
(944, 18, '017', 'Tepic', 1),
(945, 18, '018', 'Tuxpan', 1),
(946, 18, '019', 'La Yesca', 1),
(947, 18, '020', 'Bahía de Banderas', 1),
(948, 19, '001', 'Abasolo', 1),
(949, 19, '002', 'Agualeguas', 1),
(950, 19, '003', 'Los Aldamas', 1),
(951, 19, '004', 'Allende', 1),
(952, 19, '005', 'Anáhuac', 1),
(953, 19, '006', 'Apodaca', 1),
(954, 19, '007', 'Aramberri', 1),
(955, 19, '008', 'Bustamante', 1),
(956, 19, '009', 'Cadereyta Jiménez', 1),
(957, 19, '010', 'El Carmen', 1),
(958, 19, '011', 'Cerralvo', 1),
(959, 19, '012', 'Ciénega de Flores', 1),
(960, 19, '013', 'China', 1),
(961, 19, '014', 'Doctor Arroyo', 1),
(962, 19, '015', 'Doctor Coss', 1),
(963, 19, '016', 'Doctor González', 1),
(964, 19, '017', 'Galeana', 1),
(965, 19, '018', 'García', 1),
(966, 19, '019', 'San Pedro Garza García', 1),
(967, 19, '020', 'General Bravo', 1),
(968, 19, '021', 'General Escobedo', 1),
(969, 19, '022', 'General Terán', 1),
(970, 19, '023', 'General Treviño', 1),
(971, 19, '024', 'General Zaragoza', 1),
(972, 19, '025', 'General Zuazua', 1),
(973, 19, '026', 'Guadalupe', 1),
(974, 19, '027', 'Los Herreras', 1),
(975, 19, '028', 'Higueras', 1),
(976, 19, '029', 'Hualahuises', 1),
(977, 19, '030', 'Iturbide', 1),
(978, 19, '031', 'Juárez', 1),
(979, 19, '032', 'Lampazos de Naranjo', 1),
(980, 19, '033', 'Linares', 1),
(981, 19, '034', 'Marín', 1),
(982, 19, '035', 'Melchor Ocampo', 1),
(983, 19, '036', 'Mier y Noriega', 1),
(984, 19, '037', 'Mina', 1),
(985, 19, '038', 'Montemorelos', 1),
(986, 19, '039', 'Monterrey', 1),
(987, 19, '040', 'Parás', 1),
(988, 19, '041', 'Pesquería', 1),
(989, 19, '042', 'Los Ramones', 1),
(990, 19, '043', 'Rayones', 1),
(991, 19, '044', 'Sabinas Hidalgo', 1),
(992, 19, '045', 'Salinas Victoria', 1),
(993, 19, '046', 'San Nicolás de los Garza', 1),
(994, 19, '047', 'Hidalgo', 1),
(995, 19, '048', 'Santa Catarina', 1),
(996, 19, '049', 'Santiago', 1),
(997, 19, '050', 'Vallecillo', 1),
(998, 19, '051', 'Villaldama', 1),
(999, 20, '001', 'Abejones', 1),
(1000, 20, '002', 'Acatlán de Pérez Figueroa', 1),
(1001, 20, '003', 'Asunción Cacalotepec', 1),
(1002, 20, '004', 'Asunción Cuyotepeji', 1),
(1003, 20, '005', 'Asunción Ixtaltepec', 1),
(1004, 20, '006', 'Asunción Nochixtlán', 1),
(1005, 20, '007', 'Asunción Ocotlán', 1),
(1006, 20, '008', 'Asunción Tlacolulita', 1),
(1007, 20, '009', 'Ayotzintepec', 1),
(1008, 20, '010', 'El Barrio de la Soledad', 1),
(1009, 20, '011', 'Calihualá', 1),
(1010, 20, '012', 'Candelaria Loxicha', 1),
(1011, 20, '013', 'Ciénega de Zimatlán', 1),
(1012, 20, '014', 'Ciudad Ixtepec', 1),
(1013, 20, '015', 'Coatecas Altas', 1),
(1014, 20, '016', 'Coicoyán de las Flores', 1),
(1015, 20, '017', 'La Compañía', 1),
(1016, 20, '018', 'Concepción Buenavista', 1),
(1017, 20, '019', 'Concepción Pápalo', 1),
(1018, 20, '020', 'Constancia del Rosario', 1),
(1019, 20, '021', 'Cosolapa', 1),
(1020, 20, '022', 'Cosoltepec', 1),
(1021, 20, '023', 'Cuilápam de Guerrero', 1),
(1022, 20, '024', 'Cuyamecalco Villa de Zaragoza', 1),
(1023, 20, '025', 'Chahuites', 1),
(1024, 20, '026', 'Chalcatongo de Hidalgo', 1),
(1025, 20, '027', 'Chiquihuitlán de Benito Juárez', 1),
(1026, 20, '028', 'Heroica Ciudad de Ejutla de Crespo', 1),
(1027, 20, '029', 'Eloxochitlán de Flores Magón', 1),
(1028, 20, '030', 'El Espinal', 1),
(1029, 20, '031', 'Tamazulápam del Espíritu Santo', 1),
(1030, 20, '032', 'Fresnillo de Trujano', 1),
(1031, 20, '033', 'Guadalupe Etla', 1),
(1032, 20, '034', 'Guadalupe de Ramírez', 1),
(1033, 20, '035', 'Guelatao de Juárez', 1),
(1034, 20, '036', 'Guevea de Humboldt', 1),
(1035, 20, '037', 'Mesones Hidalgo', 1),
(1036, 20, '038', 'Villa Hidalgo', 1),
(1037, 20, '039', 'Heroica Ciudad de Huajuapan de León', 1),
(1038, 20, '040', 'Huautepec', 1),
(1039, 20, '041', 'Huautla de Jiménez', 1),
(1040, 20, '042', 'Ixtlán de Juárez', 1),
(1041, 20, '043', 'Heroica Ciudad de Juchitán de Zaragoza', 1),
(1042, 20, '044', 'Loma Bonita', 1),
(1043, 20, '045', 'Magdalena Apasco', 1),
(1044, 20, '046', 'Magdalena Jaltepec', 1),
(1045, 20, '047', 'Santa Magdalena Jicotlán', 1),
(1046, 20, '048', 'Magdalena Mixtepec', 1),
(1047, 20, '049', 'Magdalena Ocotlán', 1),
(1048, 20, '050', 'Magdalena Peñasco', 1),
(1049, 20, '051', 'Magdalena Teitipac', 1),
(1050, 20, '052', 'Magdalena Tequisistlán', 1),
(1051, 20, '053', 'Magdalena Tlacotepec', 1),
(1052, 20, '054', 'Magdalena Zahuatlán', 1),
(1053, 20, '055', 'Mariscala de Juárez', 1),
(1054, 20, '056', 'Mártires de Tacubaya', 1),
(1055, 20, '057', 'Matías Romero Avendaño', 1),
(1056, 20, '058', 'Mazatlán Villa de Flores', 1),
(1057, 20, '059', 'Miahuatlán de Porfirio Díaz', 1),
(1058, 20, '060', 'Mixistlán de la Reforma', 1),
(1059, 20, '061', 'Monjas', 1),
(1060, 20, '062', 'Natividad', 1),
(1061, 20, '063', 'Nazareno Etla', 1),
(1062, 20, '064', 'Nejapa de Madero', 1),
(1063, 20, '065', 'Ixpantepec Nieves', 1),
(1064, 20, '066', 'Santiago Niltepec', 1),
(1065, 20, '067', 'Oaxaca de Juárez', 1),
(1066, 20, '068', 'Ocotlán de Morelos', 1),
(1067, 20, '069', 'La Pe', 1),
(1068, 20, '070', 'Pinotepa de Don Luis', 1),
(1069, 20, '071', 'Pluma Hidalgo', 1),
(1070, 20, '072', 'San José del Progreso', 1),
(1071, 20, '073', 'Putla Villa de Guerrero', 1),
(1072, 20, '074', 'Santa Catarina Quioquitani', 1),
(1073, 20, '075', 'Reforma de Pineda', 1),
(1074, 20, '076', 'La Reforma', 1),
(1075, 20, '077', 'Reyes Etla', 1),
(1076, 20, '078', 'Rojas de Cuauhtémoc', 1),
(1077, 20, '079', 'Salina Cruz', 1),
(1078, 20, '080', 'San Agustín Amatengo', 1),
(1079, 20, '081', 'San Agustín Atenango', 1),
(1080, 20, '082', 'San Agustín Chayuco', 1),
(1081, 20, '083', 'San Agustín de las Juntas', 1),
(1082, 20, '084', 'San Agustín Etla', 1),
(1083, 20, '085', 'San Agustín Loxicha', 1),
(1084, 20, '086', 'San Agustín Tlacotepec', 1),
(1085, 20, '087', 'San Agustín Yatareni', 1),
(1086, 20, '088', 'San Andrés Cabecera Nueva', 1),
(1087, 20, '089', 'San Andrés Dinicuiti', 1),
(1088, 20, '090', 'San Andrés Huaxpaltepec', 1),
(1089, 20, '091', 'San Andrés Huayápam', 1),
(1090, 20, '092', 'San Andrés Ixtlahuaca', 1),
(1091, 20, '093', 'San Andrés Lagunas', 1),
(1092, 20, '094', 'San Andrés Nuxiño', 1),
(1093, 20, '095', 'San Andrés Paxtlán', 1),
(1094, 20, '096', 'San Andrés Sinaxtla', 1),
(1095, 20, '097', 'San Andrés Solaga', 1),
(1096, 20, '098', 'San Andrés Teotilálpam', 1),
(1097, 20, '099', 'San Andrés Tepetlapa', 1),
(1098, 20, '100', 'San Andrés Yaá', 1),
(1099, 20, '101', 'San Andrés Zabache', 1),
(1100, 20, '102', 'San Andrés Zautla', 1),
(1101, 20, '103', 'San Antonino Castillo Velasco', 1),
(1102, 20, '104', 'San Antonino el Alto', 1),
(1103, 20, '105', 'San Antonino Monte Verde', 1),
(1104, 20, '106', 'San Antonio Acutla', 1),
(1105, 20, '107', 'San Antonio de la Cal', 1),
(1106, 20, '108', 'San Antonio Huitepec', 1),
(1107, 20, '109', 'San Antonio Nanahuatípam', 1),
(1108, 20, '110', 'San Antonio Sinicahua', 1),
(1109, 20, '111', 'San Antonio Tepetlapa', 1),
(1110, 20, '112', 'San Baltazar Chichicápam', 1),
(1111, 20, '113', 'San Baltazar Loxicha', 1),
(1112, 20, '114', 'San Baltazar Yatzachi el Bajo', 1),
(1113, 20, '115', 'San Bartolo Coyotepec', 1),
(1114, 20, '116', 'San Bartolomé Ayautla', 1),
(1115, 20, '117', 'San Bartolomé Loxicha', 1),
(1116, 20, '118', 'San Bartolomé Quialana', 1),
(1117, 20, '119', 'San Bartolomé Yucuañe', 1),
(1118, 20, '120', 'San Bartolomé Zoogocho', 1),
(1119, 20, '121', 'San Bartolo Soyaltepec', 1),
(1120, 20, '122', 'San Bartolo Yautepec', 1),
(1121, 20, '123', 'San Bernardo Mixtepec', 1),
(1122, 20, '124', 'San Blas Atempa', 1),
(1123, 20, '125', 'San Carlos Yautepec', 1),
(1124, 20, '126', 'San Cristóbal Amatlán', 1),
(1125, 20, '127', 'San Cristóbal Amoltepec', 1),
(1126, 20, '128', 'San Cristóbal Lachirioag', 1),
(1127, 20, '129', 'San Cristóbal Suchixtlahuaca', 1),
(1128, 20, '130', 'San Dionisio del Mar', 1),
(1129, 20, '131', 'San Dionisio Ocotepec', 1),
(1130, 20, '132', 'San Dionisio Ocotlán', 1),
(1131, 20, '133', 'San Esteban Atatlahuca', 1),
(1132, 20, '134', 'San Felipe Jalapa de Díaz', 1),
(1133, 20, '135', 'San Felipe Tejalápam', 1),
(1134, 20, '136', 'San Felipe Usila', 1),
(1135, 20, '137', 'San Francisco Cahuacuá', 1),
(1136, 20, '138', 'San Francisco Cajonos', 1),
(1137, 20, '139', 'San Francisco Chapulapa', 1),
(1138, 20, '140', 'San Francisco Chindúa', 1),
(1139, 20, '141', 'San Francisco del Mar', 1),
(1140, 20, '142', 'San Francisco Huehuetlán', 1),
(1141, 20, '143', 'San Francisco Ixhuatán', 1),
(1142, 20, '144', 'San Francisco Jaltepetongo', 1),
(1143, 20, '145', 'San Francisco Lachigoló', 1),
(1144, 20, '146', 'San Francisco Logueche', 1),
(1145, 20, '147', 'San Francisco Nuxaño', 1),
(1146, 20, '148', 'San Francisco Ozolotepec', 1),
(1147, 20, '149', 'San Francisco Sola', 1),
(1148, 20, '150', 'San Francisco Telixtlahuaca', 1),
(1149, 20, '151', 'San Francisco Teopan', 1),
(1150, 20, '152', 'San Francisco Tlapancingo', 1),
(1151, 20, '153', 'San Gabriel Mixtepec', 1),
(1152, 20, '154', 'San Ildefonso Amatlán', 1),
(1153, 20, '155', 'San Ildefonso Sola', 1),
(1154, 20, '156', 'San Ildefonso Villa Alta', 1),
(1155, 20, '157', 'San Jacinto Amilpas', 1),
(1156, 20, '158', 'San Jacinto Tlacotepec', 1),
(1157, 20, '159', 'San Jerónimo Coatlán', 1),
(1158, 20, '160', 'San Jerónimo Silacayoapilla', 1),
(1159, 20, '161', 'San Jerónimo Sosola', 1),
(1160, 20, '162', 'San Jerónimo Taviche', 1),
(1161, 20, '163', 'San Jerónimo Tecóatl', 1),
(1162, 20, '164', 'San Jorge Nuchita', 1),
(1163, 20, '165', 'San José Ayuquila', 1),
(1164, 20, '166', 'San José Chiltepec', 1),
(1165, 20, '167', 'San José del Peñasco', 1),
(1166, 20, '168', 'San José Estancia Grande', 1),
(1167, 20, '169', 'San José Independencia', 1),
(1168, 20, '170', 'San José Lachiguiri', 1),
(1169, 20, '171', 'San José Tenango', 1),
(1170, 20, '172', 'San Juan Achiutla', 1),
(1171, 20, '173', 'San Juan Atepec', 1),
(1172, 20, '174', 'Ánimas Trujano', 1),
(1173, 20, '175', 'San Juan Bautista Atatlahuca', 1),
(1174, 20, '176', 'San Juan Bautista Coixtlahuaca', 1),
(1175, 20, '177', 'San Juan Bautista Cuicatlán', 1),
(1176, 20, '178', 'San Juan Bautista Guelache', 1),
(1177, 20, '179', 'San Juan Bautista Jayacatlán', 1),
(1178, 20, '180', 'San Juan Bautista Lo de Soto', 1),
(1179, 20, '181', 'San Juan Bautista Suchitepec', 1),
(1180, 20, '182', 'San Juan Bautista Tlacoatzintepec', 1),
(1181, 20, '183', 'San Juan Bautista Tlachichilco', 1),
(1182, 20, '184', 'San Juan Bautista Tuxtepec', 1),
(1183, 20, '185', 'San Juan Cacahuatepec', 1),
(1184, 20, '186', 'San Juan Cieneguilla', 1),
(1185, 20, '187', 'San Juan Coatzóspam', 1),
(1186, 20, '188', 'San Juan Colorado', 1),
(1187, 20, '189', 'San Juan Comaltepec', 1),
(1188, 20, '190', 'San Juan Cotzocón', 1),
(1189, 20, '191', 'San Juan Chicomezúchil', 1),
(1190, 20, '192', 'San Juan Chilateca', 1),
(1191, 20, '193', 'San Juan del Estado', 1),
(1192, 20, '194', 'San Juan del Río', 1),
(1193, 20, '195', 'San Juan Diuxi', 1),
(1194, 20, '196', 'San Juan Evangelista Analco', 1),
(1195, 20, '197', 'San Juan Guelavía', 1),
(1196, 20, '198', 'San Juan Guichicovi', 1),
(1197, 20, '199', 'San Juan Ihualtepec', 1),
(1198, 20, '200', 'San Juan Juquila Mixes', 1),
(1199, 20, '201', 'San Juan Juquila Vijanos', 1),
(1200, 20, '202', 'San Juan Lachao', 1),
(1201, 20, '203', 'San Juan Lachigalla', 1),
(1202, 20, '204', 'San Juan Lajarcia', 1),
(1203, 20, '205', 'San Juan Lalana', 1),
(1204, 20, '206', 'San Juan de los Cués', 1),
(1205, 20, '207', 'San Juan Mazatlán', 1),
(1206, 20, '208', 'San Juan Mixtepec', 1),
(1207, 20, '209', 'San Juan Mixtepec', 1),
(1208, 20, '210', 'San Juan Ñumí', 1),
(1209, 20, '211', 'San Juan Ozolotepec', 1),
(1210, 20, '212', 'San Juan Petlapa', 1),
(1211, 20, '213', 'San Juan Quiahije', 1),
(1212, 20, '214', 'San Juan Quiotepec', 1),
(1213, 20, '215', 'San Juan Sayultepec', 1),
(1214, 20, '216', 'San Juan Tabaá', 1),
(1215, 20, '217', 'San Juan Tamazola', 1),
(1216, 20, '218', 'San Juan Teita', 1),
(1217, 20, '219', 'San Juan Teitipac', 1),
(1218, 20, '220', 'San Juan Tepeuxila', 1),
(1219, 20, '221', 'San Juan Teposcolula', 1),
(1220, 20, '222', 'San Juan Yaeé', 1),
(1221, 20, '223', 'San Juan Yatzona', 1),
(1222, 20, '224', 'San Juan Yucuita', 1),
(1223, 20, '225', 'San Lorenzo', 1),
(1224, 20, '226', 'San Lorenzo Albarradas', 1),
(1225, 20, '227', 'San Lorenzo Cacaotepec', 1),
(1226, 20, '228', 'San Lorenzo Cuaunecuiltitla', 1),
(1227, 20, '229', 'San Lorenzo Texmelúcan', 1),
(1228, 20, '230', 'San Lorenzo Victoria', 1),
(1229, 20, '231', 'San Lucas Camotlán', 1),
(1230, 20, '232', 'San Lucas Ojitlán', 1),
(1231, 20, '233', 'San Lucas Quiaviní', 1),
(1232, 20, '234', 'San Lucas Zoquiápam', 1),
(1233, 20, '235', 'San Luis Amatlán', 1),
(1234, 20, '236', 'San Marcial Ozolotepec', 1),
(1235, 20, '237', 'San Marcos Arteaga', 1),
(1236, 20, '238', 'San Martín de los Cansecos', 1),
(1237, 20, '239', 'San Martín Huamelúlpam', 1),
(1238, 20, '240', 'San Martín Itunyoso', 1),
(1239, 20, '241', 'San Martín Lachilá', 1),
(1240, 20, '242', 'San Martín Peras', 1),
(1241, 20, '243', 'San Martín Tilcajete', 1),
(1242, 20, '244', 'San Martín Toxpalan', 1),
(1243, 20, '245', 'San Martín Zacatepec', 1),
(1244, 20, '246', 'San Mateo Cajonos', 1),
(1245, 20, '247', 'Capulálpam de Méndez', 1),
(1246, 20, '248', 'San Mateo del Mar', 1),
(1247, 20, '249', 'San Mateo Yoloxochitlán', 1),
(1248, 20, '250', 'San Mateo Etlatongo', 1),
(1249, 20, '251', 'San Mateo Nejápam', 1),
(1250, 20, '252', 'San Mateo Peñasco', 1),
(1251, 20, '253', 'San Mateo Piñas', 1),
(1252, 20, '254', 'San Mateo Río Hondo', 1),
(1253, 20, '255', 'San Mateo Sindihui', 1),
(1254, 20, '256', 'San Mateo Tlapiltepec', 1),
(1255, 20, '257', 'San Melchor Betaza', 1),
(1256, 20, '258', 'San Miguel Achiutla', 1),
(1257, 20, '259', 'San Miguel Ahuehuetitlán', 1),
(1258, 20, '260', 'San Miguel Aloápam', 1),
(1259, 20, '261', 'San Miguel Amatitlán', 1),
(1260, 20, '262', 'San Miguel Amatlán', 1),
(1261, 20, '263', 'San Miguel Coatlán', 1),
(1262, 20, '264', 'San Miguel Chicahua', 1),
(1263, 20, '265', 'San Miguel Chimalapa', 1),
(1264, 20, '266', 'San Miguel del Puerto', 1),
(1265, 20, '267', 'San Miguel del Río', 1),
(1266, 20, '268', 'San Miguel Ejutla', 1),
(1267, 20, '269', 'San Miguel el Grande', 1),
(1268, 20, '270', 'San Miguel Huautla', 1),
(1269, 20, '271', 'San Miguel Mixtepec', 1),
(1270, 20, '272', 'San Miguel Panixtlahuaca', 1),
(1271, 20, '273', 'San Miguel Peras', 1),
(1272, 20, '274', 'San Miguel Piedras', 1),
(1273, 20, '275', 'San Miguel Quetzaltepec', 1),
(1274, 20, '276', 'San Miguel Santa Flor', 1),
(1275, 20, '277', 'Villa Sola de Vega', 1),
(1276, 20, '278', 'San Miguel Soyaltepec', 1),
(1277, 20, '279', 'San Miguel Suchixtepec', 1),
(1278, 20, '280', 'Villa Talea de Castro', 1),
(1279, 20, '281', 'San Miguel Tecomatlán', 1),
(1280, 20, '282', 'San Miguel Tenango', 1),
(1281, 20, '283', 'San Miguel Tequixtepec', 1),
(1282, 20, '284', 'San Miguel Tilquiápam', 1),
(1283, 20, '285', 'San Miguel Tlacamama', 1),
(1284, 20, '286', 'San Miguel Tlacotepec', 1),
(1285, 20, '287', 'San Miguel Tulancingo', 1),
(1286, 20, '288', 'San Miguel Yotao', 1),
(1287, 20, '289', 'San Nicolás', 1),
(1288, 20, '290', 'San Nicolás Hidalgo', 1),
(1289, 20, '291', 'San Pablo Coatlán', 1),
(1290, 20, '292', 'San Pablo Cuatro Venados', 1),
(1291, 20, '293', 'San Pablo Etla', 1),
(1292, 20, '294', 'San Pablo Huitzo', 1),
(1293, 20, '295', 'San Pablo Huixtepec', 1),
(1294, 20, '296', 'San Pablo Macuiltianguis', 1),
(1295, 20, '297', 'San Pablo Tijaltepec', 1),
(1296, 20, '298', 'San Pablo Villa de Mitla', 1),
(1297, 20, '299', 'San Pablo Yaganiza', 1),
(1298, 20, '300', 'San Pedro Amuzgos', 1),
(1299, 20, '301', 'San Pedro Apóstol', 1),
(1300, 20, '302', 'San Pedro Atoyac', 1),
(1301, 20, '303', 'San Pedro Cajonos', 1),
(1302, 20, '304', 'San Pedro Coxcaltepec Cántaros', 1),
(1303, 20, '305', 'San Pedro Comitancillo', 1),
(1304, 20, '306', 'San Pedro el Alto', 1),
(1305, 20, '307', 'San Pedro Huamelula', 1),
(1306, 20, '308', 'San Pedro Huilotepec', 1),
(1307, 20, '309', 'San Pedro Ixcatlán', 1),
(1308, 20, '310', 'San Pedro Ixtlahuaca', 1),
(1309, 20, '311', 'San Pedro Jaltepetongo', 1),
(1310, 20, '312', 'San Pedro Jicayán', 1),
(1311, 20, '313', 'San Pedro Jocotipac', 1),
(1312, 20, '314', 'San Pedro Juchatengo', 1),
(1313, 20, '315', 'San Pedro Mártir', 1),
(1314, 20, '316', 'San Pedro Mártir Quiechapa', 1),
(1315, 20, '317', 'San Pedro Mártir Yucuxaco', 1),
(1316, 20, '318', 'San Pedro Mixtepec', 1),
(1317, 20, '319', 'San Pedro Mixtepec', 1),
(1318, 20, '320', 'San Pedro Molinos', 1),
(1319, 20, '321', 'San Pedro Nopala', 1),
(1320, 20, '322', 'San Pedro Ocopetatillo', 1),
(1321, 20, '323', 'San Pedro Ocotepec', 1),
(1322, 20, '324', 'San Pedro Pochutla', 1),
(1323, 20, '325', 'San Pedro Quiatoni', 1),
(1324, 20, '326', 'San Pedro Sochiápam', 1),
(1325, 20, '327', 'San Pedro Tapanatepec', 1),
(1326, 20, '328', 'San Pedro Taviche', 1),
(1327, 20, '329', 'San Pedro Teozacoalco', 1),
(1328, 20, '330', 'San Pedro Teutila', 1),
(1329, 20, '331', 'San Pedro Tidaá', 1),
(1330, 20, '332', 'San Pedro Topiltepec', 1),
(1331, 20, '333', 'San Pedro Totolápam', 1),
(1332, 20, '334', 'Villa de Tututepec de Melchor Ocampo', 1),
(1333, 20, '335', 'San Pedro Yaneri', 1),
(1334, 20, '336', 'San Pedro Yólox', 1),
(1335, 20, '337', 'San Pedro y San Pablo Ayutla', 1),
(1336, 20, '338', 'Villa de Etla', 1),
(1337, 20, '339', 'San Pedro y San Pablo Teposcolula', 1),
(1338, 20, '340', 'San Pedro y San Pablo Tequixtepec', 1),
(1339, 20, '341', 'San Pedro Yucunama', 1),
(1340, 20, '342', 'San Raymundo Jalpan', 1),
(1341, 20, '343', 'San Sebastián Abasolo', 1),
(1342, 20, '344', 'San Sebastián Coatlán', 1),
(1343, 20, '345', 'San Sebastián Ixcapa', 1),
(1344, 20, '346', 'San Sebastián Nicananduta', 1),
(1345, 20, '347', 'San Sebastián Río Hondo', 1),
(1346, 20, '348', 'San Sebastián Tecomaxtlahuaca', 1),
(1347, 20, '349', 'San Sebastián Teitipac', 1),
(1348, 20, '350', 'San Sebastián Tutla', 1),
(1349, 20, '351', 'San Simón Almolongas', 1),
(1350, 20, '352', 'San Simón Zahuatlán', 1),
(1351, 20, '353', 'Santa Ana', 1),
(1352, 20, '354', 'Santa Ana Ateixtlahuaca', 1),
(1353, 20, '355', 'Santa Ana Cuauhtémoc', 1),
(1354, 20, '356', 'Santa Ana del Valle', 1),
(1355, 20, '357', 'Santa Ana Tavela', 1),
(1356, 20, '358', 'Santa Ana Tlapacoyan', 1),
(1357, 20, '359', 'Santa Ana Yareni', 1),
(1358, 20, '360', 'Santa Ana Zegache', 1),
(1359, 20, '361', 'Santa Catalina Quierí', 1);
INSERT INTO `ciudades` (`id`, `estado_id`, `clave`, `nombre`, `activo`) VALUES
(1360, 20, '362', 'Santa Catarina Cuixtla', 1),
(1361, 20, '363', 'Santa Catarina Ixtepeji', 1),
(1362, 20, '364', 'Santa Catarina Juquila', 1),
(1363, 20, '365', 'Santa Catarina Lachatao', 1),
(1364, 20, '366', 'Santa Catarina Loxicha', 1),
(1365, 20, '367', 'Santa Catarina Mechoacán', 1),
(1366, 20, '368', 'Santa Catarina Minas', 1),
(1367, 20, '369', 'Santa Catarina Quiané', 1),
(1368, 20, '370', 'Santa Catarina Tayata', 1),
(1369, 20, '371', 'Santa Catarina Ticuá', 1),
(1370, 20, '372', 'Santa Catarina Yosonotú', 1),
(1371, 20, '373', 'Santa Catarina Zapoquila', 1),
(1372, 20, '374', 'Santa Cruz Acatepec', 1),
(1373, 20, '375', 'Santa Cruz Amilpas', 1),
(1374, 20, '376', 'Santa Cruz de Bravo', 1),
(1375, 20, '377', 'Santa Cruz Itundujia', 1),
(1376, 20, '378', 'Santa Cruz Mixtepec', 1),
(1377, 20, '379', 'Santa Cruz Nundaco', 1),
(1378, 20, '380', 'Santa Cruz Papalutla', 1),
(1379, 20, '381', 'Santa Cruz Tacache de Mina', 1),
(1380, 20, '382', 'Santa Cruz Tacahua', 1),
(1381, 20, '383', 'Santa Cruz Tayata', 1),
(1382, 20, '384', 'Santa Cruz Xitla', 1),
(1383, 20, '385', 'Santa Cruz Xoxocotlán', 1),
(1384, 20, '386', 'Santa Cruz Zenzontepec', 1),
(1385, 20, '387', 'Santa Gertrudis', 1),
(1386, 20, '388', 'Santa Inés del Monte', 1),
(1387, 20, '389', 'Santa Inés Yatzeche', 1),
(1388, 20, '390', 'Santa Lucía del Camino', 1),
(1389, 20, '391', 'Santa Lucía Miahuatlán', 1),
(1390, 20, '392', 'Santa Lucía Monteverde', 1),
(1391, 20, '393', 'Santa Lucía Ocotlán', 1),
(1392, 20, '394', 'Santa María Alotepec', 1),
(1393, 20, '395', 'Santa María Apazco', 1),
(1394, 20, '396', 'Santa María la Asunción', 1),
(1395, 20, '397', 'Heroica Ciudad de Tlaxiaco', 1),
(1396, 20, '398', 'Ayoquezco de Aldama', 1),
(1397, 20, '399', 'Santa María Atzompa', 1),
(1398, 20, '400', 'Santa María Camotlán', 1),
(1399, 20, '401', 'Santa María Colotepec', 1),
(1400, 20, '402', 'Santa María Cortijo', 1),
(1401, 20, '403', 'Santa María Coyotepec', 1),
(1402, 20, '404', 'Santa María Chachoápam', 1),
(1403, 20, '405', 'Villa de Chilapa de Díaz', 1),
(1404, 20, '406', 'Santa María Chilchotla', 1),
(1405, 20, '407', 'Santa María Chimalapa', 1),
(1406, 20, '408', 'Santa María del Rosario', 1),
(1407, 20, '409', 'Santa María del Tule', 1),
(1408, 20, '410', 'Santa María Ecatepec', 1),
(1409, 20, '411', 'Santa María Guelacé', 1),
(1410, 20, '412', 'Santa María Guienagati', 1),
(1411, 20, '413', 'Santa María Huatulco', 1),
(1412, 20, '414', 'Santa María Huazolotitlán', 1),
(1413, 20, '415', 'Santa María Ipalapa', 1),
(1414, 20, '416', 'Santa María Ixcatlán', 1),
(1415, 20, '417', 'Santa María Jacatepec', 1),
(1416, 20, '418', 'Santa María Jalapa del Marqués', 1),
(1417, 20, '419', 'Santa María Jaltianguis', 1),
(1418, 20, '420', 'Santa María Lachixío', 1),
(1419, 20, '421', 'Santa María Mixtequilla', 1),
(1420, 20, '422', 'Santa María Nativitas', 1),
(1421, 20, '423', 'Santa María Nduayaco', 1),
(1422, 20, '424', 'Santa María Ozolotepec', 1),
(1423, 20, '425', 'Santa María Pápalo', 1),
(1424, 20, '426', 'Santa María Peñoles', 1),
(1425, 20, '427', 'Santa María Petapa', 1),
(1426, 20, '428', 'Santa María Quiegolani', 1),
(1427, 20, '429', 'Santa María Sola', 1),
(1428, 20, '430', 'Santa María Tataltepec', 1),
(1429, 20, '431', 'Santa María Tecomavaca', 1),
(1430, 20, '432', 'Santa María Temaxcalapa', 1),
(1431, 20, '433', 'Santa María Temaxcaltepec', 1),
(1432, 20, '434', 'Santa María Teopoxco', 1),
(1433, 20, '435', 'Santa María Tepantlali', 1),
(1434, 20, '436', 'Santa María Texcatitlán', 1),
(1435, 20, '437', 'Santa María Tlahuitoltepec', 1),
(1436, 20, '438', 'Santa María Tlalixtac', 1),
(1437, 20, '439', 'Santa María Tonameca', 1),
(1438, 20, '440', 'Santa María Totolapilla', 1),
(1439, 20, '441', 'Santa María Xadani', 1),
(1440, 20, '442', 'Santa María Yalina', 1),
(1441, 20, '443', 'Santa María Yavesía', 1),
(1442, 20, '444', 'Santa María Yolotepec', 1),
(1443, 20, '445', 'Santa María Yosoyúa', 1),
(1444, 20, '446', 'Santa María Yucuhiti', 1),
(1445, 20, '447', 'Santa María Zacatepec', 1),
(1446, 20, '448', 'Santa María Zaniza', 1),
(1447, 20, '449', 'Santa María Zoquitlán', 1),
(1448, 20, '450', 'Santiago Amoltepec', 1),
(1449, 20, '451', 'Santiago Apoala', 1),
(1450, 20, '452', 'Santiago Apóstol', 1),
(1451, 20, '453', 'Santiago Astata', 1),
(1452, 20, '454', 'Santiago Atitlán', 1),
(1453, 20, '455', 'Santiago Ayuquililla', 1),
(1454, 20, '456', 'Santiago Cacaloxtepec', 1),
(1455, 20, '457', 'Santiago Camotlán', 1),
(1456, 20, '458', 'Santiago Comaltepec', 1),
(1457, 20, '459', 'Santiago Chazumba', 1),
(1458, 20, '460', 'Santiago Choápam', 1),
(1459, 20, '461', 'Santiago del Río', 1),
(1460, 20, '462', 'Santiago Huajolotitlán', 1),
(1461, 20, '463', 'Santiago Huauclilla', 1),
(1462, 20, '464', 'Santiago Ihuitlán Plumas', 1),
(1463, 20, '465', 'Santiago Ixcuintepec', 1),
(1464, 20, '466', 'Santiago Ixtayutla', 1),
(1465, 20, '467', 'Santiago Jamiltepec', 1),
(1466, 20, '468', 'Santiago Jocotepec', 1),
(1467, 20, '469', 'Santiago Juxtlahuaca', 1),
(1468, 20, '470', 'Santiago Lachiguiri', 1),
(1469, 20, '471', 'Santiago Lalopa', 1),
(1470, 20, '472', 'Santiago Laollaga', 1),
(1471, 20, '473', 'Santiago Laxopa', 1),
(1472, 20, '474', 'Santiago Llano Grande', 1),
(1473, 20, '475', 'Santiago Matatlán', 1),
(1474, 20, '476', 'Santiago Miltepec', 1),
(1475, 20, '477', 'Santiago Minas', 1),
(1476, 20, '478', 'Santiago Nacaltepec', 1),
(1477, 20, '479', 'Santiago Nejapilla', 1),
(1478, 20, '480', 'Santiago Nundiche', 1),
(1479, 20, '481', 'Santiago Nuyoó', 1),
(1480, 20, '482', 'Santiago Pinotepa Nacional', 1),
(1481, 20, '483', 'Santiago Suchilquitongo', 1),
(1482, 20, '484', 'Santiago Tamazola', 1),
(1483, 20, '485', 'Santiago Tapextla', 1),
(1484, 20, '486', 'Villa Tejúpam de la Unión', 1),
(1485, 20, '487', 'Santiago Tenango', 1),
(1486, 20, '488', 'Santiago Tepetlapa', 1),
(1487, 20, '489', 'Santiago Tetepec', 1),
(1488, 20, '490', 'Santiago Texcalcingo', 1),
(1489, 20, '491', 'Santiago Textitlán', 1),
(1490, 20, '492', 'Santiago Tilantongo', 1),
(1491, 20, '493', 'Santiago Tillo', 1),
(1492, 20, '494', 'Santiago Tlazoyaltepec', 1),
(1493, 20, '495', 'Santiago Xanica', 1),
(1494, 20, '496', 'Santiago Xiacuí', 1),
(1495, 20, '497', 'Santiago Yaitepec', 1),
(1496, 20, '498', 'Santiago Yaveo', 1),
(1497, 20, '499', 'Santiago Yolomécatl', 1),
(1498, 20, '500', 'Santiago Yosondúa', 1),
(1499, 20, '501', 'Santiago Yucuyachi', 1),
(1500, 20, '502', 'Santiago Zacatepec', 1),
(1501, 20, '503', 'Santiago Zoochila', 1),
(1502, 20, '504', 'Nuevo Zoquiápam', 1),
(1503, 20, '505', 'Santo Domingo Ingenio', 1),
(1504, 20, '506', 'Santo Domingo Albarradas', 1),
(1505, 20, '507', 'Santo Domingo Armenta', 1),
(1506, 20, '508', 'Santo Domingo Chihuitán', 1),
(1507, 20, '509', 'Santo Domingo de Morelos', 1),
(1508, 20, '510', 'Santo Domingo Ixcatlán', 1),
(1509, 20, '511', 'Santo Domingo Nuxaá', 1),
(1510, 20, '512', 'Santo Domingo Ozolotepec', 1),
(1511, 20, '513', 'Santo Domingo Petapa', 1),
(1512, 20, '514', 'Santo Domingo Roayaga', 1),
(1513, 20, '515', 'Santo Domingo Tehuantepec', 1),
(1514, 20, '516', 'Santo Domingo Teojomulco', 1),
(1515, 20, '517', 'Santo Domingo Tepuxtepec', 1),
(1516, 20, '518', 'Santo Domingo Tlatayápam', 1),
(1517, 20, '519', 'Santo Domingo Tomaltepec', 1),
(1518, 20, '520', 'Santo Domingo Tonalá', 1),
(1519, 20, '521', 'Santo Domingo Tonaltepec', 1),
(1520, 20, '522', 'Santo Domingo Xagacía', 1),
(1521, 20, '523', 'Santo Domingo Yanhuitlán', 1),
(1522, 20, '524', 'Santo Domingo Yodohino', 1),
(1523, 20, '525', 'Santo Domingo Zanatepec', 1),
(1524, 20, '526', 'Santos Reyes Nopala', 1),
(1525, 20, '527', 'Santos Reyes Pápalo', 1),
(1526, 20, '528', 'Santos Reyes Tepejillo', 1),
(1527, 20, '529', 'Santos Reyes Yucuná', 1),
(1528, 20, '530', 'Santo Tomás Jalieza', 1),
(1529, 20, '531', 'Santo Tomás Mazaltepec', 1),
(1530, 20, '532', 'Santo Tomás Ocotepec', 1),
(1531, 20, '533', 'Santo Tomás Tamazulapan', 1),
(1532, 20, '534', 'San Vicente Coatlán', 1),
(1533, 20, '535', 'San Vicente Lachixío', 1),
(1534, 20, '536', 'San Vicente Nuñú', 1),
(1535, 20, '537', 'Silacayoápam', 1),
(1536, 20, '538', 'Sitio de Xitlapehua', 1),
(1537, 20, '539', 'Soledad Etla', 1),
(1538, 20, '540', 'Villa de Tamazulápam del Progreso', 1),
(1539, 20, '541', 'Tanetze de Zaragoza', 1),
(1540, 20, '542', 'Taniche', 1),
(1541, 20, '543', 'Tataltepec de Valdés', 1),
(1542, 20, '544', 'Teococuilco de Marcos Pérez', 1),
(1543, 20, '545', 'Teotitlán de Flores Magón', 1),
(1544, 20, '546', 'Teotitlán del Valle', 1),
(1545, 20, '547', 'Teotongo', 1),
(1546, 20, '548', 'Tepelmeme Villa de Morelos', 1),
(1547, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1548, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1549, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1550, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1551, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1552, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1553, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1554, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1555, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1556, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1557, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1558, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1559, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1560, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1561, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1562, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1563, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1564, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1565, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1566, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1567, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1568, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1569, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1570, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1571, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1572, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1573, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1574, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1575, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1576, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1577, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1578, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1579, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1580, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1581, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1582, 20, '549', 'Heroica Villa Tezoatlán de Segura y Luna, Cuna de ', 1),
(1583, 20, '550', 'San Jerónimo Tlacochahuaya', 1),
(1584, 20, '551', 'Tlacolula de Matamoros', 1),
(1585, 20, '552', 'Tlacotepec Plumas', 1),
(1586, 20, '553', 'Tlalixtac de Cabrera', 1),
(1587, 20, '554', 'Totontepec Villa de Morelos', 1),
(1588, 20, '555', 'Trinidad Zaachila', 1),
(1589, 20, '556', 'La Trinidad Vista Hermosa', 1),
(1590, 20, '557', 'Unión Hidalgo', 1),
(1591, 20, '558', 'Valerio Trujano', 1),
(1592, 20, '559', 'San Juan Bautista Valle Nacional', 1),
(1593, 20, '560', 'Villa Díaz Ordaz', 1),
(1594, 20, '561', 'Yaxe', 1),
(1595, 20, '562', 'Magdalena Yodocono de Porfirio Díaz', 1),
(1596, 20, '563', 'Yogana', 1),
(1597, 20, '564', 'Yutanduchi de Guerrero', 1),
(1598, 20, '565', 'Villa de Zaachila', 1),
(1599, 20, '566', 'San Mateo Yucutindoo', 1),
(1600, 20, '567', 'Zapotitlán Lagunas', 1),
(1601, 20, '568', 'Zapotitlán Palmas', 1),
(1602, 20, '569', 'Santa Inés de Zaragoza', 1),
(1603, 20, '570', 'Zimatlán de Álvarez', 1),
(1604, 21, '001', 'Acajete', 1),
(1605, 21, '002', 'Acateno', 1),
(1606, 21, '003', 'Acatlán', 1),
(1607, 21, '004', 'Acatzingo', 1),
(1608, 21, '005', 'Acteopan', 1),
(1609, 21, '006', 'Ahuacatlán', 1),
(1610, 21, '007', 'Ahuatlán', 1),
(1611, 21, '008', 'Ahuazotepec', 1),
(1612, 21, '009', 'Ahuehuetitla', 1),
(1613, 21, '010', 'Ajalpan', 1),
(1614, 21, '011', 'Albino Zertuche', 1),
(1615, 21, '012', 'Aljojuca', 1),
(1616, 21, '013', 'Altepexi', 1),
(1617, 21, '014', 'Amixtlán', 1),
(1618, 21, '015', 'Amozoc', 1),
(1619, 21, '016', 'Aquixtla', 1),
(1620, 21, '017', 'Atempan', 1),
(1621, 21, '018', 'Atexcal', 1),
(1622, 21, '019', 'Atlixco', 1),
(1623, 21, '020', 'Atoyatempan', 1),
(1624, 21, '021', 'Atzala', 1),
(1625, 21, '022', 'Atzitzihuacán', 1),
(1626, 21, '023', 'Atzitzintla', 1),
(1627, 21, '024', 'Axutla', 1),
(1628, 21, '025', 'Ayotoxco de Guerrero', 1),
(1629, 21, '026', 'Calpan', 1),
(1630, 21, '027', 'Caltepec', 1),
(1631, 21, '028', 'Camocuautla', 1),
(1632, 21, '029', 'Caxhuacan', 1),
(1633, 21, '030', 'Coatepec', 1),
(1634, 21, '031', 'Coatzingo', 1),
(1635, 21, '032', 'Cohetzala', 1),
(1636, 21, '033', 'Cohuecan', 1),
(1637, 21, '034', 'Coronango', 1),
(1638, 21, '035', 'Coxcatlán', 1),
(1639, 21, '036', 'Coyomeapan', 1),
(1640, 21, '037', 'Coyotepec', 1),
(1641, 21, '038', 'Cuapiaxtla de Madero', 1),
(1642, 21, '039', 'Cuautempan', 1),
(1643, 21, '040', 'Cuautinchán', 1),
(1644, 21, '041', 'Cuautlancingo', 1),
(1645, 21, '042', 'Cuayuca de Andrade', 1),
(1646, 21, '043', 'Cuetzalan del Progreso', 1),
(1647, 21, '044', 'Cuyoaco', 1),
(1648, 21, '045', 'Chalchicomula de Sesma', 1),
(1649, 21, '046', 'Chapulco', 1),
(1650, 21, '047', 'Chiautla', 1),
(1651, 21, '048', 'Chiautzingo', 1),
(1652, 21, '049', 'Chiconcuautla', 1),
(1653, 21, '050', 'Chichiquila', 1),
(1654, 21, '051', 'Chietla', 1),
(1655, 21, '052', 'Chigmecatitlán', 1),
(1656, 21, '053', 'Chignahuapan', 1),
(1657, 21, '054', 'Chignautla', 1),
(1658, 21, '055', 'Chila', 1),
(1659, 21, '056', 'Chila de la Sal', 1),
(1660, 21, '057', 'Honey', 1),
(1661, 21, '058', 'Chilchotla', 1),
(1662, 21, '059', 'Chinantla', 1),
(1663, 21, '060', 'Domingo Arenas', 1),
(1664, 21, '061', 'Eloxochitlán', 1),
(1665, 21, '062', 'Epatlán', 1),
(1666, 21, '063', 'Esperanza', 1),
(1667, 21, '064', 'Francisco Z. Mena', 1),
(1668, 21, '065', 'General Felipe Ángeles', 1),
(1669, 21, '066', 'Guadalupe', 1),
(1670, 21, '067', 'Guadalupe Victoria', 1),
(1671, 21, '068', 'Hermenegildo Galeana', 1),
(1672, 21, '069', 'Huaquechula', 1),
(1673, 21, '070', 'Huatlatlauca', 1),
(1674, 21, '071', 'Huauchinango', 1),
(1675, 21, '072', 'Huehuetla', 1),
(1676, 21, '073', 'Huehuetlán el Chico', 1),
(1677, 21, '074', 'Huejotzingo', 1),
(1678, 21, '075', 'Hueyapan', 1),
(1679, 21, '076', 'Hueytamalco', 1),
(1680, 21, '077', 'Hueytlalpan', 1),
(1681, 21, '078', 'Huitzilan de Serdán', 1),
(1682, 21, '079', 'Huitziltepec', 1),
(1683, 21, '080', 'Atlequizayan', 1),
(1684, 21, '081', 'Ixcamilpa de Guerrero', 1),
(1685, 21, '082', 'Ixcaquixtla', 1),
(1686, 21, '083', 'Ixtacamaxtitlán', 1),
(1687, 21, '084', 'Ixtepec', 1),
(1688, 21, '085', 'Izúcar de Matamoros', 1),
(1689, 21, '086', 'Jalpan', 1),
(1690, 21, '087', 'Jolalpan', 1),
(1691, 21, '088', 'Jonotla', 1),
(1692, 21, '089', 'Jopala', 1),
(1693, 21, '090', 'Juan C. Bonilla', 1),
(1694, 21, '091', 'Juan Galindo', 1),
(1695, 21, '092', 'Juan N. Méndez', 1),
(1696, 21, '093', 'Lafragua', 1),
(1697, 21, '094', 'Libres', 1),
(1698, 21, '095', 'La Magdalena Tlatlauquitepec', 1),
(1699, 21, '096', 'Mazapiltepec de Juárez', 1),
(1700, 21, '097', 'Mixtla', 1),
(1701, 21, '098', 'Molcaxac', 1),
(1702, 21, '099', 'Cañada Morelos', 1),
(1703, 21, '100', 'Naupan', 1),
(1704, 21, '101', 'Nauzontla', 1),
(1705, 21, '102', 'Nealtican', 1),
(1706, 21, '103', 'Nicolás Bravo', 1),
(1707, 21, '104', 'Nopalucan', 1),
(1708, 21, '105', 'Ocotepec', 1),
(1709, 21, '106', 'Ocoyucan', 1),
(1710, 21, '107', 'Olintla', 1),
(1711, 21, '108', 'Oriental', 1),
(1712, 21, '109', 'Pahuatlán', 1),
(1713, 21, '110', 'Palmar de Bravo', 1),
(1714, 21, '111', 'Pantepec', 1),
(1715, 21, '112', 'Petlalcingo', 1),
(1716, 21, '113', 'Piaxtla', 1),
(1717, 21, '114', 'Puebla', 1),
(1718, 21, '115', 'Quecholac', 1),
(1719, 21, '116', 'Quimixtlán', 1),
(1720, 21, '117', 'Rafael Lara Grajales', 1),
(1721, 21, '118', 'Los Reyes de Juárez', 1),
(1722, 21, '119', 'San Andrés Cholula', 1),
(1723, 21, '120', 'San Antonio Cañada', 1),
(1724, 21, '121', 'San Diego la Mesa Tochimiltzingo', 1),
(1725, 21, '122', 'San Felipe Teotlalcingo', 1),
(1726, 21, '123', 'San Felipe Tepatlán', 1),
(1727, 21, '124', 'San Gabriel Chilac', 1),
(1728, 21, '125', 'San Gregorio Atzompa', 1),
(1729, 21, '126', 'San Jerónimo Tecuanipan', 1),
(1730, 21, '127', 'San Jerónimo Xayacatlán', 1),
(1731, 21, '128', 'San José Chiapa', 1),
(1732, 21, '129', 'San José Miahuatlán', 1),
(1733, 21, '130', 'San Juan Atenco', 1),
(1734, 21, '131', 'San Juan Atzompa', 1),
(1735, 21, '132', 'San Martín Texmelucan', 1),
(1736, 21, '133', 'San Martín Totoltepec', 1),
(1737, 21, '134', 'San Matías Tlalancaleca', 1),
(1738, 21, '135', 'San Miguel Ixitlán', 1),
(1739, 21, '136', 'San Miguel Xoxtla', 1),
(1740, 21, '137', 'San Nicolás Buenos Aires', 1),
(1741, 21, '138', 'San Nicolás de los Ranchos', 1),
(1742, 21, '139', 'San Pablo Anicano', 1),
(1743, 21, '140', 'San Pedro Cholula', 1),
(1744, 21, '141', 'San Pedro Yeloixtlahuaca', 1),
(1745, 21, '142', 'San Salvador el Seco', 1),
(1746, 21, '143', 'San Salvador el Verde', 1),
(1747, 21, '144', 'San Salvador Huixcolotla', 1),
(1748, 21, '145', 'San Sebastián Tlacotepec', 1),
(1749, 21, '146', 'Santa Catarina Tlaltempan', 1),
(1750, 21, '147', 'Santa Inés Ahuatempan', 1),
(1751, 21, '148', 'Santa Isabel Cholula', 1),
(1752, 21, '149', 'Santiago Miahuatlán', 1),
(1753, 21, '150', 'Huehuetlán el Grande', 1),
(1754, 21, '151', 'Santo Tomás Hueyotlipan', 1),
(1755, 21, '152', 'Soltepec', 1),
(1756, 21, '153', 'Tecali de Herrera', 1),
(1757, 21, '154', 'Tecamachalco', 1),
(1758, 21, '155', 'Tecomatlán', 1),
(1759, 21, '156', 'Tehuacán', 1),
(1760, 21, '157', 'Tehuitzingo', 1),
(1761, 21, '158', 'Tenampulco', 1),
(1762, 21, '159', 'Teopantlán', 1),
(1763, 21, '160', 'Teotlalco', 1),
(1764, 21, '161', 'Tepanco de López', 1),
(1765, 21, '162', 'Tepango de Rodríguez', 1),
(1766, 21, '163', 'Tepatlaxco de Hidalgo', 1),
(1767, 21, '164', 'Tepeaca', 1),
(1768, 21, '165', 'Tepemaxalco', 1),
(1769, 21, '166', 'Tepeojuma', 1),
(1770, 21, '167', 'Tepetzintla', 1),
(1771, 21, '168', 'Tepexco', 1),
(1772, 21, '169', 'Tepexi de Rodríguez', 1),
(1773, 21, '170', 'Tepeyahualco', 1),
(1774, 21, '171', 'Tepeyahualco de Cuauhtémoc', 1),
(1775, 21, '172', 'Tetela de Ocampo', 1),
(1776, 21, '173', 'Teteles de Avila Castillo', 1),
(1777, 21, '174', 'Teziutlán', 1),
(1778, 21, '175', 'Tianguismanalco', 1),
(1779, 21, '176', 'Tilapa', 1),
(1780, 21, '177', 'Tlacotepec de Benito Juárez', 1),
(1781, 21, '178', 'Tlacuilotepec', 1),
(1782, 21, '179', 'Tlachichuca', 1),
(1783, 21, '180', 'Tlahuapan', 1),
(1784, 21, '181', 'Tlaltenango', 1),
(1785, 21, '182', 'Tlanepantla', 1),
(1786, 21, '183', 'Tlaola', 1),
(1787, 21, '184', 'Tlapacoya', 1),
(1788, 21, '185', 'Tlapanalá', 1),
(1789, 21, '186', 'Tlatlauquitepec', 1),
(1790, 21, '187', 'Tlaxco', 1),
(1791, 21, '188', 'Tochimilco', 1),
(1792, 21, '189', 'Tochtepec', 1),
(1793, 21, '190', 'Totoltepec de Guerrero', 1),
(1794, 21, '191', 'Tulcingo', 1),
(1795, 21, '192', 'Tuzamapan de Galeana', 1),
(1796, 21, '193', 'Tzicatlacoyan', 1),
(1797, 21, '194', 'Venustiano Carranza', 1),
(1798, 21, '195', 'Vicente Guerrero', 1),
(1799, 21, '196', 'Xayacatlán de Bravo', 1),
(1800, 21, '197', 'Xicotepec', 1),
(1801, 21, '198', 'Xicotlán', 1),
(1802, 21, '199', 'Xiutetelco', 1),
(1803, 21, '200', 'Xochiapulco', 1),
(1804, 21, '201', 'Xochiltepec', 1),
(1805, 21, '202', 'Xochitlán de Vicente Suárez', 1),
(1806, 21, '203', 'Xochitlán Todos Santos', 1),
(1807, 21, '204', 'Yaonáhuac', 1),
(1808, 21, '205', 'Yehualtepec', 1),
(1809, 21, '206', 'Zacapala', 1),
(1810, 21, '207', 'Zacapoaxtla', 1),
(1811, 21, '208', 'Zacatlán', 1),
(1812, 21, '209', 'Zapotitlán', 1),
(1813, 21, '210', 'Zapotitlán de Méndez', 1),
(1814, 21, '211', 'Zaragoza', 1),
(1815, 21, '212', 'Zautla', 1),
(1816, 21, '213', 'Zihuateutla', 1),
(1817, 21, '214', 'Zinacatepec', 1),
(1818, 21, '215', 'Zongozotla', 1),
(1819, 21, '216', 'Zoquiapan', 1),
(1820, 21, '217', 'Zoquitlán', 1),
(1821, 22, '001', 'Amealco de Bonfil', 1),
(1822, 22, '002', 'Pinal de Amoles', 1),
(1823, 22, '003', 'Arroyo Seco', 1),
(1824, 22, '004', 'Cadereyta de Montes', 1),
(1825, 22, '005', 'Colón', 1),
(1826, 22, '006', 'Corregidora', 1),
(1827, 22, '007', 'Ezequiel Montes', 1),
(1828, 22, '008', 'Huimilpan', 1),
(1829, 22, '009', 'Jalpan de Serra', 1),
(1830, 22, '010', 'Landa de Matamoros', 1),
(1831, 22, '011', 'El Marqués', 1),
(1832, 22, '012', 'Pedro Escobedo', 1),
(1833, 22, '013', 'Peñamiller', 1),
(1834, 22, '014', 'Querétaro', 1),
(1835, 22, '015', 'San Joaquín', 1),
(1836, 22, '016', 'San Juan del Río', 1),
(1837, 22, '017', 'Tequisquiapan', 1),
(1838, 22, '018', 'Tolimán', 1),
(1839, 23, '001', 'Cozumel', 1),
(1840, 23, '002', 'Felipe Carrillo Puerto', 1),
(1841, 23, '003', 'Isla Mujeres', 1),
(1842, 23, '004', 'Othón P. Blanco', 1),
(1843, 23, '005', 'Benito Juárez', 1),
(1844, 23, '006', 'José María Morelos', 1),
(1845, 23, '007', 'Lázaro Cárdenas', 1),
(1846, 23, '008', 'Solidaridad', 1),
(1847, 23, '009', 'Tulum', 1),
(1848, 23, '010', 'Bacalar', 1),
(1849, 24, '001', 'Ahualulco', 1),
(1850, 24, '002', 'Alaquines', 1),
(1851, 24, '003', 'Aquismón', 1),
(1852, 24, '004', 'Armadillo de los Infante', 1),
(1853, 24, '005', 'Cárdenas', 1),
(1854, 24, '006', 'Catorce', 1),
(1855, 24, '007', 'Cedral', 1),
(1856, 24, '008', 'Cerritos', 1),
(1857, 24, '009', 'Cerro de San Pedro', 1),
(1858, 24, '010', 'Ciudad del Maíz', 1),
(1859, 24, '011', 'Ciudad Fernández', 1),
(1860, 24, '012', 'Tancanhuitz', 1),
(1861, 24, '013', 'Ciudad Valles', 1),
(1862, 24, '014', 'Coxcatlán', 1),
(1863, 24, '015', 'Charcas', 1),
(1864, 24, '016', 'Ebano', 1),
(1865, 24, '017', 'Guadalcázar', 1),
(1866, 24, '018', 'Huehuetlán', 1),
(1867, 24, '019', 'Lagunillas', 1),
(1868, 24, '020', 'Matehuala', 1),
(1869, 24, '021', 'Mexquitic de Carmona', 1),
(1870, 24, '022', 'Moctezuma', 1),
(1871, 24, '023', 'Rayón', 1),
(1872, 24, '024', 'Rioverde', 1),
(1873, 24, '025', 'Salinas', 1),
(1874, 24, '026', 'San Antonio', 1),
(1875, 24, '027', 'San Ciro de Acosta', 1),
(1876, 24, '028', 'San Luis Potosí', 1),
(1877, 24, '029', 'San Martín Chalchicuautla', 1),
(1878, 24, '030', 'San Nicolás Tolentino', 1),
(1879, 24, '031', 'Santa Catarina', 1),
(1880, 24, '032', 'Santa María del Río', 1),
(1881, 24, '033', 'Santo Domingo', 1),
(1882, 24, '034', 'San Vicente Tancuayalab', 1),
(1883, 24, '035', 'Soledad de Graciano Sánchez', 1),
(1884, 24, '036', 'Tamasopo', 1),
(1885, 24, '037', 'Tamazunchale', 1),
(1886, 24, '038', 'Tampacán', 1),
(1887, 24, '039', 'Tampamolón Corona', 1),
(1888, 24, '040', 'Tamuín', 1),
(1889, 24, '041', 'Tanlajás', 1),
(1890, 24, '042', 'Tanquián de Escobedo', 1),
(1891, 24, '043', 'Tierra Nueva', 1),
(1892, 24, '044', 'Vanegas', 1),
(1893, 24, '045', 'Venado', 1),
(1894, 24, '046', 'Villa de Arriaga', 1),
(1895, 24, '047', 'Villa de Guadalupe', 1),
(1896, 24, '048', 'Villa de la Paz', 1),
(1897, 24, '049', 'Villa de Ramos', 1),
(1898, 24, '050', 'Villa de Reyes', 1),
(1899, 24, '051', 'Villa Hidalgo', 1),
(1900, 24, '052', 'Villa Juárez', 1),
(1901, 24, '053', 'Axtla de Terrazas', 1),
(1902, 24, '054', 'Xilitla', 1),
(1903, 24, '055', 'Zaragoza', 1),
(1904, 24, '056', 'Villa de Arista', 1),
(1905, 24, '057', 'Matlapa', 1),
(1906, 24, '058', 'El Naranjo', 1),
(1907, 25, '001', 'Ahome', 1),
(1908, 25, '002', 'Angostura', 1),
(1909, 25, '003', 'Badiraguato', 1),
(1910, 25, '004', 'Concordia', 1),
(1911, 25, '005', 'Cosalá', 1),
(1912, 25, '006', 'Culiacán', 1),
(1913, 25, '007', 'Choix', 1),
(1914, 25, '008', 'Elota', 1),
(1915, 25, '009', 'Escuinapa', 1),
(1916, 25, '010', 'El Fuerte', 1),
(1917, 25, '011', 'Guasave', 1),
(1918, 25, '012', 'Mazatlán', 1),
(1919, 25, '013', 'Mocorito', 1),
(1920, 25, '014', 'Rosario', 1),
(1921, 25, '015', 'Salvador Alvarado', 1),
(1922, 25, '016', 'San Ignacio', 1),
(1923, 25, '017', 'Sinaloa', 1),
(1924, 25, '018', 'Navolato', 1),
(1925, 26, '001', 'Aconchi', 1),
(1926, 26, '002', 'Agua Prieta', 1),
(1927, 26, '003', 'Alamos', 1),
(1928, 26, '004', 'Altar', 1),
(1929, 26, '005', 'Arivechi', 1),
(1930, 26, '006', 'Arizpe', 1),
(1931, 26, '007', 'Atil', 1),
(1932, 26, '008', 'Bacadéhuachi', 1),
(1933, 26, '009', 'Bacanora', 1),
(1934, 26, '010', 'Bacerac', 1),
(1935, 26, '011', 'Bacoachi', 1),
(1936, 26, '012', 'Bácum', 1),
(1937, 26, '013', 'Banámichi', 1),
(1938, 26, '014', 'Baviácora', 1),
(1939, 26, '015', 'Bavispe', 1),
(1940, 26, '016', 'Benjamín Hill', 1),
(1941, 26, '017', 'Caborca', 1),
(1942, 26, '018', 'Cajeme', 1),
(1943, 26, '019', 'Cananea', 1),
(1944, 26, '020', 'Carbó', 1),
(1945, 26, '021', 'La Colorada', 1),
(1946, 26, '022', 'Cucurpe', 1),
(1947, 26, '023', 'Cumpas', 1),
(1948, 26, '024', 'Divisaderos', 1),
(1949, 26, '025', 'Empalme', 1),
(1950, 26, '026', 'Etchojoa', 1),
(1951, 26, '027', 'Fronteras', 1),
(1952, 26, '028', 'Granados', 1),
(1953, 26, '029', 'Guaymas', 1),
(1954, 26, '030', 'Hermosillo', 1),
(1955, 26, '031', 'Huachinera', 1),
(1956, 26, '032', 'Huásabas', 1),
(1957, 26, '033', 'Huatabampo', 1),
(1958, 26, '034', 'Huépac', 1),
(1959, 26, '035', 'Imuris', 1),
(1960, 26, '036', 'Magdalena', 1),
(1961, 26, '037', 'Mazatán', 1),
(1962, 26, '038', 'Moctezuma', 1),
(1963, 26, '039', 'Naco', 1),
(1964, 26, '040', 'Nácori Chico', 1),
(1965, 26, '041', 'Nacozari de García', 1),
(1966, 26, '042', 'Navojoa', 1),
(1967, 26, '043', 'Nogales', 1),
(1968, 26, '044', 'Onavas', 1),
(1969, 26, '045', 'Opodepe', 1),
(1970, 26, '046', 'Oquitoa', 1),
(1971, 26, '047', 'Pitiquito', 1),
(1972, 26, '048', 'Puerto Peñasco', 1),
(1973, 26, '049', 'Quiriego', 1),
(1974, 26, '050', 'Rayón', 1),
(1975, 26, '051', 'Rosario', 1),
(1976, 26, '052', 'Sahuaripa', 1),
(1977, 26, '053', 'San Felipe de Jesús', 1),
(1978, 26, '054', 'San Javier', 1),
(1979, 26, '055', 'San Luis Río Colorado', 1),
(1980, 26, '056', 'San Miguel de Horcasitas', 1),
(1981, 26, '057', 'San Pedro de la Cueva', 1),
(1982, 26, '058', 'Santa Ana', 1),
(1983, 26, '059', 'Santa Cruz', 1),
(1984, 26, '060', 'Sáric', 1),
(1985, 26, '061', 'Soyopa', 1),
(1986, 26, '062', 'Suaqui Grande', 1),
(1987, 26, '063', 'Tepache', 1),
(1988, 26, '064', 'Trincheras', 1),
(1989, 26, '065', 'Tubutama', 1),
(1990, 26, '066', 'Ures', 1),
(1991, 26, '067', 'Villa Hidalgo', 1),
(1992, 26, '068', 'Villa Pesqueira', 1),
(1993, 26, '069', 'Yécora', 1),
(1994, 26, '070', 'General Plutarco Elías Calles', 1),
(1995, 26, '071', 'Benito Juárez', 1),
(1996, 26, '072', 'San Ignacio Río Muerto', 1),
(1997, 27, '001', 'Balancán', 1),
(1998, 27, '002', 'Cárdenas', 1),
(1999, 27, '003', 'Centla', 1),
(2000, 27, '004', 'Centro', 1),
(2001, 27, '005', 'Comalcalco', 1),
(2002, 27, '006', 'Cunduacán', 1),
(2003, 27, '007', 'Emiliano Zapata', 1),
(2004, 27, '008', 'Huimanguillo', 1),
(2005, 27, '009', 'Jalapa', 1),
(2006, 27, '010', 'Jalpa de Méndez', 1),
(2007, 27, '011', 'Jonuta', 1),
(2008, 27, '012', 'Macuspana', 1),
(2009, 27, '013', 'Nacajuca', 1),
(2010, 27, '014', 'Paraíso', 1),
(2011, 27, '015', 'Tacotalpa', 1),
(2012, 27, '016', 'Teapa', 1),
(2013, 27, '017', 'Tenosique', 1),
(2014, 28, '001', 'Abasolo', 1),
(2015, 28, '002', 'Aldama', 1),
(2016, 28, '003', 'Altamira', 1),
(2017, 28, '004', 'Antiguo Morelos', 1),
(2018, 28, '005', 'Burgos', 1),
(2019, 28, '006', 'Bustamante', 1),
(2020, 28, '007', 'Camargo', 1),
(2021, 28, '008', 'Casas', 1),
(2022, 28, '009', 'Ciudad Madero', 1),
(2023, 28, '010', 'Cruillas', 1),
(2024, 28, '011', 'Gómez Farías', 1),
(2025, 28, '012', 'González', 1),
(2026, 28, '013', 'Güémez', 1),
(2027, 28, '014', 'Guerrero', 1),
(2028, 28, '015', 'Gustavo Díaz Ordaz', 1),
(2029, 28, '016', 'Hidalgo', 1),
(2030, 28, '017', 'Jaumave', 1),
(2031, 28, '018', 'Jiménez', 1),
(2032, 28, '019', 'Llera', 1),
(2033, 28, '020', 'Mainero', 1),
(2034, 28, '021', 'El Mante', 1),
(2035, 28, '022', 'Matamoros', 1),
(2036, 28, '023', 'Méndez', 1),
(2037, 28, '024', 'Mier', 1),
(2038, 28, '025', 'Miguel Alemán', 1),
(2039, 28, '026', 'Miquihuana', 1),
(2040, 28, '027', 'Nuevo Laredo', 1),
(2041, 28, '028', 'Nuevo Morelos', 1),
(2042, 28, '029', 'Ocampo', 1),
(2043, 28, '030', 'Padilla', 1),
(2044, 28, '031', 'Palmillas', 1),
(2045, 28, '032', 'Reynosa', 1),
(2046, 28, '033', 'Río Bravo', 1),
(2047, 28, '034', 'San Carlos', 1),
(2048, 28, '035', 'San Fernando', 1),
(2049, 28, '036', 'San Nicolás', 1),
(2050, 28, '037', 'Soto la Marina', 1),
(2051, 28, '038', 'Tampico', 1),
(2052, 28, '039', 'Tula', 1),
(2053, 28, '040', 'Valle Hermoso', 1),
(2054, 28, '041', 'Victoria', 1),
(2055, 28, '042', 'Villagrán', 1),
(2056, 28, '043', 'Xicoténcatl', 1),
(2057, 29, '001', 'Amaxac de Guerrero', 1),
(2058, 29, '002', 'Apetatitlán de Antonio Carvajal', 1),
(2059, 29, '003', 'Atlangatepec', 1),
(2060, 29, '004', 'Atltzayanca', 1),
(2061, 29, '005', 'Apizaco', 1),
(2062, 29, '006', 'Calpulalpan', 1),
(2063, 29, '007', 'El Carmen Tequexquitla', 1),
(2064, 29, '008', 'Cuapiaxtla', 1),
(2065, 29, '009', 'Cuaxomulco', 1),
(2066, 29, '010', 'Chiautempan', 1),
(2067, 29, '011', 'Muñoz de Domingo Arenas', 1),
(2068, 29, '012', 'Españita', 1),
(2069, 29, '013', 'Huamantla', 1),
(2070, 29, '014', 'Hueyotlipan', 1),
(2071, 29, '015', 'Ixtacuixtla de Mariano Matamoros', 1),
(2072, 29, '016', 'Ixtenco', 1),
(2073, 29, '017', 'Mazatecochco de José María Morelos', 1),
(2074, 29, '018', 'Contla de Juan Cuamatzi', 1),
(2075, 29, '019', 'Tepetitla de Lardizábal', 1),
(2076, 29, '020', 'Sanctórum de Lázaro Cárdenas', 1),
(2077, 29, '021', 'Nanacamilpa de Mariano Arista', 1),
(2078, 29, '022', 'Acuamanala de Miguel Hidalgo', 1),
(2079, 29, '023', 'Natívitas', 1),
(2080, 29, '024', 'Panotla', 1),
(2081, 29, '025', 'San Pablo del Monte', 1),
(2082, 29, '026', 'Santa Cruz Tlaxcala', 1),
(2083, 29, '027', 'Tenancingo', 1),
(2084, 29, '028', 'Teolocholco', 1),
(2085, 29, '029', 'Tepeyanco', 1),
(2086, 29, '030', 'Terrenate', 1),
(2087, 29, '031', 'Tetla de la Solidaridad', 1),
(2088, 29, '032', 'Tetlatlahuca', 1),
(2089, 29, '033', 'Tlaxcala', 1),
(2090, 29, '034', 'Tlaxco', 1),
(2091, 29, '035', 'Tocatlán', 1),
(2092, 29, '036', 'Totolac', 1),
(2093, 29, '037', 'Ziltlaltépec de Trinidad Sánchez Santos', 1),
(2094, 29, '038', 'Tzompantepec', 1),
(2095, 29, '039', 'Xaloztoc', 1),
(2096, 29, '040', 'Xaltocan', 1),
(2097, 29, '041', 'Papalotla de Xicohténcatl', 1),
(2098, 29, '042', 'Xicohtzinco', 1),
(2099, 29, '043', 'Yauhquemehcan', 1),
(2100, 29, '044', 'Zacatelco', 1),
(2101, 29, '045', 'Benito Juárez', 1),
(2102, 29, '046', 'Emiliano Zapata', 1),
(2103, 29, '047', 'Lázaro Cárdenas', 1),
(2104, 29, '048', 'La Magdalena Tlaltelulco', 1),
(2105, 29, '049', 'San Damián Texóloc', 1),
(2106, 29, '050', 'San Francisco Tetlanohcan', 1),
(2107, 29, '051', 'San Jerónimo Zacualpan', 1),
(2108, 29, '052', 'San José Teacalco', 1),
(2109, 29, '053', 'San Juan Huactzinco', 1),
(2110, 29, '054', 'San Lorenzo Axocomanitla', 1),
(2111, 29, '055', 'San Lucas Tecopilco', 1),
(2112, 29, '056', 'Santa Ana Nopalucan', 1),
(2113, 29, '057', 'Santa Apolonia Teacalco', 1),
(2114, 29, '058', 'Santa Catarina Ayometla', 1),
(2115, 29, '059', 'Santa Cruz Quilehtla', 1),
(2116, 29, '060', 'Santa Isabel Xiloxoxtla', 1),
(2117, 30, '001', 'Acajete', 1),
(2118, 30, '002', 'Acatlán', 1),
(2119, 30, '003', 'Acayucan', 1),
(2120, 30, '004', 'Actopan', 1),
(2121, 30, '005', 'Acula', 1),
(2122, 30, '006', 'Acultzingo', 1),
(2123, 30, '007', 'Camarón de Tejeda', 1),
(2124, 30, '008', 'Alpatláhuac', 1),
(2125, 30, '009', 'Alto Lucero de Gutiérrez Barrios', 1),
(2126, 30, '010', 'Altotonga', 1),
(2127, 30, '011', 'Alvarado', 1),
(2128, 30, '012', 'Amatitlán', 1),
(2129, 30, '013', 'Naranjos Amatlán', 1),
(2130, 30, '014', 'Amatlán de los Reyes', 1),
(2131, 30, '015', 'Angel R. Cabada', 1),
(2132, 30, '016', 'La Antigua', 1),
(2133, 30, '017', 'Apazapan', 1),
(2134, 30, '018', 'Aquila', 1),
(2135, 30, '019', 'Astacinga', 1),
(2136, 30, '020', 'Atlahuilco', 1),
(2137, 30, '021', 'Atoyac', 1),
(2138, 30, '022', 'Atzacan', 1),
(2139, 30, '023', 'Atzalan', 1),
(2140, 30, '024', 'Tlaltetela', 1),
(2141, 30, '025', 'Ayahualulco', 1),
(2142, 30, '026', 'Banderilla', 1),
(2143, 30, '027', 'Benito Juárez', 1),
(2144, 30, '028', 'Boca del Río', 1),
(2145, 30, '029', 'Calcahualco', 1),
(2146, 30, '030', 'Camerino Z. Mendoza', 1),
(2147, 30, '031', 'Carrillo Puerto', 1),
(2148, 30, '032', 'Catemaco', 1),
(2149, 30, '033', 'Cazones de Herrera', 1),
(2150, 30, '034', 'Cerro Azul', 1),
(2151, 30, '035', 'Citlaltépetl', 1),
(2152, 30, '036', 'Coacoatzintla', 1),
(2153, 30, '037', 'Coahuitlán', 1),
(2154, 30, '038', 'Coatepec', 1),
(2155, 30, '039', 'Coatzacoalcos', 1),
(2156, 30, '040', 'Coatzintla', 1),
(2157, 30, '041', 'Coetzala', 1),
(2158, 30, '042', 'Colipa', 1),
(2159, 30, '043', 'Comapa', 1),
(2160, 30, '044', 'Córdoba', 1),
(2161, 30, '045', 'Cosamaloapan de Carpio', 1),
(2162, 30, '046', 'Cosautlán de Carvajal', 1),
(2163, 30, '047', 'Coscomatepec', 1),
(2164, 30, '048', 'Cosoleacaque', 1),
(2165, 30, '049', 'Cotaxtla', 1),
(2166, 30, '050', 'Coxquihui', 1),
(2167, 30, '051', 'Coyutla', 1),
(2168, 30, '052', 'Cuichapa', 1),
(2169, 30, '053', 'Cuitláhuac', 1),
(2170, 30, '054', 'Chacaltianguis', 1),
(2171, 30, '055', 'Chalma', 1),
(2172, 30, '056', 'Chiconamel', 1),
(2173, 30, '057', 'Chiconquiaco', 1),
(2174, 30, '058', 'Chicontepec', 1),
(2175, 30, '059', 'Chinameca', 1),
(2176, 30, '060', 'Chinampa de Gorostiza', 1),
(2177, 30, '061', 'Las Choapas', 1),
(2178, 30, '062', 'Chocamán', 1),
(2179, 30, '063', 'Chontla', 1),
(2180, 30, '064', 'Chumatlán', 1),
(2181, 30, '065', 'Emiliano Zapata', 1),
(2182, 30, '066', 'Espinal', 1),
(2183, 30, '067', 'Filomeno Mata', 1),
(2184, 30, '068', 'Fortín', 1),
(2185, 30, '069', 'Gutiérrez Zamora', 1),
(2186, 30, '070', 'Hidalgotitlán', 1),
(2187, 30, '071', 'Huatusco', 1),
(2188, 30, '072', 'Huayacocotla', 1),
(2189, 30, '073', 'Hueyapan de Ocampo', 1),
(2190, 30, '074', 'Huiloapan de Cuauhtémoc', 1),
(2191, 30, '075', 'Ignacio de la Llave', 1),
(2192, 30, '076', 'Ilamatlán', 1),
(2193, 30, '077', 'Isla', 1),
(2194, 30, '078', 'Ixcatepec', 1),
(2195, 30, '079', 'Ixhuacán de los Reyes', 1),
(2196, 30, '080', 'Ixhuatlán del Café', 1),
(2197, 30, '081', 'Ixhuatlancillo', 1),
(2198, 30, '082', 'Ixhuatlán del Sureste', 1),
(2199, 30, '083', 'Ixhuatlán de Madero', 1),
(2200, 30, '084', 'Ixmatlahuacan', 1),
(2201, 30, '085', 'Ixtaczoquitlán', 1),
(2202, 30, '086', 'Jalacingo', 1),
(2203, 30, '087', 'Xalapa', 1),
(2204, 30, '088', 'Jalcomulco', 1),
(2205, 30, '089', 'Jáltipan', 1),
(2206, 30, '090', 'Jamapa', 1),
(2207, 30, '091', 'Jesús Carranza', 1),
(2208, 30, '092', 'Xico', 1),
(2209, 30, '093', 'Jilotepec', 1),
(2210, 30, '094', 'Juan Rodríguez Clara', 1),
(2211, 30, '095', 'Juchique de Ferrer', 1),
(2212, 30, '096', 'Landero y Coss', 1),
(2213, 30, '097', 'Lerdo de Tejada', 1),
(2214, 30, '098', 'Magdalena', 1),
(2215, 30, '099', 'Maltrata', 1),
(2216, 30, '100', 'Manlio Fabio Altamirano', 1),
(2217, 30, '101', 'Mariano Escobedo', 1),
(2218, 30, '102', 'Martínez de la Torre', 1),
(2219, 30, '103', 'Mecatlán', 1),
(2220, 30, '104', 'Mecayapan', 1),
(2221, 30, '105', 'Medellín de Bravo', 1),
(2222, 30, '106', 'Miahuatlán', 1),
(2223, 30, '107', 'Las Minas', 1),
(2224, 30, '108', 'Minatitlán', 1),
(2225, 30, '109', 'Misantla', 1),
(2226, 30, '110', 'Mixtla de Altamirano', 1),
(2227, 30, '111', 'Moloacán', 1),
(2228, 30, '112', 'Naolinco', 1),
(2229, 30, '113', 'Naranjal', 1),
(2230, 30, '114', 'Nautla', 1),
(2231, 30, '115', 'Nogales', 1),
(2232, 30, '116', 'Oluta', 1),
(2233, 30, '117', 'Omealca', 1),
(2234, 30, '118', 'Orizaba', 1),
(2235, 30, '119', 'Otatitlán', 1),
(2236, 30, '120', 'Oteapan', 1),
(2237, 30, '121', 'Ozuluama de Mascareñas', 1),
(2238, 30, '122', 'Pajapan', 1),
(2239, 30, '123', 'Pánuco', 1),
(2240, 30, '124', 'Papantla', 1),
(2241, 30, '125', 'Paso del Macho', 1),
(2242, 30, '126', 'Paso de Ovejas', 1),
(2243, 30, '127', 'La Perla', 1),
(2244, 30, '128', 'Perote', 1),
(2245, 30, '129', 'Platón Sánchez', 1),
(2246, 30, '130', 'Playa Vicente', 1),
(2247, 30, '131', 'Poza Rica de Hidalgo', 1),
(2248, 30, '132', 'Las Vigas de Ramírez', 1),
(2249, 30, '133', 'Pueblo Viejo', 1),
(2250, 30, '134', 'Puente Nacional', 1),
(2251, 30, '135', 'Rafael Delgado', 1),
(2252, 30, '136', 'Rafael Lucio', 1),
(2253, 30, '137', 'Los Reyes', 1),
(2254, 30, '138', 'Río Blanco', 1),
(2255, 30, '139', 'Saltabarranca', 1),
(2256, 30, '140', 'San Andrés Tenejapan', 1),
(2257, 30, '141', 'San Andrés Tuxtla', 1),
(2258, 30, '142', 'San Juan Evangelista', 1),
(2259, 30, '143', 'Santiago Tuxtla', 1),
(2260, 30, '144', 'Sayula de Alemán', 1),
(2261, 30, '145', 'Soconusco', 1),
(2262, 30, '146', 'Sochiapa', 1),
(2263, 30, '147', 'Soledad Atzompa', 1),
(2264, 30, '148', 'Soledad de Doblado', 1),
(2265, 30, '149', 'Soteapan', 1),
(2266, 30, '150', 'Tamalín', 1),
(2267, 30, '151', 'Tamiahua', 1),
(2268, 30, '152', 'Tampico Alto', 1),
(2269, 30, '153', 'Tancoco', 1),
(2270, 30, '154', 'Tantima', 1),
(2271, 30, '155', 'Tantoyuca', 1),
(2272, 30, '156', 'Tatatila', 1),
(2273, 30, '157', 'Castillo de Teayo', 1),
(2274, 30, '158', 'Tecolutla', 1),
(2275, 30, '159', 'Tehuipango', 1),
(2276, 30, '160', 'Álamo Temapache', 1),
(2277, 30, '161', 'Tempoal', 1),
(2278, 30, '162', 'Tenampa', 1),
(2279, 30, '163', 'Tenochtitlán', 1),
(2280, 30, '164', 'Teocelo', 1),
(2281, 30, '165', 'Tepatlaxco', 1),
(2282, 30, '166', 'Tepetlán', 1),
(2283, 30, '167', 'Tepetzintla', 1),
(2284, 30, '168', 'Tequila', 1),
(2285, 30, '169', 'José Azueta', 1),
(2286, 30, '170', 'Texcatepec', 1),
(2287, 30, '171', 'Texhuacán', 1),
(2288, 30, '172', 'Texistepec', 1),
(2289, 30, '173', 'Tezonapa', 1),
(2290, 30, '174', 'Tierra Blanca', 1),
(2291, 30, '175', 'Tihuatlán', 1),
(2292, 30, '176', 'Tlacojalpan', 1),
(2293, 30, '177', 'Tlacolulan', 1),
(2294, 30, '178', 'Tlacotalpan', 1),
(2295, 30, '179', 'Tlacotepec de Mejía', 1),
(2296, 30, '180', 'Tlachichilco', 1),
(2297, 30, '181', 'Tlalixcoyan', 1),
(2298, 30, '182', 'Tlalnelhuayocan', 1),
(2299, 30, '183', 'Tlapacoyan', 1),
(2300, 30, '184', 'Tlaquilpa', 1),
(2301, 30, '185', 'Tlilapan', 1),
(2302, 30, '186', 'Tomatlán', 1),
(2303, 30, '187', 'Tonayán', 1),
(2304, 30, '188', 'Totutla', 1),
(2305, 30, '189', 'Tuxpan', 1),
(2306, 30, '190', 'Tuxtilla', 1),
(2307, 30, '191', 'Ursulo Galván', 1),
(2308, 30, '192', 'Vega de Alatorre', 1),
(2309, 30, '193', 'Veracruz', 1),
(2310, 30, '194', 'Villa Aldama', 1),
(2311, 30, '195', 'Xoxocotla', 1),
(2312, 30, '196', 'Yanga', 1),
(2313, 30, '197', 'Yecuatla', 1),
(2314, 30, '198', 'Zacualpan', 1),
(2315, 30, '199', 'Zaragoza', 1),
(2316, 30, '200', 'Zentla', 1),
(2317, 30, '201', 'Zongolica', 1),
(2318, 30, '202', 'Zontecomatlán de López y Fuentes', 1),
(2319, 30, '203', 'Zozocolco de Hidalgo', 1),
(2320, 30, '204', 'Agua Dulce', 1),
(2321, 30, '205', 'El Higo', 1),
(2322, 30, '206', 'Nanchital de Lázaro Cárdenas del Río', 1),
(2323, 30, '207', 'Tres Valles', 1),
(2324, 30, '208', 'Carlos A. Carrillo', 1),
(2325, 30, '209', 'Tatahuicapan de Juárez', 1),
(2326, 30, '210', 'Uxpanapa', 1),
(2327, 30, '211', 'San Rafael', 1),
(2328, 30, '212', 'Santiago Sochiapan', 1),
(2329, 31, '001', 'Abalá', 1),
(2330, 31, '002', 'Acanceh', 1),
(2331, 31, '003', 'Akil', 1),
(2332, 31, '004', 'Baca', 1),
(2333, 31, '005', 'Bokobá', 1),
(2334, 31, '006', 'Buctzotz', 1),
(2335, 31, '007', 'Cacalchén', 1),
(2336, 31, '008', 'Calotmul', 1),
(2337, 31, '009', 'Cansahcab', 1),
(2338, 31, '010', 'Cantamayec', 1),
(2339, 31, '011', 'Celestún', 1),
(2340, 31, '012', 'Cenotillo', 1),
(2341, 31, '013', 'Conkal', 1),
(2342, 31, '014', 'Cuncunul', 1),
(2343, 31, '015', 'Cuzamá', 1),
(2344, 31, '016', 'Chacsinkín', 1),
(2345, 31, '017', 'Chankom', 1),
(2346, 31, '018', 'Chapab', 1),
(2347, 31, '019', 'Chemax', 1),
(2348, 31, '020', 'Chicxulub Pueblo', 1),
(2349, 31, '021', 'Chichimilá', 1),
(2350, 31, '022', 'Chikindzonot', 1),
(2351, 31, '023', 'Chocholá', 1),
(2352, 31, '024', 'Chumayel', 1),
(2353, 31, '025', 'Dzán', 1),
(2354, 31, '026', 'Dzemul', 1),
(2355, 31, '027', 'Dzidzantún', 1),
(2356, 31, '028', 'Dzilam de Bravo', 1),
(2357, 31, '029', 'Dzilam González', 1),
(2358, 31, '030', 'Dzitás', 1),
(2359, 31, '031', 'Dzoncauich', 1),
(2360, 31, '032', 'Espita', 1),
(2361, 31, '033', 'Halachó', 1),
(2362, 31, '034', 'Hocabá', 1),
(2363, 31, '035', 'Hoctún', 1),
(2364, 31, '036', 'Homún', 1),
(2365, 31, '037', 'Huhí', 1),
(2366, 31, '038', 'Hunucmá', 1),
(2367, 31, '039', 'Ixil', 1),
(2368, 31, '040', 'Izamal', 1),
(2369, 31, '041', 'Kanasín', 1),
(2370, 31, '042', 'Kantunil', 1),
(2371, 31, '043', 'Kaua', 1),
(2372, 31, '044', 'Kinchil', 1),
(2373, 31, '045', 'Kopomá', 1),
(2374, 31, '046', 'Mama', 1),
(2375, 31, '047', 'Maní', 1),
(2376, 31, '048', 'Maxcanú', 1),
(2377, 31, '049', 'Mayapán', 1),
(2378, 31, '050', 'Mérida', 1),
(2379, 31, '051', 'Mocochá', 1),
(2380, 31, '052', 'Motul', 1),
(2381, 31, '053', 'Muna', 1),
(2382, 31, '054', 'Muxupip', 1),
(2383, 31, '055', 'Opichén', 1),
(2384, 31, '056', 'Oxkutzcab', 1),
(2385, 31, '057', 'Panabá', 1),
(2386, 31, '058', 'Peto', 1),
(2387, 31, '059', 'Progreso', 1),
(2388, 31, '060', 'Quintana Roo', 1),
(2389, 31, '061', 'Río Lagartos', 1),
(2390, 31, '062', 'Sacalum', 1),
(2391, 31, '063', 'Samahil', 1),
(2392, 31, '064', 'Sanahcat', 1),
(2393, 31, '065', 'San Felipe', 1),
(2394, 31, '066', 'Santa Elena', 1),
(2395, 31, '067', 'Seyé', 1),
(2396, 31, '068', 'Sinanché', 1),
(2397, 31, '069', 'Sotuta', 1),
(2398, 31, '070', 'Sucilá', 1),
(2399, 31, '071', 'Sudzal', 1),
(2400, 31, '072', 'Suma', 1),
(2401, 31, '073', 'Tahdziú', 1),
(2402, 31, '074', 'Tahmek', 1),
(2403, 31, '075', 'Teabo', 1),
(2404, 31, '076', 'Tecoh', 1),
(2405, 31, '077', 'Tekal de Venegas', 1),
(2406, 31, '078', 'Tekantó', 1),
(2407, 31, '079', 'Tekax', 1),
(2408, 31, '080', 'Tekit', 1),
(2409, 31, '081', 'Tekom', 1),
(2410, 31, '082', 'Telchac Pueblo', 1),
(2411, 31, '083', 'Telchac Puerto', 1),
(2412, 31, '084', 'Temax', 1),
(2413, 31, '085', 'Temozón', 1),
(2414, 31, '086', 'Tepakán', 1),
(2415, 31, '087', 'Tetiz', 1),
(2416, 31, '088', 'Teya', 1),
(2417, 31, '089', 'Ticul', 1),
(2418, 31, '090', 'Timucuy', 1),
(2419, 31, '091', 'Tinum', 1),
(2420, 31, '092', 'Tixcacalcupul', 1),
(2421, 31, '093', 'Tixkokob', 1),
(2422, 31, '094', 'Tixmehuac', 1),
(2423, 31, '095', 'Tixpéhual', 1),
(2424, 31, '096', 'Tizimín', 1),
(2425, 31, '097', 'Tunkás', 1),
(2426, 31, '098', 'Tzucacab', 1),
(2427, 31, '099', 'Uayma', 1),
(2428, 31, '100', 'Ucú', 1),
(2429, 31, '101', 'Umán', 1),
(2430, 31, '102', 'Valladolid', 1),
(2431, 31, '103', 'Xocchel', 1),
(2432, 31, '104', 'Yaxcabá', 1),
(2433, 31, '105', 'Yaxkukul', 1),
(2434, 31, '106', 'Yobaín', 1),
(2435, 32, '001', 'Apozol', 1),
(2436, 32, '002', 'Apulco', 1),
(2437, 32, '003', 'Atolinga', 1),
(2438, 32, '004', 'Benito Juárez', 1),
(2439, 32, '005', 'Calera', 1),
(2440, 32, '006', 'Cañitas de Felipe Pescador', 1),
(2441, 32, '007', 'Concepción del Oro', 1),
(2442, 32, '008', 'Cuauhtémoc', 1),
(2443, 32, '009', 'Chalchihuites', 1),
(2444, 32, '010', 'Fresnillo', 1),
(2445, 32, '011', 'Trinidad García de la Cadena', 1),
(2446, 32, '012', 'Genaro Codina', 1),
(2447, 32, '013', 'General Enrique Estrada', 1),
(2448, 32, '014', 'General Francisco R. Murguía', 1),
(2449, 32, '015', 'El Plateado de Joaquín Amaro', 1),
(2450, 32, '016', 'General Pánfilo Natera', 1),
(2451, 32, '017', 'Guadalupe', 1),
(2452, 32, '018', 'Huanusco', 1),
(2453, 32, '019', 'Jalpa', 1),
(2454, 32, '020', 'Jerez', 1),
(2455, 32, '021', 'Jiménez del Teul', 1),
(2456, 32, '022', 'Juan Aldama', 1),
(2457, 32, '023', 'Juchipila', 1),
(2458, 32, '024', 'Loreto', 1),
(2459, 32, '025', 'Luis Moya', 1),
(2460, 32, '026', 'Mazapil', 1),
(2461, 32, '027', 'Melchor Ocampo', 1),
(2462, 32, '028', 'Mezquital del Oro', 1),
(2463, 32, '029', 'Miguel Auza', 1),
(2464, 32, '030', 'Momax', 1),
(2465, 32, '031', 'Monte Escobedo', 1),
(2466, 32, '032', 'Morelos', 1),
(2467, 32, '033', 'Moyahua de Estrada', 1),
(2468, 32, '034', 'Nochistlán de Mejía', 1),
(2469, 32, '035', 'Noria de Ángeles', 1),
(2470, 32, '036', 'Ojocaliente', 1),
(2471, 32, '037', 'Pánuco', 1),
(2472, 32, '038', 'Pinos', 1),
(2473, 32, '039', 'Río Grande', 1),
(2474, 32, '040', 'Sain Alto', 1),
(2475, 32, '041', 'El Salvador', 1),
(2476, 32, '042', 'Sombrerete', 1),
(2477, 32, '043', 'Susticacán', 1),
(2478, 32, '044', 'Tabasco', 1),
(2479, 32, '045', 'Tepechitlán', 1),
(2480, 32, '046', 'Tepetongo', 1),
(2481, 32, '047', 'Teúl de González Ortega', 1),
(2482, 32, '048', 'Tlaltenango de Sánchez Román', 1),
(2483, 32, '049', 'Valparaíso', 1),
(2484, 32, '050', 'Vetagrande', 1),
(2485, 32, '051', 'Villa de Cos', 1),
(2486, 32, '052', 'Villa García', 1),
(2487, 32, '053', 'Villa González Ortega', 1),
(2488, 32, '054', 'Villa Hidalgo', 1),
(2489, 32, '055', 'Villanueva', 1),
(2490, 32, '056', 'Zacatecas', 1),
(2491, 32, '057', 'Trancoso', 1),
(2492, 32, '058', 'Santa María de la Paz', 1);

-- --------------------------------------------------------

--
-- Table structure for table `companys`
--

CREATE TABLE `companys` (
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
  `picture` varchar(255) DEFAULT NULL,
  `perfil` varchar(50) DEFAULT NULL,
  `fund` decimal(20,2) DEFAULT NULL,
  `up_date` date DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `clientKey` varchar(150) NOT NULL,
  `state` varchar(300) NOT NULL,
  `RFC` varchar(200) NOT NULL DEFAULT '',
  `CURP` varchar(200) NOT NULL DEFAULT '',
  `INE` varchar(200) NOT NULL DEFAULT '',
  `Id_Comision` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Key_Company` varchar(200) NOT NULL DEFAULT '',
  `mail_notifications` varchar(300) NOT NULL DEFAULT '',
  `ComisionPaynet` varchar(30) NOT NULL DEFAULT '2.5',
  `IvaComisionPaynet` varchar(30) NOT NULL DEFAULT '16',
  `ConceptPaynet` varchar(100) NOT NULL DEFAULT 'COMISION PAYNET',
  `IdCardsUser` varchar(50) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `companys`
--

INSERT INTO `companys` (`ide`, `company`, `social_reason`, `fullname`, `email`, `telephone`, `address`, `city`, `zip`, `aboutme`, `picture`, `perfil`, `fund`, `up_date`, `active`, `clientKey`, `state`, `RFC`, `CURP`, `INE`, `Id_Comision`, `Key_Company`, `mail_notifications`, `ComisionPaynet`, `IvaComisionPaynet`, `ConceptPaynet`, `IdCardsUser`) VALUES
(1, 'Energex', 'Energex SA de CV', 'ENERGEX GRUPO ENERGETICOS', 'energex@grupoenergetico.mx', '8121234567', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 64000, 'Empresa que administra la plataforma.', 'recargas.png', 'ADMIN', '0.00', '2019-03-19', 1, '', 'NUEVO LEON', '', '', '', '0.00', '', 'salvador@hotpay.mx,jose@hotpay.mx', '2.5', '16', 'COMISION PAYNET', '1'),
(2, 'Demo Sistemas', 'Demo Sistemas  S. A de C.V.', 'Demo Energex', 'energex@demo.mx', '8121345378', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, 'Demo energex.', 'redipagos.png', 'EMPRESA', '1.00', '2019-09-03', 1, '1', 'NUEVO LEÓN', '', '', '', '0.00', '', '', '2.5', '16', 'COMISION PAYNET', '1'),
(3, 'Recargas Grupo Energeticos', 'Recargas Grupo Energeticos S. A de C.V.', 'Recargas Grupo Energeticos S. A de C.V.', 'recargasenergeticos@energex.mx', '8172753715', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, 's', 'recargas.png', 'EMPRESA', '2.00', '2019-10-08', 1, '1', 'NUEVO LEÓN', '', '', '', '0.00', '', '', '2.5', '16', 'COMISION PAYNET', '1'),
(4, 'ALZA', 'Alza Desarrollos', 'Alza Desarrollos', 'alza.mazon@gmail.com', '9868125373', 'Quinta Elisa 32-C', 'HERMOSILLO', 67000, 'a', 'logogocard.png', 'EMPRESA', '0.00', '2019-11-21', 1, '1', 'SONORA', '', '', '', '0.00', '', '', '2.5', '16', 'COMISION PAYNET', '1'),
(5, 'CLUSTER ENERGETICO DLLS', 'CLUSTER ENERGETICO DE NUEVO LEON, A.C.', 'OLIVIA MENDOZA CADENA', 'olivia@clusterenergetico.org', '8115029350', 'CARRETERA A VICTORIA KM 14.5 COLONIA CENTRO LINARES', 'LINARES', 67700, 'Empresa en dlls.', 'CLUSTER.png', 'EMPRESA', '0.00', '2020-05-13', 1, '1', 'NUEVO LEÓN', '', '', '', '0.00', '', '', '2.5', '16', 'COMISION PAYNET', '1'),
(6, 'demo1', 'demo1 sa de cv', 'demo1', 'demo1@energex.mx', '9711092328', 'sin datos por el momento', 'MONTERREY', 67000, 'de ', 'recargas.png', 'EMPRESA', '0.00', '2020-08-12', 1, '1', 'NUEVO LEÓN', '', '', '', '10.00', '', '', '2.5', '16', 'COMISION PAYNET', '1'),
(7, 'Núñez 2', 'Núñez 2', 'Núñez 2', 'cnu@grupoenergetico.mx', '7875665443', 'S/D', 'MONTERREY', 64000, 'Demo', 'logogocard.png', 'EMPRESA', '0.00', '2021-03-11', 1, '1', 'NUEVO LEÓN', '', '', '', '2.50', 'WJYE4AXX65ASX', 'jose@hotpay.mx', '2.5', '16', 'COMISION PAYNET', '1');

-- --------------------------------------------------------

--
-- Table structure for table `connect_API`
--

CREATE TABLE `connect_API` (
  `Id` int(11) NOT NULL,
  `Key_Platform` varchar(100) NOT NULL DEFAULT '',
  `Tipo` int(11) NOT NULL,
  `Description` varchar(1000) NOT NULL DEFAULT '',
  `Creacion` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `connect_API`
--

INSERT INTO `connect_API` (`Id`, `Key_Platform`, `Tipo`, `Description`, `Creacion`) VALUES
(1, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890', 1, 'Plataforma', 'CREADO DESDE PLATAFORMA'),
(2, 'TT2WZ7AJ3RTGOK1KISW55', 2, 'Llave BIONE', 'CREADO DESDE BIONE');

-- --------------------------------------------------------

--
-- Table structure for table `data_mails`
--

CREATE TABLE `data_mails` (
  `Id` int(11) NOT NULL,
  `Ide_incident` varchar(10) NOT NULL,
  `Mensage` varchar(500) NOT NULL,
  `Date` datetime NOT NULL,
  `Respuesta` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `data_mails`
--

INSERT INTO `data_mails` (`Id`, `Ide_incident`, `Mensage`, `Date`, `Respuesta`) VALUES
(1, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                        <tr><td> Saldo Cuenta Maestra </td><td>$ 400.72</td></tr><tr><td> Saldo Empresa </td><td>$ 2.00</td></tr><tr><td> Monto del reverso </td><td>$ -2.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 2.00</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2019-12-05 16:05:45', '202'),
(2, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 402.72</td></tr><tr><td> Saldo Empresa </td><td>$US 4.00</td></tr><tr><td> Monto del fondeo </td><td>$US 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 0.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-05 16:06:13', '202'),
(3, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                                <tr><td> Saldo Cuenta Maestra </td><td>$US 401.72</td></tr><tr><td> Saldo Empresa </td><td>$US 3.00</td></tr><tr><td> Monto del fondeo </td><td>$US 0.30</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 1.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-05 16:06:47', '202'),
(4, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                                <tr><td> Saldo Cuenta Maestra </td><td>$US 401.42</td></tr><tr><td> Saldo Empresa </td><td>$US 2.70</td></tr><tr><td> Monto del fondeo </td><td>$US 0.20</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 1.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-05 16:06:52', '202'),
(5, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                                <tr><td> Saldo Cuenta Maestra </td><td>$US 401.22</td></tr><tr><td> Saldo Empresa </td><td>$US 2.50</td></tr><tr><td> Monto del fondeo </td><td>$US 0.50</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 1.50</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-05 16:07:02', '202'),
(6, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\r\n                        <tr><td> Saldo Cuenta Maestra </td><td>$ 398.72</td></tr><tr><td> Saldo Administrador </td><td>$ 394.72</td></tr><tr><td> Monto del fondeo </td><td>$ 1.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 2.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-10 16:00:00', '1'),
(7, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\r\n                        <tr><td> Saldo Cuenta Maestra </td><td>$ 398.72</td></tr><tr><td> Saldo Administrador </td><td>$ 394.72</td></tr><tr><td> Monto del fondeo </td><td>$ 1.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 2.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-10 16:00:01', '202'),
(8, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\r\n                        <tr><td> Saldo Cuenta Maestra </td><td>$ 398.72</td></tr><tr><td> Saldo Administrador </td><td>$ 393.72</td></tr><tr><td> Monto del fondeo </td><td>$ -1.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 3.00</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2019-12-10 16:00:51', '202'),
(9, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 398.72</td></tr><tr><td> Saldo Empresa </td><td>$US 4.00</td></tr><tr><td> Monto del fondeo </td><td>$US 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 4.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-10 16:02:28', '202'),
(10, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                        <tr><td> Saldo Cuenta Maestra </td><td>$ 397.72</td></tr><tr><td> Saldo Empresa </td><td>$ 3.00</td></tr><tr><td> Monto del reverso </td><td>$ -1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 5.00</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2019-12-10 16:03:18', '202'),
(11, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                                <tr><td> Saldo Cuenta Maestra </td><td>$US 398.72</td></tr><tr><td> Saldo Empresa </td><td>$US 4.00</td></tr><tr><td> Monto del fondeo </td><td>$US 0.30</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 4.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-10 16:05:03', '202'),
(12, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                        <tr><td> Saldo Cuenta Maestra </td><td>$ 398.42</td></tr><tr><td> Saldo Empresa </td><td>$ 3.70</td></tr><tr><td> Monto del reverso </td><td>$ -3.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 4.30</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2019-12-10 16:26:47', '202'),
(13, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 401.42</td></tr><tr><td> Saldo Empresa </td><td>$US 6.70</td></tr><tr><td> Monto del fondeo </td><td>$US 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 1.30</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-16 12:15:17', '202'),
(14, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                                <tr><td> Saldo Cuenta Maestra </td><td>$US 400.42</td></tr><tr><td> Saldo Empresa </td><td>$US 5.70</td></tr><tr><td> Monto del fondeo </td><td>$US 2.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 2.30</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-16 12:18:43', '202'),
(15, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\r\n                        <tr><td> Saldo Cuenta Maestra </td><td>$ 398.42</td></tr><tr><td> Saldo Administrador </td><td>$ 392.72</td></tr><tr><td> Monto del fondeo </td><td>$ 150.00</td></tr><tr><td> Empresa fondeada</td><td>RECARGAS GRUPO ENERGETICOS</td></tr><tr><td> Saldo Empresa </td><td>$ 2.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-22 17:27:16', '202'),
(16, '1', '<br> <b><h3>Empresa RECARGAS GRUPO ENERGETICOS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 398.42</td></tr><tr><td> Saldo Empresa </td><td>$US 152.00</td></tr><tr><td> Monto del fondeo </td><td>$US 150.00</td></tr><tr><td> Tarjeta </td><td> ****-****-1188-7002</td></tr><tr><td> Saldo Tarjeta </td><td>$US 9.50</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-22 17:29:04', '202'),
(17, '1', '<br> <b><h3>Empresa RECARGAS GRUPO ENERGETICOS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 248.42</td></tr><tr><td> Saldo Empresa </td><td>$US 2.00</td></tr><tr><td> Monto del fondeo </td><td>$US 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-1155-4008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 3.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2019-12-23 12:11:32', '202'),
(18, '1', '<br> <b><h3>Empresa RECARGAS GRUPO ENERGETICOS</b></h3>-----------------------<br><table>\r\n                                        <tr><td> Saldo Cuenta Maestra </td><td>$ 247.42</td></tr><tr><td> Saldo Empresa </td><td>$ 1.00</td></tr><tr><td> Monto del reverso </td><td>$ -1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-1155-4008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 4.00</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2019-12-23 12:12:11', '202'),
(19, '1', '<br> <b><h3>Empresa RECARGAS GRUPO ENERGETICOS</b></h3>-----------------------<br><table>\r\n                                        <tr><td> Saldo Cuenta Maestra </td><td>$ 248.42</td></tr><tr><td> Saldo Empresa </td><td>$ 2.00</td></tr><tr><td> Monto del reverso </td><td>$ -10.00</td></tr><tr><td> Tarjeta </td><td> ****-****-1188-7002</td></tr><tr><td> Saldo Tarjeta </td><td>$ 10.08</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-01-08 11:15:53', '202'),
(20, '1', '<br> <b><h3>Empresa RECARGAS GRUPO ENERGETICOS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 258.42</td></tr><tr><td> Saldo Empresa </td><td>$US 12.00</td></tr><tr><td> Monto del fondeo </td><td>$US 5.00</td></tr><tr><td> Tarjeta </td><td> ****-****-1188-7002</td></tr><tr><td> Saldo Tarjeta </td><td>$US 0.08</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-01-08 11:16:54', '202'),
(21, '1', '<br> <b><h3>Empresa RECARGAS GRUPO ENERGETICOS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 253.42</td></tr><tr><td> Saldo Empresa </td><td>$US 7.00</td></tr><tr><td> Monto del fondeo </td><td>$US 5.00</td></tr><tr><td> Tarjeta </td><td> ****-****-1188-7002</td></tr><tr><td> Saldo Tarjeta </td><td>$US 5.08</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-01-08 11:35:29', '202'),
(22, '1', '<br> <b><h3>Empresa RECARGAS GRUPO ENERGETICOS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 198.42</td></tr><tr><td> Saldo Empresa </td><td>$US 2.00</td></tr><tr><td> Monto del fondeo </td><td>$US 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-1188-7002</td></tr><tr><td> Saldo Tarjeta </td><td>$US 1.72</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-01-23 15:07:03', '202'),
(23, '1', '<br> <b><h3>Empresa RECARGAS GRUPO ENERGETICOS</b></h3>-----------------------<br><table>\r\n                                        <tr><td> Saldo Cuenta Maestra </td><td>$ 197.42</td></tr><tr><td> Saldo Empresa </td><td>$ 1.00</td></tr><tr><td> Monto del reverso </td><td>$ -1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-1188-7002</td></tr><tr><td> Saldo Tarjeta </td><td>$ 2.72</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-01-23 15:07:45', '202'),
(24, '1', '<br> <b><h3>Empresa RECARGAS GRUPO ENERGETICOS</b></h3>-----------------------<br><table>\r\n                                        <tr><td> Saldo Cuenta Maestra </td><td>$ 137.42</td></tr><tr><td> Saldo Empresa </td><td>$ 2.00</td></tr><tr><td> Monto del reverso </td><td>$ -65.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0941-6004</td></tr><tr><td> Saldo Tarjeta </td><td>$ 65.33</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-03-11 14:40:26', '202'),
(25, '1', '<br> <b><h3>Empresa RECARGAS GRUPO ENERGETICOS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 202.42</td></tr><tr><td> Saldo Empresa </td><td>$US 67.00</td></tr><tr><td> Monto del fondeo </td><td>$US 65.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0331-0005</td></tr><tr><td> Saldo Tarjeta </td><td>$US 0.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-03-17 09:47:51', '202'),
(26, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                        <tr><td> Saldo Cuenta Maestra </td><td>$ 138.42</td></tr><tr><td> Saldo Empresa </td><td>$ 3.70</td></tr><tr><td> Monto del reverso </td><td>$ -2.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0952-9004</td></tr><tr><td> Saldo Tarjeta </td><td>$ 2.00</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-05-13 15:47:27', '202'),
(27, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Saldo Cuenta Maestra </td><td>$ 140.42</td></tr><tr><td> Saldo Administrador </td><td>$ 132.72</td></tr><tr><td> Monto del fondeo </td><td>$ 10.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 5.70</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-08-12 17:46:16', '202'),
(28, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 140.42</td></tr><tr><td> Saldo Empresa </td><td>$US 15.70</td></tr><tr><td> Monto del fondeo </td><td>$US 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 5.30</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-08-12 18:12:13', '202'),
(29, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                        <tr><td> Saldo Cuenta Maestra </td><td>$ 139.42</td></tr><tr><td> Saldo Empresa </td><td>$ 14.70</td></tr><tr><td> Monto del reverso </td><td>$ -1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 6.30</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-08-12 18:12:30', '202'),
(30, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 138.42</td></tr><tr><td> Saldo Empresa </td><td>$US 15.70</td></tr><tr><td> Monto del fondeo </td><td>$US 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 5.30</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-08-21 11:40:54', '202'),
(31, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                        <tr><td> Saldo Cuenta Maestra </td><td>$ 137.42</td></tr><tr><td> Saldo Empresa </td><td>$ 14.70</td></tr><tr><td> Monto del reverso </td><td>$ -1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 6.30</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-08-21 11:41:27', '202'),
(32, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra </td><td>$ 136.12</td></tr><tr><td> Saldo Empresa </td><td>$ 15.70</td></tr><tr><td> Monto del fondeo </td><td>$ 2.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 7.60</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-08-24 13:03:17', '202'),
(33, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra </td><td>$ 134.12</td></tr><tr><td> Saldo Empresa </td><td>$ 13.70</td></tr><tr><td> Monto del fondeo </td><td>$ -2.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 9.60</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-08-24 13:03:50', '202'),
(34, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra </td><td>$ 136.12</td></tr><tr><td> Saldo Empresa </td><td>$ 15.70</td></tr><tr><td> Monto del fondeo </td><td>$ 3.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 7.60</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-08-24 13:08:03', '202'),
(35, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra </td><td>$ 133.12</td></tr><tr><td> Saldo Empresa </td><td>$ 12.70</td></tr><tr><td> Monto del fondeo </td><td>$ -3.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 10.60</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-08-24 13:08:44', '202'),
(36, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 136.12</td></tr><tr><td> Saldo Empresa </td><td>$US 15.70</td></tr><tr><td> Monto del fondeo </td><td>$US 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 7.60</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-08-24 16:23:28', '202'),
(37, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                        <tr><td> Saldo Cuenta Maestra </td><td>$ 135.12</td></tr><tr><td> Saldo Empresa </td><td>$ 14.70</td></tr><tr><td> Monto del reverso </td><td>$ -1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 8.60</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-08-24 16:23:46', '202'),
(38, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\r\n                                <tr><td> Saldo Cuenta Maestra </td><td>$US 136.12</td></tr><tr><td> Saldo Empresa </td><td>$US 15.70</td></tr><tr><td> Monto del fondeo </td><td>$US 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$US 7.60</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-08-25 13:22:54', '202'),
(39, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra </td><td>$ 135.12</td></tr><tr><td> Saldo Empresa </td><td>$ 14.70</td></tr><tr><td> Monto del fondeo </td><td>$ 2.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 8.60</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-08-26 16:33:53', '202'),
(40, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra </td><td>$ 133.12</td></tr><tr><td> Saldo Empresa </td><td>$ 12.70</td></tr><tr><td> Monto del fondeo </td><td>$ -2.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 10.60</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-08-26 16:34:39', '202'),
(41, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra </td><td>$ 135.12</td></tr><tr><td> Saldo Empresa </td><td>$ 14.70</td></tr><tr><td> Monto del fondeo </td><td>$ 3.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 8.60</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-08-26 16:37:31', '202'),
(42, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra </td><td>$ 132.12</td></tr><tr><td> Saldo Empresa </td><td>$ 11.70</td></tr><tr><td> Monto del fondeo </td><td>$ -4.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 11.60</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-08-26 16:39:44', '202'),
(43, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Saldo Cuenta Maestra </td><td>$ 132.12</td></tr><tr><td> Saldo Administrador </td><td>$ 114.42</td></tr><tr><td> Monto del fondeo </td><td>$ 2.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 15.70</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-08-26 16:40:36', '202'),
(44, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Saldo Cuenta Maestra </td><td>$ 132.12</td></tr><tr><td> Saldo Administrador </td><td>$ 112.42</td></tr><tr><td> Monto del fondeo </td><td>$ -2.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 17.70</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-08-26 16:41:00', '202'),
(45, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Saldo Cuenta Maestra </td><td>$ 204,205.96</td></tr><tr><td> Saldo Administrador </td><td>$ 204,188.26</td></tr><tr><td> Monto del fondeo </td><td>$ 9.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 15.70</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-10-30 17:40:46', '202'),
(46, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Saldo Cuenta Maestra </td><td>$ 204,205.96</td></tr><tr><td> Saldo Administrador </td><td>$ 204,179.26</td></tr><tr><td> Monto del fondeo </td><td>$ -10.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 24.70</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-10-30 17:43:32', '202'),
(47, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Cuenta </td><td> MASTERCARD</td></tr><tr><td> Saldo Cuenta Maestra antes del fondeo </td><td>$ 204,195.96</td></tr><tr><td> Monto del fondeo </td><td>$ 10.00</td></tr><tr><td> Saldo Final </td><td>$ 204,205.96</td></tr></table>', '2020-10-30 17:48:47', '202'),
(48, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 193,205.96</td></tr><tr><td> Saldo Empresa </td><td>$ 14.70</td></tr><tr><td> Monto del fondeo </td><td>$ 2.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 9.60</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2020-10-30 17:54:35', '202'),
(49, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 193,205.96</td></tr><tr><td> Saldo Empresa </td><td>$ 12.70</td></tr><tr><td> Monto del fondeo </td><td>$ 0.10</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 11.60</td></tr><tr><td> Accion </td><td> FONDEO A TARJETA</td></tr></table>', '2020-10-30 17:56:34', '202'),
(50, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 193,205.96</td></tr><tr><td> Saldo Empresa </td><td>$ 12.60</td></tr><tr><td> Monto del fondeo </td><td>$ -2.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 11.70</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-10-30 17:57:28', '202'),
(51, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Cuenta </td><td> MASTERCARD</td></tr><tr><td> Saldo Cuenta Maestra antes del fondeo </td><td>$ -59,865.98</td></tr><tr><td> Monto del fondeo </td><td>$ 60,000.00</td></tr><tr><td> Saldo Final </td><td>$ 134.02</td></tr></table>', '2020-11-09 11:20:51', '202'),
(52, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 135.02</td></tr><tr><td> Saldo Empresa </td><td>$ 0.00</td></tr><tr><td> Monto del fondeo </td><td>$ -1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 8.70</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2020-11-24 13:14:42', '202'),
(53, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                  <tr><td> Saldo Administrador antes del fondeo </td><td>$ 133.02</td></tr><tr><td> Monto del fondeo </td><td>$ -1.00</td></tr><tr><td> Saldo Final Administrador</td><td>$ 132.02</td></tr></table>', '2020-11-24 13:51:19', '202'),
(54, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                  <tr><td> Saldo Administrador antes del fondeo </td><td>$ 132.02</td></tr><tr><td> Monto del fondeo </td><td>$ 1.00</td></tr><tr><td> Saldo Final Administrador</td><td>$ 133.02</td></tr></table>', '2020-11-24 13:51:37', '202'),
(55, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                  <tr><td> Saldo Administrador antes del fondeo </td><td>$ 133.02</td></tr><tr><td> Monto del fondeo </td><td>$ -1.00</td></tr><tr><td> Saldo Final Administrador</td><td>$ 132.02</td></tr></table>', '2020-11-24 14:03:58', '202'),
(56, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                  <tr><td> Saldo Administrador antes del fondeo </td><td>$ 132.02</td></tr><tr><td> Monto del fondeo </td><td>$ 1.00</td></tr><tr><td> Saldo Final Administrador</td><td>$ 133.02</td></tr></table>', '2020-11-24 14:04:04', '202'),
(57, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                  <tr><td> Saldo Administrador antes del fondeo </td><td>$ 133.02</td></tr><tr><td> Monto del fondeo </td><td>$ -1.00</td></tr><tr><td> Saldo Final Administrador</td><td>$ 132.02</td></tr></table>', '2020-11-24 14:05:26', '202'),
(58, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                  <tr><td> Saldo Administrador antes del fondeo </td><td>$ 132.02</td></tr><tr><td> Monto del fondeo </td><td>$ 1.00</td></tr><tr><td> Saldo Final Administrador</td><td>$ 133.02</td></tr></table>', '2020-11-24 14:05:33', '202'),
(59, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 136.02</td></tr><tr><td> Saldo Empresa </td><td>$ 1.00</td></tr><tr><td> Monto del fondeo </td><td>$ 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 7.70</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2021-03-11 13:41:53', '202'),
(60, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 135.02</td></tr><tr><td> Saldo Empresa </td><td>$ 0.00</td></tr><tr><td> Monto del fondeo </td><td>$ -1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 8.70</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2021-03-11 13:43:22', '202'),
(61, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Saldo Cuenta Maestra </td><td>$ 136.02</td></tr><tr><td> Saldo Administrador </td><td>$ 133.02</td></tr><tr><td> Monto del fondeo </td><td>$ 2.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 1.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2021-03-11 15:46:21', '202'),
(62, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Saldo Cuenta Maestra </td><td>$ 136.02</td></tr><tr><td> Saldo Administrador </td><td>$ 131.02</td></tr><tr><td> Monto del fondeo </td><td>$ -2.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 3.00</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2021-03-11 15:46:34', '202'),
(63, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 136.02</td></tr><tr><td> Saldo Empresa </td><td>$ 1.00</td></tr><tr><td> Monto del fondeo </td><td>$ 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-1015-2002</td></tr><tr><td> Saldo Tarjeta </td><td>$ 2.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2021-03-11 16:21:17', '202'),
(64, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 135.02</td></tr><tr><td> Saldo Empresa </td><td>$ 0.00</td></tr><tr><td> Monto del fondeo </td><td>$ -1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 7.70</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2021-03-11 16:26:06', '202'),
(65, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 136.02</td></tr><tr><td> Saldo Empresa </td><td>$ 1.00</td></tr><tr><td> Monto del fondeo </td><td>$ 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 6.70</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2021-03-11 16:27:43', '202'),
(66, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 135.02</td></tr><tr><td> Saldo Empresa </td><td>$ 0.00</td></tr><tr><td> Monto del fondeo </td><td>$ -1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-1015-2002</td></tr><tr><td> Saldo Tarjeta </td><td>$ 3.00</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2021-03-11 16:28:01', '202'),
(67, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 136.02</td></tr><tr><td> Saldo Empresa </td><td>$ 1.00</td></tr><tr><td> Monto del fondeo </td><td>$ 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 7.70</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2021-03-11 17:42:37', '202'),
(68, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 135.02</td></tr><tr><td> Saldo Empresa </td><td>$ 0.00</td></tr><tr><td> Monto del fondeo </td><td>$ -1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Saldo Tarjeta </td><td>$ 8.70</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2021-03-11 17:43:42', '202'),
(69, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Saldo Cuenta Maestra </td><td>$ 136.02</td></tr><tr><td> Saldo Administrador </td><td>$ 133.02</td></tr><tr><td> Monto del fondeo </td><td>$ 2.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 1.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2021-03-11 17:53:54', '202'),
(70, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Saldo Cuenta Maestra </td><td>$ 136.02</td></tr><tr><td> Saldo Administrador </td><td>$ 131.02</td></tr><tr><td> Monto del fondeo </td><td>$ -2.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 3.00</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2021-03-11 17:54:05', '202'),
(71, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 136.02</td></tr><tr><td> Saldo Empresa </td><td>$ 1.00</td></tr><tr><td> Monto del fondeo </td><td>$ 1.00</td></tr><tr><td> Tarjeta </td><td> ****-****-1015-2002</td></tr><tr><td> Saldo Tarjeta </td><td>$ 2.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2021-03-17 13:24:01', '202'),
(72, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                                <tr><td> Saldo Cuenta Maestra MASTERCARD </td><td>$ 135.02</td></tr><tr><td> Saldo Empresa </td><td>$ 0.00</td></tr><tr><td> Monto del fondeo </td><td>$ -2.00</td></tr><tr><td> Tarjeta </td><td> ****-****-1015-2002</td></tr><tr><td> Saldo Tarjeta </td><td>$ 3.00</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2021-03-17 13:24:20', '202'),
(73, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                    <tr><td> Accion </td><td> FONDEAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ 2.00</td></tr><tr><td> Descripcion </td><td> FONDEO A TARJETA</td></tr></table>', '2021-05-03 17:24:36', '202'),
(74, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                    <tr><td> Accion </td><td> REVERSAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ -3.00</td></tr><tr><td> Descripcion </td><td> REVERSO A TARJETA</td></tr></table>', '2021-05-03 18:08:02', '202'),
(75, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Monto del fondeo </td><td>$ 2.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 3.00</td></tr><tr><td> Accion </td><td> FONDEAR</td></tr></table>', '2021-05-10 17:34:23', '202'),
(76, '1', '<br> <b><h3>Empresa ENERGEX</b></h3>-----------------------<br><table>\n                    <tr><td> Monto del fondeo </td><td>$ -2.00</td></tr><tr><td> Empresa fondeada</td><td>DEMO SISTEMAS</td></tr><tr><td> Saldo Empresa </td><td>$ 5.00</td></tr><tr><td> Accion </td><td> REVERSAR</td></tr></table>', '2021-05-10 17:34:42', '202'),
(77, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                     <tr><td> Accion </td><td> FONDEAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ 2.00</td></tr><tr><td> Descripcion </td><td> INTENTO DE FONDEO A TARJETA NO COMPLETADO</td></tr></table>', '2021-05-10 17:41:31', '202'),
(78, '2', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                     <tr><td> Accion </td><td> FONDEAR</td></tr><tr><td> Tarjeta </td><td> ****-****-1015-2002</td></tr><tr><td> Monto </td><td>$ 2.00</td></tr><tr><td> Descripcion </td><td> INTENTO DE FONDEO A TARJETA NO COMPLETADO</td></tr></table>', '2021-05-10 17:43:55', '202'),
(79, '3', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                     <tr><td> Accion </td><td> FONDEAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ 1.00</td></tr><tr><td> Descripcion </td><td> INTENTO DE FONDEO A TARJETA NO COMPLETADO</td></tr></table>', '2021-05-10 17:44:40', '202'),
(80, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                    <tr><td> Accion </td><td> REVERSAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ -2.00</td></tr><tr><td> Descripcion </td><td> REVERSO A TARJETA</td></tr></table>', '2021-05-10 17:45:04', '202'),
(81, '4', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                     <tr><td> Accion </td><td> FONDEAR</td></tr><tr><td> Tarjeta </td><td> ****-****-1015-2002</td></tr><tr><td> Monto </td><td>$ 1.00</td></tr><tr><td> Descripcion </td><td> INTENTO DE FONDEO A TARJETA NO COMPLETADO</td></tr></table>', '2021-05-10 17:48:13', '202'),
(82, '5', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                     <tr><td> Accion </td><td> FONDEAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ 2.00</td></tr><tr><td> Descripcion </td><td> INTENTO DE FONDEO A TARJETA NO COMPLETADO</td></tr></table>', '2021-05-10 17:48:33', '202'),
(83, '6', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                     <tr><td> Accion </td><td> FONDEAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ 2.00</td></tr><tr><td> Descripcion </td><td> INTENTO DE FONDEO A TARJETA NO COMPLETADO</td></tr></table>', '2021-05-10 17:54:03', '202'),
(84, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                    <tr><td> Accion </td><td> FONDEAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ 2.00</td></tr><tr><td> Descripcion </td><td> FONDEO A TARJETA</td></tr></table>', '2021-05-10 17:57:47', '202'),
(85, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                    <tr><td> Accion </td><td> FONDEAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ 3.00</td></tr><tr><td> Descripcion </td><td> FONDEO A TARJETA</td></tr></table>', '2021-05-10 18:03:08', '202'),
(86, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                    <tr><td> Accion </td><td> REVERSAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ -3.00</td></tr><tr><td> Descripcion </td><td> REVERSO A TARJETA</td></tr></table>', '2021-05-10 18:03:31', '202'),
(87, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                    <tr><td> Accion </td><td> REVERSAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ -2.00</td></tr><tr><td> Descripcion </td><td> REVERSO A TARJETA</td></tr></table>', '2021-05-10 18:05:05', '202'),
(88, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                    <tr><td> Accion </td><td> FONDEAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ 4.00</td></tr><tr><td> Descripcion </td><td> FONDEO A TARJETA</td></tr></table>', '2021-05-10 18:05:28', '202'),
(89, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                    <tr><td> Accion </td><td> REVERSAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ -1.00</td></tr><tr><td> Descripcion </td><td> REVERSO A TARJETA</td></tr></table>', '2021-11-04 13:54:12', '202'),
(90, '1', '<br> <b><h3>Empresa DEMO SISTEMAS</b></h3>-----------------------<br><table>\n                    <tr><td> Accion </td><td> FONDEAR</td></tr><tr><td> Tarjeta </td><td> ****-****-0954-6008</td></tr><tr><td> Monto </td><td>$ 1.00</td></tr><tr><td> Descripcion </td><td> FONDEO A TARJETA</td></tr></table>', '2021-11-04 13:56:29', '202');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabla de Estados de la República Mexicana';

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

-- --------------------------------------------------------

--
-- Table structure for table `estados_payment`
--

CREATE TABLE `estados_payment` (
  `id` int(11) NOT NULL,
  `clave` varchar(2) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `renapo` varchar(16) NOT NULL,
  `abrev` varchar(16) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabla de Estados de la República Mexicana';

--
-- Dumping data for table `estados_payment`
--

INSERT INTO `estados_payment` (`id`, `clave`, `nombre`, `renapo`, `abrev`, `activo`) VALUES
(1, '01', 'AGUASCALIENTES', 'AG', 'Ags.', 1),
(2, '02', 'BAJA CALIFORNIA', 'BC', 'BC.', 1),
(3, '03', 'BAJA CALIFORNIA SUR', 'BS', 'BCS', 1),
(4, '04', 'CAMPECHE', 'CM', 'Camp.', 1),
(5, '05', 'COAHUILA DE ZARAGOZA', 'CO', 'Coah.', 1),
(6, '06', 'COLIMA', 'CL', 'Col.', 1),
(7, '07', 'CHIAPAS', 'CS', 'Chis.', 1),
(8, '08', 'CHIHUAHUA', 'CH', 'Chih.', 1),
(9, '09', 'DISTRITO FEDERAL', 'DF', 'DF', 1),
(10, '10', 'DURANGO', 'DG', 'Dgo.', 1),
(11, '11', 'GUANAJUATO', 'GT', 'Gto.', 1),
(12, '12', 'GUERRERO', 'GR', 'Gro.', 1),
(13, '13', 'HIDALGO', 'HG', 'Hgo.', 1),
(14, '14', 'JALISCO', 'JC', 'Jal.', 1),
(15, '15', 'MÉXICO', 'MC', 'Mex.', 1),
(16, '16', 'MICHOACÁN DE OCAMPO', 'MN', 'Mich.', 1),
(17, '17', 'MORELOS', 'MS', 'Mor.', 1),
(18, '18', 'NAYARIT', 'NT', 'Nay.', 1),
(19, '19', 'NUEVO LEÓN', 'NL', 'NL.', 1),
(20, '20', 'OAXACA', 'OC', 'Oax.', 1),
(21, '21', 'PUEBLA', 'PL', 'Pue.', 1),
(22, '22', 'QUERÉTARO', 'QT', 'Qro.', 1),
(23, '23', 'QUINTANA ROO', 'QR', 'Q. Roo.', 1),
(24, '24', 'SAN LUIS POTOSÍ', 'SP', 'SLP', 1),
(25, '25', 'SINALOA', 'SI', 'Sin.', 1),
(26, '26', 'SONORA', 'SR', 'Son.', 1),
(27, '27', 'TABASCO', 'TB', 'Tab.', 1),
(28, '28', 'TAMAULIPAS', 'TM', 'Tamps.', 1),
(29, '29', 'TLAXCALA', 'TL', 'Tlax.', 1),
(30, '30', 'VERACRUZ DE IGNACIO DE LA LLAVE', 'VE', 'Ver.', 1),
(31, '31', 'YUCATÁN', 'YU', 'Yuc.', 1),
(32, '32', 'ZACATECAS', 'ZA', 'Zac.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `facturas_cards`
--

CREATE TABLE `facturas_cards` (
  `Id` bigint(20) NOT NULL,
  `Card` varchar(20) NOT NULL,
  `Auth_Code` varchar(500) NOT NULL,
  `Url_factura` text NOT NULL,
  `Fecha_alta` datetime NOT NULL,
  `User` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `facturas_cards`
--

INSERT INTO `facturas_cards` (`Id`, `Card`, `Auth_Code`, `Url_factura`, `Fecha_alta`, `User`) VALUES
(1, '09546008', '15177771', 'facturas/15177771.pdf', '2020-08-31 13:13:27', '2'),
(2, '09546008', '15849681', 'facturas/15849681.pdf', '2020-10-30 18:18:03', '2');

-- --------------------------------------------------------

--
-- Table structure for table `founds_MA`
--

CREATE TABLE `founds_MA` (
  `Id` bigint(20) NOT NULL,
  `Concept` varchar(500) NOT NULL DEFAULT '',
  `Amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Date_upload` datetime NOT NULL,
  `Date_found` datetime NOT NULL,
  `User` varchar(200) NOT NULL,
  `url_file` varchar(500) NOT NULL DEFAULT '',
  `Comision` varchar(300) NOT NULL DEFAULT '',
  `Saldo_MA` varchar(30) NOT NULL DEFAULT '',
  `BalanceAdmin` varchar(30) NOT NULL DEFAULT '',
  `Cuenta` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `founds_MA`
--

INSERT INTO `founds_MA` (`Id`, `Concept`, `Amount`, `Date_upload`, `Date_found`, `User`, `url_file`, `Comision`, `Saldo_MA`, `BalanceAdmin`, `Cuenta`) VALUES
(1, 'FONDEO A CUENTA MAESTRA', '501.30', '2019-05-31 13:49:00', '2019-05-31 13:49:00', '1', '', '2.5', '', '', ''),
(2, 'FONDEO A CUENTA MAESTRA', '493.07', '2019-10-29 13:36:50', '2019-10-29 13:36:50', '1', '', '2.5', '', '', ''),
(3, 'FONDEO A CUENTA MAESTRA', '10.00', '2020-08-31 13:20:14', '2020-08-31 13:20:14', '7', '', '2.5', '', '', ''),
(4, 'a', '10.00', '2020-10-30 17:48:44', '2020-10-30 17:48:44', '7', '', '2.5', '00204205.96', '204179.26', 'MASTERCARD'),
(6, 'REVERSAR A ADMINISTRADOR', '-1.00', '2020-11-24 13:51:18', '2020-11-24 13:51:18', '7', '', '0', '0', '133.02', ''),
(7, 'FONDEAR A ADMINISTRADOR', '1.00', '2020-11-24 13:51:36', '2020-11-24 13:51:36', '7', '', '0', '0', '132.02', ''),
(8, 'REVERSAR A ADMINISTRADOR', '-1.00', '2020-11-24 14:03:52', '2020-11-24 14:03:52', '7', '', '0', '0', '133.02', ''),
(9, 'FONDEAR A ADMINISTRADOR', '1.00', '2020-11-24 14:04:03', '2020-11-24 14:04:03', '7', '', '0', '0', '132.02', ''),
(10, 'REVERSAR A ADMINISTRADOR', '-1.00', '2020-11-24 14:05:24', '2020-11-24 14:05:24', '7', '', '0', '0', '133.02', ''),
(11, 'FONDEAR A ADMINISTRADOR', '1.00', '2020-11-24 14:05:32', '2020-11-24 14:05:32', '7', '', '0', '0', '132.02', '');

-- --------------------------------------------------------

--
-- Table structure for table `funds`
--

CREATE TABLE `funds` (
  `ide` bigint(20) NOT NULL,
  `company` int(11) DEFAULT NULL,
  `BalanceCompany` varchar(30) NOT NULL DEFAULT '',
  `BalanceAdmin` varchar(30) NOT NULL DEFAULT '',
  `fund` decimal(20,2) DEFAULT NULL,
  `up_date` varchar(20) DEFAULT NULL,
  `description` varchar(150) NOT NULL,
  `active` int(11) DEFAULT NULL,
  `idcard` varchar(20) NOT NULL,
  `User` varchar(200) NOT NULL,
  `Comment` varchar(300) NOT NULL,
  `Transfer_company` varchar(200) NOT NULL DEFAULT '',
  `Monto_comision` varchar(200) NOT NULL DEFAULT '',
  `Iva_comision` varchar(200) NOT NULL DEFAULT '',
  `Comision` varchar(200) NOT NULL DEFAULT '',
  `newBalanceCompany` varchar(30) NOT NULL DEFAULT '',
  `newBalanceAdmin` varchar(30) NOT NULL DEFAULT '',
  `Founded_by` varchar(30) NOT NULL DEFAULT '1' COMMENT '1=platform,2=API'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `funds`
--

INSERT INTO `funds` (`ide`, `company`, `BalanceCompany`, `BalanceAdmin`, `fund`, `up_date`, `description`, `active`, `idcard`, `User`, `Comment`, `Transfer_company`, `Monto_comision`, `Iva_comision`, `Comision`, `newBalanceCompany`, `newBalanceAdmin`, `Founded_by`) VALUES
(1, 2, '', '', '1.00', '2019-09-20', 'FONDEO EMPRESA', 1, '', '', '', '', '', '', '', '', '', '1'),
(2, 2, '', '', '1.00', '2019-09-20', 'REVERSOEMPRESA', 1, '', '', '', '', '', '', '', '', '', '1'),
(5, 3, '', '', '5.00', '2019-09-26', 'COMPANY FUNDING', 1, '', '', '', '', '', '', '', '', '', '1'),
(6, 3, '', '', '1.00', '2019-09-27 10:17:20', 'CARD FUNDING', 1, '554008', '', '', '', '', '', '', '', '', '1'),
(7, 3, '', '', '-1.00', '2019-09-27 10:18:23', 'CARD REVERSE', 1, '554008', '', '', '', '', '', '', '', '', '1'),
(8, 2, '', '', '1.00', '2019-10-08 10:03:15', 'COMPANY FUNDING', 1, '', '', '', '', '', '', '', '', '', '1'),
(9, 2, '', '', '1.00', '2019-10-08 10:03:28', 'COMPANY REVERSING', 1, '', '', '', '', '', '', '', '', '', '1'),
(10, 2, '', '', '2.00', '2019-10-08 10:40:28', 'COMPANY FUNDING', 1, '', '', '', '', '', '', '', '', '', '1'),
(11, 2, '', '', '1.00', '2019-10-08 10:40:51', 'CARD FUNDING', 1, '10152002', '', '', '', '', '', '', '', '', '1'),
(12, 2, '', '', '1.00', '2019-10-08 10:41:02', 'CARD FUNDING', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(13, 2, '', '', '-1.00', '2019-10-08 10:41:21', 'CARD REVERSE', 1, '10152002', '', '', '', '', '', '', '', '', '1'),
(14, 2, '', '', '-1.00', '2019-10-08 10:42:10', 'CARD REVERSE', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(15, 2, '', '', '1.00', '2019-10-08 10:43:10', 'CARD FUNDING', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(16, 2, '', '', '-1.00', '2019-10-08 10:46:23', 'CARD REVERSE', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(17, 3, '', '', '3.00', '2019-10-09 12:34:26', 'CARD FUNDING', 1, '11554008', '', '', '', '', '', '', '', '', '1'),
(18, 3, '', '', '1.00', '2019-10-09 12:52:34', 'CARD FUNDING', 1, '11554008', '', '', '', '', '', '', '', '', '1'),
(19, 3, '', '', '-1.00', '2019-10-09 12:53:59', 'CARD REVERSE', 1, '11554008', '', '', '', '', '', '', '', '', '1'),
(20, 3, '', '', '1.00', '2019-11-13 10:25:20', 'COMPANY FUNDING', 1, '', '', '', '', '', '', '', '', '', '1'),
(21, 3, '', '', '1.00', '2019-11-13 10:25:47', 'COMPANY REVERSING', 1, '', '', '', '', '', '', '', '', '', '1'),
(22, 2, '', '', '1.00', '2019-11-14 10:15:37', 'COMPANY FUNDING', 1, '', '', '', '', '', '', '', '', '', '1'),
(23, 2, '', '', '1.00', '2019-11-14 10:16:01', 'COMPANY REVERSING', 1, '', '', '', '', '', '', '', '', '', '1'),
(24, 3, '', '', '1.00', '2019-11-21 16:00:33', 'CARD FUNDING', 1, '11554008', '', '', '', '', '', '', '', '', '1'),
(25, 3, '', '', '-1.00', '2019-11-21 16:01:22', 'CARD REVERSE', 1, '11554008', '', '', '', '', '', '', '', '', '1'),
(26, 3, '', '', '1.00', '2019-11-25 18:49:44', 'CARD FUNDING', 1, '11554008', '', '', '', '', '', '', '', '', '1'),
(27, 3, '', '', '-1.00', '2019-11-25 18:50:07', 'CARD REVERSE', 1, '11554008', '', '', '', '', '', '', '', '', '1'),
(28, 3, '', '', '150.00', '2019-11-27 17:07:51', 'COMPANY FUNDING', 1, '', '', '', '', '', '', '', '', '', '1'),
(29, 3, '', '', '150.00', '2019-11-27 17:51:30', 'CARD FUNDING', 1, '11887002', '', '', '', '', '', '', '', '', '1'),
(30, 2, '', '', '1.00', '2019-12-04 12:20:41', 'COMPANY FUNDING', 1, '', '', '', '', '', '', '', '', '', '1'),
(31, 2, '', '', '1.00', '2019-12-04 12:20:52', 'COMPANY REVERSING', 1, '', '', '', '', '', '', '', '', '', '1'),
(32, 2, '', '', '1.00', '2019-12-04 12:42:25', 'CARD FUNDING', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(33, 2, '', '', '-1.00', '2019-12-04 12:42:52', 'CARD REVERSE', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(34, 2, '', '', '1.00', '2019-12-04 12:51:22', 'CARD FUNDING', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(35, 2, '', '', '-1.00', '2019-12-04 12:51:52', 'CARD REVERSE', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(36, 2, '', '', '1.00', '2019-12-05 11:48:38', 'CARD FUNDING', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(37, 2, '', '', '0.65', '2019-12-05 11:52:09', 'CARD FUNDING', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(38, 2, '', '', '-2.00', '2019-12-05 11:52:52', 'CARD REVERSE', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(39, 2, '', '', '0.35', '2019-12-05 11:53:25', 'CARD FUNDING', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(40, 2, '', '', '-2.00', '2019-12-05 16:05:37', 'CARD REVERSE', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(41, 2, '', '', '1.00', '2019-12-05 16:06:10', 'CARD FUNDING', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(42, 2, '', '', '0.30', '2019-12-05 16:06:45', 'CARD FUNDING', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(43, 2, '', '', '0.20', '2019-12-05 16:06:51', 'CARD FUNDING', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(44, 2, '', '', '0.50', '2019-12-05 16:06:59', 'CARD FUNDING', 1, '09546008', '', '', '', '', '', '', '', '', '1'),
(45, 2, '', '', '1.00', '2019-12-10 15:59:58', 'COMPANY FUNDING', 1, '', '1', '', '', '', '', '', '', '', '1'),
(46, 2, '', '', '-1.00', '2019-12-10 16:00:50', 'COMPANY REVERSING', 1, '', '1', '', '', '', '', '', '', '', '1'),
(47, 2, '', '', '1.00', '2019-12-10 16:02:19', 'CARD FUNDING', 1, '09546008', '2', 'Demo sistemas', '', '', '', '', '', '', '1'),
(48, 2, '', '', '-1.00', '2019-12-10 16:03:09', 'CARD REVERSE', 1, '09546008', '2', 'Reverso sistemas', '', '', '', '', '', '', '1'),
(49, 2, '', '', '0.30', '2019-12-10 16:05:01', 'CARD FUNDING', 1, '09546008', '2', 'Funding by layout', '', '', '', '', '', '', '1'),
(50, 2, '', '', '-3.00', '2019-12-10 16:26:40', 'CARD REVERSE', 1, '09546008', '2', 'Reverso sistemas', '', '', '', '', '', '', '1'),
(51, 2, '', '', '1.00', '2019-12-16 12:15:15', 'CARD FUNDING', 1, '09546008', '2', 'Demo sistemas', '', '', '', '', '', '', '1'),
(52, 2, '', '', '2.00', '2019-12-16 12:18:41', 'CARD FUNDING', 1, '09546008', '2', 'Funding by layout', '', '', '', '', '', '', '1'),
(53, 3, '', '', '150.00', '2019-12-22 17:27:15', 'COMPANY FUNDING', 1, '', '1', '', '', '', '', '', '', '', '1'),
(54, 3, '', '', '150.00', '2019-12-22 17:29:01', 'FONDEO A TARJETA', 1, '11887002', '3', '|123', '', '', '', '', '', '', '1'),
(55, 3, '', '', '1.00', '2019-12-23 12:11:12', 'FONDEO A TARJETA', 1, '11554008', '3', 'dt', '', '', '', '', '', '', '1'),
(56, 3, '', '', '-1.00', '2019-12-23 12:12:09', 'CARD REVERSE', 1, '11554008', '3', 'cxfhf', '', '', '', '', '', '', '1'),
(57, 3, '', '', '-10.00', '2020-01-08 11:15:50', 'CARD REVERSE', 1, '11887002', '3', '1', '', '', '', '', '', '', '1'),
(58, 3, '', '', '5.00', '2020-01-08 11:16:53', 'FONDEO A TARJETA', 1, '11887002', '3', '1', '', '', '', '', '', '', '1'),
(59, 3, '', '', '5.00', '2020-01-08 11:35:09', 'CARD FUNDING', 1, '11887002', '3', '12', '', '', '', '', '', '', '1'),
(60, 3, '', '', '1.00', '2020-01-23 15:07:01', 'FONDEO A TARJETA', 1, '11887002', '3', 'saltillo', '', '', '', '', '', '', '1'),
(61, 3, '', '', '-1.00', '2020-01-23 15:07:42', 'CARD REVERSE', 1, '11887002', '3', 'saltillas', '', '', '', '', '', '', '1'),
(62, 3, '', '', '-65.00', '2020-03-11 14:40:01', 'CARD REVERSE', 1, '09416004', '3', 'a', '', '', '', '', '', '', '1'),
(63, 3, '', '', '65.00', '2020-03-17 09:47:35', 'FONDEO A TARJETA', 1, '03310005', '3', 'FONDEO A TARJETA NUEVA', '', '', '', '', '', '', '1'),
(64, 2, '', '', '-2.00', '2020-05-13 15:47:23', 'CARD REVERSE', 1, '09529004', '2', 'a', '', '', '', '', '', '', '1'),
(65, 2, '', '', '10.00', '2020-08-12 17:46:16', 'COMPANY FUNDING', 1, '', '1', '', '10', '0', '0', '0.00', '', '', '1'),
(66, 2, '', '', '1.00', '2020-08-12 18:11:56', 'CARD FUNDING', 1, '09546008', '2', 'a', '', '', '', '', '', '', '1'),
(67, 2, '', '', '-1.00', '2020-08-12 18:12:28', 'CARD REVERSE', 1, '09546008', '2', 'a', '', '', '', '', '', '', '1'),
(68, 2, '', '', '1.00', '2020-08-21 11:40:52', 'CARD FUNDING', 1, '09546008', '2', 'a', '', '', '', '', '', '', '1'),
(69, 2, '', '', '-1.00', '2020-08-21 11:41:10', 'CARD REVERSE', 1, '09546008', '2', 'a', '', '', '', '', '', '', '1'),
(70, 2, '', '', '2.00', '2020-08-24 13:03:15', 'CARD FUNDING', 1, '09546008', '2', 'f', '', '', '', '', '', '', '1'),
(71, 2, '', '', '-2.00', '2020-08-24 13:03:47', 'CARD REVERSE', 1, '09546008', '2', 'r', '', '', '', '', '', '', '1'),
(72, 2, '', '', '3.00', '2020-08-24 13:08:02', 'CARD FUNDING', 1, '09546008', '2', 'Funding by layout', '', '', '', '', '', '', '1'),
(73, 2, '', '', '-3.00', '2020-08-24 13:08:33', 'CARD REVERSE', 1, '09546008', '2', 'r', '', '', '', '', '', '', '1'),
(74, 2, '', '', '1.00', '2020-08-24 16:23:05', 'CARD FUNDING', 1, '09546008', '2', 'a', '', '', '', '', '', '', '1'),
(75, 2, '', '', '-1.00', '2020-08-24 16:23:42', 'CARD REVERSE', 1, '09546008', '2', 'a', '', '', '', '', '', '', '1'),
(76, 2, '', '', '1.00', '2020-08-25 13:22:52', 'CARD FUNDING', 1, '09546008', '2', 'a', '', '', '', '', '', '', '1'),
(77, 2, '', '', '2.00', '2020-08-26 16:33:45', 'CARD FUNDING', 1, '09546008', '2', 'f', '', '', '', '', '', '', '1'),
(78, 2, '', '', '-2.00', '2020-08-26 16:34:14', 'REVERSO A TARJETA', 1, '09546008', '2', 'r', '', '', '', '', '', '', '1'),
(79, 2, '', '', '3.00', '2020-08-26 16:37:26', 'FONDEO A TARJETA', 1, '09546008', '2', 'Funding by layout', '', '', '', '', '', '', '1'),
(80, 2, '', '', '-4.00', '2020-08-26 16:39:22', 'REVERSO A TARJETA', 1, '09546008', '2', 'r', '', '', '', '', '', '', '1'),
(81, 2, '', '', '2.00', '2020-08-26 16:40:36', 'FONDEO EMPRESA', 1, '', '1', '', '2', '0', '0', '1', '', '', '1'),
(82, 2, '', '', '-2.00', '2020-08-26 16:41:00', 'REVERSO EMPRESA', 1, '', '1', '', '', '', '', '', '', '', '1'),
(83, 2, '15.70', '204188.26', '9.00', '2020-10-30 17:40:44', 'FONDEO EMPRESA', 1, '', '7', '', '10', '1', '0', '10', '24.7', '204179.26', '1'),
(84, 2, '24.70', '204179.26', '-10.00', '2020-10-30 17:43:31', 'REVERSO EMPRESA', 1, '', '7', '', '', '', '', '', '14.7', '204189.26', '1'),
(85, 2, '14.70', '', '2.00', '2020-10-30 17:54:32', 'FONDEO A TARJETA', 1, '09546008', '2', 'fondeo', '', '', '', '', '12.7', '', '1'),
(86, 2, '12.70', '', '0.10', '2020-10-30 17:56:32', 'FONDEO A TARJETA', 1, '09546008', '2', 'FONDEO POR LAYOUT', '', '', '', '', '12.6', '', '1'),
(87, 2, '12.60', '', '-2.00', '2020-10-30 17:57:25', 'REVERSO A TARJETA', 1, '09546008', '2', 'Reverso', '', '', '', '', '14.6', '', '1'),
(88, 2, '0.00', '', '-1.00', '2020-11-24 13:14:39', 'REVERSO A TARJETA', 1, '09546008', '2', 'a', '', '', '', '', '1', '', '1'),
(89, 2, '1.00', '', '1.00', '2021-03-11 13:41:51', 'FONDEO A TARJETA', 1, '09546008', '2', 'Fund', '', '', '', '', '0', '', '1'),
(90, 2, '0.00', '', '-1.00', '2021-03-11 13:43:17', 'REVERSO A TARJETA', 1, '09546008', '2', 'Reverse', '', '', '', '', '1', '', '1'),
(91, 2, '1.00', '133.02', '2.00', '2021-03-11 15:46:19', 'FONDEO EMPRESA', 1, '', '1', '', '2', '0', '0', '1', '3', '131.02', '1'),
(92, 2, '3.00', '131.02', '-2.00', '2021-03-11 15:46:33', 'REVERSO EMPRESA', 1, '', '1', '', '', '', '', '', '1', '133.02', '1'),
(93, 2, '1.00', '', '1.00', '2021-03-11 16:21:15', 'FONDEO A TARJETA', 1, '10152002', '2', 'Fund', '', '', '', '', '0', '', '1'),
(94, 2, '0.00', '', '-1.00', '2021-03-11 16:26:02', 'REVERSO A TARJETA', 1, '09546008', '2', 'Reverse', '', '', '', '', '1', '', '1'),
(95, 2, '1.00', '', '1.00', '2021-03-11 16:27:40', 'FONDEO A TARJETA', 1, '09546008', '2', 'Fund', '', '', '', '', '0', '', '1'),
(96, 2, '0.00', '', '-1.00', '2021-03-11 16:27:58', 'REVERSO A TARJETA', 1, '10152002', '2', 'Reverse', '', '', '', '', '1', '', '1'),
(97, 2, '1.00', '', '1.00', '2021-03-11 17:42:34', 'FONDEO A TARJETA', 1, '09546008', '2', 'Fund', '', '', '', '', '0', '', '1'),
(98, 2, '0.00', '', '-1.00', '2021-03-11 17:43:39', 'REVERSO A TARJETA', 1, '09546008', '2', 'Reverse', '', '', '', '', '1', '', '1'),
(99, 2, '1.00', '133.02', '2.00', '2021-03-11 17:53:53', 'FONDEO EMPRESA', 1, '', '1', '', '2', '0', '0', '0', '3', '131.02', '1'),
(100, 2, '3.00', '131.02', '-2.00', '2021-03-11 17:54:03', 'REVERSO EMPRESA', 1, '', '1', '', '', '', '', '', '1', '133.02', '1'),
(101, 2, '1.00', '', '1.00', '2021-03-17 13:23:59', 'FONDEO A TARJETA', 1, '10152002', '2', 'a', '', '', '', '', '0', '', '1'),
(102, 2, '0.00', '', '-2.00', '2021-03-17 13:24:16', 'REVERSO A TARJETA', 1, '10152002', '2', 'a', '', '', '', '', '2', '', '1'),
(103, 2, '2.00', '', '2.00', '2021-05-03 17:24:33', 'FONDEO A TARJETA', 1, '09546008', '2', 'Fund', '', '', '', '', '0', '', '1'),
(104, 2, '0.00', '', '-3.00', '2021-05-03 18:07:59', 'REVERSO A TARJETA', 1, '09546008', '2', 'Reverse', '', '', '', '', '3', '', '1'),
(105, 2, '3', '133.02000427246094', '2.00', '2021-05-10 17:34:22', 'FONDEO EMPRESA', 1, '', '7', '', '2.00', '0.00', '0.00', '0.00', '5', '131.02000427246094', '1'),
(106, 2, '5', '131.02000427246094', '-2.00', '2021-05-10 17:34:41', 'REVERSO EMPRESA', 1, '', '7', '', '', '', '', '', '3', '133.02000427246094', '1'),
(107, 2, '3', '', '-2.00', '2021-05-10 17:45:01', 'REVERSO A TARJETA', 1, '09546008', '2', 'Reverse', '', '', '', '', '5', '', '1'),
(108, 2, '5', '', '2.00', '2021-05-10 17:57:44', 'FONDEO A TARJETA', 1, '09546008', '2', 'Fund', '', '', '', '', '3', '', '1'),
(109, 2, '3', '', '3.00', '2021-05-10 18:03:05', 'FONDEO A TARJETA', 1, '09546008', '2', 'Fund', '', '', '', '', '0', '', '1'),
(110, 2, '0', '', '-3.00', '2021-05-10 18:03:28', 'REVERSO A TARJETA', 1, '09546008', '2', 'Reverse', '', '', '', '', '3', '', '1'),
(111, 2, '3', '', '-2.00', '2021-05-10 18:05:01', 'REVERSO A TARJETA', 1, '09546008', '2', 'Reverse', '', '', '', '', '5', '', '1'),
(112, 2, '5', '', '4.00', '2021-05-10 18:05:26', 'FONDEO A TARJETA', 1, '09546008', '2', 'Fund', '', '', '', '', '1', '', '1'),
(113, 2, '1', '', '-1.00', '2021-11-04 13:54:08', 'REVERSO A TARJETA', 1, '09546008', '2', 'A', '', '', '', '', '2', '', '1'),
(114, 2, '2', '', '1.00', '2021-11-04 13:56:25', 'FONDEO A TARJETA', 1, '09546008', '2', 'a', '', '', '', '', '1', '', '1');

-- --------------------------------------------------------

--
-- Table structure for table `Funds_Temp`
--

CREATE TABLE `Funds_Temp` (
  `Id` int(11) NOT NULL,
  `Card` varchar(16) NOT NULL DEFAULT '',
  `Amount` varchar(20) NOT NULL DEFAULT '',
  `Concept` varchar(200) NOT NULL DEFAULT '',
  `AuthCode` varchar(100) NOT NULL DEFAULT '',
  `Company` varchar(100) NOT NULL DEFAULT '',
  `IdType` varchar(100) NOT NULL DEFAULT '',
  `Status` varchar(100) NOT NULL DEFAULT '' COMMENT '1=pendiente,2=ejectado,3=duplicado',
  `DateInsert` varchar(200) NOT NULL DEFAULT '',
  `DateUpdate` varchar(200) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Funds_Temp`
--

INSERT INTO `Funds_Temp` (`Id`, `Card`, `Amount`, `Concept`, `AuthCode`, `Company`, `IdType`, `Status`, `DateInsert`, `DateUpdate`) VALUES
(1, '09546008', '1', 'FONDEOATARJETA', '074167', '2', '1', '3', '2021-11-04 13:56:28', '2021-11-04 14:00:03');

-- --------------------------------------------------------

--
-- Table structure for table `Fund_manuals`
--

CREATE TABLE `Fund_manuals` (
  `Id` int(11) NOT NULL,
  `Card` varchar(10) NOT NULL DEFAULT '',
  `Monto` varchar(30) NOT NULL DEFAULT '',
  `Accion` varchar(30) NOT NULL DEFAULT '',
  `Motivo` varchar(1000) NOT NULL DEFAULT '',
  `Fecha` datetime NOT NULL,
  `Status` varchar(2) NOT NULL COMMENT '1=pagado,2=denegado',
  `Company` varchar(3) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Fund_manuals`
--

INSERT INTO `Fund_manuals` (`Id`, `Card`, `Monto`, `Accion`, `Motivo`, `Fecha`, `Status`, `Company`) VALUES
(1, '29682007', '95', 'FONDEO A TARJETA', 'a', '2020-11-20 10:47:31', '1', '25'),
(2, '45341000', '50', 'FONDEO A TARJETA', 'a', '2021-01-29 18:08:59', '2', '7');

-- --------------------------------------------------------

--
-- Table structure for table `IntentsPayments`
--

CREATE TABLE `IntentsPayments` (
  `Id` int(11) NOT NULL,
  `Card` varchar(30) NOT NULL,
  `IdMov` varchar(30) NOT NULL,
  `Amount` varchar(30) NOT NULL,
  `DatePayment` datetime NOT NULL,
  `Status` varchar(30) NOT NULL,
  `msg` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Keys_API`
--

CREATE TABLE `Keys_API` (
  `Id` bigint(20) NOT NULL,
  `Key_API` varchar(500) NOT NULL DEFAULT '',
  `Company` varchar(200) NOT NULL DEFAULT '',
  `Name_Company` varchar(200) NOT NULL DEFAULT '',
  `date_upload` datetime NOT NULL,
  `Description` varchar(1000) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Keys_API`
--

INSERT INTO `Keys_API` (`Id`, `Key_API`, `Company`, `Name_Company`, `date_upload`, `Description`) VALUES
(1, 'ASXWZ7AJ3SRFDSK1KISW67', '1', 'ENERGEX DOLARES', '2020-10-28 12:53:58', 'LLAVE ENERGEX DOLARES');

-- --------------------------------------------------------

--
-- Table structure for table `layout_anchor`
--

CREATE TABLE `layout_anchor` (
  `ide` bigint(20) NOT NULL,
  `idecompany` varchar(100) DEFAULT NULL,
  `BalanceCompany` varchar(30) NOT NULL DEFAULT '',
  `idcard` varchar(20) DEFAULT NULL,
  `fund` decimal(20,2) DEFAULT NULL,
  `idtype` varchar(50) DEFAULT NULL,
  `up_date` varchar(20) DEFAULT NULL,
  `User` varchar(100) NOT NULL,
  `Comment` varchar(300) NOT NULL,
  `newBalanceCompany` varchar(30) NOT NULL DEFAULT '',
  `Founded_by` varchar(30) NOT NULL DEFAULT '1' COMMENT '1=platform,2=API'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `layout_anchor`
--

INSERT INTO `layout_anchor` (`ide`, `idecompany`, `BalanceCompany`, `idcard`, `fund`, `idtype`, `up_date`, `User`, `Comment`, `newBalanceCompany`, `Founded_by`) VALUES
(1, '3', '', '11554008', '1.00', 'FUND', '2019-09-27 10:17:18', '', '', '', '1'),
(2, '3', '', '11554008', '-1.00', 'REVERSE', '2019-09-27 10:18:14', '', '', '', '1'),
(3, '2', '', '10152002', '1.00', 'FUND', '2019-10-08 10:40:51', '', '', '', '1'),
(4, '2', '', '9546008', '1.00', 'FUND', '2019-10-08 10:41:02', '', '', '', '1'),
(5, '2', '', '10152002', '-1.00', 'REVERSE', '2019-10-08 10:41:21', '', '', '', '1'),
(6, '2', '', '9546008', '-1.00', 'REVERSE', '2019-10-08 10:42:10', '', '', '', '1'),
(7, '2', '', '9546008', '1.00', 'FUND', '2019-10-08 10:43:10', '', '', '', '1'),
(8, '2', '', '9546008', '-1.00', 'REVERSE', '2019-10-08 10:46:23', '', '', '', '1'),
(9, '3', '', '11554008', '3.00', 'FUND', '2019-10-09 12:34:26', '', '', '', '1'),
(10, '3', '', '11554008', '1.00', 'FUND', '2019-10-09 12:52:34', '', '', '', '1'),
(11, '3', '', '11554008', '-1.00', 'REVERSE', '2019-10-09 12:53:59', '', '', '', '1'),
(12, '3', '', '11554008', '1.00', 'FUND', '2019-11-21 16:00:33', '', '', '', '1'),
(13, '3', '', '11554008', '-1.00', 'REVERSE', '2019-11-21 16:01:22', '', '', '', '1'),
(14, '3', '', '11554008', '1.00', 'FUND', '2019-11-25 18:49:44', '', '', '', '1'),
(15, '3', '', '11554008', '-1.00', 'REVERSE', '2019-11-25 18:50:07', '', '', '', '1'),
(16, '3', '', '11887002', '150.00', 'FUND', '2019-11-27 17:51:30', '', '', '', '1'),
(17, '2', '', '09546008', '1.00', 'FUND', '2019-12-04 12:42:25', '', '', '', '1'),
(18, '2', '', '09546008', '-1.00', 'REVERSE', '2019-12-04 12:42:52', '', '', '', '1'),
(19, '2', '', '09546008', '1.00', 'FUND', '2019-12-04 12:51:22', '', '', '', '1'),
(20, '2', '', '09546008', '-1.00', 'REVERSE', '2019-12-04 12:51:52', '', '', '', '1'),
(21, '2', '', '09546008', '1.00', 'FUND', '2019-12-05 11:48:38', '', '', '', '1'),
(22, '2', '', '09546008', '0.65', 'FUND', '2019-12-05 11:52:09', '', '', '', '1'),
(23, '2', '', '09546008', '-2.00', 'REVERSE', '2019-12-05 11:52:52', '', '', '', '1'),
(24, '2', '', '09546008', '0.35', 'FUND', '2019-12-05 11:53:25', '', '', '', '1'),
(25, '2', '', '09546008', '-2.00', 'REVERSE', '2019-12-05 16:05:37', '', '', '', '1'),
(26, '2', '', '09546008', '1.00', 'FUND', '2019-12-05 16:06:10', '', '', '', '1'),
(27, '2', '', '09546008', '0.30', 'FUND', '2019-12-05 16:06:45', '', '', '', '1'),
(28, '2', '', '09546008', '0.20', 'FUND', '2019-12-05 16:06:51', '', '', '', '1'),
(29, '2', '', '09546008', '0.50', 'FUND', '2019-12-05 16:06:59', '', '', '', '1'),
(30, '2', '', '09546008', '1.00', 'FUND', '2019-12-10 16:02:19', '2', 'Demo sistemas', '', '1'),
(31, '2', '', '09546008', '-1.00', 'REVERSE', '2019-12-10 16:03:09', '2', 'Reverso sistemas', '', '1'),
(32, '2', '', '09546008', '0.30', 'FUND', '2019-12-10 16:05:01', '2', 'Funding by layout', '', '1'),
(33, '2', '', '09546008', '-3.00', 'REVERSE', '2019-12-10 16:26:40', '2', 'Reverso sistemas', '', '1'),
(34, '2', '', '09546008', '1.00', 'FUND', '2019-12-16 12:15:15', '2', 'Demo sistemas', '', '1'),
(35, '2', '', '09546008', '2.00', 'FUND', '2019-12-16 12:18:41', '2', 'Funding by layout', '', '1'),
(36, '3', '', '11887002', '150.00', 'FUND', '2019-12-22 17:29:01', '3', '|123', '', '1'),
(37, '3', '', '11554008', '1.00', 'FUND', '2019-12-23 12:11:12', '3', 'dt', '', '1'),
(38, '3', '', '11554008', '-1.00', 'REVERSE', '2019-12-23 12:12:09', '3', 'cxfhf', '', '1'),
(39, '3', '', '11887002', '-10.00', 'REVERSE', '2020-01-08 11:15:50', '3', '1', '', '1'),
(40, '3', '', '11887002', '5.00', 'FUND', '2020-01-08 11:16:53', '3', '1', '', '1'),
(41, '3', '', '11887002', '5.00', 'FUND', '2020-01-08 11:35:09', '3', '12', '', '1'),
(42, '3', '', '11887002', '1.00', 'FUND', '2020-01-23 15:07:01', '3', 'saltillo', '', '1'),
(43, '3', '', '11887002', '-1.00', 'REVERSE', '2020-01-23 15:07:42', '3', 'saltillas', '', '1'),
(44, '3', '', '09416004', '-65.00', 'REVERSE', '2020-03-11 14:40:01', '3', 'a', '', '1'),
(45, '3', '', '03310005', '65.00', 'FUND', '2020-03-17 09:47:35', '3', 'FONDEO A TARJETA NUEVA', '', '1'),
(46, '2', '', '09529004', '-2.00', 'REVERSE', '2020-05-13 15:47:23', '2', 'a', '', '1'),
(47, '2', '', '09546008', '1.00', 'FUND', '2020-08-12 18:11:56', '2', 'a', '', '1'),
(48, '2', '', '09546008', '-1.00', 'REVERSE', '2020-08-12 18:12:28', '2', 'a', '', '1'),
(49, '2', '', '09546008', '1.00', 'FUND', '2020-08-21 11:40:52', '2', 'a', '', '1'),
(50, '2', '', '09546008', '-1.00', 'REVERSE', '2020-08-21 11:41:10', '2', 'a', '', '1'),
(51, '2', '', '09546008', '2.00', 'FUND', '2020-08-24 13:03:15', '2', 'f', '', '1'),
(52, '2', '', '09546008', '-2.00', 'REVERSE', '2020-08-24 13:03:47', '2', 'r', '', '1'),
(53, '2', '', '09546008', '3.00', 'FUND', '2020-08-24 13:08:02', '2', 'Funding by layout', '', '1'),
(54, '2', '', '09546008', '-3.00', 'REVERSE', '2020-08-24 13:08:33', '2', 'r', '', '1'),
(55, '2', '', '09546008', '1.00', 'FUND', '2020-08-24 16:23:05', '2', 'a', '', '1'),
(56, '2', '', '09546008', '-1.00', 'REVERSE', '2020-08-24 16:23:42', '2', 'a', '', '1'),
(57, '2', '', '09546008', '1.00', 'FUND', '2020-08-25 13:22:52', '2', 'a', '', '1'),
(58, '2', '', '09546008', '2.00', 'FUND', '2020-08-26 16:33:45', '2', 'f', '', '1'),
(59, '2', '', '09546008', '-2.00', 'REVERSE', '2020-08-26 16:34:14', '2', 'r', '', '1'),
(60, '2', '', '09546008', '3.00', 'FUND', '2020-08-26 16:37:26', '2', 'Funding by layout', '', '1'),
(61, '2', '', '09546008', '-4.00', 'REVERSE', '2020-08-26 16:39:22', '2', 'r', '', '1'),
(62, '2', '14.70', '09546008', '2.00', 'fund', '2020-10-30 17:54:32', '2', 'fondeo', '12.7', '1'),
(63, '2', '12.70', '09546008', '0.10', 'fund', '2020-10-30 17:56:32', '2', 'FONDEO POR LAYOUT', '12.6', '1'),
(64, '2', '12.60', '09546008', '-2.00', 'reverse', '2020-10-30 17:57:25', '2', 'Reverso', '14.6', '1'),
(65, '2', '0.00', '09546008', '-1.00', 'reverse', '2020-11-24 13:14:39', '2', 'a', '1', '1'),
(66, '2', '1.00', '09546008', '1.00', 'fund', '2021-03-11 13:41:51', '2', 'Fund', '0', '1'),
(67, '2', '0.00', '09546008', '-1.00', 'reverse', '2021-03-11 13:43:17', '2', 'Reverse', '1', '1'),
(68, '2', '1.00', '10152002', '1.00', 'fund', '2021-03-11 16:21:15', '2', 'Fund', '0', '1'),
(69, '2', '0.00', '09546008', '-1.00', 'reverse', '2021-03-11 16:26:02', '2', 'Reverse', '1', '1'),
(70, '2', '1.00', '09546008', '1.00', 'fund', '2021-03-11 16:27:40', '2', 'Fund', '0', '1'),
(71, '2', '0.00', '10152002', '-1.00', 'reverse', '2021-03-11 16:27:58', '2', 'Reverse', '1', '1'),
(72, '2', '1.00', '09546008', '1.00', 'fund', '2021-03-11 17:42:34', '2', 'Fund', '0', '1'),
(73, '2', '0.00', '09546008', '-1.00', 'reverse', '2021-03-11 17:43:39', '2', 'Reverse', '1', '1'),
(74, '2', '1.00', '10152002', '1.00', 'fund', '2021-03-17 13:23:59', '2', 'a', '0', '1'),
(75, '2', '0.00', '10152002', '-2.00', 'reverse', '2021-03-17 13:24:16', '2', 'a', '2', '1'),
(76, '2', '2.00', '09546008', '2.00', 'fund', '2021-05-03 17:24:33', '2', 'Fund', '0', '1'),
(77, '2', '0.00', '09546008', '-3.00', 'reverse', '2021-05-03 18:07:59', '2', 'Reverse', '3', '1'),
(78, '2', '3', '09546008', '-2.00', 'reverse', '2021-05-10 17:45:01', '2', 'Reverse', '5', '1'),
(79, '2', '5', '09546008', '2.00', 'fund', '2021-05-10 17:57:44', '2', 'Fund', '3', '1'),
(80, '2', '3', '09546008', '3.00', 'fund1', '2021-05-10 18:03:05', '2', 'Fund', '0', '1'),
(81, '2', '0', '09546008', '-3.00', 'reverse1', '2021-05-10 18:03:28', '2', 'Reverse', '3', '1'),
(82, '2', '3', '09546008', '-2.00', 'reverse1', '2021-05-10 18:05:01', '2', 'Reverse', '5', '1'),
(83, '2', '5', '09546008', '4.00', 'fund1', '2021-05-10 18:05:26', '2', 'Fund', '1', '1'),
(84, '2', '1', '09546008', '-1.00', 'reverse', '2021-11-04 13:54:08', '2', 'A', '2', '1'),
(85, '2', '2', '09546008', '1.00', 'fund', '2021-11-04 13:56:25', '2', 'a', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `layout_uploads`
--

CREATE TABLE `layout_uploads` (
  `id` bigint(20) NOT NULL,
  `ide` varchar(20) NOT NULL,
  `file_upload` varchar(400) NOT NULL,
  `file_download` varchar(400) NOT NULL,
  `date_mov` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `layout_uploads`
--

INSERT INTO `layout_uploads` (`id`, `ide`, `file_upload`, `file_download`, `date_mov`) VALUES
(1, '2', './uploads/C051219113747.csv', 'uploads/RC051219113747.csv', '2019-12-05 11:37:52'),
(2, '2', './uploads/C051219114003.csv', 'uploads/RC051219114003.csv', '2019-12-05 11:40:06'),
(3, '2', './uploads/C051219114406.csv', 'uploads/RC051219114406.csv', '2019-12-05 11:44:09'),
(4, '2', './uploads/C051219114512.csv', 'uploads/RC051219114512.csv', '2019-12-05 11:45:16'),
(5, '2', './uploads/C051219114807.csv', 'uploads/RC051219114807.csv', '2019-12-05 11:48:10'),
(6, '2', './uploads/C051219115110.csv', 'uploads/RC051219115110.csv', '2019-12-05 11:51:10'),
(7, '2', './uploads/C051219115209.csv', 'uploads/RC051219115209.csv', '2019-12-05 11:52:10'),
(8, '2', './uploads/C051219115325.csv', 'uploads/RC051219115325.csv', '2019-12-05 11:53:29'),
(9, '2', './uploads/C051219040640.csv', 'uploads/RC051219040640.csv', '2019-12-05 16:07:02'),
(10, '2', './uploads/C101219040454.csv', 'uploads/RC101219040454.csv', '2019-12-10 16:05:03'),
(11, '2', './uploads/C161219121838.csv', 'uploads/RC161219121838.csv', '2019-12-16 12:18:44'),
(12, '2', './uploads/C161219122107.csv', 'uploads/RC161219122107.csv', '2019-12-16 12:21:08'),
(13, '2', './uploads/CU190820115816.csv', 'uploads/RCU190820115816.csv', '2020-08-19 11:58:16'),
(14, '2', './uploads/C240820010611.csv', 'uploads/RC240820010611.csv', '2020-08-24 13:06:16'),
(15, '2', './uploads/C240820010759.csv', 'uploads/RC240820010759.csv', '2020-08-24 13:08:03'),
(16, '2', './uploads/C260820043624.csv', 'uploads/RC260820043624.csv', '2020-08-26 16:36:28'),
(17, '2', './uploads/C260820043721.csv', 'uploads/RC260820043721.csv', '2020-08-26 16:37:31'),
(18, '2', './uploads/CU310820123204.csv', 'uploads/RCU310820123204.csv', '2020-08-31 12:32:04'),
(19, '2', './uploads/CU310820123924.csv', 'uploads/RCU310820123924.csv', '2020-08-31 12:39:25'),
(20, '2', './uploads/CU040920050945.csv', 'uploads/RCU040920050945.csv', '2020-09-04 17:09:45'),
(21, '2', './uploads/C301020055629.csv', 'uploads/RC301020055629.csv', '2020-10-30 17:56:34'),
(22, '1', './uploads/CU070621103649.csv', 'uploads/RCU070621103649.csv', '2021-06-07 10:36:49');

-- --------------------------------------------------------

--
-- Table structure for table `log_funds`
--

CREATE TABLE `log_funds` (
  `id` int(11) NOT NULL,
  `ide` varchar(20) NOT NULL,
  `mount` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  `status` varchar(500) NOT NULL,
  `IdCard` varchar(10) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '1=form,2=layout'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `log_funds`
--

INSERT INTO `log_funds` (`id`, `ide`, `mount`, `date`, `status`, `IdCard`, `type`) VALUES
(1, '3', '1', '2019-09-27 10:17:20', 'FUNDING TO SUCCESSFUL CARD.', '554008', ''),
(2, '3', '1', '2019-09-27 10:18:23', 'REVERSE TO SUCCESSFUL CARD.', '554008', ''),
(3, '2', '1', '2019-10-08 10:40:53', 'FUNDING TO SUCCESSFUL CARD.', '152002', ''),
(4, '2', '1', '2019-10-08 10:41:04', 'FUNDING TO SUCCESSFUL CARD.', '546008', ''),
(5, '2', '1', '2019-10-08 10:41:22', 'REVERSE TO SUCCESSFUL CARD.', '152002', ''),
(6, '2', '1', '2019-10-08 10:42:15', 'REVERSE TO SUCCESSFUL CARD.', '546008', ''),
(7, '2', '1', '2019-10-08 10:43:11', 'FUNDING TO SUCCESSFUL CARD.', '546008', ''),
(8, '2', '1', '2019-10-08 10:46:26', 'REVERSE TO SUCCESSFUL CARD.', '546008', ''),
(9, '3', '3', '2019-10-09 12:34:29', 'FUNDING TO SUCCESSFUL CARD.', '554008', ''),
(10, '3', '5', '2019-10-09 12:35:06', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(11, '3', '4', '2019-10-09 12:35:22', 'THEY DO NOT HAVE ENOUGH FUNDS FOR THE FUNDING.', '554008', ''),
(12, '3', '1', '2019-10-09 12:35:58', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(13, '3', '3', '2019-10-09 12:36:08', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(14, '3', '3', '2019-10-09 12:36:28', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(15, '3', '1', '2019-10-09 12:36:32', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(16, '3', '1', '2019-10-09 12:37:27', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(17, '3', '3', '2019-10-09 12:37:42', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(18, '3', '3', '2019-10-09 12:41:40', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(19, '3', '1', '2019-10-09 12:44:36', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(20, '3', '1', '2019-10-09 12:48:45', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(21, '3', '1', '2019-10-09 12:52:56', 'FUNDING TO SUCCESSFUL CARD.', '554008', ''),
(22, '3', '1', '2019-10-09 12:54:00', 'REVERSE TO SUCCESSFUL CARD.', '554008', ''),
(23, '3', '1', '2019-10-09 13:04:23', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(24, '3', '3', '2019-10-09 13:04:26', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(25, '3', '3', '2019-10-09 13:04:51', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(26, '3', '1', '2019-10-09 13:27:00', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(27, '3', '3', '2019-10-09 19:07:13', 'THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.', '554008', ''),
(28, '3', '1', '2019-11-21 16:00:36', 'FUNDING TO SUCCESSFUL CARD.', '554008', ''),
(29, '3', '1', '2019-11-21 16:01:24', 'REVERSE TO SUCCESSFUL CARD.', '554008', ''),
(30, '3', '1', '2019-11-25 18:49:46', 'FUNDING TO SUCCESSFUL CARD.', '554008', ''),
(31, '3', '1', '2019-11-25 18:50:11', 'REVERSE TO SUCCESSFUL CARD.', '554008', ''),
(32, '3', '150', '2019-11-27 17:51:31', 'FUNDING TO SUCCESSFUL CARD.', '887002', ''),
(33, '2', '1', '2019-12-04 12:42:27', 'FUNDING TO SUCCESSFUL CARD.', '546008', ''),
(34, '2', '1', '2019-12-04 12:42:55', 'REVERSE TO SUCCESSFUL CARD.', '546008', ''),
(35, '2', '1', '2019-12-04 12:51:41', 'FUNDING TO SUCCESSFUL CARD.', '546008', ''),
(36, '2', '1', '2019-12-04 12:51:53', 'REVERSE TO SUCCESSFUL CARD.', '546008', ''),
(37, '2', '1', '2019-12-04 12:53:49', 'YOU CANNOT FUND THIS CARD CURRENTLY WAIT 5 MINUTES OR MODIFY THE AMOUNT', '546008', ''),
(38, '2', '1', '2019-12-05 11:37:50', '', '09546008', ''),
(39, '2', '0.65', '2019-12-05 11:37:52', '', '09546008', ''),
(40, '2', '1', '2019-12-05 11:40:04', '', '09546008', ''),
(41, '2', '0.65', '2019-12-05 11:40:06', '', '09546008', ''),
(42, '2', '1', '2019-12-05 11:44:07', '', '09546008', ''),
(43, '2', '0.65', '2019-12-05 11:44:09', '', '09546008', ''),
(44, '2', '1', '2019-12-05 11:45:13', '', '09546008', ''),
(45, '2', '0.65', '2019-12-05 11:45:16', '', '09546008', ''),
(46, '2', '1', '2019-12-05 11:48:09', '', '09546008', ''),
(47, '2', '0.65', '2019-12-05 11:48:10', '', '09546008', ''),
(48, '2', '1', '2019-12-05 11:48:42', 'FUNDING TO SUCCESSFUL CARD.', '546008', ''),
(49, '2', '0.65', '2019-12-05 11:51:10', 'IDCARD NOT VALID', '9546008', ''),
(50, '2', '0.65', '2019-12-05 11:52:10', 'FUNDING TO SUCCESSFUL CARD.', '09546008', ''),
(51, '2', '2', '2019-12-05 11:52:54', 'REVERSE TO SUCCESSFUL CARD.', '546008', ''),
(52, '2', '0.35', '2019-12-05 11:53:29', 'FUNDING TO SUCCESSFUL CARD.', '09546008', ''),
(53, '2', '2', '2019-12-05 16:05:45', 'REVERSE TO SUCCESSFUL CARD.', '546008', ''),
(54, '2', '1', '2019-12-05 16:06:13', 'FUNDING TO SUCCESSFUL CARD.', '546008', ''),
(55, '2', '0.3', '2019-12-05 16:06:47', 'FUNDING TO SUCCESSFUL CARD.', '09546008', ''),
(56, '2', '0.2', '2019-12-05 16:06:52', 'FUNDING TO SUCCESSFUL CARD.', '09546008', ''),
(57, '2', '0.5', '2019-12-05 16:07:02', 'FUNDING TO SUCCESSFUL CARD.', '09546008', ''),
(58, '2', '1', '2019-12-10 16:02:28', 'FUNDING TO SUCCESSFUL CARD.', '546008', ''),
(59, '2', '1', '2019-12-10 16:03:18', 'REVERSE TO SUCCESSFUL CARD.', '546008', ''),
(60, '2', '0.3', '2019-12-10 16:05:03', 'FUNDING TO SUCCESSFUL CARD.', '09546008', ''),
(61, '2', '3', '2019-12-10 16:26:47', 'REVERSE TO SUCCESSFUL CARD.', '546008', ''),
(62, '2', '1', '2019-12-16 12:15:17', 'FUNDING TO SUCCESSFUL CARD.', '546008', ''),
(63, '2', '2', '2019-12-16 12:18:44', 'FUNDING TO SUCCESSFUL CARD.', '09546008', ''),
(64, '2', '1', '2019-12-16 12:21:08', 'THE CARD DO NOT REGISTER IN THE SYSTEM', '42424242', ''),
(65, '3', '150', '2019-12-22 17:29:04', 'FUNDING TO SUCCESSFUL CARD.', '887002', ''),
(66, '3', '1', '2019-12-23 12:11:32', 'FUNDING TO SUCCESSFUL CARD.', '554008', ''),
(67, '3', '1', '2019-12-23 12:12:11', 'REVERSE TO SUCCESSFUL CARD.', '554008', ''),
(68, '3', '10', '2020-01-08 11:15:53', 'REVERSE TO SUCCESSFUL CARD.', '887002', ''),
(69, '3', '5', '2020-01-08 11:16:54', 'FUNDING TO SUCCESSFUL CARD.', '887002', ''),
(70, '3', '5', '2020-01-08 11:35:29', 'FUNDING TO SUCCESSFUL CARD.', '887002', ''),
(71, '3', '1', '2020-01-23 15:07:03', 'FUNDING TO SUCCESSFUL CARD.', '887002', ''),
(72, '3', '1', '2020-01-23 15:07:45', 'REVERSE TO SUCCESSFUL CARD.', '887002', ''),
(73, '2', '100', '2020-01-24 11:24:43', 'THEY DO NOT HAVE ENOUGH FUNDS FOR THE FUNDING.', '546008', ''),
(74, '3', '65', '2020-03-11 14:40:26', 'REVERSE TO SUCCESSFUL CARD.', '416004', ''),
(75, '3', '65', '2020-03-17 09:47:51', 'FUNDING TO SUCCESSFUL CARD.', '310005', ''),
(76, '2', '2', '2020-05-13 15:47:27', 'REVERSE TO SUCCESSFUL CARD.', '529004', ''),
(77, '2', '1', '2020-08-12 18:12:13', 'FUNDING TO SUCCESSFUL CARD.', '546008', ''),
(78, '2', '1', '2020-08-12 18:12:30', 'REVERSE TO SUCCESSFUL CARD.', '546008', ''),
(79, '2', '1', '2020-08-21 11:40:54', 'FUNDING TO SUCCESSFUL CARD.', '546008', ''),
(80, '2', '1', '2020-08-21 11:41:27', 'REVERSE TO SUCCESSFUL CARD.', '546008', ''),
(81, '2', '2', '2020-08-24 13:03:17', 'FUNDING TO SUCCESSFUL CARD.', '09546008', ''),
(82, '2', '2', '2020-08-24 13:03:50', 'REVERSE TO SUCCESSFUL CARD.', '09546008', ''),
(83, '2', '2', '2020-08-24 13:06:16', 'YOU CANNOT FUND THIS CARD CURRENTLY WAIT 5 MINUTES OR MODIFY THE AMOUNT', '09546008', ''),
(84, '2', '3', '2020-08-24 13:08:03', 'FUNDING TO SUCCESSFUL CARD.', '09546008', ''),
(85, '2', '3', '2020-08-24 13:08:44', 'REVERSE TO SUCCESSFUL CARD.', '09546008', ''),
(86, '2', '1', '2020-08-24 16:23:28', 'FUNDING TO SUCCESSFUL CARD.', '546008', ''),
(87, '2', '1', '2020-08-24 16:23:46', 'REVERSE TO SUCCESSFUL CARD.', '546008', ''),
(88, '2', '1', '2020-08-25 13:22:54', 'FUNDING TO SUCCESSFUL CARD.', '546008', ''),
(89, '2', '2', '2020-08-26 16:33:53', 'FUNDING TO SUCCESSFUL CARD.', '09546008', ''),
(90, '2', '2', '2020-08-26 16:34:39', 'REVERSE TO SUCCESSFUL CARD.', '09546008', ''),
(91, '2', '2', '2020-08-26 16:36:28', 'YOU CANNOT FUND THIS CARD CURRENTLY WAIT 5 MINUTES OR MODIFY THE AMOUNT', '09546008', ''),
(92, '2', '3', '2020-08-26 16:37:31', 'FUNDING TO SUCCESSFUL CARD.', '09546008', ''),
(93, '2', '4', '2020-08-26 16:39:44', 'REVERSE TO SUCCESSFUL CARD.', '09546008', ''),
(94, '2', '2', '2020-10-30 17:54:35', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(95, '2', '0.1', '2020-10-30 17:56:34', 'FONDEO A TARJETA EXITOSO.', '09546008', '2'),
(96, '2', '-2', '2020-10-30 17:57:28', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(97, '2', '-1', '2020-11-24 13:09:34', '00REVERO CORRECTO DE LA TARJETA.', '09546008', '1'),
(98, '2', '1', '2020-11-24 13:14:25', 'NO SE CUENTAN CON SUFICIENTES FONDOS PARA EL FONDEO.', '09546008', '1'),
(99, '2', '-1', '2020-11-24 13:14:42', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(100, '2', '1', '2021-03-11 13:41:53', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(101, '2', '-1', '2021-03-11 13:43:22', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(102, '2', '1', '2021-03-11 16:21:17', 'FONDEO A TARJETA EXITOSO.', '10152002', '1'),
(103, '2', '-1', '2021-03-11 16:26:06', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(104, '2', '1', '2021-03-11 16:27:43', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(105, '2', '-1', '2021-03-11 16:28:01', 'FONDEO A TARJETA EXITOSO.', '10152002', '1'),
(106, '2', '1', '2021-03-11 17:42:37', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(107, '2', '-1', '2021-03-11 17:43:42', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(108, '2', '1', '2021-03-17 13:24:01', 'FONDEO A TARJETA EXITOSO.', '10152002', '1'),
(109, '2', '-2', '2021-03-17 13:24:20', 'FONDEO A TARJETA EXITOSO.', '10152002', '1'),
(110, '2', '2', '2021-05-03 17:24:36', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(111, '2', '-3', '2021-05-03 18:08:02', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(112, '2', '2', '2021-05-10 17:41:31', 'NO SE APLICO EL FONDEO, INTENTE MAS TARDE', '09546008', '1'),
(113, '2', '2', '2021-05-10 17:43:55', 'NO SE APLICO EL FONDEO, INTENTE MAS TARDE', '10152002', '1'),
(114, '2', '1', '2021-05-10 17:44:40', 'NO SE APLICO EL FONDEO, INTENTE MAS TARDE', '09546008', '1'),
(115, '2', '-2', '2021-05-10 17:45:04', 'REVERSO A TARJETA EXITOSO.', '09546008', '1'),
(116, '2', '1', '2021-05-10 17:48:13', 'NO SE APLICO EL FONDEO, INTENTE MAS TARDE', '10152002', '1'),
(117, '2', '2', '2021-05-10 17:48:33', 'NO SE APLICO EL FONDEO, INTENTE MAS TARDE', '09546008', '1'),
(118, '2', '2', '2021-05-10 17:54:03', 'NO SE APLICO EL FONDEO, INTENTE MAS TARDE', '09546008', '1'),
(119, '2', '2', '2021-05-10 17:57:47', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(120, '2', '2', '2021-05-10 18:02:47', 'NO SE PUEDE FONDEAR ESTA TARJETA ACTUALMENTE ESPERE 5 MINUTOS O MODIFIQUE EL MONTO', '09546008', '1'),
(121, '2', '3', '2021-05-10 18:03:08', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(122, '2', '-3', '2021-05-10 18:03:31', 'REVERSO A TARJETA EXITOSO.', '09546008', '1'),
(123, '2', '2', '2021-05-10 18:04:37', 'NO SE PUEDE FONDEAR ESTA TARJETA ACTUALMENTE ESPERE 5 MINUTOS O MODIFIQUE EL MONTO', '09546008', '1'),
(124, '2', '-2', '2021-05-10 18:05:05', 'REVERSO A TARJETA EXITOSO.', '09546008', '1'),
(125, '2', '4', '2021-05-10 18:05:28', 'FONDEO A TARJETA EXITOSO.', '09546008', '1'),
(126, '2', '-1', '2021-11-04 13:54:12', 'REVERSO A TARJETA EXITOSO.', '09546008', '1'),
(127, '2', '1', '2021-11-04 13:56:29', 'FONDEO A TARJETA EXITOSO.', '09546008', '1');

-- --------------------------------------------------------

--
-- Table structure for table `log_incidents_pays`
--

CREATE TABLE `log_incidents_pays` (
  `Id` int(11) NOT NULL,
  `id_company` varchar(20) NOT NULL,
  `card` varchar(20) NOT NULL,
  `date` datetime NOT NULL,
  `monto` varchar(20) NOT NULL,
  `mensage` varchar(300) NOT NULL,
  `User` varchar(20) NOT NULL,
  `Status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `log_incidents_pays`
--

INSERT INTO `log_incidents_pays` (`Id`, `id_company`, `card`, `date`, `monto`, `mensage`, `User`, `Status`) VALUES
(1, '2', '09546008', '2021-05-10 17:40:29', '2', 'DEBE INTRODUCIR UNA TARJETA VALIDA EN CANTIDAD DE DIGITOS', '2', '2'),
(2, '2', '10152002', '2021-05-10 17:42:53', '2', 'DEBE INTRODUCIR UNA TARJETA VALIDA EN CANTIDAD DE DIGITOS', '2', '2'),
(3, '2', '09546008', '2021-05-10 17:43:37', '1', 'DEBE INTRODUCIR UNA TARJETA VALIDA EN CANTIDAD DE DIGITOS', '2', '2'),
(4, '2', '10152002', '2021-05-10 17:47:11', '1', 'DEBE INTRODUCIR UNA TARJETA VALIDA EN CANTIDAD DE DIGITOS', '2', '2'),
(5, '2', '09546008', '2021-05-10 17:47:31', '2', 'DEBE INTRODUCIR UNA TARJETA VALIDA EN CANTIDAD DE DIGITOS', '2', '2'),
(6, '2', '09546008', '2021-05-10 17:53:01', '2', 'DEBE INTRODUCIR UNA TARJETA VALIDA EN CANTIDAD DE DIGITOS', '2', '1');

-- --------------------------------------------------------

--
-- Table structure for table `log_movements`
--

CREATE TABLE `log_movements` (
  `Id` int(11) NOT NULL,
  `id_company` varchar(20) NOT NULL,
  `card` varchar(20) NOT NULL,
  `date` varchar(20) NOT NULL,
  `intento` varchar(20) NOT NULL,
  `msg` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `log_movements`
--

INSERT INTO `log_movements` (`Id`, `id_company`, `card`, `date`, `intento`, `msg`) VALUES
(1, '3', '11887002', '2019-12-04 10:35:34', '1', '01NO ESTA DECLARADO TICKETMESSAGE Y ERRORMESSAGE'),
(2, '3', '11887002', '2019-12-04 10:40:09', '1', '01NO ESTA DECLARADO TICKETMESSAGE Y ERRORMESSAGE'),
(3, '1', '09546008', '2019-12-04 12:21:40', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(4, '2', '03310005', '2020-03-11 15:21:42', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(5, '2', '03310005', '2020-03-11 15:22:02', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(6, '3', '03310005', '2020-03-11 18:07:50', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(7, '2', '09529004', '2020-05-13 14:53:52', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(8, '2', '09529004', '2020-05-13 14:55:22', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(9, '2', '09529004', '2020-05-13 14:55:34', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(10, '2', '09529004', '2020-05-13 14:55:51', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(11, '2', '09529004', '2020-05-13 14:57:43', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(12, '2', '09529004', '2020-05-13 15:43:21', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(13, '2', '09529004', '2020-05-13 15:47:50', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(14, '2', '09529004', '2020-05-13 15:48:10', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(15, '2', '09546008', '2020-05-13 15:48:22', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(16, '1', '03310005', '2020-06-18 13:16:08', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(17, '1', '03310005', '2020-06-18 13:16:25', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(18, '1', '03310005', '2020-06-18 13:16:45', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(19, '1', '09416004', '2020-08-12 17:32:45', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(20, '2', '10152002', '2020-08-12 18:14:20', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(21, '1', '09529004', '2020-08-17 10:37:45', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(22, '1', '11887002', '2020-08-17 11:34:35', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(23, '2', '09529004', '2020-08-17 11:36:07', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(24, '1', '10152002', '2020-08-19 13:04:28', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(25, '1', '11419004', '2020-08-19 13:05:40', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(26, '2', '10152002', '2020-08-19 13:18:00', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(27, '2', '10152002', '2020-08-24 13:02:01', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(28, '1', '11554008', '2020-08-27 11:32:25', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(29, '2', '09546008', '2020-08-31 12:48:23', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(30, '1', '03310005', '2020-10-30 17:49:08', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(31, '2', '10152002', '2020-10-30 17:58:10', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(32, '1', '11887002', '2020-11-03 17:14:04', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(33, '1', '11887002', '2020-11-03 17:14:27', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(34, '1', '03310005', '2020-11-03 17:14:49', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(35, '1', '81164001', '2020-11-03 17:15:34', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(36, '1', '09416004', '2020-11-03 17:18:11', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(37, '2', '09546008', '2020-11-03 17:23:41', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(38, '2', '09546008', '2020-11-03 17:23:49', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(39, '1', '11554008', '2020-11-04 12:46:25', '1', 'NO SE PUDO GENERAR EL MOVIMIENTO.'),
(40, '1', '09546008', '2021-03-11 15:47:47', '1', 'DEBE INTRODUCIR UNA FECHA DE INICIO VALIDA'),
(41, '1', '09546008', '2021-03-11 15:48:01', '1', 'DEBE INTRODUCIR UNA FECHA DE INICIO VALIDA'),
(42, '1', '09546008', '2021-03-11 15:48:15', '1', 'DEBE INTRODUCIR UNA FECHA DE INICIO VALIDA'),
(43, '1', '11419004', '2021-03-11 15:48:23', '1', 'DEBE INTRODUCIR UNA FECHA DE INICIO VALIDA'),
(44, '1', '10152002', '2021-03-11 15:48:34', '1', 'DEBE INTRODUCIR UNA FECHA DE INICIO VALIDA'),
(45, '1', '09529004', '2021-03-11 15:48:41', '1', 'DEBE INTRODUCIR UNA FECHA DE INICIO VALIDA'),
(46, '2', '09546008', '2021-03-11 16:31:42', '1', 'DEBE INTRODUCIR UNA FECHA DE INICIO VALIDA'),
(47, '2', '09546008', '2021-03-11 17:46:04', '1', 'DEBE INTRODUCIR UNA FECHA DE INICIO VALIDA'),
(48, '1', '09546008', '2021-09-02 17:03:39', '1', 'DEBE INTRODUCIR UNA FECHA DE INICIO VALIDA'),
(49, '2', '09546008', '2021-11-08 12:15:11', '1', 'DEBE INTRODUCIR UNA FECHA DE INICIO VALIDA'),
(50, '2', '09546008', '2021-11-08 12:15:23', '1', 'DEBE INTRODUCIR UNA FECHA DE INICIO VALIDA'),
(51, '2', '09546008', '2021-11-08 12:16:13', '1', 'DEBE INTRODUCIR UNA FECHA DE INICIO VALIDA');

-- --------------------------------------------------------

--
-- Table structure for table `log_terms_conditions`
--

CREATE TABLE `log_terms_conditions` (
  `Id` bigint(20) NOT NULL,
  `Id_user` int(10) NOT NULL,
  `Status` varchar(100) NOT NULL DEFAULT '',
  `Date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `log_terms_conditions`
--

INSERT INTO `log_terms_conditions` (`Id`, `Id_user`, `Status`, `Date`) VALUES
(1, 1, 'ACEPTADO', '2020-08-20 11:56:39'),
(2, 2, 'ACEPTADO', '2020-08-20 18:19:01'),
(4, 3, 'ACEPTADO', '2020-09-14 12:12:19'),
(5, 7, 'ACEPTADO', '2020-10-30 17:45:38'),
(6, 42, 'ACEPTADO', '2021-06-07 10:32:02'),
(7, 27, 'ACEPTADO', '2021-06-07 10:45:39');

-- --------------------------------------------------------

--
-- Table structure for table `log_users`
--

CREATE TABLE `log_users` (
  `id` int(11) NOT NULL,
  `company` varchar(100) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `status` varchar(300) NOT NULL,
  `email` varchar(300) NOT NULL DEFAULT '',
  `id_card` varchar(100) NOT NULL DEFAULT '',
  `id_user_created` varchar(100) NOT NULL DEFAULT '',
  `type_upload` varchar(30) NOT NULL DEFAULT '' COMMENT '1=form,2=layout'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `log_users`
--

INSERT INTO `log_users` (`id`, `company`, `date`, `status`, `email`, `id_card`, `id_user_created`, `type_upload`) VALUES
(1, '3', '2019-09-26 12:43:08', 'EN PROCESO', '', '5513530007352003', '352003@energexcard.com', ''),
(2, '3', '2019-09-26 12:51:17', 'EN PROCESO', '', '5513530006139005', '139005@energexcard.com', ''),
(3, '3', '2019-09-26 13:08:37', 'OK', '', '5513530011554008', '554008@recargasenergex.mx', ''),
(4, '2', '2019-10-31 13:10:54', 'EN PROCESO', '', '5513530005670000', '05670000@energex.mx', ''),
(5, '2', '2019-10-31 13:16:54', 'EN PROCESO', '', '5513530005698001', '05698001@energex.mx', ''),
(6, '3', '2019-11-27 17:50:56', 'OK', '', '5513530011887002', 'jramos@recargasenergex.com', ''),
(9, '2', '2019-12-04 12:59:32', 'EN PROCESO', '', '5513530008023009', '08023009@energex.mx', ''),
(10, '2', '2019-12-04 13:02:10', 'EN PROCESO', '', '5513530005317008', '05317008@energex.mx', ''),
(11, '2', '2019-12-04 13:03:55', 'OK', '', '5513530003310005', '03310005@energex.mx', ''),
(12, '2', '2019-12-04 13:06:27', 'EN PROCESO', '', '5513530009546008', '12345@hotpay.mx', ''),
(13, '2', '2019-12-04 13:07:36', 'EN PROCESO', '', '5513530009546008', '08023009@energex.com', ''),
(14, '2', '2019-12-04 13:08:29', 'EN PROCESO', '', '5513530009546008', 'sincorreo@hotpa1y.mx', ''),
(15, '2', '2019-12-16 12:12:31', 'EN PROCESO', '', '5513530009546008', '5513530009546008@energex.mx', ''),
(16, '3', '2020-02-04 18:40:23', 'EN PROCESO', '', '5513530009416004', 'valeriacadena@energex.mx', ''),
(17, '3', '2020-02-04 18:47:40', 'OK', '', '5513530009416004', 'valeriacadena@energex.mx', ''),
(18, '2', '2020-05-13 12:48:51', 'OK', '', '5513530081163003', '81163003@energex.mx', ''),
(19, '2', '2020-05-13 14:31:02', 'EN PROCESO', '', '5513530009529004', '09529004@energex.mx', ''),
(20, '5', '2020-05-20 16:22:02', 'OK', '', '5513530011419004', '11419004@energex.mx', ''),
(21, '2', '2020-08-12 17:59:02', 'EN PROCESO', 'salvador@hotpay.mx', '', 'salvador@hotpay.mx', ''),
(22, '2', '2020-08-12 18:07:13', 'OK', 'salvador21@hotpay.mx', '', 'salvador21@hotpay.mx', ''),
(23, '2', '2020-08-19 11:58:16', 'EN PROCESO', 'demo@ener123.mx', '', 'demo@ener123.mx', ''),
(24, '2', '2020-08-31 12:23:08', 'EN PROCESO', '10152002@energex.mx', '', '10152002@energex.mx', ''),
(25, '2', '2020-08-31 12:26:52', 'EN PROCESO', '10152002@energex.mx', '', '10152002@energex.mx', ''),
(26, '2', '2020-08-31 12:32:04', 'EN PROCESO', '10152002@energex.mx', '', '10152002@energex.mx', ''),
(27, '2', '2020-08-31 12:39:24', 'EN PROCESO', '10152002@energex.mx', '09546008', '10152002@energex.mx', ''),
(28, '2', '2020-08-31 12:42:06', 'EN PROCESO', '10152002@energex.mx', '09546008', '10152002@energex.mx', ''),
(29, '2', '2020-09-04 17:09:45', 'OK', 'jose@gmail.com', '11115555', 'jose@gmail.com', ''),
(30, 'userdemo@energexpass.mx', '2020-10-30 17:52:03', 'PENDIENTE', 'userdemo@energexpass.mx', '55555559', '2', '1'),
(31, 'userdemo2@energexpass.mx', '2020-10-30 17:52:22', 'OK', 'userdemo2@energexpass.mx', '55555559', '2', '1'),
(32, '2', '2020-10-30 17:53:50', 'OK', 'usercontador2@energexpass.mx', '', '2', ''),
(33, '2', '2020-10-30 18:00:03', 'OK', 'jcontador@energexpass.mx', '', '2', ''),
(34, 'prueba@energexpass.mx', '2020-11-05 15:30:26', 'OK', 'prueba@energexpass.mx', '55512212', '2', '1'),
(35, '1', '2021-03-11 11:54:34', 'OK', 'josea@gocard.mx', '', '1', ''),
(36, '1', '2021-03-11 15:45:52', 'OK', 'jose@grupoenergetico.mx', '', '1', ''),
(37, 'juan@demo.mx', '2021-03-11 17:45:36', 'OK', 'juan@demo.mx', '42424242', '2', '1'),
(38, '1', '2021-03-11 17:53:34', 'OK', 'ruebn@grupoenergetico.mx', '', '1', ''),
(39, '7', '2021-03-11 18:01:22', 'OK', 'cnu@grupoenergetico.mx', '', '1', ''),
(40, '1', '2021-06-07 10:31:47', 'OK', 'jose@hotpay.mx', '', '7', ''),
(41, 'dme@ener.mx', '2021-06-07 10:36:49', 'OK', 'dmener', '42424242', '42', '2');

-- --------------------------------------------------------

--
-- Table structure for table `PaymentsAPI`
--

CREATE TABLE `PaymentsAPI` (
  `Id` int(11) NOT NULL,
  `PetitionArray` json NOT NULL,
  `AnswerArray` json NOT NULL,
  `Date` datetime NOT NULL,
  `Company` varchar(20) NOT NULL DEFAULT '',
  `User` varchar(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `PaymentsCards`
--

CREATE TABLE `PaymentsCards` (
  `Id` int(11) NOT NULL,
  `Card` varchar(16) NOT NULL DEFAULT '',
  `Monto` decimal(15,2) NOT NULL,
  `DateP` datetime NOT NULL,
  `Status` varchar(39) NOT NULL DEFAULT '' COMMENT '1=pendiente,2=exitoso,3=denegado,4=TimeOut,5=errorGeneral',
  `CodeAuth` varchar(300) NOT NULL DEFAULT '',
  `Company` varchar(20) NOT NULL DEFAULT '',
  `User` varchar(20) NOT NULL DEFAULT '',
  `IdPetitionAPI` varchar(30) NOT NULL DEFAULT '',
  `Concept` varchar(300) NOT NULL DEFAULT '',
  `CardHolder` varchar(1000) NOT NULL DEFAULT '',
  `Answer` varchar(300) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `paynet_cards`
--

CREATE TABLE `paynet_cards` (
  `Id` int(11) NOT NULL,
  `Company` int(11) NOT NULL,
  `IdCard` int(11) NOT NULL,
  `ComisionAdmin` decimal(10,2) NOT NULL DEFAULT '0.00',
  `IvaComisionAdmin` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ComisionCompany` decimal(10,2) NOT NULL DEFAULT '0.00',
  `IvaComisionCompany` decimal(10,2) NOT NULL DEFAULT '0.00',
  `RegisterDate` datetime NOT NULL,
  `UpdateDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `paynet_cards`
--

INSERT INTO `paynet_cards` (`Id`, `Company`, `IdCard`, `ComisionAdmin`, `IvaComisionAdmin`, `ComisionCompany`, `IvaComisionCompany`, `RegisterDate`, `UpdateDate`) VALUES
(1, 3, 2, '2.50', '16.00', '0.00', '0.00', '2020-12-07 16:00:00', '2020-12-07 16:00:00'),
(2, 2, 3, '2.50', '16.00', '0.00', '0.00', '2020-12-07 16:00:00', '2020-12-07 16:00:00'),
(3, 2, 4, '2.50', '16.00', '0.00', '0.00', '2020-12-07 16:00:00', '2020-12-07 16:00:00'),
(4, 3, 5, '2.50', '16.00', '0.00', '0.00', '2020-12-07 16:00:00', '2020-12-07 16:00:00'),
(5, 3, 7, '2.50', '16.00', '0.00', '0.00', '2020-12-07 16:00:00', '2020-12-07 16:00:00'),
(6, 3, 8, '2.50', '16.00', '0.00', '0.00', '2020-12-07 16:00:00', '2020-12-07 16:00:00'),
(7, 2, 9, '2.50', '16.00', '0.00', '0.00', '2020-12-07 16:00:00', '2020-12-07 16:00:00'),
(8, 5, 10, '2.50', '16.00', '0.00', '0.00', '2020-12-07 16:00:00', '2020-12-07 16:00:00'),
(9, 3, 11, '2.50', '16.00', '0.00', '0.00', '2020-12-07 16:00:00', '2020-12-07 16:00:00'),
(10, 2, 12, '2.50', '16.00', '0.00', '0.00', '2021-03-11 17:45:37', '2021-03-11 17:45:37'),
(11, 6, 13, '2.50', '16.00', '0.00', '0.00', '2021-06-07 10:36:49', '2021-06-07 10:36:49');

-- --------------------------------------------------------

--
-- Table structure for table `paynet_comision`
--

CREATE TABLE `paynet_comision` (
  `Id` int(11) NOT NULL,
  `Card` varchar(100) NOT NULL DEFAULT '',
  `Amount_Charge` varchar(200) NOT NULL DEFAULT '',
  `AmountPaynet` varchar(100) NOT NULL DEFAULT '',
  `Date` datetime NOT NULL,
  `Concepto` varchar(100) NOT NULL DEFAULT '',
  `User` varchar(200) NOT NULL DEFAULT '',
  `Company` varchar(200) NOT NULL DEFAULT '',
  `Comision_apply` varchar(200) NOT NULL DEFAULT '',
  `Status` varchar(100) NOT NULL DEFAULT '' COMMENT '1=Activo,2=pagado',
  `Id_Transactions` varchar(100) NOT NULL DEFAULT '',
  `ComisionAdmin` decimal(10,2) NOT NULL DEFAULT '0.00',
  `IvaComisionAdmin` decimal(10,2) NOT NULL DEFAULT '0.00',
  `AmountComisionAdmin` decimal(10,2) NOT NULL DEFAULT '0.00',
  `AmountIvaComisionAdmin` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ComisionCompany` decimal(10,2) NOT NULL DEFAULT '0.00',
  `IvaComisionCompany` decimal(10,2) NOT NULL DEFAULT '0.00',
  `AmountComisionCompany` decimal(10,2) NOT NULL DEFAULT '0.00',
  `AmountIvaComisionCompany` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `products_platform`
--

CREATE TABLE `products_platform` (
  `Id` int(11) NOT NULL,
  `Id_Product` varchar(10) NOT NULL DEFAULT '',
  `Digitos_tarjeta` varchar(20) NOT NULL DEFAULT '',
  `Product` varchar(200) NOT NULL DEFAULT '',
  `AgreementId` varchar(30) NOT NULL DEFAULT '',
  `ProductId` varchar(30) NOT NULL DEFAULT '',
  `Status` varchar(2) NOT NULL DEFAULT '',
  `label_convenio` varchar(200) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products_platform`
--

INSERT INTO `products_platform` (`Id`, `Id_Product`, `Digitos_tarjeta`, `Product`, `AgreementId`, `ProductId`, `Status`, `label_convenio`) VALUES
(1, '1', '55135300', 'MASTERCARD', '3018', '27', '1', '');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `Id` bigint(20) NOT NULL,
  `Name` varchar(300) NOT NULL,
  `Options` varchar(500) NOT NULL,
  `Level` varchar(20) NOT NULL,
  `Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`Id`, `Name`, `Options`, `Level`, `Status`) VALUES
(1, 'IT', 'opt01opt02opt04opt05opt10opt11opt12opt13opt18opt19opt21opt22opt23opt24opt25opt40opt41opt42opt50opt60opt61opt63opt64opt66opt67opt68', '1', 'ACTIVE'),
(2, 'ADMIN', 'opt02opt04opt05opt10opt11opt12opt13opt21opt22opt23opt24opt25opt40opt41opt42opt60', '2', 'ACTIVE'),
(3, 'ADMINISTRACION CONTADOR', 'opt02opt04opt05opt11opt12opt23opt40opt41opt42opt60', '3', 'ACTIVE'),
(4, 'ADMINISTRACION MONITOR', 'opt02opt04opt05opt12opt40opt41opt42opt60', '3', 'ACTIVE'),
(5, 'EMPRESA', 'opt03opt04opt06opt14opt15opt16opt17opt26opt27opt28opt29opt30opt31opt44opt45opt46opt47opt62', '4', 'ACTIVE'),
(6, 'EMPRESA CONTADOR', 'opt03opt04opt06opt15opt16opt28opt29opt44opt45opt46opt47opt62', '5', 'ACTIVE'),
(7, 'EMPRESA MONITOR', 'opt03opt04opt06opt16opt44opt45opt46opt47opt62', '5', 'ACTIVE'),
(8, 'EMPLEADO', '', '6', 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `profile_employes`
--

CREATE TABLE `profile_employes` (
  `Id` int(11) NOT NULL,
  `Name_profile` varchar(500) NOT NULL,
  `Status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `profile_employes`
--

INSERT INTO `profile_employes` (`Id`, `Name_profile`, `Status`) VALUES
(1, 'EMPLOYEE', '1'),
(2, 'MANAGER', '1'),
(3, 'EXECUTIVE', '1'),
(4, 'EXECUTIVE DIRECTOR', '1');

-- --------------------------------------------------------

--
-- Table structure for table `Saldos_MA_BC`
--

CREATE TABLE `Saldos_MA_BC` (
  `Id` int(11) NOT NULL,
  `Id_Convenio` int(11) NOT NULL,
  `Concept` varchar(300) NOT NULL DEFAULT '',
  `Saldo` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Id_Mov` int(11) NOT NULL,
  `Fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ide` bigint(20) NOT NULL,
  `email` varchar(250) DEFAULT NULL,
  `company` int(11) DEFAULT NULL,
  `fullname` varchar(250) DEFAULT NULL,
  `address` text,
  `city` varchar(200) DEFAULT NULL,
  `zip` int(6) DEFAULT NULL,
  `aboutme` text,
  `picture` varchar(255) DEFAULT NULL,
  `idcard` int(11) DEFAULT NULL,
  `perfil` varchar(50) DEFAULT NULL,
  `up_date` date DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `idkey` varchar(50) DEFAULT NULL,
  `phone` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `email2` varchar(300) NOT NULL,
  `perfil_TH` varchar(200) NOT NULL DEFAULT '',
  `IdUser_API` varchar(200) NOT NULL DEFAULT '',
  `created_by` varchar(30) NOT NULL DEFAULT '',
  `CustomCamp` varchar(1000) NOT NULL DEFAULT '',
  `create_at` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ide`, `email`, `company`, `fullname`, `address`, `city`, `zip`, `aboutme`, `picture`, `idcard`, `perfil`, `up_date`, `active`, `idkey`, `phone`, `state`, `email2`, `perfil_TH`, `IdUser_API`, `created_by`, `CustomCamp`, `create_at`) VALUES
(1, 'energex@grupoenergetico.mx', 1, 'ENERGEX GRUPO ENERGETICOS', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, 'Usario Administrador de la plataforma.', 'assets/img/faces/face-8.jpg', 1, 'ADMIN', '2019-03-19', 1, 'h8MFq7yLWDbMAY1GHlBL', '8120087802', 'NUEVO LEÓN', '', '', '', '', '', ''),
(2, 'energex@demo.mx', 2, 'Demo Energex', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, 'Demo energex.', 'assets/img/empleado.png', 0, 'EMPRESA', '2020-08-12', 1, '123456789', '8121345378', 'NUEVO LEÓN', '', '', '', '', '', ''),
(3, 'recargasenergeticos@energex.mx', 3, 'Recargas Grupo Energeticos S. A de C.V.', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTEMORELOS', 67000, 'EMPRESA 2', 'assets/img/energex_1.png', 0, 'EMPRESA', '2019-09-26', 1, 'RECARGASGRUPOENERGETICOS', '8127537641', 'NUEVO LEÓN', '', '', '', '', '', ''),
(4, '554008@recargasenergex.mx', 3, 'Empleado 1.', 'Av. Fundidora 501, Interior 128PN , Colonia Obrera, Monterrey Nuevo León.', 'MONTERREY', 67000, 'empleado 1.', 'assets/img/empleado.png', 2, 'EMPLEADO', '2019-09-26', 1, '123456789', '8126654672', 'NUEVO LEÓN', '', '', '', '', '', ''),
(5, 'contador@energex.mx', 1, 'Empleado admin contador', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora...', 'MONTERREY', 64000, 'Data.', 'assets/img/empleado.png', 0, 'ADMINISTRACION CONTADOR', '2021-03-11', 1, '123456789', '8127532147', 'NUEVO LEON', '', '', '', '', '', ''),
(6, 'monitor@energex.mx', 1, 'Monitor admin', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 64000, 'data', 'assets/img/empleado.png', 0, 'ADMINISTRACION MONITOR', '2019-10-08', 1, '123456789', '8775127521', 'NUEVO LEON', '', '', '', '', '', ''),
(7, 'salvador@hotpay.mx', 1, 'Salvador', 'Sin datos por el momento', 'MONTERREY', 67000, 'sin datos por el momento', '/assets/img/empleado.png', 0, 'IT', '2019-10-08', 1, 'ZGRSD0D9H328', '9717270779', 'NUEVO LEON', '', '', '', '', '', ''),
(8, 'contador@demo.mx', 2, 'contador demo 1', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, 'Demo', 'assets/img/empleado.png', 0, 'EMPRESA CONTADOR', '2020-08-19', 1, '12345678', '8162873571', 'NUEVO LEÓN', '', '', '', '', '', ''),
(9, 'monitor@demo.mx', 2, 'Monitor demo', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, 'demo', 'assets/img/empleado.png', 0, 'EMPRESA MONITOR', '2019-10-08', 1, '123456789', '8168752187', 'NUEVO LEÓN', '', '', '', '', '', ''),
(10, '10152002@energex.mx', 2, 'SALVADOR SISTEMAS', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora..', 'MONTERREY', 67000, '', 'assets/img/empleado.png', 3, 'EMPLEADO', '2021-03-11', 1, '12345678', '8615313231', 'NUEVO LEON', '', '', '', '', '', ''),
(11, '09546008@energex.mx', 2, 'empleado 2', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, 's', 'assets/img/empleado.png', 4, 'EMPLEADO', '2020-08-12', 1, '12345678', '8154513245', 'NUEVO LEON', '', '', '', '', '', ''),
(14, 'prueba@recargasenergex.com', 3, 'Empleado 2', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, 'Empleado 2', 'assets/img/Recargas logotipo.png', 0, 'EMPRESA CONTADOR', '2019-10-29', 1, 'recargas1234', '8110000000', 'NUEVO LEÓN', '', '', '', '', '', ''),
(15, 'prueba1@recargasenergex.com', 3, 'Empleado 3', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, 'Empleado 3', 'assets/img/Recargas logotipo.png', 0, 'EMPRESA MONITOR', '2019-12-04', 1, 'recargas123', '8110000001', 'NUEVO LEÓN', '', '', '', '', '', ''),
(16, 'alza.mazon@gmail.com', 4, 'Alza Desarrollos', 'Quinta Elisa 32-C', 'HERMOSILLO', 67000, 'a', 'assets/img/empleado3da.png', 0, 'EMPRESA', '2019-12-04', 1, 'ALZA123456', '9868125373', 'SONORA', '', '', '', '', '', ''),
(17, 'jramos@recargasenergex.com', 3, 'Jesus Cesar ramos Garza', 'San Diego 174 Col Cumbres San Agustin', 'MONTERREY', 64346, 'Generación de pruebas', 'assets/img/logo_base.jpg', 5, 'EMPLEADO', '2019-11-27', 1, 'Jramos10', '8182527911', 'NUEVO LEÓN', '', '', '', '', '', ''),
(22, '03310005@energex.mx', 3, 'Valeria cadena nuevo', 'Sin datos por el momento', 'MONTERREY', 67000, 'a', 'assets/img/empleado3da.png', 7, 'EMPLEADO', '2020-03-11', 1, '12345678', '8962137851', 'NUEVO LEÓN', '03310005@energex.mx', '', '3904', '', '', ''),
(23, 'valeriacadena@energex.mx', 3, 'Valeria Cadena', 'Sin datos por el momento', 'MONTERREY', 67000, 'a', 'assets/img/empleado3da.png', 8, 'EMPLEADO', '2020-02-05', 1, '123456789', '8962136127', 'NUEVO LEÓN', 'valeriacadena@energex.mx', '', '4150', '', '', ''),
(24, '11419004@energex.mx', 2, 'CLUSTER DOLARES', 'Sin datos por el momento', 'MONTERREY', 67000, 'w', 'assets/img/empleado.png', 9, 'EMPLEADO', '2020-05-13', 1, '12345678', '8112345677', 'NUEVO LEÓN', 'dolares@clusterenergetico.org', '', '', '', '', ''),
(25, 'olivia@clusterenergetico.org', 5, 'OLIVIA MENDOZA CADENA', 'CARRETERA A VICTORIA KM 14.5 COLONIA CENTRO LINARES', 'LINARES', 67700, 'Empresa en dlls.', 'assets/img/descarga.jpg', 0, 'EMPRESA', '2020-05-14', 1, '12345678', '8115029350', 'NUEVO LEÓN', 'olivia@clusterenergetico.org', '', '', '', '', ''),
(26, 'dolares@clusterenergetico.org', 5, 'CLUSTER ENERGETICO DE NUEVO LEON AC', 'Carretera a Victoria Km 141.5', 'MONTERREY', 67000, 'uu', 'assets/img/empleado.png', 10, 'EMPLEADO', '2020-05-22', 1, '12345678', '8181891837', 'NUEVO LEÓN', '11419004@energex.mx', '', '', '', '', ''),
(27, 'demo1@energex.mx', 6, 'demo1', 'sin datos por el momento', 'MONTERREY', 67000, 'de ', 'assets/img/empleado.png', 0, 'EMPRESA', '2020-10-30', 1, '12345678', '9711092328', 'NUEVO LEÓN', 'demo1@energex.mx', '', '', '', '', ''),
(28, 'salvador21@hotpay.mx', 2, 'salvador', 'sin datos por el momento', 'MONTERREY', 67000, 'a', 'assets/img/empleado.png', 0, 'EMPLEADO', '2020-08-12', 1, '12345678', '9711092328', 'NUEVO LEÓN', 'salvador21@hotpay.mx', 'EMPLOYEE', '5710', '', '', ''),
(29, 'p1@energex.mx', 2, 'Prueba Reg', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, '34', 'assets/img/empleado.png', 0, 'EMPRESA CONTADOR', '2020-08-24', 1, '12345678', '9867969876', 'NUEVO LE?N', 'p1@energex.mx', '', '', '', '', ''),
(30, '1@energex.mx', 2, 'Reg 2', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, '', 'assets/img/empleado.png', 0, 'EMPRESA CONTADOR', '2020-08-24', 1, '12345678', '7556565656', 'NUEVO LE?N', '1@energex.mx', '', '', '', '', ''),
(32, 'josea@gmail.com', 1, 'Josea', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 64000, '', 'assets/img/empleado.png', 0, 'IT', '2020-09-24', 1, '123456789', '7878787878', 'NUEVO LEON', 'josea@gmail.com', '', '', '', '', ''),
(33, 'userdemo2@energexpass.mx', 2, 'User demo 2', 'S/D', 'TAMPACÁN', 87878, 'Prueba', 'assets/img/empleado.png', 0, 'EMPLEADO', '2020-10-30', 1, '12345678', '8787888878', 'SAN LUIS POTOSÍ', 'userdemo2@energexpass.mx', 'EMPLOYEE', '6113', '', '', ''),
(34, 'usercontador2@energexpass.mx', 2, 'User contador', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, 'prueba', 'assets/img/user.png', 0, 'EMPRESA CONTADOR', '2020-10-30', 1, '12345678', '7676677676', 'NUEVO LEÓN', 'usercontador2@energexpass.mx', '', '', '', '', ''),
(35, 'jcontador@energexpass.mx', 2, 'Jose Contador', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 67000, 'User', 'assets/img/user.png', 0, 'EMPRESA CONTADOR', '2021-03-11', 1, '12345678', '8878787878', 'NUEVO LEÓN', 'jcontador@energexpass.mx', '', '', '', '', ''),
(36, 'prueba@energexpass.mx', 2, 'Prueba', 'S/D', 'CAMPECHE', 44545, 'Prueba', 'assets/img/user.png', 0, 'EMPLEADO', '2020-11-05', 1, '12345678', '8787878778', 'CAMPECHE', 'prueba@energexpass.mx', 'EMPLOYEE', '6140', '', '', ''),
(37, 'josea@gocard.mx', 1, 'JOSE ARMANDO MORALES ANTONIO', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 64000, 'User', 'assets/img/empleado.png', 0, 'ADMINISTRACION CONTADOR', '2021-03-11', 1, 'JOSEARMANDO1', '7676767676', 'NUEVO LEON', 'josea@gocard.mx', '', '', '', '', ''),
(38, 'jose@grupoenergetico.mx', 1, 'Jose', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 64000, 'Demo', 'assets/img/user.png', 0, 'ADMINISTRACION CONTADOR', '2021-03-11', 1, '12345678', '7667767676', 'NUEVO LEON', 'jose@grupoenergetico.mx', '', '', '', '', ''),
(39, 'juan@demo.mx', 2, 'Júan Ñ', 'S/D', 'MONTERREY', 64000, 'Prueba', 'assets/img/user.png', 0, 'EMPLEADO', '2021-03-11', 1, '12345678', '8878787878', 'NUEVO LEÓN', 'juan@demo.mx', 'EMPLOYEE', '6806', '', '', ''),
(40, 'ruebn@grupoenergetico.mx', 1, 'Rubén', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 64000, '', 'assets/img/empleado.png', 0, 'ADMINISTRACION CONTADOR', '2021-03-11', 1, '12345678', '8787676765', 'NUEVO LEON', 'ruebn@grupoenergetico.mx', '', '', '', '', ''),
(41, 'cnu@grupoenergetico.mx', 7, 'Núñez 2', 'S/D', 'MONTERREY', 64000, 'Demo', 'assets/img/empleado.png', 0, 'EMPRESA', '2021-03-12', 1, '12345678', '7875665443', 'NUEVO LEÓN', 'cnu@grupoenergetico.mx', '', '', '', '', ''),
(42, 'jose@hotpay.mx', 1, 'Jose', 'Cintermex Local 82 Av. Fundidora, Parque Fundidora.', 'MONTERREY', 64000, '', 'assets/img/empleado.png', 0, 'IT', '2021-06-07', 1, '12345678', '7689898898', 'NUEVO LEON', 'jose@hotpay.mx', '', '', '', '', ''),
(43, 'dmener', 6, 'DemoEner', 'sin datos por el momento', 'MONTERREY', 67000, 'EMPLEADO DE demo1 sa de cv', 'assets/img/empleado.png', 0, 'EMPLEADO', '2021-06-07', 1, '55455194', '9711092328', 'NUEVO LEÓN', 'dme@ener.mx', 'EMPLEADO', '7170', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_services`
--
ALTER TABLE `access_services`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `analisis_cards`
--
ALTER TABLE `analisis_cards`
  ADD PRIMARY KEY (`ide`);

--
-- Indexes for table `anualidad_tarjetas`
--
ALTER TABLE `anualidad_tarjetas`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `ide` (`ide`);

--
-- Indexes for table `cards_changes`
--
ALTER TABLE `cards_changes`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `card_temp`
--
ALTER TABLE `card_temp`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `ciudades`
--
ALTER TABLE `ciudades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estado_id` (`estado_id`);

--
-- Indexes for table `companys`
--
ALTER TABLE `companys`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `ide` (`ide`);

--
-- Indexes for table `connect_API`
--
ALTER TABLE `connect_API`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `data_mails`
--
ALTER TABLE `data_mails`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facturas_cards`
--
ALTER TABLE `facturas_cards`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `founds_MA`
--
ALTER TABLE `founds_MA`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `funds`
--
ALTER TABLE `funds`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `ide` (`ide`);

--
-- Indexes for table `Funds_Temp`
--
ALTER TABLE `Funds_Temp`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `Fund_manuals`
--
ALTER TABLE `Fund_manuals`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `IntentsPayments`
--
ALTER TABLE `IntentsPayments`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `Keys_API`
--
ALTER TABLE `Keys_API`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `layout_anchor`
--
ALTER TABLE `layout_anchor`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `ide` (`ide`);

--
-- Indexes for table `layout_uploads`
--
ALTER TABLE `layout_uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_funds`
--
ALTER TABLE `log_funds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_incidents_pays`
--
ALTER TABLE `log_incidents_pays`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `log_movements`
--
ALTER TABLE `log_movements`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `log_terms_conditions`
--
ALTER TABLE `log_terms_conditions`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `log_users`
--
ALTER TABLE `log_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `PaymentsAPI`
--
ALTER TABLE `PaymentsAPI`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `PaymentsCards`
--
ALTER TABLE `PaymentsCards`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `paynet_cards`
--
ALTER TABLE `paynet_cards`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `paynet_comision`
--
ALTER TABLE `paynet_comision`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `products_platform`
--
ALTER TABLE `products_platform`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `profile_employes`
--
ALTER TABLE `profile_employes`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `Saldos_MA_BC`
--
ALTER TABLE `Saldos_MA_BC`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ide`),
  ADD KEY `ide` (`ide`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_services`
--
ALTER TABLE `access_services`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `analisis_cards`
--
ALTER TABLE `analisis_cards`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;
--
-- AUTO_INCREMENT for table `anualidad_tarjetas`
--
ALTER TABLE `anualidad_tarjetas`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `cards_changes`
--
ALTER TABLE `cards_changes`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `card_temp`
--
ALTER TABLE `card_temp`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ciudades`
--
ALTER TABLE `ciudades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2493;
--
-- AUTO_INCREMENT for table `companys`
--
ALTER TABLE `companys`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `connect_API`
--
ALTER TABLE `connect_API`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `data_mails`
--
ALTER TABLE `data_mails`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;
--
-- AUTO_INCREMENT for table `estados`
--
ALTER TABLE `estados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `facturas_cards`
--
ALTER TABLE `facturas_cards`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `founds_MA`
--
ALTER TABLE `founds_MA`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `funds`
--
ALTER TABLE `funds`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;
--
-- AUTO_INCREMENT for table `Funds_Temp`
--
ALTER TABLE `Funds_Temp`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `Fund_manuals`
--
ALTER TABLE `Fund_manuals`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `IntentsPayments`
--
ALTER TABLE `IntentsPayments`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Keys_API`
--
ALTER TABLE `Keys_API`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `layout_anchor`
--
ALTER TABLE `layout_anchor`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;
--
-- AUTO_INCREMENT for table `layout_uploads`
--
ALTER TABLE `layout_uploads`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `log_funds`
--
ALTER TABLE `log_funds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;
--
-- AUTO_INCREMENT for table `log_incidents_pays`
--
ALTER TABLE `log_incidents_pays`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `log_movements`
--
ALTER TABLE `log_movements`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
--
-- AUTO_INCREMENT for table `log_terms_conditions`
--
ALTER TABLE `log_terms_conditions`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `log_users`
--
ALTER TABLE `log_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT for table `PaymentsAPI`
--
ALTER TABLE `PaymentsAPI`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `PaymentsCards`
--
ALTER TABLE `PaymentsCards`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `paynet_cards`
--
ALTER TABLE `paynet_cards`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `paynet_comision`
--
ALTER TABLE `paynet_comision`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `products_platform`
--
ALTER TABLE `products_platform`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `profile_employes`
--
ALTER TABLE `profile_employes`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `Saldos_MA_BC`
--
ALTER TABLE `Saldos_MA_BC`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ide` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
