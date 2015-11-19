<?php

use Phinx\Migration\AbstractMigration;

class Suppliers extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('drop table if EXISTS dealer_suppliers');
        $this->execute('
CREATE TABLE IF NOT EXISTS `dealer_suppliers` (
  `dealerId` int(11) NOT NULL,
  `supplierId` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `pass` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`dealerId`,`supplierId`),
  KEY `supplierId` (`supplierId`)
) ENGINE=InnoDB;');

        $this->execute('drop table if EXISTS suppliers');
        $this->execute('
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB');

        $this->execute("insert into suppliers set id=1, name='Ingram Micro'");

        $this->execute('ALTER TABLE `dealer_suppliers`
  ADD CONSTRAINT `dealer_suppliers_ibfk_2` FOREIGN KEY (`supplierId`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dealer_suppliers_ibfk_1` FOREIGN KEY (`dealerId`) REFERENCES `dealers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;');

        $this->execute('alter table toners add `weight` decimal(10,2) null default null');
        $this->execute('alter table toners add `UPC` varchar(255) null default null');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('drop table if EXISTS dealer_suppliers');
        $this->execute('drop table if EXISTS suppliers');

        $this->execute('alter table toners drop `weight`');
        $this->execute('alter table toners drop `UPC`');
    }
}
