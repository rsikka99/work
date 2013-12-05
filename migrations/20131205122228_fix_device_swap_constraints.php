<?php

use Phinx\Migration\AbstractMigration;

class FixDeviceSwapConstraints extends AbstractMigration
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
        $this->execute("ALTER TABLE device_swaps DROP FOREIGN KEY device_swaps_ibkf2");
        $this->execute("ALTER TABLE device_swaps ADD CONSTRAINT device_swaps_ibkf2
                        FOREIGN KEY (`dealerId`)
                        REFERENCES `dealers` (`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
                    ");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // No migrate down needed due to the nature of the up of this migration.
    }
}