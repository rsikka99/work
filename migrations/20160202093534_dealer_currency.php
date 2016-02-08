<?php

use Phinx\Migration\AbstractMigration;

class DealerCurrency extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("alter table dealers add `currency` char(3) not null default 'USD'");

        $this->execute('DROP TABLE IF EXISTS `currency_exchange`');
        $this->execute('
CREATE TABLE IF NOT EXISTS `currency_exchange` (
  `currency` char(3) not null,
  `rate` decimal(10,5),
  `dt` timestamp NOT NULL default current_timestamp,
  PRIMARY KEY (`currency`)
) ENGINE=InnoDB');

        $this->execute('DROP TABLE IF EXISTS `currency_value`');
        $this->execute('
CREATE TABLE IF NOT EXISTS `currency_value` (
  `id` int not null,
  `table` varchar(128) not null,
  `field` varchar(128) not null,
  `currency` char(3) not null,
  `value` decimal(10,2) null,
  PRIMARY KEY (`id`,`table`,`field`,`currency`)
) ENGINE=InnoDB');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('DROP TABLE IF EXISTS `currency_exchange`');
        $this->execute('DROP TABLE IF EXISTS `currency_value`');
    }
}