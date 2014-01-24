<?php

/**
 * Class Proposalgen_Form_ReplacementPrinter
 */
class Proposalgen_Form_ReplacementPrinter extends Zend_Form
{

    /**
     * @param null|array $options
     */
    public function __construct ($options = null)
    {
        // Call parent constructor
        parent::__construct($options);
        $elements       = array();
        $elementCounter = 0;

        $this->setName('replacement_form');

        //*****************************************************************
        //REPLACEMENT PRINTER FIELDS
        //*****************************************************************


        //hidden mode to toggle between add/edit
        $hiddenMode = new Zend_Form_Element_Hidden('form_mode');
        $hiddenMode->setValue("edit");
        $hiddenMode->setDecorators(array(
            'ViewHelper'
        ));
        array_push($elements, $hiddenMode);
        $elementCounter++;

        //hidden field for replacement_id when in request mode
        $element = new Zend_Form_Element_Hidden('replacement_id');
        $element->setDecorators(array(
            'ViewHelper'
        ));
        array_push($elements, $element);
        $elementCounter++;

        //manufacturers list
        $manufacturer_id = new Zend_Form_Element_Select('manufacturer_id');
        $manufacturer_id->setLabel('Select Manufacturer:')
                        ->setRequired(true)
                        ->setOrder($elementCounter)
                        ->setAttrib('class', 'manufacturer_id')
                        ->setAttrib('id', 'manufacturer_id')
                        ->setDescription('<div class="replacement_field" id="manufacturer_html"></div>')
                        ->setDecorators(array(
                            'ViewHelper',
                            array(
                                'Description',
                                array(
                                    'escape' => false,
                                    'tag'    => false
                                )
                            ),
                            'Errors',
                            array(
                                'HtmlTag',
                                array(
                                    'tag' => 'dd',
                                    'id'  => 'manufacturer_id-element'
                                )
                            ),
                            array(
                                'Label',
                                array(
                                    'tag'   => 'dt',
                                    'class' => 'details_label'
                                )
                            )
                        ));
        array_push($elements, $manufacturer_id);
        $elementCounter++;

        //printer_model list
        $manufacturer_id = new Zend_Form_Element_Select('printer_model');
        $manufacturer_id->setLabel('Select Model:')
                        ->setRequired(true)
                        ->setOrder($elementCounter)
                        ->setAttrib('class', 'printer_model')
                        ->setAttrib('id', 'printer_model')
                        ->setDescription('<div class="replacement_field" id="printer_model_html"></div>')
                        ->setDecorators(array(
                            'ViewHelper',
                            array(
                                'Description',
                                array(
                                    'escape' => false,
                                    'tag'    => false
                                )
                            ),
                            'Errors',
                            array(
                                'HtmlTag',
                                array(
                                    'tag' => 'dd',
                                    'id'  => 'printer_model-element'
                                )
                            ),
                            array(
                                'Label',
                                array(
                                    'tag'   => 'dt',
                                    'class' => 'details_label'
                                )
                            )
                        ));
        array_push($elements, $manufacturer_id);
        $elementCounter++;

        //replacement_category
        $toner_config = new Zend_Form_Element_Select('replacement_category');
        $toner_config->setLabel('Replacement Category:')
                     ->setRequired(true)
                     ->setOrder($elementCounter)
                     ->setAttrib('id', 'replacement_category')
                     ->setDecorators(array(
                         'ViewHelper',
                         array(
                             'Description',
                             array(
                                 'escape' => false,
                                 'tag'    => false
                             )
                         ),
                         'Errors',
                         array(
                             'HtmlTag',
                             array(
                                 'tag' => 'dd',
                                 'id'  => 'replacement_category-element'
                             )
                         ),
                         array(
                             'Label',
                             array(
                                 'tag'   => 'dt',
                                 'class' => 'details_label'
                             )
                         )
                     ));
        array_push($elements, $toner_config);
        $elementCounter++;

        //print_speed
        $element = new Zend_Form_Element_Text('print_speed');
        $element->setLabel('Print Speed:')
                ->setRequired(true)
                ->setAttrib('maxlength', 6)
                ->setAttrib('size', 6)
                ->addValidator(new Zend_Validate_Float())
                ->addValidator(new Zend_Validate_GreaterThan(array(
                    'min' => 1
                )))
                ->setAttrib('style', 'text-align: right')
                ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
                ->setOrder($elementCounter)
                ->setDescription('ppm')
                ->setDecorators(array(
                    'ViewHelper',
                    array(
                        'Description',
                        array(
                            'escape' => false,
                            'tag'    => false
                        )
                    ),
                    'Errors',
                    array(
                        'HtmlTag',
                        array(
                            'tag' => 'dd',
                            'id'  => 'print_speed-element'
                        )
                    ),
                    array(
                        'Label',
                        array(
                            'tag'   => 'dt',
                            'class' => 'details_label'
                        )
                    )
                ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        $element->getValidator('GreaterThan')->setMessage('Must be greater than 0.');
        array_push($elements, $element);
        $elementCounter++;

        //resolution
        $element = new Zend_Form_Element_Text('resolution');
        $element->setLabel('Resolution:')
                ->setRequired(true)
                ->setAttrib('maxlength', 6)
                ->setAttrib('size', 6)
                ->addValidator(new Zend_Validate_Float())
                ->addValidator(new Zend_Validate_GreaterThan(array(
                    'min' => 1
                )))
                ->setAttrib('style', 'text-align: right')
                ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
                ->setOrder($elementCounter)
                ->setDescription('dpi')
                ->setDecorators(array(
                    'ViewHelper',
                    array(
                        'Description',
                        array(
                            'escape' => false,
                            'tag'    => false
                        )
                    ),
                    'Errors',
                    array(
                        'HtmlTag',
                        array(
                            'tag' => 'dd',
                            'id'  => 'resolution-element'
                        )
                    ),
                    array(
                        'Label',
                        array(
                            'tag'   => 'dt',
                            'class' => 'details_label'
                        )
                    )
                ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        $element->getValidator('GreaterThan')->setMessage('Must be greater than 0.');
        array_push($elements, $element);
        $elementCounter++;

        //monthly rate
        $element = new Zend_Form_Element_Text('monthly_rate');
        $element->setLabel('Monthly Rate:')
                ->setRequired(true)
                ->setAttrib('maxlength', 6)
                ->setAttrib('size', 6)
                ->addValidator(new Zend_Validate_Float())
                ->addValidator(new Zend_Validate_GreaterThan(array(
                    'min' => 0
                )))
                ->setAttrib('style', 'text-align: right')
                ->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)')
                ->setOrder($elementCounter)
                ->setDescription('$')
                ->setDecorators(array(
                    array(
                        'Description',
                        array(
                            'escape' => false,
                            'tag'    => false
                        )
                    ),
                    'ViewHelper',
                    'Errors',
                    array(
                        'HtmlTag',
                        array(
                            'tag' => 'dd',
                            'id'  => 'monthly_rate-element'
                        )
                    ),
                    array(
                        'Label',
                        array(
                            'tag'   => 'dt',
                            'class' => 'details_label'
                        )
                    )
                ));
        $element->getValidator('Float')->setMessage('Please enter a number.');
        $element->getValidator('GreaterThan')->setMessage('Must be greater than 0.');
        array_push($elements, $element);

        //add all defined elements to the form
        $this->addElements($elements);
    }
}