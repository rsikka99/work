<?php

/**
 * Edit Price Form: used editing the master price of devices and parts in
 * the system.
 *
 * @author Mike Christie
 * @version v1.0
 */
class Proposalgen_Form_EditPrice extends Zend_Form
{

    public function init ()
    {
        $elements = array ();
        $elementCounter = 0;
        $currencyRegex = '/^\d+(?:\.\d{0,2})?$/';
        $this->setName('editPrice_form');
        
        //add device drop down
        $device = new Zend_Form_Element_Select('select_device');
        $device->setLabel('Select Device:')
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->setAttrib('class', 'select_device')
            ->setAttrib('id', 'select_device');
        array_push($elements, $device);
        $elementCounter ++;
        
        //add device price field
        $devicePrice = new Zend_Form_Element_Text('devicePrice');
        $devicePrice->setLabel('Device Price:')
            ->setAttrib('maxlength', 10)
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $devicePrice);
        $elementCounter ++;
        
        //add part drop down
        $device = new Zend_Form_Element_Select('select_part');
        $device->setLabel('Select Part:')
            ->setRequired(true)
            ->setOrder($elementCounter)
            ->setAttrib('class', 'select_part')
            ->setAttrib('id', 'select_part');
        array_push($elements, $device);
        $elementCounter ++;
        
        //add part price field
        $devicePrice = new Zend_Form_Element_Text('partPrice');
        $devicePrice->setLabel('Part Price:')
            ->setRequired(true)
            ->addValidator('regex', false, array (
                'pattern' => $currencyRegex, 
                'messages' => array (
                        'regexNotMatch' => "Must be a dollar value." 
                ) 
        ))
            ->setAttrib('maxlength', 10)
            ->setRequired(true)
            ->setOrder($elementCounter);
        array_push($elements, $devicePrice);
        $elementCounter ++;
        
        //add submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton')
            ->setLabel('Save Changes')
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
        
        $deviceID = new Zend_Form_Element_Hidden('tangent_id_model');
        $deviceID->setAttrib('maxlength', 10)->setOrder($elementCounter);
        array_push($elements, $deviceID);
        
        // add all defined elements to the form
        $this->addElements($elements);
    }
}
?>