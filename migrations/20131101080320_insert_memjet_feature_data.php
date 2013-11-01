<?php

use Phinx\Migration\AbstractMigration;

class InsertMemjetFeatureData extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("INSERT INTO features (`id`) VALUES ('hardware_optimization_memjet')");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("DELETE FROM features WHERE id = 'hardware_optimization_memjet'");
    }
}