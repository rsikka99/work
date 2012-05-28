if this<?php

/**
 * Part Form: Used for Adding / Editing Parts
 *
 * @author	John Sadler
 * @version v1.0
 */

class Proposalgen_Form_Part extends Zend_Form
{

    /**
     * Constructor builds the form
     * @param $options - not used (required) 	
     * @param $type - can be set to 'edit', or null.
     * @return HTML markup for the from is automatically returned by zend_form	 
     */
    public function __construct ($options = null, $type = null)
    {
        //call parent contsructor
        parent::__construct($options);
        $elements = array ();
        $elementCounter = 0;
        
        $this->setName('part_form');
        
        //*****************************************************************
        //PARTS FIELDS
        //*****************************************************************
        

        //SKU
        $partSKU = new Zend_Form_Element_Text('partSKU');
        $partSKU->setLabel('SKU:')
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $partSKU);
        $elementCounter ++;
        
        //price
        $partPrice = new Zend_Form_Element_Text('partPrice');
        $partPrice->setLabel('Part Price:')
            ->setAttrib('maxlength', 10)
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $partPrice);
        $elementCounter ++;
        
        //description
        $partDescription = new Zend_Form_Element_Text('partDescription');
        $partDescription->setLabel('Description:')
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $partDescription);
        $elementCounter ++;
        
        //classification
        $classification = new Zend_Form_Element_Select('classification');
        $classification->setLabel('Classification:')
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $classification);
        $elementCounter ++;
        
        //parttype
        $partType = new Zend_Form_Element_Select('part_type');
        $partType->setLabel('Part Type:')
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $partType);
        $elementCounter ++;
        
        //master part
        $partMaster = new Zend_Form_Element_Checkbox('partMaster');
        $partMaster->setLabel('Master Part:')->setOrder($elementCounter);
        array_push($elements, $partMaster);
        $elementCounter ++;
        
        //save button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton')
            ->setLabel('Save')
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
        
        //back button
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
        
        //add all defined elements to the form
        $this->addElements($elements);
    	
    } //end function __construct
    
}
?>