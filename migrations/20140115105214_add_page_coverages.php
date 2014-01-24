<?php

use Phinx\Migration\AbstractMigration;

class AddPageCoverages extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE rms_upload_rows ADD COLUMN `pageCoverageMonochrome` DOUBLE NULL DEFAULT NULL,
                        ADD COLUMN `pageCoverageCyan` DOUBLE NULL DEFAULT NULL,
                        ADD COLUMN `pageCoverageMagenta` DOUBLE NULL DEFAULT NULL,
                        ADD COLUMN `pageCoverageYellow` DOUBLE NULL DEFAULT NULL;
                        ");

        $this->execute("ALTER TABLE assessment_settings ADD COLUMN `useDevicePageCoverages` TINYINT(4) NOT NULL DEFAULT 0;");
        $this->execute("ALTER TABLE healthcheck_settings ADD COLUMN `useDevicePageCoverages` TINYINT(4) NOT NULL DEFAULT 0;");
        $this->execute("ALTER TABLE hardware_optimization_settings ADD COLUMN `useDevicePageCoverages` TINYINT(4) NOT NULL DEFAULT 0;");
        $this->execute("ALTER TABLE memjet_optimization_settings ADD COLUMN `useDevicePageCoverages` TINYINT(4) NOT NULL DEFAULT 0;");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE rms_upload_rows DROP `pageCoverageMonochrome`, DROP `pageCoverageCyan`, DROP `pageCoverageMagenta`, DROP `pageCoverageYellow`");
        $this->execute("ALTER TABLE assessment_settings DROP `useDevicePageCoverages`");
        $this->execute("ALTER TABLE healthcheck_settings DROP `useDevicePageCoverages`");
        $this->execute("ALTER TABLE hardware_optimization_settings DROP `useDevicePageCoverages`");
        $this->execute("ALTER TABLE memjet_optimization_settings DROP `useDevicePageCoverages`");
    }
}