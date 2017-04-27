--
-- Copyright (C) 2017 Luis Cortes Juarez
--
-- An open source application development framework for PHP
--
-- This content is released under the MIT License (MIT)
--
-- Copyright (c) 2017, DevFy
--
-- Permission is hereby granted, free of charge, to any person obtaining a copy
-- of this software and associated documentation files (the "Software"), to deal
-- in the Software without restriction, including without limitation the rights
-- to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
-- copies of the Software, and to permit persons to whom the Software is
-- furnished to do so, subject to the following conditions:
--
-- The above copyright notice and this permission notice shall be included in
-- all copies or substantial portions of the Software.
--
-- THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
-- IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
-- FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
-- AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
-- LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
-- OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
-- THE SOFTWARE.
--
-- @package	DevfyFramework
-- @author	Luis Cortes | DevFy
-- @copyright	Copyright (c) 2017, DevFy. (http://www.devfy.net/)
-- @license	http://opensource.org/licenses/MIT	MIT License
-- @link	http://www.devfy.net
-- @since	Version 1.0.0
-- @filesource
--


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `avatar` */

DROP TABLE IF EXISTS `avatar`;

CREATE TABLE `avatar` (
  `id` TINYINT(4) NOT NULL AUTO_INCREMENT,
  `avatar` VARCHAR(10) DEFAULT 'avatar.png',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

/*Data for the table `avatar` */

LOCK TABLES `avatar` WRITE;

UNLOCK TABLES;

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id_roles` INT(11) NOT NULL,
  `rol` VARCHAR(45) DEFAULT NULL,
  PRIMARY KEY (`id_roles`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

/*Data for the table `roles` */

LOCK TABLES `roles` WRITE;

INSERT  INTO `roles`(`id_roles`,`rol`) VALUES (0,'Administrador'),(1,'Gerente Restaurante'),(2,'Cocinero'),(3,'Salonero'),(4,'Cliente');

UNLOCK TABLES;

/*Table structure for table `usuario` */

DROP TABLE IF EXISTS `usuario`;

CREATE TABLE `usuario` (
  `cedula` CHAR(15) NOT NULL,
  `usuario` VARCHAR(20) DEFAULT NULL,
  `clave` VARCHAR(88) DEFAULT NULL,
  `id_roles` INT(11) DEFAULT NULL,
  `inactivo` BIT(1) DEFAULT b'0',
  PRIMARY KEY (`cedula`),
  UNIQUE KEY `UNIQUE` (`usuario`),
  KEY `fk_rol` (`id_roles`),
  CONSTRAINT `fk_cedula` FOREIGN KEY (`cedula`) REFERENCES `usuario_datos` (`cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_roles` FOREIGN KEY (`id_roles`) REFERENCES `roles` (`id_roles`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=INNODB DEFAULT CHARSET=utf8;

/*Data for the table `usuario` */

LOCK TABLES `usuario` WRITE;

UNLOCK TABLES;

/*Table structure for table `usuario_avatar` */

DROP TABLE IF EXISTS `usuario_avatar`;

CREATE TABLE `usuario_avatar` (
  `cedula` CHAR(15) NOT NULL,
  `id_avatar` TINYINT(2) DEFAULT '1',
  PRIMARY KEY (`cedula`),
  KEY `fk_avatar` (`id_avatar`),
  CONSTRAINT `fk_avatar` FOREIGN KEY (`id_avatar`) REFERENCES `avatar` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cedula_avatar` FOREIGN KEY (`cedula`) REFERENCES `usuario_datos` (`cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=INNODB DEFAULT CHARSET=utf8;

/*Data for the table `usuario_avatar` */

LOCK TABLES `usuario_avatar` WRITE;

UNLOCK TABLES;

/*Table structure for table `usuario_datos` */

DROP TABLE IF EXISTS `usuario_datos`;

CREATE TABLE `usuario_datos` (
  `cedula` CHAR(15) NOT NULL,
  `nombre` VARCHAR(30) DEFAULT NULL,
  `apellido` VARCHAR(30) DEFAULT NULL,
  `telefono` CHAR(12) DEFAULT NULL,
  `correo` VARCHAR(30) DEFAULT NULL,
  PRIMARY KEY (`cedula`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

/*Data for the table `usuario_datos` */

LOCK TABLES `usuario_datos` WRITE;

UNLOCK TABLES;

/* Procedure structure for procedure `sp_mostrar_usuarios` */

/*!50003 DROP PROCEDURE IF EXISTS  `sp_mostrar_usuarios` */;

DELIMITER $$

/*!50003 CREATE PROCEDURE `sp_mostrar_usuarios`()
BEGIN
	SELECT
	u.`cedula`,
	u.`usuario`,
	u.`id_roles`,
	ud.`nombre`,
	ud.`apellido`,
	ud.`correo`,
	ud.`telefono`,
	a.`avatar`
	FROM `usuario` AS u 
	INNER JOIN `usuario_datos` AS ud ON ( ud.`cedula` = u.`cedula`)
	INNER JOIN `usuario_avatar` AS ua ON (ua.`cedula` = u.`cedula`)
	INNER JOIN `avatar` AS a ON (a.`id`= ua.`id_avatar`);
    END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
