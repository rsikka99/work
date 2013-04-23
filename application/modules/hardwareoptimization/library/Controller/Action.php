<?php
class Hardwareoptimization_Library_Controller_Action extends Tangent_Controller_Action
{
    /**
     * The Zend_Auth identity
     *
     * @var stdClass
     */
    protected $_identity;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_mpsSession;

    /**
     * @var Hardwareoptimization_Model_Hardware_Optimization
     */
    protected $_hardwareOptimization;

    /**
     * The navigation steps
     *
     * @var Hardwareoptimization_Model_Hardware_Optimization_Steps
     */
    protected $_navigation;


    /**
     * An object containing various word styles
     *
     * @var stdClass
     */
    protected $_wordStyles;

    /**
     * Report name is the title behind the reports that are being generated.
     *
     * @var string
     */
    public $reportName;


    /**
     * The current proposal
     *
     * @var Hardwareoptimization_ViewModel_CustomerHardwareOptimization
     */
    protected $_customerHardwareOptimizationViewModel;

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
        $this->_identity   = Zend_Auth::getInstance()->getIdentity();
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
        $this->_navigation = Hardwareoptimization_Model_Hardware_Optimization_Steps::getInstance();

        if (isset($this->_mpsSession->selectedClientId))
        {
            $client = Quotegen_Model_Mapper_Client::getInstance()->find($this->_mpsSession->selectedClientId);
            if (!$client instanceof Quotegen_Model_Client)
            {
                $this->_flashMessenger->addMessage(array(
                                                        "error" => "A client is not selected."
                                                   ));

                $this->redirector('index', 'index', 'index');
            }
        }

        $this->_reportAbsoluteCachePath = PUBLIC_PATH . "/cache/reports/hardwareoptimization/" . $this->getHardwareOptimization()->id;
        $this->_reportCachePath         = "/cache/reports/hardwareoptimization/" . $this->_hardwareOptimization->id;

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
            "Reports"              => (object)array(
                "pagetitle" => "Select a report...",
                "active"    => false,
                "url"       => $this->view->baseUrl('/hardwareoptimization/report_index/index')
            ),
            "CustomerOptimization" => (object)array(
                "pagetitle" => "Customer Optimization",
                "active"    => false,
                "url"       => $this->view->baseUrl('/hardwareoptimization/report_customer_optimization/index')
            ),
            "DealerOptimization"   => (object)array(
                "pagetitle" => "Dealer Optimization",
                "active"    => false,
                "url"       => $this->view->baseUrl('/hardwareoptimization/report_dealer_optimization/index')
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


        if ($this->getHardwareOptimization()->id < 1)
        {
            $this->_flashMessenger->addMessage(array("error" => "Please select a report first."));

            // Send user to the index
            $this->redirector('index', 'index', 'index');
        }

        // Setup the different file formats
        $this->_csvFormat           = (object)array(
            'extension'      => 'csv',
            'name'           => 'Excel (CSV)',
            'loadingmessage' => '',
            'btnstyle'       => 'success'
        );
        $this->_wordFormat          = (object)array(
            'extension'      => 'docx',
            'name'           => 'Word (DOCX)',
            'loadingmessage' => 'Please wait a moment while we generate your report',
            'btnstyle'       => 'primary'
        );
        $this->view->dealerLogoFile = $this->getDealerLogoFile();
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


        $this->view->dealerLogoFile = $this->getDealerLogoFile();

        $this->view->proposal = $this->getCustomerHardwareOptimizationViewModel();
    }

    /**
     * Gets the dealer logo file relative to the public folder
     *
     * @return string
     */
    public function getDealerLogoFile ()
    {
        $dealer   = Admin_Model_Mapper_Dealer::getInstance()->find($this->_identity->dealerId);
        $logoFile = false;
        if ($dealer)
        {
            if ($dealer->dealerLogoImageId > 0)
            {
                $logoFile = $dealer->getDealerLogoImageFile();
            }
        }


        if ($logoFile == false)
        {
            $logoFile = $this->view->theme("proposalgenerator/reports/images/mpstoolbox_logo.jpg");
        }

        return $logoFile;
    }

    /**
     * Gets the proposal object for reports to use
     *
     * @throws Zend_Exception
     * @return Hardwareoptimization_ViewModel_CustomerHardwareOptimization
     */
    public function getCustomerHardwareOptimizationViewModel ()
    {
        if (!$this->_customerHardwareOptimizationViewModel)
        {
            $this->_customerHardwareOptimizationViewModel = false;
            $hasError                                     = false;
            try
            {
                $this->_customerHardwareOptimizationViewModel = new Hardwareoptimization_ViewModel_CustomerHardwareOptimization($this->getHardwareOptimization());

                if ($this->getHardwareOptimization()->devicesModified)
                {
                    $this->_redirect('/data/modificationwarning');
                }

                if ($this->_customerHardwareOptimizationViewModel->getDeviceCount() < 1)
                {
                    $this->view->ErrorMessages [] = "All uploaded printers were excluded from your report. Reports can not be generated until at least 1 printer is added.";
                    $hasError                     = true;
                }
            }
            catch (Exception $e)
            {
                $this->view->ErrorMessages [] = "There was an error getting the reports.";
                throw new Zend_Exception("Error Getting Customer Hardware Optimization View Model.", 0, $e);
            }

            if ($hasError)
            {
                $this->_customerHardwareOptimizationViewModel = false;
            }
        }

        return $this->_customerHardwareOptimizationViewModel;
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
        $stage = ($this->getHardwareOptimization()->stepName) ? : Hardwareoptimization_Model_Hardware_Optimization_Steps::STEP_FLEET_UPLOAD;
        $this->_navigation->updateAccessibleSteps($stage);

        $this->view->placeholder('ProgressionNav')->set($this->view->NavigationMenu($this->_navigation->steps));
    }

