<?php

/**
 * proposal form:  used for selecting / creating a proposal
 * 
 * @author	Chris Garrah
 * @version v1.0
 */
class Proposalgen_Form_Proposal extends Zend_Form
{

    public function __construct ($options = null, $type = null)
    {
        
        // call parent constructor
        parent::__construct($options, $type);
        $elements = array ();
        $formtype = $type;
        $elementCounter = 0;
        
        //Add listbox for existing profiles
        $reminder = new Zend_Form_Element_Select('select_reminder', array (
                'disableLoadDefaultDecorators' => true 
        ));
        $reminder->setLabel('Select Proposal:')
            ->setOrder($elementCounter)
            ->setAttrib('class', 'select_reminder')
            ->setAttrib('size', '10')
            ->setDecorators(array (
                array (
                        'ViewHelper' 
                ), 
                array (
                        'Errors' 
                ), 
                array (
                        array (
                                'elementdd' => 'HtmlTag' 
                        ), 
                        array (
                                'tag' => 'dd' 
                        ) 
                ), 
                array (
                        'Label', 
                        array (
                                'tag' => 'dt' 
                        ) 
                ) 
        ));
        array_push($elements, $reminder);
        $elementCounter ++;
        
        $numbRequired = new Zend_Form_Element_Text('requiredWorkshops');
        $numbRequired->setLabel('# Required:')
            ->setRequired(true)
            ->setAttrib('size', 2)
            ->setAttrib('maxlength', 3)
            ->setAttrib('title', 'How many workshops must be completed to earn this certificate?')
            ->setOrder($elementCounter)
            ->setAutoInsertNotEmptyValidator(false)
            ->addPrefixPath('validators', 'validators/', 'validate')
            ->addValidator('Validatenumbrequired');
        array_push($elements, $numbRequired);
        $elementCounter ++;
        
        // Add all defined elements to the form     
        $this->addElements($elements);
    
    } // end function __construct
    
} // end class forms_locationForm

?>