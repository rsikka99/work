<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\DeviceSwapReasonsForm;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\DeviceSwapsForm;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\HardwareOptimizationNavigationForm;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapReasonMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapReasonCategoryMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapReasonDefaultMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonCategoryModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonDefaultModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\ViewModels\DeviceSwapViewModel;
use Tangent\Controller\Action;
use Tangent\Service\JQGrid;

/**
 * Class Hardwareoptimization_DeviceswapsController
 */
class Hardwareoptimization_DeviceswapsController extends Action
{
    /**
     * The Zend_Auth identity
     *
     * @var stdClass
     */
    protected $_identity;

    public function init ()
    {
        $this->_identity = Zend_Auth::getInstance()->getIdentity();
    }

    public function indexAction ()
    {
        $this->_pageTitle = ['Device Swaps'];
        $form             = new DeviceSwapsForm();
        $deviceSwapForm   = new DeviceSwapReasonsForm();

        $this->view->deviceSwap     = new DeviceSwapViewModel();
        $this->view->reasons        = DeviceSwapReasonMapper::getInstance()->fetchAllReasonByDealerId($this->_identity->dealerId);
        $this->view->navigationForm = new HardwareOptimizationNavigationForm();
        $this->view->form           = $form;
        $this->view->deviceSwapForm = $deviceSwapForm;
    }

