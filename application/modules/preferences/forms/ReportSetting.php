<?php
class Preferences_Form_ReportSetting extends Twitter_Bootstrap_Form_Horizontal
{


    public function init ()
    {
        $this->setMethod('post');

        $this->addElement('text', 'pageCoverageMonochrome', array('label' => 'Page Coverage Monochrome'));
        $this->addElement('text', 'pageCoverageColor', array('label' => 'Page Coverage Color'));

        $this->addDisplayGroup(array(
                                    'pageCoverageMonochrome',
                                    'pageCoverageColor'
                               ), 'survey',
            array(
                 'legend' => 'Survey Settings'
            ));

        $this->addElement('text', 'pricingMargin', array('label' => 'Pricing Margin'));
        $this->addElement('text', 'monthlyLeasePayment', array('label' => 'Monthly Lease Payment'));
        $this->addElement('text', 'printerCost', array('label' => 'Default Printer Cost'));
        $this->addElement('text', 'leasedMonochromeCost', array('label' => 'Leased Monochrome Cost'));
        $this->addElement('text', 'leasedColorCost', array('label' => 'Leased Color Cost'));
        $this->addElement('text', 'monochromeCost', array('label' => 'Monochrome Cost'));
        $this->addElement('text', 'colorCost', array('label' => 'Color Cost'));
        $this->addElement('text', 'energyCost', array('label' => 'Energy Cost'));
        $this->addElement('text', 'pricingConfigId', array('label' => 'Toner Preference'));

        $this->addDisplayGroup(array('pricingMargin',
                                     'monthlyLeasePayment',
                                     'printerCost',
                                     'leasedMonochromeCost',
                                     'monochromeCost',
                                     'colorCost',
                                     'energyCost',
                                     'pricingConfigId'
                               ), 'assessment',
            array(
                 'legend' => 'Assessment Settings',
            ));

        $this->addElement('text', 'pageCoverageMonochrome', array('label' => 'Page Coverage Monochrome'));
        $this->addElement('text', 'pageCoverageColor', array('label' => 'Page Coverage Color'));
        $this->addElement('text', 'adminCostPerPage', array('label' => 'Admin Cost'));
        $this->addElement('text', 'serviceCostPerPage', array('label' => 'Service Cost'));

        $this->addDisplayGroup(array('pageCoverageMonochrome', 'pageCoverageColor', 'adminCostPerPage', 'serviceCostPerPage'), 'grossMargin', array('legend' => 'Gross Margin Settings'));


        $this->addElement('text', '', array('label' => ''));
    }
}