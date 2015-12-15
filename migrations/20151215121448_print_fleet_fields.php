<?php

use Phinx\Migration\AbstractMigration;

class PrintFleetFields extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('alter table clients add industry varchar(255) null');
        $this->execute('alter table contacts add website varchar(255) null, add emailSupply varchar(255) null');
        $this->execute('ALTER TABLE `addresses` CHANGE `countryId` `countryId` INT( 11 ) NULL ');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('alter table clients drop industry');
        $this->execute('alter table contacts drop website, drop emailSupply');
    }
}