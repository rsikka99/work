<?php

use Phinx\Migration\AbstractMigration;

class GenuineSupplier extends AbstractMigration
{
    public function up()
    {
        $this->execute("replace INTO `suppliers` (`id`, `name`) VALUES ('4', 'Genuine Supply Source')");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}