<?php

use Phinx\Migration\AbstractMigration;

class DeviceListJson extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('drop view device_list');
        $this->execute(
<<<SQL
create view device_list as select
	`vw_dt`.`toner_id` AS `toner_id`,
	group_concat(concat(`vw_mf`.`displayname`,' ',`vw_md`.`modelName`) separator ', ') AS `devices`,
	concat('[',group_concat(concat('{"id":',vw_md.id,',"name":"',`vw_mf`.`displayname`,' ',`vw_md`.`modelName`,'"}') separator ', '),']') AS `json`
from ((`device_toners` `vw_dt` join `master_devices` `vw_md`) join `manufacturers` `vw_mf`)
where ((`vw_dt`.`master_device_id` = `vw_md`.`id`) and (`vw_md`.`manufacturerId` = `vw_mf`.`id`))
group by `vw_dt`.`toner_id`
SQL
        );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}