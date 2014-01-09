<?php

use Phinx\Migration\AbstractMigration;

class AddMissingFieldsToRmsUploadRow extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE rms_upload_rows ADD COLUMN reportsTonerLevels TINYINT(4) NOT NULL DEFAULT 0 AFTER `isDuplex`");
        $this->execute("ALTER TABLE rms_upload_rows ADD COLUMN isA3 TINYINT(4) NOT NULL DEFAULT 0 AFTER `isDuplex`");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE rms_upload_rows DROP `reportsTonerLevels`, DROP `isA3`");
    }
}