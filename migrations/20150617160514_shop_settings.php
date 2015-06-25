<?php

use Phinx\Migration\AbstractMigration;

class ShopSettings extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `shop_settings` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `shopifyName` varchar(255) DEFAULT NULL,
          `hardwareMargin` decimal(10,1) DEFAULT NULL,
          `oemTonerMargin` decimal(10,1) DEFAULT NULL,
          `compatibleTonerMargin` decimal(10,1) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB';
        $this->execute($sql);

        $table = $this->table('dealer_settings');
        $table->addColumn('shopSettingsId', 'integer', ['null'=>true])->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('drop table shop_settings');
        $table = $this->table('dealer_settings');
        $table->removeColumn('shopSettingsId')->save();
    }
}