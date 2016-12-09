<?php

use Phinx\Migration\AbstractMigration;

class TonerStockViews extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('drop view if exists _view_dist_stock_price;
create view _view_dist_stock_price as
select supplierId as dist, baseProductId as tonerId, isStock as stock, dealerId, price as cost
    from
      supplier_product
      join supplier_price using (supplierId, supplierSku)
    where baseProductId is not null');

        $this->execute('
drop view if exists _view_dist_stock_price_ordered');

        $this->execute('
drop view if exists _view_dist_stock_price_in_stock');

        $this->execute('
drop view if exists _view_dist_stock_price_not_in_stock');

        $this->execute('
drop view if exists _view_dist_stock_price_stock_cost');

        $this->execute('
create view _view_dist_stock_price_stock_cost as
    select
        tonerId,dealerId,
        (select min(cost) from _view_dist_stock_price as s1 where s1.stock>0 and s1.tonerId=main.tonerId and s1.dealerId=main.dealerId) as cost_in_stock,
        (select min(cost) from _view_dist_stock_price as s2 where s2.stock=0 and s2.tonerId=main.tonerId and s2.dealerId=main.dealerId) as cost_not_in_stock
        from _view_dist_stock_price as main
        group by tonerId,dealerId');

        $this->execute('
drop view if exists _view_dist_stock_price_grouped');

        $this->execute('
create view _view_dist_stock_price_grouped as
    select tonerId, dealerId, ifnull(cost_in_stock, cost_not_in_stock) as cost, if (cost_in_stock,1,0) as stock
    from _view_dist_stock_price_stock_cost');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}