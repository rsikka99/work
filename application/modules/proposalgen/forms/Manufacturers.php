<?php

/**
 * Manufacturers Form: Used for Adding / Editing Manufacturers
 *
 * @author John Sadler
 * @version v1.0
 */
class Proposalgen_Form_Manufacturers extends Zend_Form
{

    /**
     * Constructor builds the form
     * 
     * @param $options -
     *            not used (required)
     * @param $type -
     *            can be set to 'edit', or null. Differnt form elements are added for editing an instructor and adding a
     *            new instructor.
     * @return HTML markup for the from is automatically returned by zend_form
     */
    public function __construct ($options = null, $type = null)
    {
        //call parent contsructor
        parent::__construct($options);
        $elements = array ();
        $elementCounter = 0;
        
        $this->setName('manufacturers_form');
        $this->setAttrib('id', 'manufacturers_form');
        $this->setAttrib('class', 'outlined');
        
        //*****************************************************************
        //MANUFACTURER FIELDS
        //*****************************************************************
        

        //hidden id for upload_data_collector_id
        $element = new Zend_Form_Element_Hidden('hdnID');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //hidden field for hdnItem
        $element = new Zend_Form_Element_Hidden('hdnItem');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //hidden field for request_id when in request mode
        $element = new Zend_Form_Element_Hidden('ticket_id');
        $element->setValue(- 1);
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //hidden field for devices_pf_id when in request mode
        $element = new Zend_Form_Element_Hidden('devices_pf_id');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //hidden field for unknown_device_instance_id when in request mode
        $element = new Zend_Form_Element_Hidden('unknown_device_instance_id');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //hidden field for form mode
        $element = new Zend_Form_Element_Hidden('form_mode');
        $element->setDecorators(array (
                'ViewHelper' 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //manufacturers selection list
        $location = new Zend_Form_Element_Select('select_manufacturer');
        $location->setLabel('Select Manufacturer:')
            ->setOrder($elementCounter)
            ->setAttrib('style', 'width:300px')
            ->setAttrib('class', 'select_manufacturer')
            ->setAttrib('id', 'select_manufacturer')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'select_manufacturer-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ));
        array_push($elements, $location);
        $elementCounter ++;
        
        //manufacturers name
        $manufacturerName = new Zend_Form_Element_Text('manufacturer_name');
        $manufacturerName->setLabel('* Manufacturer Name:')
            ->setRequired(true)
            ->setAttrib('size', 50)
            ->setAttrib('maxlength', 50)
            ->setOrder($elementCounter)
            ->setAttrib('id', 'manufacturer_name')
            ->setDecorators(array (
                'ViewHelper', 
                array (
                        'Description', 
                        array (
                                'escape' => false, 
                                'tag' => false 
                        ) 
                ), 
                'Errors', 
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'dd', 
                                'id' => 'manufacturer_name-element' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt', 
                                'class' => 'forms_label' 
                        ) 
                ) 
        ));
        array_push($elements, $manufacturerName);
        $elementCounter ++;
        
        //save button
        $update = new Zend_Form_Element_Submit('save_manufacturer', array (
                'disableLoadDefaultDecorators' => true 
        ));
        $update->setLabel('Save')
            ->setOrder($elementCounter)
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array (
                        array (
                                'data' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'td', 
                                'class' => 'botMenu' 
                        ) 
                ), 
                array (
                        array (
                                'row' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'tr', 
                                'openOnly' => true 
                        ) 
                ) 
        ));
        array_push($elements, $update);
        $elementCounter ++;
        
        //delete button
        $element = new Zend_Form_Element_Submit('delete_manufacturer', array (
                'disableLoadDefaultDecorators' => true 
        ));
        $element->setLabel('Delete')
            ->setOrder($elementCounter)
            ->setAttrib('onclick', 'javascript: return confirm("Are you sure you want to delete this manufacturer?");')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array (
                        array (
                                'data' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'td', 
                                'class' => 'botMenu' 
                        ) 
                ) 
        ));
        array_push($elements, $element);
        $elementCounter ++;
        
        //back button
        $back = new Zend_Form_Element_Button('back_button');
        $back->setLabel('Done')
            ->setOrder($elementCounter)
            ->setAttrib('onClick', 'javascript: document.location.href="../admin";')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array (
                        array (
                                'data' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'td', 
                                'class' => 'botMenu' 
                        ) 
                ), 
                array (
                        array (
                                'row' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'tr', 
                                'closeOnly' => 'true' 
                        ) 
                ) 
        ));
        array_push($elements, $back);
        $elementCounter ++;
        
        $this->setDecorators(array (
                'FormElements', 
                array (
                        array (
                                'data' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'table', 
                                'class' => 'button_menu' 
                        ) 
                ), 
                'Form' 
        ));
        //add all defined elements to the form
        $this->addElements($elements);
    } //end function __construct
}
?>

