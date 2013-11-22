<?php

use Phinx\Migration\AbstractMigration;

class AddMemjetDeviceSwapTables extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS `memjet_device_swap_reason_categories` (
            `id`   INT(11)      NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id`));
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS `memjet_device_swap_reasons` (
            `id`                         INT(11)      NOT NULL AUTO_INCREMENT,
            `dealerId`                   INT(11)      NOT NULL,
            `deviceSwapReasonCategoryId` INT(11)      NOT NULL,
            `reason`                     VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `memjet_swap_reasons_ibfk1_idx` (`dealerId` ASC),
            INDEX `memjet_swap_reasons_ibfk2_idx` (`deviceSwapReasonCategoryId` ASC),
            CONSTRAINT `memjet_swap_reasons_ibfk1`
            FOREIGN KEY (`dealerId`)
            REFERENCES `dealers` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
            CONSTRAINT `memjet_swap_reasons_ibfk2`
            FOREIGN KEY (`deviceSwapReasonCategoryId`)
            REFERENCES `memjet_device_swap_reason_categories` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION);
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS `memjet_device_swap_reason_defaults` (
            `deviceSwapReasonCategoryId` INT(11) NOT NULL,
            `dealerId`                   INT     NOT NULL,
            `deviceSwapReasonId`         INT(11) NOT NULL,
            PRIMARY KEY (`deviceSwapReasonCategoryId`, `dealerId`),
            INDEX `memjet_device_swap_defaults_ibk2_idx` (`deviceSwapReasonId` ASC),
            INDEX `memjet_device_swap_reason_category_defaults_ibkf1_idx` (`dealerId` ASC),
            CONSTRAINT `memjet_device_swap_reason_defaults_ibfk2`
            FOREIGN KEY (`deviceSwapReasonId`)
            REFERENCES `memjet_device_swap_reasons` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
            CONSTRAINT `memjet_device_swap_reason_defaults_ibkf1`
            FOREIGN KEY (`deviceSwapReasonCategoryId`)
            REFERENCES `memjet_device_swap_reason_categories` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
            CONSTRAINT `memjet_device_swap_reason_defaults_ibkf3`
            FOREIGN KEY (`dealerId`)
            REFERENCES `dealers` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION);
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS `memjet_device_instance_device_swap_reasons` (
            `memjetOptimizationId`   INT(11) NOT NULL,
            `deviceInstanceId`       INT(11) NOT NULL,
            `deviceSwapReasonId`     INT(11) NOT NULL,
            PRIMARY KEY (`memjetOptimizationId`, `deviceInstanceId`),
            INDEX `memjet_device_instance_device_swap_reasons_ibkf1_idx` (`memjetOptimizationId` ASC),
            INDEX `memjet_device_instance_device_swap_reasons_ibkf2_idx` (`deviceInstanceId` ASC),
            INDEX `memjet_device_instance_device_swap_reasons_ibkf3_idx` (`deviceSwapReasonId` ASC),
            CONSTRAINT `memjet_device_instance_device_swap_reasons_ibkf1`
            FOREIGN KEY (`memjetOptimizationId`)
            REFERENCES `memjet_optimizations` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
            CONSTRAINT `memjet_device_instance_device_swap_reasons_ibkf2`
            FOREIGN KEY (`deviceInstanceId`)
            REFERENCES `device_instances` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
            CONSTRAINT `memjet_device_instance_device_swap_reasons_ibkf3`
            FOREIGN KEY (`deviceSwapReasonId`)
            REFERENCES `memjet_device_swap_reasons` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE);
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS `memjet_device_swaps_page_thresholds` (
                            `masterDeviceId`   INT NOT NULL,
                            `dealerId`         INT NOT NULL,
                            `minimumPageCount` INT NULL,
                            `maximumPageCount` INT NULL,
                            PRIMARY KEY (`masterDeviceId`,`dealerId`),
                            INDEX `device_swaps_memjet_page_thresholds_ibfk1_idx` (`masterDeviceId` ASC),
                            INDEX `device_swaps_memjet_page_thresholds_ibfk2_idx` (`dealerId` ASC),
                            CONSTRAINT `device_swaps_memjet_page_thresholds_ibfk1`
                            FOREIGN KEY (`masterDeviceId`)
                            REFERENCES `master_devices` (`id`)
                                ON DELETE CASCADE
                                ON UPDATE CASCADE,
                            CONSTRAINT `device_swaps_memjet_page_thresholds_ibfk2`
                            FOREIGN KEY (`dealerId`)
                            REFERENCES `dealers` (`id`)
                                ON DELETE CASCADE
                                ON UPDATE CASCADE
                    );');

        $this->execute("INSERT INTO `memjet_device_swap_reason_categories` (`id`, `name`) VALUES
            (1, 'Flagged Devices'),
            (2, 'Device Has Replacement Device'),
            (3, 'Functionality Upgrade');
        ");

        $this->execute("INSERT INTO `memjet_device_swap_reasons` (`id`, `dealerId`, `deviceSwapReasonCategoryId`, `reason`) VALUES
            (1, 1, 1, 'Device not consistent with MPS program.  AMPV is significant.'),
            (2, 1, 2, 'Device has a high cost per page.'),
            (3, 2, 1, 'Device not consistent with MPS program.  AMPV is significant.'),
            (4, 2, 2, 'Device has a high cost per page.'),
            (5, 1, 3, 'Functionality upgrade.'),
            (6, 2, 3, 'Functionality upgrade');
        ");

        $this->execute("INSERT INTO `memjet_device_swap_reason_defaults` (`deviceSwapReasonCategoryId`, `dealerId`, `deviceSwapReasonId`) VALUES
            (1, 1, 1),
            (2, 1, 2),
            (1, 2, 3),
            (2, 2, 4),
            (3, 1, 5),
            (3, 2, 6);
        ");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("DROP TABLE IF EXISTS memjet_device_swaps_page_thresholds;");
        $this->execute("DROP TABLE IF EXISTS memjet_device_instance_device_swap_reasons;");
        $this->execute("DROP TABLE IF EXISTS memjet_device_swap_reason_defaults;");
        $this->execute("DROP TABLE IF EXISTS memjet_device_swap_reasons;");
        $this->execute("DROP TABLE IF EXISTS memjet_device_swap_reason_categories;");
    }
}