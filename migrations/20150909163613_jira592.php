<?php

use Phinx\Migration\AbstractMigration;

class Jira592 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("
drop table if EXISTS history;
CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dt` timestamp not null default CURRENT_TIMESTAMP,
  `userId` int(11) null default null,
  `masterDeviceId` int(11) null default null,
  `tonerId` int(11) null default null,
  `extHardwareId` int(11) null default null,
  `dealerId` int(11) null default null,
  `action` varchar(255) null default null,
  PRIMARY KEY (`id`),
  KEY (`userId`),
  KEY (`masterDeviceId`),
  KEY (`tonerId`),
  KEY (`extHardwareId`),
  KEY (`dealerId`)
) ENGINE=InnoDB;

ALTER TABLE `history`
  ADD CONSTRAINT FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE set null ON UPDATE CASCADE,
  ADD CONSTRAINT FOREIGN KEY (`masterDeviceId`) REFERENCES `master_devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FOREIGN KEY (`tonerId`) REFERENCES `toners` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FOREIGN KEY (`extHardwareId`) REFERENCES `ext_hardware` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT FOREIGN KEY (`dealerId`) REFERENCES `dealers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

insert into history (masterDeviceId, userId, dt, action)
select id, userId, dateCreated, 'Created' from master_devices where isSystemDevice=1;

insert into history (masterDeviceId, userId, dt, dealerId, action)
select m.id, userId, dateCreated, u.dealerId, 'Created' from master_devices m join users u on m.userId=u.id where isSystemDevice=0;

");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("
drop table if EXISTS history;
");
    }
}