<?php

use Phinx\Migration\AbstractMigration;

class RefactorResidualIntoBuyoutValue extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE quote_devices CHANGE residual buyoutValue DOUBLE NOT NULL");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE quote_devices CHANGE buyoutValue residual DOUBLE NOT NULL");
    }
}