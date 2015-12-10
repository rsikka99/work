<?php

namespace Tangent\Controller;

use MPSToolbox\Entities\ExtHardwareEntity;
use MPSToolbox\Services\HardwareService;

abstract class Hardware_Action extends Action
{
    /** @var  boolean */
    private $isAdmin;

    /** @var  \MPSToolbox\Legacy\Models\UserModel */
    protected $identity;

    protected $hardware_type = 'Computers';
    protected $hardware_categories = ['Laptop','Desktop','Server','Tablet'];

    public function init ()
    {
        $this->isAdmin = $this->view->IsAllowed(\MPSToolbox\Legacy\Models\Acl\AdminAclModel::RESOURCE_ADMIN_TONER_WILDCARD, \MPSToolbox\Legacy\Models\Acl\AppAclModel::PRIVILEGE_ADMIN);
        $this->identity            = \Zend_Auth::getInstance()->getIdentity();
    }

    /**
     * Displays all devices
     */
    public function indexAction ()
    {
        $this->_pageTitle    = [$this->hardware_type];
        $this->view->isAdmin = $this->isAdmin;
    }

    protected function getForms($hardwareService) {
        return $hardwareService->getForms();
    }

    public function loadFormsAction ()
    {
        // image upload
        if (!empty($_FILES) && $this->getParam('id')) {
            $hardwareId = $this->_getParam('id', false);
            $hardware = \MPSToolbox\Entities\ExtHardwareEntity::find($hardwareId);
            if (!$hardware) {
                $this->sendJsonError('not found');
                return;
            }

            $isAllowed = ((!$hardware instanceof \MPSToolbox\Entities\ExtHardwareEntity || !$hardware->isSystemDevice || $this->isAdmin) ? true : false);
            if (!$isAllowed) {
                $this->sendJsonError('not allowed');
                return;
            }
            $service = new \MPSToolbox\Services\HardwareService($hardware, $this->identity->dealerId, $isAllowed, $this->isAdmin);
            foreach ($_FILES as $upload) {
                $service->uploadImage($upload);
                $hardware->save();
            }

            $result = array(
                'filename'=>$hardware->getImageFile()
            );
            $this->sendJson($result);
        }

        $this->_helper->layout()->disableLayout();
        $hardwareId = $this->_getParam('hardwareId', false);
        $hardware = $this->getHardware($hardwareId);

        $isAllowed    = ((!$hardware instanceof \MPSToolbox\Entities\ExtHardwareEntity || !$hardware->getIsSystemDevice() || $this->isAdmin) ? true : false);

        $service = new \MPSToolbox\Services\HardwareService($hardware, $this->identity->dealerId, $isAllowed, $this->isAdmin, $this->hardware_type);

        if ($hardware instanceof \MPSToolbox\Entities\ExtHardwareEntity)
        {
            $this->view->category       = $hardware->getCategory();
            $this->view->modelName      = $hardware->getModelName();
            $this->view->manufacturerId = $hardware->getManufacturer()->getId();
        }
        $this->view->categories     = $this->hardware_categories;

        $forms = $this->getForms($service);

        foreach ($forms as $formName => $form) {
            $this->view->$formName = $form;
        }

        $this->view->isAllowed                   = $isAllowed;
        $this->view->manufacturers               = \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper::getInstance()->fetchAllAvailableManufacturers();
        $this->view->hardware                = $hardware;
    }

    /*
    ie
    $hardware = \MPSToolbox\Entities\ExtComputerEntity::find($hardwareId)
    if (!$hardware) $hardware = new \MPSToolbox\Entities\ExtComputerEntity();
    return $hardware
    */
    /**
     * @param $hardwareId
     * @return ExtHardwareEntity
     */
    abstract public function getHardware($hardwareId, $createNew=false);

    public function updateAction ()
    {
        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();

            $hardwareId = $this->getParam('hardwareId', false);
            $category      = $this->getParam('category', false);
            $modelName      = $this->getParam('modelName', false);
            $manufacturerId = $this->getParam('manufacturerId', false);

            $hardware = $this->getHardware($hardwareId, true);

            // Are they allowed to modify data? If they are creating yes, if its not a system device then yes, otherwise use their admin privilege
            $isAllowed                 = ((!$hardware->getId() || !$hardware->isSystemDevice || $this->isAdmin) ? true : false);
            $service = new HardwareService($hardware, $this->identity->dealerId, $isAllowed, $this->isAdmin, $this->hardware_type);

            $forms = $this->getForms($service);
            $modelAndManufacturerErrors = [];
            $formErrors                 = null;

            // Validate model name and manufacturer
            if ($manufacturerId <= 0) $modelAndManufacturerErrors['modelAndManufacturer']['errorMessages']['manufacturerId'] = "Please select a valid manufacturer";
            if ($category == false) $modelAndManufacturerErrors['modelAndManufacturer']['errorMessages']['category'] = "Please select a category";
            if ($modelName == false) $modelAndManufacturerErrors['modelAndManufacturer']['errorMessages']['modelName'] = "Please enter a model name";

            foreach ($postData as $key => $form) parse_str($postData[$key], $postData[$key]);

            $formErrors = [];
            $validData  = [];

            foreach ($forms as $formName => $form)
            {
                $response = $service->validateData($form, $postData[$formName], $formName);
                if (isset($response['errorMessages'])) $formErrors[$formName] = $response;
                else $validData[$formName] = $response;
            }

            /**
             * Check to see if we had errors. If not lets save!
             */
            if ($formErrors || count($modelAndManufacturerErrors) > 0)
            {
                $this->sendJsonError(array_merge($formErrors, $modelAndManufacturerErrors));
            }
            else
            {
                $db = \Zend_Db_Table::getDefaultAdapter();
                try
                {
                    $db->beginTransaction();

                    $hardware->referenceById('manufacturer', 'MPSToolbox\Entities\ManufacturerEntity', $manufacturerId);
                    $hardware->setCategory($category);
                    $hardware->setModelName($modelName);
                    $hardware->populate($validData['hardwareAttributes']);

                    if ($validData['hardwareImage']['imageUrl'] && (0!==strcmp($validData['hardwareImage']['imageUrl'], $hardware->getImageUrl()))) {
                        $service->downloadImageFromImageUrl($validData['hardwareImage']['imageUrl']);
                    } else {
                        $validData['hardwareImage']['imageUrl'] = $hardware->getImageUrl();
                    }
                    $hardware->save();

                    $service->save($validData);

                    $db->commit();
                    $this->sendJson([
                            "hardwareId" => $hardware->getId(),
                            "message" => "Successfully updated hardware",
                            'imageFile' => $hardware->getImageFile()]
                    );
                }
                catch (\Exception $e)
                {
                    $db->rollBack();
                    \Tangent\Logger\Logger::logException($e);
                    $this->sendJsonError($e->getMessage());
                }
            }
        }

        $this->sendJsonError('This method only accepts POST');
    }
    public function deleteAction ()
    {
        if ($this->_request->isPost()) {
            $hardwareId = intval($this->getRequest()->getParam('hardwareId'));
            if ($this->isAdmin && $hardwareId) {
                $hardware = $this->getHardware($hardwareId);
                $hardware->delete();
            }
            $this->sendJson(['ok']);
        }
        $this->sendJsonError('This method only accepts POST');
    }
}
