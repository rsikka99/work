<?php

use Phinx\Migration\AbstractMigration;

class UpdateDealerFeaturesTable extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE dealer_features ADD CONSTRAINT dealer_features_ibfk_2
                        FOREIGN KEY (`dealerId`)
                        REFERENCES `dealers` (`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
                    ");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE dealer_features DROP FOREIGN KEY dealer_features_ibfk_2");
    }
}