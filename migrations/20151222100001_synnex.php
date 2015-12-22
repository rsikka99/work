<?php

use Phinx\Migration\AbstractMigration;

class Synnex extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("insert into suppliers set id=2, name='Synnex'");
        $this->execute('alter table ext_dealer_hardware drop srp');
        $this->execute('alter table devices drop srp');
        $this->execute('alter table master_devices add weight float null default null, add UPC varchar(255) null default null');
        $this->execute('alter table ext_hardware add weight float null default null, add UPC varchar(255) null default null');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('alter table ext_hardware drop weight, drop UPC');
        $this->execute('alter table master_devices drop weight, drop UPC');
        $this->execute('alter table devices add srp decimal(10,2) null');
        $this->execute('alter table ext_dealer_hardware add srp decimal(10,2) null');
        $this->execute("delete from suppliers where id=2");
    }
}