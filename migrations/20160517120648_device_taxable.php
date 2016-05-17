<?php

use Phinx\Migration\AbstractMigration;

class DeviceTaxable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
            $this->execute('alter table devices add `taxable` tinyint not null default 1');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}