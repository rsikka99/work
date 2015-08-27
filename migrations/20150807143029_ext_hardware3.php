<?php

use Phinx\Migration\AbstractMigration;

class ExtHardware3 extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute(
"
CREATE TABLE IF NOT EXISTS `ext_peripheral` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;
"
        );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('
drop TABLE IF EXISTS `ext_peripheral`;
');
    }
}