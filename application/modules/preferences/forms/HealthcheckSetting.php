<?php
/**
 * Class Preferences_Form_HealthcheckSetting
 */
class Preferences_Form_HealthcheckSetting extends Twitter_Bootstrap_Form_Horizontal
{
    public $allowsNull = false;

    public function init ()
    {
        $this->setMethod('post');
        $this->_addClassNames('reportSettingsForm');
        $this->addPrefixPath('My_Form_Decorator', 'My/Form/Decorator/', 'decorator');

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
                'validator' => 'GreaterThan',
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
        $this->addElement('text', 'pageCoverageMonochrome', array(
                                                                 'label'      => 'Page Coverage Monochrome',
                                                                 'append'     => '%',
                                                                 'validators' => $coverageValidator
                                                            ));
        $this->addElement('text', 'pageCoverageColor', array(
                                                            'label'      => 'Page Coverage Color',
                                                            'append'     => '%',
                                                            'validators' => $coverageValidator
                                                       ));
        // Health Check Elements
        $this->addElement('text', 'healthcheckMargin', array(
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
                                                              'label'      => 'Leased Monochrome Cost',
                                                              'append'     => '$ / page',
                                                              'validators' => $cppValidator
                                                         ));
        $this->addElement('text', 'leasedColorCostPerPage', array(
                                                                 'label'      => 'Leased Color Cost',
                                                                 'append'     => '$ / page',
                                                                 'validators' => $cppValidator
                                                            ));
        $this->addElement('text', 'mpsBwCostPerPage', array(
                                                           'label'      => 'Monochrome Cost',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));
        $this->addElement('text', 'mpsColorCostPerPage', array(
                                                              'label'      => 'Color Cost',
                                                              'append'     => '$ / page',
                                                              'validators' => $cppValidator
                                                         ));
        $this->addElement('text', 'kilowattsPerHour', array(
                                                           'label'      => 'Energy Cost',
                                                           'append'     => '$ / KWh',
                                                           'validators' => $costValidator
                                                      ));
        $this->addElement('text', 'adminCostPerPage', array(
                                                           'label'      => 'Admin Cost',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));
        $this->addElement('text', 'laborCostPerPage', array(
                                                           'label'      => 'Labor Cost',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));
        $this->addElement('text', 'partsCostPerPage', array(
                                                           'label'      => 'Parts Cost',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));
        $this->addElement('text', 'averageItHourlyRate', array(
                                                              'label'      => 'Estimated Average It Hourly Rate',
                                                              'append'     => '$ / hour',
                                                              'validators' => $costValidator
                                                         ));
        $hoursSpentOnIt = $this->createElement('text', 'hoursSpentOnIt', array(
                                                                              'label'      => 'Estimated Hours Spent On It',
                                                                              'append'     => 'hours',
                                                                              'validators' => $costValidator
                                                                         ));
        $hoursSpentOnIt->setRequired(false);
        $hoursSpentOnIt->setAttrib('class', 'span2 ');
        $annualCostOfLabor = $this->createElement('text', 'costOfLabor', array(
                                                                              'label'      => 'Annual Cost Of Labor',
                                                                              'append'     => '$ / fleet',
                                                                              'validators' => $costValidator
                                                                         ));
        $annualCostOfLabor->setAttrib('class', 'span2 ');
        $this->addElement('text', 'costToExecuteSuppliesOrder', array(
                                                                     'label'      => 'Estimated Cost To Execute Supplies Order',
                                                                     'append'     => '$ / order',
                                                                     'validators' => $costValidator
                                                                ));
        $this->addElement('text', 'numberOfSupplyOrdersPerMonth', array(
                                                                       'label'      => 'Estimated Supply Orders Per Month',
                                                                       'append'     => '/ month',
                                                                       'validators' => $costValidator
                                                                  ));
        $customerMonochromeVendor = $this->createElement('multiselect', 'customerMonochromeRankSetArray',
            array(
                 "class" => "tonerMultiselect",
            ));
        $customerColorVendor      = $this->createElement('multiselect', 'customerColorRankSetArray',
            array(
                 "class" => "tonerMultiselect",
            ));

        $this->allowNullValues();
        // Set a span 2 to all elements that do not have a class
        /* @var $element Zend_Form_Element_Text */
        foreach ($this->getElements() as $element)
        {
            $class = $element->getAttrib('class');
            if (!$class)
            {
                $element->setAttrib('class', 'span2 ');
            }
            $element->setRequired(true);
        }

        $this->addDisplayGroup(array('pageCoverageMonochrome',
                                     'pageCoverageColor',
                                     'healthcheckMargin',
                                     'monthlyLeasePayment',
                                     'defaultPrinterCost',
                                     'leasedBwCostPerPage',
                                     'leasedColorCostPerPage',
                                     'mpsBwCostPerPage',
                                     'mpsColorCostPerPage',
                                     'kilowattsPerHour',
                                     'adminCostPerPage',
                                     'laborCostPerPage',
                                     'partsCostPerPage',
                                     'averageItHourlyRate',
                                     $hoursSpentOnIt,
                                     $annualCostOfLabor,
                                     'costToExecuteSuppliesOrder',
                                     'numberOfSupplyOrdersPerMonth',
                                     $customerMonochromeVendor,
                                     $customerColorVendor,
                               ), 'assessment', array('legend' => 'Health Check Settings',));

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
                                              array('ColumnHeader', array('data' => array('Property', 'Value'), 'placement' => 'prepend')),
                                              array(array('table' => 'HtmlTag'), array('tag' => 'table')),
                                              array(array('well' => 'HtmlTag'), array('tag' => 'div', 'class' => 'well')),
                                              'Fieldset'
                                         ));

        $tonerVendorManufacturers = Proposalgen_Model_Mapper_TonerVendorManufacturer::getInstance()->fetchAllForDropdown();
        $customerMonochromeVendor->addMultiOptions($tonerVendorManufacturers);
        $customerColorVendor->addMultiOptions($tonerVendorManufacturers);

        $this->tonerSelectElementsDisplayGroups(2);
    }

    /**
     *  This is used to set up the form with a three column header.
     */
    public function setUpFormWithDefaultDecorators ()
    {
        $this->setDisplayGroupDecorators(array(
                                              'FormElements',
                                              array('ColumnHeader', array('data' => array('Property', 'Default', 'Value'), 'placement' => 'prepend')),
                                              array(array('table' => 'HtmlTag'), array('tag' => 'table')),
                                              array(array('well' => 'HtmlTag'), array('tag' => 'div', 'class' => 'well')),
                                              'Fieldset'
                                         ));

        $this->tonerSelectElementsDisplayGroups(3);
    }

    public function tonerSelectElementsDisplayGroups ($colSpan)
    {

        $this->getElement("customerMonochromeRankSetArray")->setDecorators(array(
                                                                                'FieldSize',
                                                                                'ViewHelper',
                                                                                'Addon',
                                                                                array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                                                                'ElementErrors',
                                                                                array(array('data' => 'HtmlTag'), array('tag' => 'td', "colspan" => $colSpan)),
                                                                                array(array('row' => 'HtmlTag'), array('tag' => 'tr', "class" => "control-group")),
                                                                                array('AddRowData', array('header'    => "Monochrome Toner Preference",
                                                                                                          "trClass"   => "control-group",
                                                                                                          "tdAttr"    => "colspan={$colSpan}",
                                                                                                          "placement" => Zend_Form_Decorator_Abstract::PREPEND))
                                                                           ));
        $this->getElement("customerColorRankSetArray")->setDecorators(array(
                                                                           'FieldSize',
                                                                           'ViewHelper',
                                                                           'Addon',
                                                                           array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                                                           'ElementErrors',
                                                                           array(array('data' => 'HtmlTag'), array('tag' => 'td', "colspan" => $colSpan)),
                                                                           array(array('row' => 'HtmlTag'), array('tag' => 'tr', "class" => "control-group")),
                                                                           array('AddRowData', array("header"    => "Color Toner Preference",
                                                                                                     "trClass"   => "control-group",
                                                                                                     "tdAttr"    => "colspan={$colSpan}",
                                                                                                     "placement" => Zend_Form_Decorator_Abstract::PREPEND))
                                                                      ));
    }

    /**
     * Allows the form to allow null values
     */
    public function allowNullValues ()
    {
        /* @var Zend_Form_Element_Text $element */
        foreach ($this->getElements() as $element)
        {
            $element->setRequired(false);
        }
        $this->allowsNull = true;
    }
}