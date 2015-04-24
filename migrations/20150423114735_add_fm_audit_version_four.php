<?php

use Phinx\Migration\AbstractMigration;

class AddFmAuditVersionFour extends AbstractMigration
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
        $this->execute('INSERT INTO `rms_providers` (`id`, `name`) VALUES (8, \'FM Audit 4.x\');');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		$this->execute('DELETE FROM `rms_providers` WHERE id=8;');
    }
}
