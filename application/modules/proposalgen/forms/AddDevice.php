<?php

/**
 * Edit Price Form:  used editing the master price of devices and parts in
 * the system.
 *
 * @author	Mike Christie
 * @version v1.0
 */

class Proposalgen_Form_AddDevice extends Zend_Form
{

    public function init ()
    {
        $elements = array ();
        $elementCounter = 0;
        $this->setName('addDevice_form');
        
        //add device manufacturer field
        $deviceManufacturer = new Zend_Form_Element_Text('deviceManufacturer');
        $deviceManufacturer->setLabel('Manufacturer:')
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $deviceManufacturer);
        $elementCounter ++;
        
        //add device model field
        $deviceModel = new Zend_Form_Element_Text('deviceModel');
        $deviceModel->setLabel('Model:')
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $deviceModel);
        $elementCounter ++;
        
        //add part price field
        $devicePrice = new Zend_Form_Element_Text('devicePrice');
        $devicePrice->setLabel('Device Price:')
            ->setAttrib('maxlength', 10)
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $devicePrice);
        $elementCounter ++;
        
        //add copier checkbox
        $is_Copier = new Zend_Form_Element_Checkbox('is_Copier');
        $is_Copier->setLabel('Copier:')->setOrder($elementCounter);
        array_push($elements, $is_Copier);
        $elementCounter ++;
        
        //add color checkbox
        $is_Color = new Zend_Form_Element_Checkbox('is_Color');
        $is_Color->setLabel('Color:')->setOrder($elementCounter);
        array_push($elements, $is_Color);
        $elementCounter ++;
        
        //add copier checkbox
        $is_Scanner = new Zend_Form_Element_Checkbox('is_Scanner');
        $is_Scanner->setLabel('Scanner:')->setOrder($elementCounter);
        array_push($elements, $is_Scanner);
        $elementCounter ++;
        
        //add fax checkbox
        $is_Fax = new Zend_Form_Element_Checkbox('is_Fax');
        $is_Fax->setLabel('Fax:')->setOrder($elementCounter);
        array_push($elements, $is_Fax);
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