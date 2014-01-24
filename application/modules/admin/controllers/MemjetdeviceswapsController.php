<?php

/**
 * Class Admin_MemjetdeviceswapsController
 */
class Admin_MemjetdeviceswapsController extends Tangent_Controller_Action
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

    /**
     * Displays the Memjet Device Swaps jQGrid
     */
    public function indexAction ()
    {
        $this->view->headTitle('Memjet Device Swaps');
        $this->view->memjetDeviceSwap = new Admin_ViewModel_MemjetDeviceSwap();
        $this->view->reasons          = Memjetoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->fetchAllReasonByDealerId($this->_identity->dealerId);
        $this->view->form             = new Admin_Form_MemjetDeviceSwaps();
        $this->view->deviceSwapForm   = new Memjetoptimization_Form_DeviceSwapReasons();
    }

    /**
     *  Fetches all the Memjet device swaps
     */
    public function deviceSwapListAction ()
    {
        $jsonArray              = array();
        $jqGridService          = new Tangent_Service_JQGrid();
        $memjetDeviceSwapMapper = Admin_Model_Mapper_Memjet_Device_Swap::getInstance();

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
        $blankModel    = new Admin_Model_Memjet_Device_Swap();
        $sortColumns   = array_keys($blankModel->toArray());
        $sortColumns[] = "device_name";

        $jqGridService->parseJQGridPagingRequest($jqGridServiceParameters);
        $jqGridService->setValidSortColumns($sortColumns);

        $groupColumns   = array();
        $groupColumns[] = "deviceType";
        $jqGridService->setValidGroupByColumns($groupColumns);

        $deviceSwapViewModel = new Admin_ViewModel_MemjetDeviceSwap();
        $costPerPageSetting  = $deviceSwapViewModel->getCostPerPageSetting();

        if ($jqGridService->sortingIsValid())
        {
            $jqGridService->setRecordCount($memjetDeviceSwapMapper->fetchAllDeviceSwaps(null, null, null, null, true));

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

            $jqGridService->setRows($memjetDeviceSwapMapper->fetchAllDeviceSwaps($costPerPageSetting, $sortOrder, $jqGridService->getSortDirection()), $jqGridService->getRecordsPerPage(), $startRecord);

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
        $deviceSwapReasonMapper = Memjetoptimization_Model_Mapper_Device_Swap_Reason::getInstance();

        /*
         * Grab the incoming parameters
         */
        $jqGridServiceParameters = array(
            'sidx' => $this->_getParam('sidx', 'reason'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 30)
        );

        // Set up validation arrays
        $blankModel  = new Memjetoptimization_Model_Device_Swap_Reason();
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


            $jqGridService->setRows($deviceSwapReasonMapper->fetAllForDealer($this->_identity->dealerId, $sortOrder, null, $startRecord));

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

    /**
     * Updates a Memjet device swap
     */
    public function updateDeviceAction ()
    {
        $this->view->headTitle('Update Memjet Device Swap');
        $postData = array(
            "maximumPageCount"       => $this->getParam("maximumPageCount"),
            "minimumPageCount"       => $this->getParam("minimumPageCount"),
            "dealerMinimumPageCount" => $this->getParam("dealerMinimumPageCount"),
            "dealerMaximumPageCount" => $this->getParam("dealerMaximumPageCount"),
            "masterDeviceId"         => $this->getParam("masterDeviceId"),
        );

        $form = new Admin_Form_MemjetDeviceSwaps();
        if ($form->isValid($postData))
        {
            $memjetDeviceSwap = new Admin_Model_Memjet_Device_Swap();
            $memjetDeviceSwap->populate($postData);

            // If we are an admin save directly
            if ($this->view->isAllowed(Admin_Model_Acl::RESOURCE_ADMIN_MEMJETDEVICESWAPS_WILDCARD, Application_Model_Acl::PRIVILEGE_ADMIN))
            {
                $memjetDeviceSwap->saveObject();
            }

            if (My_Feature::canAccess(My_Feature::MEMJET_OPTIMIZATION))
            {
                $dealerId                            = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $memjetDeviceSwapPageThresholdMapper = Admin_Model_Mapper_Memjet_Device_Swap_Page_Threshold::getInstance();
                $memjetDeviceSwapPageThreshold       = $memjetDeviceSwapPageThresholdMapper->find(array($postData['masterDeviceId'], $dealerId));
                if (!$memjetDeviceSwapPageThreshold instanceof Admin_Model_Memjet_Device_Swap_Page_Threshold)
                {
                    $memjetDeviceSwapPageThreshold                   = new Admin_Model_Memjet_Device_Swap_Page_Threshold();
                    $memjetDeviceSwapPageThreshold->dealerId         = Zend_Auth::getInstance()->getIdentity()->dealerId;
                    $memjetDeviceSwapPageThreshold->masterDeviceId   = $postData['masterDeviceId'];
                    $memjetDeviceSwapPageThreshold->minimumPageCount = $postData['dealerMinimumPageCount'];
                    $memjetDeviceSwapPageThreshold->maximumPageCount = $postData['dealerMaximumPageCount'];
                    $memjetDeviceSwapPageThresholdMapper->insert($memjetDeviceSwapPageThreshold);
                }
                else
                {
                    $memjetDeviceSwapPageThreshold->minimumPageCount = $postData['dealerMinimumPageCount'];
                    $memjetDeviceSwapPageThreshold->maximumPageCount = $postData['dealerMaximumPageCount'];
                    $memjetDeviceSwapPageThresholdMapper->save($memjetDeviceSwapPageThreshold);
                }
            }

            $this->sendJson($memjetDeviceSwap->toArray());
        }

        $this->_response->setHttpResponseCode(500);
        $json = array();
        foreach ($form->getMessages() as $errorElement)
        {
            foreach ($errorElement as $errorName => $elementErrorMessage)
            {
                $json[] = $elementErrorMessage;
            }
        }
        $memjetDeviceSwap = array("error" => $json);
        $this->sendJson($memjetDeviceSwap);
    }

    public function updateDeviceReasonAction ()
    {
        $this->view->headTitle('Update Memjet Swap Reason');
        $postData = array(
            "reasonCategory" => $this->getParam('reasonCategory'),
            "reason"         => $this->getParam('reason'),
            "id"             => $this->getParam('deviceSwapReasonId'),
            "isDefault"      => $this->getParam('isDefault'),
        );

        // Does the reason exist
        $reasonCategory = Memjetoptimization_Model_Mapper_Device_Swap_Reason_Category::getInstance()->find($postData['reasonCategory']);
        try
        {

            if ($reasonCategory instanceof Memjetoptimization_Model_Device_Swap_Reason_Category)
            {
                // Is this an edit reason ?
                $reason = Memjetoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->find($postData['id']);

                if ($reason instanceof Memjetoptimization_Model_Device_Swap_Reason)
                {
                    $reason->deviceSwapReasonCategoryId = $reasonCategory->id;
                    $reason->reason                     = $postData['reason'];
                    Memjetoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->save($reason);
                }
                else
                {
                    $reason                             = new Memjetoptimization_Model_Device_Swap_Reason();
                    $reason->dealerId                   = $this->_identity->dealerId;
                    $reason->deviceSwapReasonCategoryId = $reasonCategory->id;
                    $reason->reason                     = $postData['reason'];
                    Memjetoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->insert($reason);
                }

                if ($postData['isDefault'])
                {
                    $reasonCategoryDefault                             = new Memjetoptimization_Model_Device_Swap_Reason_Default();
                    $reasonCategoryDefault->deviceSwapReasonCategoryId = $reasonCategory->id;
                    $reasonCategoryDefault->dealerId                   = $this->_identity->dealerId;
                    $reasonCategoryDefault->deviceSwapReasonId         = $reason->id;

                    $reasonDefault = Memjetoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->find(array($reasonCategory->id, $this->_identity->dealerId));
                    if ($reasonDefault instanceof Memjetoptimization_Model_Device_Swap_Reason_Default)
                    {
                        Memjetoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->save($reasonCategoryDefault);
                    }
                    else
                    {
                        Memjetoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->insert($reasonCategoryDefault);
                    }
                }
            }
            else
            {
                $this->sendJsonError("Cannot find reason category, try again.");
            }
        }
        catch (Exception $e)
        {
            $this->sendJsonError("Error updating database, please try again.");
        }

        $this->sendJson(array());
    }

    /**
     * This action will delete a device swap reason, if the device swap reason is default and cannot
     * re-assign a new default, it will return an error.  If the device swap reason is not default
     * it will just delete the device swap.
     */
    public function deleteReasonAction ()
    {
        $this->view->headTitle('Delete Memjet Swap Reason');
        $reasonId = $this->_getParam('reasonId');

        $swapMapper        = Memjetoptimization_Model_Mapper_Device_Swap_Reason::getInstance();
        $swapDefaultMapper = Memjetoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance();
        $reason            = $swapMapper->find($reasonId);

        // Delete defaults if they exist
        if ($swapDefaultMapper->findDefaultByReasonId($reasonId) instanceof Memjetoptimization_Model_Device_Swap_Reason_Default)
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
                    $newDefaultReason                             = new Memjetoptimization_Model_Device_Swap_Reason_Default();
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
            Memjetoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->delete($reasonId);
        }

        $this->sendJson(array("Successfully delete device swap reason."));
    }

    /**
     *  Deletes a Memjet device swap
     */
    public function deleteDeviceAction ()
    {
        if ($this->view->isAllowed(Admin_Model_Acl::RESOURCE_ADMIN_MEMJETDEVICESWAPS_WILDCARD, Application_Model_Acl::PRIVILEGE_ADMIN))
        {
            $masterDeviceId = $this->_getParam("deviceInstanceId");
            $rowsDeleted    = Admin_Model_Mapper_Memjet_Device_Swap::getInstance()->delete($masterDeviceId);

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
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(array(
                'error' => 'You do not have permission to delete a device'
            ));
        }
    }
}