<?php

use Phinx\Migration\AbstractMigration;

class OtherSkus extends AbstractMigration
{
    public function up()
    {
        $this->execute('alter table base_product add otherSkus varchar(255) null default null');
    }
    public function down()
    {

    }
}