<?php

use Phinx\Migration\AbstractMigration;

class AcmManufacturer extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->query("replace into manufacturers set id=77, fullname='ACM Technologies', displayname='ACM'");
        $this->query("replace into manufacturers set id=78, fullname='ECOPlus', displayname='ECOPlus'");
        $this->query("replace into toner_vendor_manufacturers set manufacturerId=77");
        $this->query("replace into toner_vendor_manufacturers set manufacturerId=78");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}