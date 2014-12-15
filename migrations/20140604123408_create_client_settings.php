<?php

use Phinx\Migration\AbstractMigration;

class CreateClientSettings extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        /**
         * Drop old constraints
         */
        $assessments = $this->table('assessments');
        $assessments
            ->dropForeignKey('assessmentSettingId', 'assessments_ibfk_4')
            ->removeColumn('assessmentSettingId')
            ->save();

        $hardwareOptimizations = $this->table('hardware_optimizations');
        $hardwareOptimizations
            ->dropForeignKey('hardwareOptimizationSettingId', 'hardware_optimizations_ibfk_3')
            ->removeColumn('hardwareOptimizationSettingId')
            ->save();

        $healthchecks = $this->table('healthchecks');
        $healthchecks
            ->dropForeignKey('healthcheckSettingId', 'healthchecks_ibfk_4')
            ->removeColumn('healthcheckSettingId')
            ->save();

        $memjetOptimizations = $this->table('memjet_optimizations');
        $memjetOptimizations
            ->dropForeignKey('memjetOptimizationSettingId', 'memjet_optimization_ibfk_3')
            ->removeColumn('memjetOptimizationSettingId')
            ->save();

        /**
         * Drop old tables
         */
        $this->dropTable('dealer_settings');
        $this->dropTable('user_settings');
        $this->dropTable('assessment_settings');
        $this->dropTable('hardware_optimization_settings');
        $this->dropTable('healthcheck_settings');
        $this->dropTable('memjet_optimization_settings');
        $this->dropTable('quote_settings');
        $this->dropTable('report_survey_settings');
        $this->dropTable('survey_settings');


        /**
         * Fleet Settings
         */
        $fleetSettings = $this->table('fleet_settings');
        $fleetSettings
            ->addColumn('useDevicePageCoverages', 'boolean')
            ->addColumn('defaultMonochromeCoverage', 'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('defaultColorCoverage', 'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('adminCostPerPage', 'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('defaultLaborCostPerPage', 'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('defaultPartsCostPerPage', 'decimal', array('precision' => '18', 'scale' => '9'))

            // Monochrome Toner Preferences
            ->addColumn('monochromeTonerVendorRankingSetId', 'integer', array('null' => true))
            ->addForeignKey(array('monochromeTonerVendorRankingSetId'), 'toner_vendor_ranking_sets', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            // Color Toner Preferences
            ->addColumn('colorTonerVendorRankingSetId', 'integer', array('null' => true))
            ->addForeignKey(array('colorTonerVendorRankingSetId'), 'toner_vendor_ranking_sets', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->save();

        /**
         * Generic Settings used for assessing costs
         */
        $genericSettings = $this->table('generic_settings');
        //@formatter:off
        $genericSettings
            ->addColumn('defaultEnergyCost',                  'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('defaultMonthlyLeasePayment',         'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('defaultPrinterCost',                 'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('leasedMonochromeCostPerPage',        'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('leasedColorCostPerPage',             'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('mpsMonochromeCostPerPage',           'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('mpsColorCostPerPage',                'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('tonerPricingMargin',                 'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('targetMonochromeCostPerPage',        'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('targetColorCostPerPage',             'decimal', array('precision' => '18', 'scale' => '9'))
            ->save();
        //@formatter:on

        /**
         * Hardware Optimization
         */
        $optimizationSettings = $this->table('optimization_settings');
        $optimizationSettings
            // The cost per page we are looking at charging the customer without any fleet optimization


            // The cost per page we want to charge the customer if they optimize their fleet
            ->addColumn('optimizedTargetMonochromeCostPerPage', 'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('optimizedTargetColorCostPerPage', 'decimal', array('precision' => '18', 'scale' => '9'))

            // Really used for determining the minimum cost savings when replacing for the purpose of saving money (not functionality)
            ->addColumn('costThreshold', 'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('autoOptimizeCost', 'boolean')

            // Really used for determining the maximum loss when replacing for the purpose of saving upgrading (not money)
            ->addColumn('lossThreshold', 'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('autoOptimizeFunctionality', 'boolean')

            // Used to figure out how much print volume will change from black over to color when a device gets upgraded
            ->addColumn('blackToColorRatio', 'decimal', array('precision' => '18', 'scale' => '9'))

            // Mono Toner Preferences (These are used to state what's going in the replacement devices)
            ->addColumn('monochromeTonerVendorRankingSetId', 'integer', array('null' => true))
            ->addForeignKey(array('monochromeTonerVendorRankingSetId'), 'toner_vendor_ranking_sets', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            // Color Toner Preferences
            ->addColumn('colorTonerVendorRankingSetId', 'integer', array('null' => true))
            ->addForeignKey(array('colorTonerVendorRankingSetId'), 'toner_vendor_ranking_sets', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->save();

        /**
         * Quote
         */
        $quoteSettings = $this->table('quote_settings');
        $quoteSettings
            ->addColumn('defaultDeviceMargin', 'decimal', array('precision' => '18', 'scale' => '9'))
            ->addColumn('defaultPageMargin', 'decimal', array('precision' => '18', 'scale' => '9'))
            ->save();


        /**
         * Client Settings Table
         */
        $clientSettings = $this->table('client_settings', array('id' => false, 'primary_key' => 'clientId'));
        $clientSettings
            ->addColumn('clientId', 'integer')
            ->addForeignKey(array('clientId'), 'clients', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('currentFleetSettingsId', 'integer')
            ->addForeignKey(array('currentFleetSettingsId'), $fleetSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('proposedFleetSettingsId', 'integer')
            ->addForeignKey(array('proposedFleetSettingsId'), $fleetSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('genericSettingsId', 'integer')
            ->addForeignKey(array('genericSettingsId'), $genericSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('optimizationSettingsId', 'integer')
            ->addForeignKey(array('optimizationSettingsId'), $optimizationSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('quoteSettingsId', 'integer')
            ->addForeignKey(array('quoteSettingsId'), $quoteSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->save();

        /**
         * User Settings Table
         */
        $userSettings = $this->table('user_settings', array('id' => false, 'primary_key' => 'userId'));
        $userSettings
            ->addColumn('userId', 'integer')

            ->addColumn('currentFleetSettingsId', 'integer')
            ->addForeignKey(array('currentFleetSettingsId'), $fleetSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('proposedFleetSettingsId', 'integer')
            ->addForeignKey(array('proposedFleetSettingsId'), $fleetSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('genericSettingsId', 'integer')
            ->addForeignKey(array('genericSettingsId'), $genericSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('optimizationSettingsId', 'integer')
            ->addForeignKey(array('optimizationSettingsId'), $optimizationSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('quoteSettingsId', 'integer')
            ->addForeignKey(array('quoteSettingsId'), $quoteSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->save();

        /**
         * Dealer Settings Table
         */
        $dealerSettings = $this->table('dealer_settings', array('id' => false, 'primary_key' => 'dealerId'));
        $dealerSettings
            ->addColumn('dealerId', 'integer')

            ->addColumn('currentFleetSettingsId', 'integer')
            ->addForeignKey(array('currentFleetSettingsId'), $fleetSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('proposedFleetSettingsId', 'integer')
            ->addForeignKey(array('proposedFleetSettingsId'), $fleetSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('genericSettingsId', 'integer')
            ->addForeignKey(array('genericSettingsId'), $genericSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('optimizationSettingsId', 'integer')
            ->addForeignKey(array('optimizationSettingsId'), $optimizationSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))

            ->addColumn('quoteSettingsId', 'integer')
            ->addForeignKey(array('quoteSettingsId'), $quoteSettings, 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->save();
    }


    /**
     * Migrate Down.
     */
    public function down ()
    {
        /**
         * Drop the new tables
         */
        $this->dropTable('dealer_settings');
        $this->dropTable('user_settings');
        $this->dropTable('client_settings');
        $this->dropTable('optimization_settings');
        $this->dropTable('generic_assessment_settings');
        $this->dropTable('quote_settings');
        $this->dropTable('fleet_settings');

        /**
         * Create the old tables
         */
        // TODO lrobert: Create the old tables

        /**
         * Establish the old relationships
         */
        // TODO lrobert: Create the old relationships
    }
}