<?php
class Preferences_Form_ReportSetting extends Twitter_Bootstrap_Form_Horizontal
{
    public function init ()
    {
        $this->setMethod('post');
        $this->_addClassNames('reportSettingsForm');

        $coverageValidator = array(
            array(
                'validator' => 'Between',
                'options'   => array(
                    'min'       => 0,
                    'max'       => 100,
                    'inclusive' => false
                )
            ),
            'Float'
        );

        $marginValidator = array(
            array(
                'validator' => 'Between',
                'options'   => array(
                    'min' => -100,
                    'max' => 100
                )
            ),
            'Float'
        );

        $costValidator = array(
            array(
                'validator' => 'greaterThan',
                'options'   => array(
                    'min' => 0
                )
            ),
            'Float'
        );

        $cppValidator = array(
            array(
                'validator' => 'Between',
                'options'   => array(
                    'min'       => 0,
                    'max'       => 5,
                    'inclusive' => true
                )
            ),
            'Float'
        );

        // Survey Elements
        $this->addElement('text', 'pageCoverageMono', array(
                                                           'label'      => 'Page Coverage Monochrome',
                                                           'append'     => '%',
                                                           'validators' => $coverageValidator
                                                      ));
        $this->addElement('text', 'pageCoverageColor', array(
                                                            'label'      => 'Page Coverage Color',
                                                            'append'     => '%',
                                                            'validators' => $coverageValidator
                                                       ));
        // Assessment Elements
        $this->addElement('text', 'assessmentReportMargin', array(
                                                                 'label'      => 'Pricing Margin',
                                                                 'append'     => '%',
                                                                 'validators' => $marginValidator
                                                            ));
        $this->addElement('text', 'monthlyLeasePayment', array(
                                                              'label'      => 'Monthly Lease Payment',
                                                              'append'     => '$ / device',
                                                              'validators' => $costValidator
                                                         ));

        $this->addElement('text', 'defaultPrinterCost', array(
                                                             'label'      => 'Default Printer Cost',
                                                             'append'     => '$ / device',
                                                             'validators' => $costValidator
                                                        ));
        $this->addElement('text', 'leasedBwCostPerPage', array(
                                                              'label'  => 'Leased Monochrome Cost',
                                                              'append' => '$ / page',
                                                              'validators' => $cppValidator
                                                         ));
        $this->addElement('text', 'leasedColorCostPerPage', array(
                                                                 'label'  => 'Leased Color Cost',
                                                                 'append' => '$ / page',
                                                                 'validators' => $cppValidator
                                                            ));
        $this->addElement('text', 'mpsBwCostPerPage', array(
                                                           'label'  => 'Monochrome Cost',
                                                           'append' => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));
        $this->addElement('text', 'mpsColorCostPerPage', array(
                                                              'label'  => 'Color Cost',
                                                              'append' => '$ / page',
                                                              'validators' => $cppValidator
                                                         ));
        $this->addElement('text', 'kilowattsPerHour', array(
                                                           'label'  => 'Energy Cost',
                                                           'append' => '/ KWh',
                                                           'validators' => $costValidator
                                                      ));
        $this->addElement('select', 'assessmentPricingConfigId', array(
                                                                      'label' => 'Toner Preference',
                                                                      'class' => 'span3 '
                                                                 ));
        // Gross margin elements
        $this->addElement('text', 'actualPageCoverageMono', array(
                                                                 'label'  => 'Page Coverage Monochrome',
                                                                 'append' => '%',
                                                                 'validators' => $coverageValidator
                                                            ));
        $this->addElement('text', 'actualPageCoverageColor', array(
                                                                  'label'  => 'Page Coverage Color',
                                                                  'append' => '%',
                                                                  'validators' => $coverageValidator
                                                             ));
        $this->addElement('text', 'adminCostPerPage', array(
                                                           'label'  => 'Admin Cost',
                                                           'append' => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));
        $this->addElement('text', 'serviceCostPerPage', array(
                                                             'label'  => 'Service Cost',
                                                             'append' => '$ / page',
                                                             'validators' => $cppValidator
                                                        ));
        // Hardware Optimization Elements
        $this->addElement('text', 'costThreshold', array(
                                                        'label'  => 'Cost Threshold',
                                                        'append' => '$',
                                                        'validators' => $costValidator
                                                   ));
        $this->addElement('text', 'targetMonochromeCostPerPage', array(
                                                                      'label'  => 'Target Monochrome Cost Per Page',
                                                                      'append' => '$ / page',
                                                                      'validators' => $cppValidator
                                                                 ));
        $this->addElement('text', 'targetColorCostPerPage', array(
                                                                 'label'  => 'Target Color Cost Per Page',
                                                                 'append' => '$ / page',
                                                                 'validators' => $cppValidator
                                                            ));


        // Set a span 2 to all elements that do not have a class
        /* @var $element Zend_Form_Element_Text */
        foreach ($this->getElements() as $element)
        {
            $class = $element->getAttrib('class');
            if (!$class)
            {
                $element->setAttrib('class', 'span2 ');
            }
        }

        $this->addDisplayGroup(array('pageCoverageMono', 'pageCoverageColor'), 'survey', array('legend' => 'Survey Settings'));
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
        $this->addDisplayGroup(array('costThreshold', 'targetMonochromeCostPerPage', 'targetColorCostPerPage'), 'hardwareOptimization', array('legend' => 'Hardware Profitability Settings'));

        $this->setElementDecorators(array(
                                         'FieldSize',
                                         'ViewHelper',
                                         'Addon',
                                         'ElementErrors',
                                         array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                         'Wrapper',
                                         array(array('data' => 'HtmlTag'), array('tag' => 'td')),
                                         array('Description', array('tag' => 'td', 'placement' => 'prepend', 'class' => 'description')),
                                         array('Label', array('tag' => 'td')),
                                         array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'class' => 'control-group')),
                                    ));

        // Form Buttons
        $submitButton = $this->createElement('submit', 'submit', array(
                                                                      'label' => 'Submit',

                                                                 ));
        $submitButton->setDecorators(array(
                                          'FieldSize',
                                          'ViewHelper',
                                          'Addon',
                                          'ElementErrors',
                                     ));
        $this->addElement($submitButton);

        $this->setDisplayGroupDecorators(array(
                                              'FormElements',
                                              array(array('table' => 'HtmlTag'), array('tag' => 'table')),
                                              array(array('well' => 'HtmlTag'), array('tag' => 'div', 'class' => 'well')),
                                              'FieldSet'
                                         ));
    }
}