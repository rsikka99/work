<?php

use Phinx\Migration\AbstractMigration;

class DealerDeviceToners extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('drop view if exists _view_dealer_device_toner');
        $this->execute('create view _view_dealer_device_toner as
select toner_id, master_device_id, dealerId
    from device_toners
    join base_product p on device_toners.toner_id=p.id
    join _view_dist_stock_price_grouped vw on device_toners.toner_id=vw.tonerId
        where
            (p.manufacturerId not in (select `manufacturerId` from toner_vendor_manufacturers) or p.manufacturerId in (select manufacturerId from dealer_toner_vendors tv where tv.dealerId=vw.dealerId))
');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}