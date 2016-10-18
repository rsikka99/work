<?php

use Phinx\Migration\AbstractMigration;

class SupplierProductKit extends AbstractMigration
{
    public function up()
    {
        $this->query('alter table supplier_product add package varchar(64) null default null');
    }

    public function down()
    {

    }
}