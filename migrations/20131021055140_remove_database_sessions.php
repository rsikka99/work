<?php

use Phinx\Migration\AbstractMigration;

class RemoveDatabaseSessions extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE user_sessions DROP FOREIGN KEY user_sessions_ibfk_2;");
        $this->execute("ALTER TABLE user_sessions ADD UNIQUE INDEX `user_sessions_unique_sessions` (`sessionId`);");
        $this->execute("DROP TABLE `sessions`;");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS `sessions` (
                            `id`       CHAR(32) NOT NULL,
                            `modified` INT(11)  NOT NULL,
                            `lifetime` INT(11)  NOT NULL,
                            `data`     TEXT     NULL,
                            PRIMARY KEY (`id`)
                        );
        ');

        $this->execute("SET foreign_key_checks = 0; ALTER TABLE user_sessions ADD CONSTRAINT `user_sessions_ibfk_2` FOREIGN KEY(`sessionId`) REFERENCES `sessions` (`id`); SET foreign_key_checks = 1;");

        $this->execute("ALTER TABLE user_sessions DROP INDEX `user_sessions_unique_sessions`;");
    }
}