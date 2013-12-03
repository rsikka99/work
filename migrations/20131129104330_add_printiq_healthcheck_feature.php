<?php

use Phinx\Migration\AbstractMigration;

class AddPrintiqHealthcheckFeature extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("INSERT INTO features (id, name) VALUES ('healthcheck_printiq', 'Health Check PrintIQ (Overrides standard Health Check)')");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DELETE FROM features where id='healthcheck_printiq'");
    }
}