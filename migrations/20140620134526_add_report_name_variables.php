<?php

use Phinx\Migration\AbstractMigration;

class AddReportNameVariables extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up ()
    {
        $dealerBrandingTable = $this->table('dealer_branding');
        $dealerBrandingTable
            ->addColumn('assessmentTitle', 'string')
            ->addColumn('customerCostAnalysisTitle', 'string')
            ->addColumn('customerOptimizationTitle', 'string')
            ->addColumn('healthCheckTitle', 'string')
            ->addColumn('leaseQuoteTitle', 'string')
            ->addColumn('purchaseQuoteTitle', 'string')
            ->addColumn('solutionTitle', 'string')
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $dealerBrandingTable = $this->table('dealer_branding');
        $dealerBrandingTable
            ->removeColumn('assessmentTitle', 'string')
            ->removeColumn('customerCostAnalysisTitle', 'string')
            ->removeColumn('customerOptimizationTitle', 'string')
            ->removeColumn('healthCheckTitle', 'string')
            ->removeColumn('leaseQuoteTitle', 'string')
            ->removeColumn('purchaseQuoteTitle', 'string')
            ->removeColumn('solutionTitle', 'string')
            ->save();
    }
}