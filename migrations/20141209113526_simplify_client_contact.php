<?php

use Phinx\Migration\AbstractMigration;

class SimplifyClientContact extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $contactTable = $this->table('contacts');

        $contactTable
            ->addColumn('phoneNumber', 'string')
            ->update();

        $this->execute('UPDATE contacts SET phoneNumber=CONCAT(countryCode, areaCode, exchangeCode, number, extension) where TRUE');

        $contactTable
            ->removeColumn('countryCode')
            ->removeColumn('areaCode')
            ->removeColumn('exchangeCode')
            ->removeColumn('number')
            ->removeColumn('extension')
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $contactTable = $this->table('contacts');

        $contactTable
            ->removeColumn('phoneNumber')
            ->addColumn('countryCode', 'string')
            ->addColumn('areaCode', 'string')
            ->addColumn('exchangeCode', 'string')
            ->addColumn('number', 'string')
            ->addColumn('extension', 'string')
            ->update();
    }
}