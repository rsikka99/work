<?php

use Phinx\Migration\AbstractMigration;

class AddCustomerPricing extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS `client_toner_attributes` (
    `tonerId`   INT(11)      NOT NULL,
    `clientId`  INT(11)      NOT NULL,
    `cost`      DOUBLE       NULL,
    `clientSku` VARCHAR(255) NULL,
    PRIMARY KEY (`tonerId`, `clientId`),
    INDEX `client_toner_attributes_ibfk_1_idx` (`tonerId` ASC),
    INDEX `client_toner_attributes_ibfk_2_idx` (`clientId` ASC),
    CONSTRAINT `client_toner_attributes_ibfk_1`
    FOREIGN KEY (`tonerId`)
    REFERENCES `toners` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `client_toner_attributes_ibfk_2`
    FOREIGN KEY (`clientId`)
    REFERENCES `clients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('DROP TABLE IF EXISTS `client_toner_attributes`;');
    }
}