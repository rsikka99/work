<?php

use Phinx\Migration\AbstractMigration;

class EmailThresholds extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('alter table shop_settings add thresholdDays int not null default 10, add thresholdPercent int not null default 5');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('alter table shop_settings drop thresholdDays, drop thresholdPercent');
    }
}