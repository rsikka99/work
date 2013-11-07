<?php

use Phinx\Migration\AbstractMigration;

class AddRmsVendorPrintAudit extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('INSERT INTO `rms_providers` (`id`, `name`) VALUES
                (4, \'Print Audit\');');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('DELETE FROM `rms_providers` WHERE id=4;');
    }
}