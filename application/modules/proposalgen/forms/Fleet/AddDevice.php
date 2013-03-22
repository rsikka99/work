<?php
class Proposalgen_Form_Fleet_AddDevice extends Twitter_Bootstrap_Form_Horizontal
{

    public $tonerElements;

    public function init ()
    {
        $deviceDetailElements = array();
        /*
         * Hidden Array to maintain the device instance id's that we are working with
         */
        $deviceDetailElements[] = 'deviceInstanceIds';
        $this->addElement('hidden', 'deviceInstanceIds', array(
                                                              'required' => true
                                                         ));

        /*
         * Manufacturer
         */
        $manufacturerIdMultiOptions = array(
            0 => "Select Manufacturer"
        );
        $validManufacturers         = array();
        $manufacturers              = Proposalgen_Model_Mapper_Manufacturer::getInstance()->fetchAllAvailableManufacturers();
        foreach ($manufacturers as $manufacturer)
        {
            $manufacturerIdMultiOptions[$manufacturer->id] = $manufacturer->fullname;
            $validManufacturers[]                          = $manufacturer->id;
        }

        $deviceDetailElements[] = 'manufacturerId';
        $this->addElement('select', 'manufacturerId', array(
                                                           'dimension'    => 4,
                                                           'label'        => 'Manufacturer',
                                                           'multiOptions' => $manufacturerIdMultiOptions,
                                                           'required'     => true,
                                                           'validators'   => array(
                                                               array(
                                                                   'validator' => 'InArray',
                                                                   'options'   => array(
                                                                       'haystack' => $validManufacturers,
                                                                       'messages' => array(
                                                                           Zend_Validate_InArray::NOT_IN_ARRAY => 'You must select a manufacturer from the drop down'
                                                                       )
                                                                   )
                                                               )
                                                           ),
                                                      ));

        /*
         * Model Name
         */
        $deviceDetailElements[] = 'modelName';
        $this->addElement('text', 'modelName', array(
                                                    'dimension' => 4,
                                                    'label'     => 'Model Name',
                                                    'required'  => true,
                                               ));


        /*
         * Device launch date
         */
        $deviceDetailElements[] = 'launchDate';
        $this->addElement('text', 'launchDate', array(
                                                     'class'       => 'tmtwDatePicker',
                                                     'dimension'   => 2,
                                                     'description' => 'yyyy-MM-dd hh:mm:ss',
                                                     'label'       => 'Launch Date',
                                                     'maxlength'   => 20,
                                                     'required'    => true,
                                                     'validators'  => array(
                                                         array(
                                                             'validator' => 'Date',
                                                             'options'   => array(
                                                                 'format'   => 'yyyy-MM-dd hh:mm:ss',
                                                                 'messages' => array(
                                                                     Zend_Validate_Date::INVALID_DATE => 'The date you entered did not match the format(%format%)'
                                                                 )
                                                             )
                                                         )
                                                     )
                                                ));


        /*
         * Device Cost
         */
        $deviceDetailElements[] = 'cost';
        $this->addElement('text', 'cost', array(
                                               'dimension'  => 2,
                                               'label'      => 'Cost',
                                               'prepend'    => '$',
                                               'validators' => array(
                                                   'Float',
                                                   array(
                                                       'validator' => 'GreaterThan',
                                                       'options'   => array('min' => 0.00)
                                                   )
                                               )
                                          ));

         /*
         * parts Cost Per Page
         */
        $deviceDetailElements[] = 'partsCostPerPage';
        $this->addElement('text', 'partsCostPerPage', array(
                                               'dimension'  => 2,
                                               'label'      => 'Parts Cost Per Page',
                                               'prepend'    => '$',
                                               'validators' => array(
                                                   'Float',
                                                   array(
                                                       'validator' => 'GreaterThan',
                                                       'options'   => array('min' => 0.00)
                                                   )
                                               )
                                          ));

        /*
         * labor Cost Per Page
         */
        $deviceDetailElements[] = 'laborCostPerPage';
        $this->addElement('text', 'laborCostPerPage', array(
                                               'dimension'  => 2,
                                               'label'      => 'Labor Cost Per Page',
                                               'prepend'    => '$',
                                               'validators' => array(
                                                   'Float',
                                                   array(
                                                       'validator' => 'GreaterThan',
                                                       'options'   => array('min' => 0.00)
                                                   )
                                               )
                                          ));


        /*
         * Checkboxes for Copier, Duplex, Fax, Scan
         */
        $deviceDetailElements[] = 'isCopier';
        $this->addElement('checkbox', 'isCopier', array(
                                                       'label'   => 'Copier',
                                                       'filters' => array(
                                                           'Boolean'
                                                       ),
                                                  ));

        $deviceDetailElements[] = 'isDuplex';
        $this->addElement('checkbox', 'isDuplex', array(
                                                       'label'   => 'Duplex',
                                                       'filters' => array(
                                                           'Boolean'
                                                       ),
                                                  ));
        $deviceDetailElements[] = 'isFax';
        $this->addElement('checkbox', 'isFax', array(
                                                    'label'   => 'Fax',
                                                    'filters' => array(
                                                        'Boolean'
                                                    ),
                                               ));
        $deviceDetailElements[] = 'isScanner';
        $this->addElement('checkbox', 'isScanner', array(
                                                        'label'   => 'Scan',
                                                        'filters' => array(
                                                            'Boolean'
                                                        ),
                                                   ));
        $deviceDetailElements[] = 'reportsTonerLevels';
        $this->addElement('checkbox', 'reportsTonerLevels', array(
                                                                 'label'   => 'reportsTonerLevels',
                                                                 'filters' => array(
                                                                     'Boolean'
                                                                 ),
                                                            ));

        /*
         * Print Speed
         */
        $deviceDetailElements[] = 'ppmBlack';
        $this->addElement('text', 'ppmBlack', array(
                                                   'dimension'  => 2,
                                                   'label'      => 'PPM Black',
                                                   'append'     => '/Pages Per Minute',
                                                   'validators' => array(
                                                       'Int',
                                                       array(
                                                           'validator' => 'GreaterThan',
                                                           'options'   => array('min' => 0.00)
                                                       )
                                                   )
                                              ));
        $deviceDetailElements[] = 'ppmColor';
        $this->addElement('text', 'ppmColor', array(
                                                   'dimension'  => 2,
                                                   'label'      => 'PPM Color',
                                                   'append'     => '/Pages Per Minute',
                                                   'validators' => array(
                                                       'Int',
                                                       array(
                                                           'validator' => 'GreaterThan',
                                                           'options'   => array('min' => 0.00)
                                                       )
                                                   )
                                              ));

        /*
         * Duty Cycle
         */
        $deviceDetailElements[] = 'dutyCycle';
        $this->addElement('text', 'dutyCycle', array(
                                                    'dimension'  => 2,
                                                    'label'      => 'Duty Cycle',
                                                    'append'     => '/Pages Per Month',
                                                    'validators' => array(
                                                        'Int',
                                                        array(
                                                            'validator' => 'GreaterThan',
                                                            'options'   => array('min' => 0.00)
                                                        )
                                                    )
                                               ));

        /*
         * Wattage
         */
        $deviceDetailElements[] = 'wattsPowerNormal';
        $this->addElement('text', 'wattsPowerNormal', array(
                                                           'dimension'  => 1,
                                                           'label'      => 'Watts Power Normal',
                                                           'append'     => ' Watts',
                                                           'required'   => true,
                                                           'validators' => array(
                                                               'Int',
                                                               array(
                                                                   'validator' => 'GreaterThan',
                                                                   'options'   => array('min' => 0.00)
                                                               )
                                                           )
                                                      ));

        $deviceDetailElements[] = 'wattsPowerIdle';
        $this->addElement('text', 'wattsPowerIdle', array(
                                                         'dimension'  => 1,
                                                         'append'     => ' Watts',
                                                         'label'      => 'Watts Power Idle',
                                                         'required'   => true,
                                                         'validators' => array(
                                                             'Int',
                                                             array(
                                                                 'validator' => 'GreaterThan',
                                                                 'options'   => array('min' => 0.00)
                                                             )
                                                         )
                                                    ));
        /*
         * Leased Checkbox
         */
        $deviceDetailElements[] = 'isLeased';
        $this->addElement('checkbox', 'isLeased', array(
                                                       'label'   => 'Leased',
                                                       'filters' => array(
                                                           'Boolean'
                                                       ),
                                                  ));
        /*
         * Toner Configuration
         */
        $tonerConfigurationMultiOptions = Proposalgen_Model_TonerConfig::$TonerConfigNames;
        $deviceDetailElements[]         = 'tonerConfigId';
        $this->addElement('select', 'tonerConfigId', array(
                                                          'dimension'    => 2,
                                                          'label'        => 'Toner Configuration',
                                                          'multiOptions' => $tonerConfigurationMultiOptions,
                                                          'required'     => true
                                                     ));

        $this->addDisplayGroup($deviceDetailElements, 'deviceDetails');

        /*
         * Toners
         */
        $partTypes      = array('OEM' => 'oem', 'Compatible' => 'comp');
        $oemTonerColors = array(
            'Black'       => 'Black',
            'Cyan'        => 'Cyan',
            'Magenta'     => 'Magenta',
            'Yellow'      => 'Yellow',
            'Three Color' => 'ThreeColor',
            'Four Color'  => 'FourColor',
        );

        $this->tonerElements = array(
            'OEM'        => array(),
            'Compatible' => array(),
        );

        foreach ($partTypes as $partTypeName => $partType)
        {
            foreach ($oemTonerColors as $tonerColorName => $fieldName)
            {

                /**
                 * SKU
                 */
                $tonerSku = $this->createElement('text', $partType . $fieldName . 'TonerSku', array(
                                                                                                   'decorators' => array('FieldSize', 'ViewHelper', 'Addon', 'PopoverElementErrors'),
                                                                                                   'dimension'  => 2,
                                                                                                   'label'      => $tonerColorName,
                                                                                                   'prepend'    => 'SKU',
                                                                                                   'validators' => array(
                                                                                                       array(
                                                                                                           'validator' => 'Alnum',
                                                                                                           'options'   => array(
                                                                                                               'messages' => array(
                                                                                                                   Zend_Validate_Alnum::NOT_ALNUM => 'SKU must be letters and numbers. Spaces and special characters are not allowed.',
                                                                                                               )
                                                                                                           )
                                                                                                       )
                                                                                                   ),
                                                                                              ));
                $tonerSku->setAttrib('data-tonerColor', $fieldName);
                $this->addElement($tonerSku);

                /**
                 * Yield
                 */
                $tonerYield = $this->createElement('text', $partType . $fieldName . 'TonerYield', array(
                                                                                                       'decorators' => array('FieldSize', 'ViewHelper', 'Addon', 'PopoverElementErrors'),
                                                                                                       'dimension'  => 2,
                                                                                                       'label'      => $tonerColorName . ' Yield',
                                                                                                       'prepend'    => 'Yield',
                                                                                                       'validators' => array(
                                                                                                           array(
                                                                                                               'validator' => 'Int',
                                                                                                               'options'   => array(
                                                                                                                   'messages' => array(
                                                                                                                       Zend_Validate_Int::NOT_INT => 'Yield must be a whole number.'
                                                                                                                   )
                                                                                                               )
                                                                                                           ),
                                                                                                           array(
                                                                                                               'validator' => 'GreaterThan',
                                                                                                               'options'   => array(
                                                                                                                   'min'      => 0,
                                                                                                                   'messages' => array(
                                                                                                                       Zend_Validate_GreaterThan::NOT_GREATER => 'Yield must be greater than %min%.'
                                                                                                                   )
                                                                                                               )
                                                                                                           )
                                                                                                       ),
                                                                                                  ));
                $this->addElement($tonerYield);

                /**
                 * Cost
                 */
                $tonerCost = $this->createElement('text', $partType . $fieldName . 'TonerCost', array(
                                                                                                     'decorators' => array('FieldSize', 'ViewHelper', 'Addon', 'PopoverElementErrors'),
                                                                                                     'dimension'  => 2,
                                                                                                     'prepend'    => 'Cost $',
                                                                                                     'label'      => $tonerColorName . ' Cost',
                                                                                                     'validators' => array(
                                                                                                         array(
                                                                                                             'validator' => 'Float',
                                                                                                             'options'   => array(
                                                                                                                 'messages' => array(
                                                                                                                     Zend_Validate_Float::NOT_FLOAT => 'Cost must a number.'
                                                                                                                 )
                                                                                                             )
                                                                                                         ),
                                                                                                         array(
                                                                                                             'validator' => 'GreaterThan',
                                                                                                             'options'   => array(
                                                                                                                 'min'      => 0.00,
                                                                                                                 'messages' => array(
                                                                                                                     Zend_Validate_GreaterThan::NOT_GREATER => 'Cost must be greater than %min%.'
                                                                                                                 )
                                                                                                             )
                                                                                                         )
                                                                                                     ),
                                                                                                ));
                $this->addElement($tonerCost);

                $tonerSet                             = array(
                    $tonerSku,
                    $tonerYield,
                    $tonerCost
                );
                $this->tonerElements[$partTypeName][] = $tonerSet;
            }
        }

        /*
         * Buttons
         */
        $this->addElement('button', 'submit', array(
                                                   'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
                                                   'label'      => 'Save Changes',
                                                   'type'       => 'submit',

                                              ));
        // Add the buttons the the form actions
        $this->addDisplayGroup(array('submit'), 'actions', array(
                                                                'disableLoadDefaultDecorators' => true,
                                                                'decorators'                   => array(
                                                                    'Actions'
                                                                )
                                                           ));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
                                  array(
                                      'ViewScript',
                                      array(
                                          'viewScript' => 'forms/fleet/adddevice.phtml'
                                      )
                                  )
                             ));
    }

    /**
     * Sets toner fields required based on toner config id
     *
     * @param $tonerConfigId
     */
    public function setValidationOnToners ($tonerConfigId)
    {
        switch ((int)$tonerConfigId)
        {
            case Proposalgen_Model_TonerConfig::BLACK_ONLY:
                $this->_requireTonerColor("Black");
                break;
            case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED:
                $this->_requireTonerColor("Black");
                $this->_requireTonerColor("Cyan");
                $this->_requireTonerColor("Magenta");
                $this->_requireTonerColor("Yellow");
                break;
            case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED:
                $this->_requireTonerColor("Black");
                $this->_requireTonerColor("ThreeColor");
                break;
            case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED:
                $this->_requireTonerColor("FourColor");
                break;
        }
    }

    /**
     * Sets a toner color as required on the form
     *
     * @param string $tonerColor The name of the toner color
     */
    protected function _requireTonerColor ($tonerColor)
    {
        /*
         * Here we build calls to the elements based on the color that came in.
         */
        $partTypes = array(
            'oem',
//            'comp'
        );
        foreach ($partTypes as $partType)
        {
            $sku   = "{$partType}{$tonerColor}TonerSku";
            $yield = "{$partType}{$tonerColor}TonerYield";
            $cost  = "{$partType}{$tonerColor}TonerCost";

            $this->$sku->setRequired(true);
            $this->$yield->setRequired(true);
            $this->$cost->setRequired(true);
        }
    }
}