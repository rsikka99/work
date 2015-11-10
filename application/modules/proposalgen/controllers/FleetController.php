<?php
use MPSToolbox\Legacy\Entities\RmsUploadEntity;
use MPSToolbox\Legacy\Modules\Assessment\Forms\AssessmentNavigationForm;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MapDeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadRowMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsExcludedRowMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceMasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\FleetStepsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MapDeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsExcludedRowModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUploadService;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\AddDeviceForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use Tangent\Controller\Action;
use Tangent\Service\JQGrid;

/**
 * Class Proposalgen_FleetController
 */
class Proposalgen_FleetController extends Action
{

    /**
     * @var RmsUploadService
     */
    protected $uploadService;

    /**
     * @return RmsUploadService
     */
    public function getRmsUploadService()
    {
        return $this->uploadService;
    }

    /**
     * @param RmsUploadService $rmsUploadService
     */
    public function setRmsUploadService($rmsUploadService)
    {
        $this->uploadService = $rmsUploadService;
    }



    /**
     * @var FleetStepsModel
     */
    protected $_navigation;

    public function init ()
    {
        $this->_navigation = FleetStepsModel::getInstance();

        if (!$this->getSelectedClient() instanceof \MPSToolbox\Legacy\Entities\ClientEntity)
        {
            $this->_flashMessenger->addMessage([
                "danger" => "A client is not selected."
            ]);

            $this->redirectToRoute('app.dashboard');
        }

        $this->_navigation->clientName = $this->getSelectedClient()->companyName;
    }

    /**
     * Users can upload/see uploaded data on this step
     * $r->addRoute('rms-upload',                            new R('rms-uploads/:rmsUploadId',                            ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'index',                'rmsUploadId' => false]));
     */
    public function indexAction ()
    {
        $this->_pageTitle = ['Upload', 'RMS Upload'];
        $time             = -microtime(true);
        $rmsUploadId      = $this->_getParam('rmsUploadId', false);


        $this->_navigation->setActiveStep(FleetStepsModel::STEP_FLEET_UPLOAD);
        $rmsUpload = null;
        if ($rmsUploadId > 0)
        {
            $rmsUpload = RmsUploadMapper::getInstance()->find($rmsUploadId);
        }

        if ($rmsUpload instanceof RmsUploadModel)
        {
            $this->_navigation->updateAccessibleSteps(FleetStepsModel::STEP_FLEET_SUMMARY);
        }
        else
        {
            $this->_navigation->updateAccessibleSteps(FleetStepsModel::STEP_FLEET_UPLOAD);
        }

        if (!$this->uploadService) {
            $this->uploadService = new RmsUploadService($this->getIdentity()->id, $this->getIdentity()->dealerId, $this->getSelectedClient()->id, $rmsUpload);
        }

        $form = $this->uploadService->getForm();

        if (isset($this->getMpsSession()->lastSelectedRmsProviderId))
        {
            $form->getElement('rmsProviderId')->setValue($this->getMpsSession()->lastSelectedRmsProviderId);
        }

        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getParams();
            if (isset($values ["goBack"]))
            {
                // Bring the user back to the home page
                $this->redirectToRoute('app.dashboard');
            }
            else if (isset($values ["performUpload"]) && !($rmsUpload instanceof RmsUploadModel))
            {
                $this->getMpsSession()->lastSelectedRmsProviderId = $values['rmsProviderId'];

                /*
                 * Handle Upload
                 */
                if ($form->isValid($values))
                {
                    $success = $this->uploadService->processUpload($values, $this->getIdentity()->dealerId);

                    /**
                     * Log how much time it took
                     */
                    $time += microtime(true);
                    $filename = $this->uploadService->getFileName();

                    \Tangent\Logger\Logger::debug("It took {$time} seconds to process the CSV upload ({$filename}). ");

                    if ($success)
                    {
                        $timeElapsed    = number_format($time, 4);
                        $validDevices   = number_format($this->uploadService->rmsUpload->validRowCount);
                        $invalidDevices = number_format($this->uploadService->rmsUpload->invalidRowCount);
                        $this->_flashMessenger->addMessage(["success" => "Processed {$validDevices} valid devices and {$invalidDevices} invalid devices in {$timeElapsed} seconds."]);

                        $this->getMpsSession()->selectedRmsUploadId = $this->uploadService->rmsUpload->id;
                        $this->redirectToRoute(null, ["rmsUploadId" => $this->uploadService->rmsUpload->id]);
                    }
                    else
                    {
                        $this->view->invalidDevices = $this->uploadService->invalidRows;
                        $this->_flashMessenger->addMessage(["danger" => $this->uploadService->errorMessages]);
                    }

                }
                else
                {
                    $this->_flashMessenger->addMessage(["danger" => 'Please fix the errors below.']);
                }
            }
            else if (isset($values ["saveAndContinue"]))
            {
                $this->redirectToRoute('rms-upload.mapping', ["rmsUploadId" => $this->uploadService->rmsUpload->id]);
            }
        }

        $this->view->form = $form;

        $this->view->rmsUpload = $this->uploadService->rmsUpload;

