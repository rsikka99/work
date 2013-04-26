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
     * @param bool $dealerManagement
     *
     * @return Admin_Form_Client
     */
    public function getForm ($dealerManagement = true)
    {
        if (!isset($this->_form))
        {
            $this->_form = new Admin_Form_Client($dealerManagement);
        }

        return $this->_form;
    }

    /**
     * Creates a client from data
     *
     * @param array $data
     *            An array of data to use when creating a client. The data will be validated using the form.
     *
     * @return int The client id, or false on failed validation.
     */
    public function create ($data)
    {
        $clientId = false;
        $data     = $this->validateAndFilterData($data);
        if ($data !== false)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try
            {
                $client            = new Quotegen_Model_Client($data);
                $clientId          = Quotegen_Model_Mapper_Client::getInstance()->insert($client);
                $data ['clientId'] = $clientId;
                $contact           = new Quotegen_Model_Contact($data);
                if (!$contact->isEmpty())
                {
                    Quotegen_Model_Mapper_Contact::getInstance()->insert($contact);
                }

                $address = new Quotegen_Model_Address($data);
                Quotegen_Model_Mapper_Address::getInstance()->insert($address);

                // Created a hardware optimization for the client
                Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance()->insert(new Hardwareoptimization_Model_Hardware_Optimization(array("clientId" => $clientId)));
                $db->commit();
            }
            catch (Exception $e)
            {
                $db->rollback();

                return false;
            }
        }

        return $clientId;
    }

    /**
     * Updates a client
     *
     * @param array $data
     *            An array of data to use when creating a client. The data will be validated using the form.
     * @param int   $clientId
     *            A clientId to be updated
     *
     * @return boolean Whether or not the update was successful
     */
    public function update ($data, $clientId)
    {
        $success = false;
        $data    = $this->validateAndFilterData($data);
        if ($data !== false)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try
            {
                $data ['clientId'] = $clientId;

                //CLIENT
                $client = Quotegen_Model_Mapper_Client::getInstance()->find($clientId);
                if (!$client)
                {
                    $client   = new Quotegen_Model_Client($data);
                    $clientId = Quotegen_Model_Mapper_Client::getInstance()->insert($client);
                }
                else
                {
                    $client->populate($data);
                    $rowsAffected = Quotegen_Model_Mapper_Client::getInstance()->save($client);
                }

                //Contact
                $contact = Quotegen_Model_Mapper_Contact::getInstance()->getContactByClientId($clientId);
                if (!$contact)
                {
                    $contact = new Quotegen_Model_Contact($data);
                    if (!$contact->isEmpty())
                    {
                        $contactId = Quotegen_Model_Mapper_Contact::getInstance()->insert($contact);
                    }
                }
                else
                {
                    $contact->populate($data);
                    if ($contact->isEmpty())
                    {
                        Quotegen_Model_Mapper_Contact::getInstance()->delete($contact);
                    }
                    else
                    {
                        $rowsAffected = Quotegen_Model_Mapper_Contact::getInstance()->save($contact);
                    }
                }

                //Address
                $address = Quotegen_Model_Mapper_Address::getInstance()->getAddressByClientId($clientId);
                if (!$address)
                {
                    $address   = new Quotegen_Model_Address($data);
                    $addressId = Quotegen_Model_Mapper_Address::getInstance()->insert($address);
                }
                else
                {
                    $address->populate($data);
                    $rowsAffected = Quotegen_Model_Mapper_Address::getInstance()->save($address);
                }

                $success = true;
                $db->commit();
            }
            catch (Exception $e)
            {
                $db->rollback();
            }
        }

        return $success;
    }

    /**
     * Deletes a client from the database where the id is the parameter $id
     *
     * @param int $clientId
     *            The clients id number
     *
     * @return boolean|int Returns true if deleted, false if not deleted.
     */
    public function delete ($clientId)
    {
        try
        {
            return Quotegen_Model_Mapper_Client::getInstance()->delete($clientId);
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    /**
     * Validates the data with the form
     *
     * @param array $formData
     *            The array of data to validate
     *
     * @return array The array of valid and filtered data, or false on error.
     */
    protected function validateAndFilterData ($formData)
    {
        $valid = true;
        $form  = $this->getForm();

        if (!$form->isValid($formData))
        {
            $valid = false;
        }
        $validData = $formData;

        $phoneFields       = array(
            'areaCode',
            'exchangeCode',
            'number',
            'extension'
        );
        $phoneErrors       = array();
        $countValidNumbers = 0;
        foreach ($phoneFields as $phoneField)
        {
            $formElement = $form->getElement($phoneField);
            $value       = $validData [$phoneField];
            if (!is_numeric($value))
            {
                $validData [$phoneField] = new Zend_Db_Expr("NULL");
            }
            else
            {
                if ($phoneField != 'extension')
                {
                    $countValidNumbers++;
                }
            }

            if ($formElement->hasErrors())
            {
                $valid          = false;
                $phoneErrors [] = implode('<br/>', $formElement->getErrors());
            }
        }

        /*
         * Validate whether it is a full phone number or not
         */
        if (($countValidNumbers == 0 && !($validData ['extension'] instanceof Zend_Db_Expr)) || ($countValidNumbers > 0 && $countValidNumbers != 3))
        {

            $valid          = false;
            $phoneErrors [] = "Fill in all phone number fields";
        }
        else if ($countValidNumbers == 3)
        {
            $validData ['countryCode'] = 1;
        }
        else
        {
            $validData ['countryCode'] = new Zend_Db_Expr("NULL");
        }

        $this->getForm()
            ->getElement('phoneErrors')
            ->addError(implode(', ', $phoneErrors));

        //validate and get the valid the postCode
        $postCode = $this->validatePostCode($validData ['postCode'], $validData ['countryId']);
        if (!$postCode)
        {
            //postCode code was invalid, display error on form
            $this->getForm()
                ->getElement('postCode')
                ->clearErrorMessages()
                ->addError('Invalid zip or postal code');
            $valid = false;
        }
        else
        {
            $validData ['postCode'] = $postCode;
        }

        //If it is not a valid state or province, create error messages
        $regionId = $this->isValidRegion($validData ['countryId'], $validData ['region']);
        if ($regionId == false)
        {
            $this->getForm()
                ->getElement('region')
                ->clearErrorMessages()
                ->addError('Invalid state or province');
            $valid = false;
        }
        else
        {
            $validData ['region'] = $regionId;
        }
        if ($valid)
        {
            return $validData;
        }

        return false;
    }

    /**
     * Validates a post code and standardizes the format
     *
     * @param string $postCode
     * @param int    $countryId
     *
     * @return string | boolean
     */
    protected function validatePostCode ($postCode, $countryId)
    {
        $filter   = new Zend_Filter_StringToUpper();
        $postCode = $filter->filter($postCode);
        //Filter data to have a standard for each country
        switch ($countryId)
        {
            //If canada remove all non alphanumeric characters
            case Quotegen_Model_Country::COUNTRY_CANADA :
                $filter   = new Zend_Filter_Alnum(false);
                $postCode = $filter->filter($postCode);
                break;

            //If united states change all spaces to - so we have a standard format of 12345-1234
            case Quotegen_Model_Country::COUNTRY_UNITED_STATES :
                $filter   = new Zend_Filter_Word_SeparatorToDash(' ');
                $postCode = $filter->filter($postCode);
                break;
        }

        $country       = Quotegen_Model_Mapper_Country::getInstance()->find($countryId);
        $postValidator = new Zend_Validate_PostCode($country->locale);
        if ($postValidator->isValid($postCode))
        {
            return $postCode;
        }

        return false;
    }

    /**
     * Checks to see if the country has that region inside it.
     *
     * @param string $countryId
     * @param string $region
     *
     * @return boolean
     */
    protected function isValidRegion ($countryId, $regionName)
    {
        //Try to find a the region they are looking for in the specified country
        $region = Quotegen_Model_Mapper_Region::getInstance()->getByRegionNameAndCountryId($regionName, $countryId);
        if ($region)
        {
            return $region->id;
        }

        return false;
    }

    /**
     * This fills all the values out in a form.
     *
     * @param int $clientId
     */
    public function populateForm ($clientId)
    {
        $client  = Quotegen_Model_Mapper_Client::getInstance()->find($clientId);
        $address = Quotegen_Model_Mapper_Address::getInstance()->getAddressByClientId($clientId);

        $address->region = Quotegen_Model_Mapper_Region::getInstance()->getById($address->region)
            ->region;

        $contact            = Quotegen_Model_Mapper_Contact::getInstance()->getContactByClientId($clientId);
        $combinedClientData = $client->toArray();
        if ($address)
        {
            $combinedClientData = array_merge($combinedClientData, $address->toArray());
        }
        if ($contact)
        {
            $combinedClientData = array_merge($combinedClientData, $contact->toArray());
        }
        $this->getForm()->populate($combinedClientData);
    }
}

