<?php

class Admin_Service_Client
{
    /**
     * The form for a client
     *
     * @var Admin_Form_Client
     */
    protected $_form;

    /**
     * Gets the client form
     *
     * @return Admin_Form_Client
     */
    public function getForm ()
    {
        if (! isset($this->_form))
        {
            $this->_form = new Admin_Form_Client();
        }
        return $this->_form;
    }

    /**
     * Creates a client from data
     *
     * @param array $data
     *            An array of data to use when creating a client. The data will be validated using the form.
     * @return int The client id, or false on failed validation.
     */
    public function create ($data)
    {
        $clientId = false;
        $data = $this->validateAndFilterData($data);
        if ($data !== FALSE)
        {
            try
            {
                
                $client = new Quotegen_Model_Client($data);
                $clientId = Quotegen_Model_Mapper_Client::getInstance()->insert($client);
                $contact = new Quotegen_Model_Contact($data);
                $contactId = Quotegen_Model_Mapper_Contact::getInstance()->insert($contact);
                $address = new Quotegen_Model_Address($data);
                $addressId = Quotegen_Model_Mapper_Address::getInstance()->insert($address);
                $clientAddress = new Quotegen_Model_ClientAddress(array (
                        'clientId' => $clientId, 
                        'addressId' => $addressId, 
                        'primaryAddress' => 1, 
                        'name' => '' 
                ));
                Quotegen_Model_Mapper_ClientAddress::getInstance()->insert($clientAddress);
                $clientContact = new Quotegen_Model_ClientContact(array (
                        'clientId' => $clientId, 
                        'contactId' => $contactId 
                ));
                Quotegen_Model_Mapper_ClientContact::getInstance()->insert($clientContact);
            }
            catch ( Exception $e )
            {
                echo "<pre>Var dump initiated at " . __LINE__ . " of:\n" . __FILE__ . "\n\n";
                var_dump($e);
                die();
            }
        }
        
        return $clientId;
    }

    /**
     * Updates a client
     *
     * @param array $data
     *            An array of data to use when creating a client. The data will be validated using the form.
     * @return boolean Whether or not the update was successful
     */
    public function update ($data, $clientId)
    {
        $success = false;
        $data = $this->validateAndFilterData($data);
        if ($data !== FALSE)
        {
            try
            {
                
                //CLIENT
                $client = Quotegen_Model_Mapper_Client::getInstance()->find($clientId);
                if (! $client)
                {
                    $client = new Quotegen_Model_Client($data);
                    $clientId = Quotegen_Model_Mapper_Client::getInstance()->insert($client);
                }
                $client->populate($data);
                $rowsAffected = Quotegen_Model_Mapper_Client::getInstance()->save($client);
                // END CLIENT
                

                //Contact
                $contact = Quotegen_Model_Mapper_ClientContact::getInstance()->getContactByClientId($clientId);
                if (! $contact)
                {
                    $contact = new Quotegen_Model_Contact($data);
                    $contactId = Quotegen_Model_Mapper_Contact::getInstance()->insert($contact);
                    $clientContact = Quotegen_Model_Mapper_ClientContact::getInstance()->findByClientIdAndContactId($clientId, $contactId);
                    if (! $clientContact)
                    {
                        $clientContact = new Quotegen_Model_ClientContact(array (
                                'clientId' => $clientId, 
                                'contactId' => $contactId 
                        ));
                    }
                    Quotegen_Model_Mapper_ClientContact::getInstance()->insert($clientContact);
                }
                $contact->populate($data);
                $rowsAffected = Quotegen_Model_Mapper_Contact::getInstance()->save($contact);
                //End Contact
                

                //Address
                $address = Quotegen_Model_Mapper_ClientAddress::getInstance()->getAddressByClientId($clientId);
                if (! $address)
                {
                    $address = new Quotegen_Model_Address($data);
                    $addressId = Quotegen_Model_Mapper_Address::getInstance()->insert($address);
                    $clientAddress = Quotegen_Model_Mapper_ClientAddress::getInstance()->findByClientIdAndAddressId($clientId, $addressId);
                    if (! $clientAddress)
                    {
                        $clientAddress = new Quotegen_Model_ClientAddress(array (
                                'clientId' => $clientId, 
                                'addressId' => $addressId, 
                                'primaryAddress' => 1, 
                                'name' => '' 
                        ));
                    }
                    Quotegen_Model_Mapper_ClientAddress::getInstance()->insert($clientAddress);
                }
                $address->populate($data);
                $rowsAffected = Quotegen_Model_Mapper_Address::getInstance()->save($address);
            }
            catch ( Exception $e )
            {
            }
            //End Address
            

            return 1;
        }
        else
        {
            return false;
        }
        
        // return $success;
    }

