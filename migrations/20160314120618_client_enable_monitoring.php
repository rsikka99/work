<?php

use Phinx\Migration\AbstractMigration;

class ClientEnableMonitoring extends AbstractMigration
{

    public function up()
    {
        $this->execute('alter table `clients` add `monitoringEnabled` tinyint not null default 1');
    }

    public function down()
    {

    }
}