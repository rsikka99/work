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
            
            if (Quotegen_Model_Mapper_Client::getInstance()->findClientByName($client))
            {
                $this->getForm()
                    ->getElement('name')
                    ->addError('A client with this name already exists');
                $this->getForm()->buildBootstrapErrorDecorators();
            }
            else
            {
                $clientId = Quotegen_Model_Mapper_Client::getInstance()->insert($client);
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
    public function update ($data)
    {
        $success = false;
        $data = $this->validateAndFilterData($data);
        
        if ($data !== FALSE)
        {
            $client = new Quotegen_Model_Client($data);
            
            if (Quotegen_Model_Mapper_Client::getInstance()->findClientByName($client))
            {
                $this->getForm()
                    ->getElement('name')
                    ->addError('A client with this name already exists');
                $this->getForm()->buildBootstrapErrorDecorators();
            }
            else
            {
                $rowsAffected = Quotegen_Model_Mapper_Client::getInstance()->save($client);
                $success = ($rowsAffected);
            }
        }
        
        return $success;
    }

    /**
     * Validates the data with the form
     *
     * @param array $data
     *            The array of data to validate
     * @return array The array of valid and filtered data, or false on error.
     */
    protected function validateAndFilterData ($data)
    {
        $validData = false;
        $form = $this->getForm();
        
        if ($form->isValid($data))
        {
            $validData = $form->getValues();
        }
        else
        {
            $this->getForm()->buildBootstrapErrorDecorators();
        }
        return $validData;
    }
}