    /**
     * Deletes a client from the database where the id is the parameter $id
     *
     * @param unknown_type $id            
     */
    public function delete ($id)
    {
        try
        {
            //get all the clientAddresses
            $addresses = Quotegen_Model_Mapper_ClientAddress::getInstance()->fetchAll(Quotegen_Model_Mapper_ClientAddress::getInstance()->getWhereId($id));
            Quotegen_Model_Mapper_ClientAddress::getInstance()->delete($id);
            if ($addresses)
            {
                //go through each clientAddress and delete them all
                foreach ( $addresses as $address )
                {
                    Quotegen_Model_Mapper_Address::getInstance()->delete($address->getAddressId());
                }
            }
            $contacts = Quotegen_Model_Mapper_ClientContact::getInstance()->fetchAll(Quotegen_Model_Mapper_ClientContact::getInstance()->getWhereId($id));
            Quotegen_Model_Mapper_ClientContact::getInstance()->delete($id);
            if ($contacts)
                foreach ( $contacts as $contact )
                {
                    Quotegen_Model_Mapper_Contact::getInstance()->delete($contact->getContactId());
                }
            
            Quotegen_Model_Mapper_Client::getInstance()->delete($id);
        }
        catch ( Exception $e )
        {
            return 0;
        }
        return 1;
    }

    /**
     * Validates the data with the form
     *
     * @param array $formData
     *            The array of data to validate
     * @return array The array of valid and filtered data, or false on error.
     */
    protected function validateAndFilterData ($formData)
    {
        $valid = true;
        $form = $this->getForm();
        
        if (! $form->isValid($formData))
        {
            $valid = false;
        }
        $validData = $formData;
        if ($validData ['extension'] == '')
            $validData ['extension'] = null;
            //Make the state always in upper case
        $validData ['region'] = strtoupper($validData ['region']);
        //set the code to validated code
        $code = $this->validateCode($validData ['postCode']);
        
        $phoneErrors [] = $this->getForm()
            ->getElement('countryCode')
            ->getErrors();
        $phoneErrors [] = $this->getForm()
            ->getElement('areaCode')
            ->getErrors();
        $phoneErrors [] = $this->getForm()
            ->getElement('exchangeCode')
            ->getErrors();
        $phoneErrors [] = $this->getForm()
        ->getElement('number')
        ->getErrors();
        $phoneErrors [] = $this->getForm()
            ->getElement('extension')
            ->getErrors();
        $errore = "";
        foreach ( $phoneErrors as $error )
        {
            foreach ( $error as $er )
            {
                $errore .= $er . "<br/>";
            }
        }
        $this->getForm()
            ->getElement('phoneErrors')
            ->addError($errore);
        
        if (! $code)
        {
            
            //Postal or Zip code was invalid, display error on form
            $this->getForm()
                ->getElement('postCode')
                ->clearErrorMessages()
                ->addError('Invalid zip or postal code');
            $valid = false;
        }
        else
        {
            $validData ['postCode'] = $code;
        }
        //If it is not a valid state or province, create error messages
        if (! $this->isValidProvinceOrState($validData ['countryId'], $validData ['region']))
        {
            $this->getForm()
                ->getElement('region')
                ->clearErrorMessages()
                ->addError('Invalid state or province');
            $valid = false;
        }
        if ($valid)
            return $validData;
        return false;
    }

