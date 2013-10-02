<?php

use Phinx\Migration\AbstractMigration;

class AddUserDeviceAndToners extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('ALTER TABLE `master_devices`
ADD COLUMN `userId` INT(11) NULL DEFAULT 1,
ADD COLUMN `isSystemDevice` TINYINT(1) NOT NULL DEFAULT 0,
ADD CONSTRAINT `master_devices_ibfk_3` FOREIGN KEY (userId) REFERENCES `users` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
;');
        $this->execute('ALTER TABLE `toners`
ADD COLUMN `userId` INT(11) NULL DEFAULT 1,
ADD COLUMN `isSystemDevice` TINYINT(1) NOT NULL DEFAULT 0,
ADD CONSTRAINT `toners_ibfk_4` FOREIGN KEY (userId) REFERENCES `users` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
;');

        $this->execute('ALTER TABLE `device_toners`
ADD COLUMN `userId` INT(11) NULL DEFAULT 1,
ADD COLUMN `isSystemDevice` TINYINT(1) NOT NULL DEFAULT 0,
ADD CONSTRAINT `device_toners_ibfk_3` FOREIGN KEY (userId) REFERENCES `users` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
;');

        $this->execute('UPDATE `master_devices` SET `isSystemDevice` = 1');
        $this->execute('UPDATE `toners` SET `isSystemDevice` = 1');
        $this->execute('UPDATE `device_toners` SET `isSystemDevice` = 1');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('ALTER TABLE `master_devices` DROP FOREIGN KEY master_devices_ibfk_3;');
        $this->execute('ALTER TABLE `toners` DROP FOREIGN KEY `toners_ibfk_4`;');
        $this->execute('ALTER TABLE `device_toners` DROP FOREIGN KEY device_toners_ibfk_3;');

        $this->execute('ALTER TABLE `master_devices`
DROP COLUMN `userId`,
DROP COLUMN `isSystemDevice`
;');

        $this->execute('ALTER TABLE `device_toners`
DROP COLUMN `userId`,
DROP COLUMN `isSystemDevice`
;');

        $this->execute('ALTER TABLE `device_toners`
DROP COLUMN `userId`,
DROP COLUMN `isSystemDevice`
;');
    }
}