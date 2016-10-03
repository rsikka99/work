<?php

use Phinx\Migration\AbstractMigration;

class SellPrice2 extends AbstractMigration
{
    public function up()
    {
        $this->query('alter table devices add sellPrice decimal(10,2) null default null');
    }

    public function down()
    {

    }
}