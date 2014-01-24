<?php

use Phinx\Migration\AbstractMigration;

class AddCustomerCostPerPages extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE healthcheck_settings ADD (
        `customerMonochromeCostPerPage` DOUBLE NULL DEFAULT NULL,
        `customerColorCostPerPage` DOUBLE NULL DEFAULT NULL)
        ");

        // Set System Health Check settings
        $this->execute("UPDATE healthcheck_settings SET customerMonochromeCostPerPage=0.02, customerColorCostPerPage=0.09 WHERE id = 1");

        // Set Dealer Settings to have defaults
        $this->execute("UPDATE healthcheck_settings
        JOIN dealer_settings ON dealer_settings.healthcheckSettingId = healthcheck_settings.id
        SET customerMonochromeCostPerPage=0.02, customerColorCostPerPage=0.09");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE healthcheck_settings DROP `customerMonochromeCostPerPage`, DROP `customerColorCostPerPage`");
    }
}