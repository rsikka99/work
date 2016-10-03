<?php

use Phinx\Migration\AbstractMigration;

class SellPrice extends AbstractMigration
{
    public function up()
    {
        $this->query('alter table dealer_toner_attributes add sellPrice decimal(10,2) null default null');
    }

    public function down()
    {

    }
}