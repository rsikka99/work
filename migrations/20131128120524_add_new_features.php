<?php

use Phinx\Migration\AbstractMigration;

class AddNewFeatures extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up ()
    {
        $this->execute("INSERT INTO features (id, name) VALUES
        ('hardware_optimization','Hardware Optimization'),
        ('hardware_quote','Hardware Quote'),
        ('healthcheck','Health Check'),
        ('assessment','Assessment'),
        ('assessment_customer_cost_analysis','Assessment Customer Cost Analysys'),
        ('assessment_gross_margin','Assessment Gross Margin'),
        ('assessment_toner_vendor_gross_margin','Assessment Toner Vendor Gross Margin'),
        ('assessment_jit_supply_and_toner_sku_report','Assessment JIT Supply And Toner Sku Report'),
        ('assessment_old_device_list','Assessment Old Device List'),
        ('assessment_printing_device_list','Assessment Printing Device list'),
        ('assessment_solution','Assessment Solution'),
        ('assessment_lease_buyback','Assessment Lease Buyback')
        ");

        $this->execute("INSERT INTO dealer_features (dealerId, featureId) VALUES
        (2, 'hardware_optimization'),
        (2, 'hardware_quote'),
        (2, 'healthcheck'),
        (2, 'assessment'),
        (2, 'assessment_customer_cost_analysis'),
        (2, 'assessment_gross_margin'),
        (2, 'assessment_toner_vendor_gross_margin'),
        (2, 'assessment_jit_supply_and_toner_sku_report'),
        (2, 'assessment_old_device_list'),
        (2, 'assessment_printing_device_list'),
        (2, 'assessment_solution'),
        (2, 'assessment_lease_buyback')
        ");
    }

    /**
     * Migrate Down.
     */
    public function down ()
    {
        $this->execute("DELETE FROM features WHERE id != 'hardware_optimization_memjet'");
    }
}