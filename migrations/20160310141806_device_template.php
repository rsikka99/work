<?php

use Phinx\Migration\AbstractMigration;

class DeviceTemplate extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('alter table rms_device_instances add email_template tinyint not null default 0');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}