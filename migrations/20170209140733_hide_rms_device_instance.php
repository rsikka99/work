<?php

use Phinx\Migration\AbstractMigration;

class HideRmsDeviceInstance extends AbstractMigration
{

    public function up()
    {
        $this->execute('alter table rms_device_instances add `hidden` tinyint not null default 0');
    }

    public function down()
    {

    }
}