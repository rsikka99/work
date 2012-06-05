<?php

class Proposalgen_Form_Survey_General extends Proposalgen_Form_Survey_BaseSurveyForm
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        /**
         * Add class to form for label alignment
         *
         * - Vertical .form-vertical (not required)	Stacked, left-aligned labels
         * over controls (default)
         * - Inline .form-inline Left-aligned label and inline-block controls
         * for compact style
         * - Search .form-search Extra-rounded text input for a typical search
         * aesthetic
         * - Horizontal .form-horizontal
         *
         * Use .form-horizontal to have same experience as with Bootstrap v1!
         */
        $this->setAttrib('class', 'surveyForm form-vertical');
        
        
        $numb_employees = new Zend_Form_Element_Text('numb_employees');
        $numb_employees->setAttrib('style', 'width: 35px;')
            ->setAttrib('maxlength', 4)
            ->setRequired(true)
            ->addValidator('digits')
            ->setAutoInsertNotEmptyValidator(true)
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $numb_employees->getValidator('digits')->setMessage('Please enter a number. (With no decimal places)');
        $emplQst = "How many office employees do you have at the site(s) to be covered by managed print services?";
        $numb_employees->setLabel($emplQst);
        $this->addElement($numb_employees);
        
        // Rating Questions are located on the View script because of the unique requirements for the radio buttons.
        

        parent::init();
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'survey/form/general.phtml' 
                        ) 
                ) 
        ));
    }
}