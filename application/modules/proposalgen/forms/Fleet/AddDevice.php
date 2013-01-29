<?php
class Proposalgen_Form_Fleet_AddDevice extends Twitter_Bootstrap_Form_Horizontal
{

    public function init ()
    {

        /*
         * Manufacturer
         */
        $manufacturerIdMultiOptions = array();
        $manufacturers              = Proposalgen_Model_Mapper_Manufacturer::getInstance()->fetchAllAvailableManufacturers();
        foreach ($manufacturers as $manufacturer)
        {
            $manufacturerIdMultiOptions[$manufacturer->id] = $manufacturer->fullname;
        }

        $this->addElement('select', 'manufacturerId', array(
                                                           'dimension'    => 4,
                                                           'label'        => 'Manufacturer',
                                                           'multiOptions' => $manufacturerIdMultiOptions,
                                                           'required'     => true,
                                                      ));
        /*
         * Model Name
         */
        $this->addElement('text', 'modelName', array(
                                                    'dimension' => 4,
                                                    'label'     => 'Model Name',
                                                    'required'  => true,
                                               ));

        /*
         * Device launch date
         */
        $this->addElement('text', 'launchDate', array(
                                                     'class'       => 'datePicker',
                                                     'dimension'   => 2,
                                                     'description' => 'mm/dd/yyyy',
                                                     'label'       => 'Launch Date',
                                                     'maxlength'   => 10,
                                                     'required'    => true,
                                                     'validators'  => array(
                                                         'Date' => array(
                                                             'format' => 'MM/dd/yyyy'
                                                         )
                                                     )
                                                ));

        /*
         * Device Cost
         */
        $this->addElement('text', 'cost', array(
                                               'dimension'  => 2,
                                               'label'      => 'Cost',
                                               'prepend'    => '$',
                                               'validators' => array(
                                                   'Float',
                                                   array('GreaterThan',
                                                         array(
                                                             'min' => 0.00
                                                         )
                                                   )
                                               )
                                          ));

        /*
         * Checkboxes for Copier, Duplex, Fax, Scan
         */
        $this->addElement('checkbox', 'isCopier', array(
                                                       'label'    => 'Copier',
                                                       'filters'  => array(
                                                           'Boolean'
                                                       ),
                                                       'required' => true,
                                                  ));
        $this->addElement('checkbox', 'isDuplex', array(
                                                       'label'    => 'Duplex',
                                                       'filters'  => array(
                                                           'Boolean'
                                                       ),
                                                       'required' => true,
                                                  ));
        $this->addElement('checkbox', 'isFax', array(
                                                    'label'    => 'Fax',
                                                    'filters'  => array(
                                                        'Boolean'
                                                    ),
                                                    'required' => true,
                                               ));
        $this->addElement('checkbox', 'isScan', array(
                                                     'label'    => 'Scan',
                                                     'filters'  => array(
                                                         'Boolean'
                                                     ),
                                                     'required' => true,
                                                ));

        /*
         * Print Speed
         */
        $this->addElement('text', 'ppmBlack', array(
                                                   'dimension'  => 2,
                                                   'label'      => 'PPM Black',
                                                   'append'     => '/Pages Per Minute',
                                                   'validators' => array(
                                                       'Int',
                                                       array('GreaterThan',
                                                             array(
                                                                 'min' => 0.00
                                                             )
                                                       )
                                                   )
                                              ));
        $this->addElement('text', 'ppmColor', array(
                                                   'dimension'  => 2,
                                                   'label'      => 'PPM Color',
                                                   'append'     => '/Pages Per Minute',
                                                   'validators' => array(
                                                       'Int',
                                                       array('GreaterThan',
                                                             array(
                                                                 'min' => 0.00
                                                             )
                                                       )
                                                   )
                                              ));

        /*
         * Duty Cycle
         */
        $this->addElement('text', 'dutyCycle', array(
                                                    'dimension'  => 2,
                                                    'label'      => 'Duty Cycle',
                                                    'append'     => '/Pages Per Month',
                                                    'validators' => array(
                                                        'Int',
                                                        array('GreaterThan',
                                                              array(
                                                                  'min' => 0.00
                                                              )
                                                        )
                                                    )
                                               ));

        /*
         * Wattage
         */
        $this->addElement('text', 'wattsPowerNormal', array(
                                                           'dimension'  => 1,
                                                           'label'      => 'Watts Power Normal',
                                                           'append'     => ' Watts',
                                                           'required'   => true,
                                                           'validators' => array(
                                                               'Int',
                                                               array('GreaterThan',
                                                                     array(
                                                                         'min' => 0.00
                                                                     )
                                                               )
                                                           )
                                                      ));

        $this->addElement('text', 'wattsPowerIdle', array(
                                                         'dimension'  => 1,
                                                         'append'     => ' Watts',
                                                         'label'      => 'Watts Power Idle',
                                                         'required'   => true,
                                                         'validators' => array(
                                                             'Int',
                                                             array('GreaterThan',
                                                                   array(
                                                                       'min' => 0.00
                                                                   )
                                                             )
                                                         )
                                                    ));
        /*
         * Leased Checkbox
         */
        $this->addElement('checkbox', 'isLeased', array(
                                                       'label'    => 'Leased',
                                                       'filters'  => array(
                                                           'Boolean'
                                                       ),
                                                       'required' => true,
                                                  ));
        /*
         * Toner Configuration
         */
        $tonerConfigurationMultiOptions = Proposalgen_Model_TonerConfig::$TonerConfigNames;
        $this->addElement('select', 'tonerConfigId', array(
                                                          'dimension'    => 2,
                                                          'label'        => 'Toner Configuration',
                                                          'multiOptions' => $tonerConfigurationMultiOptions,
                                                          'required'     => true
                                                     ));

        /*
         * Toners
         */
        $tonerColors = array(
            'Black'       => 'black',
            'Cyan'        => 'cyan',
            'Magenta'     => 'magenta',
            'Yellow'      => 'yellow',
            'Three Color' => 'threeColor',
            'Four Color'  => 'fourColor',
        );
        foreach ($tonerColors as $tonerColorName => $fieldName)
        {
            $this->addElement('text', $fieldName . 'TonerSku', array(
                                                                    'dimension'  => 2,
                                                                    'label'      => 'Sku',
                                                                    'validators' => array(
                                                                        'Alnum'
                                                                    ),
                                                               ));
            $this->addElement('text', $fieldName . 'TonerYield', array(
                                                                      'dimension' => 2,
                                                                      'label'     => 'Yield',
                                                                 ));
            $this->addElement('text', $fieldName . 'TonerCost', array(
                                                                     'dimension' => 1,
                                                                     'label'     => 'Cost',
                                                                     'prepend'   => '$',
                                                                ));

            $this->addDisplayGroup(array($fieldName . 'TonerSku', $fieldName . 'TonerYield', $fieldName . 'TonerCost'), $fieldName . 'TonerFieldset', array('legend' => "{$tonerColorName} Toner"));
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
}