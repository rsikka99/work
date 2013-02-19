<?php
class Proposalgen_Library_Controller_Proposal extends Zend_Controller_Action
{
    /**
     * @var Proposalgen_Model_Report
     */
    protected $_report;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_reportSession;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_mpsSession;

    /**
     * @var Proposalgen_Model_Report_Step
     */
    protected $_reportSteps;

    /**
     * @var int
     */
    protected $_userId;

    /**
     * @var int
     */
    protected $_clientId;

    /**
     * The current step that the user is viewing.
     *
     * @var Proposalgen_Model_Report_Step
     */
    protected $_activeStep;


    /**
     * The current proposal
     *
     * @var Proposalgen_Model_Proposal_OfficeDepot
     */
    protected $_proposal;
    protected $_csvFormat;
    protected $_pdfFormat;
    protected $_wordFormat;
    protected $_reportId;
    protected $_reportCompanyName;
    protected $_reportAbsoluteCachePath;
    protected $_reportCachePath;

    /**
     * Called from the constructor as the final step of initialization
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
        $this->_clientId   = (int)$this->_mpsSession->selectedClientId;


        $this->_reportSession    = new Zend_Session_Namespace('proposalgenerator_report');
        $this->_reportId         = (int)$this->_reportSession->reportId;
        $this->view->reportSteps = $this->getReportSteps();
        $this->_userId           = Zend_Auth::getInstance()->getIdentity()->id;


        $this->_reportAbsoluteCachePath = PUBLIC_PATH . "/cache/reports/" . $this->_reportId;
        $this->_reportCachePath         = "/cache/reports/" . $this->_reportId;
        // Make the directory if it doesn't exist
        if (!is_dir($this->_reportAbsoluteCachePath))
        {
            if (!mkdir($this->_reportAbsoluteCachePath, 0777, true))
            {
                throw new Exception("Could not open cache folder! PATH:" . $this->_reportAbsoluteCachePath, 0);
            }
        }

        $this->view->ReportAbsoluteCachePath = $this->_reportAbsoluteCachePath;
        $this->view->ReportCachePath         = $this->_reportCachePath;
    }

    public function initReportList ()
    {
        // This is a list of reports that we can view.
        $this->view->availableReports = (object)array(
            "Assessment"         => (object)array(
                "pagetitle" => "Assessment",
                "active"    => false,
                "url"       => $this->view->baseUrl('/proposalgen/report_assessment/index')
            ),
            "Solution"           => (object)array(
                "pagetitle" => "Solution",
                "active"    => false,
                "url"       => $this->view->baseUrl('/proposalgen/report_solution/index')
            ),
            "GrossMargin"        => (object)array(
                "pagetitle" => "Gross Margin",
                "active"    => false,
                "url"       => $this->view->baseUrl('/proposalgen/report_grossmargin/index')
            ),
            "PrintingDeviceList" => (object)array(
                "pagetitle" => "Printing Device List",
                "active"    => false,
                "url"       => $this->view->baseUrl('/proposalgen/report_printingdevicelist/index')
            ),
            "PIQEssentials"      => (object)array(
                "pagetitle" => "Print IQ Essentials",
                "active"    => false,
                "url"       => $this->view->baseUrl('/proposalgen/report_piqessentials/index')
            ),
            "HealthCheck"      => (object)array(
                "pagetitle" => "Health Check",
                "active"    => false,
                "url"       => $this->view->baseUrl('/proposalgen/report_healthcheck/index')
            ),
        );
    }

    /**
     * Init function for html reports
     *
     * @throws Exception
     */
    public function initHtmlReport ()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/htmlReport.js'));

