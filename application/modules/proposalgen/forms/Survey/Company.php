<?php

class Proposalgen_Form_Survey_Company extends Proposalgen_Form_Survey_BaseSurveyForm
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
        $this->setAttrib('class', 'surveyForm form-horizontal');
        
        $company_name = new Zend_Form_Element_Text('company_name');
        $company_name->setAttrib('maxlength', 40)
            ->setRequired(true)
            ->setAutoInsertNotEmptyValidator(true);
        $companyQst = "Company name:";
        $company_name->setLabel($companyQst);
        $this->addElement($company_name);
        
        $company_address = new Zend_Form_Element_Textarea('company_address');
        $company_address->setAttrib('maxlength', 200)
            ->setRequired(true)
            ->setAutoInsertNotEmptyValidator(true)
            ->setAttrib('tmtw', 'text')
            ->setAttrib('id', '30')
            ->setAttrib('cols', '40')
            ->setAttrib('rows', '5')
            ->setAttrib('style', 'resize: none;');
        $companyAddressQst = "Address:";
        $company_address->setLabel($companyAddressQst);
        $this->addElement($company_address);
        
        parent::init();
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'survey/form/company.phtml' 
                        ) 
                ) 
        ));
    }
}