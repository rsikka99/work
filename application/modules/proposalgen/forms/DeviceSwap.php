<?php

class Proposalgen_Form_DeviceSwap extends Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');
        
        $attribs = array (
                'style' => 'width: 250px' 
        );
        
        // Manafucter of the device
        $manufacturerElement = $this->createElement('select', 'manufacturer', array (
                'label' => 'Manufacturer: ', 
                'class' => 'manufacturerId', 
                'attribs' => $attribs 
        ));
        
        // Device list per manufacturer
        $deviceElement = $this->createElement('select', 'masterDevice', array (
                'label' => 'Device: ', 
                'attribs' => $attribs 
        ));
        
        // Add button(s) to form
        $submitButton = $this->createElement('submit', 'addDevice', array (
                'label' => 'Add Device' 
        ));
        $deleteButton = $this->createElement('submit','deleteDevice', array(
                'label' => 'Remove Device'
                ));
        $cancelButton = $this->createElement('submit', 'done', array (
                'label' => 'Done' 
        ));
        
        $this->addElements(array (
                $manufacturerElement, 
                $deviceElement, 
                $submitButton,
                $deleteButton, 
                $cancelButton 
        ));
        
        $manufacturerMapper = new Application_Model_Mapper_Manufacturer();
        $manufacturers = $manufacturerMapper->fetchAll();
        $manufacturerMultiOptions = array ();
        
        /* @var $manufacturer Application_Model_Manufacturer */
        foreach ( $manufacturers as $manufacturer )
        {
            $manufacturerMultiOptions [$manufacturer->getManufacturerId()] = $manufacturer->getManufacturerName();
        }
        
        /**
         * Data for select boxes
         */
        $manufacturerElement->addMultiOptions($manufacturerMultiOptions);
        
        /**
         * Form element decorators
         */
        $submitButton->setDecorators(array (
                'ViewHelper', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'div', 
                                'class' => 'form-actions', 
                                'openOnly' => true, 
                                'placement' => Zend_Form_Decorator_Abstract::PREPEND 
                        ) 
                ) 
        ));
        $deleteButton->setDecorators(array (
                'ViewHelper', 
        ));
        $cancelButton->setDecorators(array (
                'ViewHelper', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'div', 
                                'closeOnly' => true, 
                                'placement' => Zend_Form_Decorator_Abstract::APPEND 
                        ) 
                ) 
        ));
    }
}