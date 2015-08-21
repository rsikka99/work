<?php

use Phinx\Migration\AbstractMigration;

class ExtHardware2 extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute(
"
alter table ext_computer drop column ledDisplay;
alter table ext_computer drop column usb3;
alter table ext_computer drop column usbDescription;
alter table ext_computer add column usb enum('USB 1.x', 'USB 2.0','USB 3.0', 'USB 3.1', 'USB Type-C') null default null;
alter table ext_computer add column displayType enum('TFT-LCD','LED','IPS','VA','Plasma') null default null;

ALTER TABLE `ext_computer` CHANGE `os` `os` ENUM( 'Windows 7','Windows 8','Windows 10','Mac OS X','Linux','Windows Phone','iOS','Android','Windows Server 2008','Windows Server 2012','Windows Server 2016' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL
"
        );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('
alter table ext_computer add column ledDisplay int null;
alter table ext_computer add column usb3 int null;
alter table ext_computer add column usbDescription varchar(255) null;
alter table ext_computer drop column usb;
alter table ext_computer drop column displayType;
');
    }
}