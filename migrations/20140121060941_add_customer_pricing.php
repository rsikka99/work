<?php

use Phinx\Migration\AbstractMigration;

class AddCustomerPricing extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS `client_toner_orders` (
    `id`                 INT(11)      NOT NULL AUTO_INCREMENT,
    `tonerId`            INT(11)      NULL,
    `clientId`           INT(11)      NOT NULL,
    `orderNumber`        VARCHAR(255) NULL,
    `oemSku`             VARCHAR(255) NULL,
    `dealerSku`          VARCHAR(255) NULL,
    `clientSku`          VARCHAR(255) NULL,
    `cost`               DECIMAL      NULL,
    `quantity`           INT(11)      NULL,
    `dateOrdered`        DATE         NOT NULL,
    `dateShipped`        DATE         NOT NULL,
    `dateReconciled`     DATE         NOT NULL,
    `replacementTonerId` INT(11)      NULL,
    PRIMARY KEY (`id`),
    INDEX `client_toner_attributes_ibfk_1_idx` (`tonerId` ASC),
    INDEX `client_toner_attributes_ibfk_2_idx` (`clientId` ASC),
    INDEX `client_toner_attributes_ibfk_3_idx` (`replacementTonerId` ASC),
    CONSTRAINT `client_toner_attributes_ibfk_1`
    FOREIGN KEY (`tonerId`)
    REFERENCES `toners` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT `client_toner_attributes_ibfk_2`
    FOREIGN KEY (`clientId`)
    REFERENCES `clients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `client_toner_attributes_ibfk_3`
    FOREIGN KEY (`replacementTonerId`)
    REFERENCES `toners` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('DROP TABLE IF EXISTS `client_toner_orders`;');
    }
}