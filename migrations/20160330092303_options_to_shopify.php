<?php

use Phinx\Migration\AbstractMigration;

class OptionsToShopify extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('alter table `options` add webId bigint null, add purchasePrice decimal(10,2) null, add rentPrice decimal(10,2) null, add planPrice decimal(10,2) null');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('alter table `options` drop webId, drop purchasePrice, drop rentPrice, drop planPrice');
    }
}