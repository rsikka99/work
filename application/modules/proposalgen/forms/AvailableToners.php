<?php

/**
 * Class Proposalgen_Form_AvailableToners
 */
class Proposalgen_Form_AvailableToners extends Twitter_Bootstrap_Form_Horizontal
{

    public function init ()
    {
        $this->setMethod('post');
        $allowed = $this->getView()->IsAllowed(Admin_Model_Acl::RESOURCE_ADMIN_TONER_WILDCARD, Application_Model_Acl::PRIVILEGE_ADMIN);
        $manufacturerList = array();
        foreach (Proposalgen_Model_Mapper_Manufacturer::getInstance()->fetchAllAvailableManufacturers() as $manufacturer)
        {
            $manufacturerList [$manufacturer->id] = $manufacturer->displayname;
        }
        $availableTonersManufacturer = $this->createElement('select', 'availableTonersmanufacturerId', array(
                                                                                                          'label' => 'Manufacturer',
                                                                                                     ));
        if(!$allowed)
        {
            $availableTonersManufacturer->setAttrib('disabled','disabled');
        }
        $this->addElement($availableTonersManufacturer);
        $availableTonersManufacturer->addMultiOptions($manufacturerList);

        /*
         * Color
        */
        $colors = array ();
        /* @var $color Proposalgen_Model_TonerColor */
        foreach ( Proposalgen_Model_Mapper_TonerColor::getInstance()->fetchAll() as $color )
        {
            $colors [$color->tonerColorId] = $color->tonerColorName;
        }

        $availableTonersColorIdElement = $this->createElement('select', 'availableTonerstonerColorId', array (
                                                          'label' => 'Color:',
                                                          'class' => 'span2',
                                                          'multiOptions' => $colors
                                                    ));
        if(!$allowed)
        {
            $availableTonersColorIdElement->setAttrib('disabled','disabled');
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
        if(!$allowed)
        {
            $availableTonersSystemSkuElement->setAttrib('readonly','true');
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
                                                          ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');;
        if(!$allowed)
        {
            $availableTonersYieldElement->setAttrib('readonly','true');
        }
        $this->addElement($availableTonersYieldElement);

        $availableTonersDealerCostELement = $this->createElement('text', 'availableTonersdealerCost', array(
                                                                    'label'     => 'Cost',
                                                                    'class'     => 'span3',
                                                                    'required'  => false,
                                                                    'maxlength' => 255,
                                                                    'filters'   => array(
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
                                                                    'label'     => 'System Cost',
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
                                                               ))->setAttrib('onkeypress', 'javascript: return numbersonly(this, event)');;
        if(!$allowed)
        {
            $availableTonersSystemCostElement->setAttrib('readonly','true');
        }
        $this->addElement($availableTonersSystemCostElement);
        $this->addElement('hidden', 'availableTonersid', array());

    }
}