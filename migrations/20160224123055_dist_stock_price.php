<?php

use Phinx\Migration\AbstractMigration;

class DistStockPrice extends AbstractMigration
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
select 2 as dist, synnex_products.tonerId, synnex_products.Qty_on_Hand as stock, synnex_prices.dealerId, synnex_prices.Unit_Cost as cost
    from
      synnex_products
      join synnex_prices on synnex_products.SYNNEX_SKU = synnex_prices.SYNNEX_SKU
    where synnex_products.tonerId is not null
union
select 3 as dist, ingram_products.tonerId, ingram_products.availability_flag as stock, ingram_prices.dealerId, ingram_prices.customer_price as cost
    from
      ingram_products
      join ingram_prices on ingram_products.ingram_part_number = ingram_prices.ingram_part_number
    where ingram_products.tonerId is not null
');

        $this->execute('drop view if exists _view_dist_stock_price_in_stock');
        $this->execute("
create view _view_dist_stock_price_in_stock as
select * from _view_dist_stock_price where (dist=2 and stock>0) or (dist=3 and stock='Y') order by cost
");
        $this->execute('drop view if exists _view_dist_stock_price_not_in_stock');
        $this->execute("
create view _view_dist_stock_price_not_in_stock as
select * from _view_dist_stock_price where (dist=2 and stock=0) or (dist=3 and stock='N') order by cost
");
        $this->execute('drop view if exists _view_dist_stock_price_ordered');
        $this->execute('
create view _view_dist_stock_price_ordered as
select * from _view_dist_stock_price where dist=1
union
select * from _view_dist_stock_price_in_stock
union
select * from _view_dist_stock_price_not_in_stock
');
        $this->execute('drop view if exists _view_dist_stock_price_grouped');
        $this->execute('
create view _view_dist_stock_price_grouped as
select * from _view_dist_stock_price_ordered group by dealerId, tonerId
');

    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('drop view if exists _view_dist_stock_price_ordered');
        $this->execute('drop view if exists _view_dist_stock_price_not_in_stock');
        $this->execute('drop view if exists _view_dist_stock_price_in_stock');
        $this->execute('drop view if exists _view_dist_stock_price');
    }
}