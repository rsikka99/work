<?php
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\ProposalgenAclModel;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\AvailableTonersForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\DeleteForm;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Services\TonerService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerTonerAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceTonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerColorMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerTonerAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use Tangent\Controller\Action;
use Tangent\Service\JQGrid;

/**
 * Class HardwareLibrary_TonerController
 */
class HardwareLibrary_TonerController extends Action
{
    /**
     * @var bool
     */
    protected $isMasterHardwareAdmin;

    public function init ()
    {
        $this->isMasterHardwareAdmin = $this->view->IsAllowed(ProposalgenAclModel::RESOURCE_PROPOSALGEN_ADMIN_SAVEANDAPPROVE, AppAclModel::PRIVILEGE_ADMIN);
    }

    /**
     * Displays all devices
     */
    public function indexAction ()
    {
        $this->_pageTitle = array('All Toners');
    }

    /**
     * Loads the available toners form (really just the toner form) for
     * an ajax call
     */
    public function loadFormAction ()
    {
        $tonerId = $this->getParam('tonerId', false);
        $toner   = null;
        if ($tonerId !== false)
        {
            $toner = TonerMapper::getInstance()->find($tonerId);
        }

        $this->view->tonerForm = new AvailableTonersForm(Zend_Auth::getInstance()->getIdentity()->dealerId, $toner);

        $this->_helper->layout()->disableLayout();
    }

    /**
     * Handles creating and saving toner information
     *
     * @throws Zend_Controller_Response_Exception
     */
    public function saveAction ()
    {
        if ($this->getRequest()->isPost())
        {
            try
            {
                $dealerId      = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $userId        = Zend_Auth::getInstance()->getIdentity()->id;
                $creatingToner = false;
                $postData      = $this->getRequest()->getPost();

                $tonerId = $this->getParam('tonerId', false);
                if ((int)$tonerId > 0)
                {
                    $toner = TonerMapper::getInstance()->find($tonerId);

                    if (!$toner instanceof TonerModel)
                    {
                        $this->sendJsonError('Invalid toner ID');
                    }
                }
                else
                {
                    $toner         = new TonerModel();
                    $creatingToner = true;
                }

                $form = new AvailableTonersForm($dealerId, $toner);

                if ($form->isValid($postData))
                {
                    $formData     = $form->getValues();
                    $tonerService = new TonerService($userId, $dealerId, $this->isMasterHardwareAdmin);

                    if ($creatingToner)
                    {
                        $toner = $tonerService->createToner($formData, $userId);
                    }
                    else
                    {
                        if ((int)$toner->tonerColorId !== (int)$formData['tonerColorId'])
                        {
                            $deviceToners = DeviceTonerMapper::getInstance()->fetchDeviceTonersByTonerId($toner->id);
                            if (count($deviceToners) > 0)
                            {
                                $this->getResponse()->setHttpResponseCode(500);
                                $this->sendJson([
                                    'message'       => 'Validation Error',
                                    'errorMessages' => [
                                        'tonerColorId' => [
                                            'assignedToner' => 'You cannot change the color of a toner while it\'s assigned to devices',
                                        ]
                                    ],
                                ]);
                            }
                        }

                        $toner = $tonerService->updateToner($tonerId, $formData);

                        if (!$toner instanceof TonerModel)
                        {
                            throw new Exception("An unhandled error occurred while saving the toner");
                        }
                    }

                    /**
                     * Dealer Attributes
                     */
                    $tonerService->saveDealerAttributes($toner, $formData);

                    /**
                     * Send success message
                     */
                    $this->sendJson([
                        'message' => 'Toner saved successfully',
                        'tonerId' => $toner->id,
                    ]);
                }
                else
                {
                    $this->getResponse()->setHttpResponseCode(500);
                    $this->sendJson([
                        'message'       => 'Validation Error',
                        'errorMessages' => $form->getMessages(),
                    ]);
                }
            }
            catch (Exception $e)
            {
                \Tangent\Logger\Logger::logException($e);
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson([
                    'message' => 'Unhandled Exception',
                    'uid'     => \Tangent\Logger\Logger::getUniqueId(),
                ]);
            }
        }
        else
        {
            $this->sendJsonError('Invalid Method');
        }
    }

