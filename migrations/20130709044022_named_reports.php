<?php

use Phinx\Migration\AbstractMigration;

class NamedReports extends AbstractMigration
{
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