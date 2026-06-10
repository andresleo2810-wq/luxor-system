CREATE DATABASE  IF NOT EXISTS `luxor` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `luxor`;
-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: luxor
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `caja`
--

DROP TABLE IF EXISTS `caja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caja` (
  `idCaja` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) NOT NULL,
  `FechaApertura` datetime DEFAULT NULL,
  `Cajacol` varchar(45) DEFAULT NULL,
  `FechaCierre` datetime DEFAULT NULL,
  `MontoInicial` decimal(10,2) DEFAULT NULL,
  `MontoFinal` decimal(10,2) DEFAULT NULL,
  `Estado` enum('Abierta','Cerrada') DEFAULT 'Abierta',
  PRIMARY KEY (`idCaja`),
  KEY `fk_Caja_Usuario` (`idUsuario`),
  CONSTRAINT `fk_Caja_Usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja`
--

LOCK TABLES `caja` WRITE;
/*!40000 ALTER TABLE `caja` DISABLE KEYS */;
INSERT INTO `caja` VALUES (1,3,'2025-10-01 08:00:00','Caja 1','2025-10-01 18:00:00',500000.00,1250000.00,'Cerrada'),(2,4,'2025-10-02 08:00:00','Caja 2','2025-10-02 17:30:00',450000.00,1100000.00,'Cerrada'),(3,5,'2025-10-03 08:00:00','Caja 1','2025-10-03 19:00:00',500000.00,1350000.00,'Cerrada'),(4,3,'2025-10-04 08:00:00','Caja 2','2025-10-04 18:00:00',500000.00,980000.00,'Cerrada'),(5,4,'2026-05-28 07:30:00','Caja Principal',NULL,600000.00,NULL,'Abierta');
/*!40000 ALTER TABLE `caja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categoria`
--

DROP TABLE IF EXISTS `categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria` (
  `idCategoria` int(11) NOT NULL AUTO_INCREMENT,
  `NombreCategoria` varchar(60) NOT NULL,
  `Descripcion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`idCategoria`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria`
--

LOCK TABLES `categoria` WRITE;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
INSERT INTO `categoria` VALUES (1,'Ron','Ron blanco, añejo y premium'),(2,'Whisky','Whisky escocés, bourbon y blends'),(3,'Vino','Vinos tintos, blancos y espumantes'),(4,'Cerveza','Nacionales, artesanales e importadas'),(5,'Vodka','Vodka premium y neutro'),(6,'Tequila','Tequila blanco, reposado y añejo'),(7,'Licor de Crema','Baileys y derivados'),(8,'Aguardiente','Aguardiente antioqueño y del valle'),(9,'Pisco','Pisco peruano y chileno'),(10,'Champán','Champán y vinos espumantes de lujo');
/*!40000 ALTER TABLE `categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detallepedido`
--

DROP TABLE IF EXISTS `detallepedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detallepedido` (
  `idDetallePedido` int(11) NOT NULL AUTO_INCREMENT,
  `idProducto` int(11) NOT NULL,
  `idPedido` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `PrecioUnitarioCompra` decimal(10,2) DEFAULT NULL,
  `Subtotal` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`idDetallePedido`),
  KEY `fk_DetallePedido_Producto` (`idProducto`),
  KEY `fk_DetallePedido_Pedido` (`idPedido`),
  CONSTRAINT `fk_DetallePedido_Pedido` FOREIGN KEY (`idPedido`) REFERENCES `pedido` (`idPedido`),
  CONSTRAINT `fk_DetallePedido_Producto` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detallepedido`
--

LOCK TABLES `detallepedido` WRITE;
/*!40000 ALTER TABLE `detallepedido` DISABLE KEYS */;
INSERT INTO `detallepedido` VALUES (1,1,1,50,28000.00,1400000.00),(2,12,1,200,3500.00,700000.00),(3,5,2,30,58000.00,1740000.00),(4,16,2,40,32000.00,1280000.00),(5,9,3,60,18000.00,1080000.00),(6,24,3,150,12000.00,1800000.00),(7,13,4,150,4500.00,675000.00),(8,22,4,25,45000.00,1125000.00),(9,2,5,20,65000.00,1300000.00),(10,6,5,20,62000.00,1240000.00),(11,19,6,30,30000.00,900000.00),(12,29,6,5,180000.00,900000.00),(13,7,7,15,85000.00,1275000.00),(14,17,7,40,25000.00,1000000.00),(15,20,8,10,95000.00,950000.00),(16,34,8,5,195000.00,975000.00),(17,33,9,80,5000.00,400000.00),(18,25,9,50,14000.00,700000.00),(19,35,10,10,160000.00,1600000.00),(20,28,10,10,55000.00,550000.00),(21,1,11,40,28000.00,1120000.00),(22,5,11,20,58000.00,1160000.00),(23,20,12,15,95000.00,1425000.00),(24,34,12,8,195000.00,1560000.00);
/*!40000 ALTER TABLE `detallepedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalleventa`
--

DROP TABLE IF EXISTS `detalleventa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalleventa` (
  `idDetalleVenta` int(11) NOT NULL AUTO_INCREMENT,
  `idVentas` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `PrecioUnitario` decimal(10,2) DEFAULT NULL,
  `Subtotal` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`idDetalleVenta`),
  KEY `fk_DetalleVenta_Ventas` (`idVentas`),
  KEY `fk_DetalleVenta_Producto` (`idProducto`),
  CONSTRAINT `fk_DetalleVenta_Producto` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`idProducto`),
  CONSTRAINT `fk_DetalleVenta_Ventas` FOREIGN KEY (`idVentas`) REFERENCES `ventas` (`idVentas`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalleventa`
--

LOCK TABLES `detalleventa` WRITE;
/*!40000 ALTER TABLE `detalleventa` DISABLE KEYS */;
INSERT INTO `detalleventa` VALUES (1,1,1,1,45000.00,45000.00),(2,2,5,1,95000.00,95000.00),(3,3,12,1,6000.00,6000.00),(4,4,2,1,110000.00,110000.00),(5,5,16,1,35000.00,35000.00),(6,6,9,1,32000.00,32000.00),(7,6,12,1,6000.00,6000.00),(8,6,13,1,7500.00,7500.00),(9,7,22,1,75000.00,75000.00),(10,8,8,1,155000.00,155000.00),(11,9,19,1,28000.00,28000.00),(12,10,24,1,20000.00,20000.00),(13,10,25,1,23000.00,23000.00),(14,10,12,1,6000.00,6000.00),(15,10,13,1,7500.00,7500.00),(16,11,12,2,6000.00,12000.00),(17,11,13,1,7500.00,7500.00),(18,12,6,1,105000.00,105000.00),(19,13,23,1,48000.00,48000.00),(20,14,9,1,32000.00,32000.00),(21,15,20,1,90000.00,90000.00),(22,16,13,1,7000.00,7000.00),(23,17,17,1,42000.00,42000.00),(24,17,12,1,6000.00,6000.00),(25,18,35,1,125000.00,125000.00),(26,19,12,1,6000.00,6000.00),(27,20,7,1,85000.00,85000.00),(28,21,10,1,42000.00,42000.00),(29,22,2,1,110000.00,110000.00),(30,23,16,1,28000.00,28000.00),(31,24,22,1,75000.00,75000.00),(32,25,28,1,55000.00,55000.00),(33,26,12,1,6000.00,6000.00),(34,26,13,1,7500.00,7500.00),(35,27,5,1,95000.00,95000.00),(36,28,24,1,20000.00,20000.00),(37,28,25,1,23000.00,23000.00),(38,29,21,1,65000.00,65000.00),(39,30,19,1,20000.00,20000.00),(40,31,34,1,140000.00,140000.00),(41,32,33,1,8500.00,8500.00),(42,33,17,1,50000.00,50000.00),(43,34,2,1,110000.00,110000.00),(44,35,10,1,42000.00,42000.00),(45,36,22,1,75000.00,75000.00),(46,37,16,1,35000.00,35000.00),(47,38,6,1,90000.00,90000.00),(48,39,12,1,6000.00,6000.00),(49,39,13,1,7500.00,7500.00),(50,40,28,1,55000.00,55000.00),(51,41,35,1,125000.00,125000.00),(52,42,23,1,48000.00,48000.00),(53,43,19,1,28000.00,28000.00),(54,44,7,1,85000.00,85000.00),(55,45,13,1,7000.00,7000.00),(56,46,21,1,60000.00,60000.00),(57,47,10,1,42000.00,42000.00),(58,48,5,1,95000.00,95000.00),(59,49,24,1,20000.00,20000.00),(60,49,25,1,23000.00,23000.00),(61,50,8,1,110000.00,110000.00),(62,51,19,1,20000.00,20000.00),(63,52,22,1,75000.00,75000.00),(64,53,28,1,55000.00,55000.00),(65,54,12,1,6000.00,6000.00),(66,54,13,1,7500.00,7500.00),(67,55,6,1,90000.00,90000.00);
/*!40000 ALTER TABLE `detalleventa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logsauditoria`
--

DROP TABLE IF EXISTS `logsauditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logsauditoria` (
  `idLogsAuditoria` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) NOT NULL,
  `Accion` varchar(100) DEFAULT NULL,
  `Modulo` varchar(50) DEFAULT NULL,
  `Detalles` varchar(45) DEFAULT NULL,
  `FechaHora` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`idLogsAuditoria`),
  KEY `fk_LogsAuditoria_Usuario` (`idUsuario`),
  CONSTRAINT `fk_LogsAuditoria_Usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logsauditoria`
--

LOCK TABLES `logsauditoria` WRITE;
/*!40000 ALTER TABLE `logsauditoria` DISABLE KEYS */;
INSERT INTO `logsauditoria` VALUES (1,1,'Login exitoso','Seguridad','Inicio de sesión admin','2025-10-01 08:00:00'),(2,3,'Login exitoso','Seguridad','Inicio de sesión vendedor','2025-10-01 08:15:00'),(3,4,'Login exitoso','Seguridad','Inicio de sesión vendedor','2025-10-01 08:20:00'),(4,6,'Login exitoso','Seguridad','Inicio de sesión bodega','2025-10-01 08:30:00'),(5,1,'Creación de usuario','Usuarios','Usuario Maria López creado','2025-09-25 10:00:00'),(6,6,'Recepción de pedido','Pedidos','Pedido #1 recibido parcialmente','2025-09-12 14:00:00'),(7,3,'Venta registrada','Ventas','Factura FAC-20251001-001 generada','2025-10-01 09:15:00'),(8,1,'Login Exitoso','Seguridad','Ingreso al sistema','2026-05-28 17:05:23'),(9,1,'Login Exitoso','Seguridad','Ingreso al sistema','2026-05-28 17:07:46'),(10,11,'Login Exitoso','Seguridad','Ingreso al sistema','2026-05-28 17:12:48'),(11,12,'Login Exitoso','Seguridad','Ingreso al sistema','2026-05-28 17:13:48'),(12,11,'Login Exitoso','Seguridad','Ingreso al sistema','2026-05-28 17:13:54'),(13,11,'Login Exitoso','Seguridad','Ingreso al sistema','2026-05-28 17:14:20'),(14,1,'Login Exitoso','Seguridad','Ingreso al sistema','2026-05-28 17:21:32'),(15,11,'Login Exitoso','Seguridad','Ingreso al sistema','2026-06-09 20:06:16');
/*!40000 ALTER TABLE `logsauditoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido`
--

DROP TABLE IF EXISTS `pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido` (
  `idPedido` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) NOT NULL,
  `idProveedor` int(11) NOT NULL,
  `FechaPedido` datetime DEFAULT current_timestamp(),
  `NumeroFacturaProveedor` varchar(50) DEFAULT NULL,
  `TotalPedido` decimal(10,2) DEFAULT NULL,
  `Estado` enum('Pendiente','Recibido','Cancelado') DEFAULT 'Pendiente',
  PRIMARY KEY (`idPedido`),
  KEY `fk_Pedido_Usuario` (`idUsuario`),
  KEY `fk_Pedido_Proveedor` (`idProveedor`),
  CONSTRAINT `fk_Pedido_Proveedor` FOREIGN KEY (`idProveedor`) REFERENCES `proveedor` (`idProveedor`),
  CONSTRAINT `fk_Pedido_Usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
INSERT INTO `pedido` VALUES (1,6,1,'2025-09-10 10:00:00','FP-001',1250000.00,'Recibido'),(2,6,2,'2025-09-15 11:30:00','FP-002',890000.00,'Recibido'),(3,8,3,'2025-09-20 09:00:00','FP-003',2100000.00,'Recibido'),(4,6,5,'2025-10-05 14:00:00','FP-004',750000.00,'Recibido'),(5,8,1,'2025-10-12 10:00:00','FP-005',1500000.00,'Recibido'),(6,6,4,'2025-10-20 16:00:00','FP-006',980000.00,'Recibido'),(7,8,2,'2025-11-01 11:00:00','FP-007',1100000.00,'Recibido'),(8,6,3,'2025-11-15 09:30:00','FP-008',1800000.00,'Recibido'),(9,8,5,'2025-12-01 10:00:00','FP-009',650000.00,'Recibido'),(10,6,1,'2026-01-10 11:00:00','FP-010',1400000.00,'Recibido'),(11,8,2,'2026-04-15 10:00:00',NULL,1200000.00,'Pendiente'),(12,6,3,'2026-05-20 14:00:00',NULL,2500000.00,'Pendiente');
/*!40000 ALTER TABLE `pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producto` (
  `idProducto` int(11) NOT NULL AUTO_INCREMENT,
  `idCategoria` int(11) NOT NULL,
  `CodigoBarras` varchar(50) DEFAULT NULL,
  `Nombre` varchar(100) NOT NULL,
  `TipoLicor` enum('Ron','Vino','Cerveza','Whisky','Vodka','Tequila','Otro') DEFAULT NULL,
  `PrecioCompra` decimal(10,2) DEFAULT NULL,
  `PrecioVenta` decimal(10,2) DEFAULT NULL,
  `StockActual` int(11) DEFAULT 0,
  `StockMinimo` int(11) DEFAULT 5,
  `FechaVencimiento` date DEFAULT NULL,
  `Estado` enum('Disponible','Agotado') DEFAULT 'Disponible',
  PRIMARY KEY (`idProducto`),
  KEY `fk_Producto_Categoria` (`idCategoria`),
  CONSTRAINT `fk_Producto_Categoria` FOREIGN KEY (`idCategoria`) REFERENCES `categoria` (`idCategoria`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto`
--

LOCK TABLES `producto` WRITE;
/*!40000 ALTER TABLE `producto` DISABLE KEYS */;
INSERT INTO `producto` VALUES (1,1,'7701001','Ron Barceló Añejo 750ml','Ron',28000.00,45000.00,45,10,'2028-01-01','Disponible'),(2,1,'7701002','Ron Santa Teresa 1796','Ron',65000.00,110000.00,12,8,'2029-06-01','Disponible'),(3,1,'7701003','Ron Bacardí Blanco','Ron',22000.00,35000.00,0,15,'2027-12-01','Agotado'),(4,1,'7701004','Ron Havana Club 7 Años','Ron',35000.00,58000.00,22,10,'2028-11-01','Disponible'),(5,2,'7702001','Whisky Old Parr 12 Años','Whisky',58000.00,95000.00,18,5,'2030-01-01','Disponible'),(6,2,'7702002','Whisky Jack Daniels','Whisky',62000.00,105000.00,25,8,'2029-08-01','Disponible'),(7,2,'7702003','Whisky Johnnie Walker Black','Whisky',85000.00,140000.00,8,6,'2030-03-01','Disponible'),(8,2,'7702004','Whisky Chivas Regal 12','Whisky',90000.00,155000.00,3,5,'2029-12-01','Disponible'),(9,3,'7703001','Vino Santa Carolina Reserva','Vino',18000.00,32000.00,30,10,'2026-08-01','Disponible'),(10,3,'7703002','Vino Concha y Toro Casillero','Vino',25000.00,42000.00,15,8,'2027-02-01','Disponible'),(11,3,'7703003','Vino Tinto Argentino Malbec','Vino',20000.00,35000.00,0,12,'2026-05-01','Agotado'),(12,4,'7704001','Cerveza Corona 355ml','Cerveza',3500.00,6000.00,200,50,'2025-12-01','Disponible'),(13,4,'7704002','Cerveza Heineken 330ml','Cerveza',4500.00,7500.00,120,40,'2025-11-01','Disponible'),(14,4,'7704003','Cerveza Stella Artois','Cerveza',4000.00,7000.00,0,30,'2025-10-01','Agotado'),(15,4,'7704004','Cerveza Artesanal IPA','Cerveza',8000.00,14000.00,45,15,'2026-03-01','Disponible'),(16,5,'7705001','Vodka Absolut Original','Vodka',32000.00,55000.00,28,10,'2029-01-01','Disponible'),(17,5,'7705002','Vodka Smirnoff Red','Vodka',25000.00,42000.00,35,12,'2028-07-01','Disponible'),(18,5,'7705003','Vodka Grey Goose','Vodka',75000.00,125000.00,6,5,'2029-11-01','Disponible'),(19,6,'7706001','Tequila José Cuervo Especial','Tequila',30000.00,50000.00,18,8,'2028-09-01','Disponible'),(20,6,'7706002','Tequila Don Julio Blanco','Tequila',95000.00,160000.00,9,6,'2030-02-01','Disponible'),(21,6,'7706003','Tequila Herradura Reposado','Tequila',80000.00,135000.00,4,5,'2029-05-01','Disponible'),(22,7,'7707001','Baileys Original 750ml','',45000.00,75000.00,22,8,'2027-04-01','Disponible'),(23,7,'7707002','Carolans Irish Cream','',28000.00,48000.00,15,6,'2026-12-01','Disponible'),(24,8,'7708001','Aguardiente Antioqueño Verde','',12000.00,20000.00,150,40,'2027-01-01','Disponible'),(25,8,'7708002','Aguardiente Cristal','',14000.00,23000.00,80,30,'2027-06-01','Disponible'),(26,8,'7708003','Aguardiente Néctar','',13000.00,22000.00,0,25,'2026-09-01','Agotado'),(27,9,'7709001','Pisco Quebranta 800ml','',35000.00,60000.00,12,8,'2029-03-01','Disponible'),(28,9,'7709002','Pisco Macchu 1605','',55000.00,90000.00,7,5,'2029-08-01','Disponible'),(29,10,'77010001','Champán Moët & Chandon','',180000.00,310000.00,8,4,'2028-12-01','Disponible'),(30,10,'77010002','Champán Veuve Clicquot','',210000.00,360000.00,3,3,'2029-04-01','Disponible'),(31,10,'77010003','Espumante Freixenet Cordon','',22000.00,38000.00,25,10,'2027-10-01','Disponible'),(32,10,'77010004','Espumante Chandon Brut','',28000.00,48000.00,18,8,'2028-02-01','Disponible'),(33,4,'7704005','Cerveza Modelo Especial','Cerveza',5000.00,8500.00,60,20,'2026-01-01','Disponible'),(34,2,'7702005','Whisky Macallan 12 Sherry','Whisky',195000.00,320000.00,2,2,'2031-01-01','Disponible'),(35,1,'7701005','Ron Zacapa 23 Años','Ron',160000.00,265000.00,5,3,'2030-06-01','Disponible');
/*!40000 ALTER TABLE `producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedor`
--

DROP TABLE IF EXISTS `proveedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedor` (
  `idProveedor` int(11) NOT NULL AUTO_INCREMENT,
  `RazonSocial` varchar(100) NOT NULL,
  `Nit` varchar(45) NOT NULL,
  `Telefono` varchar(45) DEFAULT NULL,
  `Contacto` varchar(100) DEFAULT NULL,
  `Estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  PRIMARY KEY (`idProveedor`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedor`
--

LOCK TABLES `proveedor` WRITE;
/*!40000 ALTER TABLE `proveedor` DISABLE KEYS */;
INSERT INTO `proveedor` VALUES (1,'Distribuidora Andina S.A.S','900111222-1','3001112233','Pedro Gómez','Activo'),(2,'Licores del Caribe Ltda','900333444-3','3003334455','Ana Martínez','Activo'),(3,'Importadora Global Bebidas','900555666-5','3005556677','Carlos Ruiz','Activo'),(4,'Casa Vitivinícola del Sur','900777888-7','3007778899','Laura Sánchez','Activo'),(5,'Cervecería Nacional','900999000-9','3009990011','Jorge Pérez','Activo'),(6,'Distribuidora Premium','900222333-2','3002223344','Miguel Torres','Inactivo');
/*!40000 ALTER TABLE `proveedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `idRoles` int(11) NOT NULL AUTO_INCREMENT,
  `NombreRol` varchar(45) NOT NULL,
  `Descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idRoles`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador','Control total del sistema'),(2,'Vendedor','Realiza ventas y consultas'),(3,'Almacenista','Gestión de inventario y pedidos');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL AUTO_INCREMENT,
  `idRoles` int(11) NOT NULL,
  `Nombre` varchar(60) NOT NULL,
  `Apellido` varchar(60) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  PRIMARY KEY (`idUsuario`),
  UNIQUE KEY `Email` (`Email`),
  KEY `fk_Usuario_Roles` (`idRoles`),
  CONSTRAINT `fk_Usuario_Roles` FOREIGN KEY (`idRoles`) REFERENCES `roles` (`idRoles`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,1,'Juan','Pérez','admin@luxor.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','Activo'),(2,1,'María','López','maria.admin@luxor.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','Activo'),(3,2,'Carlos','Ramírez','carlos.vend1@luxor.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','Activo'),(4,2,'Lucía','Fernández','lucia.vend2@luxor.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','Activo'),(5,2,'Andrés','Castillo','andres.vend3@luxor.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','Activo'),(6,3,'Diana','Morales','diana.bodega@luxor.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','Activo'),(7,2,'Felipe','Guzmán','felipe.vend4@luxor.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','Activo'),(8,3,'Valentina','Ríos','valentina.bodega2@luxor.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','Activo'),(9,1,'Roberto','Soto','roberto.gerente@luxor.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','Activo'),(10,2,'Camila','Vargas','camila.vend5@luxor.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','Activo'),(11,2,'Maria','Gómez','vendedor@luxor.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','Activo'),(12,3,'Carlos','López','bodega@luxor.com','03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4','Activo');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ventas` (
  `idVentas` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) NOT NULL,
  `idCaja` int(11) NOT NULL,
  `NumeroFactura` varchar(50) NOT NULL,
  `FechaVenta` datetime DEFAULT current_timestamp(),
  `Total` decimal(10,2) DEFAULT NULL,
  `Subtotal` decimal(10,2) DEFAULT NULL,
  `MetodoPago` varchar(20) DEFAULT NULL,
  `Estado` enum('Completada','Anulada') DEFAULT 'Completada',
  PRIMARY KEY (`idVentas`),
  KEY `fk_Ventas_Usuario` (`idUsuario`),
  KEY `fk_Ventas_Caja` (`idCaja`),
  CONSTRAINT `fk_Ventas_Caja` FOREIGN KEY (`idCaja`) REFERENCES `caja` (`idCaja`),
  CONSTRAINT `fk_Ventas_Usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (1,3,1,'FAC-20251001-001','2025-10-01 09:15:00',45000.00,45000.00,'Efectivo','Completada'),(2,4,1,'FAC-20251001-002','2025-10-01 10:30:00',95000.00,95000.00,'Tarjeta','Completada'),(3,5,2,'FAC-20251001-003','2025-10-01 11:45:00',6000.00,6000.00,'Efectivo','Completada'),(4,3,1,'FAC-20251001-004','2025-10-01 14:00:00',110000.00,110000.00,'Tarjeta','Completada'),(5,4,2,'FAC-20251001-005','2025-10-01 16:20:00',35000.00,35000.00,'Efectivo','Completada'),(6,5,1,'FAC-20251002-001','2025-10-02 08:30:00',42000.00,42000.00,'Transferencia','Completada'),(7,3,1,'FAC-20251002-002','2025-10-02 10:15:00',75000.00,75000.00,'Efectivo','Completada'),(8,4,2,'FAC-20251002-003','2025-10-02 12:00:00',155000.00,155000.00,'Tarjeta','Completada'),(9,5,1,'FAC-20251002-004','2025-10-02 15:45:00',28000.00,28000.00,'Efectivo','Completada'),(10,3,2,'FAC-20251003-001','2025-10-03 09:00:00',58000.00,58000.00,'Tarjeta','Completada'),(11,4,2,'FAC-20251003-002','2025-10-03 11:30:00',14000.00,14000.00,'Efectivo','Completada'),(12,5,1,'FAC-20251003-003','2025-10-03 13:15:00',105000.00,105000.00,'Tarjeta','Completada'),(13,3,1,'FAC-20251003-004','2025-10-03 16:00:00',48000.00,48000.00,'Efectivo','Completada'),(14,4,2,'FAC-20251004-001','2025-10-04 08:45:00',32000.00,32000.00,'Efectivo','Completada'),(15,5,1,'FAC-20251004-002','2025-10-04 10:30:00',90000.00,90000.00,'Tarjeta','Completada'),(16,3,1,'FAC-20251004-003','2025-10-04 14:00:00',7000.00,7000.00,'Efectivo','Completada'),(17,4,2,'FAC-20251004-004','2025-10-04 16:30:00',50000.00,50000.00,'Tarjeta','Completada'),(18,5,1,'FAC-20251005-001','2025-10-05 09:15:00',125000.00,125000.00,'Efectivo','Completada'),(19,3,1,'FAC-20251005-002','2025-10-05 11:00:00',6000.00,6000.00,'Efectivo','Completada'),(20,4,2,'FAC-20251005-003','2025-10-05 13:45:00',85000.00,85000.00,'Tarjeta','Completada'),(21,5,1,'FAC-20251006-001','2025-10-06 08:30:00',42000.00,42000.00,'Transferencia','Completada'),(22,3,1,'FAC-20251006-002','2025-10-06 10:15:00',110000.00,110000.00,'Efectivo','Completada'),(23,4,2,'FAC-20251006-003','2025-10-06 12:00:00',28000.00,28000.00,'Tarjeta','Completada'),(24,5,1,'FAC-20251006-004','2025-10-06 15:30:00',75000.00,75000.00,'Efectivo','Completada'),(25,3,1,'FAC-20251007-001','2025-10-07 09:00:00',55000.00,55000.00,'Tarjeta','Completada'),(26,4,2,'FAC-20251007-002','2025-10-07 11:30:00',15000.00,15000.00,'Efectivo','Completada'),(27,5,1,'FAC-20251007-003','2025-10-07 14:00:00',95000.00,95000.00,'Tarjeta','Completada'),(28,3,1,'FAC-20251007-004','2025-10-07 16:45:00',38000.00,38000.00,'Efectivo','Completada'),(29,4,2,'FAC-20251008-001','2025-10-08 08:15:00',65000.00,65000.00,'Tarjeta','Completada'),(30,5,1,'FAC-20251008-002','2025-10-08 10:00:00',20000.00,20000.00,'Efectivo','Completada'),(31,3,1,'FAC-20251008-003','2025-10-08 13:30:00',140000.00,140000.00,'Tarjeta','Completada'),(32,4,2,'FAC-20251008-004','2025-10-08 15:00:00',8500.00,8500.00,'Efectivo','Completada'),(33,5,1,'FAC-20251009-001','2025-10-09 09:30:00',50000.00,50000.00,'Efectivo','Completada'),(34,3,1,'FAC-20251009-002','2025-10-09 11:15:00',110000.00,110000.00,'Tarjeta','Completada'),(35,4,2,'FAC-20251009-003','2025-10-09 14:00:00',42000.00,42000.00,'Efectivo','Completada'),(36,5,1,'FAC-20251009-004','2025-10-09 16:30:00',75000.00,75000.00,'Tarjeta','Completada'),(37,3,1,'FAC-20251010-001','2025-10-10 08:45:00',35000.00,35000.00,'Efectivo','Completada'),(38,4,2,'FAC-20251010-002','2025-10-10 10:30:00',90000.00,90000.00,'Tarjeta','Completada'),(39,5,1,'FAC-20251010-003','2025-10-10 13:00:00',15000.00,15000.00,'Efectivo','Completada'),(40,3,1,'FAC-20251010-004','2025-10-10 15:45:00',55000.00,55000.00,'Tarjeta','Completada'),(41,4,2,'FAC-20251011-001','2025-10-11 09:00:00',125000.00,125000.00,'Efectivo','Completada'),(42,5,1,'FAC-20251011-002','2025-10-11 11:30:00',48000.00,48000.00,'Tarjeta','Completada'),(43,3,1,'FAC-20251011-003','2025-10-11 14:00:00',28000.00,28000.00,'Efectivo','Completada'),(44,4,2,'FAC-20251011-004','2025-10-11 16:00:00',85000.00,85000.00,'Tarjeta','Completada'),(45,5,1,'FAC-20251012-001','2025-10-12 08:30:00',7000.00,7000.00,'Efectivo','Completada'),(46,3,1,'FAC-20251012-002','2025-10-12 10:15:00',60000.00,60000.00,'Tarjeta','Completada'),(47,4,2,'FAC-20251012-003','2025-10-12 13:00:00',42000.00,42000.00,'Efectivo','Completada'),(48,5,1,'FAC-20251012-004','2025-10-12 15:30:00',95000.00,95000.00,'Tarjeta','Completada'),(49,3,1,'FAC-20251013-001','2025-10-13 09:15:00',38000.00,38000.00,'Efectivo','Completada'),(50,4,2,'FAC-20251013-002','2025-10-13 11:45:00',110000.00,110000.00,'Tarjeta','Completada'),(51,5,1,'FAC-20251013-003','2025-10-13 14:30:00',20000.00,20000.00,'Efectivo','Completada'),(52,3,1,'FAC-20251013-004','2025-10-13 16:00:00',75000.00,75000.00,'Tarjeta','Completada'),(53,4,2,'FAC-20251014-001','2025-10-14 08:45:00',55000.00,55000.00,'Efectivo','Completada'),(54,5,1,'FAC-20251014-002','2025-10-14 11:00:00',15000.00,15000.00,'Tarjeta','Completada'),(55,3,1,'FAC-20251014-003','2025-10-14 14:15:00',90000.00,90000.00,'Efectivo','Completada');
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-09 23:43:52
