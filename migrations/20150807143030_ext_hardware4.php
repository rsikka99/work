<?php

use Phinx\Migration\AbstractMigration;

class ExtHardware4 extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute(
"
alter table ext_hardware add column grade enum('Good','Better','Best')
"
        );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('
alter table ext_hardware drop column grade;
');
    }
}