    /**
     * Validates the code, changing it if it can to make it work, then returns the new code
     *
     * @param unknown_type $code            
     * @return Ambigous <valid, boolean, mixed>|unknown|boolean
     */
    protected function validateCode ($code)
    {
        $newCode = $this->isValidPostalCode($code);
        if ($newCode != false)
        {
            return $newCode;
        }
        else if ($this->isValidZipCode($code))
        {
            return $code;
        }
        return false;
    }

    /**
     * Validates a zip code
     *
     * @param $zipcode the
     *            zip code
     * @return valid Returns whether it was valid or not
     *        
     */
    protected function isValidZipCode ($zipCode)
    {
        $postValidator = new Zend_Validate_PostCode('us');
        if ($postValidator->isValid($zipCode))
            
            return true;
        return false;
    }

    /**
     * Validates a Postal Code
     *
     * @param $postalcode the
     *            postal code
     * @return valid Returns whether it was valid or not
     */
    protected function isValidPostalCode ($postalcode)
    {
        $newPostalCode = str_replace(array (
                '-', 
                ' ' 
        ), '', $postalcode);
        if (preg_match("/^([a-ceghj-npr-tv-z]){1}[0-9]{1}[a-ceghj-npr-tv-z]{1}[0-9]{1}[a-ceghj-npr-tv-z]{1}[0-9]{1}$/i", $newPostalCode))
            return $newPostalCode;
        else
            return false;
    }

    /**
     * Checks to see if the country has the parameter $stateOrProv
     *
     * @param string $country            
     * @param string $stateOrProv            
     * @return boolean
     */
    protected function isValidProvinceOrState ($country, $stateOrProv)
    {
        //List of canadian province abbreviations
        $canada = array (
                'AB', 
                'BC', 
                'MB', 
                'NB', 
                'NL', 
                'NT', 
                'NS', 
                'NU', 
                'ON', 
                'PE', 
                'QC', 
                'SK', 
                'YT' 
        );
        //List of american state abreviations
        $usa = array (
                "AK", 
                "AL", 
                "AR", 
                "AS", 
                "AZ", 
                "CA", 
                "CO", 
                "CT", 
                "DC", 
                "DE", 
                "FL", 
                "GA", 
                "GU", 
                "HI", 
                "IA", 
                "ID", 
                "IL", 
                "IN", 
                "KS", 
                "KY", 
                "LA", 
                "MA", 
                "MD", 
                "ME", 
                "MH", 
                "MI", 
                "MN", 
                "MO", 
                "MS", 
                "MT", 
                "NC", 
                "ND", 
                "NE", 
                "NH", 
                "NJ", 
                "NM", 
                "NV", 
                "NY", 
                "OH", 
                "OK", 
                "OR", 
                "PA", 
                "PR", 
                "PW", 
                "RI", 
                "SC", 
                "SD", 
                "TN", 
                "TX", 
                "UT", 
                "VA", 
                "VI", 
                "VT", 
                "WA", 
                "WI", 
                "WV", 
                "WY" 
        );
        //determines which country is selected then returns if the code was valid or not
        if ($country == '1')
        {
            $countryValidator = new Zend_Validate_InArray($canada);
            return $countryValidator->isValid($stateOrProv);
        }
        else if ($country == '2')
        {
            $countryValidator = new Zend_Validate_InArray($usa);
            return $countryValidator->isValid($stateOrProv);
        }
        return false;
    }

    public function populateForm ($clientId)
    {
        $client = Quotegen_Model_Mapper_Client::getInstance()->find($clientId);
        $address = Quotegen_Model_Mapper_ClientAddress::getInstance()->getAddressByClientId($clientId);
        $contact = Quotegen_Model_Mapper_ClientContact::getInstance()->getContactByClientId($clientId);
        $combinedArray = $client->toArray();
        if ($address)
            $combinedArray = array_merge($combinedArray, $address->toArray());
        if ($contact)
            $combinedArray = array_merge($combinedArray, $contact->toArray());
        $this->getForm()->populate($combinedArray);
        //$form->populate(array_merge($client->toArray(),$address->toArray(),$contact->toArray()));
    }
}

