<?php

use Phinx\Migration\AbstractMigration;

class RemoveDutyCycle extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("ALTER TABLE master_devices DROP COLUMN `dutyCycle`");
        $this->execute("ALTER TABLE rms_upload_rows DROP COLUMN `dutyCycle`");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("ALTER TABLE master_devices ADD COLUMN `dutyCycle` INT(11) NULL");
        $this->execute("ALTER TABLE rms_upload_rows ADD COLUMN `dutyCycle` INT(11) NULL");
    }
}