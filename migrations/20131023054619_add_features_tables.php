<?php

use Phinx\Migration\AbstractMigration;

class AddFeaturesTables extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("CREATE TABLE features (
                            id VARCHAR(255) PRIMARY KEY
                        )");

        $this->execute("CREATE TABLE dealer_features (
                            dealerId INTEGER NOT NULL,
                            featureId VARCHAR(255) NOT NULL,
                            PRIMARY KEY(dealerId, featureId),
                            CONSTRAINT dealer_features_ibfk_1 FOREIGN KEY (featureId) REFERENCES features (id)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE
                        );");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("DROP TABLE dealer_features");
        $this->execute("DROP TABLE features");
    }
}