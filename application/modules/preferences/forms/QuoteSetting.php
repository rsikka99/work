<?php
class Preferences_Form_QuoteSetting extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * Bool used to determine if the form requires values or not.
     *
     * @var bool
     */
    public $allowsNull = false;

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->_addClassNames('reportSettingsForm');
        $this->setMethod('post');
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

        $this->addElement('text', 'pageCoverageMonochrome', array(
                                                                 'label'      => 'Page Coverage Mono:',
                                                                 'required'   => true,
                                                                 'append'     => '$',
                                                                 'filters'    => array(
                                                                     'StringTrim',
                                                                     'StripTags'
                                                                 ),
                                                                 'validators' => $coverageValidator,
                                                            ));

        $this->addElement('text', 'pageCoverageColor', array(
                                                            'label'      => 'Page Coverage Color:',
                                                            'required'   => true,
                                                            'append'     => '$',
                                                            'filters'    => array(
                                                                'StringTrim',
                                                                'StripTags'
                                                            ),
                                                            'validators' => $coverageValidator
                                                       ));

        $this->addElement('text', 'deviceMargin', array(
                                                       'label'      => 'Device Margin:',
                                                       'required'   => true,
                                                       'append'     => '$',
                                                       'filters'    => array(
                                                           'StringTrim',
                                                           'StripTags'
                                                       ),
                                                       'validators' => $marginValidator
                                                  ));

        $this->addElement('text', 'pageMargin', array(
                                                     'label'      => 'Page Margin:',
                                                     'required'   => true,
                                                     'append'     => '$',
                                                     'filters'    => array(
                                                         'StringTrim',
                                                         'StripTags'
                                                     ),
                                                     'validators' => $marginValidator
                                                ));

        $this->addElement('text', 'adminCostPerPage', array(
                                                           'label'      => 'Admin Cost:',
                                                           'required'   => true,
                                                           'append'     => '$ / per page',
                                                           'filters'    => array(
                                                               'StringTrim',
                                                               'StripTags'
                                                           ),
                                                           'validators' => $cppValidator
                                                      ));

        $this->addElement('text', 'laborCostPerPage', array(
                                                             'label'      => 'Labor Cost:',
                                                             'required'   => true,
                                                             'append'     => '$ / per page',
                                                             'filters'    => array(
                                                                 'StringTrim',
                                                                 'StripTags'
                                                             ),
                                                             'validators' => $cppValidator
                                                        ));

        $this->addElement('text', 'partsCostPerPage', array(
                                                             'label'      => 'Parts Cost Per Page:',
                                                             'required'   => true,
                                                             'append'     => '$ / per page',
                                                             'filters'    => array(
                                                                 'StringTrim',
                                                                 'StripTags'
                                                             ),
                                                             'validators' => $cppValidator
                                                        ));



        $pricingConfigDropdown = $this->createElement('select', 'pricingConfigId', array('label' => 'Toner Preference:', 'class' => 'span3'));
        /* @var $pricingConfig Proposalgen_Model_PricingConfig */
        foreach (Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll() as $pricingConfig)
        {
            if ($pricingConfig->pricingConfigId === Proposalgen_Model_PricingConfig::NONE)
            {
                continue;
            }
            $pricingConfigDropdown->addMultiOption($pricingConfig->pricingConfigId, $pricingConfig->configName);
        }
        $this->addElement($pricingConfigDropdown);

        // Display groups and decoratos
        $this->addDisplayGroup(array('pageCoverageMonochrome', 'pageCoverageColor', 'deviceMargin', 'pageMargin', 'adminCostPerPage', 'laborCostPerPage' , 'partsCostPerPage', $pricingConfigDropdown), 'quoteSetting', array('legend' => 'Quote Settings'));
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
        $this->setDisplayGroupDecorators(array(
                                              'FormElements',
                                              array('ColumnHeader', array('data' => array('Property', 'Value'), 'placement' => 'prepend')),
                                              array(array('table' => 'HtmlTag'), array('tag' => 'table')),
                                              array(array('well' => 'HtmlTag'), array('tag' => 'div', 'class' => 'well')),
                                              'FieldSet'
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

        // Form Buttons
        $submitButton = $this->createElement('submit', 'submit', array('label' => 'Submit',));
        $submitButton->setDecorators(array(
                                          'FieldSize',
                                          'ViewHelper',
                                          'Addon',
                                          'ElementErrors',
                                     ));
        $this->addElement($submitButton);
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