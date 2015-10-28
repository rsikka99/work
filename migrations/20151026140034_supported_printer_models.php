<?php

use Phinx\Migration\AbstractMigration;

class SupportedPrinterModels extends AbstractMigration
{
    public function up()
    {
        $this->execute("alter table clients add `notSupportedMasterDevices` text null default null");
        $this->execute("alter table clients add `deviceGroup` varchar(255) null default null");

        $this->execute("alter table shop_settings add `rmsUri` varchar(255) null default null");
    }

    public function down()
    {
        $this->execute("alter table clients drop `notSupportedMasterDevices`");
        $this->execute("alter table clients drop `deviceGroup`");

        $this->execute("alter table shop_settings drop `rmsUri`");
    }
}