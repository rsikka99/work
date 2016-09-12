<?php

use Phinx\Migration\AbstractMigration;

class Dandh extends AbstractMigration
{

    public function up()
    {
        $this->execute("replace INTO `suppliers` (`id`, `name`) VALUES ('6', 'D&H Distributing')");
    }

    public function down()
    {

    }
}