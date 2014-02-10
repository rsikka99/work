<?php

use Phinx\Migration\AbstractMigration;

class AddMemjetOptimizationOptimizedTargetCostPerPage extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("ALTER TABLE memjet_optimization_settings ADD COLUMN optimizedTargetMonochromeCostPerPage DOUBLE,ADD COLUMN optimizedTargetColorCostPerPage DOUBLE");

        // Set System memjet optimization settings settings
        $this->execute("UPDATE memjet_optimization_settings SET optimizedTargetMonochromeCostPerPage=0.02, optimizedTargetColorCostPerPage=0.1 WHERE id = 1");

        // Set Dealer Settings to have defaults
        $this->execute("UPDATE memjet_optimization_settings
        JOIN dealer_settings ON dealer_settings.memjetOptimizationSettingId = memjet_optimization_settings.id
        SET optimizedTargetMonochromeCostPerPage=0.02, optimizedTargetColorCostPerPage=0.1");

        // Set current reports settings to use the defaults
        $this->execute("UPDATE memjet_optimization_settings
        JOIN memjet_optimizations ON memjet_optimizations.memjetOptimizationSettingId = memjet_optimization_settings.id
        SET optimizedTargetMonochromeCostPerPage=0.02, optimizedTargetColorCostPerPage=0.1");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("ALTER TABLE memjet_optimization_settings DROP optimizedTargetMonochromeCostPerPage, DROP optimizedTargetColorCostPerPage");
    }
}