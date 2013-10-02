<?php

use Phinx\Migration\AbstractMigration;

class AddMasterDeviceAdministratorRole extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("INSERT INTO `roles` VALUES (4, 'Master Device Administrator', 1)");
        $this->execute("INSERT INTO `user_roles` VALUES (1, 4)");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('DELETE FROM `roles` where `id` = 4');
    }
}