<?php

use Phinx\Migration\AbstractMigration;

class IsSelling extends AbstractMigration
{
    public function up()
    {
        $this->query('alter table devices add isSelling tinyint null default 1');
    }

    public function down()
    {

    }
}