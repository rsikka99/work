<?php
namespace MPSToolbox\Legacy\Modules\Admin\Services;

use Exception;
use MPSToolbox\Api\PrintFleet;
use MPSToolbox\Legacy\Entities\DealerEntity;
use MPSToolbox\Legacy\Modules\Admin\Forms\ClientForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\AddressMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ContactMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\CountryMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\AddressModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ContactModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\CountryModel;
use MPSToolbox\Services\RmsUpdateService;
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

    private function recursivePrintFleet($groups, $ids, &$result, $parentName='') {
        foreach ($groups as $group) {
            $children = $group['children'];
            unset($group['children']);
            $name = $group['name'];
            if (in_array($group['id'], $ids)) {
                if ($group['groupType']=='Office/Location') {
                    $group['name'] = trim($parentName.' > '.$group['name']);
                }
                $result[] = $group;
            }
            if (!empty($children)) $this->recursivePrintFleet($children, $ids, $result, $name);
        }
    }

    public function importFromFmaudit(array $ids, $csv_filename) {
        $dealerId = DealerEntity::getDealerId();
        $result = [];
        $inserted = false;
        foreach ($ids as $name) {
            $name = trim($name);
            if (!empty($name)) {
                $client = ClientMapper::getInstance()->findByName($dealerId, $name);
                if (!$client) {
                    $client = new ClientModel();
                    $client->dealerId = $dealerId;
                    $client->companyName = $name;
                    ClientMapper::getInstance()->insert($client);
                    $inserted = true;
                    $result[$client->id] = 'i';
                } else {
                    $result[$client->id] = 'u';
                }
            }
        }
        if ($inserted) {
            $rmsUpdateService = new RmsUpdateService();
            $rmsUpdateService->updateFmauditCsv($dealerId, $csv_filename);
        }
        return $result;
    }

    public function importFromPrintFleet(PrintFleet $printFleet, array $ids) {
        $result = [];
        $arr = [];
        $this->recursivePrintFleet($printFleet->groups(), $ids, $arr);

        $rmsUpdateService = new RmsUpdateService();

        foreach ($arr as $group) {
            $client = ClientMapper::getInstance()->fetchByRmsId($group['id']);
            $group_result='u';
            if (!$client) {
                $client = new ClientModel();
                $client->dealerId = DealerEntity::getDealerId();
                $client->deviceGroup = $group['id'];
                $group_result='i';
            }
            $client->companyName = $group['name'];
            if (isset($group['additionalDetails']['number of Employees'])) $client->employeeCount = $group['additionalDetails']['number of Employees'];
            if (isset($group['additionalDetails']['industry Vertical'])) $client->industry = $group['additionalDetails']['industry Vertical'];
            $client->id ? ClientMapper::getInstance()->save($client) : $client->id = ClientMapper::getInstance()->insert($client);
            $result[$client->id] = $group_result;

            $contact = $client->getContact();
            if (!$contact) {
                $contact = new ContactModel();
                $contact->clientId = $client->id;
            }
            if (isset($group['additionalDetails']['email'])) $contact->email = $group['additionalDetails']['email'];
            if (isset($group['additionalDetails']['email']) && empty($contact->emailSupply)) $contact->emailSupply = $group['additionalDetails']['email'];
            if (isset($group['additionalDetails']['phone'])) $contact->phoneNumber = $group['additionalDetails']['phone'];
            if (isset($group['additionalDetails']['website URL'])) $contact->website = $group['additionalDetails']['website URL'];
            $contact->id ? ContactMapper::getInstance()->save($contact) : ContactMapper::getInstance()->insert($contact);

            $address = $client->getAddress();
            if (!$address) {
                $address = new AddressModel();
                $address->clientId = $client->id;
            }

            if (isset($group['additionalDetails']['street 1'])) $address->addressLine1 = $group['additionalDetails']['street 1'];
            if (isset($group['additionalDetails']['street 2'])) $address->addressLine2 = $group['additionalDetails']['street 2'];
            if (isset($group['additionalDetails']['city'])) $address->city = $group['additionalDetails']['city'];
            if (isset($group['additionalDetails']['country'])) $address->countryId = CountryMapper::getInstance()->nameToId($group['additionalDetails']['country']);;
            if (isset($group['additionalDetails']['ziP/Postal Code'])) $address->postCode = $group['additionalDetails']['ziP/Postal Code'];
            if (isset($group['additionalDetails']['state/Prov'])) $address->region = $group['additionalDetails']['state/Prov'];
            $address->id ? AddressMapper::getInstance()->save($address) : AddressMapper::getInstance()->insert($address);

            if ($group_result=='i') {
                $rmsUpdateService->updateFromPrintfleet($client->id, $printFleet, $group['id']);
            }
        }
        return $result;
    }

}

