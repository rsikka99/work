<?php

use Phinx\Migration\AbstractMigration;

class RmsGroup extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('ALTER TABLE `shop_settings` ADD `rmsGroup` VARCHAR( 255 ) NULL AFTER `rmsUri`');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('ALTER TABLE `shop_settings` drop `rmsGroup`');
    }
}