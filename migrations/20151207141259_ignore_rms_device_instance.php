<?php

use Phinx\Migration\AbstractMigration;

class IgnoreRmsDeviceInstance extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('alter table rms_device_instances add `ignore` tinyint not null default 0');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('alter table rms_device_instances drop `ignore`');
    }
}