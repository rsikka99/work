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

        $energyCostValidator = array(
            array(
                'validator' => 'Between',
                'options'   => array(
                    'min'       => 0,
                    'max'       => 25,
                    'inclusive' => true
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

        $this->addElement('checkbox', 'useDevicePageCoverages', array(
            'label' => 'Use Device Page Coverages',
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
        if (My_Feature::canAccess(My_Feature::HEALTHCHECK_PRINTIQ))
        {
            $this->addElement('text', 'customerMonochromeCostPerPage', array(
                                                                            'label'      => 'Customer Monochrome Cost Per Page',
                                                                            'append'     => '$ / page',
                                                                            'validators' => $cppValidator
                                                                       ));
            $this->addElement('text', 'customerColorCostPerPage', array(
                                                                       'label'      => 'Customer Color Cost Per Page',
                                                                       'append'     => '$ / page',
                                                                       'validators' => $cppValidator
                                                                  ));
        }

        $this->addElement('text', 'kilowattsPerHour', array(
            'label'      => 'Energy Cost',
            'append'     => '$ / KWh',
            'validators' => $energyCostValidator
        ));
        $this->addElement('text', 'adminCostPerPage', array(
            'label'      => 'Admin Cost Per Page',
            'append'     => '$ / page',
            'validators' => $cppValidator
        ));
        $this->addElement('text', 'laborCostPerPage', array(
            'label'      => 'Labor Cost Per Page',
            'append'     => '$ / page',
            'validators' => $cppValidator
        ));
        $this->addElement('text', 'partsCostPerPage', array(
            'label'      => 'Parts Cost Per Page',
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
                                     'useDevicePageCoverages',
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
                                     'customerMonochromeCostPerPage',
                                     'customerColorCostPerPage',
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
        $submitButton = $this->createElement('submit', 'save', array(
            'label'      => 'Submit',
            'class'      => 'btn-primary',
            'decorators' => array(
                'FieldSize',
                'ViewHelper',
                'Addon',
                'ElementErrors'
            )
        ));

        $cancelButton = $this->createElement('submit', 'cancel', array(
            'label'      => 'Cancel',
            'decorators' => array(
                'FieldSize',
                'ViewHelper',
                'Addon',
                'ElementErrors'
            )
        ));

        $this->addDisplayGroup(array($submitButton, $cancelButton), 'buttonGroup');

        $this->setDisplayGroupDecorators(array(
            'FormElements',
            array('ColumnHeader', array('data' => array('Property', 'Value'), 'placement' => 'prepend')),
            array(array('table' => 'HtmlTag'), array('tag' => 'table')),
            array(array('well' => 'HtmlTag'), array('tag' => 'div', 'class' => 'well')),
            'Fieldset'
        ));

        $this->getDisplayGroup('buttonGroup')->setDecorators(array(
            'FormElements',
            array(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'center-form-actions')),
            'Fieldset'
        ));

        $this->getElement("useDevicePageCoverages")->setDecorators(array("ViewHelper",
                                                                         'FieldSize',
                                                                         'ViewHelper',
                                                                         'Addon',
                                                                         'ElementErrors',
                                                                         array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                                                                         'Wrapper',
                                                                         array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'value', 'style' => 'text-align:right')),
                                                                         array('Description', array('tag' => 'td', 'placement' => 'prepend', 'class' => 'description')),
                                                                         array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'switch', 'data-on-label' => 'Yes', 'data-off-label' => 'No', 'data-off' => 'danger', 'data-on' => 'success')),
                                                                         array("label", array('class' => 'control-label')),
                                                                         array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
                                                                         array('Label', array('tag' => 'td')),
                                                                         array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'class' => 'control-group'))));


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
            array('ColumnHeader', array('data' => array('Property', 'Default', 'Value'), 'class' => array('property', 'default', 'value'), 'placement' => 'prepend')),
            array(array('table' => 'HtmlTag'), array('tag' => 'table')),
            array(array('well' => 'HtmlTag'), array('tag' => 'div', 'class' => 'well')),
            'Fieldset'
        ));

        $this->tonerSelectElementsDisplayGroups(3);
        $this->getDisplayGroup('buttonGroup')->setDecorators(array(
            'FormElements',
            array(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'center-form-actions')),
            'Fieldset'
        ));
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
                                      "placement" => Zend_Form_Decorator_Abstract::PREPEND)),
            array(array('defaultDescription' => 'AddRowData'), array('header'    => '<em>OEM toners will be used by default</em>',
                                                                     "tdClass"   => "short-row",
                                                                     "tdAttr"    => "colspan={$colSpan}",
                                                                     "placement" => Zend_Form_Decorator_Abstract::APPEND))
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
                                      "placement" => Zend_Form_Decorator_Abstract::PREPEND)),
            array(array('defaultDescription' => 'AddRowData'), array('header'    => '<em>OEM toners will be used by default</em>',
                                                                     "tdClass"   => "short-row",
                                                                     "tdAttr"    => "colspan={$colSpan}",
                                                                     "placement" => Zend_Form_Decorator_Abstract::APPEND))
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