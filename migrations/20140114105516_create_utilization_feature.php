<?php

use Phinx\Migration\AbstractMigration;

class CreateUtilizationFeature extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("INSERT INTO features (id, name) VALUES ('assessment_utilization','Assessment Utilization')");
        $this->execute("INSERT INTO dealer_features (dealerId, featureId) VALUES (2, 'assessment_utilization')");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("DELETE FROM features WHERE id = 'assessment_utilization'");
    }
}