    /**
     * Handles creating and saving dealer toner information
     *
     * @throws Zend_Controller_Response_Exception
     */
    public function saveDealerAttributesAction ()
    {
        if ($this->getRequest()->isPost())
        {
            try
            {
                $userId   = Zend_Auth::getInstance()->getIdentity()->id;
                $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $creating = false;
                $postData = $this->getRequest()->getPost();

                $tonerId = $this->getParam('tonerId', false);
                if ((int)$tonerId > 0)
                {
                    $dealerTonerAttribute = DealerTonerAttributeMapper::getInstance()->find(array($tonerId, $dealerId));

                    if (!$dealerTonerAttribute instanceof DealerTonerAttributeModel)
                    {
                        $dealerTonerAttribute = new DealerTonerAttributeModel();
                        $creating             = true;
                    }
                }
                else
                {
                    $this->sendJsonError('You must provide a toner id');
                }

                $form = new AvailableTonersForm($dealerId, $dealerTonerAttribute);

                if ($form->isValid($postData))
                {
                    $formData = $form->getValues();

                    $tonerService = new TonerService($userId, $dealerId, $this->isMasterHardwareAdmin);

                    if ($creating)
                    {
                        $dealerTonerAttribute = $tonerService->createToner($formData);
                    }
                    else
                    {
                        $dealerTonerAttribute = $tonerService->updateToner($tonerId, $formData);

                        if (!$dealerTonerAttribute instanceof TonerModel)
                        {
                            throw new Exception("An unhandled error occurred while saving the toner");
                        }
                    }

                    /**
                     * Send success message
                     */
                    $this->sendJson([
                        'message' => 'Toner saved successfully',
                        'tonerId' => $dealerTonerAttribute->id,
                    ]);
                }
                else
                {
                    $this->getResponse()->setHttpResponseCode(500);
                    $this->sendJson([
                        'message'       => 'Validation Error',
                        'errorMessages' => $form->getMessages(),
                    ]);
                }
            }
            catch (Exception $e)
            {
                \Tangent\Logger\Logger::logException($e);
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson([
                    'message' => 'Unhandled Exception',
                    'uid'     => \Tangent\Logger\Logger::getUniqueId(),
                ]);
            }
        }
        else
        {
            $this->sendJsonError('Invalid Method');
        }
    }

    /**
     * Fetches all the toners
     */
    public function allTonersListAction ()
    {
        $filterManufacturerId = $this->_getParam('filterManufacturerId', false);
        $filterTonerSku       = $this->_getParam('filterTonerSku', false);
        $filterTonerColorId   = $this->_getParam('filterTonerColorId', false);

        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'manufacturer'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10),
        );

        $jqGridService = new JQGrid();

        $jqGridService->setValidSortColumns(array(
            'id',
            'tonerColorId',
            'sku',
            'dealerSku',
            'manufacturer',
            'yield',
            'dealerCost',
            'device_list',
        ));

        $jqGridService->parseJQGridPagingRequest($jqGridParameters);
        $tonerMapper = TonerMapper::getInstance();

        if ($jqGridService->sortingIsValid())
        {
            $jqGridService->setRecordCount(count($tonerMapper->fetchTonersWithMachineCompatibilityUsingColorId(
                null,
                10000,
                0,
                $filterManufacturerId,
                $filterTonerSku,
                $filterTonerColorId
            )));

            // Validate current page number since we don't want to be out of bounds
            if ($jqGridService->getCurrentPage() < 1)
            {
                $jqGridService->setCurrentPage(1);
            }
            else if ($jqGridService->getCurrentPage() > $jqGridService->calculateTotalPages())
            {
                $jqGridService->setCurrentPage($jqGridService->calculateTotalPages());
            }

            // Return a small subset of the results based on the jqGrid parameters
            $startRecord = $jqGridService->getRecordsPerPage() * ($jqGridService->getCurrentPage() - 1);

            if ($startRecord < 0)
            {
                $startRecord = 0;
            }

            $sortOrder = array();

            if ($jqGridService->hasGrouping())
            {
                $sortOrder[] = $jqGridService->getGroupByColumn() . ' ' . $jqGridService->getGroupBySortOrder();
            }

            if ($jqGridService->hasColumns())
            {
                if (strcasecmp($jqGridService->getSortColumn(), 'dealerCost') === 0)
                {
                    $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
                    $sortOrder[] = 'toners.cost' . ' ' . $jqGridService->getSortDirection();
                }
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }


            $jqGridService->setRows($tonerMapper->fetchTonersWithMachineCompatibilityUsingColorId(
                $sortOrder,
                $jqGridService->getRecordsPerPage(),
                $startRecord,
                $filterManufacturerId,
                $filterTonerSku,
                $filterTonerColorId
            ));

            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array(
                'error' => sprintf('Sort index "%s" is not a valid sorting index.', $jqGridService->getSortColumn())
            ));
        }
    }

    /**
     * Returns a json list of toner colors for a toner configuration
     */
    public function colorsForConfigurationAction ()
    {
        $tonerColorList       = array();
        $tonerConfigurationId = $this->getParam('tonerConfigId', false);
        $tonerColorId         = $this->getParam('tonerColorId', false);

        if ($tonerColorId)
        {
            $tonerColor = TonerColorMapper::getInstance()->find($tonerColorId);
            if ($tonerColor instanceof TonerColorModel)
            {
                $this->sendJson(array(
                    "id"   => $tonerColor->id,
                    "text" => $tonerColor->name,
                ));
            }
        }

        if ($tonerConfigurationId > 0)
        {
            if (!isset(TonerConfigModel::$TonerConfigNames[$tonerConfigurationId]))
            {
                $this->sendJsonError('Invalid Toner Configuration');
            }

            foreach (TonerConfigModel::getRequiredTonersForTonerConfig($tonerConfigurationId) as $name => $id)
            {
                $tonerColorList[] = array(
                    "id"   => $id,
                    "text" => $name,
                );
            }
        }
        else
        {
            foreach (TonerColorModel::$ColorNames as $tonerColorId => $tonerColorName)
            {
                $tonerColorList[] = array(
                    "id"   => $tonerColorId,
                    "text" => $tonerColorName,
                );
            }
        }


        $this->sendJson($tonerColorList);
    }
}