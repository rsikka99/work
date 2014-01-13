<?php

use Phinx\Migration\AbstractMigration;

class RemoveReplacementDeviceTable extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("DROP TABLE replacement_devices");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS `replacement_devices` (
                            `masterDeviceId`      INT(11)                                                          NOT NULL,
                            `dealerId`            INT                                                              NOT NULL,
                            `replacementCategory` ENUM('BLACK & WHITE', 'BLACK & WHITE MFP', 'COLOR', 'COLOR MFP') NULL DEFAULT NULL,
                            `printSpeed`          INT(11)                                                          NULL,
                            `resolution`          INT(11)                                                          NULL DEFAULT NULL,
                            `monthlyRate`         DOUBLE                                                           NULL,
                            INDEX `replacement_devices_ibfk_2_idx` (`dealerId` ASC),
                            PRIMARY KEY (`masterDeviceId`, `dealerId`),
                            CONSTRAINT `replacement_devices_ibfk_1`
                            FOREIGN KEY (`masterDeviceId`)
                            REFERENCES `master_devices` (`id`)
                                ON DELETE RESTRICT
                                ON UPDATE RESTRICT,
                            CONSTRAINT `replacement_devices_ibfk_2`
                            FOREIGN KEY (`dealerId`)
                            REFERENCES `dealers` (`id`)
                                ON DELETE CASCADE
                                ON UPDATE CASCADE
                        );");

        $this->execute("INSERT INTO `replacement_devices` (`dealerId`, `masterDeviceId`, `replacementCategory`, `printSpeed`, `resolution`, `monthlyRate`) VALUES
                            (1, 78, 'COLOR MFP', 1, 1, 249),
                            (1, 43, 'BLACK & WHITE', 1, 1, 99),
                            (1, 460, 'BLACK & WHITE MFP', 1, 1, 199),
                            (1, 79, 'COLOR', 1, 1, 199),
                            (2, 78, 'COLOR MFP', 1, 1, 249),
                            (2, 43, 'BLACK & WHITE', 1, 1, 99),
                            (2, 460, 'BLACK & WHITE MFP', 1, 1, 199),
                            (2, 79, 'COLOR', 1, 1, 199);
                            ");
    }
}