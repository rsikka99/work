<?php

use Phinx\Migration\AbstractMigration;

class AddLocationField extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('ALTER TABLE device_instances
            ADD COLUMN location VARCHAR(255) DEFAULT NULL
        ');

        $this->execute('ALTER TABLE rms_upload_rows
            ADD COLUMN location VARCHAR(255) DEFAULT NULL
        ');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('ALTER TABLE device_instances
            DROP COLUMN location
        ');

        $this->execute('ALTER TABLE rms_upload_rows
            DROP COLUMN rms_upload_rows
        ');
    }
}