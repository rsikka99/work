<?php

use Phinx\Migration\AbstractMigration;

class AddInvalidRmsModels extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('ALTER TABLE `rms_devices` ADD COLUMN
            `isGeneric` TINYINT NOT NULL DEFAULT 0
        ');

        // 0    - Undefined
        // 1534 - Hewlett-Packard Color LaserJet
        // 8777 - Hewlett-Packard LaserJet
        $this->execute('UPDATE `rms_devices`
                            SET `isGeneric`=1
                        WHERE `rmsProviderId` = 1 and `rmsModelId` IN(0, 1534, 8777);
        ');

    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('ALTER TABLE `rms_devices` DROP COLUMN `isGeneric`;');
    }
}