<?php

/**
 * Class Quotegen_Form_DeviceSetup
 */
class Quotegen_Form_DeviceSetup extends EasyBib_Form
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        /**
         * Add class to form for label alignment
         *
         * - Vertical .form-vertical (not required)    Stacked, left-aligned labels
         * over controls (default)
         * - Inline .form-inline Left-aligned label and inline-block controls
         * for compact style
         * - Search .form-search Extra-rounded text input for a typical search
         * aesthetic
         * - Horizontal .form-horizontal
         *
         * Use .form-horizontal to have same experience as with Bootstrap v1!
         */
        $this->setAttrib('class', 'form-horizontal');

        /*
         * Manufacturer
         */
        $manufacturers = array();
        /* @var $manufacturer Proposalgen_Model_Manufacturer */
        foreach (Proposalgen_Model_Mapper_Manufacturer::getInstance()->fetchAllAvailableManufacturers() as $manufacturer)
        {
            $manufacturers [$manufacturer->id] = $manufacturer->fullname;
        }

        $this->addElement('select', 'manufacturerId', array(
                                                           'label'        => 'Manufacturer:',
                                                           'class'        => 'span3',
                                                           'multiOptions' => $manufacturers
                                                      ));

        /*
         * Printer Model Name
         */
        $this->addElement('text', 'modelName', array(
                                                    'label'      => 'Model Name:',
                                                    'class'      => 'span3',
                                                    'required'   => true,
                                                    'maxlength'  => 255,
                                                    'filters'    => array(
                                                        'StringTrim',
                                                        'StripTags'
                                                    ),
                                                    'validators' => array(
                                                        array(
                                                            'validator' => 'StringLength',
                                                            'options'   => array(
                                                                1,
                                                                255
                                                            )
                                                        )
                                                    )
                                               ));

        /*
         * Is Quote Gen Device
         */
        $this->addElement('checkbox', 'can_sell', array(
                                                       'label'       => 'Can Sell Device:',
                                                       'description' => 'Note: SKU is required when checked.',
                                                       'filters'     => array(
                                                           'Boolean'
                                                       )
                                                  ));

        /*
         * SKU
         */
        $this->addElement('text', 'oemSku', array(
                                                 'label'      => 'OEM SKU:',
                                                 'class'      => 'span2',
                                                 'maxlength'  => 255,
                                                 'required'   => false,
                                                 'filters'    => array(
                                                     'StringTrim',
                                                     'StripTags'
                                                 ),
                                                 'allowEmpty' => false,
                                                 'validators' => array(
                                                     new Tangent_Validate_FieldDependsOnValue('can_sell', '1', array(
                                                                                                                    new Zend_Validate_NotEmpty()
                                                                                                               ), array(
                                                                                                                       'validator' => 'StringLength',
                                                                                                                       'options'   => array(
                                                                                                                           1,
                                                                                                                           255
                                                                                                                       )
                                                                                                                  ))
                                                 )
                                            ));

        $this->addElement('text', 'dealerSku', array(
                                                    'label'      => 'Dealer SKU:',
                                                    'class'      => 'span2',
                                                    'maxlength'  => 255,
                                                    'required'   => false,
                                                    'filters'    => array(
                                                        'StringTrim',
                                                        'StripTags'
                                                    ),
                                                    'validators' => array(
                                                        new Tangent_Validate_FieldDependsOnValue('can_sell', '1', array(
                                                                                                                       new Zend_Validate_NotEmpty()
                                                                                                                  ), array(
                                                                                                                          'validator' => 'StringLength',
                                                                                                                          'options'   => array(
                                                                                                                              1,
                                                                                                                              255
                                                                                                                          )
                                                                                                                     ))
                                                    )
                                               ));

        /*
         * Description of standard features
         */
        $this->addElement('textarea', 'description', array(
                                                          'label'    => 'Standard Features:',
                                                          'style'    => 'height: 100px',
                                                          'required' => false,
                                                          'filters'  => array(
                                                              'StringTrim',
                                                              'StripTags'
                                                          )
                                                     ));

        /*
         * Device Price
         */
        $this->addElement('text', 'cost', array(
                                               'label'      => 'Device Cost:',
                                               'class'      => 'span1',
                                               'prepend'    => '$',
                                               'dimension'  => 1,
                                               'maxlength'  => 8,
                                               'required'   => false,
                                               'allowEmpty' => false,
                                               'filters'    => array(
                                                   'StringTrim',
                                                   'StripTags'
                                               ),
                                               'validators' => array(
                                                   new Tangent_Validate_FieldDependsOnValue('can_sell', '1', array(
                                                                                                                  new Zend_Validate_NotEmpty(),
                                                                                                                  new Zend_Validate_Float(),
                                                                                                                  new Zend_Validate_Between(array(
                                                                                                                                                 'min' => 1,
                                                                                                                                                 'max' => 30000
                                                                                                                                            ))
                                                                                                             ))
                                               )
                                          ));

        /*
         * Toner Configuration
         */
        $tonerConfigs = array();
        /* @var $tonerConfig Proposalgen_Model_TonerConfig */
        foreach (Proposalgen_Model_Mapper_TonerConfig::getInstance()->fetchAll() as $tonerConfig)
        {
            $tonerConfigs [$tonerConfig->tonerConfigId] = $tonerConfig->tonerConfigName;
        }

        $this->addElement('select', 'tonerConfigId', array(
                                                          'label'        => 'Toner Configuration:',
                                                          'class'        => 'span3',
                                                          'required'     => true,
                                                          'multiOptions' => $tonerConfigs
                                                     ));

        /*
         * Hidden Toner Configuration This will be used when editing to hold the toner config id when the dropdown is
         * disabled
         */

        /*
         * Is copier
         */
        $this->addElement('checkbox', 'isCopier', array(
                                                       'label'   => 'Is Copier:',
                                                       'filters' => array(
                                                           'Boolean'
                                                       )
                                                  ));

        /*
         * Is fax
         */
        $this->addElement('checkbox', 'isFax', array(
                                                    'label'   => 'Is Fax:',
                                                    'filters' => array(
                                                        'Boolean'
                                                    )
                                               ));

        /*
         * Is duplex
         */
        $this->addElement('checkbox', 'isDuplex', array(
                                                       'label'   => 'Is Duplex:',
                                                       'filters' => array(
                                                           'Boolean'
                                                       )
                                                  ));

        /*
         * Reports toner levels (JIT Compatible)
         */
        $this->addElement('checkbox', 'reportsTonerLevels', array(
                                                                 'label'   => 'Reports toner levels:',
                                                                 'filters' => array(
                                                                     'Boolean'
                                                                 )
                                                            ));

        /*
         * Printer Wattage (Running)
         */
        $this->addElement('text', 'wattsPowerNormal', array(
                                                           'label'      => 'Watts Power Normal:',
                                                           'class'      => 'span1',
                                                           'maxlength'  => 4,
                                                           'filters'    => array(
                                                               'StringTrim',
                                                               'StripTags'
                                                           ),
                                                           'validators' => array(
                                                               'Int',
                                                               array(
                                                                   'validator' => 'Between',
                                                                   'options'   => array(
                                                                       'min' => 1,
                                                                       'max' => 5000
                                                                   )
                                                               )
                                                           )
                                                      ));

        /*
         * Printer Wattage (Idle)
         */
        $this->addElement('text', 'wattsPowerIdle', array(
                                                         'label'      => 'Watts Power Idle:',
                                                         'class'      => 'span1',
                                                         'maxlength'  => 4,
                                                         'filters'    => array(
                                                             'StringTrim',
                                                             'StripTags'
                                                         ),
                                                         'append'     => 'watts',
                                                         'dimension'  => 1,
                                                         'validators' => array(
                                                             'Int',
                                                             array(
                                                                 'validator' => 'Between',
                                                                 'options'   => array(
                                                                     'min' => 1,
                                                                     'max' => 5000
                                                                 )
                                                             )
                                                         )
                                                    ));

        /*
         * Parts Cost Per Page
         */
        $this->addElement('text', 'partsCostPerPage', array(
                                                           'label'      => 'Parts Cost Per Page:',
                                                           'class'      => 'span1',
                                                           'maxlength'  => 8,
                                                           'allowEmpty' => false,
                                                           'filters'    => array(
                                                               'StringTrim',
                                                               'StripTags'
                                                           ),
                                                           'validators' => array(new Tangent_Validate_FieldDependsOnValue('can_sell', '1', array(
                                                                                                                                                new Zend_Validate_NotEmpty(),
                                                                                                                                                new Zend_Validate_Float(),
                                                                                                                                                new Zend_Validate_Between(array(
                                                                                                                                                                               'min' => 0,
                                                                                                                                                                               'max' => 5,
                                                                                                                                                                          ))
                                                                                                                                           )))
                                                      ));

        /*
        * Labor Cost Per Page
        */
        $this->addElement('text', 'laborCostPerPage', array(
                                                           'label'      => 'Labor Cost Per Page:',
                                                           'class'      => 'span1',
                                                           'maxlength'  => 8,
                                                           'allowEmpty' => false,
                                                           'filters'    => array(
                                                               'StringTrim',
                                                               'StripTags'
                                                           ),
                                                           'validators' => array(new Tangent_Validate_FieldDependsOnValue('can_sell', '1', array(
                                                                                                                                                new Zend_Validate_NotEmpty(),
                                                                                                                                                new Zend_Validate_Float(),
                                                                                                                                                new Zend_Validate_Between(array(
                                                                                                                                                                               'min' => 0,
                                                                                                                                                                               'max' => 5,
                                                                                                                                                                          ))
                                                                                                                                           )))
                                                      ));

        /*
         * Launch Date /
         */
        $minYear    = 1950;
        $maxYear    = ((int)date('Y')) + 2;
        $launchDate = new ZendX_JQuery_Form_Element_DatePicker('launchDate');
        $launchDate->setLabel('Launch Date:')
                   ->setAttrib('class', 'span2')
                   ->setJQueryParam('dateFormat', 'yy-mm-dd')
                   ->setJqueryParam('timeFormat', 'hh:mm')
                   ->setJQueryParam('changeYear', 'true')
                   ->setJqueryParam('changeMonth', 'true')
                   ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}")
                   ->setDescription('yyyy-mm-dd')
                   ->addValidator(new My_Validate_DateTime('/\d{4}-\d{2}-\d{2}/'))
                   ->setRequired(true)
                   ->setAttrib('maxlength', 10)
                   ->addFilters(array(
                                     'StringTrim',
                                     'StripTags'
                                ));
        $this->addElement($launchDate);

        /*
         * Print Speed (Monochrome)
         */
        $this->addElement('text', 'ppmBlack', array(
                                                   'label'      => 'Print Speed (Mono):',
                                                   'class'      => 'span1',
                                                   'maxlength'  => 4,
                                                   'filters'    => array(
                                                       'StringTrim',
                                                       'StripTags'
                                                   ),
                                                   'validators' => array(
                                                       'Int',
                                                       array(
                                                           'validator' => 'Between',
                                                           'options'   => array(
                                                               'min' => 0,
                                                               'max' => 1000
                                                           )
                                                       )
                                                   )
                                              ));

        /*
         * Print Speed (Color)
         */
        $this->addElement('text', 'ppmColor', array(
                                                   'label'      => 'Print Speed (Color):',
                                                   'class'      => 'span1',
                                                   'maxlength'  => 4,
                                                   'filters'    => array(
                                                       'StringTrim',
                                                       'StripTags'
                                                   ),
                                                   'validators' => array(
                                                       'Int',
                                                       array(
                                                           'validator' => 'Between',
                                                           'options'   => array(
                                                               'min' => 0,
                                                               'max' => 1000
                                                           )
                                                       )
                                                   )
                                              ));

        /*
         * Is leased
         */
        $this->addElement('checkbox', 'isLeased', array(
                                                       'label'       => 'Is Leased:',
                                                       'description' => 'Note: Leased Toner Yield is required when checked.',
                                                       'filters'     => array(
                                                           'Boolean'
                                                       )
                                                  ));

        /*
         * Leased Toner Yield
         */
        $this->addElement('text', 'leasedTonerYield', array(
                                                           'label'      => 'Leased Toner Yield:',
                                                           'class'      => 'span1',
                                                           'maxlength'  => 6,
                                                           'filters'    => array(
                                                               'StringTrim',
                                                               'StripTags'
                                                           ),
                                                           'allowEmpty' => false,
                                                           'validators' => array(
                                                               new Tangent_Validate_FieldDependsOnValue('isLeased', '1', array(
                                                                                                                              new Zend_Validate_NotEmpty(),
                                                                                                                              new Zend_Validate_Int(),
                                                                                                                              new Zend_Validate_Between(array(
                                                                                                                                                             'min' => 0,
                                                                                                                                                             'max' => 100000
                                                                                                                                                        ))
                                                                                                                         ))
                                                           )
                                                      ));

        $this->addElement('hidden', 'toner_array');

        // Add the submit button
        $this->addElement('submit', 'submit', array(
                                                   'label' => 'Save'
                                              ));

        // Add the cancel button
        $this->addElement('submit', 'cancel', array(
                                                   'label' => 'Cancel'
                                              ));

        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
