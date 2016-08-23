<?php

use Phinx\Migration\AbstractMigration;

class SupplierProduct extends AbstractMigration
{
    public function up()
    {
        $this->query('drop table if exists supplier_price');
        $this->query('drop table if exists supplier_product');

        $this->query('
CREATE TABLE `supplier_product` (
  `supplierId` int(11) NOT NULL,
  `supplierSku` varchar(64) NOT NULL,

  `baseProductId` int(11) DEFAULT NULL,
  `manufacturer` varchar(64) DEFAULT NULL,
  `manufacturerId` int(11) DEFAULT NULL,
  `vpn` varchar(64) NOT NULL,
  `name` varchar(64) default NULL,
  `msrp` decimal(10,2) DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL,
  `length` decimal(10,3) DEFAULT NULL,
  `width` decimal(10,3) DEFAULT NULL,
  `height` decimal(10,3) DEFAULT NULL,
  `upc` varchar(16) DEFAULT NULL,

  `description` text,
  `isStock` tinyint(4) DEFAULT NULL,
  `qty` text,
  `category` varchar(255) DEFAULT NULL,
  `categoryId` varchar(255) DEFAULT NULL,
  `dateCreated` date DEFAULT NULL,
  `_md5` char(32) DEFAULT NULL,
  PRIMARY KEY (`supplierId`,`supplierSku`),
  KEY `manufacturer` (`manufacturer`),
  KEY `manufacturerId` (`manufacturerId`),
  KEY `vpn` (`vpn`),
  KEY `baseProductId` (`baseProductId`),
  KEY `supplierId` (`supplierId`),
  CONSTRAINT FOREIGN KEY (`supplierId`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
        ');

        $this->query('
CREATE TABLE `supplier_price` (
  `supplierId` int(11) NOT NULL,
  `supplierSku` varchar(64) NOT NULL,
  `dealerId` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `promotion` tinyint(4) DEFAULT NULL,
  `_md5` char(32) DEFAULT NULL,
  PRIMARY KEY (`supplierId`,`supplierSku`,`dealerId`),
  KEY `supplierId` (`supplierId`),
  KEY `dealerId` (`dealerId`),
  CONSTRAINT FOREIGN KEY (`supplierId`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT FOREIGN KEY (`dealerId`) REFERENCES `dealers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
        ');

        $this->execute("replace INTO `suppliers` (`id`, `name`) VALUES ('5', 'ACM Technologies')");

    }

    public function down()
    {

    }
}