<?php
namespace MPSToolbox\Legacy\Modules\Admin\Services;

use Exception;
use MPSToolbox\Legacy\Modules\Admin\Forms\ClientForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\AddressMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ContactMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\CountryMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\AddressModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ContactModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CountryModel;
use Tangent\Logger\Logger;
use Zend_Db_Expr;
use Zend_Db_Table;
use Zend_Filter_Alnum;
use Zend_Filter_StringToUpper;
use Zend_Filter_Word_SeparatorToDash;
use Zend_Validate_PostCode;

/**
 * Class ClientService
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Services
 */
class ClientService
{
    /**
     * The form for a client
     *
     * @var ClientForm
     */
    protected $_form;

    /**
     * Gets the client form
     *
     * @param bool $dealerManagement
     *
     * @return ClientForm
     */
    public function getForm ($dealerManagement = true)
    {
        if (!isset($this->_form))
        {
            $this->_form = new ClientForm($dealerManagement);
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
                $client            = new ClientModel($data);
                $clientId          = ClientMapper::getInstance()->insert($client);
                $data ['clientId'] = $clientId;
                $contact           = new ContactModel($data);
                if (!$contact->isEmpty())
                {
                    ContactMapper::getInstance()->insert($contact);
                }

                $address = new AddressModel($data);
                AddressMapper::getInstance()->insert($address);

                $db->commit();
            }
            catch (Exception $e)
            {
                $db->rollback();

                Logger::logException($e);

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
                $client = ClientMapper::getInstance()->find($clientId);
                if (!$client)
                {
                    $client   = new ClientModel($data);
                    $clientId = ClientMapper::getInstance()->insert($client);
                }
                else
                {
                    $client->populate($data);
                    ClientMapper::getInstance()->save($client);
                }

                //Contact
                $contact = ContactMapper::getInstance()->getContactByClientId($clientId);
                if (!$contact)
                {
                    $contact = new ContactModel($data);
                    if (!$contact->isEmpty())
                    {
                        ContactMapper::getInstance()->insert($contact);
                    }
                }
                else
                {
                    $data['id'] = $contact->id;
                    $contact->populate($data);
                    if ($contact->isEmpty())
                    {
                        ContactMapper::getInstance()->delete($contact);
                    }
                    else
                    {
                        ContactMapper::getInstance()->save($contact);
                    }
                }

                //Address
                $address = AddressMapper::getInstance()->getAddressByClientId($clientId);
                if (!$address)
                {
                    $address = new AddressModel($data);
                    AddressMapper::getInstance()->insert($address);
                }
                else
                {
                    $data['id'] = $address->id;
                    $address->populate($data);
                    AddressMapper::getInstance()->save($address);
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
        return ClientMapper::getInstance()->delete($clientId);
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


        if ($valid)
        {
            return $validData;
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
        $client  = ClientMapper::getInstance()->find($clientId);
        $address = AddressMapper::getInstance()->getAddressByClientId($clientId);
        $contact = ContactMapper::getInstance()->getContactByClientId($clientId);

        $combinedClientData = $client->toArray();
        if ($address) {
            $combinedClientData = array_merge($address->toArray(), $combinedClientData);
        }
        if ($contact) {
            $combinedClientData = array_merge($contact->toArray(), $combinedClientData);
        }
        $this->getForm()->populate($combinedClientData);
    }
}

