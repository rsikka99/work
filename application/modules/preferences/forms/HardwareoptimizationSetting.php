<?php
/**
 * Class Preferences_Form_HardwareoptimizationSetting
 */
class Preferences_Form_HardwareoptimizationSetting extends Twitter_Bootstrap_Form_Horizontal
{
    public $allowsNull = false;

    public function init ()
    {
        $this->setMethod('post');
        $this->_addClassNames('reportSettingsForm');
        $this->addPrefixPath('My_Form_Decorator', 'My/Form/Decorator/', 'decorator');

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


        // Hardware Optimization Elements
        $this->addElement('text', 'costThreshold', array(
                                                        'label'      => 'Cost Threshold',
                                                        'append'     => '$',
                                                        'validators' => $costValidator
                                                   ));

        $this->addElement('text', 'dealerMargin', array(
                                                       'label'      => 'Cost Threshold',
                                                       'append'     => '$',
                                                       'validators' => $marginValidator
                                                  ));
        $this->addElement('text', 'partsCostPerPage', array(
                                                           'label'      => 'Parts Cost Per Page',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));

        $this->addElement('text', 'laborCostPerPage', array(
                                                           'label'      => 'Labor Cost Per Page',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));
        $this->addElement('text', 'adminCostPerPage', array(
                                                           'label'      => 'Admin Cost Per Page',
                                                           'append'     => '$ / page',
                                                           'validators' => $cppValidator
                                                      ));


        $this->addElement('text', 'targetMonochromeCostPerPage', array(
                                                                      'label'      => 'Target Monochrome Cost Per Page',
                                                                      'append'     => '$ / page',
                                                                      'validators' => $cppValidator
                                                                 ));
        $this->addElement('text', 'targetColorCostPerPage', array(
                                                                 'label'      => 'Target Color Cost Per Page',
                                                                 'append'     => '$ / page',
                                                                 'validators' => $cppValidator
                                                            ));
        $replacementPricingConfig = $this->createElement('select', 'replacementPricingConfigId', array(
                                                                                                      'label' => 'Toner Preference',
                                                                                                      'class' => 'span3 '
                                                                                                 ));
        $dealerPricingConfigId    = $this->createElement('select', 'dealerPricingConfigId', array(
                                                                                                 'label' => 'Toner Preference',
                                                                                                 'class' => 'span3 '
                                                                                            ));
        $customerPricingConfigId  = $this->createElement('select', 'customerPricingConfigId', array(
                                                                                                   'label' => 'Toner Preference',
                                                                                                   'class' => 'span3 '
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
            $element->setRequired(true);
        }


        $this->addDisplayGroup(array('costThreshold', 'dealerMargin', 'laborCostPerPage', 'partsCostPerPage', 'adminCostPerPage', 'targetMonochromeCostPerPage', 'targetColorCostPerPage', $replacementPricingConfig, $dealerPricingConfigId, $customerPricingConfigId), 'hardwareOptimization', array('legend' => 'Hardware Profitability Settings'));

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

        $pricingConfigOptions = array();
        // Add multi options to toner preferences.
        /* @var $pricingConfig Proposalgen_Model_PricingConfig */
        foreach (Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll() as $pricingConfig)
        {
            $pricingConfigOptions [$pricingConfig->pricingConfigId] = $pricingConfig->configName;
        }
        $replacementPricingConfig->addMultiOptions($pricingConfigOptions);

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
                                              'FieldSet'
                                         ));
    }

    /**
     * Allows the form to allow null vlaues
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