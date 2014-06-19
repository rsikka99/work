<?php

use Phinx\Migration\AbstractMigration;

class AddDealerReportCustomizationVariables extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $dealerBrandingTable = $this->table('dealer_branding');
        $dealerBrandingTable
            ->addColumn('dealerId', 'integer')
            ->addForeignKey('dealerId', 'dealers', 'id', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
            ->addColumn('dealerName', 'string')
            ->addColumn('shortDealerName', 'string')
            ->addColumn('mpsProgramName', 'string')
            ->addColumn('shortMpsProgramName', 'string')
            ->addColumn('jitProgramName', 'string')
            ->addColumn('shortJitProgramName', 'string')

            ->addColumn('titlePageTitleFontColor', 'string')
            ->addColumn('titlePageTitleBackgroundColor', 'string')
            ->addColumn('titlePageInformationFontColor', 'string')
            ->addColumn('titlePageInformationBackgroundColor', 'string')
            ->addColumn('h1FontColor', 'string')
            ->addColumn('h1BackgroundColor', 'string')
            ->addColumn('h2FontColor', 'string')
            ->addColumn('h2BackgroundColor', 'string')

            ->addColumn('graphCustomerColor', 'string')
            ->addColumn('graphDealerColor', 'string')
            ->addColumn('graphPositiveColor', 'string')
            ->addColumn('graphNegativeColor', 'string')

            ->addColumn('graphPurchasedDeviceColor', 'string')
            ->addColumn('graphLeasedDeviceColor', 'string')
            ->addColumn('graphExcludedDeviceColor', 'string')
            ->addColumn('graphIndustryAverageColor', 'string')

            ->addColumn('graphKeepDeviceColor', 'string')
            ->addColumn('graphReplacedDeviceColor', 'string')
            ->addColumn('graphDoNotRepairDeviceColor', 'string')
            ->addColumn('graphRetireDeviceColor', 'string')

            ->addColumn('graphManagedDeviceColor', 'string')
            ->addColumn('graphManageableDeviceColor', 'string')
            ->addColumn('graphFutureReviewDeviceColor', 'string')
            ->addColumn('graphJitCompatibleDeviceColor', 'string')
            ->addColumn('graphCompatibleDeviceColor', 'string')
            ->addColumn('graphNotCompatibleDeviceColor', 'string')

            ->addColumn('graphCurrentSituationColor', 'string')
            ->addColumn('graphNewSituationColor', 'string')

            ->addColumn('graphAgeOfDevices1', 'string')
            ->addColumn('graphAgeOfDevices2', 'string')
            ->addColumn('graphAgeOfDevices3', 'string')
            ->addColumn('graphAgeOfDevices4', 'string')

            ->addColumn('graphMonoDeviceColor', 'string')
            ->addColumn('graphColorDeviceColor', 'string')
            ->addColumn('graphCopyCapableDeviceColor', 'string')
            ->addColumn('graphDuplexCapableDeviceColor', 'string')
            ->addColumn('graphFaxCapableDeviceColor', 'string')
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->dropTable('dealer_branding');
    }
}