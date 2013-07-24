<?php

/**
 * Class Proposalgen_Form_AvailableToners
 */
class Proposalgen_Form_AvailableToners extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_isAllowed;

    /**
     * @param null $options
     * @param bool $isAllowed
     */
    public function __construct ($options = null, $isAllowed = false)
    {
        $this->_isAllowed = $isAllowed;
        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');
        $manufacturerList = array();
        foreach (Proposalgen_Model_Mapper_Manufacturer::getInstance()->fetchAllAvailableManufacturers() as $manufacturer)
        {
            $manufacturerList [$manufacturer->id] = $manufacturer->displayname;
        }

        $availableTonersManufacturer = $this->createElement('select', 'availableTonersmanufacturerId', array(
                                                                                                            'label' => 'Manufacturer',
                                                                                                       ));
        if (!$this->_isAllowed)
        {
            $availableTonersManufacturer->setAttrib('disabled', 'disabled');
        }

        $this->addElement($availableTonersManufacturer);
        $availableTonersManufacturer->addMultiOptions($manufacturerList);

        /*
         * Color
        */
        $colors = array();
        /* @var $color Proposalgen_Model_TonerColor */
        foreach (Proposalgen_Model_Mapper_TonerColor::getInstance()->fetchAll() as $color)
        {
            $colors [$color->tonerColorId] = $color->tonerColorName;
        }

        $availableTonersColorIdElement = $this->createElement('select', 'availableTonerstonerColorId', array(
                                                                                                            'label'        => 'Color:',
                                                                                                            'class'        => 'span2',
                                                                                                            'multiOptions' => $colors
                                                                                                       ));
        if (!$this->_isAllowed)
        {
            $availableTonersColorIdElement->setAttrib('disabled', 'disabled');
        }

        $this->addElement($availableTonersColorIdElement);

        $this->addElement('text', 'availableTonersdealerSku', array(
                                                                   'label'     => 'Your SKU:',
                                                                   'class'     => 'span3',
                                                                   'required'  => false,
                                                                   'maxlength' => 255,
                                                                   'filters'   => array(
                                                                       'StringTrim',
                                                                       'StripTags'
                                                                   ),
                                                                   'validator' => 'StringLength',
                                                                   'options'   => array(
                                                                       1,
                                                                       255
                                                                   )
                                                              ));

        $availableTonersSystemSkuElement = $this->createElement('text', 'availableTonerssystemSku', array(
                                                                                                         'label'     => 'MFG. Part #',
                                                                                                         'class'     => 'span3',
                                                                                                         'required'  => true,
                                                                                                         'maxlength' => 255,
                                                                                                         'filters'   => array(
                                                                                                             'StringTrim',
                                                                                                             'StripTags'
                                                                                                         ),
                                                                                                         'validator' => 'StringLength',
                                                                                                         'options'   => array(
                                                                                                             1,
                                                                                                             255
                                                                                                         )
                                                                                                    ));
        if (!$this->_isAllowed)
        {
            $availableTonersSystemSkuElement->setAttrib('readonly', 'true');
        }

        $this->addElement($availableTonersSystemSkuElement);
        $availableTonersYieldElement = $this->createElement('text', 'availableTonersyield', array(
                                                                                                 'label'     => 'Yield',
                                                                                                 'class'     => 'span3',
                                                                                                 'required'  => true,
                                                                                                 'maxlength' => 255,
                                                                                                 'filters'   => array(
                                                                                                     'StringTrim',
                                                                                                     'StripTags'
                                                                                                 ),
                                                                                                 'validator' => 'StringLength',
                                                                                                 'options'   => array(
                                                                                                     1,
                                                                                                     255
                                                                                                 )
                                                                                            ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');
        if (!$this->_isAllowed)
        {
            $availableTonersYieldElement->setAttrib('readonly', 'true');
        }

        $this->addElement($availableTonersYieldElement);

        $availableTonersDealerCostELement = $this->createElement('text', 'availableTonersdealerCost', array(
                                                                                                           'label'      => 'Cost',
                                                                                                           'class'      => 'span3',
                                                                                                           'required'   => false,
                                                                                                           'maxlength'  => 255,
                                                                                                           'filters'    => array(
                                                                                                               'StringTrim',
                                                                                                               'StripTags'
                                                                                                           ),
                                                                                                           'validators' => array(
                                                                                                               array(
                                                                                                                   'validator' => 'greaterThan',
                                                                                                                   'options'   => array(
                                                                                                                       'min' => 0
                                                                                                                   )
                                                                                                               ),
                                                                                                               'Float'
                                                                                                           )
                                                                                                      ));
        $availableTonersDealerCostELement->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');;
        $this->addElement($availableTonersDealerCostELement);


        $availableTonersSystemCostElement = $this->createElement('text', 'availableTonerssystemCost', array(
                                                                                                           'label'      => "System Cost",
                                                                                                           'class'      => 'span3',
                                                                                                           'required'   => true,
                                                                                                           'maxlength'  => 255,
                                                                                                           'filters'    => array(
                                                                                                               'StringTrim',
                                                                                                               'StripTags'
                                                                                                           ),
                                                                                                           'validators' => array(
                                                                                                               array(
                                                                                                                   'validator' => 'greaterThan',
                                                                                                                   'options'   => array(
                                                                                                                       'min' => 0
                                                                                                                   )
                                                                                                               ),
                                                                                                               'Float'
                                                                                                           )
                                                                                                      ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');;
        if (!$this->_isAllowed)
        {
            $availableTonersSystemCostElement->setAttrib('readonly', 'true');
        }

        $this->addElement($availableTonersSystemCostElement);
        $this->addElement('hidden', 'availableTonersid', array());
    }
}