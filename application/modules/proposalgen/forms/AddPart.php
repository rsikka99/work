<?php

/**
 * Edit Price Form: used editing the master price of devices and parts in
 * the system.
 *
 * @author Mike Christie
 * @version v1.0
 */
class Proposalgen_Form_AddPart extends Zend_Form
{

    public function init ()
    {
        $elements = array ();
        $elementCounter = 0;
        $this->setName('addPart_form');
        
        //add device drop down
        $device = new Zend_Form_Element_Select('select_device');
        $device->setLabel('Select Device:')
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->setAttrib('class', 'select_device')
            ->setAttrib('id', 'select_device');
        array_push($elements, $device);
        $elementCounter ++;
        
        //add part manufacturer field
        $partManufacturer = new Zend_Form_Element_Text('partManufacturer');
        $partManufacturer->setLabel('Manufacturer:')
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $partManufacturer);
        $elementCounter ++;
        
        //add part SKU field
        $partSKU = new Zend_Form_Element_Text('partSKU');
        $partSKU->setLabel('SKU:')
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $partSKU);
        $elementCounter ++;
        
        //add part price field
        $partPrice = new Zend_Form_Element_Text('partPrice');
        $partPrice->setLabel('Part Price:')
            ->setAttrib('maxlength', 10)
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $partPrice);
        $elementCounter ++;
        
        //add part description field
        $partDescription = new Zend_Form_Element_Text('partDescription');
        $partDescription->setLabel('Description:')
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $partDescription);
        $elementCounter ++;
        
        //add classification drop down
        $classification = new Zend_Form_Element_Select('classification');
        $classification->setLabel('Classification:')
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $classification);
        $elementCounter ++;
        
        //add part type drop down
        $partType = new Zend_Form_Element_Select('part_type');
        $partType->setLabel('Part Type:')
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $partType);
        $elementCounter ++;
        
        //add submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton')
            ->setLabel('Add')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array (
                        array (
                                'dd' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'dd', 
                                'class' => 'botMenu' 
                        ) 
                ) 
        ));
        array_push($elements, $submit);
        $elementCounter ++;
        
        $back = new Zend_Form_Element_Button('back_button');
        $back->setLabel('Done')
            ->setOrder($elementCounter)
            ->setAttrib('onClick', 'javascript: history.back();')
            ->setDecorators(array (
                'ViewHelper', 
                'Errors', 
                array (
                        array (
                                'dd' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'dd', 
                                'class' => 'botMenu' 
                        ) 
                ) 
        ));
        
        array_push($elements, $back);
        $elementCounter ++;
        
        // add all defined elements to the form
        $this->addElements($elements);
    }
}
?>