<?php

use Tangent\Controller\Action;

/**
 * Class HardwareLibrary_DevicesController
 */
class HardwareLibrary_ComputersController extends Action
{
    /** @var  boolean */
    private $isAdmin;

    /** @var  \MPSToolbox\Legacy\Models\UserModel */
    protected $identity;

    public function init ()
    {
        $this->isAdmin = $this->view->IsAllowed(\MPSToolbox\Legacy\Models\Acl\AdminAclModel::RESOURCE_ADMIN_TONER_WILDCARD, \MPSToolbox\Legacy\Models\Acl\AppAclModel::PRIVILEGE_ADMIN);
        $this->identity            = Zend_Auth::getInstance()->getIdentity();
    }

    /**
     * Displays all devices
     */
    public function indexAction ()
    {
        $this->_pageTitle    = ['Computers'];
        $this->view->isAdmin = $this->isAdmin;
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

        $hardware = \MPSToolbox\Entities\ExtComputerEntity::find($hardwareId);
        $isAllowed    = ((!$hardware instanceof \MPSToolbox\Entities\ExtComputerEntity || !$hardware->getIsSystemDevice() || $this->isAdmin) ? true : false);

        $service = new \MPSToolbox\Services\HardwareService($hardware, $this->identity->dealerId, $isAllowed, $this->isAdmin);

        if ($hardware instanceof \MPSToolbox\Entities\ExtComputerEntity)
        {
            $this->view->category       = $hardware->getCategory();
            $this->view->modelName      = $hardware->getModelName();
            $this->view->manufacturerId = $hardware->getManufacturer()->getId();
        }
        $this->view->categories     = [
            'Laptop','Desktop','Server','Tablet'
        ];

        $forms = $service->getForms();

        foreach ($forms as $formName => $form) {
            $this->view->$formName = $form;
        }

        $this->view->isAllowed                   = $isAllowed;
        $this->view->manufacturers               = \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper::getInstance()->fetchAllAvailableManufacturers();
        $this->view->hardware                = $hardware;
    }

    public function updateAction ()
    {
        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();

            $hardwareId = $this->getParam('hardwareId', false);
            $category      = $this->getParam('category', false);
            $modelName      = $this->getParam('modelName', false);
            $manufacturerId = $this->getParam('manufacturerId', false);

            $computer = \MPSToolbox\Entities\ExtComputerEntity::find($hardwareId);
            if (!$computer) $computer = new \MPSToolbox\Entities\ExtComputerEntity();

            // Are they allowed to modify data? If they are creating yes, if its not a system device then yes, otherwise use their admin privilege
            $isAllowed                 = ((!$computer->getId() || !$computer->isSystemDevice || $this->isAdmin) ? true : false);
            $service = new \MPSToolbox\Services\HardwareService($computer, $this->identity->dealerId, $isAllowed, $this->isAdmin);

            $forms                      = [];
            $modelAndManufacturerErrors = [];
            $formErrors                 = null;

            // Validate model name and manufacturer
            if ($manufacturerId <= 0) $modelAndManufacturerErrors['modelAndManufacturer']['errorMessages']['manufacturerId'] = "Please select a valid manufacturer";
            if ($category == false) $modelAndManufacturerErrors['modelAndManufacturer']['errorMessages']['category'] = "Please select a category";
            if ($modelName == false) $modelAndManufacturerErrors['modelAndManufacturer']['errorMessages']['modelName'] = "Please enter a model name";

            foreach ($postData as $key => $form) parse_str($postData[$key], $postData[$key]);

            $forms['hardwareAttributes'] = new \MPSToolbox\Forms\ComputersAttributesForm(null, $isAllowed);
            $forms['hardwareImage'] = new \MPSToolbox\Forms\HardwareImageForm(null, $isAllowed);
            $forms['hardwareQuote'] = new \MPSToolbox\Forms\HardwareQuoteForm();

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
                $db = Zend_Db_Table::getDefaultAdapter();
                try
                {
                    $db->beginTransaction();

                    $computer->referenceById('manufacturer', 'MPSToolbox\Entities\ManufacturerEntity', $manufacturerId);
                    $computer->setCategory($category);
                    $computer->setModelName($modelName);
                    $computer->populate($validData['hardwareAttributes']);

                    if ($validData['hardwareImage']['imageUrl'] && (0!==strcmp($validData['hardwareImage']['imageUrl'], $computer->getImageUrl()))) {
                        $service->downloadImageFromImageUrl($validData['hardwareImage']['imageUrl']);
                    } else {
                        $validData['hardwareImage']['imageUrl'] = $computer->getImageUrl();
                    }
                    $computer->save();

                    $service->save($validData);

                    $db->commit();
                    $this->sendJson([
                            "hardwareId" => $computer->getId(),
                            "message" => "Successfully updated hardware",
                            'imageFile' => $computer->getImageFile()]
                    );
                }
                catch (Exception $e)
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
                $computer = \MPSToolbox\Entities\ExtComputerEntity::find($hardwareId);
                $computer->delete();
            }
            $this->sendJson(['ok']);
        }
        $this->sendJsonError('This method only accepts POST');
    }
}
