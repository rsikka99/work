<?php

use Phinx\Migration\AbstractMigration;

class DistributorMd5 extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->query('alter table ingram_products add _md5 char(32) null default null');
        $this->query('alter table synnex_products add _md5 char(32) null default null');
        $this->query('alter table techdata_products add _md5 char(32) null default null');
        $this->query('alter table ingram_prices add _md5 char(32) null default null');
        $this->query('alter table synnex_prices add _md5 char(32) null default null');
        $this->query('alter table techdata_prices add _md5 char(32) null default null');

        $this->query('ALTER TABLE synnex_prices DROP foreign key synnex_prices_ibfk_1');
        $this->query('ALTER TABLE synnex_prices DROP foreign key synnex_prices_ibfk_2');

        $this->query('ALTER TABLE ingram_prices DROP foreign key ingram_prices_ibfk_5');
        $this->query('ALTER TABLE ingram_prices DROP foreign key ingram_prices_ibfk_6');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}