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

    public function create ($data)
    {
        $clientId = false;
        $data = $this->validateData($data);
        if ($data !== FALSE)
        {
            $client = new Quotegen_Model_Client($data);
            
            $clientId = Quotegen_Model_Mapper_Client::getInstance()->insert($client);
        }
        
        return $clientId;
    }

    protected function validateData ($data)
    {
        $validData = false;
        if ($this->getForm()->isValid($data))
        {
            $validData = $this->getForm()->getValues();
        }
        else
        {
            $this->getForm()->buildBootstrapErrorDecorators();
        }
        return $validData;
    }
}

