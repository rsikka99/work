<?php

/**
 * Class Admin_Form_Client
 */
class Admin_Form_Client extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * @var bool
     */
    protected $_dealerManagement;

    /**
     * @param bool       $dealerManagement
     * @param null|array $options
     */
    public function __construct ($dealerManagement = false, $options = null)
    {
        $this->_dealerManagement = $dealerManagement;

        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->setAttrib('class', 'form-horizontal form-center-actions');

        // setup account number
        $accountNumber = $this->createElement('text', 'accountNumber')->setLabel("Account Number:");

        // setup company name
        $companyName = $this->createElement('text', 'companyName')
            ->setRequired(true)
            ->addErrorMessage("Please enter a company name")
            ->setLabel("Name:");

        $employeeCount = $this->createElement('text', 'employeeCount')
            ->setRequired(true)
            ->addErrorMessage("Please enter the number of employees")
            ->setLabel("# of employees:");

        // setup legal name
        $legalName = $this->createElement('text', 'legalName')->setLabel("Legal Name:");

        //setup contact first name
        $firstName = $this->createElement('text', 'firstName')
            ->addErrorMessage("Please enter a first name")
            ->setLabel("First Name:")
            ->setAllowEmpty(true);

        //setup contact last name
        $lastName     = $this->createElement('text', 'lastName')
            ->addErrorMessage("Please enter a last name")
            ->setLabel("Last Name:")
            ->setAllowEmpty(true);
        $dealerSelect = null;
        // If they have admin privileges for this, and are not within the dealer form
        if ($this->getView()->IsAllowed(Admin_Model_Acl::RESOURCE_ADMIN_CLIENT_WILDCARD, Application_Model_Acl::PRIVILEGE_ADMIN) && $this->_dealerManagement == false)
        {
            $firstDealerId = null;
            $dealers       = array();
            foreach (Admin_Model_Mapper_Dealer::getInstance()->fetchAll() as $dealer)
            {
                // Use this to grab the first id in the leasing schema dropdown
                if (!$firstDealerId)
                {
                    $firstDealerId = $dealer->id;
                }
                $dealers [$dealer->id] = $dealer->dealerName;
            }
            if ($dealers)
            {
                $dealerSelect = $this->createElement('select', 'dealerId', array(
                                                                                'label'        => 'Dealer:',
                                                                                'class'        => 'input-medium',
                                                                                'multiOptions' => $dealers,
                                                                                'required'     => true,
                                                                                'value'        => $firstDealerId));
            }
        }

        ///////////////////////PHONE NUMBERS/////////////////////////////////////
        //Country Code
        $countryCode = $this->createElement('text', 'countryCode')
            ->setAttrib('class', 'input-country-code')
            ->clearDecorators()
            ->addDecorator('Label', array(
                                         'class' => 'label-phone-number-country control-label'
                                    ))
            ->addValidator('regex', true, array(
                                               '/^\d{1,4}$/'
                                          ))
            ->setDescription("")
            ->setLabel("Phone Number:")
            ->addErrorMessage("Invalid country code");

        //Area Code
        $areaCode = $this->createElement('text', 'areaCode')
            ->setAttrib('class', 'phone-text')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array(
                                         'class' => 'label-phone-number'
                                    ))
            ->setAttrib('class', 'input-area-code')
            ->addValidator('regex', true, array(
                                               '/^\d{3}$/'
                                          ))
            ->setLabel("(")
            ->addErrorMessage("Invalid Area code");

        //Exchange Code
        $exchangeCode = $this->createElement('text', 'exchangeCode')
            ->setAttrib('class', 'input-exchange-code')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array(
                                         'class' => 'label-phone-number'
                                    ))
            ->addValidator('regex', true, array(
                                               '/^\d{3}$/'
                                          ))
            ->setLabel(")")
            ->addErrorMessage("Invalid Exchange code");

        //Number
        $number = $this->createElement('text', 'number')
            ->setAttrib('class', 'input-number-code')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array(
                                         'class' => 'label-phone-number'
                                    ))
            ->addValidator('regex', true, array(
                                               '/^\d{4}$/'
                                          ))
            ->setLabel("-")
            ->addErrorMessage("Invalid phone number");

        //Extension
        $extension   = $this->createElement('text', 'extension')
            ->addValidator('regex', true, array(
                                               '/^\d{0,4}$/'
                                          ))
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array(
                                         'class' => 'label-phone-number'
                                    ))
            ->setLabel("Ext")
            ->setAttrib('class', 'input-extension-code')
            ->addErrorMessage("Invalid extension number");
        $phoneErrors = $this->createElement('text', 'phoneErrors')->removeDecorator('viewhelper');
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
            ->addFilter('StringToUpper')
            ->setLabel("State or Province:")
            ->addErrorMessage("Please enter a state or province")
            ->setDescription("Format: TX");

        //setup zip or postal code, NO ERROR MESSAGE
        $postCode = $this->createElement('text', 'postCode')
            ->setRequired(true)
            ->addFilter('StringToUpper')
            ->setDescription("Format : Zip Code: 12345 Postal Code: A1B2C3")
            ->addErrorMessage("Please enter a zip or postal code")
            ->setLabel("Zip or Postal Code:");

        //Grab a list of all the countries
        $countryList = Quotegen_Model_Mapper_Country::getInstance()->fetchAll();
        $countries   = array();
        foreach ($countryList as $country)
        {
            $countries [$country->id] = $country->name;
        }

        //setup country
        $countryId = $this->createElement('select', 'countryId', array(
                                                                      'required'     => true,
                                                                      'multiOptions' => $countries,
                                                                      'value'        => Quotegen_Model_Country::COUNTRY_UNITED_STATES
                                                                 ))->setLabel("Country:");

        //setup cancel button
        $cancel = $this->createElement('submit', 'Cancel', array(
                                                                'ignore' => true,
                                                                'label'  => 'Cancel'
                                                           ));
        //setup submit button
        $submit = $this->createElement('submit', 'Save', array(
                                                              'ignore'     => true,
                                                              'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
                                                         ));
        //Groups the Company Information onto the left side of the screen
        if ($dealerSelect && $this->_dealerManagement == false)
        {
            $this->addDisplayGroup(array(
                                        $accountNumber,
                                        $companyName,
                                        $legalName,
                                        $employeeCount,
                                        $countryId,
                                        $region,
                                        $city,
                                        $addressLine1,
                                        $addressLine2,
                                        $postCode,
                                        $dealerSelect
                                   ), 'company', array(
                                                      'legend' => 'Company Information'
                                                 ));
        }
        else
        {
            $this->addDisplayGroup(array(
                                        $accountNumber,
                                        $companyName,
                                        $legalName,
                                        $employeeCount,
                                        $countryId,
                                        $region,
                                        $city,
                                        $addressLine1,
                                        $addressLine2,
                                        $postCode
                                   ), 'company', array(
                                                      'legend' => 'Company Information'
                                                 ));
        }
        $company = $this->getDisplayGroup('company');
        $company->setDecorators(array(

                                     'FormElements',
                                     array(
                                         'Fieldset',
                                         array(
                                             'class' => 'pull-left half-width'
                                         )
                                     ),
                                     array(
                                         'HtmlTag',
                                         array(
                                             'tag'       => 'div',
                                             'openOnly'  => true,
                                             'class'     => 'clearfix',
                                             'placement' => Zend_Form_Decorator_Abstract::PREPEND
                                         )
                                     )
                                ));

        //Groups the contact information onto the right side of the screen
        $this->addDisplayGroup(array(
                                    $firstName,
                                    $lastName,
                                    $countryCode,
                                    $areaCode,
                                    $exchangeCode,
                                    $number,
                                    $extension,
                                    $phoneErrors
                               ), 'contact', array(
                                                  'legend' => 'Contact Information'
                                             ));
        $contact = $this->getDisplayGroup('contact');
        $contact->setDecorators(array(
                                     'FormElements',

                                     array(
                                         'Fieldset',
                                         array(
                                             'class' => 'pull-right half-width'
                                         )
                                     ),
                                     array(
                                         'HtmlTag',

                                         array(
                                             'tag'       => 'div',
                                             'closeOnly' => true,
                                             'class'     => 'clearfix',
                                             'placement' => Zend_Form_Decorator_Abstract::APPEND
                                         )
                                     )
                                ));

        $this->addDisplayGroup(array(
                                    $submit,
                                    $cancel
                               ), 'actions', array(
                                                  'disableLoadDefaultDecorators' => true,
                                                  'decorators'                   => array(
                                                      'Actions'
                                                  ),
                                                  'class'                        => 'form-actions-center'
                                             ));
    }
}
