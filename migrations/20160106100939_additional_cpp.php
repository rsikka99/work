<?php

use Phinx\Migration\AbstractMigration;

class AdditionalCpp extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('alter table devices add additionalCpp decimal(10,2) null');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('alter table devices drop additionalCpp');
    }

}