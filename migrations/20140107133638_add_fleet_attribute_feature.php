<?php

use Phinx\Migration\AbstractMigration;

class AddFleetAttributeFeature extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("INSERT INTO features (id, name) VALUES ('assessment_fleet_attributes','Assessment Fleet Attributes')");
        $this->execute("INSERT INTO dealer_features (dealerId, featureId) VALUES (2, 'assessment_fleet_attributes')");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("DELETE FROM features WHERE id = 'assessment_fleet_attributes'");
    }
}