        $this->_user = Application_Model_Mapper_User::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->id);

        if ($this->_reportId < 1)
        {
            $this->_helper->flashMessenger(array(
                                                "error" => "Please select a report first."
                                           ));
            // Send user to the index
            $this->_redirect('/');
        }

        $this->_report = Proposalgen_Model_Mapper_Report::getInstance()->find($this->_reportId);
        if ($this->_report === null)
        {
            $this->_helper->flashMessenger(array(
                                                "error" => "Please select a report first."
                                           ));
            // Send user to the index
            $this->_redirect('/');
        }

        $this->view->reportMenu = new Custom_Report_Menu($this->_report);
        if (!$this->view->reportMenu->canAccessPage('finished'))
        {
            // Redirect to the last page that the user is allowed seeing
            $this->_redirect($this->view->reportMenu->currentPage());
        }

        // Setup the different file formats
        $this->_csvFormat  = (object)array(
            'extension'      => 'csv',
            'name'           => 'Excel (CSV)',
            'loadingmessage' => '',
            'btnstyle'       => 'success'
        );
        $this->_pdfFormat  = (object)array(
            'extension'      => 'pdf',
            'name'           => 'Adobe PDF',
            'loadingmessage' => 'Please be patient as PDF\'s take a few minutes to generate',
            'btnstyle'       => 'danger'
        );
        $this->_wordFormat = (object)array(
            'extension'      => 'docx',
            'name'           => 'Word (DOCX)',
            'loadingmessage' => 'Please wait a moment while we generate your report',
            'btnstyle'       => 'primary'
        );
    }

    /**
     * Prepares the view (for html reports) with the variables needed.
     *
     * @param $filename
     */
    public function initReportVariables ($filename)
    {
        $this->view->publicFileName = $this->_reportCachePath . "/" . $filename;
        $this->view->savePath       = $this->_reportAbsoluteCachePath . "/" . $filename;

        $this->view->proposal = $this->getProposal();
    }

    /**
     * Gets the proposal object for reports to use
     *
     * @throws Zend_Exception
     * @return Proposalgen_Model_Proposal_OfficeDepot
     */
    public function getProposal ()
    {
        if (!$this->_proposal)
        {
            $this->_proposal = false;
            $hasError        = false;
            try
            {
                $this->_proposal = new Proposalgen_Model_Proposal_OfficeDepot($this->_report);

                if ($this->_report->devicesModified)
                {
                    $this->_redirect('/data/modificationwarning');
                }

                if (count($this->_proposal->getDeviceCount()) < 1)
                {
                    $this->view->ErrorMessages [] = "All uploaded printers were excluded from your report. Reports can not be generated until at least 1 printer is added.";
                    $hasError                     = true;
                }
            }
            catch (Exception $e)
            {
                $this->view->ErrorMessages [] = "There was an error getting the reports.";
                throw new Zend_Exception("Error Getting Proposal Object.", 0, $e);
                $hasError = true;
            }

            if ($hasError)
            {
                $this->_proposal = false;
            }
        }

        return $this->_proposal;
    }


    /**
     * Deletes old files in the report cache
     *
     * @throws Exception
     * @return int The number of files deleted
     */
    public function clearCacheForReport ()
    {
        $path = $this->_reportAbsoluteCachePath;
        try
        {
            $fileDeleteDate = strtotime("-1 hour");
            $files          = array();

            // Get all files to delete
            if (false !== ($handle = @opendir($path)))
            {
                // Get rid of cache to ensure we have proper information on the
                // files we want to delete.
                clearstatcache();
                while (false !== ($file = readdir($handle)))
                {
                    if ($file != "." && $file != "..")
                    {
                        // Only select the file if it is older than
                        // $fileDeleteDate
                        if (filemtime("$path/$file") < $fileDeleteDate)
                        {
                            $files [] = "$path/$file";
                        }
                    }
                }
                closedir($handle);
            }

            // Delete all files that we found
            foreach ($files as $file)
            {
                @unlink($file);
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Error while cleaning the cache for the report");
        }

        return count($files);
    }

    /**
     * Downloads images ahead of time using curl.
     * Uses multi threading
     *
     * @param         $imageArray array
     *                            An array of URL's to images. Currently only saves .png files
     * @param boolean $local
     *                            Whether or not the change the image path to a local path or a
     *                            web accessible path
     *
     * @throws Exception
     * @return array
     */
    public function cachePNGImages ($imageArray, $local = true)
    {

        $cachePath = $this->_reportAbsoluteCachePath;

        $reportId = $this->_reportId;
        try
        {
            // Download files ahead of time
            $randomSalt         = strftime("%s") . mt_rand(10000, 30000);
            $imagePathAndPrefix = $cachePath . '/' . $randomSalt . "_";

            $newImages   = array();
            $multihandle = curl_multi_init();

            foreach ($imageArray as $i => $fetchUrl)
            {
                $imageFilename = $imagePathAndPrefix . $i . '.png';
                if (file_exists($imageFilename)) // Delete file if it already exists
                {
                    unlink($imageFilename);
                }

                // To fix the way the graphs are generated, we change &amp; to &
                $fetchUrl = str_replace("&amp;", "&", $fetchUrl);
                $fetchUrl = str_replace(" ", "%20", $fetchUrl);

                $conn [$i] = curl_init($fetchUrl);
                $file [$i] = fopen($imageFilename, "w");

                curl_setopt($conn [$i], CURLOPT_FILE, $file [$i]);
                curl_setopt($conn [$i], CURLOPT_HEADER, 0);
                curl_setopt($conn [$i], CURLOPT_CONNECTTIMEOUT, 60);
                curl_multi_add_handle($multihandle, $conn [$i]);
                $newImages [] = $imageFilename;
            }

            // Wait until all the images are downloaded
            do
            {
                $n = curl_multi_exec($multihandle, $active);
            } while ($active);

            // Change the path of the images to a new path
            foreach ($imageArray as $i => & $imageUrl)
            {
                curl_multi_remove_handle($multihandle, $conn [$i]);
                curl_close($conn [$i]);
                fclose($file [$i]);
                if ($local)
                {
                    $imageUrl = PUBLIC_PATH . "/cache/reports/$reportId/" . $randomSalt . "_$i.png";
                }
                else
                {
                    $imageUrl = $this->view->FullUrl("/cache/reports/$reportId/" . $randomSalt . "_$i.png");
                }

            }
            curl_multi_close($multihandle);
            // Change Permissions on all the images
            $newImages [] = $imageFilename;
            chmod($cachePath, 0777);
            foreach ($newImages as $filePath)
            {
                chmod($filePath, 0777);
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Could not cache image files!", 0, $e);
        }

        return $imageArray;

    }

    /**
     * Gets the name of the company in the survey
     *
     * @throws Exception
     */
    public function getReportCompanyName ()
    {
        if (!isset($this->_reportCompanyName))
        {
            $questionTable = new Proposalgen_Model_DbTable_TextAnswer();
            $where         = $questionTable->getAdapter()->quoteInto('report_id = ? AND question_id = 4', $this->_reportId, 'INTEGER');
            $row           = $questionTable->fetchRow($where);

            if ($row ['textual_answer'])
            {
                $this->_reportCompanyName = $row ['textual_answer'];
            }
            else
            {
                throw new Exception("No Company Name Found!");
            }
        }

        return $this->_reportCompanyName;

    } // end function getReportCompanyName


    /**
     * Verifies that a replacement device of each type is found.
     */
    public function verifyReplacementDevices ()
    {
        $replacementDeviceMapper = Proposalgen_Model_Mapper_ReplacementDevice::getInstance();
        foreach (Proposalgen_Model_ReplacementDevice::$replacementTypes as $type)
        {
            try
            {
                $row = null;
                $row = $replacementDeviceMapper->fetchRow(array(
                                                               'replacement_category = ?' => $type
                                                          ));
                if (!$row)
                {
                    throw new Exception("Error: Missing replacement device for the $type category.");
                }
            }
            catch (Exception $e)
            {
                $this->view->ErrorMessages [] = "Error: Missing replacement device for the $type category.";
            }
        }

    }

    /**
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch ()
    {
        // Render our survey menu
        $stage = ($this->getReport()->reportStage) ? : Proposalgen_Model_Report_Step::STEP_SURVEY;
        Proposalgen_Model_Report_Step::updateAccessibleSteps($this->getReportSteps(), $stage);

        $this->view->placeholder('ProgressionNav')->set($this->view->ProposalMenu($this->getReportSteps()));
    }

    /**
     * Gets the report object that we are working with
     *
     * @throws Exception
     * @return Proposalgen_Model_Report
     */
    protected function getReport ()
    {
        if (!isset($this->_report))
        {
            // Fetch the existing report, or create a new one if the session id isn't set
            if (isset($this->_reportSession->reportId) && $this->_reportSession->reportId > 0)
            {
                $this->_report = Proposalgen_Model_Mapper_Report::getInstance()->find((int)$this->_reportSession->reportId);
                if ($this->_report === null)
                {
                    throw new Exception("Error selecting the report with an id of '{$this->_reportSession->reportId}'.");
                }
            }
            else
            {
                $identity                   = Zend_Auth::getInstance()->getIdentity();
                $this->_report              = new Proposalgen_Model_Report();
                $this->_report->userId      = $identity->id;
                $this->_report->clientId    = $this->_clientId;
                $this->_report->reportStage = Proposalgen_Model_Report_Step::STEP_SURVEY;
                $this->_report->dateCreated = date('Y-m-d H:i:s');
                $this->_report->reportDate  = date('Y-m-d H:i:s');
            }
        }

        return $this->_report;
    }

    /**
     * Saves the current report.
     * This keeps the updated modification date in the same location at all times.
     */
    protected function saveReport ($updateReportStage = true)
    {
        $reportMapper                = Proposalgen_Model_Mapper_Report::getInstance();
        $this->_report->lastModified = date('Y-m-d H:i:s');

        if ($updateReportStage)
        {
            // This updates the reports progress
            $newStep = $this->checkIfNextStepIsNew($this->_activeStep);
            if ($newStep !== false)
            {
                $this->_report->reportStage = $newStep->enumValue;

                // We need to adjust the menu just in case we're not redirecting
                Proposalgen_Model_Report_Step::updateAccessibleSteps($this->getReportSteps(), $newStep->enumValue);
            }
        }

        if ($this->_report->id === null || $this->_report->id < 1)
        {
            $id                = $reportMapper->insert($this->_report);
            $this->_report->id = $id;
        }
        else
        {
            $id = $reportMapper->save($this->_report);
        }

        $this->_reportSession->reportId = $this->_report->id;
    }

    /**
     * Gets an array of report steps.
     *
     * @return Proposalgen_Model_Report_Step[]
     */
    protected function getReportSteps ()
    {
        $report      = $this->getReport();
        $reportSteps = null;
        if ($report === null)
        {
            $reportSteps = Proposalgen_Model_Report_Step::getSteps();
        }
        else
        {
            $reportSteps = $report->getReportSteps();
        }

        return $reportSteps;
    }

    /**
     * Checks to see if the next step is a new step.
     *
     * @param Proposalgen_Model_Report_Step $step
     *
     * @return Proposalgen_Model_Report_Step Step Name. Returns FALSE if the step is not new.
     */
    protected function checkIfNextStepIsNew ($step)
    {
        if ($step !== null)
        {
            $nextStep = $step->nextStep;
            if ($nextStep !== null)
            {
                if (!$nextStep->canAccess)
                {
                    return $nextStep;
                }
            }
        }

        return false;
    }

    /**
     * Gets the last available step for a report
     *
     * @return Proposalgen_Model_Report_Step
     */
    protected function getLatestAvailableReportStep ()
    {
        $latestStep = null;

        /* @var $step Proposalgen_Model_Report_Step */
        foreach ($this->getReportSteps() as $step)
        {
            /*
             * Just in case we don't find anything, lets set the step to the very first step.
             */
            if ($latestStep === null)
            {
                $latestStep = $step;
            }

            /*
         * If we can access the current step, and the next step either doesn't exist or is inaccessable.
         */
            if ($step->canAccess && ($step->nextStep === null || !$step->nextStep->canAccess))
            {
                $latestStep = $step;
                break;
            }
        }

        return $latestStep;
    }

    /**
     * Sets a report step as active
     *
     * @param string $activeStepName
     *            The name of the step that is active
     */
    protected function setActiveReportStep ($activeStepName)
    {
        $this->_activeStep = null;
        foreach ($this->getReportSteps() as $step)
        {
            $step->active = false;
            if (strcasecmp($step->enumValue, $activeStepName) === 0)
            {

                $this->_activeStep = $step;
                $step->active      = true;
                break;
            }
        }
    }

    /**
     * Takes the user to the next step in the survey in order.
     * So company always goes to general
     */
    protected function gotoNextStep ()
    {
        if (isset($this->_activeStep))
        {
            $nextStep = $this->_activeStep->nextStep;
            if ($nextStep)
            {
                $this->_helper->redirector($nextStep->action, $nextStep->controller);
            }
        }
    }

    /**
     * Takes the user to the previous step in the survey in order.
     * So company always goes to general
     */
    protected function gotoPreviousStep ()
    {
        if (isset($this->_activeStep))
        {
            $prevStep = $this->_activeStep->previousStep;
            if ($prevStep)
            {
                $this->_helper->redirector($prevStep->action, $prevStep->controller);
            }
        }
    }
}
