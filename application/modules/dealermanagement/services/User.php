<?php

class Dealermanagement_Service_User
{
    /**
     * The form
     *
     * @var Dealermanagement_Form_User
     */
    protected $_form;

    /**
     *
     * @param Admin_Model_Role[] $roles
     *
     * @param bool               $createMode
     *
     * @return Dealermanagement_Form_User
     */
    public function getForm ($roles, $createMode = false)
    {
        if (!isset($this->_form))
        {
            $this->_form = new Dealermanagement_Form_User($roles, $createMode);
        }

        return $this->_form;
    }

    /**
     * Handles creation
     *
     * @param array $data The post data
     *
     * @return bool
     */
    public function create ($data)
    {
        $success      = false;
        $filteredData = $this->validateAndFilterData($data);
        if ($filteredData !== false)
        {
            $success = true;
        }

        return $success;
    }

    /**
     * Handles updates
     *
     * @param $data
     * @param $id
     */
    public function update ($data, $id)
    {

    }

    /**
     * Handles deletion
     *
     * @param int $id The id to delete
     *
     * @return int The number of rows deleted.
     */
    public function delete ($id)
    {
        return Quotegen_Model_Mapper_Client::getInstance()->delete($id);
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

    }
}