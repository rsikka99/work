<?php

use Phinx\Migration\AbstractMigration;

class TonerPriceLevels extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("alter table dealer_toner_attributes add `level1` decimal(10,2) null default null");
        $this->execute("alter table dealer_toner_attributes add `level2` decimal(10,2) null default null");
        $this->execute("alter table dealer_toner_attributes add `level3` decimal(10,2) null default null");
        $this->execute("alter table dealer_toner_attributes add `level4` decimal(10,2) null default null");
        $this->execute("alter table dealer_toner_attributes add `level5` decimal(10,2) null default null");

        $this->execute("alter table fleet_settings add `level` varchar(10) null default null");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("alter table dealer_toner_attributes drop `level1`");
        $this->execute("alter table dealer_toner_attributes drop `level2`");
        $this->execute("alter table dealer_toner_attributes drop `level3`");
        $this->execute("alter table dealer_toner_attributes drop `level4`");
        $this->execute("alter table dealer_toner_attributes drop `level5`");

        $this->execute("alter table fleet_settings drop `level`");
    }
}