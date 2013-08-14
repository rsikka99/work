<?php

use Phinx\Migration\AbstractMigration;

class AddLastSeenToUserTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
     */

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('CREATE TABLE `user_activities` (
            `id`        INT(11) NOT NULL AUTO_INCREMENT,
            `userId` 	INT(11) NOT NULL,
            `lastSeen`  DATETIME NOT NULL,
            `url` 	    VARCHAR(255),
            PRIMARY KEY (`id`),
            CONSTRAINT `user_activities_ibfk_1`
            FOREIGN KEY (`userId`)
            REFERENCES `users`(`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            );'
        );

        $this->execute('ALTER TABLE `users`
            ADD `lastSeen` DATETIME;'
        );
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('DROP TABLE `user_activities`');
        $this->execute('ALTER TABLE `users`
            DROP COLUMN `lastSeen;
        ');
    }
}