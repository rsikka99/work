<?php

use Phinx\Migration\AbstractMigration;

class CloverImagingGroup extends AbstractMigration
{
    public function up()
    {
        $this->execute("replace INTO `suppliers` (`id`, `name`) VALUES ('7', 'Clover Imaging Group')");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}