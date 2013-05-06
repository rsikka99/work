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
        $form = new Hardwareoptimization_Form_DeviceSwaps();

        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();
            if (!isset($value['goBack']))
            {
                if ($form->isValid($postData))
                {
                    $formData = $form->getValues();

                    // save the device swap to the database
                    $formData['dealerId'] = $this->_identity->dealerId;
                    $deviceSwap           = new Hardwareoptimization_Model_Device_Swap();
                    if ($deviceSwap->saveObject($formData))
                    {
                        $this->_flashMessenger->addMessage(array("success" => "Device swap successfully added."));
                        // If save and continue re-direct to settings page
                        if (isset($formData['saveAndContinue']))
                        {
                            $this->redirector("index", "admin", "default");
                        }
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("danger" => "Adding device swap failed. Please try again."));
                    }
                }

            }
        }

        $this->view->deviceSwap     = new Hardwareoptimization_ViewModel_DeviceSwap();
        $this->view->navigationForm = new Hardwareoptimization_Form_Hardware_Optimization_Navigation();
        $this->view->form           = $form;
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
            'sidx' => $this->_getParam('sidx', 'fullname'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10)
        );

        // Set up validation arrays
        $blankModel    = new Hardwareoptimization_Model_Device_Swap();
        $sortColumns   = array_keys($blankModel->toArray());
        $sortColumns[] = "deviceType  , ";
        $sortColumns[] = "fullname";

        $jqGridService->parseJQGridPagingRequest($jqGridServiceParameters);
        $jqGridService->setValidSortColumns($sortColumns);

        $deviceSwapViewModel = new Hardwareoptimization_ViewModel_DeviceSwap();
        $costPerPageSetting  = $deviceSwapViewModel->getCostPerPageSetting();

        if ($jqGridService->sortingIsValid())
        {
            $jqGridService->setRecordCount($deviceSwapMapper->fetAllForDealer($this->_identity->dealerId, null, null, null, null, null, true));

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

            $jqGridService->setRows($deviceSwapMapper->fetAllForDealer($this->_identity->dealerId, $costPerPageSetting, $jqGridService->getSortColumn() . " " . $jqGridService->getSortDirection(), $jqGridService->getRecordsPerPage(), $startRecord));

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
}