    public function deviceSwapListAction ()
    {
        $jsonArray        = [];
        $jqGridService    = new JQGrid();
        $deviceSwapMapper = DeviceSwapMapper::getInstance();

        /*
         * Grab the incoming parameters
         */
        $jqGridServiceParameters = [
            'sidx' => $this->_getParam('sidx', 'minimumPageCount'),
            'sord' => $this->_getParam('sord', 'asc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10)
        ];

        $jqGridService->setSortColumn('minimumPageCount');
        $jqGridService->setSortDirection('asc');

        // Set up validation arrays
        $blankModel    = new DeviceSwapModel();
        $sortColumns   = array_keys($blankModel->toArray());
        $sortColumns[] = "device_name";
        $sortColumns[] = "deviceType";

        $jqGridService->setValidSortColumns($sortColumns);

        $groupColumns   = [];
        $groupColumns[] = "deviceType";
        $jqGridService->setValidGroupByColumns($groupColumns);

        $deviceSwapViewModel = new DeviceSwapViewModel();
        $costPerPageSetting  = $deviceSwapViewModel->getCostPerPageSetting();

        $jqGridService->parseJQGridPagingRequest($jqGridServiceParameters);

        if ($jqGridService->sortingIsValid())
        {
            $jqGridService->setRecordCount($deviceSwapMapper->fetAllForDealer($this->_identity->dealerId, null, null, null, null, true));

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

            $sortOrder = [];

            if ($jqGridService->hasGrouping())
            {
                $sortOrder[] = $jqGridService->getGroupByColumn() . ' ' . $jqGridService->getGroupBySortOrder();
            }

            if ($jqGridService->hasColumns())
            {
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }


            $jqGridService->setRows($deviceSwapMapper->fetAllForDealer($this->_identity->dealerId, $costPerPageSetting, $sortOrder, $jqGridService->getSortDirection()), $jqGridService->getRecordsPerPage(), $startRecord);

            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson([
                'error' => sprintf('Sort index "%s" or Group index "%s" is not a valid.', $jqGridService->getSortColumn(), $jqGridService->getGroupByColumn())
            ]);
        }

        $json = json_encode($jsonArray);
        $this->sendJson($json);
    }

    public function deviceReasonListAction ()
    {
        $jsonArray              = [];
        $jqGridService          = new JQGrid();
        $deviceSwapReasonMapper = DeviceSwapReasonMapper::getInstance();

        /*
         * Grab the incoming parameters
         */
        $jqGridServiceParameters = [
            'sidx' => $this->_getParam('sidx', 'reason'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10)
        ];

        // Set up validation arrays
        $blankModel  = new DeviceSwapReasonModel();
        $sortColumns = array_keys($blankModel->toArray());

        $jqGridService->parseJQGridPagingRequest($jqGridServiceParameters);
        $jqGridService->setValidSortColumns($sortColumns);


        if ($jqGridService->sortingIsValid())
        {
            $jqGridService->setRecordCount($deviceSwapReasonMapper->fetAllForDealer($this->_identity->dealerId, null, null, null, true));

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

            $sortOrder = [];

            if ($jqGridService->hasGrouping())
            {
                $sortOrder[] = $jqGridService->getGroupByColumn() . ' ' . $jqGridService->getGroupBySortOrder();
            }

            if ($jqGridService->hasColumns())
            {
                $sortOrder[] = $jqGridService->getSortColumn() . ' ' . $jqGridService->getSortDirection();
            }


            $jqGridService->setRows($deviceSwapReasonMapper->fetAllForDealer($this->_identity->dealerId, $sortOrder, $jqGridService->getRecordsPerPage(), $startRecord));

            // Send back jqGrid JSON data
            $this->sendJson($jqGridService->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson([
                'error' => 'Sorting parameters are invalid'
            ]);
        }

        $json = json_encode($jsonArray);
        $this->sendJson($json);
    }

    public function updateDeviceAction ()
    {
        $postData = [
            "maximumPageCount" => $this->getParam("maximumPageCount"),
            "minimumPageCount" => $this->getParam("minimumPageCount"),
            "masterDeviceId"   => $this->getParam("masterDeviceId"),
        ];

        $form = new DeviceSwapsForm();
        if ($form->isValid($postData))
        {
            $deviceSwap = new DeviceSwapModel();
            $deviceSwap->populate($postData);
            $deviceSwap->dealerId = $this->_identity->dealerId;
            $deviceSwap->saveObject();
            $this->sendJson($deviceSwap->toArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $json = [];
            foreach ($form->getMessages() as $errorElement)
            {
                foreach ($errorElement as $errorName => $elementErrorMessage)
                {
                    $json[] = $elementErrorMessage;
                }
            }
            $deviceSwap = ["error" => $json];
            $this->sendJson($deviceSwap);
        }

    }

    public function updateDeviceReasonAction ()
    {
        $form     = new DeviceSwapReasonsForm();
        $postData = [
            "id"             => $this->getParam('deviceSwapReasonId'),
            "isDefault"      => $this->getParam('isDefault'),
            "reason"         => $this->getParam('reason'),
            "reasonCategory" => $this->getParam('reasonCategory'),
        ];

        if ($form->isValid($postData))
        {
            // Does the reason exist
            $db = Zend_Db_Table::getDefaultAdapter();
            try
            {
                $db->beginTransaction();

                $reasonCategory = DeviceSwapReasonCategoryMapper::getInstance()->find($postData['reasonCategory']);
                if ($reasonCategory instanceof DeviceSwapReasonCategoryModel)
                {
                    // Is this an edit reason ?
                    $reason = DeviceSwapReasonMapper::getInstance()->find($postData['id']);

                    if ($reason instanceof DeviceSwapReasonModel)
                    {
                        $reason->deviceSwapReasonCategoryId = $reasonCategory->id;
                        $reason->reason                     = $postData['reason'];
                        DeviceSwapReasonMapper::getInstance()->save($reason);
                    }
                    else
                    {
                        $reason                             = new DeviceSwapReasonModel();
                        $reason->dealerId                   = $this->_identity->dealerId;
                        $reason->deviceSwapReasonCategoryId = $reasonCategory->id;
                        $reason->reason                     = $postData['reason'];
                        DeviceSwapReasonMapper::getInstance()->insert($reason);
                    }

                    $reasonDefault = DeviceSwapReasonDefaultMapper::getInstance()->find([$reasonCategory->id, $this->_identity->dealerId]);

                    if ($reasonDefault->deviceSwapReasonId === $reason->id)
                    {
                        // This reason is default
                        if (!$postData["isDefault"])
                        {
                            $this->sendJsonError("You cannot unset the default reason. Instead please set a different reason to be the new default.");
                        }
                    }
                    else
                    {
                        // Do we want to set this reason as default?
                        if ($postData['isDefault'])
                        {
                            if ($reasonDefault instanceof DeviceSwapReasonDefaultModel)
                            {
                                $reasonDefault->deviceSwapReasonId = $reason->id;
                                DeviceSwapReasonDefaultMapper::getInstance()->save($reasonDefault);
                            }
                            else
                            {
                                $reasonDefault                             = new DeviceSwapReasonDefaultModel();
                                $reasonDefault->deviceSwapReasonCategoryId = $reasonCategory->id;
                                $reasonDefault->dealerId                   = $this->_identity->dealerId;
                                $reasonDefault->deviceSwapReasonId         = $reason->id;
                                DeviceSwapReasonDefaultMapper::getInstance()->insert($reasonDefault);
                            }
                        }
                    }
                }
                else
                {
                    $this->sendJsonError("Cannot find reason category, try again.");
                }

                $db->commit();

                $this->sendJson(['success' => true, 'message' => 'Device swap reason saved.']);
            }
            catch (Exception $e)
            {
                $db->rollBack();
                $this->sendJsonError("Error updating database, please try again.");
            }
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson([
                'message'       => 'Validation error',
                'errorMessages' => $form->getMessages(),
            ]);
        }
    }

    public function deleteDeviceAction ()
    {
        $masterDeviceId = $this->_getParam("deviceInstanceId");
        $rowsDeleted    = DeviceSwapMapper::getInstance()->delete([$masterDeviceId, $this->_identity->dealerId]);

        if ($rowsDeleted > 0)
        {
            $json = ["rowsDeleted" => $rowsDeleted];
        }
        else
        {
            $json = ["error" => "Error deleting device swap."];
        }

        $this->sendJson($json);
    }

    /**
     * This action will delete a device swap reason, if the device swap reason is default and cannot
     * re-assign a new default, it will return an error.  If the device swap reason is not default
     * it will just delete the device swap.
     */
    public function deleteReasonAction ()
    {
        $reasonId = $this->_getParam('reasonId');

        $swapMapper        = DeviceSwapReasonMapper::getInstance();
        $swapDefaultMapper = DeviceSwapReasonDefaultMapper::getInstance();
        $reason            = $swapMapper->find($reasonId);

        // Delete defaults if they exist
        if ($swapDefaultMapper->findDefaultByReasonId($reasonId) instanceof DeviceSwapReasonDefaultModel)
        {
            $reasonsByCategory = $swapMapper->fetchAllByCategoryId($reason->deviceSwapReasonCategoryId, $reason->dealerId);
            // is this the last device in this category?
            if (count($reasonsByCategory) > 1)
            {
                // Delete the reason and assign it a new default
                $rowsAffected = $swapDefaultMapper->delete([$reason->deviceSwapReasonCategoryId, $this->_identity->dealerId]);
                if ($rowsAffected > 0)
                {
                    $swapMapper->delete($reason->id);
                    // Re query the database for left over reasons after we deleted and select the first item in the array
                    $newReason = $swapMapper->fetchAllByCategoryId($reason->deviceSwapReasonCategoryId, $reason->dealerId)[0];
                    // Create a new default reason for the reason we just found
                    $newDefaultReason                             = new DeviceSwapReasonDefaultModel();
                    $newDefaultReason->dealerId                   = $newReason->dealerId;
                    $newDefaultReason->deviceSwapReasonCategoryId = $newReason->deviceSwapReasonCategoryId;
                    $newDefaultReason->deviceSwapReasonId         = $newReason->id;
                    // Insert as the new default
                    $swapDefaultMapper->insert($newDefaultReason);
                }
                else
                {
                    $this->sendJsonError("Error deleting default swap reason. Please try again.");
                }
            }
            else
            {
                // Send error cannot delete last item in category
                $this->sendJsonError("Cannot delete last default item in the {$reason->getCategory()->name} group.");
            }
        }
        else
        {
            DeviceSwapReasonMapper::getInstance()->delete($reasonId);
        }

        $this->sendJson(["Successfully delete device swap reason."]);
    }
}