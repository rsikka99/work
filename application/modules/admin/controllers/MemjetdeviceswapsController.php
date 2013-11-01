<?php
/**
 * Class Admin_MemjetdeviceswapsController
 */
class Admin_MemjetdeviceswapsController extends Tangent_Controller_Action
{
    /**
     * Displays the Memjet Device Swaps jQGrid
     */
    public function indexAction ()
    {
        $form                         = new Admin_Form_MemjetDeviceSwaps();
        $this->view->memjetDeviceSwap = new Admin_ViewModel_MemjetDeviceSwap();
        $this->view->form             = $form;
    }

    /**
     * Fetchs all the memjet device swaps
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

            // Send back jqGrid json data
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
     * Updates a memjet device swap
     */
    public function updateDeviceAction ()
    {
        $postData = array(
            "maximumPageCount" => $this->getParam("maximumPageCount"),
            "minimumPageCount" => $this->getParam("minimumPageCount"),
            "masterDeviceId"   => $this->getParam("masterDeviceId"),
        );

        $form = new Admin_Form_MemjetDeviceSwaps();
        if ($form->isValid($postData))
        {
            $memjetDeviceSwap = new Admin_Model_Memjet_Device_Swap();
            $memjetDeviceSwap->populate($postData);
            $memjetDeviceSwap->saveObject();
            $this->sendJson($memjetDeviceSwap->toArray());
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
            $memjetDeviceSwap = array("error" => $json);
            $this->sendJson($memjetDeviceSwap);
        }

    }

    /**
     * deletes a memjet device swap
     */
    public function deleteDeviceAction ()
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
}