<?php

use Phinx\Migration\AbstractMigration;

class ToShopify extends AbstractMigration
{

    public function up()
    {
        $this->execute("alter table clients add `webId` bigint null");
        $this->execute("alter table contacts add `email` varchar(255) null");
    }

    public function down()
    {
        $this->execute("alter table clients drop `webId`");
        $this->execute("alter table contacts drop `email`");
    }
}