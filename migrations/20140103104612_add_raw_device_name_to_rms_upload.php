<?php

use Phinx\Migration\AbstractMigration;

class AddRawDeviceNameToRmsUpload extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('ALTER TABLE `device_instances`
                        ADD COLUMN `rawDeviceName` VARCHAR(255) DEFAULT NULL;');

        $this->execute('ALTER TABLE `rms_upload_rows`
                        ADD COLUMN `rawDeviceName` VARCHAR(255) DEFAULT NULL;');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('ALTER TABLE `device_instances`
                        DROP COLUMN `rawDeviceName`;');

        $this->execute('ALTER TABLE `rms_upload_rows`
                        DROP COLUMN `rawDeviceName`;');
    }
}