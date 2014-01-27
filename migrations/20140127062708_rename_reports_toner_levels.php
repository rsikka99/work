<?php

use Phinx\Migration\AbstractMigration;

class RenameReportsTonerLevels extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("ALTER TABLE master_devices change reportsTonerLevels isCapableOfReportingTonerLevels TINYINT(4) NOT NULL DEFAULT 0");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("ALTER TABLE master_devices change isCapableOfReportingTonerLevels reportsTonerLevels TINYINT(4) NOT NULL DEFAULT 0");
    }
}