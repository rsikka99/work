<?php

use Phinx\Migration\AbstractMigration;

class DeviceInstanceMeterUpdate extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("RENAME TABLE `device_instance_meters` to `device_instance_meters_old`;");
        $this->execute("CREATE TABLE `device_instance_meters` (
                        `id` INT AUTO_INCREMENT,
                         `deviceInstanceId` INT(11),
                         `monitorStartDate` DATETIME,
                         `monitorEndDate` DATETIME,
                         `startMeterBlack` INT(11),
                         `endMeterBlack` INT(11),
                         `startMeterColor` INT(11),
                         `endMeterColor` INT(11),
                         `startMeterPrintBlack` INT(11),
                         `endMeterPrintBlack` INT(11),
                         `startMeterPrintColor` INT(11),
                         `endMeterPrintColor` INT(11),
                         `startMeterCopyBlack` INT(11),
                         `endMeterCopyBlack` INT(11),
                         `startMeterCopyColor` INT(11),
                         `endMeterCopyColor` INT(11),
                         `startMeterFax` INT(11),
                         `endMeterFax` INT(11),
                         `startMeterScan` INT(11),
                         `endMeterScan` INT(11),
                         `startMeterPrintA3Black` INT(11),
                         `endMeterPrintA3Black` INT(11),
                         `startMeterPrintA3Color` INT(11),
                         `endMeterPrintA3Color` INT(11),
                         `startMeterLife` INT(11),
                         `endMeterLife` INT(11),
                        PRIMARY KEY (id));
        ");

        $this->execute("INSERT INTO `device_instance_meters` (deviceInstanceId)
                         SELECT `deviceInstanceId` FROM `device_instance_meters_old`
                         GROUP BY deviceInstanceId;
         ");

        $this->execute("UPDATE `device_instance_meters` SET
                         monitorStartDate=(     SELECT monitorStartDate FROM device_instance_meters_old where device_instance_meters_old.meterType = 'BLACK'        AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         monitorEndDate=(       SELECT monitorEndDate   FROM device_instance_meters_old where device_instance_meters_old.meterType = 'BLACK'        AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         startMeterBlack=(      SELECT startMeter       FROM device_instance_meters_old where device_instance_meters_old.meterType = 'BLACK'        AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         endMeterBlack=(        SELECT endMeter         FROM device_instance_meters_old where device_instance_meters_old.meterType = 'BLACK'        AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         startMeterColor=(      SELECT startMeter       FROM device_instance_meters_old where device_instance_meters_old.meterType = 'COLOR'        AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         endMeterColor=(        SELECT endMeter         FROM device_instance_meters_old where device_instance_meters_old.meterType = 'COLOR'        AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         startMeterPrintBlack=( SELECT startMeter       FROM device_instance_meters_old where device_instance_meters_old.meterType = 'PRINT BLACK'  AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         endMeterPrintBlack=(   SELECT endMeter         FROM device_instance_meters_old where device_instance_meters_old.meterType = 'PRINT BLACK'  AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         startMeterPrintColor=( SELECT startMeter       FROM device_instance_meters_old where device_instance_meters_old.meterType = 'PRINT COLOR'  AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         endMeterPrintColor=(   SELECT endMeter         FROM device_instance_meters_old where device_instance_meters_old.meterType = 'PRINT COLOR'  AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         startMeterCopyBlack=(  SELECT startMeter       FROM device_instance_meters_old where device_instance_meters_old.meterType = 'COPY BLACK'   AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         endMeterCopyBlack=(    SELECT endMeter         FROM device_instance_meters_old where device_instance_meters_old.meterType = 'COPY BLACK'   AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         startMeterCopyColor=(  SELECT startMeter       FROM device_instance_meters_old where device_instance_meters_old.meterType = 'COPY COLOR'   AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         endMeterCopyColor=(    SELECT endMeter         FROM device_instance_meters_old where device_instance_meters_old.meterType = 'COPY COLOR'   AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         startMeterScan=(       SELECT startMeter       FROM device_instance_meters_old where device_instance_meters_old.meterType = 'SCAN'         AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         endMeterScan=(         SELECT endMeter         FROM device_instance_meters_old where device_instance_meters_old.meterType = 'SCAN'         AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         startMeterFax=(        SELECT startMeter       FROM device_instance_meters_old where device_instance_meters_old.meterType = 'FAX'          AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         endMeterFax=(          SELECT endMeter         FROM device_instance_meters_old where device_instance_meters_old.meterType = 'FAX'          AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         startMeterLife=(       SELECT startMeter       FROM device_instance_meters_old where device_instance_meters_old.meterType = 'LIFE'         AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId),
                         endMeterLife=(         SELECT endMeter         FROM device_instance_meters_old where device_instance_meters_old.meterType = 'LIFE'         AND device_instance_meters.deviceInstanceId = device_instance_meters_old.deviceInstanceId);
         ");

        $this->execute("DROP TABLE `device_instance_meters_old`;");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("RENAME TABLE `device_instance_meters` to `device_instance_meters_old`;");

        $this->execute("CREATE TABLE IF NOT EXISTS `device_instance_meters` (
                        `id`               INT(11)                                                                                                 NOT NULL AUTO_INCREMENT,
                        `deviceInstanceId` INT(11)                                                                                                 NOT NULL,
                        `meterType`        ENUM('LIFE', 'COLOR', 'COPY BLACK', 'BLACK', 'PRINT BLACK', 'PRINT COLOR', 'COPY COLOR', 'SCAN', 'FAX') NULL,
                        `startMeter`       INT(11)                                                                                                 NOT NULL,
                        `endMeter`         INT(11)                                                                                                 NOT NULL,
                        `monitorStartDate` DATETIME                                                                                                NOT NULL,
                        `monitorEndDate`   DATETIME                                                                                                NOT NULL,
                        PRIMARY KEY (`id`),
                        UNIQUE INDEX `device_instance_id` (`deviceInstanceId` ASC, `meterType` ASC),
                        CONSTRAINT `device_instance_meters_ibfk_1`
                        FOREIGN KEY (`deviceInstanceId`)
                        REFERENCES `device_instances` (`id`)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE);
        ");

        $this->execute("INSERT INTO `device_instance_meters` (deviceInstanceId,meterType,startMeter,endMeter,monitorStartDate,monitorEndDate)
                        SELECT deviceInstanceId,'BLACK',startMeterBlack,endMeterBlack,monitorStartDate,monitorEndDate
                        FROM device_instance_meters_old
                        WHERE device_instance_meters_old.endMeterBlack > 0;
        ");

        $this->execute("INSERT INTO `device_instance_meters` (deviceInstanceId,meterType,startMeter,endMeter,monitorStartDate,monitorEndDate)
                        SELECT deviceInstanceId,'COLOR',startMeterColor,endMeterColor,monitorStartDate,monitorEndDate
                        FROM device_instance_meters_old
                        WHERE device_instance_meters_old.endMeterColor > 0;
        ");

        $this->execute("INSERT INTO `device_instance_meters` (deviceInstanceId,meterType,startMeter,endMeter,monitorStartDate,monitorEndDate)
                        SELECT deviceInstanceId,'PRINT BLACK',startMeterPrintBlack,endMeterPrintBlack,monitorStartDate,monitorEndDate
                        FROM device_instance_meters_old
                        WHERE device_instance_meters_old.endMeterPrintBlack > 0;
        ");

        $this->execute("INSERT INTO `device_instance_meters` (deviceInstanceId,meterType,startMeter,endMeter,monitorStartDate,monitorEndDate)
                        SELECT deviceInstanceId,'PRINT COLOR',startMeterPrintColor,endMeterPrintColor,monitorStartDate,monitorEndDate
                        FROM device_instance_meters_old
                        WHERE device_instance_meters_old.endMeterPrintColor > 0;
        ");

        $this->execute("INSERT INTO `device_instance_meters` (deviceInstanceId,meterType,startMeter,endMeter,monitorStartDate,monitorEndDate)
                        SELECT deviceInstanceId,'COPY BLACK',startMeterCopyBlack,endMeterCopyBlack,monitorStartDate,monitorEndDate
                        FROM device_instance_meters_old
                        WHERE device_instance_meters_old.endMeterCopyBlack > 0;
        ");

        $this->execute("INSERT INTO `device_instance_meters` (deviceInstanceId,meterType,startMeter,endMeter,monitorStartDate,monitorEndDate)
                        SELECT deviceInstanceId,'COPY COLOR',startMeterCopyColor,endMeterCopyColor,monitorStartDate,monitorEndDate
                        FROM device_instance_meters_old
                        WHERE device_instance_meters_old.endMeterCopyColor > 0;
        ");

        $this->execute("INSERT INTO `device_instance_meters` (deviceInstanceId,meterType,startMeter,endMeter,monitorStartDate,monitorEndDate)
                        SELECT deviceInstanceId,'SCAN',startMeterScan,endMeterScan,monitorStartDate,monitorEndDate
                        FROM device_instance_meters_old
                        WHERE device_instance_meters_old.endMeterScan > 0;
        ");

        $this->execute("INSERT INTO `device_instance_meters` (deviceInstanceId,meterType,startMeter,endMeter,monitorStartDate,monitorEndDate)
                        SELECT deviceInstanceId,'FAX',startMeterFax,endMeterFax,monitorStartDate,monitorEndDate
                        FROM device_instance_meters_old
                        WHERE device_instance_meters_old.endMeterFax > 0;
        ");

        $this->execute("INSERT INTO `device_instance_meters` (deviceInstanceId,meterType,startMeter,endMeter,monitorStartDate,monitorEndDate)
                        SELECT deviceInstanceId,'LIFE',startMeterLife,endMeterLife,monitorStartDate,monitorEndDate
                        FROM device_instance_meters_old
                        WHERE device_instance_meters_old.endMeterLife > 0;
        ");

        $this->execute("DROP TABLE `device_instance_meters_old`;");
    }
}
