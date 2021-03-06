<?php

use Phinx\Migration\AbstractMigration;

class FixGetCheapestTonersForDeviceProcedure extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('DROP PROCEDURE getCheapestTonersForDevice;');

        $this->execute('CREATE PROCEDURE getCheapestTonersForDevice(IN inMasterDeviceId       INT(11), IN inDealerId INT(11), IN inMonochromeTonerPreference TEXT,
                                            IN inColorTonerPreference TEXT)
    BEGIN
        SET inMonochromeTonerPreference = IF(CHAR_LENGTH(inMonochromeTonerPreference) > 0, CONCAT(inMonochromeTonerPreference, \',\'),
                                             inMonochromeTonerPreference);
        SET inColorTonerPreference = IF(CHAR_LENGTH(inColorTonerPreference) > 0, CONCAT(inColorTonerPreference, \',\'), inColorTonerPreference);

        SELECT
            *
        FROM (
                 SELECT
                    device_toners.toner_id as id,
                    toners.sku,
                    toners.cost,
                    COALESCE(dealer_toner_attributes.cost, toners.cost) as calculatedCost,
                    toners.yield,
                    COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield AS costPerPage,
                    toners.manufacturerId,
                    IF(master_devices.manufacturerId = toners.manufacturerId, 1, 0)    AS isOem,
                    toners.tonerColorId
                 FROM device_toners
                     LEFT JOIN toners ON toners.id = device_toners.toner_id
                     LEFT JOIN master_devices ON master_devices.id = device_toners.master_device_id
                     LEFT JOIN dealer_toner_attributes
                         ON dealer_toner_attributes.tonerId = device_toners.toner_id AND dealer_toner_attributes.dealerId = inDealerId
                 WHERE device_toners.master_device_id = inMasterDeviceId AND toners.tonerColorId != 1
                       AND find_in_set(toners.manufacturerId, CONCAT(inColorTonerPreference, master_devices.manufacturerId))
                       AND COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield =
                           (SELECT
                                min(COALESCE(dta.cost, t.cost) / t.yield)
                            FROM device_toners
                                LEFT JOIN toners AS t
                                    ON t.id = device_toners.toner_id
                                LEFT JOIN
                                master_devices AS md
                                    ON md.id = device_toners.master_device_id
                                LEFT JOIN dealer_toner_attributes AS dta
                                    ON dta.tonerId = device_toners.toner_id AND dta.dealerId = inDealerId
                            WHERE
                                toners.tonerColorId = t.tonerColorId AND
                                device_toners.master_device_id = inMasterDeviceId AND
                                t.manufacturerId = toners.manufacturerId)
                 ORDER BY find_in_set(toners.manufacturerId, CONCAT(inColorTonerPreference, master_devices.manufacturerId)),
                     COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield ASC
             ) AS selectStatement1
        GROUP BY selectStatement1.tonerColorId

        UNION

        SELECT
            *
        FROM (
                 SELECT
                     device_toners.toner_id as id,
                     toners.sku,
                     toners.cost,
                     COALESCE(dealer_toner_attributes.cost, toners.cost) as calculatedCost,
                     toners.yield,
                     COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield AS costPerPage,
                     toners.manufacturerId,
                     IF(master_devices.manufacturerId = toners.manufacturerId, 1, 0)    AS isOem,
                     toners.tonerColorId
                 FROM device_toners
                     LEFT JOIN toners ON toners.id = device_toners.toner_id
                     LEFT JOIN master_devices ON master_devices.id = device_toners.master_device_id
                     LEFT JOIN dealer_toner_attributes
                         ON dealer_toner_attributes.tonerId = device_toners.toner_id AND dealer_toner_attributes.dealerId = inDealerId
                 WHERE device_toners.master_device_id = inMasterDeviceId AND toners.tonerColorId = 1
                       AND find_in_set(toners.manufacturerId, CONCAT(inMonochromeTonerPreference, master_devices.manufacturerId))
                       AND COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield =
                           (SELECT
                                min(COALESCE(dta.cost, t.cost) / t.yield)
                            FROM device_toners
                                LEFT JOIN toners AS t
                                    ON t.id = device_toners.toner_id
                                LEFT JOIN
                                master_devices AS md
                                    ON md.id = device_toners.master_device_id
                                LEFT JOIN dealer_toner_attributes AS dta
                                    ON dta.tonerId = device_toners.toner_id AND dta.dealerId = inDealerId
                            WHERE
                                toners.tonerColorId = t.tonerColorId AND
                                device_toners.master_device_id = inMasterDeviceId AND
                                t.manufacturerId = toners.manufacturerId)
                 ORDER BY find_in_set(toners.manufacturerId, CONCAT(inMonochromeTonerPreference, master_devices.manufacturerId)),
                     COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield ASC
             ) AS selectStatement2
        GROUP BY selectStatement2.tonerColorId;

    END;
    ');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('DROP PROCEDURE getCheapestTonersForDevice;');

        $this->execute('CREATE PROCEDURE getCheapestTonersForDevice(IN inMasterDeviceId       INT(11), IN inDealerId INT(11), IN inMonochromeTonerPreference TEXT,
                                            IN inColorTonerPreference TEXT)
    BEGIN
        SET inMonochromeTonerPreference = IF(CHAR_LENGTH(inMonochromeTonerPreference) > 0, CONCAT(inMonochromeTonerPreference, \',\'),
                                             inMonochromeTonerPreference);
        SET inColorTonerPreference = IF(CHAR_LENGTH(inColorTonerPreference) > 0, CONCAT(inColorTonerPreference, \',\'), inColorTonerPreference);

        SELECT
            *
        FROM (
                 SELECT
                    device_toners.toner_id as id,
                    toners.sku,
                    toners.cost,
                    COALESCE(dealer_toner_attributes.cost, toners.cost) as calculatedCost,
                    toners.yield,
                    COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield AS costPerPage,
                    toners.manufacturerId,
                    IF(master_devices.manufacturerId = toners.manufacturerId, 1, 0)    AS isOem,
                    toners.tonerColorId
                 FROM device_toners
                     LEFT JOIN toners ON toners.id = device_toners.toner_id
                     LEFT JOIN master_devices ON master_devices.id = device_toners.master_device_id
                     LEFT JOIN dealer_toner_attributes
                         ON dealer_toner_attributes.tonerId = device_toners.toner_id AND dealer_toner_attributes.dealerId = inDealerId
                 WHERE device_toners.master_device_id = inMasterDeviceId AND toners.tonerColorId != 1
                       AND find_in_set(toners.manufacturerId, CONCAT(inColorTonerPreference, master_devices.manufacturerId))
                       AND COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield =
                           (SELECT
                                min(COALESCE(dta.cost, t.cost) / t.yield)
                            FROM device_toners
                                LEFT JOIN toners AS t
                                    ON t.id = device_toners.toner_id
                                LEFT JOIN
                                master_devices AS md
                                    ON md.id = device_toners.master_device_id
                                LEFT JOIN dealer_toner_attributes AS dta
                                    ON dta.tonerId = device_toners.toner_id AND dta.dealerId = 1
                            WHERE
                                toners.tonerColorId = t.tonerColorId AND
                                device_toners.master_device_id = inMasterDeviceId AND
                                t.manufacturerId = toners.manufacturerId)
                 ORDER BY find_in_set(toners.manufacturerId, CONCAT(inColorTonerPreference, master_devices.manufacturerId)),
                     COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield ASC
             ) AS selectStatement1
        GROUP BY selectStatement1.tonerColorId

        UNION

        SELECT
            *
        FROM (
                 SELECT
                     device_toners.toner_id as id,
                     toners.sku,
                     toners.cost,
                     COALESCE(dealer_toner_attributes.cost, toners.cost) as calculatedCost,
                     toners.yield,
                     COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield AS costPerPage,
                     toners.manufacturerId,
                     IF(master_devices.manufacturerId = toners.manufacturerId, 1, 0)    AS isOem,
                     toners.tonerColorId
                 FROM device_toners
                     LEFT JOIN toners ON toners.id = device_toners.toner_id
                     LEFT JOIN master_devices ON master_devices.id = device_toners.master_device_id
                     LEFT JOIN dealer_toner_attributes
                         ON dealer_toner_attributes.tonerId = device_toners.toner_id AND dealer_toner_attributes.dealerId = inDealerId
                 WHERE device_toners.master_device_id = inMasterDeviceId AND toners.tonerColorId = 1
                       AND find_in_set(toners.manufacturerId, CONCAT(inMonochromeTonerPreference, master_devices.manufacturerId))
                       AND COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield =
                           (SELECT
                                min(COALESCE(dta.cost, t.cost) / t.yield)
                            FROM device_toners
                                LEFT JOIN toners AS t
                                    ON t.id = device_toners.toner_id
                                LEFT JOIN
                                master_devices AS md
                                    ON md.id = device_toners.master_device_id
                                LEFT JOIN dealer_toner_attributes AS dta
                                    ON dta.tonerId = device_toners.toner_id AND dta.dealerId = inDealerId
                            WHERE
                                toners.tonerColorId = t.tonerColorId AND
                                device_toners.master_device_id = inMasterDeviceId AND
                                t.manufacturerId = toners.manufacturerId)
                 ORDER BY find_in_set(toners.manufacturerId, CONCAT(inMonochromeTonerPreference, master_devices.manufacturerId)),
                     COALESCE(dealer_toner_attributes.cost, toners.cost) / toners.yield ASC
             ) AS selectStatement2
        GROUP BY selectStatement2.tonerColorId;

    END;
    ');
    }
}