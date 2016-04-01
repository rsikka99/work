<?php

use Phinx\Migration\AbstractMigration;

class TonerViewFix2 extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('drop view if exists _view_dist_stock_price_grouped');
        $this->execute('
        create view _view_dist_stock_price_grouped as
            select * from _view_dist_stock_price_ordered o
            where dist=1 or cost = (select min(cost) from _view_dist_stock_price_ordered s where o.tonerId=s.tonerId and o.dealerId=s.dealerId)
            group by tonerId, dealerId
        ');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}