<?php

use Phinx\Migration\AbstractMigration;

class FixScanCopy extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('UPDATE `master_devices` SET `isCopier`=1 WHERE `isScanner`=1;');
        $this->execute('UPDATE `rms_upload_rows` SET `isCopier`=1 WHERE `isScanner`=1;');

        $this->execute('ALTER TABLE `master_devices`
                        DROP COLUMN `isScanner`;');

        $this->execute('ALTER TABLE `rms_upload_rows`
                        DROP COLUMN `isScanner`;');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('ALTER TABLE `master_devices`
                        ADD COLUMN `isScanner` TINYINT(1) NOT NULL DEFAULT 0;');

        $this->execute('ALTER TABLE `rms_upload_rows`
                        ADD COLUMN `isScanner` TINYINT(1) NOT NULL DEFAULT 0;');

        $this->execute('UPDATE `master_devices` SET `isScanner`=1 WHERE `isCopier`=1;');
        $this->execute('UPDATE `rms_upload_rows` SET `isScanner`=1 WHERE `isCopier`=1;');
    }
}