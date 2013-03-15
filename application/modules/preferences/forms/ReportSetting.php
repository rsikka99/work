<?php
class Preferences_Form_ReportSetting extends Twitter_Bootstrap_Form_Horizontal
{
    public function init ()
    {
        $this->setMethod('post');


        $this->_addClassNames('reportSettingsForm');

        $reportSettings = Proposalgen_Model_Mapper_Report_Setting::getInstance()->find(1);

        // Survey Elements
        $element = $this->createElement('text', 'pageCoverageMono', array('label' => 'Page Coverage Monochrome', 'description' => 'defaultValue',))->addValidator('greaterThan', true, array('min' => 0));
        $this->addElement('text', 'pageCoverageColor', array('label' => 'Page Coverage Color', 'description' => "{$reportSettings->actualPageCoverageColor}%"));
        // Assessment Elements
        $this->addElement('text', 'assessmentReportMargin', array('label' => 'Pricing Margin', 'description' => 'defaultValue',));
        $this->addElement('text', 'monthlyLeasePayment', array('label' => 'Monthly Lease Payment', 'description' => 'defaultValue',));
        $this->addElement('text', 'defaultPrinterCost', array('label' => 'Default Printer Cost', 'description' => 'defaultValue',));
        $this->addElement('text', 'leasedBwCostPerPage', array('label' => 'Leased Monochrome Cost', 'description' => 'defaultValue',));
        $this->addElement('text', 'leasedColorCostPerPage', array('label' => 'Leased Color Cost', 'description' => 'defaultValue',));
        $this->addElement('text', 'mpsBwCostPerPage', array('label' => 'Monochrome Cost', 'description' => 'defaultValue',));
        $this->addElement('text', 'mpsColorCostPerPage', array('label' => 'Color Cost', 'description' => 'defaultValue',));
        $this->addElement('text', 'kilowattsPerHour', array('label' => 'Energy Cost', 'description' => 'defaultValue',));
        $this->addElement('text', 'assessmentPricingConfigId', array('label' => 'Toner Preference', 'description' => 'defaultValue',));
        // Gross margin elements
        $this->addElement('text', 'actualPageCoverageMono', array('label' => 'Page Coverage Monochrome', 'description' => 'defaultValue',));
        $this->addElement('text', 'actualPageCoverageColor', array('label' => 'Page Coverage Color', 'description' => 'defaultValue',));
        $this->addElement('text', 'adminCostPerPage', array('label' => 'Admin Cost', 'description' => 'defaultValue',));
        $this->addElement('text', 'serviceCostPerPage', array('label' => 'Service Cost', 'description' => 'defaultValue',));
        // Hardware Optimization Elements
        $this->addElement('text', 'costThreshold', array('label' => 'Cost Threshold', 'description' => 'defaultValue',));
        $this->addElement('text', 'targetMonochromeCostPerPage', array('label' => 'Target Monochrome Cost Per Page', 'description' => 'defaultValue',));
        $this->addElement('text', 'targetCostPerPage', array('label' => 'Target Color Cost Per Page', 'description' => 'defaultValue',));

        $this->addDisplayGroup(array($element, 'pageCoverageColor'), 'survey', array('legend' => 'Survey Settings'));
        $this->addDisplayGroup(array('assessmentReportMargin',
                                     'monthlyLeasePayment',
                                     'defaultPrinterCost',
                                     'leasedBwCostPerPage',
                                     'leasedColorCostPerPage',
                                     'mpsBwCostPerPage',
                                     'mpsColorCostPerPage',
                                     'kilowattsPerHour',
                                     'assessmentPricingConfigId'
                               ), 'assessment', array('legend' => 'Assessment Settings',));
        $this->addDisplayGroup(array('actualPageCoverageMono', 'actualPageCoverageColor', 'adminCostPerPage', 'serviceCostPerPage'), 'grossMargin', array('legend' => 'Gross Margin Settings'));
        $this->addDisplayGroup(array('costThreshold', 'targetMonochromeCostPerPage', 'targetCostPerPage'), 'hardwareOptimization', array('legend' => 'Hardware Profitability Settings'));

        $this->addElement('submit', 'submit');
        $this->setElementDecorators(array(
                                         array('FieldSize'),
                                         array('ViewHelper'),
                                         array('ElementErrors'),
                                         array('Addon'),
                                         array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                         array('Wrapper'),
                                         array(array('data' => 'HtmlTag'), array('tag' => 'td')),
                                         array('Description', array('tag' => 'td', 'placement' => 'prepend', 'class' => 'description')),
                                         array('Label', array('tag' => 'td')),
                                         array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'class' => 'control-group')),
                                    ));

        $this->setDisplayGroupDecorators(array(
                                              'FormElements',
                                              array('HtmlTag', array('tag' => 'table')),
                                              array('FieldSet', array('tag' => 'div', 'class' => 'well')),
                                         ));

        $this->setDecorators(array(
                                  'FormElements',
                                  'Form',
                             ));
    }
}