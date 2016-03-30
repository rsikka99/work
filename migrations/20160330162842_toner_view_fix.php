<?php

use Phinx\Migration\AbstractMigration;

class TonerViewFix extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('drop view if exists _view_dist_stock_price_in_stock');
        $this->execute("
create view _view_dist_stock_price_in_stock as
select * from _view_dist_stock_price where (dist=1) or (dist=2 and stock='Y') or (dist=3 and stock>0) or (dist=4 and stock>0) order by cost
");

        $this->execute('drop view if exists _view_dist_stock_price_ordered');
        $this->execute('
create view _view_dist_stock_price_ordered as
select * from _view_dist_stock_price_in_stock
union
select * from _view_dist_stock_price_not_in_stock
');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}