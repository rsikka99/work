<?php

use Phinx\Migration\AbstractMigration;

class DealerLeased extends AbstractMigration
{
    public function up()
    {
        $this->execute('alter table dealer_master_device_attributes add isLeased tinyint not null default 0');
        $this->execute('alter table dealer_master_device_attributes add leasedTonerYield int null default null');
        $this->execute('
replace into dealer_master_device_attributes
select `masterDeviceId`, `dealerId`, `laborCostPerPage`, `partsCostPerPage`, `leaseBuybackPrice`, m.isLeased, m.leasedTonerYield
from dealer_master_device_attributes dd
    join master_devices m on dd.masterDeviceId=m.id
');
        $this->execute('alter table master_devices drop isLeased');
        $this->execute('alter table master_devices drop leasedTonerYield');
    }

    public function down()
    {
        $this->execute('alter table dealer_master_device_attributes drop isLeased');
        $this->execute('alter table dealer_master_device_attributes drop leasedTonerYield');

        $this->execute('alter table master_devices add isLeased tinyint not null default 0');
        $this->execute('alter table master_devices add leasedTonerYield int null default null');
    }
}
