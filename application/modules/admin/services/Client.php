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
            $client = new Quotegen_Model_Client($data);
            $clientId = Quotegen_Model_Mapper_Client::getInstance()->insert($client);
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
            $client = new Quotegen_Model_Client($data);
            $client->setId($clientId);
            $rowsAffected = Quotegen_Model_Mapper_Client::getInstance()->save($client);
            return 1;
        }else{
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
        Quotegen_Model_Mapper_Client::getInstance()->delete($id);
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

        
        if (!$form->isValid($formData)){
            $valid = false;
        }
        $validData = $formData;
        //Make the state always in upper case
        $validData ['companyStateOrProv'] = strtoupper($validData ['companyStateOrProv']);
        
        //create a client using the valid data

        $client = new Quotegen_Model_Client($validData);
        
        //set the code to validated code
        $code = $this->validateCode($client->getCompanyZipOrPostalCode());
        if (! $code)
        {
            
            //Postal or Zip code was invalid, display error on form
            $this->getForm()
                ->getElement('companyZipOrPostalCode')
                ->clearErrorMessages()
                ->addError('Invalid zip or postal code');
            $valid = false;
        }
        else
        {
            $validData ['companyZipOrPostalCode'] = $code;
        }
        //If it is not a valid state or province, create error messages
        if (! $this->isValidProvinceOrState($client->getCompanyCountry(), $client->getCompanyStateOrProv()))
        {
            $this->getForm()
                ->getElement('companyStateOrProv')
                ->clearErrorMessages()
                ->addError('Invalid state or province');
            $valid= false;
        }
        if($valid)
        	return $validData;
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
                        if ($country == 'Canada')
                        {
                            $countryValidator = new Zend_Validate_InArray($canada);
                            return $countryValidator->isValid($stateOrProv);
                        }
                        else if ($country == 'United States')
                        {
                            $countryValidator = new Zend_Validate_InArray($usa);
                            return $countryValidator->isValid($stateOrProv);
                        }
                        return false;
    }
    
}