        $navigationButtons          = ($this->uploadService->rmsUpload instanceof RmsUploadModel) ? AssessmentNavigationForm::BUTTONS_BACK_NEXT : AssessmentNavigationForm::BUTTONS_BACK;
        $this->view->navigationForm = new AssessmentNavigationForm($navigationButtons);

    }

    /**
     * @deprecated
     */
    public function rmsUploadListAction ()
    {
        throw new Exception('Deprecated');
    }

    /**
     * This handles the mapping of devices to our master devices
     * $r->addRoute('rms-upload.mapping',                    new R('rms-uploads/mapping/:rmsUploadId',                    ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'mapping',              'rmsUploadId' => false]));
     */
    public function mappingAction ()
    {
        $this->_pageTitle = ['Map Devices', 'RMS Upload',];
        $this->_navigation->setActiveStep(FleetStepsModel::STEP_FLEET_MAPPING);
        $this->_navigation->updateAccessibleSteps(FleetStepsModel::STEP_FLEET_SUMMARY);

        $rmsUploadId = intval($this->_getParam('rmsUploadId', false));

        $rmsUpload = null;
        if ($rmsUploadId) {
            $rmsUpload = RmsUploadMapper::getInstance()->find($rmsUploadId);
            if ($rmsUpload) {
                $client = ClientMapper::getInstance()->find($rmsUpload->clientId);
                if ($client->dealerId != $this->getIdentity()->dealerId) $rmsUpload = null;
            }
        }

        if (!$rmsUpload instanceof RmsUploadModel) {
            $this->redirectToRoute('rms-upload');
        }

        $this->view->rmsUpload = $rmsUpload;

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['saveAndContinue']))
            {
                $this->redirectToRoute('rms-upload.summary', ["rmsUploadId" => $rmsUploadId]);
            }
            else if (isset($postData['goBack']))
            {
                // Call the base controller to send us to the next logical step in the proposal.
                $this->redirectToRoute('rms-upload', ["rmsUploadId" => $rmsUploadId]);
            }
        }

        $this->view->navigationForm = new AssessmentNavigationForm(AssessmentNavigationForm::BUTTONS_BACK_NEXT);
    }

    /**
     * Generates a list of devices that were not mapped automatically
     * $r->addRoute('rms-upload.mapping.list',               new R('rms-uploads/mapping/list',                            ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'device-mapping-list'                         ]));
     */
    public function deviceMappingListAction ()
    {
        $rmsUploadId = intval($this->_getParam('rmsUploadId', false));

        $rmsUpload = null;
        if ($rmsUploadId) {
            $rmsUpload = RmsUploadMapper::getInstance()->find($rmsUploadId);
            if ($rmsUpload) {
                $client = ClientMapper::getInstance()->find($rmsUpload->clientId);
                if ($client->dealerId != $this->getIdentity()->dealerId) $rmsUpload = null;
            }
        }
        if (empty($rmsUpload)) {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson([
                'error' => 'RmsUpload not found'
            ]);
            return;
        }

        $jqGrid                  = new JQGrid();
        $mapDeviceInstanceMapper = MapDeviceInstanceMapper::getInstance();

        /*
         * Grab the incoming parameters
         */
        $jqGridParameters = [
            'sidx' => $this->_getParam('sidx', 'deviceCount'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10)
        ];

        // Set up validation arrays
        $blankModel  = new MapDeviceInstanceModel();
        $sortColumns = array_keys($blankModel->toArray());

        $jqGrid->parseJQGridPagingRequest($jqGridParameters);
        $jqGrid->setValidSortColumns($sortColumns);


        if ($jqGrid->sortingIsValid())
        {
            $jqGrid->setRecordCount($mapDeviceInstanceMapper->fetchAllForRmsUpload($rmsUploadId, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), null, null, true));

            // Validate current page number since we don't want to be out of bounds
            if ($jqGrid->getCurrentPage() < 1)
            {
                $jqGrid->setCurrentPage(1);
            }
            else if ($jqGrid->getCurrentPage() > $jqGrid->calculateTotalPages())
            {
                $jqGrid->setCurrentPage($jqGrid->calculateTotalPages());
            }

            // Return a small subset of the results based on the jqGrid parameters
            $startRecord = $jqGrid->getRecordsPerPage() * ($jqGrid->getCurrentPage() - 1);
            $jqGrid->setRows($mapDeviceInstanceMapper->fetchAllForRmsUpload($rmsUploadId, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $jqGrid->getRecordsPerPage(), $startRecord));

            // Send back jqGrid JSON data
            $this->sendJson($jqGrid->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson([
                'error' => 'Sorting parameters are invalid'
            ]);
        }
    }

    /**
     * Generates a list of devices that were not mapped automatically
     *$r->addRoute('rms-upload.excluded-list',              new R('rms-uploads/excluded-list',                           ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'excluded-list',                              ]));
     */
    public function excludedListAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);

        if ($rmsUploadId > 0)
        {
            $jqGrid            = new JQGrid();
            $excludedRowMapper = RmsExcludedRowMapper::getInstance();

            /*
             * Grab the incoming parameters
             */
            $jqGridParameters = [
                'sidx' => $this->_getParam('sidx', 'csvLineNumber'),
                'sord' => $this->_getParam('sord', 'desc'),
                'page' => $this->_getParam('page', 1),
                'rows' => $this->_getParam('rows', 10)
            ];

            // Set up validation arrays
            $blankModel  = new RmsExcludedRowModel();
            $sortColumns = array_keys($blankModel->toArray());

            $jqGrid->parseJQGridPagingRequest($jqGridParameters);
            $jqGrid->setValidSortColumns($sortColumns);


            if ($jqGrid->sortingIsValid())
            {
                $jqGrid->setRecordCount($excludedRowMapper->fetchAllForRmsUpload($rmsUploadId, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), null, null, true));

                // Validate current page number since we don't want to be out of bounds
                if ($jqGrid->getCurrentPage() < 1)
                {
                    $jqGrid->setCurrentPage(1);
                }
                else if ($jqGrid->getCurrentPage() > $jqGrid->calculateTotalPages())
                {
                    $jqGrid->setCurrentPage($jqGrid->calculateTotalPages());
                }

                // Return a small subset of the results based on the jqGrid parameters
                $startRecord = $jqGrid->getRecordsPerPage() * ($jqGrid->getCurrentPage() - 1);

                if ($startRecord < 0)
                {
                    $startRecord = 0;
                }


                $jqGrid->setRows($excludedRowMapper->fetchAllForRmsUpload($rmsUploadId, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $jqGrid->getRecordsPerPage(), $startRecord));

                // Send back jqGrid JSON data
                $this->sendJson($jqGrid->createPagerResponseArray());
            }
            else
            {
                $this->_response->setHttpResponseCode(500);
                $this->sendJson(['error' => 'Sorting parameters are invalid']);
            }
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(['error' => 'Invalid RMS Upload Id']);
        }
    }

    /**
     * Handles mapping a device
     * $r->addRoute('rms-upload.mapping.set-mapped-to',      new R('rms-uploads/mapping/set-mapped-to',                   ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'set-mapped-to'                               ]));
     *
     * @throws Exception
     */
    public function setMappedToAction ()
    {
        $db                               = Zend_Db_Table_Abstract::getDefaultAdapter();
        $targetDeviceInstanceId           = $this->_getParam('deviceInstanceId', false);
        $masterDeviceId                   = $this->_getParam('masterDeviceId', false);
        $errorMessage                     = null;
        $deviceInstanceMasterDeviceMapper = DeviceInstanceMasterDeviceMapper::getInstance();
        $deviceInstanceMapper             = DeviceInstanceMapper::getInstance();
        $successMessage                   = "Device mapped successfully";

        if ($targetDeviceInstanceId !== false && $masterDeviceId !== false)
        {
            /** @var MasterDeviceModel $masterDevice */
            $masterDevice = MasterDeviceMapper::getInstance()->find($masterDeviceId);
            if ($masterDevice instanceof MasterDeviceModel || $masterDeviceId == 0)
            {
                $targetDeviceInstance = $deviceInstanceMapper->find($targetDeviceInstanceId);
                if ($targetDeviceInstance instanceof DeviceInstanceModel)
                {
                    $deviceInstances = [];
                    if (strlen($targetDeviceInstance->getRmsUploadRow()->rmsModelId) > 0)
                    {
                        $deviceInstances = $deviceInstanceMapper->fetchAllWithRmsModelId($targetDeviceInstance->rmsUploadId, $targetDeviceInstance->getRmsUploadRow()->rmsProviderId, $targetDeviceInstance->getRmsUploadRow()->rmsModelId);
                    }
                    else
                    {
                        $deviceInstances[] = $targetDeviceInstance;
                    }

                    $db->beginTransaction();
                    try
                    {
                        if ($masterDeviceId == 0)
                        {
                            $successMessage = "Device unmapped successfully";
                            // Delete mapping
                            $deviceInstanceIds = [];
                            foreach ($deviceInstances as $deviceInstance)
                            {
                                $deviceInstanceIds[] = $deviceInstance->id;
                            }

                            $deviceInstanceMasterDeviceMapper->deleteMany($deviceInstanceIds);
                        }
                        else
                        {
                            foreach ($deviceInstances as $deviceInstance)
                            {

                                $deviceInstanceMasterDevice = $deviceInstance->getDeviceInstanceMasterDevice();
                                if ($deviceInstanceMasterDevice instanceof DeviceInstanceMasterDeviceModel)
                                {
                                    $deviceInstanceMasterDevice->masterDeviceId = $masterDeviceId;
                                    $deviceInstanceMasterDeviceMapper->save($deviceInstanceMasterDevice);
                                }
                                else
                                {
                                    $deviceInstanceMasterDevice                   = new DeviceInstanceMasterDeviceModel();
                                    $deviceInstanceMasterDevice->deviceInstanceId = $deviceInstance->id;
                                    $deviceInstanceMasterDevice->masterDeviceId   = $masterDeviceId;
                                    $deviceInstanceMasterDeviceMapper->insert($deviceInstanceMasterDevice);
                                }

                                $dealerId = $this->getIdentity()->dealerId;
                                $deviceInstance->compatibleWithJitProgram = $masterDevice->isJitCompatible($dealerId);
                                $deviceInstance->isLeased                 = ($masterDevice->isLeased($dealerId)) ? true : $deviceInstance->isLeased;
                                $deviceInstanceMapper->save($deviceInstance);
                            }
                        }

                        $db->commit();
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        \Tangent\Logger\Logger::logException($e);
                        $errorMessage = "An error occurred while mapping";
                    }
                }
                else
                {
                    $errorMessage = "Invalid Device Instance Selected!";
                }
            }
            else
            {
                // Invalid Master Device
                $errorMessage = "Invalid master device selected";
            }
        }
        else
        {
            $errorMessage = "You must send a master device id and a device instance id";
        }

        if ($errorMessage !== null)
        {
            $this->getResponse()->setHttpResponseCode(500);
            $this->sendJson(["error" => true, "message" => $errorMessage]);
        }
        else
        {
            $this->sendJson(["success" => true, "message" => $successMessage]);
        }
    }

    /**
     * This is the device summary page
     * $r->addRoute('rms-upload.summary',                    new R('rms-uploads/summary/:rmsUploadId',                    ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'summary',              'rmsUploadId' => false]));
     */
    public function summaryAction ()
    {
        $this->_pageTitle = ['Device Summary', 'RMS Upload'];
        $rmsUploadId      = $this->_getParam('rmsUploadId', false);
        $this->_navigation->setActiveStep(FleetStepsModel::STEP_FLEET_SUMMARY);
        $this->_navigation->updateAccessibleSteps(FleetStepsModel::STEP_FLEET_SUMMARY);
        $rmsUpload = null;
        if ($rmsUploadId > 0)
        {
            $rmsUpload = RmsUploadMapper::getInstance()->find($rmsUploadId);
        }

        if (!$rmsUpload instanceof RmsUploadModel)
        {
            $this->redirectToRoute('rms-upload');
        }

        $this->view->rmsUpload = $rmsUpload;

        if (!$rmsUpload instanceof RmsUploadModel)
        {
            $this->redirectToRoute('rms-upload');
        }

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['saveAndContinue']))
            {
                // FIXME: Should this have a magic way of going to a report?
                $this->redirectToRoute('app.dashboard'); // goes to dashboard (same as original mps)
            }
            else if (isset($postData['goBack']))
            {
                $this->redirectToRoute('rms-upload.mapping', ["rmsUploadId" => $rmsUploadId]);
            }
        }

        $this->view->navigationForm = new AssessmentNavigationForm(AssessmentNavigationForm::BUTTONS_ALL);
    }

    /**
     * The list of devices that are mapped in the report. It's used on the summary page
     * $r->addRoute('rms-upload.summary.device-list',        new R('rms-uploads/summary/device-list',                     ['module' => 'proposalgen', 'controller' => 'fleet',         'action' => 'device-summary-list',                        ]));
     */
    public function deviceSummaryListAction ()
    {
        $rmsUploadId = intval($this->_getParam('rmsUploadId', false));

        $rmsUpload = null;
        if ($rmsUploadId) {
            $rmsUpload = RmsUploadMapper::getInstance()->find($rmsUploadId);
            if ($rmsUpload) {
                $client = ClientMapper::getInstance()->find($rmsUpload->clientId);
                if ($client->dealerId != $this->getIdentity()->dealerId) $rmsUpload = null;
            }
        }
        if (empty($rmsUpload)) {
            $this->sendJson(['error' => 'Invalid RMS Upload Id']);
            return;
        }

        $jqGrid               = new JQGrid();
        $deviceInstanceMapper = DeviceInstanceMapper::getInstance();

        /*
         * Grab the incoming parameters
         */
        $jqGridParameters = [
            'sidx' => $this->_getParam('sidx', 'id'),
            'sord' => $this->_getParam('sord', 'ASC'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10)
        ];

        // Set up validation arrays
        $validSortColumns = ['id'];
        $sortColumns      = array_keys($validSortColumns);

        $jqGrid->parseJQGridPagingRequest($jqGridParameters);
        $jqGrid->setValidSortColumns($sortColumns);


        if ($jqGrid->sortingIsValid())
        {
            $jqGrid->setRecordCount($deviceInstanceMapper->getMappedDeviceInstances($rmsUploadId, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), null, null, true));

            // Validate current page number since we don't want to be out of bounds
            if ($jqGrid->getCurrentPage() < 1)
            {
                $jqGrid->setCurrentPage(1);
            }
            else if ($jqGrid->getCurrentPage() > $jqGrid->calculateTotalPages())
            {
                $jqGrid->setCurrentPage($jqGrid->calculateTotalPages());
            }

            // Return a small subset of the results based on the jqGrid parameters
            $startRecord     = $jqGrid->getRecordsPerPage() * ($jqGrid->getCurrentPage() - 1);
            $deviceInstances = $deviceInstanceMapper->getMappedDeviceInstances($rmsUploadId, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $jqGrid->getRecordsPerPage(), $startRecord);

            $rows = [];
            foreach ($deviceInstances as $deviceInstance)
            {
                $row = [
                    "id"                       => $deviceInstance->id,
                    "isExcluded"               => $deviceInstance->isExcluded,
                    "isManaged"                => $deviceInstance->isManaged,
                    "compatibleWithJitProgram" => $deviceInstance->compatibleWithJitProgram,
                    "ampv"                     => $this->view->formatPageVolume($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly()),
                    "reportsTonerLevels"       => $deviceInstance->isCapableOfReportingTonerLevels ? "Yes" : "No",
                    "isLeased"                 => $deviceInstance->isLeased,
                    "validToners"              => ($deviceInstance->hasValidToners($this->getIdentity()->dealerId, $this->getSelectedClient()->id)),
                ];

                $row["deviceName"] = $deviceInstance->getRmsUploadRow()->manufacturer . " " . $deviceInstance->getRmsUploadRow()->modelName . "<br>" . $deviceInstance->ipAddress . "; " . $deviceInstance->serialNumber;

                if ($deviceInstance->getMasterDevice() instanceof MasterDeviceModel)
                {
                    $row["mappedToDeviceName"]              = $deviceInstance->getMasterDevice()->getManufacturer()->fullname . " " . $deviceInstance->getMasterDevice()->modelName;
                    $row["isCapableOfReportingTonerLevels"] = $deviceInstance->getMasterDevice()->isCapableOfReportingTonerLevels ? "Yes" : "No";
                }
                else
                {
                    $row["mappedToDeviceName"]              = $deviceInstance->getRmsUploadRow()->getManufacturer()->fullname . " " . $deviceInstance->getRmsUploadRow()->modelName;
                    $row["isCapableOfReportingTonerLevels"] = "No";
                }

                $rows[] = $row;

            }

            $jqGrid->setRows($rows);

            // Send back jqGrid JSON data
            $this->sendJson($jqGrid->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->sendJson(['error' => 'Sorting parameters are invalid']);
        }
    }

    /**
     * This is where a user can modify the properties of an RMS upload row in a way that will make it valid
     * @deprecated
     */
    public function editUnknownDeviceAction ()
    {
        throw new Exception('deprecated');
    }

    /*
     * Handles removing an unknown device.
     * This is a JSON function
     * @deprecated
     */
    public function removeUnknownDeviceAction ()
    {
        throw new Exception('Deprecated');
    }

    /**
     * Handles excluding/including devices
     * @see FleetDeviceSummary.js
     */
    public function toggleExcludedFlagAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);
        if ($rmsUploadId > 0)
        {
            $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
            $isExcluded       = $this->_getParam("isExcluded", false) == 'true';
            $errorMessage     = false;

            if ($deviceInstanceId !== false)
            {
                $deviceInstanceMapper = DeviceInstanceMapper::getInstance();
                $deviceInstance       = $deviceInstanceMapper->find($deviceInstanceId);
                if ($deviceInstance instanceof DeviceInstanceModel)
                {
                    $rmsUpload = RmsUploadMapper::getInstance()->find($deviceInstance->rmsUploadId);
                    if ($rmsUpload)
                    {
                        $includedDeviceInstanceCount = DeviceInstanceMapper::getInstance()->getMappedDeviceInstances($rmsUpload->id, null, null, null, null, true, true);
                        if ($includedDeviceInstanceCount > 2 || $isExcluded === false)
                        {
                            if ($rmsUpload->id == $rmsUploadId)
                            {
                                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                                $db->beginTransaction();
                                try
                                {
                                    $deviceInstance->isExcluded = $isExcluded;
                                    $deviceInstanceMapper->save($deviceInstance);
                                    $db->commit();
                                }
                                catch (Exception $e)
                                {
                                    $db->rollBack();
                                    \Tangent\Logger\Logger::logException($e);
                                    $errorMessage = "The system encountered an error while trying to exclude the device. Reference #" . \Tangent\Logger\Logger::getUniqueId();
                                }
                            }
                            else
                            {
                                $errorMessage = "You can only exclude device instances that belong to the same assessment." . $rmsUpload->id . " - " . $rmsUploadId;
                            }
                        }
                        else
                        {
                            $errorMessage = "You must include at least 2 devices in your report.";

                        }
                    }
                    else
                    {
                        $errorMessage = "Invalid RMS Upload.";
                    }
                }
                else
                {
                    $errorMessage = "Invalid device instance.";
                }
            }
            else
            {
                $errorMessage = "Invalid device instance id.";
            }

            if ($errorMessage !== false)
            {
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson(["error" => true, "message" => $errorMessage]);
                return;
            }

            if ($isExcluded)
            {
                $this->sendJson(["success" => true, "message" => "Device is now excluded."]);
            }
            else
            {
                $this->sendJson(["success" => true, "message" => "Device is now included. "]);
            }
        }
        else
        {
            $this->getResponse()->setHttpResponseCode(500);
            $this->sendJson(["error" => true, "message" => "Invalid RMS Upload Id"]);
        }
    }

    /**
     * Handles Toggling of the isLeased flag
     * @see FleetDeviceSummary.js
     */
    public function toggleLeasedFlagAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);
        if ($rmsUploadId > 0)
        {
            $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
            $isLeased         = $this->_getParam("isLeased", false) == 'true';
            $errorMessage     = false;

            if ($deviceInstanceId !== false)
            {
                $deviceInstanceMapper = DeviceInstanceMapper::getInstance();
                $deviceInstance       = $deviceInstanceMapper->find($deviceInstanceId);
                if ($deviceInstance instanceof DeviceInstanceModel)
                {
                    $rmsUpload = RmsUploadMapper::getInstance()->find($deviceInstance->rmsUploadId);
                    if ($rmsUpload)
                    {
                        $includedDeviceInstanceCount = DeviceInstanceMapper::getInstance()->getMappedDeviceInstances($rmsUpload->id, null, null, null, null, true, true);
                        if ($includedDeviceInstanceCount > 2 || $isLeased === false)
                        {
                            if ($rmsUpload->id == $rmsUploadId)
                            {

                                if ($isLeased || $deviceInstance->hasValidToners($this->getIdentity()->dealerId, $this->getSelectedClient()->id))
                                {
                                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                                    $db->beginTransaction();
                                    try
                                    {
                                        $deviceInstance->isLeased = $isLeased;
                                        $deviceInstanceMapper->save($deviceInstance);
                                        $db->commit();
                                    }
                                    catch (Exception $e)
                                    {
                                        $db->rollBack();
                                        \Tangent\Logger\Logger::logException($e);
                                        $errorMessage = "The system encountered an error while trying to toggle the leased state of the device. Reference #" . \Tangent\Logger\Logger::getUniqueId();
                                    }
                                }
                                else
                                {
                                    $errorMessage = "Device does not have valid toners";
                                }
                            }
                            else
                            {
                                $errorMessage = "You can only change the leased state of device instances that belong to the same assessment." . $rmsUpload->id . " - " . $rmsUploadId;
                            }
                        }
                        else
                        {
                            $errorMessage = "You must include at least 2 devices in your report.";

                        }
                    }
                    else
                    {
                        $errorMessage = "Invalid RMS Upload.";
                    }
                }
                else
                {
                    $errorMessage = "Invalid device instance.";
                }
            }
            else
            {
                $errorMessage = "Invalid device instance id.";
            }

            if ($errorMessage !== false)
            {
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson(["error" => true, "message" => $errorMessage]);
                return;
            }

            if ($isLeased)
            {
                $this->sendJson(["success" => true, "message" => "Device is now leased."]);
            }
            else
            {
                $this->sendJson(["success" => true, "message" => "Device is no longer leased. "]);
            }
        }
        else
        {
            $this->getResponse()->setHttpResponseCode(500);
            $this->sendJson(["error" => true, "message" => "Invalid RMS Upload Id"]);
        }
    }

    /**
     * Handles Toggling of the isManaged flag
     * @see FleetDeviceSummary.js
     */
    public function toggleManagedFlagAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);
        if ($rmsUploadId > 0)
        {
            $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
            $isManaged        = $this->_getParam("isManaged", false) == 'true';
            $errorMessage     = false;

            if ($deviceInstanceId !== false)
            {
                $deviceInstanceMapper = DeviceInstanceMapper::getInstance();
                $deviceInstance       = $deviceInstanceMapper->find($deviceInstanceId);

                if ($deviceInstance instanceof DeviceInstanceModel)
                {
                    $rmsUpload = RmsUploadMapper::getInstance()->find($deviceInstance->rmsUploadId);

                    if ($rmsUpload)
                    {
                        $includedDeviceInstanceCount = DeviceInstanceMapper::getInstance()->getMappedDeviceInstances($rmsUpload->id, null, null, null, null, true, true);

                        if ($includedDeviceInstanceCount > 2 || $isManaged === false)
                        {
                            if ($rmsUpload->id == $rmsUploadId)
                            {
                                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                                $db->beginTransaction();
                                try
                                {
                                    $deviceInstance->isManaged = $isManaged;
                                    $deviceInstanceMapper->save($deviceInstance);
                                    $db->commit();
                                }
                                catch (Exception $e)
                                {
                                    $db->rollBack();
                                    \Tangent\Logger\Logger::logException($e);
                                    $errorMessage = "The system encountered an error while trying to toggle the managed state of the device. Reference #" . \Tangent\Logger\Logger::getUniqueId();
                                }
                            }
                            else
                            {
                                $errorMessage = "You can only change the leased state of device instances that belong to the same report." . $rmsUpload->id . " - " . $rmsUploadId;
                            }
                        }
                        else
                        {
                            $errorMessage = "You must include at least 2 devices in your report.";

                        }
                    }
                    else
                    {
                        $errorMessage = "Invalid RMS Upload.";
                    }
                }
                else
                {
                    $errorMessage = "Invalid device instance.";
                }
            }
            else
            {
                $errorMessage = "Invalid device instance id.";
            }

            if ($errorMessage !== false)
            {
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson(["error" => true, "message" => $errorMessage]);
                return;
            }

            if ($isManaged)
            {
                $this->sendJson(["success" => true, "message" => "Device is now managed."]);
            }
            else
            {
                $this->sendJson(["success" => true, "message" => "Device is no longer managed. "]);
            }
        }
        else
        {
            $this->getResponse()->setHttpResponseCode(500);
            $this->sendJson(["error" => true, "message" => "Invalid RMS Upload Id"]);
        }
    }

    /**
     * Handles Toggling the JIT Compatibility of devices
     * @see FleetDeviceSummary.js
     */
    public function toggleJitFlagAction ()
    {
        $rmsUploadId = $this->_getParam('rmsUploadId', false);
        if ($rmsUploadId > 0)
        {
            $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
            $isJitCompatible  = $this->_getParam("compatibleWithJitProgram", false) == 'true';
            $errorMessage     = false;

            if ($deviceInstanceId !== false)
            {
                $deviceInstanceMapper = DeviceInstanceMapper::getInstance();
                $deviceInstance       = $deviceInstanceMapper->find($deviceInstanceId);
                if ($deviceInstance instanceof DeviceInstanceModel)
                {
                    $rmsUpload = RmsUploadMapper::getInstance()->find($deviceInstance->rmsUploadId);
                    if ($rmsUpload)
                    {
                        $includedDeviceInstanceCount = DeviceInstanceMapper::getInstance()->getMappedDeviceInstances($rmsUpload->id, null, null, null, null, true, true);
                        if ($includedDeviceInstanceCount > 2 || $isJitCompatible === false)
                        {
                            if ($rmsUpload->id == $rmsUploadId)
                            {
                                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                                $db->beginTransaction();
                                try
                                {
                                    $deviceInstance->compatibleWithJitProgram = $isJitCompatible;
                                    $deviceInstanceMapper->save($deviceInstance);
                                    $db->commit();
                                }
                                catch (Exception $e)
                                {
                                    $db->rollBack();
                                    \Tangent\Logger\Logger::logException($e);
                                    $errorMessage = "The system encountered an error while trying to toggle the " . My_Brand::$jit . " compatibility of the device. Reference #" . \Tangent\Logger\Logger::getUniqueId();
                                }
                            }
                            else
                            {
                                $errorMessage = "You can only change the " . My_Brand::$jit . " compatibility of device instances that belong to the same assessment." . $rmsUpload->id . " - " . $rmsUploadId;
                            }
                        }
                        else
                        {
                            $errorMessage = "You must include at least 2 devices in your report.";

                        }
                    }
                    else
                    {
                        $errorMessage = "Invalid RMS Upload.";
                    }
                }
                else
                {
                    $errorMessage = "Invalid device instance.";
                }
            }
            else
            {
                $errorMessage = "Invalid device instance id.";
            }

            if ($errorMessage !== false)
            {
                $this->getResponse()->setHttpResponseCode(500);
                $this->sendJson(["error" => true, "message" => $errorMessage]);
                return;
            }

            if ($isJitCompatible)
            {
                $this->sendJson(["success" => true, "message" => "Device is now " . My_Brand::$jit . " Compatible."]);
            }
            else
            {
                $this->sendJson(["success" => true, "message" => "Device is now no longer " . My_Brand::$jit . " Compatible. "]);
            }
        }
        else
        {
            $this->getResponse()->setHttpResponseCode(500);
            $this->sendJson(["error" => true, "message" => "Invalid RMS Upload Id"]);
        }
    }

    /**
     * Gets all the details for a device instance
     * @see FleetDeviceSummary.js
     */
    public function deviceInstanceDetailsAction ()
    {
        $rmsUploadId  = $this->_getParam('rmsUploadId', false);
        $jsonResponse = [];
        $errorMessage = false;

        $costPerPageSetting = new CostPerPageSettingModel();

        if ($rmsUploadId > 0)
        {
            $deviceInstanceId = $this->_getParam("deviceInstanceId", false);

            /*
             * We must get a deviceInstanceId
             */
            if ($deviceInstanceId !== false)
            {
                /*
                 * Devices must exist
                 */
                $deviceInstance = DeviceInstanceMapper::getInstance()->find($deviceInstanceId);
                if ($deviceInstance instanceof DeviceInstanceModel)
                {
                    /*
                     * Devices must be part of our report
                     */
                    if ($deviceInstance->rmsUploadId == $rmsUploadId)
                    {
                        /*
                         * Once we get here we can start populating our response
                         */
                        $jsonResponse                                       = $deviceInstance->toArray();
                        $jsonResponse['masterDevice']                       = $deviceInstance->getMasterDevice()->toArray();
                        $launchDate                                         = new Zend_Date($jsonResponse['masterDevice']['launchDate']);
                        $jsonResponse['masterDevice']['launchDate']         = $launchDate->toString(Zend_Date::DATE_MEDIUM);
                        $jsonResponse['masterDevice']['age']                = $deviceInstance->getAge();
                        $jsonResponse['masterDevice']['cost']               = ''; //($jsonResponse['cost'] > 0) ? $this->view->currency((float)$jsonResponse['cost']) : '';
                        $jsonResponse['masterDevice']['ppmBlack']           = ($jsonResponse['masterDevice']['ppmBlack'] > 0) ? number_format($jsonResponse['masterDevice']['ppmBlack']) : '';
                        $jsonResponse['masterDevice']['ppmColor']           = ($jsonResponse['masterDevice']['ppmColor'] > 0) ? number_format($jsonResponse['masterDevice']['ppmColor']) : '';
                        $jsonResponse['masterDevice']['wattsPowerNormal']   = number_format($jsonResponse['masterDevice']['wattsPowerNormal']);
                        $jsonResponse['masterDevice']['wattsPowerIdle']     = number_format($jsonResponse['masterDevice']['wattsPowerIdle']);
                        $jsonResponse['masterDevice']['leasedTonerYield']   = number_format($jsonResponse['masterDevice']['leasedTonerYield']);
                        $jsonResponse["masterDevice"]["manufacturer"]       = $deviceInstance->getMasterDevice()->getManufacturer()->toArray();
                        $jsonResponse["masterDevice"]["reportsTonerLevels"] = $deviceInstance->getMasterDevice()->isCapableOfReportingTonerLevels;
                        $jsonResponse["masterDevice"]["tonerConfigName"]    = $deviceInstance->getMasterDevice()->getTonerConfig()->name;
                        $jsonResponse["masterDevice"]["compatibleWithJit"]  = $deviceInstance->getMasterDevice()->isJitCompatible($this->getIdentity()->dealerId);
                        $jsonResponse["masterDevice"]["isColor"]            = $deviceInstance->getMasterDevice()->isColor();
                        $jsonResponse["pageCounts"]                         = [];
                        $jsonResponse["pageCounts"]['monochrome']           = number_format($deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly());
                        $jsonResponse["pageCounts"]['color']                = number_format($deviceInstance->getPageCounts()->getColorPageCount()->getMonthly());
                        $jsonResponse["pageCounts"]['a3Combined']           = number_format($deviceInstance->getPageCounts()->getPrintA3CombinedPageCount()->getMonthly());
                        $jsonResponse["meters"]                             = [];
                        $jsonResponse["meters"]['life']                     = number_format($deviceInstance->getMeter()->endMeterLife);
                        $jsonResponse["meters"]['maxLife']                  = number_format($deviceInstance->getMasterDevice()->calculateEstimatedMaxLifeCount());
                        $jsonResponse["lifeUsage"]                          = number_format($deviceInstance->getLifeUsage() * 100) . '%';
                        $jsonResponse["pageCoverage"]                       = [];
                        $jsonResponse["pageCoverage"]['monochrome']         = number_format((float)$deviceInstance->pageCoverageMonochrome, 2) . '%';
                        $jsonResponse["pageCoverage"]['cyan']               = number_format((float)$deviceInstance->pageCoverageCyan, 2) . '%';
                        $jsonResponse["pageCoverage"]['magenta']            = number_format((float)$deviceInstance->pageCoverageMagenta, 2) . '%';
                        $jsonResponse["pageCoverage"]['yellow']             = number_format((float)$deviceInstance->pageCoverageYellow, 2) . '%';
                        $jsonResponse["masterDevice"]["toners"]             = [];

                        foreach ($deviceInstance->getMasterDevice()->getToners($this->getIdentity()->dealerId, $this->getSelectedClient()->id) as $tonersByManufacturer)
                        {
                            foreach ($tonersByManufacturer as $tonersByColor)
                            {
                                /* @var $toner TonerModel */
                                foreach ($tonersByColor as $toner)
                                {
                                    $tonerArray                               = $toner->toArray();
                                    $tonerArray['cost']                       = $this->view->currency((float)$tonerArray['cost']);
                                    $tonerArray['yield']                      = number_format($tonerArray['yield']);
                                    $tonerArray['manufacturer']               = ($toner->getManufacturer()) ? $toner->getManufacturer()->toArray() : "Unknown";
                                    $tonerArray['tonerColorName']             = TonerColorModel::$ColorNames[$toner->tonerColorId];
                                    $jsonResponse["masterDevice"]["toners"][] = $tonerArray;

                                }
                            }
                        }
                    }
                    else
                    {
                        $errorMessage = "Device does not belong to your report.";
                    }
                }
                else
                {
                    $errorMessage = "Invalid device specified.";
                }
            }
            else
            {
                $errorMessage = "Missing/Invalid Parameter 'deviceInstanceId'.";
            }
        }
        else
        {
            $errorMessage = "Missing/Invalid Parameter 'rmsUploadId'.";
        }

        if ($errorMessage !== false)
        {
            $jsonResponse = ["error" => true, "message" => $errorMessage];
            $this->getResponse()->setHttpResponseCode(500);
        }

        $this->sendJson($jsonResponse);
    }

    /**
     * Deletes an RMS Upload
     * @Deprecated
     */
    public function deleteAction ()
    {
        throw new Exception('Deprecated');
    }

    /**
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch ()
    {
        parent::postDispatch();

        $rmsUploadId = $this->_getParam('rmsUploadId', false);
        $this->view->placeholder('ProgressionNav')->set($this->view->NavigationMenu($this->_navigation, ['rmsUploadId' => $rmsUploadId]));
    }
}