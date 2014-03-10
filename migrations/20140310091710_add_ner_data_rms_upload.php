<?php

use Phinx\Migration\AbstractMigration;

class AddNerDataRmsUpload extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute('INSERT INTO `rms_providers` (`id`, `name`) VALUES
                (5, \'NER Data\');');
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute('DELETE FROM `rms_providers` WHERE id=5;');
    }
}