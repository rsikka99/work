<?php

use Phinx\Migration\AbstractMigration;

class NamedReports extends AbstractMigration
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
    public function up ()
    {
        $this->execute('ALTER TABLE assessments ADD COLUMN `name` VARCHAR(255);
                ALTER TABLE quotes ADD COLUMN `name` VARCHAR(255);
        ');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('ALTER TABLE assessments DROP COLUMN `name`;
            ALTER TABLE quotes DROP COLUMN `name`;
        ');
    }
}