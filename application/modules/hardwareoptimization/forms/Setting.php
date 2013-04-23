<?php
class Hardwareoptimization_Form_Setting extends Twitter_Bootstrap_Form_Horizontal
{
    public $allowsNull = false;

    public function init ()
    {
        $this->setMethod('post');
        $this->_addClassNames('reportSettingsForm form-center-actions');
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
                                                       'label'      => 'Dealer Margin',
                                                       'append'     => '$',
                                                       'validators' => $marginValidator
                                                  ));
        $this->addElement('text', 'adminCostPerPage', array(
                                                           'label'      => 'Admin Cost Per Page',
                                                           'append'     => '$',
                                                           'validators' => $costValidator,
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
                                                                                                      'label' => 'Replacement Toner Preference',
                                                                                                      'class' => 'span3 '
                                                                                                 ));
        $dealerPricingConfig      = $this->createElement('select', 'dealerPricingConfigId', array(
                                                                                                 'label' => 'Dealer Toner Preference',
                                                                                                 'class' => 'span3 '
                                                                                            ));
        $customerPricingConfig    = $this->createElement('select', 'customerPricingConfigId', array(
                                                                                                   'label' => 'Customer Toner Preference',
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


        $this->addDisplayGroup(array('costThreshold', 'dealerMargin',"adminCostPerPage", 'targetMonochromeCostPerPage', 'targetColorCostPerPage', $replacementPricingConfig, $dealerPricingConfig, $customerPricingConfig), 'hardwareOptimization');

        $group = $this->getDisplayGroup('hardwareOptimization');
        $group->setDecorators(array(
                                   'FormElements',
                                   array('ColumnHeader', array('data' => array('Property', 'Value'), 'placement' => 'prepend')),
                                   array(array('table' => 'HtmlTag'), array('tag' => 'table')),
                                   array(array('well' => 'HtmlTag'), array('tag' => 'div', 'class' => 'well')),
                                   'Fieldset'
                              ));

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

        $pricingConfigOptions = array();
        // Add multi options to toner preferences.
        /* @var $pricingConfig Proposalgen_Model_PricingConfig */
        foreach (Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll() as $pricingConfig)
        {
            $pricingConfigOptions [$pricingConfig->pricingConfigId] = $pricingConfig->configName;

        }
        $replacementPricingConfig->addMultiOptions($pricingConfigOptions);
        $dealerPricingConfig->addMultiOptions($pricingConfigOptions);
        $customerPricingConfig->addMultiOptions($pricingConfigOptions);

        Hardwareoptimization_Form_Hardware_Optimization_Navigation::addFormActionsToForm(Hardwareoptimization_Form_Hardware_Optimization_Navigation::BUTTONS_ALL, $this);
    }

    /**
     *  This is used to set up the form with a three column header.
     */
    public function setUpFormWithDefaultDecorators ()
    {
        $group = $this->getDisplayGroup('hardwareOptimization');
        $group->setDecorators(array(
                                   'FormElements',
                                   array('ColumnHeader', array('data' => array('Property', 'Default', 'Value'), 'placement' => 'prepend')),
                                   array(array('table' => 'HtmlTag'), array('tag' => 'table')),
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