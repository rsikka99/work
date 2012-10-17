<?php

class Admin_Form_Client extends Twitter_Bootstrap_Form_Horizontal
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
        
        $this->setAttrib('class', 'form-horizontal form-center-actions');
        //setup account number
        $accountNumber = $this->createElement('text', 'accountNumber')->setLabel("Account Number:");
        //setup company name, set required
        $companyName = $this->createElement('text', 'companyName')
            ->setRequired(true)
            ->addErrorMessage("Please enter a company name")
            ->setLabel("Name:");
        //setup company legal name
        $legalName = $this->createElement('text', 'legalName')->setLabel("Legal Name:");
        //setup contact first name
        

        $contactFirstName = $this->createElement('text', 'contactFirstName')
            ->setRequired(true)
            ->addErrorMessage("Please enter a first name")
            ->setLabel("First Name:");
        //setup contact last name
        $contactLastName = $this->createElement('text', 'contactLastName')
            ->setRequired(true)
            ->addErrorMessage("Please enter a last name")
            ->setLabel("Last Name:");
        //setup contact phone number, uses regex to validate
        $contactPhone = $this->createElement('text', 'contactPhone')
            ->setRequired(true)
            ->addValidator('regex', true, array (
                '/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/' 
        ))
            ->setDescription("Format: (123)123-4567")
            ->setLabel("Phone Number:")
            ->addErrorMessage("Invalid phone number");
        //setup address 1
        $companyAddress1 = $this->createElement('text', 'companyAddress1')
            ->setRequired(true)
            ->addErrorMessage("Please enter a address")
            ->setLabel("Address 1:");
        //setup address 2
        $companyAddress2 = $this->createElement('text', 'companyAddress2')->setLabel("Address 2:");
        //setup company city
        $companyCity = $this->createElement('text', 'companyCity')
            ->setRequired(true)
            ->addErrorMessage("Please enter a city")
            ->setLabel("City:");
        //setup state or province, DOES NOT NEED ERROR MESSAGE
        $companyStateOrProv = $this->createElement('text', 'companyStateOrProv')
            ->setRequired(true)
            ->setLabel("State or Province:")
            ->addErrorMessage("Please enter a state or province")
            ->setDescription("Format: TX");
        //setup zip or postal code, NO ERROR MESSAGE
        $companyZipOrPostalCode = $this->createElement('text', 'companyZipOrPostalCode')
            ->setRequired(true)
            ->setDescription("Format : Zip Code: 12345 Postal Code: A1B2C3")
            ->addErrorMessage("Please enter a zip or postal code")
            ->setLabel("Zip or Postal Code:");
        //setup country
        $companyCountry = $this->createElement('select', 'companyCountry', array (
                'multiOptions' => array (
                        'Canada' => 'Canada', 
                        'United States' => 'United States' 
                ), 
                'value' => 'United States' 
        ))->setLabel("Country");
        //setup cancel button
        $cancel = $this->createElement('submit', 'Cancel', array (
                'ignore' => true, 
                'label' => 'Cancel' 
        ));
        //setup submit button
        $submit = $this->createElement('submit', 'Save', array (
                'ignore' => true, 
                'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY 
        ));
        //Groups the Company Information onto the left side of the screen
        $this->addDisplayGroup(array (
                $accountNumber, 
                $companyName, 
                $legalName, 
                $companyCountry, 
                $companyStateOrProv, 
                $companyCity, 
                $companyAddress1, 
                $companyAddress2, 
                
                $companyZipOrPostalCode 
        ), 'company', array (
                'legend' => 'Company Information' 
        ));
        $company = $this->getDisplayGroup('company');
        $company->setDecorators(array (
                
                'FormElements', 
                array (
                        'Fieldset',
                        array (
                                'class' => 'pull-left half-width'
                        )
                         
                ),
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'div', 
                                'openOnly' => true,
                                'class' => 'clearfix',
                                'placement' => Zend_Form_Decorator_Abstract::PREPEND,
                               
                        )
                         
                ) 
        ));
        
        //Groups the contact information onto the right side of the screen
        $this->addDisplayGroup(array (
                $contactFirstName, 
                $contactLastName, 
                $contactPhone 
        ), 'contact', array (
                'legend' => 'Contact Information' 
        ));
        $contact = $this->getDisplayGroup('contact');
        $contact->setDecorators(array (
                
                'FormElements', 
                array (
                        'Fieldset',
                        array (
                        		'class' => 'pull-right half-width'
                        )
                         
                ),
                
                array (
                        'HtmlTag', 
                        array (
                                'tag' => 'div', 
                                'closeOnly' => true,
                                'placement' => Zend_Form_Decorator_Abstract::APPEND,
                        )
                         
                ) 
        ));
        $this->addDisplayGroup(array (
                $submit, 
                $cancel 
        ), 'actions', array (
                'disableLoadDefaultDecorators' => true, 
                'decorators' => array (
                        'Actions' 
                ), 
                'class' => 'form-actions-center' 
                
        ));
        
        // EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
