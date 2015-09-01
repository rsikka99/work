<?php

use Phinx\Migration\AbstractMigration;

class ExtHardware5 extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute(
"
alter table ext_peripheral add column appliesTo set('Laptop','Desktop','Server','Tablet') null;
alter table ext_computer add column ssd tinyint null default 0;
"
        );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('
alter table ext_peripheral drop column appliesTo;
alter table ext_computer drop column ssd;
');
    }
}