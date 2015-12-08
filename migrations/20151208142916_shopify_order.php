<?php

use Phinx\Migration\AbstractMigration;

class ShopifyOrder extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('DROP TABLE IF EXISTS `shopify_orders`');
        $this->execute('CREATE TABLE IF NOT EXISTS `shopify_orders` (
  `id` int(11) NOT NULL,
  `clientId` int(11) NOT NULL,
  `created_at` timestamp NOT NULL,
  `number` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `subtotal_price` decimal(10,2) NOT NULL,
  `line_items` text NOT NULL,
  `customer` text NOT NULL,
  `raw` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB');

        $this->execute('alter table device_needs_toner add `shopify_order` int null,  ADD INDEX ( `shopify_order` ), ADD FOREIGN KEY ( `shopify_order` ) REFERENCES `shopify_orders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('alter table device_needs_toner DROP FOREIGN KEY `device_needs_toner_ibfk_6`, DROP INDEX shopify_order, drop shopify_order');
        $this->execute('DROP TABLE IF EXISTS `shopify_orders`');
    }
}