    public function getWordStyles ()
    {
        if (!isset($this->_wordStyles))
        {
            // Get the for a dealer styles table
            $this->_wordStyles                                         = new stdClass();
            $this->_wordStyles->default->sectionHeaderFontColor        = "0096D6";
            $this->_wordStyles->default->sectionHeaderBorderColor      = "000000";
            $this->_wordStyles->default->subSectionBackgroundColor     = "0096D6";
            $this->_wordStyles->default->subSectionFontColor           = "FFFFFF";
            $this->_wordStyles->default->tableHeaderBackgroundColor    = "B8CCE3";
            $this->_wordStyles->default->tableSubHeaderBackgroundColor = "EAF0F7";
            $this->_wordStyles->default->tableHeaderFontColor          = "FFFFFF";
        }

        return $this->_wordStyles;
    }


    /**
     * Gets the hardware optimization we're working on
     *
     * @return Hardwareoptimization_Model_Hardware_Optimization
     */
    public function getHardwareOptimization ()
    {
        if (!isset($this->_hardwareOptimization))
        {
            if (isset($this->_mpsSession->hardwareOptimizationId) && $this->_mpsSession->hardwareOptimizationId > 0)
            {
                $this->_hardwareOptimization = Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance()->find($this->_mpsSession->hardwareOptimizationId);
            }
            else
            {
                $this->_hardwareOptimization               = new Hardwareoptimization_Model_Hardware_Optimization();
                $this->_hardwareOptimization->dateCreated  = date('Y-m-d H:i:s');
                $this->_hardwareOptimization->lastModified = date('Y-m-d H:i:s');
                $this->_hardwareOptimization->dealerId     = $this->_identity->dealerId;
                $this->_hardwareOptimization->clientId     = $this->_mpsSession->selectedClientId;
            }
        }

        return $this->_hardwareOptimization;
    }

    /**
     * Saves a hardware optimization
     */
    public function saveHardwareOptimization ()
    {
        if (isset($this->_mpsSession->hardwareOptimizationId) && $this->_mpsSession->hardwareOptimizationId > 0)
        {
            // Update the last modified date
            $this->_hardwareOptimization->lastModified = date('Y-m-d H:i:s');
            Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance()->save($this->_hardwareOptimization);
        }
        else
        {
            $this->_hardwareOptimization->hardwareOptimizationSettingId = Hardwareoptimization_Model_Mapper_Hardware_Optimization_Setting::getInstance()->insert(new Hardwareoptimization_Model_Hardware_Optimization_Setting());
            Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance()->insert($this->_hardwareOptimization);
            $this->_mpsSession->hardwareOptimizationId = $this->_hardwareOptimization->id;
        }
    }

    /**
     * Redirects the user to the very last available step
     */
    public function redirectToLatestStep ()
    {
        $stage = ($this->getHardwareOptimization()->stepName) ? : Hardwareoptimization_Model_Hardware_Optimization_Steps::STEP_FLEET_UPLOAD;
        $this->_navigation->updateAccessibleSteps($stage);


        $firstStep  = false;
        $latestStep = false;
        foreach ($this->_navigation->steps as $step)
        {
            if ($firstStep === false)
            {
                $firstStep = $step;
            }

            if (!$step->canAccess)
            {
                break;
            }

            $latestStep = $step;
        }

        if ($latestStep)
        {
            $this->redirector($latestStep->action, $latestStep->controller, $latestStep->module);
        }
        else
        {
            $this->redirector($firstStep->action, $firstStep->controller, $firstStep->module);
        }
    }

    /**
     * Updates an hardware optimization to be at the next available step
     *
     * @param bool $force Whether or not to force the update
     */
    public function updateStepName ($force = false)
    {
        // We can only do this when we have an active step
        if ($this->_navigation->activeStep instanceof My_Navigation_Step)
        {
            // That step also needs a next step for this to work
            if ($this->_navigation->activeStep->nextStep instanceof My_Navigation_Step)
            {
                $update = true;
                // We only want to update
                if ($force)
                {
                    $update = true;
                }
                else
                {
                    $newStepName = $this->_navigation->activeStep->nextStep->enumValue;

                    foreach ($this->_navigation->steps as $step)
                    {
                        // No need to update the step if we were going back in time.
                        if ($step->enumValue == $newStepName && $step->canAccess)
                        {
                            $update = false;
                            break;
                        }
                    }
                }

                if ($update)
                {
                    $this->getHardwareOptimization()->stepName = $this->_navigation->activeStep->nextStep->enumValue;
                }
            }
        }
    }
}