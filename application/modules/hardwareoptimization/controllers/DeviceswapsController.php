<?php

/**
 * Class Hardwareoptimization_DeviceswapsController
 */
class Hardwareoptimization_DeviceswapsController extends Tangent_Controller_Action
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
        $this->view->headTitle('Device Swaps');
        $form           = new Hardwareoptimization_Form_DeviceSwaps();
        $deviceSwapForm = new Hardwareoptimization_Form_DeviceSwapReasons();

        $this->view->deviceSwap     = new Hardwareoptimization_ViewModel_DeviceSwap();
        $this->view->reasons        = Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->fetchAllReasonByDealerId($this->_identity->dealerId);
        $this->view->navigationForm = new Hardwareoptimization_Form_Hardware_Optimization_Navigation();
        $this->view->form           = $form;
        $this->view->deviceSwapForm = $deviceSwapForm;
    }

    public function deviceSwapListAction ()
    {
        $jsonArray        = array();
        $jqGridService    = new Tangent_Service_JQGrid();
        $deviceSwapMapper = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance();

        /*
         * Grab the incoming parameters
         */
        $jqGridServiceParameters = array(
            'sidx' => $this->_getParam('sidx', 'deviceType'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10)
        );

        // Set up validation arrays
        $blankModel    = new Hardwareoptimization_Model_Device_Swap();
        $sortColumns   = array_keys($blankModel->toArray());
        $sortColumns[] = "device_name";

        $jqGridService->parseJQGridPagingRequest($jqGridServiceParameters);
        $jqGridService->setValidSortColumns($sortColumns);

        $groupColumns   = array();
        $groupColumns[] = "deviceType";
        $jqGridService->setValidGroupByColumns($groupColumns);

        $deviceSwapViewModel = new Hardwareoptimization_ViewModel_DeviceSwap();
        $costPerPageSetting  = $deviceSwapViewModel->getCostPerPageSetting();

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

            $sortOrder = array();

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
            $this->sendJson(array(
                'error' => 'Sorting parameters are invalid'
            ));
        }

        $json = json_encode($jsonArray);
        $this->sendJson($json);
    }

    public function deviceReasonListAction ()
    {
        $jsonArray              = array();
        $jqGridService          = new Tangent_Service_JQGrid();
        $deviceSwapReasonMapper = Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance();

        /*
         * Grab the incoming parameters
         */
        $jqGridServiceParameters = array(
            'sidx' => $this->_getParam('sidx', 'reason'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10)
        );

        // Set up validation arrays
        $blankModel  = new Hardwareoptimization_Model_Device_Swap_Reason();
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

            $sortOrder = array();

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
            $this->sendJson(array(
                'error' => 'Sorting parameters are invalid'
            ));
        }

        $json = json_encode($jsonArray);
        $this->sendJson($json);
    }

    public function updateDeviceAction ()
    {
        $postData = array(
            "maximumPageCount" => $this->getParam("maximumPageCount"),
            "minimumPageCount" => $this->getParam("minimumPageCount"),
            "masterDeviceId"   => $this->getParam("masterDeviceId"),
        );

        $form = new Hardwareoptimization_Form_DeviceSwaps();
        if ($form->isValid($postData))
        {
            $deviceSwap = new Hardwareoptimization_Model_Device_Swap();
            $deviceSwap->populate($postData);
            $deviceSwap->dealerId = $this->_identity->dealerId;
            $deviceSwap->saveObject();
            $this->sendJson($deviceSwap->toArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $json = array();
            foreach ($form->getMessages() as $errorElement)
            {
                foreach ($errorElement as $errorName => $elementErrorMessage)
                {
                    $json[] = $elementErrorMessage;
                }
            }
            $deviceSwap = array("error" => $json);
            $this->sendJson($deviceSwap);
        }

    }

    public function updateDeviceReasonAction ()
    {
        $postData = array(
            "reasonCategory" => $this->getParam('reasonCategory'),
            "reason"         => $this->getParam('reason'),
            "id"             => $this->getParam('deviceSwapReasonId'),
            "isDefault"      => $this->getParam('isDefault'),
        );

        // Does the reason exist
        $db = Zend_Db_Table::getDefaultAdapter();
        try
        {
            $db->beginTransaction();

            $reasonCategory = Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Category::getInstance()->find($postData['reasonCategory']);
            if ($reasonCategory instanceof Hardwareoptimization_Model_Device_Swap_Reason_Category)
            {
                // Is this an edit reason ?
                $reason = Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->find($postData['id']);

                if ($reason instanceof Hardwareoptimization_Model_Device_Swap_Reason)
                {
                    $reason->deviceSwapReasonCategoryId = $reasonCategory->id;
                    $reason->reason                     = $postData['reason'];
                    Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->save($reason);
                }
                else
                {
                    $reason                             = new Hardwareoptimization_Model_Device_Swap_Reason();
                    $reason->dealerId                   = $this->_identity->dealerId;
                    $reason->deviceSwapReasonCategoryId = $reasonCategory->id;
                    $reason->reason                     = $postData['reason'];
                    Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->insert($reason);
                }

                $reasonDefault = Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->find(array($reasonCategory->id, $this->_identity->dealerId));

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
                        if ($reasonDefault instanceof Hardwareoptimization_Model_Device_Swap_Reason_Default)
                        {
                            $reasonDefault->deviceSwapReasonId = $reason->id;
                            Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->save($reasonDefault);
                        }
                        else
                        {
                            $reasonDefault                             = new Hardwareoptimization_Model_Device_Swap_Reason_Default();
                            $reasonDefault->deviceSwapReasonCategoryId = $reasonCategory->id;
                            $reasonDefault->dealerId                   = $this->_identity->dealerId;
                            $reasonDefault->deviceSwapReasonId         = $reason->id;
                            Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->insert($reasonDefault);
                        }
                    }
                }
            }
            else
            {
                $this->sendJsonError("Cannot find reason category, try again.");
            }

            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
            $this->sendJsonError("Error updating database, please try again.");
        }

        $this->sendJson(array("success" => "true", "message" => "Device swap reason saved."));
    }

    public function deleteDeviceAction ()
    {
        $masterDeviceId = $this->_getParam("deviceInstanceId");
        $rowsDeleted    = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->delete(array($masterDeviceId, $this->_identity->dealerId));

        if ($rowsDeleted > 0)
        {
            $json = array("rowsDeleted" => $rowsDeleted);
        }
        else
        {
            $json = array("error" => "Error deleting device swap.");
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

        $swapMapper        = Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance();
        $swapDefaultMapper = Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance();
        $reason            = $swapMapper->find($reasonId);

        // Delete defaults if they exist
        if ($swapDefaultMapper->findDefaultByReasonId($reasonId) instanceof Hardwareoptimization_Model_Device_Swap_Reason_Default)
        {
            $reasonsByCategory = $swapMapper->fetchAllByCategoryId($reason->deviceSwapReasonCategoryId, $reason->dealerId);
            // is this the last device in this category?
            if (count($reasonsByCategory) > 1)
            {
                // Delete the reason and assign it a new default
                $rowsAffected = $swapDefaultMapper->delete(array($reason->deviceSwapReasonCategoryId, $this->_identity->dealerId));
                if ($rowsAffected > 0)
                {
                    $swapMapper->delete($reason->id);
                    // Re query the database for left over reasons after we deleted and select the first item in the array
                    $newReason = $swapMapper->fetchAllByCategoryId($reason->deviceSwapReasonCategoryId, $reason->dealerId)[0];
                    // Create a new default reason for the reason we just found
                    $newDefaultReason                             = new Hardwareoptimization_Model_Device_Swap_Reason_Default();
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
            Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->delete($reasonId);
        }

        $this->sendJson(array("Successfully delete device swap reason."));
    }
}