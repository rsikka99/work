<?php

use Phinx\Migration\AbstractMigration;

class AddUpdateTonerVendorByManufacturerIdProcedure extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('CREATE PROCEDURE updateTonerVendorByManufacturerId(IN inManufacturerId INT(11))
        /*
         * Updates toner vendor status. It will mark new toner vendors and delete ones that no longer have toners assigned to devices.
         */
            BEGIN
                DECLARE shouldBeTonerVendor INT DEFAULT 0;
                DECLARE isCurrentlyTonerVendor INT DEFAULT 0;

                # New Status
                SELECT
                    IF(COUNT(*) > 0, 1, 0)
                INTO shouldBeTonerVendor
                FROM toners AS t
                    JOIN device_toners AS dt
                        ON dt.toner_id = t.id
                    JOIN manufacturers AS m
                        ON m.id = t.manufacturerId
                    JOIN master_devices AS md
                        ON dt.master_device_id = md.id
                    JOIN manufacturers AS mdm
                        ON mdm.id = md.manufacturerId
                WHERE t.manufacturerId != md.manufacturerId AND t.manufacturerId = inManufacturerId;


                # Current Status
                SELECT
                    IF(COUNT(*) > 0, 1, 0)
                INTO isCurrentlyTonerVendor
                FROM toner_vendor_manufacturers AS tvm
                WHERE tvm.manufacturerId = inManufacturerId;

                # Update Status
                IF shouldBeTonerVendor = 0 AND isCurrentlyTonerVendor = 1
                THEN
                    DELETE FROM toner_vendor_manufacturers
                    WHERE manufacturerId = inManufacturerId;
                ELSE
                    IF shouldBeTonerVendor = 1 AND isCurrentlyTonerVendor = 0
                    THEN
                        INSERT INTO toner_vendor_manufacturers VALUES (inManufacturerId);
                    END IF;
                END IF;
            END'
        );
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('DROP PROCEDURE updateTonerVendorByManufacturerId');
    }
}