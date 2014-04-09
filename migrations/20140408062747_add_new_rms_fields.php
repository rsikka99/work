<?php

use Phinx\Migration\AbstractMigration;

class AddNewRmsFields extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        try
        {
            $this->getAdapter()->beginTransaction();

            /**
             * Drop the old foreign keys in preparation for the new data type
             */
            $this->execute('ALTER TABLE `rms_master_matchups` DROP FOREIGN KEY `rms_master_matchups_ibfk_2`');
            $this->execute('ALTER TABLE `rms_upload_rows` DROP FOREIGN KEY `rms_upload_rows_ibfk_2`');
            $this->execute('ALTER TABLE `rms_user_matchups` DROP FOREIGN KEY `rms_user_matchups_ibfk_2`');

            /**
             * Convert rmsModelId columns to var char so that they can accept GUIDs
             */
            $this->execute('ALTER TABLE `rms_devices` MODIFY `rmsModelId` VARCHAR(255)');
            $this->execute('ALTER TABLE `rms_excluded_rows` MODIFY `rmsModelId` VARCHAR(255)');
            $this->execute('ALTER TABLE `rms_master_matchups` MODIFY `rmsModelId` VARCHAR(255)');
            $this->execute('ALTER TABLE `rms_upload_rows` MODIFY `rmsModelId` VARCHAR(255)');
            $this->execute('ALTER TABLE `rms_user_matchups` MODIFY `rmsModelId` VARCHAR(255)');

            /**
             * Create new foreign keys based on the new
             */
            $this->execute('ALTER TABLE `rms_master_matchups`
                                ADD CONSTRAINT `rms_master_matchups_ibfk_2`
                                    FOREIGN KEY (`rmsProviderId`, `rmsModelId`) REFERENCES `rms_devices` (`rmsProviderId`, `rmsModelId`)
                                    ON UPDATE CASCADE
                                    ON DELETE CASCADE
            ');

            $this->execute('ALTER TABLE `rms_upload_rows`
                                ADD CONSTRAINT `rms_upload_rows_ibfk_2`
                                    FOREIGN KEY (`rmsProviderId`, `rmsModelId`) REFERENCES `rms_devices` (`rmsProviderId`, `rmsModelId`)
                                    ON UPDATE CASCADE
                                    ON DELETE CASCADE
            ');

            $this->execute('ALTER TABLE `rms_user_matchups`
                                ADD CONSTRAINT `rms_user_matchups_ibfk_2`
                                    FOREIGN KEY (`rmsProviderId`, `rmsModelId`) REFERENCES `rms_devices` (`rmsProviderId`, `rmsModelId`)
                                    ON UPDATE CASCADE
                                    ON DELETE CASCADE
            ');


            /**
             * Change device_instance rmsDeviceId to be assetId and ensure assetId is a varchar
             */
            $this->execute('ALTER TABLE `device_instances` CHANGE `rmsDeviceId` `assetId` VARCHAR(255)');


            /**
             * Add the missing columns
             */
            $this->execute('ALTER TABLE `rms_upload_rows`
                                ADD COLUMN `assetId` VARCHAR(255) DEFAULT NULL,
                                ADD COLUMN `pageCoverageColor` DECIMAL DEFAULT NULL,
                                ADD COLUMN `rmsVendorName` VARCHAR(255) DEFAULT NULL,
                                ADD COLUMN `rmsReportVersion` VARCHAR(255) DEFAULT NULL,
                                ADD COLUMN `managementProgram` VARCHAR(255) DEFAULT NULL
            ');

            /**
             * Add page coverage color to device instances as well
             */
            $this->execute('ALTER TABLE `device_instances` ADD COLUMN `pageCoverageColor` DECIMAL DEFAULT NULL');


            $this->getAdapter()->commitTransaction();
        }
        catch (Exception $e)
        {
            $this->getAdapter()->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        try
        {
            $this->getAdapter()->beginTransaction();


            /**
             * Drop the old foreign keys in preparation for the new data type
             */
            $this->execute('ALTER TABLE `rms_master_matchups` DROP FOREIGN KEY `rms_master_matchups_ibfk_2`');
            $this->execute('ALTER TABLE `rms_upload_rows` DROP FOREIGN KEY `rms_upload_rows_ibfk_2`');
            $this->execute('ALTER TABLE `rms_user_matchups` DROP FOREIGN KEY `rms_user_matchups_ibfk_2`');

            // This could break once GUID's are in the table
            $this->execute('ALTER TABLE `rms_devices` MODIFY `rmsModelId` INTEGER');
            $this->execute('ALTER TABLE `rms_upload_rows` MODIFY `rmsModelId` INTEGER');
            $this->execute('ALTER TABLE `rms_user_matchups` MODIFY `rmsModelId` INTEGER');
            $this->execute('ALTER TABLE `rms_master_matchups` MODIFY `rmsModelId` INTEGER');
            $this->execute('ALTER TABLE `rms_excluded_rows` MODIFY `rmsModelId` INTEGER');

            /**
             * Create new foreign keys based on the new
             */
            $this->execute('ALTER TABLE `rms_master_matchups`
                                ADD CONSTRAINT `rms_master_matchups_ibfk_2`
                                    FOREIGN KEY (`rmsProviderId`, `rmsModelId`) REFERENCES `rms_devices` (`rmsProviderId`, `rmsModelId`)
                                    ON UPDATE CASCADE
                                    ON DELETE CASCADE
            ');

            $this->execute('ALTER TABLE `rms_upload_rows`
                                ADD CONSTRAINT `rms_upload_rows_ibfk_2`
                                    FOREIGN KEY (`rmsProviderId`, `rmsModelId`) REFERENCES `rms_devices` (`rmsProviderId`, `rmsModelId`)
                                    ON UPDATE CASCADE
                                    ON DELETE CASCADE
            ');

            $this->execute('ALTER TABLE `rms_user_matchups`
                                ADD CONSTRAINT `rms_user_matchups_ibfk_2`
                                    FOREIGN KEY (`rmsProviderId`, `rmsModelId`) REFERENCES `rms_devices` (`rmsProviderId`, `rmsModelId`)
                                    ON UPDATE CASCADE
                                    ON DELETE CASCADE
            ');


            // Remove missing columns
            $this->execute('ALTER TABLE `rms_upload_rows`
                                 DROP COLUMN assetId,
                                 DROP COLUMN pageCoverageColor,
                                 DROP COLUMN rmsVendorName,
                                 DROP COLUMN rmsReportVersion,
                                 DROP COLUMN managementProgram
            ');

            // Change device_instance rmsDeviceId to be assetId and ensure assetId is a varchar
            $this->execute('ALTER TABLE `device_instances` CHANGE `assetId` `rmsDeviceId` VARCHAR(255)');

            // Add page coverage color
            $this->execute('ALTER TABLE `device_instances` DROP COLUMN `pageCoverageColor`');

            $this->getAdapter()->commitTransaction();
        }
        catch (Exception $e)
        {
            $this->getAdapter()->rollbackTransaction();
            throw $e;
        }
    }
}