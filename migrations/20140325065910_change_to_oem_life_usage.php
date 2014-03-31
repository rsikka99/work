<?php

use Phinx\Migration\AbstractMigration;

class ChangeToOemLifeUsage extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        try
        {
            $this->getAdapter()->beginTransaction();
            $this->execute('ALTER TABLE `master_devices` ADD COLUMN `maximumRecommendedMonthlyPageVolume` INT DEFAULT 0;');
            $this->execute('UPDATE master_devices
    JOIN (
        SELECT
            md.id                                                                      AS master_device_id,
            COALESCE(IF(max(t.yield) > 0, max(t.yield), NULL), md.leasedTonerYield, 0) AS highestYield
        FROM master_devices AS md
            LEFT JOIN device_toners AS dt ON md.id = dt.master_device_id
            LEFT JOIN toners AS t ON dt.toner_id = t.id AND t.manufacturerId = md.manufacturerId
        GROUP BY md.id
ORDER BY md.id ASC
LIMIT 50000
) AS table2 ON table2.master_device_id = master_devices.id
SET maximumRecommendedMonthlyPageVolume = highestYield;');

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
            $this->execute('ALTER TABLE `master_devices` DROP COLUMN `maximumRecommendedMonthlyPageVolume`;');
        }
        catch (Exception $e)
        {
            $this->getAdapter()->rollbackTransaction();

            throw $e;
        }
    }
}