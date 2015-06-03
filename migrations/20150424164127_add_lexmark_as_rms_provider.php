<?php

use Phinx\Migration\AbstractMigration;

class AddLexmarkAsRmsProvider extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('INSERT INTO `rms_providers` (`id`, `name`) VALUES (9, \'Lexmark\');');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('DELETE FROM `rms_providers` WHERE id=9;');
    }
}