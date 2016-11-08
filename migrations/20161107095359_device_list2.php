<?php

use Phinx\Migration\AbstractMigration;

class DeviceList2 extends AbstractMigration
{

    public function up()
    {
        $this->query('drop VIEW `device_list`');
        $this->query(
<<<SQL
CREATE VIEW `device_list`  AS
select `vw_dt`.`toner_id` AS `toner_id`,
group_concat(concat('{"id":',`vw_md`.`id`,',"name":"',convert(`vw_mf`.`displayname` using utf8),' ',`vw_md`.`modelName`,'"}') separator ', ') AS `json`,
group_concat(concat(convert(`vw_mf`.`displayname` using utf8),' ',`vw_md`.`modelName`) separator ', ') AS `devices`
from ((`device_toners` `vw_dt` join `master_devices` `vw_md`) join `manufacturers` `vw_mf`) where ((`vw_dt`.`master_device_id` = `vw_md`.`id`) and (`vw_md`.`manufacturerId` = `vw_mf`.`id`)) group by `vw_dt`.`toner_id`
SQL
);

        $this->query("update currency_value set `table`='base_printer_consumable' where `table`='toners'");

    }

    public function down()
    {

    }
}