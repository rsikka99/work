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
        

        $firstName = $this->createElement('text', 'firstName')
            ->setRequired(true)
            ->addErrorMessage("Please enter a first name")
            ->setLabel("First Name:");
        //setup contact last name
        $lastName = $this->createElement('text', 'lastName')
            ->setRequired(true)
            ->addErrorMessage("Please enter a last name")
            ->setLabel("Last Name:");
        
        ///////////////////////PHONE NUMBERS/////////////////////////////////////
        

        //Country Code
        $countryCode = $this->createElement('text', 'countryCode')
            ->setRequired(true)
            ->addValidator('regex', true, array (
                '/^\d{1,2}$/' 
        ))
            ->setDescription("")
            ->setLabel("Country Code")
            ->addErrorMessage("Invalid country code");
        
        //Area Code
        $areaCode = $this->createElement('text', 'areaCode')
            ->setRequired(true)
            ->setAttrib('class', 'input-area-code')
            ->addValidator('regex', true, array (
                '/^\d{1,3}$/' 
        ))
            ->setLabel("Area Code")
            ->addErrorMessage("Invalid Area code");
        
        //Exchange Code
        $exchangeCode = $this->createElement('text', 'exchangeCode')
            ->setRequired(true)
            ->addValidator('regex', true, array (
                '/^\d{3}$/' 
        ))
            ->setLabel("3 numbers")
            ->addErrorMessage("Invalid Exchange code");
        
        //Number
        $number = $this->createElement('text', 'number')
            ->setRequired(true)
            ->addValidator('regex', true, array (
                '/^\d{4}$/' 
        ))
            ->setLabel("4 Numbers")
            ->addErrorMessage("Invalid phone number");
        
        //Extension
        $extension = $this->createElement('text', 'extension')
            ->setRequired(true)
            ->addValidator('regex', true, array (
                '/^\d{4}$/' 
        ))
            ->setLabel("Extension")
            ->addErrorMessage("Invalid Area code");
        
        ///////////////////////END PHONE NUMBERS/////////////////////////////////////
        //setup address 1
        $addressLine1 = $this->createElement('text', 'addressLine1')
            ->setRequired(true)
            ->addErrorMessage("Please enter a address")
            ->setLabel("Address 1:");
        //setup address 2
        $addressLine2 = $this->createElement('text', 'addressLine2')->setLabel("Address 2:");
        //setup company city
        $city = $this->createElement('text', 'city')
            ->setRequired(true)
            ->addErrorMessage("Please enter a city")
            ->setLabel("City:");
        //setup state or province, DOES NOT NEED ERROR MESSAGE
        $region = $this->createElement('text', 'region')
            ->setRequired(true)
            ->setLabel("State or Province:")
            ->addErrorMessage("Please enter a state or province")
            ->setDescription("Format: TX");
        //setup zip or postal code, NO ERROR MESSAGE
        $postCode = $this->createElement('text', 'postCode')
            ->setRequired(true)
            ->setDescription("Format : Zip Code: 12345 Postal Code: A1B2C3")
            ->addErrorMessage("Please enter a zip or postal code")
            ->setLabel("Zip or Postal Code:");
        //setup country
        $countryId = $this->createElement('select', 'countryId', array (
                'multiOptions' => array (
                        '1' => 'Canada', 
                        '2' => 'United States' 
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
                $countryId, 
                $region, 
                $city, 
                $addressLine1, 
                $addressLine2, 
                
                $postCode 
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
                                'placement' => Zend_Form_Decorator_Abstract::PREPEND 
                        ) 
                ) 
        ));
        
        //Groups the contact information onto the right side of the screen
        $this->addDisplayGroup(array (
                $firstName, 
                $lastName 
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
                ) 
        ));
        
        $this->addDisplayGroup(array (
                $countryCode, 
                $areaCode, 
                $exchangeCode, 
                $number, 
                $extension 
        )
        , 'phoneNumbers', array (
                'legend' => 'Phone Number' 
        ));
        $phoneNumbers = $this->getDisplayGroup('phoneNumbers');
        $contact->setDecorators(array (
                
                'FormElements', 
                array (
                        'Fieldset', 
                        array (
                                'class' => 'pull-right half-width' 
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
