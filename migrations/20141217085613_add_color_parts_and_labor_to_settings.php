<?php

use Phinx\Migration\AbstractMigration;

class AddColorPartsAndLaborToSettings extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $fleetSettings = $this->table('fleet_settings');
        $fleetSettings
            ->renameColumn('defaultLaborCostPerPage', 'defaultMonochromeLaborCostPerPage')
            ->renameColumn('defaultPartsCostPerPage', 'defaultMonochromePartsCostPerPage')
            ->addColumn('defaultColorLaborCostPerPage', 'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('defaultColorPartsCostPerPage', 'decimal', array('precision' => '18', 'scale' => '9'))
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $fleetSettings = $this->table('fleet_settings');
        $fleetSettings
            ->renameColumn('defaultMonochromeLaborCostPerPage', 'defaultLaborCostPerPage')
            ->renameColumn('defaultMonochromePartsCostPerPage', 'defaultPartsCostPerPage')
            ->removeColumn('defaultColorLaborCostPerPage')
            ->removeColumn('defaultColorPartsCostPerPage')
            ->update();
    }
}