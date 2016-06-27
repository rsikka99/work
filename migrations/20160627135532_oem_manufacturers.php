<?php

use Phinx\Migration\AbstractMigration;

class OemManufacturers extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        try {
            $this->execute('drop view oem_manufacturers');
            $this->execute('drop view device_list');
        } catch (Exception $ex) {}

        $this->execute(
"
create view `oem_manufacturers` as
select
	manufacturerId
from
	base_product
where
	base_type='printer'
group by manufacturerId
");

        $this->execute(
"
create view device_list as
SELECT
	toner_id,
	GROUP_CONCAT( CONCAT( displayname, ' ', modelName ) SEPARATOR ', ' ) as devices
FROM
	device_toners vw_dt,
    master_devices vw_md,
    manufacturers vw_mf
where
	vw_dt.master_device_id = vw_md.id
    and vw_md.manufacturerId = vw_mf.id
GROUP BY toner_id
");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}