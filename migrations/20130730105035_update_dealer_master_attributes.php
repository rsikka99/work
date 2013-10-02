<?php

use Phinx\Migration\AbstractMigration;

class UpdateDealerMasterAttributes extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {

        $this->execute("
            INSERT INTO dealer_master_device_attributes (masterDeviceId, dealerId, partsCostPerPage, laborCostPerPage)
                VALUES
                    (42, 2, 0, 0),
                    (79, 2, 0, 0),
                    (95, 2, 0, 0),
                    (401, 2, 0, 0),
                    (445, 2, 0, 0),
                    (471, 2, 0, 0),
                    (478, 2, 0, 0),
                    (484, 2, 0, 0)
                ON DUPLICATE KEY UPDATE
                    masterDeviceId = VALUES(masterDeviceId)
        ");

        $this->execute("
            INSERT INTO devices (masterDeviceId, dealerId, cost, dealerSku, oemSku, description)
                VALUES
                    (42,  2, 1000, null, '8870/DN', null),
                    (79,  2, 1000, null, '8870/DN', null),
                    (95,  2, 1500, null, 'CC395A', null),
                    (401, 2, 3000, null, 'Q3939A', '- Fax accessory'),
                    (445, 2, 5000, 'S7938341', '8570/DN',  null),
                    (471, 2, 4699.99, 'S8145808', 'CE504A',  null),
                    (478, 2, 1299.99,  null, 'CE992A',  null),
                    (483, 2, 14500,  null, '1102LB2US0', '65/65 PPM A3 Color MFP'),
                    (484, 2, 780,  null, 'C6010', '65/65 PPM A3 Color MFP')
                ON DUPLICATE KEY UPDATE
                    masterDeviceId = VALUES(masterDeviceId)
        ");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
    }
}