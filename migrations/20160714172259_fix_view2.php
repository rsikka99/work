<?php

use Phinx\Migration\AbstractMigration;

class FixView2 extends AbstractMigration
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

        $this->execute('drop view if exists _view_dist_stock_price');
        $this->execute('
create view _view_dist_stock_price as
select 1 as dist, dealer_toner_attributes.tonerId, null as stock, dealer_toner_attributes.dealerId, dealer_toner_attributes.cost
    from
      dealer_toner_attributes
    where cost is not null
union
select 2 as dist, ingram_products.tonerId, ingram_products.availability_flag as stock, ingram_prices.dealerId, ingram_prices.customer_price as cost
    from
      ingram_products
      join ingram_prices on ingram_products.ingram_part_number = ingram_prices.ingram_part_number
    where ingram_products.tonerId is not null
union
select 3 as dist, synnex_products.tonerId, synnex_products.Qty_on_Hand as stock, synnex_prices.dealerId, synnex_prices.Contract_Price as cost
    from
      synnex_products
      join synnex_prices on synnex_products.SYNNEX_SKU = synnex_prices.SYNNEX_SKU
    where synnex_products.tonerId is not null
union
select 4 as dist, techdata_products.tonerId, techdata_products.Qty as stock, techdata_prices.dealerId, currency_exchange.rate * techdata_prices.CustBestPrice as cost
    from
      techdata_products
      join techdata_prices on techdata_products.Matnr = techdata_prices.Matnr
      join dealers on dealers.id = techdata_prices.dealerId
      join currency_exchange on currency_exchange.currency = dealers.currency
    where techdata_products.tonerId is not null
');
        #--
        $this->execute('drop view if exists _view_device_dist_stock_price');
        $this->execute('
create view _view_device_dist_stock_price as
select 1 as dist, masterDeviceId, 1 as stock, dealerId, cost
    from
      devices
union
select 2 as dist, ingram_products.masterDeviceId, ingram_products.availability_flag as stock, ingram_prices.dealerId, ingram_prices.customer_price as cost
    from
      ingram_products
      join ingram_prices on ingram_products.ingram_part_number = ingram_prices.ingram_part_number
    where ingram_products.masterDeviceId is not null
union
select 3 as dist, synnex_products.masterDeviceId, synnex_products.Qty_on_Hand as stock, synnex_prices.dealerId, synnex_prices.Contract_Price as cost
    from
      synnex_products
      join synnex_prices on synnex_products.SYNNEX_SKU = synnex_prices.SYNNEX_SKU
    where synnex_products.masterDeviceId is not null
union
select 4 as dist, techdata_products.masterDeviceId, techdata_products.Qty as stock, techdata_prices.dealerId, currency_exchange.rate * techdata_prices.CustBestPrice as cost
    from
      techdata_products
      join techdata_prices on techdata_products.Matnr = techdata_prices.Matnr
      join dealers on dealers.id = techdata_prices.dealerId
      join currency_exchange on currency_exchange.currency = dealers.currency
    where techdata_products.masterDeviceId is not null
');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}