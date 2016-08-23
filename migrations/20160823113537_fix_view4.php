<?php

use Phinx\Migration\AbstractMigration;

class FixView4 extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        try {
            $this->execute('drop view if exists _view_computer_dist_stock_price');
            $this->execute('drop view if exists _view_computer_dist_stock_price_in_stock');
            $this->execute('drop view if exists _view_computer_dist_stock_price_not_in_stock');
            $this->execute('drop view if exists _view_computer_dist_stock_price_ordered');
            $this->execute('drop view if exists _view_computer_dist_stock_price_grouped');

            $this->execute('drop view if exists _view_peripheral_dist_stock_price');
            $this->execute('drop view if exists _view_peripheral_dist_stock_price_in_stock');
            $this->execute('drop view if exists _view_peripheral_dist_stock_price_not_in_stock');
            $this->execute('drop view if exists _view_peripheral_dist_stock_price_ordered');
            $this->execute('drop view if exists _view_peripheral_dist_stock_price_grouped');
        } catch (\Exception $ex) {

        }


        $this->execute('drop view if exists _view_dist_stock_price');
        $this->execute('
create view _view_dist_stock_price as
select 1 as dist, dealer_toner_attributes.tonerId, null as stock, dealer_toner_attributes.dealerId, dealer_toner_attributes.cost
    from
      dealer_toner_attributes
    where cost is not null
union
select 1+supplierId as dist, baseProductId as tonerId, isStock as stock, dealerId, price as cost
    from
      supplier_product
      join supplier_price using (supplierId, supplierSku)
    where baseProductId is not null
');

        $this->execute('drop view if exists _view_dist_stock_price_in_stock');
        $this->execute("
create view _view_dist_stock_price_in_stock as
select * from _view_dist_stock_price where stock>0 order by cost
");
        $this->execute('drop view if exists _view_dist_stock_price_not_in_stock');
        $this->execute("
create view _view_dist_stock_price_not_in_stock as
select * from _view_dist_stock_price where stock=0 order by cost
");

        #--

        $this->execute('drop view if exists _view_device_dist_stock_price');
        $this->execute('
create view _view_device_dist_stock_price as
select 1 as dist, masterDeviceId, 1 as stock, dealerId, cost
    from
      devices
    where cost>0
union
select 1+supplierId as dist, baseProductId as masterDeviceId, isStock as stock, dealerId, price as cost
    from
      supplier_product
      join supplier_price using (supplierId, supplierSku)
    where baseProductId is not null
');

        $this->execute('drop view if exists _view_device_dist_stock_price_in_stock');
        $this->execute("
create view _view_device_dist_stock_price_in_stock as
select * from _view_device_dist_stock_price where stock>0 order by cost
");
        $this->execute('drop view if exists _view_device_dist_stock_price_not_in_stock');
        $this->execute("
create view _view_device_dist_stock_price_not_in_stock as
select * from _view_device_dist_stock_price where stock=0 order by cost
");


    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}